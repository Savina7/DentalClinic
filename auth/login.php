<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Smile Dental</title>
    <link rel="stylesheet" href="/smile-dental/style.css">
</head>
<body>

    <img src="/smile-dental/image/background.jpg" alt="" class="bg-image">

    <div class="login-container">
        <h2 class="login-title">Welcome Back</h2>
        
        <form action="/smile-dental/auth/process-login.php" method="POST" class="login-form" id="loginForm">
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                <span id="email_message" class="text-danger"></span>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                <span id="password_message" class="text-danger"></span>
            </div>

            <div class="forgot-pass">
                <a href="#" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="register-redirect">
            <p class="redirect-text">Don't have an account? <a href="/smile-dental/auth/register.php" class="register-link">Sign Up</a></p>
        </div>
    </div>
<?php include '../includes/nologin/auth-footer.php'; ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {

            $("#loginForm").submit(function (event) {
                event.preventDefault(); // prevent normal form submission

                let email = $("#email").val();
                let password = $("#password").val();

                let email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                let password_regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

                let error = 0;

                // Email validation
                if (!email_regex.test(email)) {
                    $("#email").addClass("is-invalid");
                    $("#email_message").text("Please enter a valid email address ");
                    error++;
                } else {
                    $("#email").removeClass("is-invalid");
                    $("#email_message").text("");
                }

                // Password validation
                if (!password_regex.test(password)) {
                    $("#password").addClass("is-invalid");
                    $("#password_message").text("Password must be at least 8 characters, with 1 uppercase, 1 lowercase, 1 number, and 1 special character.");
                    error++;
                } else {
                    $("#password").removeClass("is-invalid");
                    $("#password_message").text("");
                }

                // Submit if no errors
                 if (error === 0) {

            let formData = new FormData(this); // get all form data

            $.ajax({
                type: "POST",
                url: "process-login.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
                    response = JSON.parse(response);

                    if(response.status == 200){
                        toastr.success(response.message, "Success");
                        setTimeout(function(){
                            window.location.href = response.location; // redirect after success
                        }, 1500);
                    } else {
                        toastr.error(response.message, "Error");
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
