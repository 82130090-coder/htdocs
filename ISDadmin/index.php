<?php
session_start(); 
$isLoggedIn = isset($_SESSION['user_id']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>
<div id="bar">
    <div class="logo-div">
        <img src="images/logo.jpg" alt="Logo">
    </div>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="menu.php">Menu</a>
        <a href="book.php">Book Table</a>
        <?php if (!$isLoggedIn): ?>
            <a href="login.php" class="login-btn">Log In</a>
        <?php endif; ?>
    </div>
</div>
    <div id="home-content">
      <div id="p1">
        <h1>Welcome to <span style="color: #ffbe33">golden plates</span> restaurant</h1>
        <h5>Your satisfaction is our top priority</h5>
        <a href="menu.php"><button class='glowing-btn'><span class='glowing-txt'>O<span class='faulty-letter'>rder</span> Now</span></button></a>
      </div>
      <div id="GoldenPlate">
        <img src="images/golden plate.jpg">
      </div>
    </div>
 <?php else: ?>
  <div id="bar">
    <div class="logo-div">
        <img src="images/logo.jpg" alt="Logo">
    </div>
    <div class="navbar">
        <a href="login.php">Home</a>
        <a href="login.php">About</a>
        <a href="login.php">Menu</a>
        <a href="login.php">Book Table</a>
        <?php if (!$isLoggedIn): ?>
            <a href="login.php" class="login-btn">Log In</a>
        <?php endif; ?>
    </div>
</div>
    <div id="home-content">
      <div id="p1">
        <h1>Welcome to <span style="color: #ffbe33">golden plates</span> restaurant</h1>
        <h5>Your satisfaction is our top priority</h5>
        <a href="login.php"><button class='glowing-btn'><span class='glowing-txt'>O<span class='faulty-letter'>rder</span> Now</span></button></a>
      </div>
      <div id="GoldenPlate">
        <img src="images/golden plate.jpg">
      </div>
    </div>
<?php endif; ?>
</body>
</html>
