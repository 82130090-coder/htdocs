<?php
session_start();
require("db.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = $_POST['username'];
    $user_phone = $_POST['phone_number'];
    $user_email = $_POST['email'];
    $user_password = $_POST['password'];
    if (!empty($user_email) && !empty($user_password) && !is_numeric($user_email)) {
        $query = "INSERT INTO signup (username, phone_number, email, password) VALUES ('$user_name', '$user_phone', '$user_email', '$user_password')";
        if (!mysqli_query($con, $query)) {
            echo "Error: " . mysqli_error($con);
        } else {
            echo "<script type='text/javascript'> alert('successfully registered'); </script>";
            header ("location: login.php");
        }
    } else {
        echo "<script type='text/javascript'> alert('Please enter some valid information'); </script>";
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="./styles/signup.css">
    <title>Animated login form</title>  
</head>
<body>
    <div class="signup">
        <img src="images/GoldenPlates.jpg" alt="signup image" class="signup__img">
        <form method="POST" class="container" >
            <h1 class="signup__title">Sign Up</h1>
            <div class="signup__content">
                <div class="signup__box">
                    <i class="ri-user-3-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="text" required class="signup__input" name="username" id="signup-username" placeholder=" " title="enter username">
                        <label for="signup-username" class="signup__label">Username</label>
                    </div>
                </div>
                <div class="signup__box">
                    <i class="ri-phone-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="tel" required class="signup__input" name="phone_number" id="signup-phone" placeholder=" " title="enter phone number">
                        <label for="signup-phone" class="signup__label">Phone Number</label>
                    </div>
                </div>
                <div class="signup__box">
                    <i class="ri-mail-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="email" required class="signup__input" id="signup-email" name="email" placeholder=" " title="enter email">
                        <label for="signup-email" class="signup__label">Email</label>
                    </div>
                </div>
                <div class="signup__box">
                    <i class="ri-lock-2-line signup__icon"></i>
                    <div class="signup__box-input">
                    <input type="password" required class="signup__input" id="signup-pass" name="password" placeholder=" " title="Enter your password">
                        <label for="signup-pass" class="signup__label">Password</label>
                        
                    </div>
                </div>
            </div>
            <input type="submit" class="signup__button" value="Submit">
            <p class="signup__register">
                Already have an account? <a href="login.php">Register</a>
             </p>
        </form>
        
    </div>


</body>
</html>