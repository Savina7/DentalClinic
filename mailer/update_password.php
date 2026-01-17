<?php
session_start();
require_once __DIR__ . '/../auth/db.php';

if (!isset($_SESSION['code_verified'], $_SESSION['reset_email'])) {
    header('Location: forgot_password.php');
    exit;
}

$message = '';

if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, code = NULL, code_expiry = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hash, $_SESSION['reset_email']);
        $stmt->execute();
        session_destroy();
        header('Location: ../auth/login.php?reset=success');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password – Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/style.css">
</head>
<body class="auth-body">

    <img src="/smile-dental/image/background.jpg" alt="" class="bg-image">

    <div class="login-container">
        <h2 class="login-title">Set New Password</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 20px;">Enter your new password below</p>

        <?php if ($message): ?>
            <p style="color: #e74c3c; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="login-form">
            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter new password" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm" class="form-input" placeholder="Confirm new password" required>
            </div>

            <button type="submit" name="submit" class="btn-submit">Update Password</button>
        </form>
    </div>

</body>
</html>
