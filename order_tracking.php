<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}
$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        orders.id AS order_id,
        orders.fullname, orders.email, orders.phone, orders.address,
        orders.total_amount, orders.status, orders.created_at,
        order_items.product_id, order_items.quantity,
        COALESCE(order_items.price, 0) AS price
    FROM users
    JOIN orders ON users.id = orders.user_id
    JOIN order_items ON orders.id = order_items.order_id
    WHERE users.id = ?
    ORDER BY orders.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Group orders by order_id
$orders = [];
while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'info' => $row,
            'items' => []
        ];
    }
    $orders[$order_id]['items'][] = [
        'product_id' => $row['product_id'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Your Orders</title>
    <style>
        body {
            font-family: Arial;
            background: #f9fafb;
            padding: 20px;
        }

        .order-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }

        .status {
            font-weight: bold;
        }

        .pending {
            color: orange;
        }

        .shipped {
            color: blue;
        }

        .delivered {
            color: green;
        }

        ul {
            padding-left: 18px;
        }

        ul li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

    <h2>Your Orders</h2>

    <?php if (empty($orders)): ?>
        <p>You haven't made any orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php $info = $order['info']; ?>
            <div class="order-box">
                <h4>Order #<?= $info['order_id'] ?> -
                    <span class="status <?= strtolower($info['status']) ?>">
                        <?= ucfirst($info['status']) ?>
                    </span>
                </h4>
                <p><strong>Name:</strong> <?= htmlspecialchars($info['fullname']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($info['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($info['phone']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($info['address']) ?></p>
                <p><strong>Total:</strong> ₦<?= number_format($info['total_amount'], 2) ?></p>
                <p><strong>Date:</strong> <?= $info['created_at'] ?></p>

                <h5>Items:</h5>
                <ul>
                    <?php foreach ($order['items'] as $item): ?>
                        <li>Product ID: <?= $item['product_id'] ?> | Qty: <?= $item['quantity'] ?> |
                            Price: ₦<?= number_format($item['price'], 2) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>