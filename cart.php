<?php
require "./navbar.php";

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];

if (!empty($cart)) {
    $productIds = implode(',', array_map('intval', array_keys($cart)));
    $query = "SELECT * FROM products WHERE id IN ($productIds)";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $cartItems[] = $row;
    }
}

        $timeout_duration = 180;

        // Check if the user is logged in
        if (isset($_SESSION['user_id'])) {
            // If last activity is set and current time exceeds it by timeout, logout
            if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
                session_unset();
                session_destroy();
                header("Location: login.php?timeout=true");
                exit();
            }
            // Update last activity time
            $_SESSION['LAST_ACTIVITY'] = time();
        } else {
            // If not logged in at all
            header("Location: login.php");
            exit();
        }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #1f2937;
        }

        body.dark .cart-container {
            background-color: #1e293b !important;
            color: #f0f0f0;
        }

        .cart-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding: 15px 0;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item input[type="number"] {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 60px;
        }

        .cart-item button {
            padding: 5px 10px;
            background-color: #0ea5e9;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .cart-item a {
            text-decoration: none;
            color: #ef4444;
            margin-left: 10px;
            font-size: 14px;
        }

        .cart-total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #22c55e;
            color: white;
            text-align: center;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            margin-top: 15px;
            cursor: pointer;
        }

        .empty-message {
            text-align: center;
            font-size: 18px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="cart-container">
        <h1>Shopping Cart</h1>

        <?php if (!empty($cartItems)): ?>
            <?php
            $total = 0;
            foreach ($cartItems as $item):
                $quantity = $cart[$item['id']];
                $subtotal = $item['price'] * $quantity;
                $total += $subtotal;
            ?>
                <div class="cart-item">
                    <div>
                        <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                        ₦<?= number_format($item['price'], 2) ?>
                    </div>
                    <div>
                        <form action="update_cart.php" method="POST" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="number" name="quantity" value="<?= $quantity ?>" min="1">
                            <button type="submit">Update</button>
                        </form>
                        <a href="remove_from_cart.php?product_id=<?= $item['id'] ?>">🗑 Remove</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-total">
                Total: ₦<?= number_format($total, 2) ?>
            </div>

            <form action="payment.php" method="POST">
                <button type="submit" class="checkout-btn">Proceed to Checkout</button>
            </form>

        <?php else: ?>
            <p class="empty-message">Your cart is empty 😢</p>
        <?php endif; ?>
    </div>
</body>

</html>