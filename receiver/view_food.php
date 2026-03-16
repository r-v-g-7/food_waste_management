<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$sql = "SELECT * FROM food_donations WHERE status='available'";
$result = $conn->query($sql);

echo "<h2>Available Food</h2>";

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<div style='border:1px solid black;padding:10px;margin:10px;'>";

        echo "Food: " . $row['food_name'] . "<br>";
        echo "Quantity: " . $row['quantity'] . "<br>";
        echo "Location: " . $row['location'] . "<br>";
        echo "Expiry: " . $row['expiry_time'] . "<br>";

        echo "<br>";
        echo "<a href='request_food.php?food_id=" . $row['id'] . "'>Request Food</a>";
        echo "</div>";
    }
} else {
    echo "No food available";
}
