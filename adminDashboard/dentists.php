<?php
include __DIR__ . '/../auth/db.php';

// Merr dentistët nga databaza
$stmt = $conn->prepare("SELECT * FROM dentists ORDER BY id ASC");
$stmt->execute();
$result = $stmt->get_result();
$dentists = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dentists List | Smile Dental Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="/smile-dental/styleAdmin.css">
</head>
<body class="admin-page-body">

<!-- Sidebar -->
<?php
$activePage = 'dentists'; // ose 'dentists', 'appointments', 'patients', sipas faqes
include '../includes/login/menu.php';
?>

<!-- Main Content -->
<main class="layout-content">
    <header class="welcome-section-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Dentists List</h1>
            <p>You have <strong><?= count($dentists); ?></strong> registered specialists.</p>
        </div>
        <button class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addDentistModal">
            <i class="fas fa-plus-circle me-2"></i> Add New Dentist
        </button>
    </header>

    <div class="card border-0 shadow-sm p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle custom-patients-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Specialty</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($dentists) > 0): ?>
                        <?php foreach($dentists as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['name']); ?></td>
                            <td><span class="badge bg-light text-primary border"><?= htmlspecialchars($d['specialty']); ?></span></td>
                            <td><?= htmlspecialchars($d['email']); ?></td>
                            <td><?= htmlspecialchars($d['phone']); ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Edit Dentist -->
                                    <a href="edit_dentist.php?id=<?= $d['id']; ?>" class="btn-edit-custom">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <!-- Delete Dentist -->
                                    <a href="delete_dentist.php?id=<?= $d['id']; ?>" 
                                       class="btn-delete-custom"
                                       onclick="return confirm('Are you sure you want to delete this dentist?');">
                                       <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No dentists found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal për të shtuar dentist (me description + image) -->
<div class="modal fade" id="addDentistModal" tabindex="-1" aria-labelledby="addDentistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="save_dentist.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addDentistModalLabel">Register New Dentist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Dr. Arben Hoxha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Specialty</label>
                        <select name="specialty" class="form-select rounded-3" required>
                            <option value="">Select Specialty...</option>
                            <option value="General Dentist">General Dentist</option>
                            <option value="Orthodontist">Orthodontist</option>
                            <option value="Surgeon">Surgeon</option>
                            <option value="Periodontist">Periodontist</option>
                            <option value="Implantologist">Implantologist</option>
                            <option value="Endodontist">Endodontist</option>
                            <option value="Pediatric Dentist">Pediatric Dentist</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="email@smiledental.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control rounded-3" placeholder="+355 6X XXX XXXX" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Bio</label>
                        <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Write a short bio..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Profile Image</label>
                        <input type="file" name="image" class="form-control rounded-3" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Dentist</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
