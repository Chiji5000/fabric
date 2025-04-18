<?php
session_start();
include 'db_connect.php';

// Retrieve user messages (you can customize the query)
$userId = $_SESSION['user_id'] ?? null;
if ($userId) {
    $query = "SELECT * FROM replies WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $messages = $stmt->fetchAll();
} else {
    $messages = [];
}

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #1e293b;
            color: #f8fafc;
            padding: 1rem;
            text-align: center;
            font-size: 1.2rem;
        }

        .message-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .message-header {
            font-size: 1.5rem;
            color: #38bdf8;
            margin-bottom: 20px;
            text-align: center;
        }

        .message-card {
            background-color: #fff;
            border: 1px solid #e0e7ff;
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .message-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .message-card .subject {
            font-weight: bold;
            font-size: 1.1rem;
            color: #1e293b;
        }

        .message-card .date {
            font-size: 0.9rem;
            color: #a1a1aa;
        }

        .message-card .content {
            font-size: 1rem;
            color: #333;
            margin-top: 10px;
        }

        .no-messages {
            text-align: center;
            color: #999;
            font-size: 1.2rem;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #38bdf8;
            color: white;
            font-size: 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #1e293b;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <span>Messages</span>
    </div>

    <div class="message-container">
        <div class="message-header">Your Messages</div>

        <?php if (empty($messages)): ?>
            <div class="no-messages">You have no messages yet.</div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="message-card">
                    <div class="date"><?= date("F j, Y, g:i a", strtotime($message['created_at'])) ?></div>
                    <div class="content"><?= nl2br(htmlspecialchars($message['reply'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="index.php" class="back-btn">Back to Home</a>
    </div>

</body>

</html>