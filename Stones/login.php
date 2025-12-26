<?php
// JSON login endpoint using site's mysqli connection from config.php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

try {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success'=>false,'message'=>'Email and password required']); exit;
    }

    $conn = getDBConnection();
    $stmt = $conn->prepare('SELECT user_id, password, first_name, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res ? $res->fetch_assoc() : null;

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success'=>false,'message'=>'Invalid credentials']); exit;
    }

    // login success
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['first_name'] ?? $email;
    // set role if available, default to USER
    $_SESSION['role'] = (!empty($user['role']) ? $user['role'] : 'USER');

    echo json_encode(['success'=>true,'name'=>$_SESSION['user_name']]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
