<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 4) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_bookings.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $con->prepare("SELECT * FROM booktable WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header("Location: admin_bookings.php?error=notfound");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = $_POST['name'];
    $phone  = $_POST['phoneNumber'];
    $email  = $_POST['email'];
    $people = $_POST['numberOfPersons'];
    $table  = $_POST['tableNumber'];
    $date   = $_POST['date'];
    $reason = $_POST['reason'];

    $update = $con->prepare("
        UPDATE booktable 
        SET name=?, phoneNumber=?, email=?, numberOfPersons=?, tableNumber=?, date=?, reason=?
        WHERE id=?
    ");
    $update->bind_param("sssisssi", $name, $phone, $email, $people, $table, $date, $reason, $id);

    if ($update->execute()) {
        // ✅ رجوع مباشر مع رسالة نجاح
        header("Location: admin_bookings.php?updated=1");
        exit();
    } else {
        header("Location: admin_bookings.php?updated=0");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <style>
        body { margin: 0; font-family: Arial; background: #111; color: white; }
        #content { padding: 20px; margin-left: 260px; }
        input, textarea { width: 100%; padding: 10px; margin-bottom: 15px; }
        button { padding: 10px 15px; background: #FFD700; border: none; cursor: pointer; }
        button:hover { background: #ffeb3b; }
    </style>
</head>
<body>

<div id="content">
    <h1>Edit Booking #<?php echo $booking['id']; ?></h1>

    <form method="POST">

        <label>Name:</label>
        <input type="text" name="name" value="<?php echo $booking['name']; ?>" required>

        <label>Phone:</label>
        <input type="text" name="phoneNumber" value="<?php echo $booking['phoneNumber']; ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $booking['email']; ?>">

        <label>People:</label>
        <input type="number" name="numberOfPersons" value="<?php echo $booking['numberOfPersons']; ?>" required>

        <label>Table Number:</label>
        <input type="number" name="tableNumber" value="<?php echo $booking['tableNumber']; ?>">

        <label>Date & Time:</label>
        <input type="datetime-local" name="date" 
               value="<?php echo date('Y-m-d\TH:i', strtotime($booking['date'])); ?>" required>

        <label>Reason / Comments:</label>
        <textarea name="reason"><?php echo $booking['reason']; ?></textarea>

        <button type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
