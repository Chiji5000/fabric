<?php
include 'db_connect.php';
$username = $_POST['username'];
$password = $_POST['password'];
$result = $conn->query("SELECT * FROM admins WHERE username = '$username' AND password = '$password'");
if ($result->num_rows > 0) {
    session_start();
    $_SESSION['admin'] = $username;
    header("Location: user_dashboard.php");
} else {
    echo "Invalid login.";
}
?>
