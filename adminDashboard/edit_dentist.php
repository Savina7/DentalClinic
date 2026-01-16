<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
include __DIR__ . '/../auth/db.php';

if (!isset($_GET['id'])) {
    header("Location: dentists.php");
    exit;
}

$id = $_GET['id'];

// Merr dentistin nga databaza
$stmt = $conn->prepare("SELECT * FROM dentists WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$dentist = $result->fetch_assoc();

if (!$dentist) {
    header("Location: dentists.php");
    exit;
}

// Kur forma të dërgohet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $description = $_POST['description'];

    // Kontrollo nëse është ngarkuar imazh i ri
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../image/' . $imageName);

        // Fshi imazhin e vjetër
        if (!empty($dentist['image']) && file_exists(__DIR__ . '/../image/' . $dentist['image'])) {
            unlink(__DIR__ . '/../image/' . $dentist['image']);
        }
    } else {
        $imageName = $dentist['image']; // ruaj imazhin e vjetër
    }

    // Përditëso dentistin
    $stmt = $conn->prepare("UPDATE dentists SET name=?, specialty=?, email=?, phone=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $specialty, $email, $phone, $description, $imageName, $id);
    $stmt->execute();

    header("Location: dentists.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Dentist | Smile Dental Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/smile-dental/styleAdmin.css">
</head>
<body class="admin-page-body">

<div class="container py-5">
    <h2 class="mb-4">Edit Dentist: <?= htmlspecialchars($dentist['name']); ?></h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($dentist['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Specialty</label>
            <select name="specialty" class="form-select" required>
                <option value="General Dentist" <?= $dentist['specialty']=='General Dentist'?'selected':'' ?>>General Dentist</option>
                <option value="Orthodontist" <?= $dentist['specialty']=='Orthodontist'?'selected':'' ?>>Orthodontist</option>
                <option value="Surgeon" <?= $dentist['specialty']=='Surgeon'?'selected':'' ?>>Surgeon</option>
                <option value="Periodontist" <?= $dentist['specialty']=='Periodontist'?'selected':'' ?>>Periodontist</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($dentist['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($dentist['phone']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description / Bio</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($dentist['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <?php if(!empty($dentist['image'])): ?>
                <img src="/smile-dental/image/<?= htmlspecialchars($dentist['image']); ?>" alt="" style="height:100px; margin-top:10px;">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update Dentist</button>
        <a href="dentists.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
