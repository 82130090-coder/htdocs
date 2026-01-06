<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

echo "<h2>Fixing 'orders' Table Schema</h2>";

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connection successful.<br>";
}

// Alter table to modify signup_id column
$sql = "ALTER TABLE orders MODIFY signup_id INT NULL";

if (mysqli_query($con, $sql)) {
    echo "Table 'orders' modified successfully. 'signup_id' now allows NULL.<br>";
} else {
    echo "Error modifying table: " . mysqli_error($con) . "<br>";
}

// Verify formatting
$sql_struct = "DESCRIBE orders";
$result = mysqli_query($con, $sql_struct);
if($result) {
    echo "<h3>Updated Table Structure:</h3><ul>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . $row['Field'] . " - " . $row['Type'] . " (Null: " . $row['Null'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "Could not describe table: " . mysqli_error($con);
}
?>
