<?php
require "./navbar.php";

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];

if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

// Fetch product info for cart
$productIds = implode(',', array_map('intval', array_keys($cart)));
$query = "SELECT * FROM products WHERE id IN ($productIds)";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $cartItems[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        .checkout-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px
        }

        body.dark .checkout-container {
            background-color: #1e293b !important;
            color: #f0f0f0;
        }

        h2 {
            color: #1f2937;
            margin-bottom: 20px;
        }

        .item-summary {
            margin-bottom: 25px;
        }

        .item-summary div {
            margin-bottom: 10px;
        }

        form input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            resize: none;
        }

        .checkout-btn {
            background-color: #22c55e;
            color: white;
            padding: 14px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="checkout-container">
        <h2>Checkout</h2>

        <div class="item-summary">
            <h3>Order Summary</h3>
            <?php
            $total = 0;
            foreach ($cartItems as $item):
                $qty = $cart[$item['id']];
                $subtotal = $qty * $item['price'];
                $total += $subtotal;
            ?>
                <div>
                    <?= htmlspecialchars($item['name']) ?> x <?= $qty ?> = ₦<?= number_format($subtotal, 2) ?>
                </div>
            <?php endforeach; ?>
            <div class="total">Total: ₦<?= number_format($total, 2) ?></div>
        </div>

        <form action="place_order.php" method="POST">
            <h3>Shipping Information</h3>
            <label>Full Name</label>
            <input type="text" name="fullname" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Phone Number</label>
            <input type="number" name="phone_number" required>

            <label>Shipping Address</label>
            <textarea name="address" rows="4" required></textarea>

            <input type="hidden" name="total" value="<?= $total ?>">

            <button type="submit" class="checkout-btn">Place Order</button>
        </form>
    </div>
</body>

</html>