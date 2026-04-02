<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$sql = "SELECT * FROM food_donations WHERE status='available'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Available Food</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Available Food</h2>

    <?php

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            echo "<div style='border:1px solid black;padding:10px;margin:10px;'>";

            echo "Food: " . $row['food_name'] . "<br>";
            echo "Quantity: " . $row['quantity'] . "<br>";

            echo "<br>";
            echo "<a href='request_food.php?food_id=" . $row['id'] . "'>Request Food</a>";
            echo "</div>";
        }
    } else {
        echo "No food available";
    }

    ?>

</body>

</html>