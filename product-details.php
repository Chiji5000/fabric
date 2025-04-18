<?php
ob_start();
require "./navbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$productId) {
    echo "<div class='error'>Invalid product ID.</div>";
    exit;
}

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<div class='error'>Product not found.</div>";
    exit;
}

$timeout_duration = 180;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $commentText = trim($_POST['comment_text']);
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id']; // Assuming user is logged in

    if ($commentText !== '') {
        // Insert the comment into the database
        $stmt = $conn->prepare("INSERT INTO comments (product_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $productId, $userId, $commentText);
        if ($stmt->execute()) {
            // If the comment is successfully inserted, redirect to the same page to show the new comment
            header("Location: product-details.php?id=" . $productId);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text'])) {
    $replyText = trim($_POST['reply_text']);
    $commentId = $_POST['comment_id'];
    $userId = $_SESSION['user_id']; // Assuming user is logged in

    if ($replyText !== '') {
        // Insert the reply into the comment_replies table
        $stmt = $conn->prepare("INSERT INTO comment_replies (comment_id, user_id, reply) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $commentId, $userId, $replyText);
        if ($stmt->execute()) {
            // If the reply is successfully inserted, reload the page to show the new reply
            header("Location: product-details.php?id=" . $_GET['id']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Fetch all comments and replies for the current product
$commentsQuery = "SELECT comments.id AS comment_id, comments.comment, comments.created_at, users.username 
                  FROM comments
                  JOIN users ON comments.user_id = users.id
                  WHERE comments.product_id = ?
                  ORDER BY comments.created_at DESC";

$commentsStmt = $conn->prepare($commentsQuery);
$commentsStmt->bind_param("i", $productId);
$commentsStmt->execute();
$commentsResult = $commentsStmt->get_result();

$comments = [];
while ($comment = $commentsResult->fetch_assoc()) {
    // Fetch replies for each comment
    $repliesQuery = "SELECT comment_replies.reply, comment_replies.created_at, users.username
                     FROM comment_replies
                     JOIN users ON comment_replies.user_id = users.id
                     WHERE comment_replies.comment_id = ?";
    $repliesStmt = $conn->prepare($repliesQuery);
    $repliesStmt->bind_param("i", $comment['comment_id']);
    $repliesStmt->execute();
    $repliesResult = $repliesStmt->get_result();

    $replies = [];
    while ($reply = $repliesResult->fetch_assoc()) {
        $replies[] = $reply;
    }

    $comment['replies'] = $replies;
    $comments[] = $comment;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> | Product Details</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        body.dark .details-container {
            background-color: #1e293b !important;
            color: #f0f0f0;
        }

        body.dark .product-info .description {
            color: #f0f0f0;
        }

        body.dark .product-info .category {
            color: #f0f0f0;
        }

        body.dark .related-products {
            background-color: #1e293b !important;
            color: #f0f0f0;
        }

        body.dark .related-grid {
            background-color: #1e293b !important;
            color: #f0f0f0 !important;
        }

        body.dark .related-card {
            background-color: rgb(17, 23, 32) !important;
            color: #f0f0f0 !important;
        }

        body.dark h3 {
            background-color: rgb(17, 23, 32) !important;
            color: #f0f0f0 !important;
        }

        .details-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 30px;
            display: flex;
            flex-wrap: wrap;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .product-image {
            flex: 1 1 45%;
            padding: 20px;
        }

        .product-image img {
            width: 100%;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .product-info {
            flex: 1 1 55%;
        }

        .product-info h1 {
            font-size: 36px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 10px;
        }

        .product-info .category {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .product-info .price {
            font-size: 28px;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 20px;
        }

        .product-info .description {
            color: #374151;
            ;
        }

        .form-inline {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .form-inline input[type="number"] {
            width: 80px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .form-inline button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
            position: relative;
        }

        .form-inline button:hover {
            background-color: #1d4ed8;
        }

        .form-inline button:active {
            transform: scale(0.97);
        }

        .cart-animation {
            animation: bounce 0.5s ease;
        }

        @keyframes bounce {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .back-link {
            text-align: center;
            margin-top: 40px;
        }

        .back-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .error {
            text-align: center;
            color: red;
            font-weight: bold;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .details-container {
                flex-direction: column;
                padding: 20px;
            }

            .product-image,
            .product-info {
                padding: 10px;
            }

            .product-info {
                text-align: center !important;
            }
        }

        .related-products {
            max-width: 1000px;
            margin: 60px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.04);
        }

        .related-products h2 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 25px;
            text-align: center;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .related-card {
            background-color: #f9fafb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        }

        .related-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
        }

        .related-card a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .related-info {
            padding: 15px;
            text-align: center;
        }

        .related-info h3 {
            font-size: 16px;
            margin: 10px 0 5px;
            color: #111827;
        }

        .related-info p {
            color: #059669;
            font-weight: bold;
            font-size: 15px;
        }

        .no-related {
            text-align: center;
            color: #9ca3af;
            font-style: italic;
        }

        .comments-section {
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .comments-title {
            font-size: 26px;
            font-weight: 500;
            color: #333;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        .comment-form {
            margin-bottom: 20px;
        }

        .comment-input,
        .reply-input {
            width: 70%;
            padding: 14px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 10px;
            background-color: #fff;
            transition: border-color 0.3s;
            resize: none !important;
        }

        .comment-input:focus,
        .reply-input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .comment-submit-btn,
        .reply-submit-btn {
            background-color: #333;
            color: white;
            padding: 12px 18px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 70%;
            transition: background-color 0.3s;
        }

        .comment-submit-btn:hover,
        .reply-submit-btn:hover {
            background-color: #555;
        }

        .comment-card {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .comment-author {
            font-weight: 600;
            color: #333;
        }

        .comment-date {
            color: #888;
            font-size: 14px;
        }

        .comment-text {
            font-size: 16px;
            color: #444;
            margin-bottom: 15px;
        }

        .comment-actions {
            text-align: right;
        }

        #reply- {
            width: 70% !important;
        }

        .reply-link {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .reply-link:hover {
            color: #333;
        }

        .reply-form-container {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .reply-card {
            padding: 15px;
            background-color: #fdfdfd;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            margin-top: 15px;
        }

        .reply-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .reply-author {
            font-weight: 600;
            color: #333;
        }

        .reply-date {
            color: #888;
            font-size: 14px;
        }

        .reply-text {
            font-size: 16px;
            color: #444;
        }
    </style>
    <script>
        function animateButton(btn) {
            btn.classList.add("cart-animation");
            setTimeout(() => btn.classList.remove("cart-animation"), 500);
        }
    </script>
</head>

<body>

    <div class="details-container">
        <div class="product-image">
            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="category">Category: <?php echo htmlspecialchars($product['category']); ?></p>
            <p class="price">₦<?php echo htmlspecialchars($product['price']); ?></p>
            <p class="description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <form method="POST" action="cart.php" class="form-inline">
                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                <input type="number" name="quantity" min="1" value="1" required>
                <a href="add_to_cart.php?id=<?= $product['id'] ?>" class="btn" style="background:#0ea5e9; color:white; padding:8px 12px; border-radius:6px; display:inline-block; margin-top:10px;" onclick="animateButton(this)">Add to Cart</a>

            </form>
        </div>
    </div>

    <div class="back-link">
        <a href="index.php">← Back to Products</a>
    </div>

    <section class="related-products">
        <h2>Related Products</h2>
        <div class="related-grid">
            <?php
            $category = $product['category'];
            $relatedSql = "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4";
            $relatedStmt = $conn->prepare($relatedSql);
            $relatedStmt->bind_param("si", $category, $productId);
            $relatedStmt->execute();
            $relatedResult = $relatedStmt->get_result();

            while ($related = $relatedResult->fetch_assoc()) {
                echo "<div class='related-card'>";
                echo "<a href='product-details.php?id=" . $related['id'] . "'>";
                echo "<img src='uploads/" . htmlspecialchars($related['image']) . "' alt='" . htmlspecialchars($related['name']) . "'>";
                echo "<div class='related-info'>";
                echo "<h3>" . htmlspecialchars($related['name']) . "</h3>";
                echo "<p>₦" . htmlspecialchars($related['price']) . "</p>";
                echo "</div>";
                echo "</a>";
                echo "</div>";
            }

            if ($relatedResult->num_rows === 0) {
                echo "<p class='no-related'>No related products found.</p>";
            }
            ?>
        </div>
    </section>

    <div class="comments-section">
        <h3 class="comments-title">Customer Comments</h3>

        <form method="POST" action="product-details.php?id=<?php echo $productId; ?>" class="comment-form">
            <textarea name="comment_text" rows="4" placeholder="Leave a comment..." required class="comment-input"></textarea>
            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
            <button type="submit" class="comment-submit-btn">Submit Comment</button>
        </form>

        <?php foreach ($comments as $comment): ?>
            <div class="comment-card">
                <div class="comment-header">
                    <div class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></div>
                    <div class="comment-date"><?php echo htmlspecialchars($comment['created_at']); ?></div>
                </div>
                <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>

                <div class="comment-actions">
                    <a href="#reply-<?php echo $comment['comment_id']; ?>" onclick="document.getElementById('reply-<?php echo $comment['comment_id']; ?>').style.display = 'block';" class="reply-link">Reply</a>
                </div>

                <div id="reply-<?php echo $comment['comment_id']; ?>" class="reply-form-container" style="display: none;">
                    <form method="POST" action="product-details.php?id=<?php echo $productId; ?>" class="reply-form">
                        <textarea name="reply_text" rows="3" placeholder="Write your reply..." required class="reply-input"></textarea>
                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                        <button type="submit" class="reply-submit-btn">Submit Reply</button>
                    </form>
                </div>

                <?php if (!empty($comment['replies'])): ?>
                    <div class="replies-section">
                        <?php foreach ($comment['replies'] as $reply): ?>
                            <div class="reply-card">
                                <div class="reply-header">
                                    <span class="reply-author"><?php echo htmlspecialchars($reply['username']); ?></span>
                                    <span class="reply-date"><?php echo htmlspecialchars($reply['created_at']); ?></span>
                                </div>
                                <p class="reply-text"><?php echo nl2br(htmlspecialchars($reply['reply'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>