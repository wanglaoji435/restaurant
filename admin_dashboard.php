<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("<script>alert('请先登录！'); window.location.href='admin_login.html';</script>");
}

// 连接数据库
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "restaurant";
$link = new mysqli($host, $db_user, $db_password, $db_name);

if ($link->connect_error) {
    die("数据库连接失败：" . $link->connect_error);
}

// 获取所有预约记录
$stmt = $link->prepare("SELECT id, name, email, date, time, people FROM reservations ORDER BY date, time");
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($reservation_id, $name, $email, $date, $time, $people);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员后台</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .cancel-btn { background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .logout { display: block; text-align: center; margin-top: 20px; }
    </style>
    <script>
        function cancelReservation(reservationId, email) {
            let reason = prompt("请输入取消预约的原因：");
            if (reason) {
                window.location.href = "cancel_reservation_by_admin.php?id=" + reservationId + "&email=" + email + "&reason=" + encodeURIComponent(reason);
            }
        }
    </script>
</head>
<body>

    <h2>管理员后台</h2>

    <?php if ($stmt->num_rows > 0): ?>
        <table>
            <tr>
                <th>姓名</th>
                <th>邮箱</th>
                <th>日期</th>
                <th>时间</th>
                <th>人数</th>
                <th>操作</th>
            </tr>
            <?php while ($stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($name); ?></td>
                    <td><?php echo htmlspecialchars($email); ?></td>
                    <td><?php echo htmlspecialchars($date); ?></td>
                    <td><?php echo htmlspecialchars($time); ?></td>
                    <td><?php echo htmlspecialchars($people); ?></td>
                    <td>
                        <button class="cancel-btn" onclick="cancelReservation(<?php echo $reservation_id; ?>, '<?php echo $email; ?>')">取消</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: red;">暂无预约记录。</p>
    <?php endif; ?>

    <a class="logout" href="admin_logout.php">退出登录</a>

</body>
</html>

<?php
$stmt->close();
$link->close();
?>
