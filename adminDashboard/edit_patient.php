<?php
require_once __DIR__ . '/../auth/admin_auth_check.php';
include __DIR__ . '/../auth/db.php'; // lidh databazën

if (!isset($_GET['id'])) {
    header("Location: patients.php");
    exit;
}

$id = $_GET['id'];

// Merr pacientin nga tabela users ku role = 'user'
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'user'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    header("Location: patients.php");
    exit;
}

$alert = ''; // për alert JS

// Kur forma të dërgohet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);

    // Kontrollo nëse emaili përdoret nga dikush tjetër
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ? AND role = 'user'");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $exists = $row[0];

    if ($exists > 0) {
        $alert = "This email already exists. Please use another email.";
    } else {
        // Përditëso pacientin
        $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, email=?, phone=? WHERE id=? AND role='user'");
        $stmt->bind_param("ssssi", $firstname, $lastname, $email, $phone, $id);
        $stmt->execute();

        // Kthehu te lista e pacientëve
        header("Location: patients.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Patient | Smile Dental Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../styleAdmin.css">
</head>
<body class="p-5">

<div class="container">
    <h2>Edit Patient: <?= htmlspecialchars($patient['firstname'] . ' ' . $patient['lastname']); ?></h2>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($patient['firstname']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($patient['lastname']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($patient['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($patient['phone']); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update Patient</button>
        <a href="patients.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php if($alert): ?>
<script>
    alert("<?= $alert; ?>");
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
