<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

$message = "";

// ✅ REMOVE FROM BLACKLIST
if (isset($_GET['remove_id'])) {
    $remove_id = intval($_GET['remove_id']);
    $stmt = $con->prepare("DELETE FROM blacklist WHERE id = ?");
    $stmt->bind_param("i", $remove_id);

    if ($stmt->execute()) {
        $message = "✅ User removed from blacklist.";
    } else {
        $message = "❌ Error removing user.";
    }
}

// ✅ بحث
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM blacklist";

if (!empty($search)) {
    $query .= " WHERE username LIKE '%$search%' OR phone LIKE '%$search%' OR reason LIKE '%$search%'";
}

$query .= " ORDER BY strikes DESC, created_at DESC";
$result = $con->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blacklist</title>
    <style>
        body { margin:0; font-family:Arial,sans-serif; background:#111; color:white; }
        #sidebar { width:250px; height:100vh; background:#000; position:fixed; left:0; top:0; padding-top:20px; border-right:2px solid #FFD700; }
        #sidebar h2 { text-align:center; color:#FFD700; margin-bottom:30px; }
        #sidebar a { display:block; padding:15px 20px; color:white; text-decoration:none; border-bottom:1px solid #333; }
        #sidebar a:hover { background:#FFD700; color:black; }
        #content { margin-left:260px; padding:20px; }
        h1 { color:#FFD700; }
        .msg-box { background:#222; border-left:4px solid #FFD700; padding:12px; margin-bottom:15px; border-radius:5px; font-weight:bold; }
        table { width:100%; border-collapse:collapse; margin-top:25px; }
        table th, table td { border:1px solid #333; padding:10px; text-align:center; }
        table th { background:#222; color:#FFD700; }
        .btn-remove { background:#00c853; padding:7px 12px; color:white; text-decoration:none; border-radius:5px; }
        .btn-remove:hover { background:#00e676; }
        .search-box { margin-top:20px; }
        .search-box input { padding:8px; width:300px; border-radius:5px; border:none; }
        .search-box button { padding:8px 12px; background:#FFD700; border:none; border-radius:5px; margin-left:10px; cursor:pointer; }
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
    <h1>Blacklist</h1>

    <?php if (!empty($message)): ?>
        <div class="msg-box"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- ✅ Search Form -->
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by name, phone, or reason" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Phone</th>
            <th>Reason</th>
            <th>Strikes</th>
            <th>Added On</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['reason']}</td>
                    <td>{$row['strikes']}</td>
                    <td>{$row['created_at']}</td>
                    <td>
                        <a class='btn-remove' href='admin_blacklist.php?remove_id={$row['id']}'>Remove</a>
                    </td>
                </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='7'>No blacklisted users.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
