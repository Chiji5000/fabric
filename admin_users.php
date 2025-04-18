<?php
session_start();
include 'db_connect.php';

// Delete user if requested and not an admin
if (isset($_GET['delete_id'])) {
    $userId = intval($_GET['delete_id']);

    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();

    if ($role !== 'admin') {
        $delStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delStmt->bind_param("i", $userId);
        $delStmt->execute();
        $delStmt->close();
        header("Location: admin_users.php?deleted=1");
        exit;
    }
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';
$valid_columns = ['id', 'username', 'email', 'role'];
$sort = in_array($sort, $valid_columns) ? $sort : 'id';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = $search ? "WHERE username LIKE '%$search%' OR email LIKE '%$search%'" : '';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conn->query("SELECT COUNT(*) as total FROM users $whereClause");
$totalUsers = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

$query = "SELECT id, username, email, role FROM users $whereClause ORDER BY $sort $order LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #0f172a;
            margin-bottom: 25px;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        th,
        td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background-color: #0ea5e9;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        .delete-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #dc2626;
        }

        .protected {
            color: #9ca3af;
            font-style: italic;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #0ea5e9;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .pagination a:hover {
            background-color: #0369a1;
        }

        .success-msg {
            text-align: center;
            color: #16a34a;
            font-weight: bold;
            margin-bottom: 20px;
        }

        @media screen and (max-width: 768px) {

            th,
            td {
                padding: 10px 5px;
                font-size: 14px;
            }

            .delete-btn {
                padding: 6px 12px;
                font-size: 13px;
            }
        }
    </style>
    <script>
        function sortTable(column) {
            const urlParams = new URLSearchParams(window.location.search);
            let currentSort = urlParams.get('sort') || 'id';
            let currentOrder = urlParams.get('order') || 'desc';
            let newOrder = (currentSort === column && currentOrder === 'asc') ? 'desc' : 'asc';
            urlParams.set('sort', column);
            urlParams.set('order', newOrder);
            window.location.search = urlParams.toString();
        }

        function liveSearch() {
            const searchInput = document.getElementById("search").value.toLowerCase();
            const rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                const username = row.children[1].textContent.toLowerCase();
                const email = row.children[2].textContent.toLowerCase();
                if (username.includes(searchInput) || email.includes(searchInput)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>All Registered Users</h2>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="success-msg">User deleted successfully.</div>
        <?php endif; ?>

        <div class="search-bar">
            <input type="text" id="search" onkeyup="liveSearch()" placeholder="Search by username or email...">
        </div>

        <table>
            <thead>
                <tr>
                    <th onclick="sortTable('id')">#ID</th>
                    <th onclick="sortTable('username')">Username</th>
                    <th onclick="sortTable('email')">Email</th>
                    <th onclick="sortTable('role')">Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <?php if ($user['role'] !== 'admin'): ?>
                                <a href="?delete_id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <button class="delete-btn">Delete</button>
                                </a>
                            <?php else: ?>
                                <span class="protected">Protected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>">Page <?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>

</html>