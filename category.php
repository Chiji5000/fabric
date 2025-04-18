<?php
require 'navbar.php';

$category = $_GET['name'] ?? '';
$category = trim($category);

if (!$category) {
    echo "Invalid category.";
    exit;
}

// Prepare and execute
$stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

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
<html>

<head>
    <title><?= htmlspecialchars($category) ?> - Category</title>
    <style>
        /* body {
            background-color: #f9fafb;
            font-family: Arial;
            padding: 20px;
        } */

        h2 {
            color: #2c3e50;
            padding: 30px 0;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 30px auto;
        }

        .product-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        body.dark .product-card {
            background-color: #1e293b !important;
            color: #f0f0f0;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 10px;
        }

        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #3498db;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
        }

        .product-card h3,
        .product-card p {
            text-decoration: none !important;
            padding: 10px 0;
        }

        body.dark .product-card h3,
        body.dark .product-card p {
            background-color: #1e293b !important;
            color: #f0f0f0;
        }
    </style>
</head>

<body>

    <h2>Category: <?= htmlspecialchars(ucfirst($category)) ?></h2>

    <div class="product-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <a href="product-details.php?id=<?= $row['id'] ?>">
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="product-img" alt="<?= htmlspecialchars($row['name']) ?>">
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p>Category: <?= htmlspecialchars($row['category']) ?></p>
                    <a href="product-details.php?id=<?= $row['id'] ?>" class="btn">View Details</a>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>