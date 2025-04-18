<?php
session_start();
require 'db_connect.php';
require 'activity_logger.php';

// Admin check - verify if the logged-in user is in the 'admins' table
if (!isset($_SESSION['user_email'])) {
    echo "You must be logged in as an admin to view messages.";
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
    echo "You must be logged in as an admin to view messages.";
    exit;
}

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

// Search/filter query
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$search_query = '';

if ($search) {
    $search_query = "AND (users.username LIKE '%$search%' OR users.email LIKE '%$search%')";
}

// Query to get messages with search/filter and pagination
$query = "
    SELECT messages.*, users.username, users.email 
    FROM messages 
    JOIN users ON messages.user_id = users.id 
    WHERE (users.username LIKE '%$search%' OR users.email LIKE '%$search%')
    $search_query
    ORDER BY messages.created_at DESC 
    LIMIT $start_from, $limit
";

$result = $conn->query($query);

// Get total number of messages for pagination
$total_result = $conn->query("SELECT COUNT(*) FROM messages 
                              JOIN users ON messages.user_id = users.id 
                              WHERE (users.username LIKE '%$search%' OR users.email LIKE '%$search%')");
$total_messages = $total_result->fetch_row()[0];
$total_pages = ceil($total_messages / $limit);

// Log activity
logActivity($conn, $_SESSION['user_name'], "Viewed messages with filter", "admin_view_messages.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Messages</title>
    <style>
        /* Add the styles you already had here */
    </style>
</head>

<body>
    <style>
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

        .search-bar {
            text-align: center;
            margin-bottom: 30px;
        }

        .search-bar input[type="text"] {
            width: 60%;
            max-width: 500px;
            padding: 12px 18px;
            font-size: 16px;
            border: 1px solid #dcdcdc;
            border-radius: 30px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-bar input[type="text"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        /* Message Box */
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

        .no-message {
            text-align: center;
            color: #aaa;
            font-size: 17px;
            margin: 40px 0;
        }

        /* Pagination */
        .pagination {
            text-align: center;
            margin-top: 40px;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            margin: 0 5px;
            padding: 10px 16px;
            font-size: 14px;
            border-radius: 8px;
            background: #eaeaea;
            color: #333;
            text-decoration: none;
            transition: background 0.3s;
        }

        .pagination a:hover {
            background: #007bff;
            color: white;
        }

        .pagination span {
            background: #007bff;
            color: white;
            font-weight: bold;
        }
    </style>
    <div class="container">
        <h2>User Messages</h2>

        <!-- Search Bar -->
        <div class="search-bar">
            <form action="admin_view_messages.php" method="get">
                <input type="text" name="search" placeholder="Search by username or email" value="<?= $search ?>">
            </form>
        </div>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $username = htmlspecialchars($row['username']);
                $email = htmlspecialchars($row['email']);
                $message = nl2br(htmlspecialchars($row['message']));
                $message_id = $row['id'];
                $created_at = date('F j, Y, g:i a', strtotime($row['created_at'])); // Format timestamp

                echo "<div class='message-box'>";
                echo "<div class='message-content'>";
                echo "<p class='meta'><strong>Username:</strong> $username | <strong>Email:</strong> $email</p>";
                echo "<p>$message</p>";
                echo "<p class='timestamp'><strong>Sent on:</strong> $created_at</p>";
                echo "</div>";
                echo "<div class='action-buttons'>";
                echo "<a href='reply_message.php?id=$message_id'>Reply</a>";
                echo "<a href='delete_message.php?id=$message_id'>Delete</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-message'>No available messages.</p>";
        }
        ?>

        <!-- Pagination -->
        <div class="pagination">
            <?php
            if ($page > 1) {
                echo "<a href='admin_view_messages.php?page=" . ($page - 1) . "&search=$search'>Prev</a>";
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<span>$i</span>";
                } else {
                    echo "<a href='admin_view_messages.php?page=$i&search=$search'>$i</a>";
                }
            }

            if ($page < $total_pages) {
                echo "<a href='admin_view_messages.php?page=" . ($page + 1) . "&search=$search'>Next</a>";
            }
            ?>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>