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

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    die("请先登录");
}

$user_id = $_SESSION['user_id'];

// 查询当前用户的预约记录
$stmt = $link->prepare("SELECT id, date, time, people FROM reservations WHERE user_id = ? ORDER BY date, time");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($reservation_id, $date, $time, $people);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的预约</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: auto; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .restaurant-info { margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px; }
        .cancel-btn { background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
    </style>
    <script>
        function cancelReservation(reservationId) {
            if (confirm("确定要取消此预约吗？")) {
                window.location.href = "cancel_reservation.php?id=" + reservationId;
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2>我的预约</h2>

    <?php if ($stmt->num_rows > 0): ?>
        <table>
            <tr>
                <th>日期</th>
                <th>时间</th>
                <th>人数</th>
                <th>操作</th>
            </tr>
            <?php while ($stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($date); ?></td>
                    <td><?php echo htmlspecialchars($time); ?></td>
                    <td><?php echo htmlspecialchars($people); ?></td>
                    <td>
                        <button class="cancel-btn" onclick="cancelReservation(<?php echo $reservation_id; ?>)">取消预约</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: red;">暂无预约记录。</p>
    <?php endif; ?>

    <div class="restaurant-info">
        
        <h3>餐厅介绍</h3>
        <p><strong>名称：</strong>美味佳肴餐厅</p>
        <p><strong>地址：</strong>東京都新宿區名店ビル2F</p>
        <p><strong>营业时间：</strong>每天 11:00 - 23:00</p>
        <p><strong>特色菜：</strong>麻辣火锅</p>
        <p><strong>聯係電話：</strong>08040699386</p>
    </div>
</div>

</body>
</html>
<a href="logout.php" style="display:block; text-align:center; margin-top:20px; font-size:16px; color:red;">退出登录</a>

<?php
$stmt->close();
$link->close();
?>