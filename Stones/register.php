<?php
// JSON register endpoint using site's mysqli connection from config.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

try {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$first || !$email || !$password) {
        echo json_encode(['success'=>false,'message'=>'Missing required fields']); exit;
    }

    $conn = getDBConnection();

    // check existing
    $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->fetch_assoc()) {
        echo json_encode(['success'=>false,'message'=>'Email already registered']);
        $stmt->close(); $conn->close();
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    // Insert user with default role = 'USER'
    $stmt = $conn->prepare('INSERT INTO users (first_name,last_name,email,password,role,created_at) VALUES (?,?,?,?,? , NOW())');
    $role = 'USER';
    $stmt->bind_param('sssss', $first, $last, $email, $hash, $role);
    $ok = $stmt->execute();

    if ($ok) {
        echo json_encode(['success'=>true]);
    } else {
        error_log('Register insert error: ' . $stmt->error);
        echo json_encode(['success'=>false,'message'=>'Server error']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
