<?php
require_once __DIR__ . '/../auth/session_15.php';
require_once __DIR__ . '/../auth/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 401, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

// VALIDIME
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid email']);
    exit;
}

if (!preg_match('/^\d{10}$/', $phone)) {
    echo json_encode(['status' => 400, 'message' => 'Phone must be exactly 10 digits']);
    exit;
}

$imagePath = null;

// UPLOAD FOTO
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {

    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => 400, 'message' => 'Invalid image type']);
        exit;
    }

    $folder = __DIR__ . '/../image/profile/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $fileName = 'user_' . $user_id . '.' . $ext;
    $fullPath = $folder . $fileName;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $fullPath)) {
        $imagePath = '/smile-dental/image/profile/' . $fileName;
    }
}

// UPDATE DB
if ($imagePath) {
    $stmt = $conn->prepare(
        "UPDATE users SET email = ?, phone = ?, profile_image = ? WHERE id = ?"
    );
    $stmt->bind_param("sssi", $email, $phone, $imagePath, $user_id);
} else {
    $stmt = $conn->prepare(
        "UPDATE users SET email = ?, phone = ? WHERE id = ?"
    );
    $stmt->bind_param("ssi", $email, $phone, $user_id);
}

if ($stmt->execute()) {
    echo json_encode([
        'status' => 200,
        'message' => 'Profile updated',
        'image_path' => $imagePath
    ]);
} else {
    echo json_encode(['status' => 500, 'message' => 'Database error']);
}
