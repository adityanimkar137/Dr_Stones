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

// ================= SWITCH =================
switch ($action) {

    // ================= USER: PLACE ORDER =================
    case 'placeOrder':

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'USER') {
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

        $stmt = $conn->prepare(
            "INSERT INTO orders (user_id, item_id, status)
             VALUES (?, ?, 'PENDING')"
        );

        $inserted = 0;

        foreach ($items as $item) {
            if (!isset($item['id'])) continue;

            $itemId = (int)$item['id'];

            // Ensure item exists
            $check = $conn->prepare("SELECT id FROM items WHERE id=?");
            $check->bind_param("i", $itemId);
            $check->execute();
            $check->store_result();

            if ($check->num_rows === 0) {
                $check->close();
                continue;
            }
            $check->close();

            $stmt->bind_param("ii", $userId, $itemId);
            $stmt->execute();
            $inserted++;
        }

        $stmt->close();

        if ($inserted === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No valid items found in cart'
            ]);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;

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

        $result = $conn->query("
            SELECT o.id,
                   u.first_name,
                   u.last_name,
                   u.email,
                   i.name AS item_name,
                   o.status,
                   o.date AS created_at
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN items i ON o.item_id = i.id
            ORDER BY o.date DESC
        ");

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        echo json_encode([
            'success' => true,
            'orders'  => $orders
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

        $result = $conn->query("
            SELECT id,
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
            ORDER BY created_at DESC
        ");

        $proposals = [];
        while ($row = $result->fetch_assoc()) {
            $proposals[] = $row;
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

        $conn->query("UPDATE proposals SET status='APPROVED' WHERE id=$id");

        $stmt = $conn->prepare("
            INSERT INTO items
            (name, subtitle, description, price, weight, origin, era, image)
            SELECT stone_name,
                   stone_subtitle,
                   stone_description,
                   price,
                   weight,
                   origin,
                   era,
                   image
            FROM proposals
            WHERE id=?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true]);
        exit;

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

        $conn->query("UPDATE proposals SET status='REJECTED' WHERE id=$id");

        echo json_encode(['success' => true]);
        exit;

    // ================= DEFAULT =================
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        exit;
}

$conn->close();
