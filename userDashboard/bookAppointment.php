<?php
require_once __DIR__ . '/../auth/auth_check.php';
// PHP Logic for services and dentists
$services = [
    "General Dentistry", 
    "Oral Hygiene", 
    "Invisalign", 
    "Dental Implants", 
    "Teeth Whitening", 
    "Minor Oral Surgery"
];

$dentists = ["Dr. Smith", "Dr. Jones", "Dr. Brown"];

// Placeholder for full name (usually comes from session)
$fullname = "Savina Berisha"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - Dental Clinic</title>
    <link rel="stylesheet" href="/smile-dental/styleUser.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<aside class="layout-sidebar">
    <div class="sidebar-logo-box">
        <span class="clinic-name-text">
            <i class="fas fa-tooth"></i> Smile Dental
        </span>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-links-list">
            <li><a href="home.php" class="nav-item-link"><i class="fas fa-home nav-icon-style"></i> Dashboard</a></li>
            <li><a href="myProfile.php" class="nav-item-link"><i class="fas fa-user-circle nav-icon-style"></i> My Profile</a></li>
            <li><a href="bookAppointment.php" class="nav-item-link active-nav-page"><i class="fas fa-plus-circle nav-icon-style"></i> Book Now</a></li>
        </ul>
    </nav>

    <div class="logout-section-bottom">
        <a href="logout.php" class="nav-item-link logout-color">
            <i class="fas fa-sign-out-alt nav-icon-style"></i> Logout
        </a>
    </div>
</aside>

<div id="dental-booking-section">
    <div class="booking-card">
        
        <div class="booking-header">
            <h2><i class="fas fa-calendar-check"></i> Book Now</h2>
            <p>Enter your details to schedule a visit.</p>
        </div>

        <form action="process_booking.php" method="POST" class="booking-grid-form">
            
            <div class="field-wrapper full-width">
                <label for="fullname">Patient Name</label>
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" id="fullname" name="fullname" class="booking-input readonly" 
                           value="<?php echo htmlspecialchars($fullname); ?>" readonly required>
                </div>
            </div>

            <div class="field-wrapper">
                <label for="service">Service</label>
                <div class="input-container">
                    <i class="fas fa-tooth"></i>
                    <select id="service" name="service" class="booking-select" required>
                        <option value="" disabled selected>Select Service</option>
                        <?php foreach($services as $s): ?>
                            <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field-wrapper">
                <label for="dentist">Dentist</label>
                <div class="input-container">
                    <i class="fas fa-user-md"></i>
                    <select id="dentist" name="dentist" class="booking-select" required>
                        <option value="" disabled selected>Select Dentist</option>
                        <?php foreach($dentists as $d): ?>
                            <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="field-wrapper">
                <label for="date">Date</label>
                <div class="input-container">
                    <i class="fas fa-calendar-day"></i>
                    <input type="date" id="date" name="date" class="booking-input" required>
                </div>
            </div>

            <div class="field-wrapper">
                <label for="time">Time</label>
                <div class="input-container">
                    <i class="fas fa-clock"></i>
                    <input type="time" id="time" name="time" class="booking-input" required>
                </div>
            </div>

            <div class="field-wrapper full-width">
                <label for="message">Symptoms / Notes</label>
                <div class="input-container">
                   <i class="fas fa-comment-medical" style="margin-top: 15px;"></i>
                    <textarea id="message" name="message" class="booking-input" 
                              placeholder="Describe your symptoms..." 
                              style="height: 80px; padding-top: 10px; resize: none;" required></textarea>
                </div>
            </div>

            <div class="field-wrapper full-width">
                <button type="submit" class="booking-submit-btn">
                    Confirm Booking <i class="fas fa-check"></i>
                </button>
            </div>

        </form>
    </div>
</div>
<footer class="dashboard-footer">
    <div class="footer-container">
        <p>© 2025 Smile Dental. All rights reserved.</p>
        <div class="footer-links">
            <a href="../pages/contact.php">Contact</a>
            <span class="separator">|</span>
            <a href="../pages/privacy.php">Privacy Policy</a>
        </div>
    </div>
</footer>

</body>
</html>