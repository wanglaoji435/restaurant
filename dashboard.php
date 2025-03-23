<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('请先登录！'); window.location.href='login.html';</script>";
    exit();
}

$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "restaurant";
$link = new mysqli($host, $db_user, $db_password, $db_name);

if ($link->connect_error) {
    die("数据库连接失败：" . $link->connect_error);
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// 查询用户预约信息
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
    <title>用户控制台</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }
        .container { max-width: 600px; margin: auto; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        input, button, select { width: 100%; padding: 10px; margin: 5px 0; }
        .logout { color: red; display: block; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>欢迎, <?php echo htmlspecialchars($user_email); ?>！</h2>

    <!-- 预约表单 -->
    <h3>预约餐厅</h3>
    <form action="reserve.php" method="POST">
        <input type="text" name="name" placeholder="姓名" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
        <input type="date" name="date" required>

        <label for="time">预约时间:</label>
        <select name="time" id="time" required>
            <option value="">请选择时间</option>
            <?php
            for ($hour = 11; $hour <= 22; $hour++) {
                echo "<option value='$hour:00'>$hour:00</option>";
                if ($hour != 22) { // 22:30 不是营业时间，所以不加
                    echo "<option value='$hour:30'>$hour:30</option>";
                }
            }
            ?>
        </select>

        <input type="number" name="people" placeholder="人数 (最多40)" required min="1" max="40">
        <button type="submit">提交预约</button>
    </form>

    <!-- 预约记录 -->
    <h3>我的预约</h3>
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
                        <a href="cancel_reservation.php?id=<?php echo $reservation_id; ?>" style="color:red;" onclick="return confirm('确定要取消此预约吗？');">取消</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="color: red;">暂无预约记录。</p>
    <?php endif; ?>

    <a class="logout" href="logout.php">退出登录</a>

</div>

</body>
</html>

<?php
$stmt->close();
$link->close();
?>
