<?php
session_start();
$link = new mysqli("localhost", "root", "", "restaurant");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('请先登录！'); window.location.href='login.html';</script>";
    exit();
}

$reservation_id = $_GET['id'] ?? '';

// 确保 `id` 是整数，防止 SQL 注入
if (!ctype_digit($reservation_id)) {
    echo "<script>alert('非法请求！'); window.location.href='dashboard.php';</script>";
    exit();
}

// 确保用户只能取消自己的预约
$stmt = $link->prepare("DELETE FROM reservations WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $reservation_id, $_SESSION['user_id']);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "<script>alert('预约已取消！'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('取消失败，请重试！'); window.history.back();</script>";
}

$stmt->close();
$link->close();
?>
