<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus {
            border-color: #0057a4;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #0057a4;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #003f7f;
        }

        .file-upload-wrapper {
            display: flex;
            align-items: center;
        }

        .file-upload-wrapper input[type="file"] {
            flex: 1;
        }

        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #f9f9f9;
            resize: none;
            transition: border-color 0.3s ease;
        }

        textarea:focus {
            border-color: #0057a4;
            outline: none;
        }

        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 30px;
        }

        .footer a {
            color: white;
            text-decoration: none;
        }

        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .alert.success {
            background-color: #4CAF50;
        }

        .alert.error {
            background-color: #f44336;
        }
    </style>
</head>

<body>

    <header>
        <h1>Add New Product</h1>
    </header>

    <div class="container">
        <form id="productForm" action="upload_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" name="price" id="price" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" name="category" id="category" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="4" placeholder="Describe the product..." required></textarea>
            </div>
            <div class="form-group file-upload-wrapper">
                <label for="image">Product Image:</label>
                <input type="file" name="image" id="image" accept="image/*" required>
            </div>
            <input type="submit" value="Upload Product">
        </form>
        <div id="alertContainer"></div>
    </div>

    <div class="footer">
        <p>&copy; 2025 Your Company | <a href="#">Privacy Policy</a></p>
    </div>

    <script>
        const form = document.getElementById('productForm');
        const name = document.getElementById('name');
        const price = document.getElementById('price');
        const category = document.getElementById('category');
        const description = document.getElementById('description');
        const image = document.getElementById('image');
        const alertContainer = document.getElementById('alertContainer');

        // Form validation and success/error handling
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let valid = true;
            let errorMessage = '';
            let successMessage = '';

            // Validation
            if (name.value.trim() === '') {
                errorMessage = 'Product name is required.';
                valid = false;
            } else if (price.value <= 0) {
                errorMessage = 'Price must be a positive number.';
                valid = false;
            } else if (category.value.trim() === '') {
                errorMessage = 'Category is required.';
                valid = false;
            } else if (description.value.trim() === '') {
                errorMessage = 'Description is required.';
                valid = false;
            } else if (!image.files[0]) {
                errorMessage = 'Please upload a product image.';
                valid = false;
            }

            // Show success or error messages
            if (valid) {
                successMessage = 'Product uploaded successfully!';
                alertContainer.innerHTML = `<div class="alert success">${successMessage}</div>`;
                // Simulate form submission success
                setTimeout(() => {
                    form.submit(); // Submit the form if all fields are valid
                }, 1500); // 1.5 seconds delay before submitting
            } else {
                alertContainer.innerHTML = `<div class="alert error">${errorMessage}</div>`;
            }
        });
    </script>

</body>

</html>