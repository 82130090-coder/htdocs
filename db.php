<?php
$host = "sql113.infinityfree.com";
$user = "if0_40812760";
$password = "hadiyaghi123";
$database = "if0_40812760_restaurant";

$con = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
