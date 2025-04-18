<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate user inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color: red;'>Invalid email address.</p>";
        exit;
    }

    // Website email where the messages should be sent
    $websiteEmail = "cjiruke@gmail.com"; // Change this to your actual email address

    // Email subject
    $subject = "New Contact Message from $name";

    // Create the email body
    $body = "
    You have received a new message from the contact form on your website.\n\n
    Name: $name\n
    Email: $email\n\n
    Message:\n
    $message
";

    // Email headers
    $headers = "From: $email\r\n";  // The 'From' header should be the email address of the user
    $headers .= "Reply-To: $email\r\n";  // The 'Reply-To' header to ensure replies go to the user's email
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send the email
    if (mail($websiteEmail, $subject, $body, $headers)) {
        header("Location: contact.php?msg=Message sent successfully.");
    } else {
        echo "<p style='color: red;'>Error: Message not sent. Please try again later.</p>";
    }
}
