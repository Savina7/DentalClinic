<?php
require_once __DIR__ . '/session_15.php';

// Vetëm admin mund të hyjë
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /smile-dental/auth/login.php"); // ridrejton tek login
    exit;
}
