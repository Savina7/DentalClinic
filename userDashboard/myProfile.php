<?php
require_once __DIR__ . '/../auth/auth_check.php';
$user_name = "Savina";
$user_surname = "Duka";
$user_email = "savina@email.com";
$user_phone = "+355 69 000 0000";
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
                <img src="/smile-dental/image/profilePhoto.jpg" id="profilePic" alt="Profile">
                <div class="overlay"><i class="fas fa-camera"></i></div>
                <input type="file" id="imageUpload" accept="image/*" style="display: none;">
            </div>
        </div>

        <div class="profile-right">
            <div class="info-row">
                <strong>First Name:</strong> <span><?php echo $user_name; ?></span>
            </div>
            <div class="info-row">
                <strong>Last Name:</strong> <span><?php echo $user_surname; ?></span>
            </div>
            <div class="info-row">
                <strong>Email:</strong>
                <span id="emailText"><?php echo $user_email; ?></span>
                <input type="email" id="emailInput" class="edit-input" value="<?php echo $user_email; ?>">
            </div>
            <div class="info-row">
                <strong>Phone:</strong>
                <span id="phoneText"><?php echo $user_phone; ?></span>
                <input type="text" id="phoneInput" class="edit-input" value="<?php echo $user_phone; ?>">
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
        emailInput.style.display = "block";
        phoneInput.style.display = "block";
        
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
        emailText.innerText = emailInput.value;
        phoneText.innerText = phoneInput.value;
        resetUI();
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