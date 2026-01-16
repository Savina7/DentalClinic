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

// 7️⃣ Kontrollo nëse email ekziston në DB
$stmt = $conn->prepare("SELECT id, status FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    if($user['status'] == 'active'){
        $errors[] = "Email is already registered.";
    } else {
        $errors[] = "Email already exists. Please activate your account using the link sent to you.";
    }
}

// 8️⃣ Nëse ka gabime, i shfaq dhe ndalon ekzekutimin
if(!empty($errors)){
    echo json_encode([
        "status" => 400,
        "message" => implode(", ", $errors)
    ]);
    exit;
}

// 9️⃣ Gjenero token unik për aktivizim
$token = bin2hex(random_bytes(16)); // 32 karaktere hex

// 🔟 Hash i fjalëkalimit
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 1️⃣1️⃣ Insert në DB me status inactive dhe token
$stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role, status, token) VALUES (?, ?, ?, ?, 'user', 'inactive', ?)");
$stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $token);
$stmt->execute();

// 1️⃣2️⃣ Nëse sukses, dërgo JSON
if($conn->affected_rows > 0){
    // TODO: Shto funksion për të dërguar email me token
    // Shembull: sendActivationEmail($email, $token);

    echo json_encode([
        "status" => 200,
        "message" => "Registration successful! Please check your email to activate your account.",
        "location" => "login.php"
    ]);
} else {
    echo json_encode([
        "status" => 500,
        "message" => "Database error, please try again."
    ]);
}
?>
