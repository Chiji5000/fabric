<?php
session_start();
include 'db_connect.php';
include 'activity_logger.php'; // Assuming this file has the logActivity() function

if (!isset($_SESSION['user_id']) || empty($_POST['message'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = htmlspecialchars(trim($_POST['message']));

$stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
if ($stmt) {
    $stmt->bind_param("is", $user_id, $message);
    if ($stmt->execute()) {
        logActivity($conn, $_SESSION['user_id'], "User sent a message", "send_message.php");
        header("Location: index.php?message=sent");
    } else {
        logActivity($conn, $_SESSION['user_id'], "Failed to send message", "send_message.php");
        header("Location: index.php?error=failed_to_send");
    }
    $stmt->close();
} else {
    logActivity($conn, $_SESSION['user_id'], "DB Error: Failed to prepare message insert", "send_message.php");
    header("Location: index.php?error=server_error");
}

exit;
