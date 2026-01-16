<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
include __DIR__ . '/../auth/db.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Sigurohu që role përputhet me atë që ke te patients.php (ti ke përdorur 'user')
    // Gjithashtu shtojmë një kontroll që mos të fshihet aksidentalisht ndonjë admin
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        // Opsionale: Mund të shtosh një mesazh suksesi në session këtu
    }
}

// Ridrejtohu te lista e pacientëve
header("Location: patients.php");
exit;