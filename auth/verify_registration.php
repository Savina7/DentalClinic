<?php
session_start();
require_once __DIR__ . '/db.php';

// Check if user has pending registration
if (!isset($_SESSION['pending_registration'])) {
    header('Location: register.php');
    exit;
}

$message = '';
$registration_data = $_SESSION['pending_registration'];
$email = $registration_data['email'];

if (isset($_POST['submit'])) {
    $code = trim($_POST['code']);

    // Verify OTP
    if ($registration_data['otp'] == $code) {
        if (new DateTime() <= new DateTime($registration_data['otp_expiry'])) {
            // OTP is valid - Create the account
            $activation_token = bin2hex(random_bytes(16));
            
            $stmt = $conn->prepare("
                INSERT INTO users 
                (firstname, lastname, email, password, role, status, activation_token)
                VALUES (?, ?, ?, ?, 'user', 'active', ?)
            ");
            $stmt->bind_param("sssss", 
                $registration_data['firstname'],
                $registration_data['lastname'],
                $registration_data['email'],
                $registration_data['password'],
                $activation_token
            );
            $stmt->execute();

            if ($conn->affected_rows > 0) {
                // Account created successfully - Log them in
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['role'] = 'user';
                $_SESSION['last_activity'] = time();
                
                // Clear pending registration
                unset($_SESSION['pending_registration']);
                
                header('Location: ../userDashboard/home.php');
                exit;
            } else {
                $message = 'Failed to create account. Please try again.';
            }
        } else {
            $message = 'The code has expired. Please register again.';
        }
    } else {
        $message = 'Invalid verification code.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Registration – Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/style.css">
</head>
<body class="auth-body">

    <img src="/smile-dental/image/background.jpg" alt="" class="bg-image">

    <div class="login-container">
        <h2 class="login-title">Verify Your Email</h2>
        <p style="text-align: center; color: #7f8c8d; margin-bottom: 20px;">Enter the 6-digit code sent to <strong><?= htmlspecialchars($email) ?></strong></p>

        <?php if ($message): ?>
            <p style="color: #e74c3c; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="login-form">
            <div class="form-group">
                <label class="form-label" for="code">Verification Code</label>
                <input type="text" id="code" name="code" class="form-input" placeholder="Enter 6-digit code" maxlength="6" required autofocus>
            </div>

            <button type="submit" name="submit" class="btn-submit">Verify & Create Account</button>
        </form>

        <div class="register-redirect">
            <p class="redirect-text">Didn't receive a code? <a href="register.php" class="register-link">Try again</a></p>
        </div>
    </div>

</body>
</html>
