<?php
session_start();
require_once __DIR__ . '/../auth/db.php';

$message = '';
$email = $_GET['email'] ?? '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);

    $stmt = $conn->prepare("SELECT id, code, code_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($user['code'] == $code) {
            if (new DateTime() <= new DateTime($user['code_expiry'])) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['code_verified'] = true;
                header('Location: update_password.php');
                exit;
            } else {
                $message = 'The code has expired. Please request a new one.';
            }
        } else {
            $message = 'Invalid verification code.';
        }
    } else {
        $message = 'Email not found.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code – Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/style.css">
</head>
<body class="auth-body">

    <img src="/smile-dental/image/background.jpg" alt="" class="bg-image">

    <div class="login-container">
        <h2 class="login-title">Verify Code</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 20px;">Enter the 6-digit code sent to your email</p>

        <?php if ($message): ?>
            <p style="color: #e74c3c; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="login-form">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            
            <div class="form-group">
                <label class="form-label" for="code">Verification Code</label>
                <input type="text" id="code" name="code" class="form-input" placeholder="Enter 6-digit code" maxlength="6" required>
            </div>

            <button type="submit" name="submit" class="btn-submit">Verify Code</button>
        </form>

        <div class="register-redirect">
            <p class="redirect-text">Didn't receive a code? <a href="forgot_password.php" class="register-link">Resend</a></p>
        </div>
    </div>

</body>
</html>
