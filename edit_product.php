<?php
session_start();
include 'db_connect.php';

// Check if product ID is provided
if (!isset($_GET['id'])) {
    echo "Product ID not provided.";
    exit;
}

$product_id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE id = $product_id");
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $image_path = 'uploads/' . $product['image'];
    if ($conn->query("DELETE FROM products WHERE id = $product_id")) {
        if (file_exists($image_path)) unlink($image_path);
        header("Location: user_dashboard.php?msg=Product deleted successfully.");
        exit;
    } else {
        echo "Delete failed: " . $conn->error;
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $conn->query("UPDATE products SET name='$name', price='$price', category='$category', image='$image', description='$description' WHERE id=$product_id");
    } else {
        $conn->query("UPDATE products SET name='$name', price='$price', category='$category', description='$description' WHERE id=$product_id");
    }

    header("Location: user_dashboard.php?msg=Product updated successfully.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .card {
            background: white;
            width: 100%;
            max-width: 600px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            font-weight: 600;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 95%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        img {
            margin-top: 10px;
            width: 120px;
            border-radius: 8px;
        }

        .btn {
            margin-top: 20px;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007BFF;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 500px) {
            .action-row {
                flex-direction: column;
                gap: 10px;
            }

            .btn-danger {
                float: none;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Edit Product</h2>

        <?php if (isset($_GET['msg'])): ?>
            <p style="color: green; font-weight: bold;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="name">Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label for="price">Price ($)</label>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

            <label for="category">Category</label>
            <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>

            <label for="description">Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

            <label for="image">Current Image</label>
            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">

            <label for="image">Change Image (optional)</label>
            <input type="file" name="image">

            <div class="action-row">
                <button type="submit" name="edit" class="btn btn-primary">Update Product</button>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete Product</button>
            </div>
        </form>
    </div>
</body>

</html>