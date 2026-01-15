<?php 
require_once "db.php"; 
// 1️⃣ Merr inputet dhe sanitizo
$firstname = htmlspecialchars(trim($_POST['firstname'] ?? ''));
$lastname = htmlspecialchars(trim($_POST['lastname'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$terms = isset($_POST['terms']); // true/false

$errors = [];

// 2️⃣ Validimi i emrit dhe mbiemrit
if(!preg_match("/^[a-zA-Z]{3,40}$/", $firstname)){
    $errors[] = "First name must be 3-40 letters only.";
}
if(!preg_match("/^[a-zA-Z]{3,40}$/", $lastname)){
    $errors[] = "Last name must be 3-40 letters only.";
}

// 3️⃣ Validimi i email
if(!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)){
    $errors[] = "Invalid email address format.";
}

// 4️⃣ Validimi i password
$password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
if(empty($password)){
    $errors[] = "Password cannot be empty.";
} elseif(!preg_match($password_regex, $password)){
    $errors[] = "Password must be at least 8 characters long, include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.";
}

// 5️⃣ Kontrolli i confirm password
if($password !== $confirm_password){
    $errors[] = "Passwords do not match.";
}

// 6️⃣ Kontrolli i terms
if(!$terms){
    $errors[] = "You must accept the terms and conditions.";
}




$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $errors[] = "Email is already registered";
}


// 7️⃣ Nëse ka gabime, i shfaq dhe ndalon ekzekutimin
if(!empty($errors)){
    echo json_encode([
        "status" => 400,
        "message" => implode(", ", $errors)
    ]);
    exit;
}

// 8️⃣ Hash i fjalëkalimit para ruajtjes në DB
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, 'user')");
$stmt->bind_param("ssss", $firstname, $lastname, $email, $hashed_password);
$stmt->execute();

if($conn->affected_rows > 0){
    echo json_encode([
        "status" => 200,
        "message" => "Registration successful",
        "location" => "login.php" // redirect front-end
    ]);
} else {
    echo json_encode([
        "status" => 500,
        "message" => "Database error, please try again"
    ]);
}
?>
