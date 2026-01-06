<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_bookings.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $con->prepare("DELETE FROM booktable WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Booking deleted'); window.location='admin_bookings.php';</script>";
} else {
    echo "<script>alert('Error deleting booking'); window.location='admin_bookings.php';</script>";
}
?>
