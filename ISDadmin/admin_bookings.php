<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

$message = "";

// ✅ DELETE BOOKING
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $con->prepare("DELETE FROM booktable WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "✅ Booking deleted successfully.";
    } else {
        $message = "❌ Error deleting booking.";
    }
}

// ✅ ADD TO BLACKLIST
if (isset($_GET['blacklist_phone']) && isset($_GET['blacklist_username'])) {
    $phone = $_GET['blacklist_phone'];
    $username = $_GET['blacklist_username'];
    $reason = "No-Show Booking";

    $check = $con->prepare("SELECT id FROM blacklist WHERE phone = ? LIMIT 1");
    $check->bind_param("s", $phone);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        $message = "⚠️ User already in blacklist.";
    } else {
        $stmt = $con->prepare("INSERT INTO blacklist (username, phone, reason) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $phone, $reason);

        if ($stmt->execute()) {
            $message = "✅ User added to blacklist.";
        } else {
            $message = "❌ Error adding user to blacklist.";
        }
    }
}

// ✅ بحث
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM booktable";

if (!empty($search)) {
    $query .= " WHERE name LIKE '%$search%' OR phoneNumber LIKE '%$search%' OR date LIKE '%$search%'";
}

$query .= " ORDER BY date DESC";
$result = $con->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Bookings</title>
    <style>
        body { margin:0; font-family:Arial; background:#111; color:white; }
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
        .btn-blacklist { background:red; padding:7px 12px; color:white; text-decoration:none; border-radius:5px; display:inline-block; margin-top:8px; }
        .btn-blacklist:hover { background:darkred; }
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
    <h1>Bookings</h1>

    <?php if (!empty($message)): ?>
        <div class="msg-box"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="msg-box">✅ Booking updated successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['blacklisted'])): ?>
        <div class="msg-box">✅ User added to blacklist.</div>
    <?php endif; ?>

    <!-- ✅ Search Form -->
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by name, phone, or date" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Phone</th>
            <th>People</th>
            <th>Date & Time</th>
            <th>Table</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['phoneNumber']}</td>
                    <td>{$row['numberOfPersons']}</td>
                    <td>{$row['date']}</td>
                    <td>{$row['tableNumber']}</td>
                    <td>
                        <a class='btn-view' href='admin_view_booking.php?id={$row['id']}'>View</a>
                        <a class='btn-view' style='background:#2196F3;' href='admin_edit_booking.php?id={$row['id']}'>Edit</a>
                        <a class='btn-blacklist' href='admin_bookings.php?blacklist_phone={$row['phoneNumber']}&blacklist_username={$row['name']}'>Blacklist</a>
                        <a class='btn-blacklist' style='background:#b30000;' href='admin_bookings.php?delete_id={$row['id']}'>Delete</a>
                    </td>
                </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='7'>No bookings found.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
