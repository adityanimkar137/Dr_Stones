<?php
require_once 'config.php';

$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {

        $conn = getDBConnection();

        $stmt = $conn->prepare(
            "SELECT user_id, password 
             FROM users 
             WHERE email = ? AND role = 'ADMIN' 
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['user_id'];
                $_SESSION['role'] = 'ADMIN';

                header('Location: admin.php');
                exit;
            }
        }

        $stmt->close();
        $conn->close();
    }

    $error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #1a1714;
            color: #f4ede1;
            font-family: 'Cinzel', serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #2a2521;
            padding: 2.5rem;
            border: 3px solid #d4af37;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.8);
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #d4af37;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Sanctum</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            Invalid admin credentials
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="form-group">
            <label>Admin Email</label>
            <input type="email" name="email" class="form-control" required autocomplete="off">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required autocomplete="off">
        </div>

        <button type="submit" class="btn btn-warning btn-block">
            Enter Sanctum
        </button>
    </form>
</div>

</body>
</html>


