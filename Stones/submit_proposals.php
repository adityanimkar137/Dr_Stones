<?php 
session_start();
require_once 'config.php';

// User must be logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'USER') {
    $_SESSION['error'] = 'You must be logged in to submit a proposal';
    header('Location: submit_proposal_form.php');
    exit;
}

// Validate POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: submit_proposal_form.php');
    exit;
}

// Validate required fields
$requiredFields = ['sellerName', 'sellerEmail', 'stoneName', 'stonePrice', 'stoneWeight', 'stoneOrigin', 'stoneEra', 'stoneDescription'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: submit_proposal_form.php');
        exit;
    }
}

// Validate file upload
if (!isset($_FILES['stoneImages']) || $_FILES['stoneImages']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Image upload failed';
    header('Location: submit_proposal_form.php');
    exit;
}

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileType = $_FILES['stoneImages']['type'];
if (!in_array($fileType, $allowedTypes)) {
    $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed';
    header('Location: submit_proposal_form.php');
    exit;
}

// Validate file size (max 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB
if ($_FILES['stoneImages']['size'] > $maxSize) {
    $_SESSION['error'] = 'Image file is too large. Maximum size is 5MB';
    header('Location: submit_proposal_form.php');
    exit;
}

// Validate email
$vendorEmail = trim($_POST['sellerEmail']);
if (!filter_var($vendorEmail, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email address';
    header('Location: submit_proposal_form.php');
    exit;
}

// Validate price
$price = filter_var($_POST['stonePrice'], FILTER_VALIDATE_FLOAT);
if ($price === false || $price <= 0) {
    $_SESSION['error'] = 'Invalid price. Must be a positive number';
    header('Location: submit_proposal_form.php');
    exit;
}

try {
    $conn = getDBConnection();
    
    // Sanitize inputs
    $vendorName  = htmlspecialchars(trim($_POST['sellerName']), ENT_QUOTES, 'UTF-8');
    $stoneName   = htmlspecialchars(trim($_POST['stoneName']), ENT_QUOTES, 'UTF-8');
    $weight      = htmlspecialchars(trim($_POST['stoneWeight']), ENT_QUOTES, 'UTF-8');
    $origin      = htmlspecialchars(trim($_POST['stoneOrigin']), ENT_QUOTES, 'UTF-8');
    $era         = htmlspecialchars(trim($_POST['stoneEra']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['stoneDescription']), ENT_QUOTES, 'UTF-8');
    
    // Create upload directory if it doesn't exist
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Generate secure filename
    $ext = strtolower(pathinfo($_FILES['stoneImages']['name'], PATHINFO_EXTENSION));
    $fileName = uniqid('stone_', true) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($_FILES['stoneImages']['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save image file');
    }
    
    // Insert proposal into database
    $stmt = $conn->prepare("
        INSERT INTO proposals
        (stone_name, stone_description, price, weight, origin, era,
         vendor_name, vendor_email, image, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')
    ");
    
    if (!$stmt) {
        unlink($targetPath);
        throw new Exception('Database error: ' . $conn->error);
    }
    
    $stmt->bind_param(
        "ssdssssss",
        $stoneName,
        $description,
        $price,
        $weight,
        $origin,
        $era,
        $vendorName,
        $vendorEmail,
        $targetPath
    );
    
    if (!$stmt->execute()) {
        unlink($targetPath);
        throw new Exception('Failed to save proposal: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    // Redirect to index.php with success message
    $_SESSION['success'] = 'Your proposal has been submitted successfully and is awaiting approval';
    header('Location: index.php');
    exit;
    
} catch (Exception $e) {
    error_log('Proposal submission error: ' . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while submitting your proposal. Please try again.';
    header('Location: submit_proposal_form.php');
    exit;
}
?>
