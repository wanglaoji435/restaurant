<?php
session_start();
$link = new mysqli("localhost", "root", "", "restaurant");

if ($link->connect_error) {
    die("数据库连接失败：" . $link->connect_error);
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// 先检查邮箱是否已存在
$check_stmt = $link->prepare("SELECT id FROM users WHERE email = ?");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>alert('该邮箱已注册，请直接登录！'); window.location.href='login.html';</script>";
    exit();
}

$check_stmt->close();

// 插入用户
$stmt = $link->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    echo "<script>alert('注册成功，请登录！'); window.location.href='login.html';</script>";
} else {
    echo "<script>alert('注册失败，请重试！'); window.history.back();</script>";
}

$stmt->close();
$link->close();
?>
