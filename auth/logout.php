<?php
// Nis sesionin
session_start();

// Fshi të gjitha variablat e sesionit
$_SESSION = [];

// Shkatërro sesionin
session_destroy();

// Opsionale: fshi cookie-n e sesionit (për siguri më të madhe)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Ridrejto te faqja e login
   header("Location: /smile-dental/auth/login.php");
exit;
?>
