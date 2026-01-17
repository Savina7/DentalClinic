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
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
