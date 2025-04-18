<?php
session_start();
require 'db_connect.php';
require 'activity_logger.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $message_id = (int)$_GET['id'];

    // Fetch the message details along with the username and email using a JOIN
    $stmt = $conn->prepare("
        SELECT messages.*, users.username, users.email
        FROM messages
        JOIN users ON messages.user_id = users.id
        WHERE messages.id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();

    if (!$message) {
        echo "Message not found.";
        exit;
    }

    // Mark the message as read
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
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

<!-- Rest of the HTML and form code goes here -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Messages</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <nav class="navbar">
        <a href="index.php" class="logo">MyStore</a>
        <!-- Add other navigation links here -->
    </nav>

    <div class="messages-container">
        <h2>Your Messages</h2>

        <?php if ($result->num_rows > 0): ?>
            <ul class="messages-list">
                <?php while ($message = $result->fetch_assoc()): ?>
                    <li class="message-item <?= $message['is_read'] == 0 ? 'unread' : '' ?>">
                        <strong>From: Admin</strong>
                        <p><strong>Subject:</strong> <?= htmlspecialchars($message['subject']) ?></p>
                        <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($message['message'])) ?></p>
                        <p><strong>Date:</strong> <?= date('F j, Y, g:i a', strtotime($message['created_at'])) ?></p>
                        <?php if ($message['is_read'] == 0): ?>
                            <span class="new-message-badge">New</span>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>You have no messages.</p>
        <?php endif; ?>
    </div>

    <style>
        .messages-container {
            margin: 2rem;
            font-family: Arial, sans-serif;
        }

        .messages-list {
            list-style-type: none;
            padding: 0;
        }

        .message-item {
            background-color: #f4f4f4;
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .message-item.unread {
            background-color: #e0f7fa;
        }

        .new-message-badge {
            background-color: #ff5722;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .message-item p {
            margin: 0.5rem 0;
        }

        .message-item strong {
            font-weight: bold;
        }
    </style>
</body>

</html>

<?php
$conn->close();
?>