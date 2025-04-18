<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Move uploaded image to the uploads folder
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Insert into database using prepared statement
        $stmt = $conn->prepare("INSERT INTO products (name, price, category, image, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $name, $price, $category, $image, $description);

        if ($stmt->execute()) {
            echo "Product uploaded successfully.";
        } else {
            echo "Failed to insert into database: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to upload image.";
    }

    $conn->close();
}
?>
