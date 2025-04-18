<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$commentId = intval($_GET['id']);
$productId = intval($_GET['product_id']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newComment = trim($_POST['comment']);
    $stmt = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $newComment, $commentId, $_SESSION['user_id']);
    $stmt->execute();
    header("Location: product-details.php?id=$productId");
    exit;
}

// Fetch existing comment for prefill
$stmt = $conn->prepare("SELECT comment FROM comments WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $commentId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$commentData = $result->fetch_assoc();

if (!$commentData) {
    echo "Comment not found or unauthorized.";
    exit;
}
?>

<form method="POST" style="max-width:600px;margin:40px auto;">
    <textarea name="comment" rows="4" required style="width:100%;padding:10px;"><?php echo htmlspecialchars($commentData['comment']); ?></textarea>
    <button type="submit" style="padding:10px 20px;margin-top:10px;">Update Comment</button>
</form>