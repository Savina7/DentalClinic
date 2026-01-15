<?php
session_start();

/* Kontrollo nëse user është loguar
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}
KTO DUHEN JAN TE RENDESISHME 
// Merr emrin e user-it nga session
$user_name = $_SESSION['user_name'] ?? "Filan"; */
$user_name = "Filan";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/styleUser.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<aside class="layout-sidebar">
    <div class="sidebar-logo-box">
        <span class="clinic-name-text"><i class="fas fa-tooth"></i> Smile Dental</span>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-links-list">
            <li><a href="home.php" class="nav-item-link active-nav-page"><i class="fas fa-home nav-icon-style"></i> Dashboard</a></li>
            <li><a href="myProfile.php" class="nav-item-link"><i class="fas fa-user-circle nav-icon-style"></i> My Profile</a></li>
        
            <li><a href="bookAppointment.php" class="nav-item-link"><i class="fas fa-plus-circle nav-icon-style"></i> Book Now</a></li>
        </ul>
    </nav>

    <div class="logout-section-bottom">
        <a href="logout.php" class="nav-item-link logout-color">
            <i class="fas fa-sign-out-alt nav-icon-style"></i> Logout
        </a>
    </div>
</aside>

<main class="layout-content">
    <header class="welcome-section-header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
        <p>Here is your account summary for today.</p>
    </header>

    <section class="hero-promo-card">
        <h2>Koha për një buzëqeshje të re?</h2>
        <p>Your last check-up was 4 months ago. Don't forget your routine cleaning!</p>
    </section>

    <div class="dashboard-stats-grid">
        <div class="info-box-card">
            <div class="icon-wrapper-circle bg-blue-soft">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-details">
                <p class="stat-title-label">Next Appointment</p>
                <p class="stat-main-text">March 14, 09:30 AM</p>
            </div>
        </div>

        <div class="info-box-card">
            <div class="icon-wrapper-circle bg-green-soft">
                <i class="fas fa-history"></i>
            </div>
            <div class="stat-details">
                <p class="stat-title-label">Total Visits</p>
                <p class="stat-main-text">8 Completed Visits</p>
            </div>
        </div>
    </div>
</main>

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