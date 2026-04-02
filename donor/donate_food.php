<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: /food_waste_project/auth/login.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id = $_SESSION['user_id'];
    $food     = trim($_POST['food_name']);
    $qty      = trim($_POST['quantity']);
    $loc      = trim($_POST['location']);
    $expiry   = trim($_POST['expiry']);

    if (empty($food) || empty($qty) || empty($loc) || empty($expiry)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($qty) || $qty <= 0) {
        $error = "Quantity must be a positive number.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO food_donations (donor_id, food_name, quantity, location, expiry_time, status) VALUES (?, ?, ?, ?, ?, 'available')"
        );
        $stmt->bind_param("issss", $donor_id, $food, $qty, $loc, $expiry);
        if ($stmt->execute()) {
            $success = "Your food donation has been listed successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}

// Fetch this donor's past donations
$my_donations = $conn->prepare("SELECT * FROM food_donations WHERE donor_id = ? ORDER BY id DESC");
$my_donations->bind_param("i", $_SESSION['user_id']);
$my_donations->execute();
$past = $my_donations->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Food — FoodShare</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <nav>
        <a href="/food_waste_project/index.php" class="nav-brand">FoodShare</a>
        <div class="nav-links">
            <a href="/food_waste_project/donor/donate_food.php" class="active">Donate Food</a>
            <a href="/food_waste_project/auth/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>Donate Food</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Donor'); ?>! List your surplus food below.</p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start;">

            <div class="card">
                <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Food Name</label>
                        <input type="text" name="food_name" placeholder="e.g. Rice, Bread, Vegetables"
                            value="<?php echo htmlspecialchars($_POST['food_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Quantity (servings / kg / units)</label>
                        <input type="number" name="quantity" min="1" placeholder="e.g. 10"
                            value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Pickup Location</label>
                        <input type="text" name="location" placeholder="e.g. Sector 12, Delhi"
                            value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Expiry / Best Before</label>
                        <input type="datetime-local" name="expiry"
                            value="<?php echo htmlspecialchars($_POST['expiry'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">List Donation</button>
                </form>
            </div>

            <div>
                <h3 style="font-family:'Fraunces',serif;font-size:1.1rem;margin-bottom:1rem;">Your Past Donations</h3>
                <?php if ($past->num_rows > 0): ?>
                    <div style="display:flex;flex-direction:column;gap:0.75rem;">
                        <?php while ($row = $past->fetch_assoc()): ?>
                            <div class="card" style="padding:1rem;">
                                <div style="display:flex;justify-content:space-between;align-items:start;">
                                    <strong><?php echo htmlspecialchars($row['food_name']); ?></strong>
                                    <span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
                                </div>
                                <div class="meta" style="margin-top:6px;font-size:13px;color:#6b7280;">
                                    <span>Qty: <?php echo htmlspecialchars($row['quantity']); ?></span>
                                    <span><?php echo htmlspecialchars($row['location']); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="padding:2rem;">
                        <div class="empty-icon">📦</div>
                        <p>No donations yet. Your listings will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>

</html>