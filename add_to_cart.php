<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += 1; // increment quantity
    } else {
        $_SESSION['cart'][$productId] = 1; // add new product
    }

    $_SESSION['cart_message'] = "Product added to cart!";
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
