<?php
// ------------------------------
// process-register.php
// ------------------------------

session_start();
ini_set('display_errors', 1); // vendos 1 për debug, pas testimit vë 0
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once "db.php"; // lidhja me DB

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
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $errors[] = "Email is already registered.";
}

// 8️⃣ Nëse ka gabime, kthe JSON dhe exit
if(!empty($errors)){
    echo json_encode([
        "status" => 400,
        "message" => implode(", ", $errors)
    ]);
    exit;
}

// 9️⃣ Generate 6-digit OTP
require_once __DIR__ . '/../mailer/mail.php';
$otp = random_int(100000, 999999);
$otp_expiry = date('Y-m-d H:i:s', time() + 300); // 5 minutes

// 🔟 Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 1️⃣1️⃣ Store registration data in session (NOT in database yet)
$_SESSION['pending_registration'] = [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'email' => $email,
    'password' => $hashed_password,
    'otp' => $otp,
    'otp_expiry' => $otp_expiry
];

// 1️⃣2️⃣ Send OTP via email
$email_sent = sendEmail($email, $otp, 'register');

if (!$email_sent) {
    // DEVELOPMENT MODE: Show OTP on screen if email fails
    $_SESSION['dev_otp_display'] = $otp;
    
    echo json_encode([
        "status" => 200,
        "message" => "Email service unavailable. Your verification code is: $otp (Check console for testing)",
        "location" => "verify_registration.php?email=" . urlencode($email)
    ]);
    exit;
}

// 1️⃣3️⃣ Redirect to verification page
echo json_encode([
    "status" => 200,
    "message" => "Verification code sent to your email!",
    "location" => "verify_registration.php?email=" . urlencode($email)
]);
exit;
?>
