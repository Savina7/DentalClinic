<?php
session_start(); // gjithmonë në fillim
require_once "db.php"; 
require_once "logLogin.php"; 

$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$errors = [];

// --------- VALIDIMI I EMAIL ---------
if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
    $errors[] = "Invalid email address format.";
}

// --------- VALIDIMI I PASSWORD ---------
$password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
if (empty($password)) {
    $errors[] = "Password cannot be empty.";
} elseif (!preg_match($password_regex, $password)) {
    $errors[] = "Password must be at least 8 characters long, include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.";
}

// --------- NËSE KEMI GABIME VALIDIMI ---------
if (!empty($errors)) {
    $error_msg = implode(" | ", $errors);
    logLogin($conn, $email, 'error', $error_msg);

    echo json_encode([
        "status" => 400,
        "message" => $error_msg
    ]);
    exit;
}

// --------- MERR USER NGA DB ---------
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    logLogin($conn, $email, 'failed', 'Email not found');
    echo json_encode([
        "status" => 401,
        "message" => "Email or password is incorrect"
    ]);
    exit;
}

// --------- CHECK BLOKIM PËR TENTATIVA  PER 30 MINUTA---------
if (!empty($user['lock_until']) && strtotime($user['lock_until']) > time()) {
    logLogin($conn, $email, 'blocked', 'Account temporarily locked');
    echo json_encode([
        "status" => 423, // Locked
        "message" => "Account has been blocked. Try again later."
    ]);
    exit;
}

// --------- PASSWORD I GABUAR ---------
if (!password_verify($password, $user['password'])) {
    $failed = $user['failed_attempts'] + 1;
    $lockUntil = null;

    if ($failed >= 7) {
        $lockUntil = date("Y-m-d H:i:s", strtotime("+30 minutes"));
    }

    $update = $conn->prepare("UPDATE users SET failed_attempts = ?, lock_until = ? WHERE id = ?");
    $update->bind_param("isi", $failed, $lockUntil, $user['id']);
    $update->execute();

    logLogin($conn, $email, 'failed', "Wrong password ($failed/7)");

    echo json_encode([
        "status" => 401,
        "message" => "Incorrect password. Attempts: $failed/7"
    ]);
    exit;
}

// --------- LOGIN I SUKSESSHËM ---------
$reset = $conn->prepare("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = ?");
$reset->bind_param("i", $user['id']);
$reset->execute();

$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

// Set last activity time per 15 minutshin
$_SESSION['last_activity'] = time();

logLogin($conn, $email, 'success', 'Login successful');

// --------- REDIRECT DYNAMIK BAZUAR NË ROL ---------
$redirect_location = ($user['role'] === 'admin') 
    ? "../adminDashboard/patients.php" 
    : "../userDashboard/home.php";

echo json_encode([
    "status" => 200,
    "message" => "Login successful!",
    "location" => $redirect_location
]);
exit;
?>
