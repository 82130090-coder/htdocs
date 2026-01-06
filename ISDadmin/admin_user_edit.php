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
    header("Location: admin_users.php?error=notfound");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone_number']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($phone) && !empty($email)) {

        if (!empty($password)) {
            $hashed = md5($password);
        } else {
            $hashed = $user['password'];
        }

        $stmt2 = $con->prepare("UPDATE signup SET username=?, phone_number=?, email=?, password=? WHERE id=?");
        $stmt2->bind_param("ssssi", $username, $phone, $email, $hashed, $id);

        if ($stmt2->execute()) {
            // ✅ رجوع مباشر مع رسالة نجاح
            header("Location: admin_users.php?updated=1");
            exit();
        } else {
            header("Location: admin_users.php?updated=0");
            exit();
        }
    } else {
        header("Location: admin_users.php?updated=0");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

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

        form {
            background: #222; padding: 20px; border-radius: 10px;
            width: 400px; border: 1px solid #333;
        }

        input {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border-radius: 5px; border: none;
        }

        button {
            padding: 10px 15px; background: #FFD700; border: none;
            border-radius: 5px; cursor: pointer; width: 100%;
        }

        button:hover { background: #ffeb3b; }
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
    <h1>Edit User</h1>

    <form method="POST">

        <input type="text" name="username" value="<?php echo $user['username']; ?>" required>

        <input type="text" name="phone_number" value="<?php echo $user['phone_number']; ?>" required>

        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

        <input type="password" name="password" placeholder="New Password (optional)">

        <button type="submit">Save Changes</button>

    </form>
</div>

</body>
</html>
