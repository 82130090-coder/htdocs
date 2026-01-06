<?php
session_start(); 
$isLoggedIn = isset($_SESSION['user_id']); 
$username = "";

// If logged in → fetch username
if ($isLoggedIn) {
    require("db.php");
    $uid = $_SESSION['user_id'];

    $stmt = $con->prepare("SELECT username FROM signup WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['username'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Plates</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<!-- ✅ Navbar -->
<div id="bar">
    <div class="logo-div">
        <img src="images/logo.jpg" alt="Logo">
    </div>

    <div class="navbar">
        <a href="index.php">Home</a>
       
        <a href="menu.php">Menu</a>
        <a href="book.php">Book Table</a>

        <?php if (!$isLoggedIn): ?>
            <a href="login.php" class="login-btn">Log In</a>
        <?php else: ?>
            <a href="logout.php" class="login-btn">Welcome <?php echo htmlspecialchars($username); ?></a>
        <?php endif; ?>
    </div>
</div>

<!-- ✅ Home Section -->
<div id="home-content">
    <div id="p1">
        <h1>Welcome to <span style="color: #ffbe33">golden plates</span> restaurant</h1>
        <h5>Your satisfaction is our top priority</h5>
        <a href="menu.php">
            <button class='glowing-btn'>
                <span class='glowing-txt'>O<span class='faulty-letter'>rder</span> Now</span>
            </button>
        </a>
    </div>

    <div id="GoldenPlate">
        <img src="images/golden plate.jpg" alt="Golden Plate">
    </div>
</div>

<!-- ✅ Carousel (صور مباشرة بالـ <img>) -->
<div class="carousel">
    <ul class="carousel-item-wrapper">
        <li class="carousel-item"><img src="images/1.jpg" alt="Example 1"></li>
        <li class="carousel-item"><img src="images/2.jpg" alt="Example 2"></li>
        <li class="carousel-item"><img src="images/3.jpg" alt="Example 3"></li>
        <li class="carousel-item"><img src="images/4.jpg" alt="Example 4"></li>
        <li class="carousel-item"><img src="images/5.jpg" alt="Example 5"></li>
        <li class="carousel-item"><img src="images/6.jpg" alt="Example 6"></li>
        <li class="carousel-item"><img src="images/7.jpg" alt="Example 7"></li>
        
        
    </ul>
</div>

<!-- ✅ About Section -->
<div class="About-container">
    <div class="About-us">
        <h2>About Us</h2>
        <p class="About-paragraph">
            Golden Plates is a unique dining spot that offers a diverse menu combining the best of Western and Eastern cuisines, alongside an array of fresh juices and delectable sweets. With an emphasis on delivering a rich and varied dining experience, Golden Plates caters to customers seeking everything from classic Western dishes like pasta, steaks, and burgers to Eastern delicacies such as sushi, dumplings, and flavorful curries.<br><br>
            The restaurant’s juice bar serves freshly squeezed and blended juices made from seasonal fruits, perfect for a refreshing accompaniment to any meal. For dessert lovers, Golden Plates offers a selection of sweets, featuring traditional treats from both culinary worlds, as well as contemporary favorites like cakes, pastries, and exotic fruit desserts.
        </p>
    </div>

    <div class="About-contact">
        <footer class="footer_section">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 footer-col">
                        <div class="footer_contact">
                            <h2>Contact Us</h2>
                            <div class="contact_link_box">
                                 <a href="https://www.google.com/maps?q=baalbeck" target="_blank">
        <i class="fa fa-map-marker"></i><span> baalbeck</span>
    </a>

    <!-- Phone: يفتح تطبيق الاتصال -->
    <a href="tel:+9613825234">
        <i class="fa fa-phone"></i><span> Call +961 3 825 234</span>
    </a>

    <!-- Email: يفتح برنامج البريد -->
  <a href="https://mail.google.com/mail/?view=cm&fs=1&to=hadiyaghi2004@gmail.com" target="_blank">
    <i class="fa fa-envelope"></i><span> hadiyaghi2004@gmail.com</span>
</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 footer-col">
                        <div class="footer_detail">
                            <div class="footer_social">
                                
                          <a href="https://www.instagram.com/hadi__yaghi?igsh=MTA2dWw3dXlqcXRsaw%3D%3D&utm_source=qr"><i class="fa fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 footer-col"><hr>
                        <h2>Opening Hours</h2>
                        <p>Everyday</p>
                        <p>10.00 Am - 10.00 Pm</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

</body>
</html>
