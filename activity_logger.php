<?php
function logActivity($conn, $username, $action, $page)
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $stmt = $conn->prepare("INSERT INTO user_activity (username, action, page, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $action, $page, $ip, $userAgent);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['remember_me'])) {
    setcookie("username", $username, time() + (86400 * 30), "/"); // 30 days
    setcookie("user_id", $user_id, time() + (86400 * 30), "/");
}