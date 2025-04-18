<?php
session_start();
require 'db_connect.php';
require 'activity_logger.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $message_id = (int)$_GET['id'];

    // Fetch the message details
    $stmt = $conn->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();

    if (!$message) {
        echo "Message not found.";
        exit;
    }
    $stmt->close();
} else {
    echo "Invalid message ID.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message'])) {
    $reply = htmlspecialchars(trim($_POST['reply_message']));

    if (!empty($reply)) {
        // Insert the reply into the database
        $stmt = $conn->prepare("INSERT INTO replies (message_id, reply, admin_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $message_id, $reply, $_SESSION['user_id']);
        if ($stmt->execute()) {
            // Log the reply action
            logActivity($conn, $_SESSION['user_name'], "Replied to message ID: $message_id", "reply_message.php");
            header("Location: view_messages.php?message=replied");
        } else {
            echo "Error sending reply.";
        }
        $stmt->close();
    } else {
        echo "Please enter a reply.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message</title>
</head>

<body>
    <h2>Reply to Message</h2>
    <div>
        <strong>Name:</strong> <?php echo htmlspecialchars($message['name']); ?><br>
        <strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?><br>
        <strong>Message:</strong> <?php echo nl2br(htmlspecialchars($message['message'])); ?><br>
    </div>

    <form method="POST">
        <label for="reply_message">Your Reply:</label><br>
        <textarea name="reply_message" id="reply_message" rows="5" cols="50" required></textarea><br>
        <input type="submit" value="Send Reply">
    </form>
</body>

</html>

<?php
$conn->close();
?>