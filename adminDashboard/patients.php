<?php
include __DIR__ . '/../auth/db.php'; 

// Merr pacientët nga tabela users ku role = 'user'
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'user' ORDER BY id ASC");
$stmt->execute();
$result = $stmt->get_result();
$patients = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients List | Smile Dental Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../styleAdmin.css">
</head>
<body class="patients-page-body">

    <?php
    $activePage = 'patients'; // E vendosim 'patients' që të jetë aktive në menu
    include '../includes/login/menu.php';
    ?>

    <main class="custom-section-wrapper">
        <header class="custom-page-header">
            <div>
                <h1>Patients List</h1>
                <p>You have <strong><?= count($patients); ?></strong> registered patients.</p>
            </div>
            <button class="custom-btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                <i class="fas fa-plus-circle"></i> Add New Patient
            </button>
        </header>

        <div class="custom-data-card">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($patients) > 0): ?>
                        <?php foreach($patients as $p): ?>
                        <tr>
                            <td class="patient-name">
                                <?= htmlspecialchars($p['firstname'] . ' ' . $p['lastname']); ?>
                            </td>
                            <td><?= htmlspecialchars($p['email']); ?></td>
                            <td><?= htmlspecialchars($p['phone']); ?></td>
                            <td>
                                <?php if($p['status'] == 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-container">
                                    <a href="edit_patient.php?id=<?= $p['id']; ?>" class="btn-edit-custom">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="delete_patient.php?id=<?= $p['id']; ?>" 
                                       class="btn-delete-custom" 
                                       onclick="return confirm('Are you sure you want to delete this patient?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                    <?php if($p['status'] == 'inactive'): ?>
                                        <a href="send_activation.php?id=<?= $p['id']; ?>" 
                                           class="btn btn-sm btn-warning mt-1">
                                           <i class="fas fa-envelope"></i> Send Activation Link
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                <i class="fas fa-user-injured fa-2x mb-3"></i><br>
                                No patients found in the database.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="modal fade" id="addPatientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <form action="save_patient.php" method="POST">
                    <div class="modal-header border-0 bg-light">
                        <h5 class="modal-title fw-bold">Register New Patient</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">First Name</label>
                            <input type="text" name="firstname" class="form-control" placeholder="John" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Name</label>
                            <input type="text" name="lastname" class="form-control" placeholder="Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+355 6X XXX XXXX">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="custom-btn-primary">Save Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
