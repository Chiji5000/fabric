<?php
require "./navbar.php";
?>
<h2>Request Payment</h2>
<?php if (isset($_GET['msg'])) echo "<p style='color: green;'>" . htmlspecialchars($_GET['msg']) . "</p>"; ?>
<form action="submit_payment.php" method="post">
    <label>Name:</label><input type="text" name="user_name" required><br>
    <label>Amount:</label><input type="number" name="amount" required><br>
    <label>Status:</label><input type="text" name="status" required><br>
    <input type="submit" value="Submit">
</form>
<?php include 'footer.php'; ?>