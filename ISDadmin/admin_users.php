<?php
session_start();
include '../db.php';

// ✅ حماية: بس admin رقم 4 بيفوت
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

$message = "";

// ✅ DELETE USER
if (isset($_GET['delete_id'])) {

    $delete_id = intval($_GET['delete_id']);

    // ما منسمح تمسح admin رقم 4
    if ($delete_id == 4) {
        $message = "❌ You cannot delete the main admin.";
    } else {
        $stmt = $con->prepare("DELETE FROM signup WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $message = "✅ User deleted successfully.";
        } else {
            $message = "❌ Error deleting user.";
        }
    }
}

// ✅ بحث
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM signup";

if (!empty($search)) {
    $query .= " WHERE username LIKE '%$search%' OR phone_number LIKE '%$search%' OR email LIKE '%$search%'";
}

$query .= " ORDER BY id DESC";
$result = $con->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>

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

        .msg-box {
            background: #222;
            border-left: 4px solid #FFD700;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        table {
            width: 100%; border-collapse: collapse; margin-top: 25px;
        }

        table th, table td {
            border: 1px solid #333; padding: 10px; text-align: center;
        }

        table th { background: #222; color: #FFD700; }

        .btn {
            padding: 6px 10px; border-radius: 5px; text-decoration: none; color: white;
        }

        .btn-view { background: #00c853; }
        .btn-edit { background: #2196F3; }
        .btn-delete { background: red; }
        .btn-view:hover { background: #00e676; }
        .btn-edit:hover { background: #64b5f6; }
        .btn-delete:hover { background: darkred; }

        .search-box {
            margin-top: 20px;
        }

        .search-box input {
            padding: 8px; width: 300px; border-radius: 5px; border: none;
        }

        .search-box button {
            padding: 8px 12px; background: #FFD700; border: none; border-radius: 5px;
            margin-left: 10px; cursor: pointer;
        }

        .add-user {
            margin-top: 20px;
            display: inline-block;
            background: #FFD700;
            color: black;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .add-user:hover {
            background: #ffeb3b;
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
    <h1>Users</h1>

    <?php if (!empty($message)): ?>
        <div class="msg-box"><?php echo $message; ?></div>
    <?php endif; ?>

<?php if (isset($_GET['updated'])): ?>
    <div class="msg-box">✅ User updated successfully.</div>
<?php endif; ?>


    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by name, phone, or email" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <a href="admin_user_add.php" class="add-user">+ Add New User</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['phone_number']}</td>
                    <td>{$row['email']}</td>
                    <td>
                        <a class='btn btn-view' href='admin_user_view.php?id={$row['id']}'>View</a>
                        <a class='btn btn-edit' href='admin_user_edit.php?id={$row['id']}'>Edit</a>
                        <a class='btn btn-delete' href='admin_users.php?delete_id={$row['id']}'>Delete</a>
                    </td>
                </tr>
                ";
            }
        } else {
            echo "<tr><td colspan='5'>No users found.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
