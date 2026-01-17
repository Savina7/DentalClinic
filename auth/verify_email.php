<?php
session_start();
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/logLogin.php';

// Check if user has pending login
if (!isset($_SESSION['pending_user_id'], $_SESSION['pending_email'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$email = $_SESSION['pending_email'];

if (isset($_POST['submit'])) {
    $code = trim($_POST['code']);
    $user_id = $_SESSION['pending_user_id'];

    // Get OTP from database
    $stmt = $conn->prepare("SELECT email_otp, email_otp_expires_at, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($user['email_otp'] == $code) {
            if (new DateTime() <= new DateTime($user['email_otp_expires_at'])) {
                // OTP is valid - Complete login
                $clear_otp = $conn->prepare("UPDATE users SET email_otp = NULL, email_otp_expires_at = NULL WHERE id = ?");
                $clear_otp->bind_param("i", $user_id);
                $clear_otp->execute();

                // Set session for full login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();

                // Clear pending session data
                unset($_SESSION['pending_user_id']);
                unset($_SESSION['pending_email']);
                unset($_SESSION['pending_role']);

                logLogin($conn, $email, 'success', 'Login successful after OTP verification');

                // Redirect based on role
                $redirect = ($user['role'] === 'admin') 
                    ? "../adminDashboard/patients.php" 
                    : "../userDashboard/home.php";
                
                header("Location: $redirect");
                exit;
            } else {
                $message = 'The code has expired. Please login again.';
                logLogin($conn, $email, 'failed', 'OTP expired');
            }
        } else {
            $message = 'Invalid verification code.';
            logLogin($conn, $email, 'failed', 'Invalid OTP');
        }
    } else {
        $message = 'Session error. Please login again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Login – Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/style.css">
</head>
<body class="auth-body">

    <img src="/smile-dental/image/background.jpg" alt="" class="bg-image">

    <div class="login-container">
        <h2 class="login-title">Verify Your Login</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 20px;">Enter the 6-digit code sent to <strong><?= htmlspecialchars($email) ?></strong></p>

        <?php if ($message): ?>
            <p style="color: #e74c3c; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="login-form">
            <div class="form-group">
                <label class="form-label" for="code">Verification Code</label>
                <input type="text" id="code" name="code" class="form-input" placeholder="Enter 6-digit code" maxlength="6" required autofocus>
            </div>

            <button type="submit" name="submit" class="btn-submit">Verify & Login</button>
        </form>

        <div class="register-redirect">
            <p class="redirect-text">Didn't receive a code? <a href="login.php" class="register-link">Try again</a></p>
        </div>
    </div>

</body>
</html>
