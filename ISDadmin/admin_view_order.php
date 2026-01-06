<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

// ✅ إذا كبس Add to Blacklist
if (isset($_GET['blacklist_phone']) && isset($_GET['blacklist_username'])) {

    $phone = $_GET['blacklist_phone'];
    $username = $_GET['blacklist_username'];
    $reason = "Order Not Collected";

    // check if already blacklisted
    $check = $con->prepare("SELECT id FROM blacklist WHERE phone = ? LIMIT 1");
    $check->bind_param("s", $phone);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        header("Location: admin_orders.php?blacklisted=exists");
        exit();
    }

    $stmt = $con->prepare("INSERT INTO blacklist (username, phone, reason) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $phone, $reason);

    if ($stmt->execute()) {
        header("Location: admin_orders.php?blacklisted=1");
        exit();
    } else {
        header("Location: admin_orders.php?blacklisted=0");
        exit();
    }
}

// ✅ لازم يكون في ID
if (!isset($_GET['id'])) {
    header("Location: admin_orders.php");
    exit();
}

$id = intval($_GET['id']);

// ✅ جلب بيانات الطلب
$stmt = $con->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: admin_orders.php?error=notfound");
    exit();
}

// ✅ تحويل JSON إلى Array
$orderItems = json_decode($order['orderContent'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>

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

        .btn-blacklist {
            background: red;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-blacklist:hover {
            background: darkred;
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
    <a href="admin_blacklist.php">Blacklist</a>
    <a href="../logout.php" style="background:red;">Logout</a>
</div>

<div id="content">
    <h1>Order #<?php echo $order['id']; ?></h1>

    <h2 style="color:#FFD700;">Customer Info</h2>
    <p><strong>Name:</strong> <?php echo $order['username']; ?></p>
    <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
    
    <p><strong>Comments:</strong> <?php echo $order['comments']; ?></p>
    <p><strong>Date:</strong> <?php echo $order['created_at']; ?></p>

    <h2 style="color:#FFD700;">Order Items</h2>

    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>

        <?php
        $grandTotal = 0;

        foreach ($orderItems as $item) {
            $total = $item['price'] * $item['qty'];
            $grandTotal += $total;

            echo "
            <tr>
                <td><img src='../images/{$item['image']}'></td>
                <td>{$item['name']}</td>
                <td>{$item['qty']}</td>
                <td>" . number_format($item['price']) . "</td>
                <td>" . number_format($total) . "</td>
            </tr>
            ";
        }
        ?>

        <tr>
            <th colspan="4">Grand Total</th>
            <th><?php echo number_format($grandTotal); ?> LBP</th>
        </tr>
    </table>

    <a class="btn-blacklist"
       href="admin_view_order.php?blacklist_phone=<?php echo $order['phone']; ?>&blacklist_username=<?php echo $order['username']; ?>">
       Add to Blacklist
    </a>

</div>

</body>
</html>
