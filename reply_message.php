<?php
session_start();
require 'db_connect.php';
require 'activity_logger.php';

// Admin check - verify if the logged-in user is in the 'admins' table
if (!isset($_SESSION['user_email'])) {
    echo "You must be logged in as an admin to reply to messages.";
    exit;
}

// Get the email from session
$userEmail = $_SESSION['user_email'];

// Check if the user is an admin
$query = "SELECT * FROM admins WHERE email = '$userEmail'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    // User is an admin, proceed
    $admin = $result->fetch_assoc();
} else {
    // User is not an admin, redirect or show an error message
    echo "You must be logged in as an admin to reply to messages.";
    exit;
}

// Get message ID from URL parameter
if (isset($_GET['id'])) {
    $message_id = (int)$_GET['id'];

    // Query to get the message details
    $message_query = "SELECT messages.*, users.username, users.email 
                      FROM messages 
                      JOIN users ON messages.user_id = users.id 
                      WHERE messages.id = $message_id";
    $message_result = $conn->query($message_query);

    if ($message_result && $message_result->num_rows > 0) {
        $message = $message_result->fetch_assoc();
        $user_id = $message['user_id']; // Get the user_id from the messages table
    } else {
        echo "Message not found.";
        exit;
    }
} else {
    echo "Invalid message ID.";
    exit;
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message'])) {
    $reply_message = htmlspecialchars(trim($_POST['reply_message']));

    if (!empty($reply_message)) {
        // Insert reply into database
        $stmt = $conn->prepare("INSERT INTO replies (message_id, reply, admin_id, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('issi', $message_id, $reply_message, $admin['id'], $user_id);

        if ($stmt->execute()) {
            // Log activity
            logActivity($conn, $_SESSION['user_name'], "Replied to message ID: $message_id", "reply_message.php");

            // Redirect with an alert
            echo "<script>
                    alert('Message successfully sent');
                    window.location.href = 'user_dashboard.php';
                  </script>";
            exit;
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
    <style>
        /* Add your styles here */
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #1e1e1e;
        }

        .message-box {
            border: 1px solid #e1e5ea;
            border-left: 4px solid #007bff;
            background: #fefefe;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }

        .message-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.04);
        }

        .message-content p {
            margin: 10px 0;
            line-height: 1.6;
            font-size: 15px;
        }

        .meta {
            color: #555;
            font-size: 14px;
        }

        .timestamp {
            color: #999;
            font-size: 13px;
            margin-top: 5px;
        }

        .action-buttons {
            margin-top: 15px;
        }

        .action-buttons a {
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
            margin-right: 20px;
            transition: color 0.2s;
        }

        .action-buttons a:hover {
            color: #0056b3;
        }

        .form-container {
            margin-top: 30px;
        }

        label {
            font-size: 16px;
            color: #333;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            color: #333;
            background-color: #f9f9f9;
            resize: none;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Reply to Message</h2>

        <!-- Message Details -->
        <div class="message-box">
            <div class="message-content">
                <p class="meta"><strong>Username:</strong> <?php echo htmlspecialchars($message['username']); ?> | <strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                <p class="timestamp"><strong>Sent on:</strong> <?php echo date('F j, Y, g:i a', strtotime($message['created_at'])); ?></p>
            </div>
        </div>

        <!-- Reply Form -->
        <div class="form-container">
            <form method="POST">
                <label for="reply_message">Your Reply:</label><br>
                <textarea name="reply_message" id="reply_message" placeholder="Write your reply here..." required></textarea><br>
                <input type="submit" class="btn-submit" value="Send Reply">
            </form>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>