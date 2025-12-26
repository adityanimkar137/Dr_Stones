<?php
// Verify Google ID token server-side and create/sign-in user.
// Requires PHP cURL or file_get_contents; uses config.php to access DB ($pdo).
session_start();
header('Content-Type: application/json');
require_once 'config.php';

// use mysqli connection from config
$conn = getDBConnection();

// Use centralized Google client ID from config
$GOOGLE_CLIENT_ID = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '507577421075-0vnpcp82983b7fgapir9lg931irioj6c.apps.googleusercontent.com';

$input = json_decode(file_get_contents('php://input'), true);
$id_token = $input['id_token'] ?? '';

if (!$id_token) { 
    echo json_encode(['success'=>false,'message'=>'No token']); 
    $conn->close();
    exit; 
}

// Verify token using Google's tokeninfo endpoint with file_get_contents, falling back to cURL.
function verify_id_token_remote($id_token) {
    $verify_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
    $resp = @file_get_contents($verify_url);
    if ($resp === false) {
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $verify_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $resp = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_err = curl_error($ch);
            curl_close($ch);
            if ($resp === false || ($http_code && $http_code >= 400)) {
                return null;
            }
        } else {
            return null;
        }
    }
    $data = json_decode($resp, true);
    return is_array($data) ? $data : null;
}

$data = verify_id_token_remote($id_token);
if (!$data) {
    echo json_encode(['success'=>false,'message'=>'Token verification failed']);
    $conn->close();
    exit;
}
if (empty($data['aud']) || $data['aud'] !== $GOOGLE_CLIENT_ID) {
    echo json_encode(['success'=>false,'message'=>'Invalid token audience']); 
    $conn->close();
    exit;
}

// $data contains email, name, sub (google id), picture...
$email = $data['email'] ?? null;
$name  = $data['name'] ?? null;
$google_sub = $data['sub'] ?? null;

if (!$email) { 
    echo json_encode(['success'=>false,'message'=>'No email in token']); 
    $conn->close();
    exit; 
}

// find or create user
$stmt = $conn->prepare('SELECT user_id, first_name FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res ? $res->fetch_assoc() : null;

if (!$user) {
    // create a user (Google sign-in). The `users` table requires a non-null password and last_name,
    // so fill them with a generated hash and optional last name parsed from the display name.
    $parts = preg_split('/\s+/', trim($name ?: ''));
    $first = $parts[0] ?? $email;
    $last = $parts[1] ?? '';
    try {
        $rand = bin2hex(random_bytes(16));
    } catch (Exception $e) {
        $rand = uniqid('', true);
    }
    $pw_hash = password_hash($rand, PASSWORD_DEFAULT);

    $ins = $conn->prepare('INSERT INTO users (first_name,last_name,email,password,created_at) VALUES (?,?,?,?,NOW())');
    $ins->bind_param('ssss', $first, $last, $email, $pw_hash);
    $ins->execute();
    if ($ins->affected_rows <= 0) {
        echo json_encode(['success'=>false,'message'=>'User creation failed']);
        $ins->close();
        $stmt->close();
        $conn->close();
        exit;
    }
    $user_id = $conn->insert_id;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $first;
    echo json_encode(['success'=>true,'name'=>$_SESSION['user_name']]);
    $ins->close();
    $stmt->close();
    $conn->close();
    exit;
}

// existing user — log in
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['first_name'] ?? $email;
echo json_encode(['success'=>true,'name'=>$_SESSION['user_name']]);

$stmt->close();
$conn->close();
?>