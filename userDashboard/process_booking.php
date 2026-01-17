<?php
require_once __DIR__ . '/../auth/session_15.php';
require_once __DIR__ . '/../auth/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

// Merr të dhënat nga forma
$user_id  = $_SESSION['user_id'];
$firstname = $_POST['firstname'] ?? '';
$lastname  = $_POST['lastname'] ?? '';
$service   = $_POST['service'] ?? '';
$date      = $_POST['date'] ?? '';
$time      = $_POST['time'] ?? '';
$message   = $_POST['message'] ?? '';

// Validim minimal
if(empty($service) || empty($date) || empty($time)){
    $_SESSION['booking_message'] = "Ju lutem plotësoni të gjitha fushat.";
    header("Location: bookAppointment.php");
    exit;
}

// Fut rezervimin në DB me status 'pending'
$stmt = $conn->prepare("
    INSERT INTO appointments
    (user_id, firstname, lastname, service, appointment_date, appointment_time, message, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
");
$stmt->bind_param("issssss", $user_id, $firstname, $lastname, $service, $date, $time, $message);

if($stmt->execute()){
    $_SESSION['booking_message'] = "Rezervimi u dergua dhe po pret aprovimin nga admini!";
} else {
    $_SESSION['booking_message'] = "Gabim gjatë rezervimit: " . $stmt->error;
}

header("Location: bookAppointment.php");
exit;
?>
