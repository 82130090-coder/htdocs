<?php
session_start();
include '../db.php';

// حماية
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

// لازم ID
if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $con->prepare("SELECT * FROM signup WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<script>alert('User not found'); window.location='admin_users.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>

    <style>
        body { margin: 0; font-family: Arial; background: #111; color: white; }

        #sidebar {
            width: 250px; height: 100vh; background: #000;
            position: fixed; left: 0; top: 0; padding-top: 20px;
            border-right: 2px solid #FFD700;
        }

        #sidebar a {
            display: block; padding: 15px 20px; color: white;
            text-decoration: none; border-bottom: 1px solid #333;
        }

        #sidebar a:hover { background: #FFD700; color: black; }

        #content { margin-left: 260px; padding: 20px; }

        h1 { color: #FFD700; }

        .box {
            background: #222; padding: 20px; border-radius: 10px;
            width: 400px; border: 1px solid #333;
        }

        .row { margin-bottom: 12px; }
        .label { color: #FFD700; font-weight: bold; }
    </style>
</head>
<body>

<div id="sidebar">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_products.php">Products</a>
    <a href="admin_orders.php">Orders</a>
    <a href="admin_users.php">Users</a>
    <a href="admin_bookings.php">Bookings</a>
    <a href="admin_blacklist.php">Blacklist</a>
    <a href="../logout.php" style="background:red;">Logout</a>
</div>

<div id="content">
    <h1>User Details</h1>

    <div class="box">
        <div class="row"><span class="label">ID:</span> <?php echo $user['id']; ?></div>
        <div class="row"><span class="label">Username:</span> <?php echo $user['username']; ?></div>
        <div class="row"><span class="label">Phone:</span> <?php echo $user['phone_number']; ?></div>
        <div class="row"><span class="label">Email:</span> <?php echo $user['email']; ?></div>
        <div class="row"><span class="label">Password (hashed/plain):</span> <?php echo $user['password']; ?></div>
       

</div>

</body>
</html>
