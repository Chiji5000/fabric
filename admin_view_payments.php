<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>View Payment Requests</title>
</head>

<body>
    <h2>Payment Requests</h2>
    <?php
    $result = $conn->query("SELECT * FROM payments");
    while ($row = $result->fetch_assoc()) {
        echo "<div><strong>User:</strong> " . $row['user_name'] . "<br>";
        echo "<strong>Amount:</strong> $" . $row['amount'] . "<br>";
        echo "<strong>Status:</strong> " . $row['status'] . "<hr></div>";
    }
    ?>
<?php include 'footer.php'; ?>