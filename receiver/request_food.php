<?php
session_start();
require_once("../config/db.php");

$receiver = $_SESSION['user_id'];
$food_id = $_GET['food_id'];

$sql = "INSERT INTO requests(food_id,receiver_id)
VALUES('$food_id','$receiver')";

$conn->query($sql);

$conn->query("UPDATE food_donations SET status='requested' WHERE id='$food_id'");

echo "Food request sent successfully";
