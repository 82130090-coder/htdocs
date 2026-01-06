<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging out...</title>
    <script>
        // ✅ امسح الكارت من localStorage وقت يعمل logout
        localStorage.removeItem('gp_cart');
        // ✅ رجّع المستخدم على الصفحة الرئيسية
        window.location.href = "index.php";
    </script>
</head>
<body>
</body>
</html>
