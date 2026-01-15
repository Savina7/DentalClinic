<?php
require_once "db.php"; 
$email=htmlspecialchars(trim($_POST['email'] ?? ''));
$password=$_POST['password'] ?? '';

$errors=[];
if(!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)){
    $errors[]="Invalid email address format.";
}
$password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
if(empty($password)){
    $errors[] = "Password cannot be empty.";
} elseif(!preg_match($password_regex, $password)){
    $errors[] = "Password must be at least 8 characters long, include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.";
}


if(!empty($errors)){
    echo json_encode([
        "status" => 400,
        "message" => implode(" | ", $errors) // të gjitha gabimet bashkë
    ]);
    exit;
}  

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // login i suksesshëm
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role']; 

    echo json_encode([
        "status" => 200,
        "message" => "Login successful!",
        "location" => "dashboard.php" // front do e redirect
    ]);
    exit;
} else {
    echo json_encode([
        "status" => 401,
        "message" => "Email or password is incorrect"
    ]);
    exit;
}



?>