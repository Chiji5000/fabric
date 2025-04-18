<?php
session_start();
include 'db_connect.php'; // Update with your actual DB connection file

// Make sure cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Collect form data
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone_number'];
$address = $_POST['address'];
$total = $_POST['total'];
$cart = $_SESSION['cart'];

// Save order to `orders` table
$orderQuery = "INSERT INTO orders (fullname, email, phone, address, total_amount, created_at)
               VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("ssssd", $fullname, $email, $phone, $address, $total);
$stmt->execute();

$orderId = $stmt->insert_id;

// Save order items to `order_items` table
foreach ($cart as $productId => $qty) {
    // Get product info
    $productQuery = "SELECT price FROM products WHERE id = ?";
    $pstmt = $conn->prepare($productQuery);
    $pstmt->bind_param("i", $productId);
    $pstmt->execute();
    $result = $pstmt->get_result();
    $product = $result->fetch_assoc();

    $price = $product['price'];
    $subtotal = $price * $qty;

    $itemInsert = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
                   VALUES (?, ?, ?, ?, ?)";
    $itemStmt = $conn->prepare($itemInsert);
    $itemStmt->bind_param("iiidd", $orderId, $productId, $qty, $price, $subtotal);
    $itemStmt->execute();
}

// Clear cart
unset($_SESSION['cart']);

// Redirect to success page
header("Location: success.php");
exit;
