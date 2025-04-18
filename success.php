<?php
require "./navbar.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];
    $total = $_POST['total'];
    $user_id = $_SESSION['user_id'];
    $cart = $_SESSION['cart'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0fdf4;
            text-align: center;
        }

        .success-message {
            background: white;
            padding: 40px;
            max-width: 500px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px
        }

        body.dark .success-message {
            background-color: #1e293b !important;
            color: #f0f0f0;
        }

        h2 {
            color: #22c55e;
        }

        .success-message a {
            margin-top: 20px;
            display: inline-block;
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="success-message">
        <h2>🎉 Order Placed Successfully!</h2>
        <p>Thank you for your purchase. We will process your order shortly.</p>
        <a href="index.php">← Continue Shopping</a><br>
    </div>
</body>

</html>