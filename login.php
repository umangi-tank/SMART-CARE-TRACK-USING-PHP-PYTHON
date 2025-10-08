<?php
session_start();
$login_error = '';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Backend Validation
    if (empty($email) || empty($password)) {
        $login_error = "Please enter both email and password.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@rku\.ac\.in$/", $email)) {
        $login_error = "Email must be an RKU email (e.g., student@rku.ac.in).";
    } elseif (strlen($password) < 5) {
        $login_error = "Password must be at least 5 characters long.";
    } else {
        // ✅ Successful login → Redirect to Student Dashboard
        $_SESSION['user_email'] = $email; // session store
        header("Location: student_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RKU CAREDESK | Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: "Gill Sans", "Gill Sans MT", Calibri, sans-serif;
            background: #f9f9f9;
            color: #333;
            min-height: 100vh;
        }

        .main {
            background-color: #F5F5DC !important;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #b71c1c;
        }

        .main h2 {
            color: #b71c1c;
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .form-control {
            border-radius: 4px;
            padding: 10px;
            font-family: "Gill Sans", sans-serif;
        }

        /* Placeholder Styling */
        ::placeholder {
            font-family: "Gill Sans", "Gill Sans MT", Calibri, sans-serif;
            font-size: 14px;
            color: #666;
        }

        .button {
            display: block;
            width: 100%;
            padding: 12px;
            background: #b71c1c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
            margin-top: 15px;
        }

        .button:hover {
            background: #880e4f;
            color: white;
        }

        .a {
            color: #424242;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .a:hover {
            color: #b71c1c;
            text-decoration: underline;
        }

        .alert-error {
            color: #b71c1c;
            background: #fbebeb;
            border: 1px solid #b71c1c;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }

        .back-button {
            margin-bottom: 10px;
            background: none;
            border: none;
            color: #b71c1c;
            font-size: 16px;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            font-weight: bold;
            transition: color 0.3s;
        }

        .back-button:hover {
            color: #880e4f;
            text-decoration: underline;
        }

        .back-button i {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center my-5">
        <div class="w-100">
            <div style="max-width: 450px; margin: 0 auto;">
                <button type="button" class="back-button" onclick="goToIndexPage()">
                    <i class="fas fa-arrow-left"></i> BACK TO HOME
                </button>
            </div>
            <div class="main p-5 border shadow-sm mx-auto" style="max-width: 450px;">
                
                <h2><i class="fas fa-user-tie me-2"></i>SIGN IN</h2>

                <!-- PHP Error -->
                <?php 
                if (!empty($login_error)) {
                    echo '<div class="alert-error">' . htmlspecialchars($login_error) . '</div>';
                }
                ?>
                <!-- JavaScript Error -->
                <div id="js-error" class="alert-error d-none"></div>
                
                <form class="mt-3" method="post" id="form" onsubmit="return validateSignInForm()">
                    <div class="mb-3">
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Example@rku.ac.in">
                    </div>
                    
                    <div class="mb-3">
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="Minimum 5 characters">
                    </div>
                    
                    <input type="submit" name="submit" value="Sign In" class="button">
                    
                    <p class="text-center mt-3 mb-0">
                        <a href="#" class="a">Forgot Password?</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function goToIndexPage() {
            window.location.href = "index.php"; 
        }

        function showError(message) {
            const errorBox = document.getElementById("js-error");
            errorBox.textContent = message;
            errorBox.classList.remove("d-none");
        }

        function clearError() {
            document.getElementById("js-error").classList.add("d-none");
        }

        function validateSignInForm() {
            clearError();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@rku\.ac\.in$/;

            if (email === "") {
                showError("Please enter your Email Address.");
                return false;
            }
            if (!emailPattern.test(email)) {
                showError("Email must be a valid RKU Email (e.g., student@rku.ac.in).");
                return false;
            }
            if (password === "") {
                showError("Please enter your Password.");
                return false;
            }
            if (password.length < 5) {
                showError("Password must be at least 5 characters long.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
