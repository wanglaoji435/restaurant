<?php
session_start();
session_destroy();
echo "<script>alert('您已退出登录！'); window.location.href='login.html';</script>";
?>
