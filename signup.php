<?php
// Redirect if already logged in
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    header("Location: index.php");  // Redirect to home page
    exit();
}
require "./navbar.php";
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == "Signup successful!"): ?>
    <script>
        // Show success alert and redirect after 4 seconds
        alert("User successfully registered!");
        setTimeout(function() {
            window.location.href = "login.php"; // Redirect to login page
        }, 4000); // Wait for 4 seconds before redirect
    </script>
<?php endif; ?>
<style>
    .container {
        width: 100%;
        max-width: 450px;
        margin: 80px auto;
        background-color: white;
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    body.dark {
        background: linear-gradient(135deg, #1a1a1a, #333);
        color: #f0f0f0;
    }

    body.dark .container {
        background-color: #000;
        color: #f0f0f0;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
        font-size: 16px;
    }

    input[type="submit"] {
        width: 100%;
        background-color: #0057a4;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #003f7f;
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
        font-size: 15px;
    }

    .login-link a {
        color: #0057a4;
        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .error {
        color: red;
        font-size: 13px;
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <h2>Create Your Account</h2>
    <form id="signupForm" action="signup_process.php" method="post" novalidate>
        <input type="text" name="username" id="username" placeholder="Full Name" required>
        <div class="error" id="nameError"></div>

        <input type="email" name="email" id="email" placeholder="Email Address" required>
        <div class="error" id="emailError"></div>

        <input type="password" name="password" id="password" placeholder="Password" required>
        <div class="error" id="passwordError"></div>

        <input type="submit" value="Sign Up">
    </form>
    <div class="login-link">
        Already have an account? <a href="login.php">Sign In</a>
    </div>
    <div class="login-link">
        Are you an Admin? <a href="admin_login.php">Sign In</a>
    </div>
</div>

<script>
    // Dark mode from localStorage
    window.onload = () => {
        if (localStorage.getItem("mode") === "dark") {
            document.body.classList.add("dark");
        }
    };

    // List of permitted email domains
    const permittedDomains = [
        "gmail.com", // Add your allowed domains here
        "yahoo.com", // Add more allowed domains
        "hotmail.com"
    ];

    // Regex validation for form fields
    const form = document.getElementById('signupForm');
    const nameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');

    form.addEventListener('submit', (e) => {
        let valid = true;

        // Name: only letters and spaces
        const nameRegex = /^[A-Za-z ]{3,}$/;
        if (!nameRegex.test(nameInput.value)) {
            nameError.textContent = "Name must be at least 3 letters and contain only alphabets.";
            valid = false;
        } else {
            nameError.textContent = "";
        }

        // Email: standard email regex
        const emailRegex = /^[^@]+@[^@]+\.[a-z]{2,6}$/i;
        if (!emailRegex.test(emailInput.value)) {
            emailError.textContent = "Please enter a valid email address.";
            valid = false;
        } else {
            emailError.textContent = "";
        }

        // Check if email domain is permitted
        const emailDomain = emailInput.value.split('@')[1];
        if (!permittedDomains.includes(emailDomain)) {
            alert("Invalid email domain. Please use an allowed domain.");
            valid = false;
        }

        // Password: min 6 chars, at least 1 letter & 1 number
        const passRegex = /^(?=.*[A-Za-z])(?=.*\d).{6,}$/;
        if (!passRegex.test(passwordInput.value)) {
            passwordError.textContent = "Password must be at least 6 characters, including a letter and a number.";
            valid = false;
        } else {
            passwordError.textContent = "";
        }

        if (!valid) e.preventDefault();
    });
</script>

<?php include 'footer.php'; ?>