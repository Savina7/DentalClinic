<?php
session_start();
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/mail.php';

$message = '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $code = random_int(100000, 999999);
            $expiry = date('Y-m-d H:i:s', time() + 3600);

            $upd = $conn->prepare("UPDATE users SET code = ?, code_expiry = ? WHERE email = ?");
            $upd->bind_param("sss", $code, $expiry, $email);
            $upd->execute();

            sendEmail($email, $code, 'reset');
        }

        header('Location: verify_code.php?email=' . urlencode($email));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password – Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/style.css">
</head>
<body class="auth-body">

    <img src="/smile-dental/image/background.jpg" alt="" class="bg-image">

    <div class="login-container">
        <h2 class="login-title">Reset Password</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 20px;">Enter your email to receive a verification code</p>

        <?php if ($message): ?>
            <p style="color: #e74c3c; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="login-form">
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
            </div>

            <button type="submit" name="submit" class="btn-submit">Send Code</button>
        </form>

        <div class="register-redirect">
            <p class="redirect-text">Remember your password? <a href="/smile-dental/auth/login.php" class="register-link">Login</a></p>
        </div>
    </div>

</body>
</html>
