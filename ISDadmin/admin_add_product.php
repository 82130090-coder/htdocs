<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

// ✅ عند الضغط على زر إضافة المنتج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = $_POST['name'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $price       = $_POST['price'];

    // ✅ رفع الصورة
    $imageName = $_FILES['image']['name'];
    $imageTmp  = $_FILES['image']['tmp_name'];

    $uploadPath = "../images/" . $imageName;

    if (move_uploaded_file($imageTmp, $uploadPath)) {

        // ✅ إدخال المنتج بقاعدة البيانات
        $stmt = $con->prepare("
            INSERT INTO products (name, description, category, price, image)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("sssds", $name, $description, $category, $price, $imageName);

        if ($stmt->execute()) {
            // ✅ رجوع مباشر مع رسالة نجاح
            header("Location: admin_products.php?added=1");
            exit();
        } else {
            header("Location: admin_products.php?added=0");
            exit();
        }

        $stmt->close();

    } else {
        header("Location: admin_products.php?added=0");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #111;
            color: white;
        }

        #sidebar {
            width: 250px;
            height: 100vh;
            background: #000;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            border-right: 2px solid #FFD700;
        }

        #sidebar h2 {
            text-align: center;
            color: #FFD700;
            margin-bottom: 30px;
        }

        #sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid #333;
        }

        #sidebar a:hover {
            background: #FFD700;
            color: black;
        }

        #content {
            margin-left: 260px;
            padding: 20px;
        }

        h1 {
            color: #FFD700;
        }

        form {
            background: #222;
            padding: 20px;
            border-radius: 10px;
            width: 450px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
        }

        button {
            background: #FFD700;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
            width: 100%;
        }

        button:hover {
            background: white;
        }
    </style>
</head>
<body>

<div id="sidebar">
    <h2>Admin Panel</h2>

    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_products.php">Products</a>
    <a href="admin_add_product.php">Add Product</a>
    <a href="admin_orders.php">Orders</a>
    <a href="admin_users.php">Users</a>
    <a href="admin_bookings.php">Bookings</a>
    <a href="../logout.php" style="background:red;">Logout</a>
</div>

<div id="content">
    <h1>Add New Product</h1>

    <form method="POST" enctype="multipart/form-data">

        <label>Product Name:</label>
        <input type="text" name="name" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Category:</label>
        <select name="category" required>
            <option>Main Dishes</option>
            <option>Burger</option>
            <option>Appetizers</option>
            <option>Salads</option>
            <option>Drinks</option>
            <option>Desserts</option>
        </select>

        <label>Price (LBP):</label>
        <input type="number" name="price" required min="0">

        <label>Image:</label>
        <input type="file" name="image" required>

        <button type="submit">Add Product</button>
    </form>
</div>

</body>
</html>
