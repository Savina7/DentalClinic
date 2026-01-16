<?php
require_once __DIR__ . '/session_15.php';
if (!isset($_SESSION['user_id'])) {
   header("Location: /smile-dental/auth/login.php");
exit;
}

//Kjo eshte per redirect