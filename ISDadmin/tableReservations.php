<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./styles/tableReservations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="./script/book.js"></script>
</head>
<body>
    <div id="bar">
        <div class="logo-div">
            <img src="images/logo.jpg" alt="Logo">
        </div>
        <div class="navbar">
            <a href="product.php">Add Product</a>
            <a href="orders.php">Orders</a>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="menu.php">Menu</a>
            <a href="book.php">Book Table</a>
            <a href="tableReservations.php">Table Reservations</a>
        </div>
    </div>
    
    <table>
        <tr>
            <th>id</th>
            <th>name</th>
            <th>phone number</th>
            <th>email</th>
            <th>number of persons</th>
            <th>date</th>
            <th>actions</th>
        </tr>
        <?php
include 'db.php';

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM booktable WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT id, name, phoneNumber, email, numberOfPersons, date FROM booktable"; 
$result = mysqli_query($con, $sql);

if ($result === false) {
    // Output the error message
    echo "Error: " . mysqli_error($con);
} else {
    // Only call mysqli_num_rows if $result is valid
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $id = $row['id'];
            $name = $row['name'];
            $phone = $row['phoneNumber'];
            $email = $row['email'];
            $number = $row['numberOfPersons'];
            $date = $row['date'];
            echo "<tr>
                      <td>" . htmlspecialchars($id) . "</td>
                      <td>" . htmlspecialchars($name) . "</td>
                      <td>" . htmlspecialchars($phone) . "</td>
                      <td>" . htmlspecialchars($email) . "</td>
                      <td>" . htmlspecialchars($number) . "</td>
                      <td>" . htmlspecialchars($date) . "</td>
                      <td>
                          <form method='POST' action='tableReservations.php' onsubmit='return confirmDelete(" . $row['id'] . ")'>
                              <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . " '>
                              <button type='submit' name='delete' onclick='return confirm(\"Are you sure you want to delete this order?\");'><i class='fas fa-trash'></i></button>
                          </form>
                      </td>  
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No orders found.</td></tr>";
    }
}
?>
    </table>
</body>
</html>