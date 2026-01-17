<?php
require_once __DIR__ . '/../auth/session_15.php'; // session + 15min timeout
require_once __DIR__ . '/../auth/db.php';        // lidhja me DB

// Kontrollo nëse user është loguar
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Merr info nga DB
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT firstname, lastname, email, phone, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fallback nëse nuk gjendet user
$user_name = $user['firstname'] ?? "SomethingIsWrong";
$user_surname = $user['lastname'] ?? "SomethingIsWrong";
$user_email = $user['email'] ?? "SomethingIsWrong";
$user_phone = $user['phone'] ?? "0000000000";
$profile_img = $user['profile_image'] ?? "/smile-dental/image/profilePhoto.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | Smile Dental</title>
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
            <li><a href="home.php" class="nav-item-link"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="myProfile.php" class="nav-item-link active-nav-page"><i class="fas fa-user-circle"></i> My Profile</a></li>
            <li><a href="bookAppointment.php" class="nav-item-link"><i class="fas fa-plus-circle"></i> Book Now</a></li>
        </ul>
    </nav>
    <div class="logout-section-bottom">
    <a href="/smile-dental/auth/logout.php" class="nav-item-link logout-color">
        <i class="fas fa-sign-out-alt nav-icon-style"></i> Logout
    </a>
</div>
</aside>

<main class="layout-content">
    <header class="welcome-section-header">
        <h1>My Profile</h1>
        <p>Manage your account settings</p>
    </header>

    <div class="profile-card">
        <h2>Profile Details</h2>
        
        <div class="profile-left">
            <div class="profile-image-wrapper" onclick="document.getElementById('imageUpload').click();">
                <img src="<?php echo $profile_img; ?>" id="profilePic" alt="Profile">
                <div class="overlay"><i class="fas fa-camera"></i></div>
                <input type="file" id="imageUpload" accept="image/*" style="display: none;">
            </div>
        </div>

        <div class="profile-right">
            <div class="info-row">
                <strong>First Name:</strong> <span><?php echo htmlspecialchars($user_name); ?></span>
            </div>
            <div class="info-row">
                <strong>Last Name:</strong> <span><?php echo htmlspecialchars($user_surname); ?></span>
            </div>
            <div class="info-row">
                <strong>Email:</strong>
                <span id="emailText"><?php echo htmlspecialchars($user_email); ?></span>
                <input type="email" id="emailInput" class="edit-input" value="<?php echo htmlspecialchars($user_email); ?>" style="display:none;">
            </div>
            <div class="info-row">
                <strong>Phone:</strong>
                <span id="phoneText"><?php echo htmlspecialchars($user_phone); ?></span>
                <input type="text" id="phoneInput" class="edit-input" value="<?php echo htmlspecialchars($user_phone); ?>" style="display:none;">
            </div>

            <div class="action-buttons">
                <button id="editBtn" class="btn-blue">Edit Profile</button>
                <div id="editGroup" style="display: none;">
                    <button id="saveBtn" class="btn-green">Save</button>
                    <button id="cancelBtn" class="btn-red">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const editBtn = document.getElementById("editBtn");
const editGroup = document.getElementById("editGroup");
const saveBtn = document.getElementById("saveBtn");
const cancelBtn = document.getElementById("cancelBtn");

const emailText = document.getElementById("emailText");
const phoneText = document.getElementById("phoneText");
const emailInput = document.getElementById("emailInput");
const phoneInput = document.getElementById("phoneInput");
const profilePic = document.getElementById("profilePic");
const imageUpload = document.getElementById("imageUpload");

let backup = {};

editBtn.addEventListener("click", () => {
    backup = { email: emailText.innerText, phone: phoneText.innerText, img: profilePic.src };

    emailText.style.display = "none";
    phoneText.style.display = "none";
    emailInput.style.display = "inline-block";
    phoneInput.style.display = "inline-block";

    editBtn.style.display = "none";
    editGroup.style.display = "flex";
});

cancelBtn.addEventListener("click", () => {
    emailText.innerText = backup.email;
    phoneText.innerText = backup.phone;
    profilePic.src = backup.img;
    resetUI();
});

saveBtn.addEventListener("click", () => {
    const newEmail = emailInput.value;
    const newPhone = phoneInput.value;

    const formData = new FormData();
    formData.append("email", newEmail);
    formData.append("phone", newPhone);
    if(imageUpload.files[0]){
        formData.append("profile_image", imageUpload.files[0]);
    }

    fetch('saveProfile.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 200){
            emailText.innerText = newEmail;
            phoneText.innerText = newPhone;
            if(data.image_path){
                profilePic.src = data.image_path + "?t=" + new Date().getTime(); // cache busting
            }
            alert("Profile updated!");
        } else {
            alert("Error: " + data.message);
        }
        resetUI();
    });
});

function resetUI() {
    emailText.style.display = "inline";
    phoneText.style.display = "inline";
    emailInput.style.display = "none";
    phoneInput.style.display = "none";
    editBtn.style.display = "block";
    editGroup.style.display = "none";
}

imageUpload.addEventListener("change", function() {
    if (this.files && this.files[0]) {
        profilePic.src = URL.createObjectURL(this.files[0]);
    }
});
</script>
</body>
</html>
