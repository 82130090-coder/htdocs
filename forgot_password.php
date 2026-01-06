<?php
session_start();
require("db.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // Validate Lebanese phone
    $valid_phone = preg_match('/^(76|81|03|01|70|71)[0-9]{6}$/', $phone);

    if (!empty($phone) && !empty($email) && $valid_phone) {

        $stmt = $con->prepare("SELECT id FROM signup WHERE phone_number = ? AND email = ? LIMIT 1");
        $stmt->bind_param("ss", $phone, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {

            $_SESSION['reset_phone'] = $phone;
            $_SESSION['reset_email'] = $email;

            header("Location: reset_password.php");
            exit();
        }

        echo "<script>alert('Phone or email not found');</script>";
    } else {
        echo "<script>alert('Enter valid phone and email');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="./styles/login.css">
</head>
<body>

<div class="login">
    <img src="images/GoldenPlates.jpg" class="login__img">

    <form method="POST" class="container">
        <h1 class="login__title">Forgot Password</h1>

        <div class="login__content">

            <div class="login__box">
                <i class="ri-phone-line login__icon"></i>
                <div class="login__box-input">
                    <input type="tel" required class="login__input" name="phone" placeholder=" ">
                    <label class="login__label">Phone Number</label>
                </div>
            </div>

            <div class="login__box">
                <i class="ri-mail-line login__icon"></i>
                <div class="login__box-input">
                    <input type="email" required class="login__input" name="email" placeholder=" ">
                    <label class="login__label">Email</label>
                </div>
            </div>

        </div>

        <button type="submit" class="login__button">Verify</button>

        <p class="login__register">
            Back to <a href="login.php">Login</a>
        </p>
    </form>
</div>

</body>
</html>
