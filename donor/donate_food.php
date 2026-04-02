<?php
session_start();
require_once("../config/db.php");

// ✅ Auth check — donors only
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

    // ✅ Validation
    if (empty($food) || empty($qty) || empty($loc) || empty($expiry)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($qty) || $qty <= 0) {
        $error = "Quantity must be a positive number.";
    } else {
        // ✅ Prepared statement
        $stmt = $conn->prepare(
            "INSERT INTO food_donations (donor_id, food_name, quantity, location, expiry_time, status)
             VALUES (?, ?, ?, ?, ?, 'available')"
        );
        $stmt->bind_param("issss", $donor_id, $food, $qty, $loc, $expiry);

        if ($stmt->execute()) {
            $success = "Food donated successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Donate Food</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Donate Food</h2>

    <?php if ($error)   echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="POST">

        Food Name:<br>
        <input type="text" name="food_name"
            value="<?php echo htmlspecialchars($_POST['food_name'] ?? ''); ?>" required><br><br>

        Quantity:<br>
        <input type="number" name="quantity" min="1"
            value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>" required><br><br>

        Location:<br>
        <input type="text" name="location"
            value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required><br><br>

        Expiry Time:<br>
        <input type="datetime-local" name="expiry"
            value="<?php echo htmlspecialchars($_POST['expiry'] ?? ''); ?>" required><br><br>

        <button type="submit">Donate Food</button>

    </form>

    <br><a href="../index.php">Back to Home</a>

</body>

</html>