<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

// ✅ إحصائيات
$productsCount   = $con->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$ordersCount     = $con->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$bookingsCount   = $con->query("SELECT COUNT(*) AS c FROM booktable")->fetch_assoc()['c'];
$usersCount      = $con->query("SELECT COUNT(*) AS c FROM signup")->fetch_assoc()['c'];
$blacklistCount  = $con->query("SELECT COUNT(*) AS c FROM blacklist")->fetch_assoc()['c'];

// ✅ آخر 5 طلبات
$latestOrders = $con->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");

// ✅ آخر 5 حجوزات
$latestBookings = $con->query("SELECT * FROM booktable ORDER BY date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <style>
        body { margin: 0; font-family: Arial; background: #111; color: white; }

        #sidebar {
            width: 250px; height: 100vh; background: #000;
            position: fixed; left: 0; top: 0; padding-top: 20px;
            border-right: 2px solid #FFD700;
        }

        #sidebar h2 { text-align: center; color: #FFD700; margin-bottom: 30px; }

        #sidebar a {
            display: block; padding: 15px 20px; color: white;
            text-decoration: none; border-bottom: 1px solid #333;
        }

        #sidebar a:hover { background: #FFD700; color: black; }

        #content { margin-left: 260px; padding: 20px; }

        h1 { color: #FFD700; }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: #222; padding: 20px; border-radius: 10px;
            text-align: center; border: 1px solid #333;
        }

        .card h2 { margin: 0; color: #FFD700; font-size: 40px; }
        .card p { margin-top: 10px; font-size: 18px; }

        .section {
            margin-top: 40px;
        }

        table {
            width: 100%; border-collapse: collapse; margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #333; padding: 10px; text-align: center;
        }

        table th { background: #222; color: #FFD700; }
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
    <h1>Dashboard</h1>

    <div class="cards">
        <div class="card">
            <h2><?php echo $productsCount; ?></h2>
            <p>Products</p>
        </div>

        <div class="card">
            <h2><?php echo $ordersCount; ?></h2>
            <p>Orders</p>
        </div>

        <div class="card">
            <h2><?php echo $bookingsCount; ?></h2>
            <p>Bookings</p>
        </div>

        <div class="card">
            <h2><?php echo $usersCount; ?></h2>
            <p>Users</p>
        </div>

        <div class="card">
            <h2><?php echo $blacklistCount; ?></h2>
            <p>Blacklisted Users</p>
        </div>
    </div>

    <div class="section">
        <h2 style="color:#FFD700;">Latest Orders</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Phone</th>
                <th>Date</th>
            </tr>

            <?php
            if ($latestOrders->num_rows > 0) {
                while ($row = $latestOrders->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['created_at']}</td>
                    </tr>
                    ";
                }
            } else {
                echo "<tr><td colspan='4'>No orders found.</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="section">
    <h2 style="color:#FFD700;">Latest Bookings</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Phone</th>
            <th>Date</th>
            <th>Time</th>
        </tr>

        <?php
        if ($latestBookings->num_rows > 0) {
            while ($row = $latestBookings->fetch_assoc()) {

                $date = date('Y-m-d', strtotime($row['date']));
                $time = date('H:i:s', strtotime($row['date']));

                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['phoneNumber']}</td>
                    <td>{$date}</td>
                    <td>{$time}</td>
                </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='5'>No bookings found.</td></tr>";
        }
        ?>
    </table>
</div>


</div>

</body>
</html>
