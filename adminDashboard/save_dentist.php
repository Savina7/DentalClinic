<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
include __DIR__ . '/../auth/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $description = $_POST['description'];

    // Përpunimi i imazhit
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = __DIR__ . '/../image/'; // folderi ku do ruhet imazhi
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = $imageName;
        } else {
            echo "Error uploading image!";
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO dentists (name, specialty, description, image, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $specialty, $description, $image, $email, $phone);
    $stmt->execute();

    header("Location: dentists.php");
    exit;
}
?>
