
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div id="bar">
        <div class="logo-div">
            <img src="images/logo.jpg" alt="Logo">
        </div>
        <div class="navbar">
        <a href="product.php">Add Product</a>
        <a href="orders.php">Orders</a>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="menu.php">Menu</a>
        <a href="book.php">Book Table</a>
    </div>
    </div>
    <table>
        <tr>
            <th id="orderUsername">Username</th>
            <th id="orderPhone">Phone</th>
            <th id="orderLocation">Location</th>
            <th id="orderContent">Order Content</th>
            <th id="orderComments">Comments</th>
            <th id="orderDate">Ordered at:</th>
            <th id="orderAction">Action</th>
            
        </tr>
        <?php
include 'db.php';

// Check if the delete button was pressed
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    // Prepare the delete statement
    $sql = "DELETE FROM orders WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT id, username, phone, location, orderContent, comments, created_at FROM orders"; 
if ($result = mysqli_query($con, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $id = $row['id'];
            $username = $row['username'];
            $phone = $row['phone'];
            $location = $row['location'];
            $orderContent = $row['orderContent'];
            $comments = $row['comments'];
            $date = $row['created_at'];
            echo "<tr>
                    <td>{$username}</td>
                    <td>{$phone}</td>
                    <td>{$location}</td>
                    <td>{$orderContent}</td>
                    <td>{$comments}</td>
                    <td>{$date}</td>
                    <td>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='{$id}'>
                            <button type='submit' name='delete' onclick='return confirm(\"Are you sure you want to delete this order?\");'>
                                <i class='fas fa-trash-alt'></i>
                            </button>
                        </form>
                    </td>
                   </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No orders found.</td></tr>";
    }
}
?>
    </table>
</body>
</html>