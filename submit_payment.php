<?php
include 'db_connect.php';
include 'activity_logger.php';
$user_name = $_POST['user_name'];
$amount = $_POST['amount'];
$status = $_POST['status'];
if ($conn->query("INSERT INTO payments (user_name, amount, status) VALUES ('$user_name', '$amount', '$status')")) {
    logActivity($conn, $user_name, "Requested payment", "payment_request.php");
    header("Location: payment_request.php?msg=Payment request submitted.");
} else {
    echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
}
