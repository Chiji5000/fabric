<?php
session_start();
include 'db_connect.php';
require 'vendor/autoload.php'; // Make sure Stripe SDK is installed

\Stripe\Stripe::setApiKey('sk_test_51PO3Qs05iBOSLW0Liq3LM6M6uFxOBzlvdQu84OxLHcHpYGHkm8QragRKyMnSddPXUGYCher2ERjq2ZXJy9IDWJVP001SbQnIvY'); // SECRET KEY

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "Cart is empty. <a href='index.php'>Go shopping</a>";
    exit;
}

// Calculate total
$total = 0;
$line_items = [];

foreach ($cart as $product_id => $quantity) {
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
    $row = mysqli_fetch_assoc($result);
    $subtotal = $row['price'] * $quantity;
    $total += $subtotal;

    $line_items[] = [
        'price_data' => [
            'currency' => 'ngn',
            'product_data' => [
                'name' => $row['name'],
            ],
            'unit_amount' => $row['price'] * 100, // in kobo
        ],
        'quantity' => $quantity,
    ];
}

// Create Stripe Checkout Session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => 'http://localhost/fabric/checkout.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/fabric/cart.php',
]);

header("Location: " . $session->url);
exit;
