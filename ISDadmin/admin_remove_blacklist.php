<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

// ✅ لازم يكون في ID
if (!isset($_GET['id'])) {
    header("Location: admin_blacklist.php");
    exit();
}

$id = intval($_GET['id']);

// ✅ حذف الشخص من الـ blacklist
$stmt = $con->prepare("DELETE FROM blacklist WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('User removed from blacklist'); window.location='admin_blacklist.php';</script>";
} else {
    echo "<script>alert('Error removing user'); window.location='admin_blacklist.php';</script>";
}

$stmt->close();
?>
