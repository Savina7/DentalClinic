<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
require __DIR__ . '/../auth/db.php';
require_once __DIR__ . '/../auth/session_15.php';

// Fetch pending appointments from database
$stmt = $conn->prepare("SELECT * FROM appointments WHERE status = 'pending' ORDER BY date ASC, time ASC");
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
$activePage = 'appointments'; // ose 'dentists', 'appointments', 'patients', sipas faqes
include '../includes/login/menu.php';
?>
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-titles">
                <h1>Pending Appointments</h1>
                <p>You have <strong><?php echo count($appointments); ?></strong> new requests to review.</p>
            </div>
        </header>

        <div class="appointments-list">
            
            <?php foreach ($appointments as $app): ?>
                <div class="appointment-card">
                    <div class="card-top">
                        <div class="patient-info">
                            <div class="avatar"><i class="fas fa-user"></i></div>
                            <div>
                                <h3><?php echo $app['fullname']; ?></h3>
                                <span class="service-tag"><?php echo $app['service']; ?></span>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button class="btn-reject"><i class="fas fa-times-circle"></i> Reject</button>
                            <button class="btn-approve"><i class="fas fa-check-circle"></i> Approve</button>
                        </div>
                    </div>

                    <div class="card-details">
                        <div class="detail-item">
                            <i class="far fa-calendar-alt"></i> 
                            <?php echo date('l, F j, Y', strtotime($app['date'])); ?>
                        </div>
                        <div class="detail-item">
                            <i class="far fa-clock"></i> 
                            <?php echo date('h:i A', strtotime($app['time'])); ?>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-phone-alt"></i> 
                            <?php echo $app['phone']; ?>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-user-md"></i> 
                            <?php echo $app['dentist']; ?>
                        </div>
                    </div>

                    <div class="patient-note">
                        <strong>Note:</strong> <?php echo $app['message']; ?>
                    </div>
                    
                    <a href="#" class="view-details">View Full Details &rarr;</a>
                </div>
            <?php endforeach; ?>

        </div>
    </main>
</div>



</body>
</html>