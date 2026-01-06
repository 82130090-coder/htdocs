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
    header("Location: admin_products.php");
    exit();
}

$id = intval($_GET['id']);

// ✅ جلب اسم الصورة قبل الحذف
$stmt = $con->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product) {
    $imagePath = "../images/" . $product['image'];

    // ✅ حذف المنتج من قاعدة البيانات
    $stmt2 = $con->prepare("DELETE FROM products WHERE id = ?");
    $stmt2->bind_param("i", $id);

    if ($stmt2->execute()) {

        // ✅ حذف الصورة من السيرفر (اختياري)
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        echo "<script>alert('Product deleted successfully!'); window.location='admin_products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product'); window.location='admin_products.php';</script>";
    }

    $stmt2->close();
}

$stmt->close();
?>
