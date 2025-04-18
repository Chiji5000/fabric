<?php
session_start();

$admin_access_password = 'LetMeIn123'; // Change this to your secure admin password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_password = $_POST['admin_password'];

    if ($entered_password === $admin_access_password) {
        $_SESSION['admin_access_granted'] = true;
        header('Location: admin_signup.php');
        exit;
    } else {
        $error = "Incorrect password. Access denied.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Access</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }

        .access-box {
            background: #1e293b;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            width: 300px;
        }

        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 0.75rem;
            margin-top: 1rem;
            border: none;
            border-radius: 5px;
        }

        input[type="password"] {
            background: #334155;
            color: #fff;
        }

        input[type="submit"] {
            background: #38bdf8;
            color: #000;
            cursor: pointer;
        }

        .error {
            color: #f87171;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="access-box">
        <h2>Enter Admin Password</h2>
        <form method="POST">
            <input type="password" name="admin_password" placeholder="Admin Access Password" required>
            <input type="submit" value="Enter">
        </form>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
</body>

</html>