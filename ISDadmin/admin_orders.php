<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

$message = "";

// ✅ DELETE ORDER
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $con->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $message = $stmt->execute() ? "✅ Order deleted successfully." : "❌ Error deleting order.";
}

// ✅ ADD TO BLACKLIST
if (isset($_GET['blacklist_phone']) && isset($_GET['blacklist_username'])) {
    $phone = $_GET['blacklist_phone'];
    $username = $_GET['blacklist_username'];
    $reason = "Order Not Collected";

    $check = $con->prepare("SELECT id FROM blacklist WHERE phone = ? LIMIT 1");
    $check->bind_param("s", $phone);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        $message = "⚠️ User already in blacklist.";
    } else {
        $stmt = $con->prepare("INSERT INTO blacklist (username, phone, reason) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $phone, $reason);
        $message = $stmt->execute() ? "✅ User added to blacklist." : "❌ Error adding user to blacklist.";
    }
}

// ✅ UPDATE STATUS
if (isset($_GET['status_id']) && isset($_GET['new_status'])) {
    $status_id = intval($_GET['status_id']);
    $new_status = $_GET['new_status'];
    $allowed = ['pending', 'done', 'nottaken'];
    if (in_array($new_status, $allowed)) {
        $stmt = $con->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $status_id);
        $stmt->execute();
    }
}

// ✅ بحث
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM orders";
if (!empty($search)) {
    $query .= " WHERE username LIKE '%$search%' OR phone LIKE '%$search%' OR comments LIKE '%$search%'";
}
$query .= " ORDER BY created_at DESC";
$result = $con->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Orders</title>
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
        .btn-view { background:#00c853; padding:7px 12px; color:white; text-decoration:none; border-radius:5px; }
        .btn-view:hover { background:#00e676; }
        .btn-delete { background:#b30000; padding:7px 12px; color:white; text-decoration:none; border-radius:5px; margin-top:8px; display:inline-block; }
        .btn-delete:hover { background:#ff1a1a; }
        .btn-blacklist { background:red; padding:7px 12px; color:white; text-decoration:none; border-radius:5px; margin-top:8px; display:inline-block; }
        .btn-blacklist:hover { background:darkred; }
        .search-box { margin-top:20px; }
        .search-box input { padding:8px; width:300px; border-radius:5px; border:none; }
        .search-box button { padding:8px 12px; background:#FFD700; border:none; border-radius:5px; margin-left:10px; cursor:pointer; }
        .status-label { padding:6px 12px; border-radius:5px; font-weight:bold; display:inline-block; background:#999; color:white; }
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
    <h1>Orders</h1>

    <?php if (!empty($message)): ?>
        <div class="msg-box"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by user, phone, or comments" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Phone</th>
            <th>Comments</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $status = ucfirst($row['status']);
                $color = "#999";
                if ($row['status'] === "pending") $color = "#FFD700";
                elseif ($row['status'] === "done") $color = "#00c853";
                elseif ($row['status'] === "nottaken") $color = "#d50000";

                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['comments']}</td>
                    <td>{$row['created_at']}</td>
                    <td>
                        <span id='status-{$row['id']}' class='status-label' style='background:$color;'>$status</span><br>
                        <button onclick=\"setStatus({$row['id']}, 'pending')\" style='background:#FFD700; color:black; border:none; padding:5px 8px; border-radius:4px;'>Pending</button>
                        <button onclick=\"setStatus({$row['id']}, 'done')\" style='background:#00c853; color:white; border:none; padding:5px 8px; border-radius:4px;'>Done</button>
                        <button onclick=\"setStatus({$row['id']}, 'nottaken')\" style='background:#d50000; color:white; border:none; padding:5px 8px; border-radius:4px;'>Not Taken</button>
                    </td>
                    <td>
                        <a class='btn-view' href='admin_view_order.php?id={$row['id']}'>View</a>
                        <a class='btn-blacklist' href='admin_orders.php?blacklist_phone={$row['phone']}&blacklist_username={$row['username']}'>Blacklist</a>
                        <a class='btn-delete' href='admin_orders.php?delete_id={$row['id']}'>Delete</a>
                    </td>
                </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='7'>No orders found.</td></tr>";
        }
        ?>
    </table>
</div>

<script>
function setStatus(id, state) {
    // ✅ غيّر الحالة مباشرة بالـ URL حتى تنحفظ بالـ DB
    window.location.href = "admin_orders.php?status_id=" + id + "&new_status=" + state;
}
</script>

</body>
</html>
