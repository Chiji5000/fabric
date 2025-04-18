<?php
session_start();
include 'db_connect.php';

// Debugging: Check if session variables are set
// echo '<pre>';
// print_r($_SESSION);  // For debugging session data
// echo '</pre>';

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header('Location: admin_login.php');
    exit;
}

// Check if the logged-in user is an admin by checking the `admins` table
$userEmail = $_SESSION['user_email'];
$query = "SELECT * FROM admins WHERE email = '$userEmail'";
$result = $conn->query($query);

// Check if the query was successful and if user is an admin
if ($result && $result->num_rows > 0) {
    // User is an admin, proceed
    $admin = $result->fetch_assoc();
} else {
    // User is not an admin, redirect or show an error message
    echo "You must be logged in as an admin to view this page.";
    exit;
}

// Fetch the admin's name from session
$adminName = $_SESSION['user_name'] ?? 'Admin'; // Default to 'Admin' if not set

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external stylesheet -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome Icons -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        .wrapper {
            display: flex;
            flex-wrap: wrap;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
            color: #ecf0f1;
            position: fixed;
            left: -250px;
            top: 0;
            transition: 0.3s ease;
            z-index: 1000;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: #ecf0f1;
            text-decoration: none;
            margin: 5px 0;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .content {
            margin-left: 0;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
            transition: margin-left 0.3s ease;
        }

        .topbar {
            background-color: #34495e;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
        }

        .topbar .welcome-msg {
            font-size: 18px;
        }

        .topbar .logout {
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
        }

        .topbar .logout:hover {
            background-color: #c0392b;
        }

        .hamburger {
            font-size: 30px;
            cursor: pointer;
            color: white;
            display: block;
        }

        .close-sidebar {
            display: none;
            font-size: 30px;
            color: white;
            cursor: pointer;
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
            margin-top: 30px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 23%;
            text-align: center;
            margin-bottom: 20px;
        }

        .card i {
            font-size: 40px;
            margin-bottom: 10px;
            color: #3498db;
        }

        .card h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .card p {
            color: #7f8c8d;
            font-size: 16px;
        }

        .card:hover {
            background-color: #ecf0f1;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #34495e;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        /* Media Queries for responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
                /* Hide sidebar off-screen by default */
            }

            .sidebar.open {
                left: 0;
                /* Show sidebar when open */
            }

            .content {
                margin-left: 0;
                /* Content takes full width when sidebar is hidden */
            }

            .hamburger {
                display: block;
                /* Always display hamburger on all screens */
            }

            .close-sidebar {
                display: block;
                margin-top: 50px;
                /* Display close icon in sidebar */
            }

            .dashboard-cards .card {
                width: 100%;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar .welcome-msg {
                font-size: 18px;
                position: relative;
                left: 30% !important;
            }
        }

        @media (min-width: 769px) {
            .hamburger {
                display: block;
                /* Display hamburger on large screens as well */
            }

            .close-sidebar {
                display: block;
                margin-top: 50px;
                /* Always show close icon */
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <a href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="admin_users.php"><i class="fas fa-users"></i> Users</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

            <div class="close-sidebar" onclick="toggleSidebar()">×</div> <!-- Close icon -->
        </div>

        <!-- Content Area -->
        <div class="content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="hamburger" onclick="toggleSidebar()">
                    &#9776; <!-- Hamburger Icon -->
                </div>
                <div class="welcome-msg">Welcome, <?php echo $adminName; ?>!</div>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <i class="fas fa-users"></i>
                    <h3>Manage Users</h3>
                    <a href="./admin_users.php">
                        <p>View and manage registered users</p>
                    </a>
                </div>
                <div class="card">
                    <i class="fas fa-boxes"></i>
                    <h3>Add Products</h3>
                    <a href="./admin_add_product.php">
                        <p>Manage products and inventory</p>
                    </a>
                </div>
                <div class="card">
                    <i class="fas fa-cogs"></i>
                    <h3>Messages</h3>
                    <a href="./admin_view_messages.php">
                        <p>View user messages and feedback</p>
                    </a>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Manage Products</h3>
                    <a href="./admin_manage_products.php">
                        <p>Edit and Delete Products</p>
                    </a>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line"></i>
                    <h3>View Users Ordered History</h3>
                    <a href="./order_tracking_admin.php">
                        <p>Ordered History</p>
                    </a>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Login Metrics</h3>
                    <a href="./admin_metrics.php">
                        <p>See all Login Metrics</p>
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <footer>
                <p>&copy; 2025 Admin Dashboard | All rights reserved</p>
            </footer>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.content').classList.toggle('open');
        }
    </script>

</body>

</html>