<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

// ✅ إذا كبس Add to Blacklist
if (isset($_GET['blacklist_phone']) && isset($_GET['blacklist_username'])) {

    $phone = $_GET['blacklist_phone'];
    $username = $_GET['blacklist_username'];
    $reason = "No-Show Booking";

    // check if already blacklisted
    $check = $con->prepare("SELECT id FROM blacklist WHERE phone = ? LIMIT 1");
    $check->bind_param("s", $phone);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        header("Location: admin_bookings.php?blacklisted=exists");
        exit();
    }

    $stmt = $con->prepare("INSERT INTO blacklist (username, phone, reason) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $phone, $reason);

    if ($stmt->execute()) {
        header("Location: admin_bookings.php?blacklisted=1");
        exit();
    } else {
        header("Location: admin_bookings.php?blacklisted=0");
        exit();
    }
}

// ✅ لازم ID
if (!isset($_GET['id'])) {
    header("Location: admin_bookings.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $con->prepare("SELECT * FROM booktable WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header("Location: admin_bookings.php?error=notfound");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Details</title>
    <style>
        body { margin: 0; font-family: Arial; background: #111; color: white; }
        #sidebar { width: 250px; height: 100vh; background: #000; position: fixed; left: 0; top: 0; padding-top: 20px; border-right: 2px solid #FFD700; }
        #sidebar a { display: block; padding: 15px 20px; color: white; text-decoration: none; border-bottom: 1px solid #333; }
        #sidebar a:hover { background: #FFD700; color: black; }
        #content { margin-left: 260px; padding: 20px; }
        h1 { color: #FFD700; }
        .btn-blacklist { background: red; padding: 10px 15px; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
        .btn-blacklist:hover { background: darkred; }
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
</div>

<div id="content">
    <h1>Booking #<?php echo $booking['id']; ?></h1>

    <p><strong>Name:</strong> <?php echo $booking['name']; ?></p>
    <p><strong>Phone:</strong> <?php echo $booking['phoneNumber']; ?></p>
    <p><strong>People:</strong> <?php echo $booking['numberOfPersons']; ?></p>
    <p><strong>Date:</strong> <?php echo date('Y-m-d', strtotime($booking['date'])); ?></p>
    <p><strong>Time:</strong> <?php echo date('H:i:s', strtotime($booking['date'])); ?></p>
    <p><strong>Comments:</strong> <?php echo $booking['reason']; ?></p>

    <a class="btn-blacklist"
       href="admin_view_booking.php?blacklist_phone=<?php echo $booking['phoneNumber']; ?>&blacklist_username=<?php echo $booking['name']; ?>">
       Add to Blacklist
    </a>
</div>

</body>
</html>
