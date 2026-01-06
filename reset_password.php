<?php
session_start();
require("db.php");

// ✅ لازم يكون جاي من forgot_password
if (!isset($_SESSION['reset_phone']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$phone = $_SESSION['reset_phone'];
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $new_pass = trim($_POST['new_password']);

    if (!empty($new_pass)) {

        $hashed = md5($new_pass);

        $stmt = $con->prepare("UPDATE signup SET password = ? WHERE phone_number = ? AND email = ?");
        $stmt->bind_param("sss", $hashed, $phone, $email);

        if ($stmt->execute()) {

            unset($_SESSION['reset_phone']);
            unset($_SESSION['reset_email']);

            echo "<script>alert('Password updated successfully'); window.location='login.php';</script>";
            exit();
        }

        echo "<script>alert('Error updating password');</script>";
    } else {
        echo "<script>alert('Enter a valid password');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="./styles/login.css">
</head>
<body>

<div class="login">
    <img src="images/GoldenPlates.jpg" class="login__img">

    <form method="POST" class="container">
        <h1 class="login__title">Reset Password</h1>

        <div class="login__content">

            <div class="login__box">
                <i class="ri-lock-2-line login__icon"></i>
                <div class="login__box-input">
                    <input type="password" required class="login__input" name="new_password" placeholder=" ">
                    <label class="login__label">New Password</label>
                </div>
            </div>

        </div>

        <button type="submit" class="login__button">Update Password</button>

        <p class="login__register">
            Back to <a href="login.php">Login</a>
        </p>
    </form>
</div>

</body>
</html>
