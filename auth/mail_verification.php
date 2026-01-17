<?php
session_start();
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../mailer/mail.php';
require_once __DIR__ . '/../auth/logLogin.php';

// Check if user has pending login
if (!isset($_SESSION['pending_user_id'], $_SESSION['pending_email'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['pending_user_id'];
$email = $_SESSION['pending_email'];

// Generate new OTP
$otp = random_int(100000, 999999);
$otp_expiry = date('Y-m-d H:i:s', time() + 300); // 5 minutes

// Update database
$update_otp = $conn->prepare("UPDATE users SET email_otp = ?, email_otp_expires_at = ? WHERE id = ?");
$update_otp->bind_param("ssi", $otp, $otp_expiry, $user_id);
$update_otp->execute();

// Send new OTP
$email_sent = sendEmail($email, $otp, 'login_otp');

if ($email_sent) {
    logLogin($conn, $email, 'otp_resent', 'OTP resent to email');
    $_SESSION['otp_message'] = 'A new verification code has been sent to your email.';
} else {
    logLogin($conn, $email, 'error', 'Failed to resend OTP');
    $_SESSION['otp_message'] = 'Failed to send code. Please try again.';
}

header('Location: verify_email.php');
exit;
?>
