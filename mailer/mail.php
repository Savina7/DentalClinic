<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';
require_once __DIR__ . '/Exception.php';

function sendEmail($toEmail, $code, $action = 'reset') {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'smiledentalalbania2005@gmail.com';
        $mail->Password = 'rlfguklgtzorezto'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('smiledentalalbania2005@gmail.com', 'Smile Dental');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);

        if ($action === 'reset') {
            $mail->Subject = 'Reset Your Password - Smile Dental';
            $mail->Body = "
                <h2>🔐 Password Reset Request</h2>
                <p>Your password reset code is:</p>
                <h1>$code</h1>
                <p>This code will expire in 1 hour.</p>
            ";
        } elseif ($action === 'login_otp') {
            $mail->Subject = 'Your Login Verification Code - Smile Dental';
            $mail->Body = "
                <h2>🔒 Login Verification</h2>
                <p>Your login verification code is:</p>
                <h1 style='color: #0077b6; font-size: 48px; letter-spacing: 8px;'>$code</h1>
                <p>This code will expire in <strong>5 minutes</strong>.</p>
                <p style='color: #666; font-size: 14px;'>If you didn't attempt to log in, please ignore this email.</p>
            ";
        } elseif ($action === 'register') {
            $mail->Subject = 'Verify Your Email - Smile Dental';
            $mail->Body = "
                <h2>✉️ Email Verification</h2>
                <p>Welcome to Smile Dental! Your verification code is:</p>
                <h1 style='color: #0077b6; font-size: 48px; letter-spacing: 8px;'>$code</h1>
                <p>This code will expire in <strong>5 minutes</strong>.</p>
                <p>Enter this code to complete your registration.</p>
            ";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
