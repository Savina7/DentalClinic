<?php
include __DIR__ . '/../auth/db.php';

// Kontrollo nëse token ekziston në URL
if (!isset($_GET['token'])) {
    die("Invalid activation link.");
}

$token = $_GET['token'];

// Gjej pacientin me këtë token
$stmt = $conn->prepare("SELECT id, firstname, email FROM users WHERE activation_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired activation link.");
}

$user = $result->fetch_assoc();
$stmt->close();

// Nëse formi është dërguar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Update DB: vendos password, status = active, fshi token
        $stmt = $conn->prepare("UPDATE users SET password = ?, status = 'active', activation_token = NULL, must_change_password = 0 WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $user['id']);
        $stmt->execute();
        $stmt->close();

        // Redirect te login me mesazh suksesi
        header("Location: login.php?activated=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activate Your Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow" style="width: 380px;">
        <h4 class="mb-3 text-center">Activate Your Account</h4>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password_confirm" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Activate Account</button>
        </form>
        <small class="text-muted mt-2 d-block text-center">Welcome, <?= htmlspecialchars($user['firstname']); ?>!</small>
    </div>
</body>
</html>
