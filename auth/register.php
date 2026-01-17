<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account – Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/style.css">
</head>
<body class="auth-body">

    <img src="/smile-dental/image/background.jpg" alt="" class="bg-image">

    <div class="register-container">
        <h2 class="register-title">Create Account</h2>
        
        <form action="/smile-dental/auth/process-register.php" method="POST" class="register-form" id="registerForm">
            
            <!-- First Name -->
            <div class="form-group">
                <label class="form-label" for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" class="form-input" placeholder="Enter your first name" required>
                <span id="firstname_message" class="text-danger"></span>
            </div>

            <!-- Last Name -->
            <div class="form-group">
                <label class="form-label" for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" class="form-input" placeholder="Enter your last name" required>
                <span id="lastname_message" class="text-danger"></span>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                <span id="email_message" class="text-danger"></span>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="************" required>
                <span id="password_message" class="text-danger"></span>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="************" required>
                <span id="confirm_password_message" class="text-danger"></span>
            </div>

            <!-- Terms -->
            <div class="terms-container">
                <input type="checkbox" id="terms" name="terms" class="form-checkbox" required>
                <label for="terms" class="terms-text">
                    I agree to the <a href="#" class="terms-link">Terms & Conditions</a>
                </label>
                <br>
                <span id="terms_message" class="text-danger"></span>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit">Register Now</button>
            
        </form>

        <div class="login-redirect">
            <p class="redirect-text">Already have an account? <a href="/smile-dental/auth/login.php" class="login-link">Login here</a></p>
        </div>
    </div>

       
       <?php include '../includes/nologin/auth-footer.php'; ?>
<!-- Load jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Toastr JS  MSOJEEEE kte -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Validation JS -->
<script>
$(document).ready(function () {

    $(".register-form").submit(function (event) {
        event.preventDefault(); // Stop form from submitting immediately

        // Get values
        let firstName = $("#firstname").val();
        let lastName = $("#lastname").val();
        let email = $("#email").val();
        let password = $("#password").val();
        let confirm_password = $("#confirm_password").val();
        let terms = $("#terms").is(":checked");

        // Regex patterns
        let name_regex = /^[a-zA-Z]{3,40}$/;
        let email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let password_regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        let error = 0;

        // First Name validation
        if (!name_regex.test(firstName)) {
            $("#firstname").addClass("is-invalid");
            $("#firstname_message").text("Please enter a valid first name (letters only, 3-40 characters)");
            error++;
        } else {
            $("#firstname").removeClass("is-invalid");
            $("#firstname_message").text("");
        }

        // Last Name validation
        if (!name_regex.test(lastName)) {
            $("#lastname").addClass("is-invalid");
            $("#lastname_message").text("Please enter a valid last name (letters only, 3-40 characters)");
            error++;
        } else {
            $("#lastname").removeClass("is-invalid");
            $("#lastname_message").text("");
        }

        // Email validation
        if (!email_regex.test(email)) {
            $("#email").addClass("is-invalid");
            $("#email_message").text("Please enter a valid email address");
            error++;
        } else {
            $("#email").removeClass("is-invalid");
            $("#email_message").text("");
        }

        // Password validation
        if (password.trim() === "") {
            $("#password").addClass("is-invalid");
            $("#password_message").text("Password cannot be empty");
            error++;
        } else {
            $("#password").removeClass("is-invalid");
            $("#password_message").text("");
        }


        if (!password_regex.test(password)) {
            $("#password").addClass("is-invalid");
            $("#password_message").text("Password must be at least 8 characters long, include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.");
        error++;
        } else {
            $("#password").removeClass("is-invalid");
            $("#password_message").text("");
        }

        // Confirm Password validation
        if (confirm_password !== password) {
            $("#confirm_password").addClass("is-invalid");
            $("#confirm_password_message").text("Passwords do not match. Please try again.");
            error++;
        } else {
            $("#confirm_password").removeClass("is-invalid");
            $("#confirm_password_message").text("");
        }

        // Terms validation
        if (!terms) {
            alert("You must agree to the Terms & Conditions to continue.");
            error++;
        }

        // Submit if no errors
         if (error === 0) {
    let formData = new FormData(this); // merr të gjitha input-et

    $.ajax({
        type: "POST",
        url: "process-register.php", // backend PHP
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            response = JSON.parse(response); // JSON nga back-end

            if(response.status == 200){
                toastr.success(response.message, "Success");
                setTimeout(function(){
                    window.location.href = response.location; // redirect
                }, 2000);
            } else {
                toastr.warning(response.message, "Warning");
            }
        },
        error: function(){
            toastr.error("Something went wrong!", "Error");
        }
    });
}

    });

});
</script>

</body>
</html>
