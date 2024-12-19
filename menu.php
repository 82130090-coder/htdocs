<?php
session_start();
include 'db.php';
if ($con) {
    $signup_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $phone = $_POST['phone'];
        $location = $_POST['location'];
        $orderContent=$_POST['orderContent'];
        $comments = $_POST['comments'];
        $user_id= $_POST['signup_id'];

        $sql = "INSERT INTO orders(username, phone, location,orderContent, comments,signup_id ) VALUES ('$username', '$phone', '$location','$orderContent', '$comments', $user_id)";
        $QueryResult = mysqli_query($con, $sql);
        if ($QueryResult) {
            mysqli_close($con);
            echo ("Row successfully Inserted ");
        } else {
            echo ("Error found: " . mysqli_error($con));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="./ISDadmin/script/product.js"></script>
</head>
<body>
 <div id="bar">
    <div class="logo-div">
        <img src="images/logo.jpg" alt="Logo">
    </div>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="menu.php">Menu</a>
        <a href="book.php">Book Table</a>
    </div>
</div>
<h1>My Menu</h1>
<div id="bar2">
    <div class="navbar2">
        <button onclick="showProducts('all')">All Products</button>
        <button onclick="showProducts('main-dishes')">Main Dishes</button>
        <button onclick="showProducts('side-dishes')">Side Dishes</button>
        <button onclick="showProducts('appetizers')">Appetizers</button>
        <button onclick="showProducts('salads')">Salads</button>
        <button onclick="showProducts('drinks')">Drinks</button>
        <button onclick="showProducts('desserts')">Desserts</button>
    </div>
</div>
<div id="product-container"></div>
<div id="productModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Select Products</h2>
        <form id="orderForm" method="POST">
            <div id="product-checkboxes">
            </div>
            <input type="hidden" id="signup_id" name="signup_id" value="<?php echo $signup_id; ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
            <label for="location">Order Content:</label>
            <input type="text" id="orderContent" name="orderContent" required>
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments"></textarea>
            <button type="submit" value="Submit">Submit</button>
        </form>
    </div>
</div>



</body>
</html>