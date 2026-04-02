<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receiver') {
    header("Location: /food_waste_project/auth/login.php");
    exit();
}

$sql    = "SELECT * FROM food_donations WHERE status = 'available' ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Food — FoodShare</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <nav>
        <a href="/food_waste_project/index.php" class="nav-brand">FoodShare</a>
        <div class="nav-links">
            <a href="/food_waste_project/receiver/view_food.php" class="active">Browse Food</a>
            <a href="/food_waste_project/auth/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>Available Food</h1>
            <p>Hello, <?php echo htmlspecialchars($_SESSION['name'] ?? 'there'); ?>! Browse and request food donations below.</p>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="card-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="food-card">
                        <h3><?php echo htmlspecialchars($row['food_name']); ?></h3>
                        <div class="meta">
                            <span>🍽️ Qty: <?php echo htmlspecialchars($row['quantity']); ?></span>
                            <span>📍 <?php echo htmlspecialchars($row['location']); ?></span>
                            <span>⏰ Expires: <?php echo htmlspecialchars($row['expiry_time']); ?></span>
                        </div>
                        <a href="/food_waste_project/receiver/request_food.php?food_id=<?php echo intval($row['id']); ?>"
                            class="btn btn-primary btn-sm btn-full"
                            onclick="return confirm('Request this food donation?')">
                            Request Food
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🍽️</div>
                <p>No food available right now. Check back soon!</p>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>