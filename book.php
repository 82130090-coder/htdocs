<?php
    ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$signup_id = $isLoggedIn ? $_SESSION['user_id'] : null;

$name = "";
$phone = "";
$message = ""; // ✅ message box

// ✅ Fetch user info if logged in
if ($isLoggedIn) {
    $stmt = $con->prepare("SELECT username, phone_number FROM signup WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $signup_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user  = $result->fetch_assoc();
        $name  = $user['username'];
        $phone = $user['phone_number'];
    }
}

// ✅ Handle booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!$isLoggedIn) {
        $message = "You must log in before booking a table.";
    } else {

        // ✅ Force server-side values from session (ignore POST for name/phone)
        $user_id = $signup_id;
        $name    = $name;   // from session fetch above
        $phone   = $phone;  // from session fetch above

        $number  = $_POST['numberOfPersons'];
        $table   = $_POST['tableNumber'];
        $reason  = $_POST['reason'] ?? "";

        // ✅ Convert datetime-local → DATETIME
        $dateInput = $_POST['date'];

        if (!$dateInput || strpos($dateInput, 'T') === false) {
            $message = "Please select a valid date and time.";
        } else {

            $date = str_replace("T", " ", $dateInput) . ":00";
            $timestamp = strtotime($date);

            if (!$timestamp) {
                $message = "Invalid date format.";
            } else {

                $date = date('Y-m-d H:i:s', $timestamp);
                $now = date('Y-m-d H:i:s');

                // ✅ Prevent booking in the past
                if ($date <= $now) {
                    $message = "You cannot book a table in the past.";
                }

                // ✅ Prevent booking more than 7 days ahead
                elseif ($date > date('Y-m-d H:i:s', strtotime('+7 days'))) {
                    $message = "You can only book within one week from today.";
                }

                else {
                    // ✅ BLACKLIST CHECK
                    $phoneEscaped = mysqli_real_escape_string($con, $phone);
                    $checkBlacklist = $con->query("SELECT id FROM blacklist WHERE phone = '$phoneEscaped' LIMIT 1");

                    if ($checkBlacklist && $checkBlacklist->num_rows > 0) {
                        $message = "You are blacklisted and cannot book a table.";
                    } else {

                        // ✅ Prevent booking same table on same day
                        $dayOnly = date('Y-m-d', strtotime($date));

                        $stmt = $con->prepare("SELECT id FROM booktable WHERE tableNumber = ? AND DATE(date) = ? LIMIT 1");
                        $stmt->bind_param("is", $table, $dayOnly);
                        $stmt->execute();
                        $check = $stmt->get_result();

                        if ($check->num_rows > 0) {
                            $message = "This table is already booked for this day.";
                        } else {

                            // ✅ Insert booking
                            $stmt = $con->prepare("INSERT INTO booktable (signup_id, name, phoneNumber, numberOfPersons, tableNumber, date, reason) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ississs", $user_id, $name, $phone, $number, $table, $date, $reason);

                            if ($stmt->execute()) {
                                $message = "✅ Your table has been successfully booked!";
                            } else {
                                $message = "Error: Could not complete booking.";
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book A Table</title>
    <link rel="stylesheet" href="styles/book.css">
</head>
<body>

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
        <?php endif; ?>
    </div>
</div>

<!-- ✅ Welcome message -->
<?php if ($isLoggedIn): ?>
<div class="welcome-banner">
    Welcome <span><?php echo htmlspecialchars($name); ?></span>
</div>
<?php endif; ?>

<div class="container">
    <div class="form-container">
        <h2>Book A Table</h2>

        <?php if (!empty($message)): ?>
            <div class="msg-box <?php echo (strpos($message, '✅') !== false) ? 'success' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="signup_id" value="<?php echo $signup_id; ?>">

           

            <select name="numberOfPersons" required>
                <option value="" disabled selected>How many persons?</option>
                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <select name="tableNumber" required>
                <option value="" disabled selected>Select Table Number</option>
                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <option value="<?php echo $i; ?>">Table <?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <input type="datetime-local" name="date" id="bookingTime" required>

            <textarea name="reason" placeholder="Reason for booking (optional)"></textarea>

            <button type="submit">Book Now</button>
        </form>
    </div>

    <div class="map-container">
        <iframe 
            src="https://maps.google.com/maps?q=Golden+Plates+Restaurant+Bahrain&t=&z=13&ie=UTF8&iwloc=&output=embed"
            width="600" height="450" style="border:0;" allowfullscreen loading="lazy">
        </iframe>
    </div>
</div>

<script>
// Set min date = now (local)
const bookingInput = document.getElementById('bookingTime');
const now = new Date();
now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
bookingInput.min = now.toISOString().slice(0, 16);

// Set max date = +7 days
const maxDate = new Date();
maxDate.setDate(maxDate.getDate() + 7);
maxDate.setMinutes(maxDate.getMinutes() - now.getTimezoneOffset());
bookingInput.max = maxDate.toISOString().slice(0, 16);

// Optional: keep time range 10 AM to 10 PM
bookingInput.addEventListener('change', function () {
    const selected = new Date(this.value);

    const start = new Date(selected);
    const end = new Date(selected);
    start.setHours(10, 0, 0);
    end.setHours(22, 0, 0);

    if (selected < start || selected > end) {
        alert("Please select a time between 10 AM and 10 PM.");
        this.value = "";
    }
});
</script>

</body>
</html>
