<?php
include __DIR__ . '/../auth/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);

    // Kontrollo nëse emaili ekziston
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        echo "<script>alert('This email already exists. Please use another email.'); window.history.back();</script>";
        exit;
    }

    // Gjenero token unik për aktivizim
    $activation_token = bin2hex(random_bytes(16)); // 32 karaktere
    $status = 'inactive';
    $role = 'user';
    $must_change_password = 1;
    $password = NULL;

    // Fut pacientin në DB
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, phone, password, role, must_change_password, status, activation_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssiss",
        $firstname,
        $lastname,
        $email,
        $phone,
        $password,
        $role,
        $must_change_password,
        $status,
        $activation_token
    );
    $stmt->execute();
    $stmt->close();

    // Opsionale: dërgo email me link aktivizimi
    // $activation_link = "https://example.com/activate.php?token=" . $activation_token;
    // mail($email, "Activate Your Account", "Click this link to activate your account: $activation_link");

    // Kthehu tek lista e pacientëve
    header("Location: patients.php");
    exit;
}
?>
