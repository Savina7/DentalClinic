<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
include __DIR__ . '/../auth/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Merr imazhin aktual për ta fshirë nga folderi
    $stmt = $conn->prepare("SELECT image FROM dentists WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dentist = $result->fetch_assoc();

    if ($dentist) {
        if (!empty($dentist['image']) && file_exists(__DIR__ . '/../image/' . $dentist['image'])) {
            unlink(__DIR__ . '/../image/' . $dentist['image']);
        }

        // Fshi dentistin nga databaza
        $stmt = $conn->prepare("DELETE FROM dentists WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

header("Location: dentists.php");
exit;
