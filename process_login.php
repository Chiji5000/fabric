<?php
session_start();
include 'db_connect.php';
include 'activity_logger.php';

$ip = $_SERVER['REMOTE_ADDR'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check failed attempts in the last 15 minutes
    $stmt = $conn->prepare("SELECT COUNT(*) AS attempts FROM login_attempts 
                            WHERE email = ? AND ip_address = ? AND success = 0 
                            AND created_at > (NOW() - INTERVAL 15 MINUTE)");
    $stmt->bind_param("ss", $email, $ip);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $failed_attempts = $result['attempts'];

    if ($failed_attempts >= 3) {
        $_SESSION['error_message'] = "Too many failed attempts. Try again in 15 minutes.";
        header("Location: login.php");
        exit;
    }

    // Try to fetch user
    $result = $conn->query("SELECT id, username, email, password FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // ✅ SUCCESSFUL LOGIN
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Log success attempt
            $stmt = $conn->prepare("INSERT INTO login_attempts (email, success, ip_address) VALUES (?, 1, ?)");
            $stmt->bind_param("ss", $email, $ip);
            $stmt->execute();

            // ✅ Clear failed attempts on success
            $stmt = $conn->prepare("DELETE FROM login_attempts WHERE email = ? AND ip_address = ? AND success = 0");
            $stmt->bind_param("ss", $email, $ip);
            $stmt->execute();

            header("Location: index.php");
            exit;
        } else {
            // ❌ WRONG PASSWORD
            $stmt = $conn->prepare("INSERT INTO login_attempts (email, success, ip_address) VALUES (?, 0, ?)");
            $stmt->bind_param("ss", $email, $ip);
            $stmt->execute();

            // ⏰ Send alert message to user's inbox
            $timestamp = date("Y-m-d H:i:s");
            $message = "⚠️ Failed login attempt to your account on $timestamp from IP address: $ip.";

            $is_from_admin = 0;
            $is_read = 0;
            $read_status = "unread";
            $user_id = $user['id']; // get user_id for the message table

            $stmt = $conn->prepare("INSERT INTO messages (user_id, message, is_from_admin, created_at, is_read, read_status) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isisss", $user_id, $message, $is_from_admin, $timestamp, $is_read, $read_status);
            $stmt->execute();

            header("Location: login.php?error=incorrect_password");
            exit;
        }
    } else {
        // ❌ USER NOT FOUND
        $stmt = $conn->prepare("INSERT INTO login_attempts (username, success, ip_address) VALUES (?, 0, ?)");
        $stmt->bind_param("ss", $email, $ip);
        $stmt->execute();

        header("Location: login.php?error=user_not_found");
        exit;
    }
}
?>



<!DOCTYPE html>
<html>

<head>
    <title>Login Error</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 30px;
            background-color: #f5f5f5;
            text-align: center;
        }

        .error-box {
            display: inline-block;
            background-color: #ffe0e0;
            padding: 20px 30px;
            border-radius: 10px;
            color: #d8000c;
        }

        a {
            color: #0057a4;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <h2>Login Failed</h2>
        <p><?php echo isset($error) ? $error : "Unknown error occurred."; ?></p>
        <p><a href="login.php">← Back to Login</a></p>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>