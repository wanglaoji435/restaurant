<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('请先登录！'); window.location.href='admin_login.html';</script>";
    exit();
}

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "restaurant";
$link = new mysqli($host, $db_user, $db_password, $db_name);

$stmt = $link->prepare("SELECT id, name, email, date, time, people FROM reservations ORDER BY date, time");
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($reservation_id, $name, $email, $date, $time, $people);
?>

<!DOCTYPE html>
<html lang="zh">
<head><script src="db.js"></script></head>
<body>
    <h2>所有预约</h2>
    <table>
        <tr><th>姓名</th><th>邮箱</th><th>日期</th><th>时间</th><th>人数</th><th>操作</th></tr>
        <?php while ($stmt->fetch()): ?>
            <tr>
                <td><?= htmlspecialchars($name); ?></td>
                <td><?= htmlspecialchars($email); ?></td>
                <td><?= htmlspecialchars($date); ?></td>
                <td><?= htmlspecialchars($time); ?></td>
                <td><?= htmlspecialchars($people); ?></td>
                <td><button onclick="cancelReservation(<?= $reservation_id; ?>)">取消</button></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php $stmt->close(); $link->close(); ?>
