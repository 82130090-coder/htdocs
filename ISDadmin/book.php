<?php
session_start();
include 'db.php';
if ($con) {
    $signup_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id= $_POST['signup_id'];
        $name = $_POST['name'];
        $phone = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $number = $_POST['numberOfPersons'];
        $date = $_POST['date'];
        $sql = "INSERT INTO booktable(signup_id,name, phoneNumber, email, numberOfPersons,date ) VALUES ('$user_id','$name', '$phone', '$email', '$number', '$date')";
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
    <link rel="stylesheet" href="styles/book.css">
    <script src="script/book.js" defer></script>
</head>
<body>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book A Table</title>
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
            <a href="tableReservations.php">Table Reservations</a>
        </div>
    </div>
    <div class="container">
        <div class="form-container">
            <h2>Book A Table</h2>
            <form method="POST">
                <input type="hidden" id="signup_id" name="signup_id" value="<?php echo $signup_id; ?>">
                <input type="text" name="name"  placeholder="Your Name" required>
                <input type="tel" name="phoneNumber" placeholder="Phone Number" required>
                <input type="email" name="email"  placeholder="Your Email" required>
                <select name="numberOfPersons" required>
                    <option value="" disabled selected>How many persons?</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
                <input type="datetime-local" name="date" id="bookingTime" required>
                <button type="submit" value="Submit">Book Now</button>
            </form>
        </div>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345098975!2d144.9537353153163!3d-37.81627997975142!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11f6d1%3A0x5045675218ceed30!2sYour+Location!5e0!3m2!1sen!2sau!4v1615975352276!5m2!1sen!2sau" 
                width="600" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
    <script>
        document.getElementById('bookingTime').addEventListener('change', function() {
            const selectedTime = new Date(this.value);
            const startTime = new Date(selectedTime);
            const endTime = new Date(selectedTime);
            startTime.setHours(10, 0, 0); // 10 AM
            endTime.setHours(22, 0, 0); // 10 PM

            if (selectedTime < startTime || selectedTime > endTime) {
                alert("Please select a time between 10 AM and 10 PM.");
                this.value = ""; // Clear the input
            }
        });
    </script>
</body>
</html>

</body>
</html>