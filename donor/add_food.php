<?php
include("../config/db.php");

if (isset($_POST['submit'])) {
    $food_name = $_POST['food_name'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO food_donations(food_name, quantity, status) 
            VALUES('$food_name','$quantity','available')";

    if ($conn->query($sql)) {
        echo "Food added successfully";
    }
}
?>

<h2>Add Food Donation</h2>

<form method="POST">
    Food Name:<br>
    <input type="text" name="food_name"><br><br>

    Quantity:<br>
    <input type="text" name="quantity"><br><br>

    <input type="submit" name="submit" value="Add Food">
</form>