<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /smile-dental/auth/login.php");
    exit;
}
?>
