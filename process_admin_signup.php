<?php
session_start();
include 'db_connect.php'; // use your existing database

// Only allow access if admin_gate allowed it
if (!isset($_SESSION['admin_access_granted']) || $_SESSION['admin_access_granted'] !== true) {
    header('Location: admin_gate.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check for existing admin
    $check = $conn->prepare("SELECT id FROM admins WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();
        die("An admin with this email or username already exists.");
    }
    $check->close();

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into admins table
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        unset($_SESSION['admin_access_granted']); // Revoke access after signup
        echo "Admin account created successfully. <a href='admin_login.php'>Login here</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: admin_signup.php");
    exit;
}
