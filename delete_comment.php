<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$commentId = intval($_GET['id']);
$productId = intval($_GET['product_id']);

$stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $commentId, $_SESSION['user_id']);
$stmt->execute();

header("Location: product-details.php?id=$productId");
exit;
