<?php
session_start();
include '../db.php';

// حماية
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone_number']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if (!empty($username) && !empty($phone) && !empty($email) && !empty($password) && !empty($confirm)) {

        if ($password !== $confirm) {
            echo "<script>alert('Passwords do not match');</script>";
        } else {

            $hashed = md5($password);

            $stmt = $con->prepare("INSERT INTO signup (username, phone_number, email, password, confirm_password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $phone, $email, $hashed, $confirm);

            if ($stmt->execute()) {
                echo "<script>alert('User added successfully'); window.location='admin_users.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error adding user');</script>";
            }
        }

    } else {
        echo "<script>alert('All fields are required');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>

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
    <h1>Add New User</h1>

    <form method="POST">

        <input type="text" name="username" placeholder="Full Name" required>

        <input type="text" name="phone_number" placeholder="Phone Number" required>

        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit">Add User</button>

    </form>
</div>

</body>
</html>
