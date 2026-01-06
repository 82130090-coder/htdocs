<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

// ✅ لازم يكون في بيانات
if (!isset($_GET['phone']) || !isset($_GET['username']) || !isset($_GET['reason'])) {
    echo "<script>alert('Missing data'); window.location='admin_orders.php';</script>";
    exit();
}

$phone    = $_GET['phone'];
$username = $_GET['username'];
$reason   = $_GET['reason'];

// ✅ هل الشخص موجود أصلاً بالـ blacklist؟
$stmt = $con->prepare("SELECT * FROM blacklist WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();

if ($existing) {

    // ✅ إذا موجود → منزيد strike
    $newStrike = $existing['strikes'] + 1;

    $stmt2 = $con->prepare("UPDATE blacklist SET strikes = ?, reason = ?, updated_at = NOW() WHERE phone = ?");
    $stmt2->bind_param("iss", $newStrike, $reason, $phone);
    $stmt2->execute();

    echo "<script>alert('User already in blacklist. Strike added.'); window.location='admin_orders.php';</script>";
    exit();

} else {

    // ✅ إذا مش موجود → منضيفه جديد
    $stmt3 = $con->prepare("
        INSERT INTO blacklist (username, phone, reason, strikes, created_at)
        VALUES (?, ?, ?, 1, NOW())
    ");
    $stmt3->bind_param("sss", $username, $phone, $reason);
    $stmt3->execute();

    echo "<script>alert('User added to blacklist'); window.location='admin_orders.php';</script>";
    exit();
}
?>
