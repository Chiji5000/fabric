<?php
require "./navbar.php";

// Redirect if already logged in
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    header("Location: index.php");  // Redirect to home page
    exit();
}

$error = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']);


?>

<?php if ($error): ?>
    <div class="alert-box">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>


<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #f0f0f0, #c0d6e4);
        transition: background-color 0.3s ease;
    }

    body.dark {
        background: linear-gradient(135deg, #1a1a1a, #333);
        color: #f0f0f0;
    }

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

    body.dark .container {
        background-color: #000 !important;
        color: #f0f0f0 !important;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 15px;
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

    .signup-link {
        text-align: center;
        margin-top: 20px;
    }

    .signup-link a {
        color: #0057a4;
        text-decoration: none;
    }

    .signup-link a:hover {
        text-decoration: underline;
    }

    .error {
        color: red;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .alert-box {
        margin: 20px auto;
        padding: 15px 25px;
        background-color: #ffdddd;
        color: #a70000;
        border-left: 5px solid #ff5c5c;
        width: 80%;
        border-radius: 5px;
        font-size: 16px;
        text-align: center;
    }
</style>
</head>

<body>
    <div class="container">
        <h2>Login to Your Account</h2>

        <?php
        // Check if there's an error parameter in the URL (from the login process)
        if (isset($_GET['error'])) {
            $error = $_GET['error'];

            if ($error == 'user_not_found') {
                echo "<script>alert('User not found! Please check your email and try again.');</script>";
            } elseif ($error == 'incorrect_password') {
                echo "<script>alert('Incorrect password! Please try again.');</script>";
            }
        }
        ?>

        <form id="loginForm" action="process_login.php" method="post" novalidate>
            <input type="text" name="email" id="email" placeholder="Email Address" required>
            <div class="error" id="emailError"></div>

            <input type="password" name="password" id="password" placeholder="Password" required>
            <div class="error" id="passwordError"></div>

            <input type="submit" value="Login">
        </form>
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>
    </div>

    <script>
        window.onload = () => {
            if (localStorage.getItem("mode") === "dark") {
                document.body.classList.add("dark");
            }
        };

        const form = document.getElementById('loginForm');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');

        form.addEventListener('submit', function(e) {
            let valid = true;

            const emailRegex = /^[^@]+@[^@]+\.[a-z]{2,6}$/i;
            if (!emailRegex.test(email.value)) {
                emailError.textContent = "Please enter a valid email.";
                valid = false;
            } else {
                emailError.textContent = "";
            }

            if (password.value.length < 6) {
                passwordError.textContent = "Password must be at least 6 characters.";
                valid = false;
            } else {
                passwordError.textContent = "";
            }

            if (!valid) e.preventDefault();
        });

        setTimeout(() => {
            const alert = document.querySelector('.alert-box');
            if (alert) alert.style.display = 'none';
        }, 8000);
    </script>

    <?php include 'footer.php'; ?>