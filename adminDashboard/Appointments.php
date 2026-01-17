<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
require_once __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/session_15.php';

// Fetch pending appointments + user info
$stmt = $conn->prepare("
    SELECT a.*, u.firstname, u.lastname, u.phone 
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.status = 'pending'
    ORDER BY a.appointment_date ASC, a.appointment_time ASC
");
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Manage Appointments</title>

<link rel="stylesheet" href="/smile-dental/styleAdmin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<?php
$activePage = 'appointments';
include __DIR__ . '/../includes/login/menu.php';
?>

<main class="admin-main">

    <header class="admin-header">
        <div class="header-titles">
            <h1>Pending Appointments</h1>
            <p>You have <strong><?php echo count($appointments); ?></strong> new requests to review.</p>
        </div>
    </header>

    <div class="appointments-list">

        <?php if (count($appointments) === 0): ?>
            <p>No pending appointments.</p>
        <?php else: ?>

            <?php foreach ($appointments as $app): ?>

                <div class="appointment-card" id="appointment-<?php echo $app['id']; ?>">

                    <div class="card-top">

                        <div class="patient-info">

                            <div>
                                <h3>
                                    <?php echo htmlspecialchars($app['firstname'] . ' ' . $app['lastname']); ?>
                                </h3>
                                <span class="service-tag">
                                    <?php echo htmlspecialchars($app['service']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="action-buttons">

                            <!-- APPROVE WITH PRICE -->
                            <form action="update_appointment.php" method="POST" class="approve-form-unified">
                                <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                <input type="hidden" name="action" value="approve">

                                <div class="approval-group">
                                    <span class="currency-prefix">€</span>
                                    <input
                                        type="number"
                                        name="price"
                                        step="0.01"
                                        min="1"
                                        placeholder="0.00"
                                        required
                                        class="bare-price-input"
                                    >
                                    <button type="submit" class="btn-approve-unified">
                                        Approve
                                    </button>
                                </div>
                            </form>

                            <!-- DENY -->
                            <form action="update_appointment.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                <input type="hidden" name="action" value="deny">

                                <button type="submit" class="btn-reject">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                            </form>

                        </div>
                    </div>

                    <!-- DETAILS -->
                    <div class="card-details">
                        <div class="detail-item">
                            <i class="far fa-calendar-alt"></i>
                            <?php echo date('l, F j, Y', strtotime($app['appointment_date'])); ?>
                        </div>

                        <div class="detail-item">
                            <i class="far fa-clock"></i>
                            <?php echo date('h:i A', strtotime($app['appointment_time'])); ?>
                        </div>

                        <div class="detail-item">
                            <i class="fas fa-phone-alt"></i>
                            <?php echo htmlspecialchars($app['phone']); ?>
                        </div>
                    </div>

                    <!-- NOTE -->
                    <div class="patient-note">
                        <strong>Note:</strong>
                        <?php echo htmlspecialchars($app['message']); ?>
                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</main>

</body>
</html>
