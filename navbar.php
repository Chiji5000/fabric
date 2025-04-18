<?php
session_start();
include 'db_connect.php';
$currentPage = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;

// Query to get unread messages count
$unreadMessagesCount = 0;
if ($isLoggedIn) {
    $query = "SELECT COUNT(*) FROM messages WHERE user_id = ? AND read_status = 0";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $unreadMessagesCount = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>African Wrapper Fabric Store</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<!-- navbar.php -->
<style>
    /* Preloader Styling */
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #f8f9fa;
        /* Light background */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000 !important;
    }

    .loader {
        text-align: center;
    }

    .cart-icon {
        font-size: 60px;
        color: #ff6f61;
        /* Shopping cart color */
        animation: bounce 1.5s infinite;
    }

    .loading-text {
        margin-top: 20px;
        font-size: 18px;
        color: #333;
        font-family: Arial, sans-serif;
    }

    /* Bounce animation for the cart */
    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-20px);
        }
    }


    /* Message Icon */
    .message-icon {
        position: relative;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .message-icon .icon {
        font-size: 24px;
        color: #f8fafc;
    }

    .message-icon .unread-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #f00;
        color: white;
        font-size: 12px;
        font-weight: bold;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #message-bot {
        position: fixed;
        bottom: 30px;
        right: 30px;
    }

    .message-btn {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 15px;
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    #message-form {
        margin-top: 10px;
        background: white;
        padding: 10px;
        border: 1px solid #ccc;
    }

    /* Alert Styles */
    .alert {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #38bdf8;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 14px;
        display: none;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        z-index: 1001;
    }

    .alert.show {
        display: block;
        opacity: 1;
    }

    .alert .close-btn {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        padding: 0 5px;
    }

    .navbar {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 3rem !important;
        background-color: #0f172a;
        color: #f8fafc;
        position: relative;
        z-index: 1000;
    }

    .nav-links {
        display: flex;
        gap: 1.5rem;
    }

    .nav-links a {
        text-decoration: none;
        color: #f8fafc;
        padding: 0.3rem 0.6rem;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .nav-links a:hover,
    .nav-links .active {
        background-color: #1e293b;
        color: #38bdf8;
    }

    /* Hamburger */
    .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        gap: 5px;
        transition: all 0.3s ease;
    }

    .hamburger div {
        width: 25px;
        height: 3px;
        background-color: #f8fafc;
        transition: all 0.4s ease;
    }

    .hamburger.active div:nth-child(1) {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .hamburger.active div:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active div:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    /* Mobile Menu */
    .mobile-nav {
        display: none !important;
        flex-direction: column !important;
        background-color: #0f172a;
        position: absolute !important;
        top: 65px;
        right: 0;
        width: 100%;
        padding: 1rem 2rem;
        animation: slideIn 0.4s ease forwards;
        z-index: 10000000 !important;
    }

    .mobile-nav a {
        text-decoration: none;
        color: #f8fafc;
        padding: 0.8rem 0;
        border-bottom: 1px solid #334155;
        transition: all 0.3s;
        z-index: 10000000 !important;
    }

    .mobile-nav a:hover,
    .mobile-nav .active {
        color: #38bdf8;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 700px) {
        .nav-links {
            display: none !important;
        }

        .hamburger {
            display: flex !important;
        }

        .mobile-nav.show {
            display: flex !important;
        }

        .dark-toggle {
            position: absolute;
            top: 0.3rem;
            right: 1.5rem;
            z-index: 1001;
        }
    }

    .dark-toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
        margin-left: 1rem;
    }

    .dark-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        background-color: #ccc;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        transition: 0.4s;
        border-radius: 34px;
    }

    .slider::before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    .dark-toggle input:checked+.slider {
        background-color: #38bdf8;
    }

    .dark-toggle input:checked+.slider::before {
        transform: translateX(24px);
    }

    /* Dark Theme Classes */
    body.dark {
        background-color: #0f172a;
        color: #f8fafc;
    }

    body.dark .navbar {
        background-color: #1e293b;
    }

    body.dark .nav-links a {
        color: #f8fafc;
    }

    body.dark .nav-links a.active,
    body.dark .nav-links a:hover {
        background-color: #334155;
        color: #38bdf8;
    }

    body.dark .mobile-nav {
        background-color: #1e293b;
    }

    /* WhatsApp Floating Icon - Left Side */
    .whatsapp-float {
        position: fixed;
        bottom: 50px;
        left: 30px;
        /* Changed from right: 20px */
        background-color: #25d366;
        color: white;
        font-size: 2.4rem;
        padding: 0.3em 0.50em;
        border-radius: 50%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        text-align: center;
        z-index: 1000;
        transition: background-color 0.3s ease;
    }

    .whatsapp-float:hover {
        background-color: #1ebc57;
        color: white;
        text-decoration: none;
    }

    .logo img{
        width: 5% !important;
        height: 5% !important;
    }
</style>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <i class="fas fa-shopping-cart cart-icon"></i>
            <div class="loading-text">Loading your shopping experience...</div>
        </div>
    </div>


    <nav id="content" style="display:none;" class="navbar">
        <a href="index.php" class="logo"><img src="./images/my-logo.png" alt="logo"></a>

        <!-- Hamburger Icon -->
        <div class="hamburger" id="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </div>

        <div class="nav-links">
            <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Home</a>
            <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">Contact</a>

            <?php if (!$isLoggedIn): ?>
                <a href="signup.php" class="<?= $currentPage == 'signup.php' ? 'active' : '' ?>">SignUp</a>
                <a href="login.php" class="<?= $currentPage == 'login.php' ? 'active' : '' ?>">Login</a>
            <?php else: ?>
                <a href="logout.php">Logout</a>

                <div class="message-icon" onclick="window.location.href='messages.php'">
                    <span class="icon">📩</span>
                    <?php if ($unreadMessagesCount > 0): ?>
                        <div class="unread-count"><?= $unreadMessagesCount ?></div>
                    <?php endif; ?>
                </div>
                <?php
                $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                ?>
                <div class="message-icon" onclick="window.location.href='cart.php'">
                    <span class="icon">🛒</span>
                    <?php if ($cartCount > 0): ?>
                        <div class="unread-count"><?= $cartCount ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <label class="dark-toggle">
                <input type="checkbox" class="darkSwitch">
                <span class="slider"></span>
            </label>

        </div>
    </nav>

    <div class="mobile-nav" id="mobileNav">

        <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Home</a>
        <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">Contact</a>

        <?php if (!$isLoggedIn): ?>
            <a href="signup.php" class="<?= $currentPage == 'signup.php' ? 'active' : '' ?>">Sign Up</a>
            <a href="login.php" class="<?= $currentPage == 'login.php' ? 'active' : '' ?>">Login</a>
        <?php else: ?>
            <a href="logout.php">Logout</a>
            <div class="message-icon" onclick="window.location.href='messages.php'">
                <span class="icon">📩</span>
                <?php if ($unreadMessagesCount > 0): ?>
                    <div class="unread-count"><?= $unreadMessagesCount ?></div>
                <?php endif; ?>
            </div>
            <?php
            $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
            ?>
            <div class="message-icon" onclick="window.location.href='cart.php'">
                <span class="icon">🛒</span>
                <?php if ($cartCount > 0): ?>
                    <div class="unread-count"><?= $cartCount ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <label class="dark-toggle">
            <input type="checkbox" class="darkSwitch">
            <span class="slider"></span>
        </label>
    </div>

    <!-- Message bot and alert message -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div id="message-bot">
            <button onclick="toggleMessageForm()" class="message-btn">💬</button>
            <div id="message-form" style="display:none;">
                <form action="send_message.php" method="POST">
                    <textarea name="message" placeholder="Type your message..." required></textarea>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>

        <a href="https://wa.me/+2348108728887" class="whatsapp-float" target="_blank" title="Chat with Admin">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    <?php endif; ?>

    <!-- Alert Message -->
    <div id="alertMessage" class="alert">
        Message Sent!
        <button class="close-btn" onclick="closeAlert()">×</button>
    </div>

    <script>
        const hamburger = document.getElementById("hamburger");
        const mobileNav = document.getElementById("mobileNav");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            mobileNav.classList.toggle("show");
        });

        function toggleMobileNav(el) {
            el.classList.toggle("active");
            document.getElementById("mobileNav").classList.toggle("show");
        }

        const switches = document.querySelectorAll(".darkSwitch");
        const body = document.body;

        // Load preference
        if (localStorage.getItem("darkMode") === "true") {
            body.classList.add("dark");
            switches.forEach(s => s.checked = true);
        }

        switches.forEach(toggle => {
            toggle.addEventListener("change", () => {
                if (toggle.checked) {
                    body.classList.add("dark");
                    localStorage.setItem("darkMode", "true");
                    switches.forEach(s => s.checked = true); // sync both
                } else {
                    body.classList.remove("dark");
                    localStorage.setItem("darkMode", "false");
                    switches.forEach(s => s.checked = false); // sync both
                }
            });
        });

        function toggleMessageForm() {
            const form = document.getElementById('message-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function showAlert(message) {
            const alert = document.getElementById('alertMessage');
            alert.textContent = message;
            alert.classList.add('show');

            // Automatically hide the alert after 3 seconds
            setTimeout(() => {
                alert.classList.remove('show');
            }, 3000);
        }

        function closeAlert() {
            const alert = document.getElementById('alertMessage');
            alert.classList.remove('show');
        }

        let logoutTimer;

        function startLogoutTimer() {
            clearTimeout(logoutTimer);
            logoutTimer = setTimeout(() => {
                window.location.href = 'logout.php';
            }, 180000); // 3 minutes
        }

        // Reset timer on user activity
        ['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, startLogoutTimer, false);
        });

        startLogoutTimer(); // Start initially

        setTimeout(function() {
            document.getElementById("preloader").style.display = "none";
            document.getElementById("content").style.display = "block";
        }, 6000);
    </script>

</body>

</html>