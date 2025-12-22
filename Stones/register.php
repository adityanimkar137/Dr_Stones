<?php
require_once 'config.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request');
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// Validate required fields
if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
    die('All fields are required');
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email');
}

// Check password match
if ($password !== $confirm) {
    die('Passwords do not match');
}

// Check password length
if (strlen($password) < 8) {
    die('Password must be at least 8 characters');
}

$conn = getDBConnection();

// Check for duplicate email
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    die('Email already registered');
}
$check->close();

// Insert new user
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'USER')");
$stmt->bind_param("ssss", $firstName, $lastName, $email, $hash);

if ($stmt->execute()) {
    $userId = $stmt->insert_id;

    // Auto-login
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $firstName;
    $_SESSION['role'] = 'USER';

    $stmt->close();
    $conn->close();

    // Redirect to index.php
    header("Location: index.php");
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    $conn->close();
    die('Registration failed: ' . $err);
}
