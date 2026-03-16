<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $donor_id = $_SESSION['user_id'];
    $food = $_POST['food_name'];
    $qty = $_POST['quantity'];
    $loc = $_POST['location'];
    $expiry = $_POST['expiry'];

    $sql = "INSERT INTO food_donations (donor_id,food_name,quantity,location,expiry_time)
VALUES ('$donor_id','$food','$qty','$loc','$expiry')";

    if ($conn->query($sql)) {
        echo "Food added successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST">

    Food Name:
    <input type="text" name="food_name"><br><br>

    Quantity:
    <input type="text" name="quantity"><br><br>

    Location:
    <input type="text" name="location"><br><br>

    Expiry Time:
    <input type="datetime-local" name="expiry"><br><br>

    <button type="submit">Donate Food</button>

</form>