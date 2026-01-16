<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
include __DIR__ . '/../auth/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);

    // Kontrollo email unik
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        $_SESSION['error'] = "This email already exists.";
        header("Location: patients.php");
        exit;
    }

    // Gjenero password random 6 karaktere
    $plain_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    $role = 'user';
    $status = 'inactive'; // user inaktiv deri sa të ndryshojë password-in
    $must_change_password = 1;

    // Insert user në databazë
    $stmt = $conn->prepare("
        INSERT INTO users 
        (firstname, lastname, email, phone, password, role, status, must_change_password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssi",
        $firstname,
        $lastname,
        $email,
        $phone,
        $hashed_password,
        $role,
        $status,
        $must_change_password
    );
    $stmt->execute();
    $stmt->close();

    // Ruaj plain password vetëm për shfaqje në dashboard
    $_SESSION['new_patient_password'][$email] = $plain_password;

    header("Location: patients.php");
    exit;
}
?>
