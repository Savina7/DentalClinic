<?php
require_once __DIR__ . '/../auth/session_15.php';
require_once __DIR__ . '/../auth/db.php';

// 🔐 Kontroll login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Merr emrin dhe mbiemrin nga DB
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT firstname, lastname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$firstname = $user['firstname'] ?? 'Unknown';
$lastname  = $user['lastname'] ?? 'User';

// Services (mund t’i marrësh nga DB më vonë)
$services = [
    "General Dentistry",
    "Oral Hygiene",
    "Invisalign",
    "Dental Implants",
    "Teeth Whitening",
    "Minor Oral Surgery"
];

// Merr mesazhin nga rezervimi i fundit (nese ekziston)
$booking_message = $_SESSION['booking_message'] ?? null;
unset($_SESSION['booking_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Appointment | Smile Dental</title>

<link rel="stylesheet" href="/smile-dental/styleUser.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="layout-sidebar">
    <div class="sidebar-logo-box">
        <span class="clinic-name-text"><i class="fas fa-tooth"></i> Smile Dental</span>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-links-list">
            <li><a href="home.php" class="nav-item-link"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="myProfile.php" class="nav-item-link"><i class="fas fa-user-circle"></i> My Profile</a></li>
            <li><a href="bookAppointment.php" class="nav-item-link active-nav-page"><i class="fas fa-plus-circle"></i> Book Now</a></li>
        </ul>
    </nav>

    <div class="logout-section-bottom">
    <a href="/smile-dental/auth/logout.php" class="nav-item-link logout-color">
        <i class="fas fa-sign-out-alt nav-icon-style"></i> Logout
    </a>
</div>
</aside>

<!-- CONTENT -->
<main id="dental-booking-section">
    <div class="booking-card">

        <div class="booking-header">
            <h2><i class="fas fa-calendar-check"></i> Book Appointment</h2>
            <p>Fill the form to schedule your visit</p>
        </div>

        <?php if($booking_message): ?>
            <div class="booking-message" style="padding:10px; background:#d4edda; color:#155724; margin-bottom:15px; border-radius:5px;">
                <?php echo htmlspecialchars($booking_message); ?>
            </div>
        <?php endif; ?>

        <form action="process_booking.php" method="POST" class="booking-grid-form">

            <!-- FIRST NAME -->
            <div class="field-wrapper">
                <label>First Name</label>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="firstname" class="booking-input readonly" value="<?php echo htmlspecialchars($firstname); ?>" readonly>
                </div>
            </div>

            <!-- LAST NAME -->
            <div class="field-wrapper">
                <label>Last Name</label>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="lastname" class="booking-input readonly" value="<?php echo htmlspecialchars($lastname); ?>" readonly>
                </div>
            </div>

            <!-- SERVICE -->
            <div class="field-wrapper">
                <label>Service</label>
                <div class="input-container">
                    <i class="fas fa-tooth"></i>
                    <select name="service" class="booking-select" required>
                        <option value="" disabled selected>Select Service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service; ?>"><?php echo $service; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- DATE -->
            <div class="field-wrapper">
                <label>Date</label>
                <div class="input-container">
                    <i class="fas fa-calendar-day"></i>
                    <input type="date" name="date" class="booking-input" required>
                </div>
            </div>

            <!-- TIME -->
            <div class="field-wrapper">
                <label>Time</label>
                <div class="input-container">
                    <i class="fas fa-clock"></i>
                    <input type="time" name="time" class="booking-input" required>
                </div>
            </div>

            <!-- MESSAGE -->
            <div class="field-wrapper full-width">
                <label>Symptoms / Notes</label>
                <div class="input-container">
                    <i class="fas fa-comment-medical" style="margin-top:14px;"></i>
                    <textarea name="message" class="booking-input" placeholder="Describe your symptoms..." style="height:80px; resize:none;" required></textarea>
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="field-wrapper full-width">
                <button type="submit" class="booking-submit-btn">Confirm Booking <i class="fas fa-check"></i></button>
            </div>

        </form>
    </div>
</main>

</body>
</html>
