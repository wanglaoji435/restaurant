<?php
session_start();
$link = new mysqli("localhost", "root", "", "restaurant");

if ($link->connect_error) {
    die("数据库连接失败：" . $link->connect_error);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $link->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($userId, $storedPassword);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    if ($password == $storedPassword) {  // 直接比对明文密码
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        echo "<script>alert('登录成功！'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('密码错误，请重试！'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('用户不存在！'); window.history.back();</script>";
}

$stmt->close();
$link->close();
?>

$stmt->close();
$link->close();
?>
