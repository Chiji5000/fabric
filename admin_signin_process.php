<?php
session_start();
include 'db_connect.php';
include 'activity_logger.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['username'];

            // Log login activity
            logActivity($conn, $user['username'], "Admin logged in", "admin_signin_process.php");

            header("Location: user_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: admin_login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: admin_login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
