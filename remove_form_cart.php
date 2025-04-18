<?php
session_start();

if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];
    unset($_SESSION['cart'][$productId]);
}

header('Location: cart.php');
exit;
