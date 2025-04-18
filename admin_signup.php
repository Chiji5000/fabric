<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['admin_access_granted']) || $_SESSION['admin_access_granted'] !== true) {
    header('Location: admin_gate.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Sign Up</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .signup-container {
            background-color: #1e293b;
            padding: 2rem 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 400px;
        }

        .signup-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #38bdf8;
        }

        .signup-container label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .signup-container input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1.25rem;
            border: none;
            border-radius: 5px;
            background-color: #334155;
            color: #fff;
        }

        .signup-container input[type="submit"] {
            background-color: #38bdf8;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .signup-container input[type="submit"]:hover {
            background-color: #0ea5e9;
        }

        .signup-container p {
            text-align: center;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .signup-container a {
            color: #38bdf8;
            text-decoration: none;
        }

        .signup-container a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .signup-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="signup-container">
        <h2>Admin Sign Up</h2>
        <form action="process_admin_signup.php" method="post" id="signupForm">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required pattern="[a-zA-Z0-9_]{3,}" title="At least 3 characters, letters, numbers or underscores only">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" title="At least 6 characters long">

            <input type="submit" value="Register as Admin">
        </form>
        <p>Already an admin? <a href="admin_login.php">Sign in here</a></p>
    </div>

    <script>
        document.getElementById("signupForm").addEventListener("submit", function(event) {
            var username = document.getElementById("username").value;
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;

            // Regex for username validation: At least 3 characters, letters, numbers, or underscores
            var usernameRegex = /^[a-zA-Z0-9_]{3,}$/;
            // Regex for email validation: Standard email format
            var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            // Regex for password validation: At least 8 characters, one uppercase letter, one lowercase letter, and one digit
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

            // Check if username is valid
            if (!usernameRegex.test(username)) {
                alert("Username must be at least 3 characters and contain only letters, numbers, or underscores.");
                event.preventDefault();
                return false;
            }

            // Check if email is valid
            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address.");
                event.preventDefault();
                return false;
            }

            // Check if password matches the required pattern
            if (!passwordPattern.test(password)) {
                alert("Password must be at least 6 characters long, contain at least one uppercase letter, one lowercase letter, and one digit.");
                event.preventDefault();
                return false;
            }

            // If all validations pass, allow form submission
            return true;
        });
    </script>
</body>

</html>