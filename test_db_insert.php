<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

echo "<h2>Testing DB Connection and Insert</h2>";

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connection successful.<br>";
}

// Try inserting a guest order (signup_id = NULL)
$sql = "INSERT INTO orders(username, phone, location, orderContent, comments, signup_id) VALUES ('TestUser', '123456789', 'Test Location', 'Test Product (x1)', 'Test Comment', NULL)";

if (mysqli_query($con, $sql)) {
    echo "Guest order inserted successfully.<br>";
} else {
    echo "Error inserting guest order: " . mysqli_error($con) . "<br>";
}

// Fetch structure to see columns
$sql_struct = "DESCRIBE orders";
$result = mysqli_query($con, $sql_struct);
if($result) {
    echo "<h3>Table Structure:</h3><ul>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['Field'] . " - " . $row['Type'] . " (Null: " . $row['Null'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "Could not describe table: " . mysqli_error($con);
}
?>
