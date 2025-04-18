<?php
include 'db_connect.php';
require 'activity_logger.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Pagination settings
$limit = 10; // records per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Handle search
$search = "";
$searchSql = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $searchSql = "WHERE username LIKE '%$search%' OR action LIKE '%$search%'";
}

// Count total records
$countSql = "SELECT COUNT(*) as total FROM user_activity $searchSql";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch current page records
$sql = "SELECT * FROM user_activity $searchSql ORDER BY timestamp DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Activity Logs</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        form {
            margin-bottom: 15px;
        }

        input[type="text"] {
            padding: 6px;
            width: 250px;
        }

        input[type="submit"] {
            padding: 6px 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            padding: 6px 12px;
            margin: 0 3px;
            border: 1px solid #ccc;
            text-decoration: none;
            background-color: #eee;
            color: #333;
        }

        .pagination a.active {
            background-color: #333;
            color: white;
        }
    </style>
</head>

<body>
    <h2>User Activity Logs</h2>

    <form method="get">
        <input type="text" name="search" placeholder="Search by username or action" value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Search">
        <?php if ($search): ?>
            <a href="admin_activity_logs.php">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Action</th>
                <th>Page</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['action'] ?></td>
                        <td><?= $row['page'] ?></td>
                        <td><?= $row['ip_address'] ?></td>
                        <td><?= $row['user_agent'] ?></td>
                        <td><?= $row['timestamp'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= ($i == $page) ? 'active' : '' ?>"
                href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php include 'footer.php'; ?>