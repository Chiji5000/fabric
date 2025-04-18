<?php
// db_connect.php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Optional: helps with debugging
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>