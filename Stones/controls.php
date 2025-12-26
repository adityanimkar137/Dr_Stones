<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Read JSON input ONCE and safely
$rawInput = file_get_contents("php://input");
$json = json_decode($rawInput, true);
if (!is_array($json)) {
    $json = [];
}

// Resolve action from JSON, POST, or GET
$action = $json['action']
    ?? $_POST['action']
    ?? $_GET['action']
    ?? '';

$conn = getDBConnection();

// Ensure proposals table has optional columns for tracking approvals/rejections
function ensureProposalColumns($conn) {
    $cols = [];
    $res = $conn->query("SHOW COLUMNS FROM proposals");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $cols[$r['Field']] = true;
        }
        $res->free();
    }

    if (!isset($cols['approved_at'])) {
        $conn->query("ALTER TABLE proposals ADD COLUMN approved_at TIMESTAMP NULL DEFAULT NULL");
    }
    if (!isset($cols['rejection_reason'])) {
        $conn->query("ALTER TABLE proposals ADD COLUMN rejection_reason TEXT NULL");
    }
    if (!isset($cols['rejected_at'])) {
        $conn->query("ALTER TABLE proposals ADD COLUMN rejected_at TIMESTAMP NULL DEFAULT NULL");
    }
}

// ================= SWITCH =================
switch ($action) {

    // ================= USER: PLACE ORDER =================
    case 'placeOrder':

        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'USER') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'User login required'
            ]);
            exit;
        }

        $items = $json['items'] ?? [];

        if (!is_array($items) || empty($items)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid cart data'
            ]);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];

        // Begin transaction to ensure atomic order insertion
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare(
                "INSERT INTO orders (user_id, item_id, status, date) VALUES (?, ?, 'PENDING', NOW())"
            );

            if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);

            $inserted = 0;

            foreach ($items as $item) {
                if (!isset($item['id'])) continue;

                $itemId = (int)$item['id'];

                // Ensure item exists
                $check = $conn->prepare("SELECT id FROM items WHERE id=?");
                if (!$check) throw new Exception('Prepare failed: ' . $conn->error);
                $check->bind_param("i", $itemId);
                $check->execute();
                $check->store_result();

                if ($check->num_rows === 0) {
                    $check->close();
                    continue;
                }
                $check->close();

                if (!$stmt->bind_param("ii", $userId, $itemId) || !$stmt->execute()) {
                    throw new Exception('Failed inserting order: ' . $stmt->error);
                }
                $inserted++;
            }

            $stmt->close();

            if ($inserted === 0) {
                $conn->rollback();
                echo json_encode([
                    'success' => false,
                    'message' => 'No valid items found in cart'
                ]);
                exit;
            }

            $conn->commit();
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            error_log('placeOrder error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Order failed']);
            exit;
        }

        // ================= ADMIN: GET ORDERS =================
        case 'getOrders':

            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Admins only'
                ]);
                exit;
            }

            // Aggregate items for each order id into a single slip and compute net price
            $sql = "SELECT o.id,
                           o.user_id,
                           u.first_name,
                           u.last_name,
                           u.email,
                           GROUP_CONCAT(i.name SEPARATOR ', ') AS item_name,
                           o.status,
                           o.date AS created_at,
                           SUM(IFNULL(i.price,0)) AS net_price,
                           COUNT(i.id) AS item_count
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    JOIN items i ON o.item_id = i.id
                    GROUP BY o.id, o.user_id, u.first_name, u.last_name, u.email, o.status, o.date
                    ORDER BY o.date DESC";

            $result = $conn->query($sql);

            $orders = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $orders[] = [
                        'id' => $row['id'],
                        'user_id' => $row['user_id'],
                        'first_name' => $row['first_name'],
                        'last_name' => $row['last_name'],
                        'email' => $row['email'],
                        'item_name' => $row['item_name'],
                        'status' => $row['status'],
                        'created_at' => $row['created_at'],
                        'net_price' => $row['net_price'] !== null ? (float)$row['net_price'] : 0.0,
                        'item_count' => (int)($row['item_count'] ?? 0)
                    ];
                }
                $result->free();
            }

            $sessionInfo = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'role' => $_SESSION['role'] ?? null
            ];

            echo json_encode([
                'success' => true,
                'orders'  => $orders,
                'session' => $sessionInfo
            ]);
            exit;

        // ================= ADMIN: GET PROPOSALS =================
        case 'getProposals':

            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Admins only'
                ]);
                exit;
            }

            $result = $conn->query("SELECT id,
                                           stone_name,
                                           stone_subtitle,
                                           stone_description,
                                           price,
                                           weight,
                                           origin,
                                           era,
                                           vendor_name,
                                           vendor_email,
                                           image,
                                           status,
                                           created_at
                                    FROM proposals
                                    WHERE status = 'PENDING'
                                    ORDER BY created_at DESC");

            $proposals = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $proposals[] = $row;
                }
                $result->free();
            }

            echo json_encode([
                'success'   => true,
                'proposals' => $proposals
            ]);
            exit;

    // ================= ADMIN: APPROVE PROPOSAL =================
    case 'approveProposal':

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Admins only'
            ]);
            exit;
        }

        $id = (int)($json['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid proposal ID'
            ]);
            exit;
        }

        // Use transaction to move proposal to items and mark approved
        $conn->begin_transaction();
        try {
            // ensure audit columns exist
            ensureProposalColumns($conn);

            // Only insert when proposal is still pending to avoid duplicate approvals
            $insertStmt = $conn->prepare(
                "INSERT INTO items
                (name, subtitle, description, price, weight, origin, era, image)
                SELECT stone_name, stone_subtitle, stone_description, price, weight, origin, era, image
                FROM proposals
                WHERE id=? AND status='PENDING'"
            );
            if (!$insertStmt) throw new Exception('Prepare failed: ' . $conn->error);
            $insertStmt->bind_param('i', $id);
            if (!$insertStmt->execute()) throw new Exception('Insert items failed: ' . $insertStmt->error);

            // If nothing was inserted, the proposal was not found or already processed
            if ($conn->affected_rows === 0) {
                throw new Exception('No pending proposal found to approve');
            }
            $insertStmt->close();

            // Mark proposal as approved (keep record)
            $update = $conn->prepare("UPDATE proposals SET status='APPROVED', approved_at=NOW() WHERE id=? AND status='PENDING'");
            if (!$update) throw new Exception('Prepare failed: ' . $conn->error);
            $update->bind_param('i', $id);
            if (!$update->execute()) throw new Exception('Update proposal failed: ' . $update->error);

            if ($update->affected_rows === 0) {
                throw new Exception('Failed to mark proposal approved (it may have been processed already)');
            }
            $update->close();

            $conn->commit();
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            error_log('approveProposal error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Approval failed']);
            exit;
        }

    // ================= ADMIN: REJECT PROPOSAL =================
    case 'rejectProposal':

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Admins only'
            ]);
            exit;
        }

        $id = (int)($json['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid proposal ID'
            ]);
            exit;
        }

        // Mark proposal as rejected and store optional reason
        try {
            ensureProposalColumns($conn);

            $reason = $json['reason'] ?? $_POST['reason'] ?? '';

            // Only reject if still pending
            $stmt = $conn->prepare("UPDATE proposals SET status='REJECTED', rejection_reason=?, rejected_at=NOW() WHERE id=? AND status='PENDING'");
            if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
            $stmt->bind_param('si', $reason, $id);
            if (!$stmt->execute()) throw new Exception('Execute failed: ' . $stmt->error);

            if ($stmt->affected_rows === 0) {
                // No rows updated: either not found or already processed
                $stmt->close();
                echo json_encode(['success' => false, 'message' => 'No pending proposal found to reject']);
                exit;
            }

            $stmt->close();

            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            error_log('rejectProposal error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Rejection failed']);
            exit;
        }

    // ================= DEFAULT =================
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        exit;
}

$conn->close();
