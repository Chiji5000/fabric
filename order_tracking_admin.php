<?php
session_start();
require 'db_connect.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Handle status update if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $order_id);
    $update_stmt->execute();
}

// Count total orders for pagination
$count_result = $conn->query("SELECT COUNT(*) as total FROM orders");
$total_orders = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $limit);

// Get orders for current page
$query = "SELECT * FROM orders ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin - Order Tracking</title>
    <style>
        body {
            font-family: Arial;
            background: #f8fafc;
            padding: 20px;
        }

        .order-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .status-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            color: white;
        }

        .pending {
            background-color: orange;
        }

        .shipped {
            background-color: blue;
        }

        .delivered {
            background-color: green;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            background: #e2e8f0;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            color: #333;
        }

        .pagination a.active {
            background: #0ea5e9;
            color: white;
        }
    </style>
</head>

<body>

    <h2>📦 Admin - Order Tracking</h2>

    <?php while ($order = $result->fetch_assoc()): ?>
        <div class="order-box">
            <h4>Order #<?= $order['id'] ?> -
                <span class="<?= strtolower($order['status']) ?>"><strong><?= ucfirst($order['status']) ?></strong></span>
            </h4>
            <p><strong>Name:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
            <p><strong>Total:</strong> ₦<?= number_format($order['total_amount'], 2) ?></p>
            <p><strong>Date:</strong> <?= $order['created_at'] ?></p>

            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="new_status">
                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                </select>
                <button type="submit" class="status-btn <?= strtolower($order['status']) ?>">Update Status</button>
            </form>
        </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

</body>

</html>