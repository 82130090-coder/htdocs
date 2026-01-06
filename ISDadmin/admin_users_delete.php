<?php
session_start();
include '../db.php';

// ✅ حماية
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $con->prepare("DELETE FROM signup WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('User deleted'); window.location='admin_users.php';</script>";
} else {
    echo "<script>alert('Error deleting user'); window.location='admin_users.php';</script>";
}
?>
