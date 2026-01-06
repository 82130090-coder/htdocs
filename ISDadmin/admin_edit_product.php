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

// ✅ جلب بيانات المنتج
$stmt = $con->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: admin_products.php?error=notfound");
    exit();
}

// ✅ عند حفظ التعديلات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = $_POST['name'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $price       = $_POST['price'];

    $newImage = $product['image']; // default

    // ✅ إذا رفع صورة جديدة
    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmp  = $_FILES['image']['tmp_name'];

        $uploadPath = "../images/" . $imageName;

        if (move_uploaded_file($imageTmp, $uploadPath)) {
            $newImage = $imageName;
        }
    }

    // ✅ تحديث البيانات
    $stmt2 = $con->prepare("
        UPDATE products 
        SET name=?, description=?, category=?, price=?, image=? 
        WHERE id=?
    ");

    $stmt2->bind_param("sssisi", $name, $description, $category, $price, $newImage, $id);

    if ($stmt2->execute()) {
        // ✅ رجوع مباشر مع رسالة نجاح
        header("Location: admin_products.php?updated=1");
        exit();
    } else {
        header("Location: admin_products.php?updated=0");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>

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

        img {
            width: 120px;
            border-radius: 10px;
            margin-bottom: 10px;
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
    <h1>Edit Product</h1>

    <form method="POST" enctype="multipart/form-data">

        <label>Current Image:</label><br>
        <img src="../images/<?php echo $product['image']; ?>">

        <label>Change Image:</label>
        <input type="file" name="image">

        <label>Product Name:</label>
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?php echo $product['description']; ?></textarea>

        <label>Category:</label>
        <select name="category" required>
            <option <?php if ($product['category']=="Main Dishes") echo "selected"; ?>>Main Dishes</option>
            <option <?php if ($product['category']=="Side Dishes") echo "selected"; ?>>Side Dishes</option>
            <option <?php if ($product['category']=="Appetizers") echo "selected"; ?>>Appetizers</option>
            <option <?php if ($product['category']=="Salads") echo "selected"; ?>>Salads</option>
            <option <?php if ($product['category']=="Drinks") echo "selected"; ?>>Drinks</option>
            <option <?php if ($product['category']=="Desserts") echo "selected"; ?>>Desserts</option>
        </select>

        <label>Price (LBP):</label>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" required>

        <button type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
