<?php
include("db.php");

echo "Checking for duplicate emails in 'signup' table...\n";
$query = "SELECT email, COUNT(*) as count FROM signup GROUP BY email HAVING count > 1";
$result = mysqli_query($con, $query);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "Found duplicates:\n";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "Email: " . $row['email'] . " - Count: " . $row['count'] . "\n";
            
            // Show details for these duplicates
            $email = $row['email'];
            $detail_query = "SELECT id, username, password FROM signup WHERE email = '$email'";
            $detail_result = mysqli_query($con, $detail_query);
            while($detail = mysqli_fetch_assoc($detail_result)) {
                 echo "    ID: " . $detail['id'] . ", Username: " . $detail['username'] . ", Password: " . $detail['password'] . "\n";
            }
        }
    } else {
        echo "No duplicate emails found.\n";
    }
} else {
    echo "Query failed: " . mysqli_error($con) . "\n";
}

echo "\nChecking table structure:\n";
$structure_query = "SHOW CREATE TABLE signup";
$structure_result = mysqli_query($con, $structure_query);
if ($structure_result) {
    $row = mysqli_fetch_assoc($structure_result);
    echo $row['Create Table'];
}
?>
