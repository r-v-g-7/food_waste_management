<?php
session_start();
require_once("../config/db.php");

// ✅ Auth + role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receiver') {
    header("Location: /food_waste_project/auth/login.php");
    exit();
}

// ✅ Validate food_id is a real integer
if (!isset($_GET['food_id']) || !is_numeric($_GET['food_id'])) {
    header("Location: /food_waste_project/receiver/view_food.php");
    exit();
}

$receiver_id = $_SESSION['user_id'];
$food_id     = intval($_GET['food_id']);
$error       = "";
$success     = "";

// ✅ Check food actually exists and is still available
$check = $conn->prepare("SELECT id FROM food_donations WHERE id = ? AND status = 'available'");
$check->bind_param("i", $food_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $error = "This food is no longer available.";
} else {

    // ✅ Check for duplicate request
    $dup = $conn->prepare("SELECT id FROM requests WHERE food_id = ? AND receiver_id = ?");
    $dup->bind_param("ii", $food_id, $receiver_id);
    $dup->execute();
    $dup->store_result();

    if ($dup->num_rows > 0) {
        $error = "You have already requested this food.";
    } else {

        // ✅ Prepared statement for insert
        $stmt = $conn->prepare("INSERT INTO requests (food_id, receiver_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $food_id, $receiver_id);

        if ($stmt->execute()) {

            // ✅ Only update status if insert succeeded
            $upd = $conn->prepare("UPDATE food_donations SET status = 'requested' WHERE id = ?");
            $upd->bind_param("i", $food_id);
            $upd->execute();

            $success = "Food request sent successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Request Food</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Request Status</h2>

    <?php if ($error)   echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <a href="view_food.php">Back to Food List</a>

</body>

</html>