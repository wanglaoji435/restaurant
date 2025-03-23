<?php
session_start();

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "restaurant";
$link = new mysqli($host, $db_user, $db_password, $db_name);

if ($link->connect_error) {
    die("数据库连接失败：" . $link->connect_error);
}

if (!isset($_SESSION['admin_id'])) {
    die("请先登录！");
}

$reservation_id = $_GET['id'] ?? '';
$customer_email = $_GET['email'] ?? '';
$reason = $_GET['reason'] ?? '未提供原因';

// 验证预约是否存在
$stmt = $link->prepare("SELECT name, date, time, people FROM reservations WHERE id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "<script>alert('该预约不存在！'); window.location.href='admin_dashboard.php';</script>";
    exit();
}

$stmt->bind_result($name, $date, $time, $people);
$stmt->fetch();
$stmt->close();

// 删除预约
$stmt = $link->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->bind_param("i", $reservation_id);
if ($stmt->execute()) {
    // 发送邮件通知顾客
    $subject = "您的餐厅预约已被取消";
    $message = "
        <html>
        <head>
            <title>预约取消通知</title>
        </head>
        <body>
            <h2>尊敬的 $name，您好！</h2>
            <p>您的餐厅预约已被取消，详情如下：</p>
            <p><strong>预约时间：</strong> $date $time</p>
            <p><strong>预约人数：</strong> $people 人</p>
            <p><strong>取消原因：</strong> $reason</p>
            <p>如有疑问，请联系餐厅。</p>
            <p>美味佳肴餐厅</p>
        </body>
        </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: restaurant@example.com" . "\r\n";

    mail($customer_email, $subject, $message, $headers);

    echo "<script>alert('预约已取消，并已通知顾客。'); window.location.href='admin_dashboard.php';</script>";
} else {
    echo "<script>alert('取消失败，请重试！'); window.history.back();</script>";
}

$stmt->close();
$link->close();
?>
