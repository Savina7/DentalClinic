<?php
require_once __DIR__ . '/../auth/session_15.php';
require_once __DIR__ . '/../auth/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ===================== USER NAME ===================== */
$stmt = $conn->prepare("SELECT firstname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_name = $user['firstname'] ?? "Filan";

/* ===================== ALL APPOINTMENTS ===================== */
$stmt = $conn->prepare("
    SELECT id, service, status, price, payment_status, appointment_date, appointment_time
    FROM appointments
    WHERE user_id = ?
    ORDER BY appointment_date ASC, appointment_time ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ===================== NEXT APPOINTMENT (PAID + APPROVED) ===================== */
$stmt = $conn->prepare("
    SELECT service, appointment_date, appointment_time
    FROM appointments
    WHERE user_id = ?
      AND status = 'approved'
      AND payment_status = 'paid'
      AND appointment_date >= CURDATE()
    ORDER BY appointment_date ASC, appointment_time ASC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$nextAppointment = $stmt->get_result()->fetch_assoc();

/* ===================== TOTAL VISITS (PAID) ===================== */
$stmt = $conn->prepare("
    SELECT service, appointment_date, appointment_time
    FROM appointments
    WHERE user_id = ?
      AND status = 'approved'
      AND payment_status = 'paid'
    ORDER BY appointment_date DESC, appointment_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$visits = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$totalVisits = count($visits);
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
            <li><a href="home.php" class="nav-item-link active-nav-page"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="myProfile.php" class="nav-item-link"><i class="fas fa-user-circle"></i> My Profile</a></li>
            <li><a href="bookAppointment.php" class="nav-item-link"><i class="fas fa-plus-circle"></i> Book Now</a></li>
        </ul>
    </nav>

    <div class="logout-section-bottom">
        <a href="/smile-dental/auth/logout.php" class="nav-item-link logout-color">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>

<main class="layout-content">

<header class="welcome-section-header">
    <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
    <p>Here is your account summary for today.</p>
</header>

<!-- ===================== STATUS MESSAGES ===================== -->
<?php foreach ($appointments as $app): ?>
    <?php if ($app['status'] === 'approved' && $app['payment_status'] === 'unpaid'): ?>
        <section class="appointment-message approved">
            <h3>✅ Appointment Approved</h3>
            <p>Your request for <strong><?php echo htmlspecialchars($app['service']); ?></strong> has been accepted.</p>
            <p>Price: <strong><?php echo number_format($app['price'], 2); ?> €</strong></p>
            <a href="pay.php?id=<?php echo $app['id']; ?>" class="btn-pay">Pay Now</a>
        </section>
    <?php elseif ($app['status'] === 'denied'): ?>
        <section class="appointment-message denied">
            <h3>❌ Appointment Denied</h3>
            <p>Your request for <strong><?php echo htmlspecialchars($app['service']); ?></strong> was not accepted.</p>
        </section>
    <?php endif; ?>
<?php endforeach; ?>

<!-- ===================== DASHBOARD STATS ===================== -->
<div class="dashboard-stats-grid">

    <!-- NEXT APPOINTMENT -->
    <div class="info-box-card">
        <div class="icon-wrapper-circle bg-blue-soft">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-details">
            <p class="stat-title-label">Next Appointment</p>
            <p class="stat-main-text">
                <?php if ($nextAppointment): ?>
                    <?php
                        echo date('M d, Y', strtotime($nextAppointment['appointment_date']))
                        . ' • '
                        . date('H:i', strtotime($nextAppointment['appointment_time']));
                    ?>
                <?php else: ?>
                    —
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- TOTAL VISITS (TOGGLE) -->
    <div class="info-box-card">
        <div class="icon-wrapper-circle bg-green-soft">
            <i class="fas fa-history"></i>
        </div>
        <div class="stat-details">
            <details>
                <summary class="stat-title-label">
                    Total Visits: <?php echo $totalVisits; ?>
                </summary>

                <?php if ($totalVisits > 0): ?>
                    <ul>
                        <?php foreach ($visits as $v): ?>
                            <li>
                                <?php
                                    echo htmlspecialchars($v['service']) . ' – '
                                    . date('M d, Y', strtotime($v['appointment_date']))
                                    . ' ' . date('H:i', strtotime($v['appointment_time']));
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No completed visits yet.</p>
                <?php endif; ?>
            </details>
        </div>
    </div>

</div>

</main>

</body>
</html>
