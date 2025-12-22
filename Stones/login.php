<?php
require_once 'config.php';

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit;
}

$conn = getDBConnection();

// Prepare query to fetch user
$stmt = $conn->prepare("SELECT user_id, first_name, password, role FROM users WHERE email = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    $stmt->close();
    $conn->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    $conn->close();
    exit;
}

// Ensure role is USER
if (!isset($user['role']) || $user['role'] !== 'USER') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    $conn->close();
    exit;
}

// Set session securely
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['first_name'];
$_SESSION['role'] = $user['role'];

// Close connection
$conn->close();

// Return success
echo json_encode(['success' => true]);
exit;
