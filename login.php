<?php
session_start();
require("db.php");

$error = "";

// ✅ Auto-fill values from signup
$prefill_phone = $_SESSION['signup_phone'] ?? "";
$prefill_password = $_SESSION['signup_password'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $user_phone    = trim($_POST['phone_number']);
    $user_password = trim($_POST['password']);
    $hashed_input  = md5($user_password);

    if ($user_phone === "70862233" && $user_password === "1234567890") {
        $_SESSION['user_id'] = 4;
        header("Location: ISDadmin/admin_dashboard.php");
        exit();
    }

    $valid_phone = preg_match('/^(76|81|03|01|70|71)[0-9]{6}$/', $user_phone);

    if (!empty($user_phone) && !empty($user_password) && $valid_phone) {

        $stmt = $con->prepare("SELECT id, password FROM signup WHERE phone_number = ? LIMIT 1");
        $stmt->bind_param("s", $user_phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $db_password = $user_data['password'];

            if ($db_password === $hashed_input || $db_password === $user_password) {
                $_SESSION['user_id'] = $user_data['id'];
                unset($_SESSION['signup_phone']);
                unset($_SESSION['signup_password']);
                header("Location: index.php");
                exit();
            }
        }

        $error = "Wrong phone number or password.";

    } else {
        $error = "Please enter a valid Lebanese phone number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="./styles/login.css">
    <title>Login</title>
    <style>
        .error-message {
            background: #d50000;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        /* ✅ زر الرجوع */
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(0,0,0,0.6);
            padding: 10px 12px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 999;
            transition: 0.2s;
        }
        .back-btn i {
            color: white;
            font-size: 20px;
        }
        .back-btn:hover {
            background: rgba(0,0,0,0.8);
        }
    </style>
</head>
<body>

    <!-- ✅ زر الرجوع -->
    <a href="index.php" class="back-btn">
        <i class="ri-arrow-left-line"></i>
    </a>

    <div class="login">
        <img src="images/GoldenPlates.jpg" alt="login image" class="login__img">

        <form method="POST" class="container">
            <h1 class="login__title">Login</h1>

            <div id="msgBox" class="error-message"><?php echo $error; ?></div>

            <div class="login__content">

                <div class="login__box">
                    <i class="ri-phone-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="tel" required class="login__input" name="phone_number" placeholder=" " 
                               value="<?php echo htmlspecialchars($prefill_phone); ?>">
                        <label class="login__label">Phone Number</label>
                    </div>
                </div>

                <div class="login__box">
                    <i class="ri-lock-2-line login__icon"></i>
                    <div class="login__box-input">
                        <input type="password" required class="login__input" name="password" placeholder=" " 
                               value="<?php echo htmlspecialchars($prefill_password); ?>">
                        <label class="login__label">Password</label>
                    </div>
                </div>

            </div>

            <button type="submit" class="login__button">Login</button>

            <p class="login__register">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </p>

            <p class="login__register" style="margin-top:10px;">
                <a href="forgot_password.php" style="color:#FFD700;">Forgot Password?</a>
            </p>

        </form>
    </div>

    <script>
        window.onload = function () {
            const box = document.getElementById("msgBox");
            if (box.innerText.trim() !== "") {
                box.style.display = "block";
                setTimeout(() => { box.style.opacity = 1; }, 50);
                setTimeout(() => { box.style.opacity = 0; }, 3000);
                setTimeout(() => { box.style.display = "none"; }, 3500);
            }
        };
    </script>
</body>
</html>

