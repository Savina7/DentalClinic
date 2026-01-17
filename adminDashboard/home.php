<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
require_once __DIR__ . '/../auth/session_15.php';
require_once __DIR__ . '/../auth/db.php';

// Get admin name
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT firstname FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$user_name = $admin['firstname'] ?? "Admin";

// Get today's appointments
$stmt = $conn->prepare("
    SELECT 
        a.id,
        a.service,
        a.appointment_time,
        a.status,
        u.firstname,
        u.lastname
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE DATE(a.appointment_date) = CURDATE()
    ORDER BY a.appointment_time ASC
");
$stmt->execute();
$today_appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$today_count = count($today_appointments);

// Get pending appointments count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'");
$stmt->execute();
$pending_count = $stmt->get_result()->fetch_assoc()['count'];
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
$activePage = 'home';
include '../includes/login/menu.php';
?>

<!-- ===== MAIN CONTENT ===== -->
<main class="layout-content">
    <header class="welcome-section-header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
        <p>Here is a quick overview for today.</p>
    </header>

    <!-- ===== STATS CARDS ===== -->
    <div class="dashboard-cards">
        <div class="info-box-card">
            <div class="icon-wrapper-circle bg-blue-soft">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-details">
                <p class="stat-title-label">Appointments Today</p>
                <p class="stat-main-text"><?php echo $today_count; ?> Appointments</p>
            </div>
        </div>

        <div class="info-box-card">
            <div class="icon-wrapper-circle bg-orange-soft">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <p class="stat-title-label">Pending Approvals</p>
                <p class="stat-main-text"><?php echo $pending_count; ?> Pending</p>
            </div>
        </div>
    </div>

    <!-- ===== TODAY'S SCHEDULE ===== -->
    <section class="today-schedule-section">
        <h2 class="section-heading">Today's Schedule</h2>
        
        <?php if ($today_count > 0): ?>
            <div class="schedule-list">
                <?php foreach ($today_appointments as $apt): ?>
                    <div class="schedule-item <?php echo $apt['status']; ?>">
                        <div class="schedule-time">
                            <i class="fas fa-clock"></i>
                            <?php echo date('H:i', strtotime($apt['appointment_time'])); ?>
                        </div>
                        <div class="schedule-details">
                            <h3><?php echo htmlspecialchars($apt['firstname'] . ' ' . $apt['lastname']); ?></h3>
                            <p><?php echo htmlspecialchars($apt['service']); ?></p>
                        </div>
                        <div class="schedule-status">
                            <span class="status-badge status-<?php echo $apt['status']; ?>">
                                <?php echo ucfirst($apt['status']); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <p>No appointments scheduled for today</p>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
