<?php
include 'db_connect.php';
include 'activity_logger.php';

$username = $conn->real_escape_string($_POST['username']);
$email = $conn->real_escape_string($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hash

// Define a list of permitted domains
$permittedDomains = [
    "gmail.com",  // Add your allowed domains here
    "yahoo.com", // Add more allowed domains
    "hotmail.com"
];

// Extract the domain from the email
$emailDomain = substr(strrchr($email, "@"), 1);

// Check if the email domain is in the permitted list
if (!in_array($emailDomain, $permittedDomains)) {
    header("Location: signup.php?msg=Email domain not permitted.");
    exit;
}

// Check for existing email or username
$check = $conn->query("SELECT id FROM users WHERE username='$username' OR email='$email'");
if ($check->num_rows > 0) {
    header("Location: signup.php?msg=Username or email already taken.");
    exit;
}

// Insert into users table
$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
if ($conn->query($sql)) {
    logActivity($conn, $username, "Signed up", "signup.php");
    header("Location: signup.php?msg=Signup successful!");
} else {
    echo "Error: " . $conn->error;
}
