<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('请先登录！'); window.location.href='index.html';</script>";
    exit();
}

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "restaurant";
$link = new mysqli($host, $db_user, $db_password, $db_name);

$user_id = $_SESSION['user_id'];
$stmt = $link->prepare("SELECT id, date, time, people FROM reservations WHERE user_id = ? ORDER BY date, time");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($reservation_id, $date, $time, $people);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <script src="db.js"></script>
</head>
<body>
    <h2>预约记录</h2>
    <?php if ($stmt->num_rows > 0): ?>
        <table>
            <tr><th>日期</th><th>时间</th><th>人数</th><th>操作</th></tr>
            <?php while ($stmt->fetch()): ?>
                <tr>
                    <td><?= htmlspecialchars($date); ?></td>
                    <td><?= htmlspecialchars($time); ?></td>
                    <td><?= htmlspecialchars($people); ?></td>
                    <td><button onclick="cancelReservation(<?= $reservation_id; ?>)">取消</button></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>暂无预约记录。</p>
    <?php endif; ?>
</body>
</html>

<?php $stmt->close(); $link->close(); ?>
