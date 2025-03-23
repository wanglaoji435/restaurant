<?php
session_start();
$link = new mysqli("localhost", "root", "", "restaurant");

if ($link->connect_error) {
    die("数据库连接失败：" . $link->connect_error);
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// 查询管理员信息
$stmt = $link->prepare("SELECT id, password FROM admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($adminId, $storedPassword);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    if ($password == $storedPassword) {  // ✅ 直接比对明文密码
        $_SESSION['admin_id'] = $adminId;
        echo "<script>alert('管理员登录成功！'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('密码错误，请重试！'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('管理员账号不存在！'); window.history.back();</script>";
}

$stmt->close();
$link->close();
?>
