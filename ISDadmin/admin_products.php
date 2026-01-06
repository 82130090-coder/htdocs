<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

$message = "";

// ✅ DELETE PRODUCT (بدون reload خارجي)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $stmt = $con->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "✅ Product deleted successfully.";
    } else {
        $message = "❌ Error deleting product.";
    }
}

// ✅ جلب المنتجات
$result = $con->query("SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>

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

        .msg-box {
            background: #222;
            border-left: 4px solid #FFD700;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table th, table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }

        table th {
            background: #222;
            color: #FFD700;
        }

        img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .btn-edit {
            background: #00c853;
            padding: 7px 12px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-edit:hover {
            background: #00e676;
        }

        .btn-delete {
            background: red;
            padding: 7px 12px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-delete:hover {
            background: darkred;
        }

        .add-btn {
            background: #FFD700;
            padding: 10px 15px;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .add-btn:hover {
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
    <h1>Products</h1>

    <?php if (!empty($message)): ?>
        <div class="msg-box"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
    <div class="msg-box success">✅ Product updated successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['added'])): ?>
    <div class="msg-box success">✅ Product added successfully.</div>
<?php endif; ?>


    <a class="add-btn" href="admin_add_product.php">+ Add New Product</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Price (LBP)</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td><img src='../images/{$row['image']}'></td>
                    <td>{$row['name']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['category']}</td>
                    <td>" . number_format($row['price']) . "</td>
                    <td>
                        <a class='btn-edit' href='admin_edit_product.php?id={$row['id']}'>Edit</a>
                        <a class='btn-delete' href='admin_products.php?delete_id={$row['id']}'>Delete</a>
                    </td>
                </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='7'>No products found.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
