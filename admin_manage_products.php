<?php
session_start();
include 'db_connect.php';

// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = (isset($_GET['order']) && strtolower($_GET['order']) == 'desc') ? 'DESC' : 'ASC';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 5;
$offset = ($page - 1) * $per_page;

// Unique categories
$categories_result = $conn->query("SELECT DISTINCT category FROM products");
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat['category'];
}

// WHERE clauses
$where_clauses = [];
if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $where_clauses[] = "(name LIKE '%$search_escaped%' OR description LIKE '%$search_escaped%')";
}
if ($category) {
    $category_escaped = $conn->real_escape_string($category);
    $where_clauses[] = "category = '$category_escaped'";
}
$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$allowed_sorts = ['name', 'price', 'category'];
$sort_sql = in_array($sort, $allowed_sorts) ? $sort : 'id';

// Total count for pagination
$count_result = $conn->query("SELECT COUNT(*) AS total FROM products $where_sql");
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $per_page);

// Main query with pagination
$query = "SELECT * FROM products $where_sql ORDER BY $sort_sql $order LIMIT $per_page OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }

        form.filter-bar {
            margin-bottom: 20px;
        }

        input[type="text"],
        select {
            padding: 8px;
            font-size: 14px;
            margin-right: 10px;
        }

        button {
            padding: 8px 12px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #333;
            color: white;
            cursor: pointer;
        }

        a.sort-link {
            color: white;
            text-decoration: none;
        }

        img {
            width: 100px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            background: #0057a4;
            color: white;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #003f7f;
        }

        .delete-btn {
            background-color: #f44336;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .desc-cell {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            margin: 0 5px;
            padding: 6px 12px;
            text-decoration: none;
            background: #ddd;
            color: #333;
            border-radius: 4px;
        }

        .pagination .current {
            background: #0057a4;
            color: white;
            font-weight: bold;
        }

        .pagination a:hover {
            background: #aaa;
        }
    </style>
</head>

<body>
    <h1>Manage Products</h1>

    <form method="GET" class="filter-bar">
        <input type="text" name="search" placeholder="Search by name or description" value="<?php echo htmlspecialchars($search); ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($cat == $category) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <table>
        <tr>
            <th>Image</th>
            <th><a class="sort-link" href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name', 'order' => ($sort == 'name' && $order == 'ASC') ? 'desc' : 'asc'])); ?>">Name <?php echo ($sort == 'name') ? ($order == 'ASC' ? '↑' : '↓') : ''; ?></a></th>
            <th><a class="sort-link" href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price', 'order' => ($sort == 'price' && $order == 'ASC') ? 'desc' : 'asc'])); ?>">Price ($) <?php echo ($sort == 'price') ? ($order == 'ASC' ? '↑' : '↓') : ''; ?></a></th>
            <th><a class="sort-link" href="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'category', 'order' => ($sort == 'category' && $order == 'ASC') ? 'desc' : 'asc'])); ?>">Category <?php echo ($sort == 'category') ? ($order == 'ASC' ? '↑' : '↓') : ''; ?></a></th>
            <th>Description</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt=""></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td class="desc-cell" title="<?php echo htmlspecialchars($row['description']); ?>">
                        <?php echo htmlspecialchars(mb_strimwidth($row['description'], 0, 50, '...')); ?>
                    </td>
                    <td>
                        <a class="btn" href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <form action="edit_product.php?id=<?php echo $row['id']; ?>" method="post" style="display:inline-block;">
                            <input type="hidden" name="delete" value="1">
                            <input type="submit" class="btn delete-btn" onclick="return confirm('Delete this product?')" value="Delete">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No products found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>

</html>