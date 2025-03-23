<?php
session_start();
$link = new mysqli("localhost", "root", "", "restaurant");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('请先登录！'); window.location.href='login.html';</script>";
    exit();
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$people = $_POST['people'] ?? '';

// 允许的固定时间点
$allowed_times = [];
for ($hour = 11; $hour <= 22; $hour++) {
    $allowed_times[] = "$hour:00";
    if ($hour != 22) { // 22:30 不是营业时间，所以不加
        $allowed_times[] = "$hour:30";
    }
}

// 确保时间是合法的
if (!in_array($time, $allowed_times)) {
    echo "<script>alert('请选择有效的预约时间！'); window.history.back();</script>";
    exit();
}

// 限制人数 1-40
if ($people < 1 || $people > 40) {
    echo "<script>alert('人数必须在 1-40 之间！'); window.history.back();</script>";
    exit();
}

// 插入预约
$stmt = $link->prepare("INSERT INTO reservations (user_id, name, email, date, time, people) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssi", $_SESSION['user_id'], $name, $email, $date, $time, $people);

if ($stmt->execute()) {
    echo "<script>alert('预约成功！'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('预约失败，请重试！'); window.history.back();</script>";
}

$stmt->close();
$link->close();
?>
