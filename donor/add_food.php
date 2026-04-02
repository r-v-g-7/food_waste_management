<?php
include("../config/db.php");

if (isset($_POST['submit'])) {

    $food_name = trim($_POST['food_name']);
    $quantity = trim($_POST['quantity']);

    if (!empty($food_name) && !empty($quantity)) {

        $sql = "INSERT INTO food_donations (food_name, quantity, status) 
                VALUES ('$food_name', '$quantity', 'available')";

        if ($conn->query($sql)) {
            echo "Food added successfully";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Please fill all fields";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Food</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<?php
include("../config/db.php");

if (isset($_POST['submit'])) {

    $food_name = trim($_POST['food_name']);
    $quantity = trim($_POST['quantity']);

    if (!empty($food_name) && !empty($quantity)) {

        // FIXED spelling here
        $sql = "INSERT INTO food_donations (food_name, quantity, status) 
                VALUES ('$food_name', '$quantity', 'available')";

        if ($conn->query($sql)) {
            echo "Food added successfully";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Please fill all fields";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Food</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Add Food Donation</h2>

    <form method="POST">
        Food Name:<br>
        <input type="text" name="food_name" required><br><br>

        Quantity:<br>
        <input type="text" name="quantity" required><br><br>

        <input type="submit" name="submit" value="Add Food">
    </form>

</body>

</html>

</html>