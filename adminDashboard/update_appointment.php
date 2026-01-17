<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/session_15.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$id || !in_array($action, ['approve', 'deny'])) {
        die("Invalid request");
    }

    $status = ($action === 'approve') ? 'approved' : 'denied';

    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        header("Location: Appointments.php");
        exit;
    } else {
        die("Failed to update appointment.");
    }
} else {
    die("Invalid request method.");
}
