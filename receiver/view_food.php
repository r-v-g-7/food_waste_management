<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receiver') {
    header("Location: ../auth/login.php");
    exit();
}

$sql = "SELECT * FROM food_donations WHERE status = 'available'";
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

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div style="border:1px solid black; padding:10px; margin:10px;">
                <!-- ✅ htmlspecialchars prevents XSS -->
                Food: <?php echo htmlspecialchars($row['food_name']); ?><br>
                Quantity: <?php echo htmlspecialchars($row['quantity']); ?><br>
                Location: <?php echo htmlspecialchars($row['location']); ?><br>
                Expiry: <?php echo htmlspecialchars($row['expiry_time']); ?><br><br>
                <!-- ✅ intval() ensures only a clean integer in the URL -->
                <a href="request_food.php?food_id=<?php echo intval($row['id']); ?>">Request Food</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No food available at the moment.</p>
    <?php endif; ?>

    <br><a href="../index.php">Back to Home</a>

</body>

</html>