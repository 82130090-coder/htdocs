<?php
session_start();
require("db.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $user_name      = trim($_POST['username']);
    $user_phone     = trim($_POST['phone_number']);
    $user_email     = trim($_POST['email']);
    $user_password  = trim($_POST['password']);
    $confirm_pass   = trim($_POST['confirm_password']);

    $valid_phone = preg_match('/^(76|81|03|01|70|71)[0-9]{6}$/', $user_phone);
    $passwords_match = ($user_password === $confirm_pass);
    $valid_password_length = (strlen($user_password) >= 8);

    if (!empty($user_name) && !empty($user_phone) && !empty($user_email) && 
        !empty($user_password) && !empty($confirm_pass) && 
        $valid_phone && $passwords_match && $valid_password_length) {

        $hashed_password = md5($user_password);

        $check_stmt = $con->prepare("SELECT id FROM signup WHERE email = ? LIMIT 1");
        $check_stmt->bind_param("s", $user_email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $insert_stmt = $con->prepare(
                "INSERT INTO signup (username, phone_number, email, password) VALUES (?, ?, ?, ?)"
            );
            $insert_stmt->bind_param("ssss", $user_name, $user_phone, $user_email, $hashed_password);

            if ($insert_stmt->execute()) {
                // ✅ Save signup info for auto-fill
                $_SESSION['signup_phone'] = $user_phone;
                $_SESSION['signup_password'] = $user_password;

                header("Location: login.php");
                exit();
            } else {
                $error = "Error: Could not complete registration.";
            }
        }

    } else {
        if (!$valid_phone) {
            $error = "Please enter a valid Lebanese phone number.";
        } elseif (!$passwords_match) {
            $error = "Passwords do not match.";
        } elseif (!$valid_password_length) {
            $error = "Password must be at least 8 characters.";
        } else {
            $error = "Please fill all fields correctly.";
        }
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
    <title>Sign Up</title>
    <style>
        .error-message {
            background: #ffdddd;
            color: #a00;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="signup">
        <img src="images/GoldenPlates.jpg" alt="signup image" class="signup__img">
        <form method="POST" class="container">
            <h1 class="signup__title">Sign Up</h1>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="signup__content">

                <div class="signup__box">
                    <i class="ri-user-3-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="text" required class="signup__input" name="username" placeholder=" " value="<?php echo htmlspecialchars($user_name ?? ''); ?>">
                        <label class="signup__label">full name</label>
                    </div>
                </div>

                <div class="signup__box">
                    <i class="ri-phone-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="tel" required class="signup__input" name="phone_number" placeholder=" " value="<?php echo htmlspecialchars($user_phone ?? ''); ?>">
                        <label class="signup__label">Phone Number</label>
                    </div>
                </div>

                <div class="signup__box">
                    <i class="ri-mail-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="email" required class="signup__input" name="email" placeholder=" " value="<?php echo htmlspecialchars($user_email ?? ''); ?>">
                        <label class="signup__label">Email</label>
                    </div>
                </div>

                <div class="signup__box">
                    <i class="ri-lock-2-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="password" required class="signup__input" name="password" placeholder=" ">
                        <label class="signup__label">Password</label>
                    </div>
                </div>

                <div class="signup__box">
                    <i class="ri-lock-password-line signup__icon"></i>
                    <div class="signup__box-input">
                        <input type="password" required class="signup__input" name="confirm_password" placeholder=" ">
                        <label class="signup__label">Confirm Password</label>
                    </div>
                </div>

            </div>

            <input type="submit" class="signup__button" value="Submit">

            <p class="signup__register">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </form>
    </div>
</body>
</html>
