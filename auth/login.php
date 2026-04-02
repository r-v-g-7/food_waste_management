<?php
session_start();
include("../config/db.php");

$error = "";

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['name']    = $user['name'];

            if ($user['role'] == "admin")         header("Location: /food_waste_project/admin/dashboard.php");
            elseif ($user['role'] == "donor")     header("Location: /food_waste_project/donor/donate_food.php");
            else                                  header("Location: /food_waste_project/receiver/view_food.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — FoodShare</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <nav>
        <a href="/food_waste_project/index.php" class="nav-brand">FoodShare</a>
        <div class="nav-links">
            <a href="/food_waste_project/auth/register.php">Register</a>
        </div>
    </nav>

    <div class="page-wrapper" style="display:flex;align-items:center;min-height:calc(100vh - 60px);">
        <div class="form-card" style="width:100%;">
            <h2>Welcome back</h2>
            <p class="subtitle">Sign in to your FoodShare account</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email address</label>
                    <input type="email" name="email" placeholder="you@example.com" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-full" style="margin-top:0.5rem;">
                    Sign In
                </button>
            </form>

            <div class="form-footer">
                Don't have an account? <a href="/food_waste_project/auth/register.php">Register here</a>
            </div>
        </div>
    </div>

</body>

</html>