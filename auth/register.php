<?php
include("../config/db.php");
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!in_array($role, ['donor', 'receiver'])) {
        $error = "Invalid role selected.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2  = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("ssss", $name, $email, $hashed, $role);
            if ($stmt2->execute()) {
                $success = "Account created! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — FoodShare</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <nav>
        <a href="/food_waste_project/index.php" class="nav-brand">FoodShare</a>
        <div class="nav-links">
            <a href="/food_waste_project/auth/login.php">Login</a>
        </div>
    </nav>

    <div class="page-wrapper" style="display:flex;align-items:center;min-height:calc(100vh - 60px);">
        <div class="form-card" style="width:100%;">
            <h2>Create account</h2>
            <p class="subtitle">Join FoodShare and make a difference today</p>

            <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?> <a href="/food_waste_project/auth/login.php">Login →</a></div><?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full name</label>
                    <input type="text" name="name" placeholder="John Doe" required
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Email address</label>
                    <input type="email" name="email" placeholder="you@example.com" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 6 characters" required>
                </div>
                <div class="form-group">
                    <label>I want to</label>
                    <select name="role">
                        <option value="donor" <?php echo (($_POST['role'] ?? '') == 'donor')    ? 'selected' : ''; ?>>Donate Food</option>
                        <option value="receiver" <?php echo (($_POST['role'] ?? '') == 'receiver') ? 'selected' : ''; ?>>Receive Food</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full" style="margin-top:0.5rem;">
                    Create Account
                </button>
            </form>

            <div class="form-footer">
                Already have an account? <a href="/food_waste_project/auth/login.php">Sign in</a>
            </div>
        </div>
    </div>

</body>

</html>