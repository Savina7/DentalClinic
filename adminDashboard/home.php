<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
// Vetëm për test, pa login
$user_name = "Admin"; // mund ta ndryshosh si të duash
$today_appointments = 3; // numri i takimeve për sot
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/styleAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<?php
$activePage = 'home'; // ose 'dentists', 'appointments', 'patients', sipas faqes
include '../includes/login/menu.php';
?>


<!-- ===== MAIN CONTENT ===== -->
<main class="layout-content">
    <header class="welcome-section-header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
        <p>Here is a quick overview for today.</p>
    </header>

    <!-- ===== TODAY'S APPOINTMENTS CARD ===== -->
    <div class="dashboard-cards">
        <div class="info-box-card">
            <div class="icon-wrapper-circle bg-blue-soft">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-details">
                <p class="stat-title-label">Appointments Today</p>
                <p class="stat-main-text"><?php echo $today_appointments; ?> Appointments</p>
            </div>
        </div>
    </div>
</main>



</body>
</html>
