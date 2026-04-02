<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare — Donate & Receive Food</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <nav>
        <a href="/food_waste_project/index.php" class="nav-brand">FoodShare</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'donor'): ?>
                    <a href="/food_waste_project/donor/donate_food.php">Donate Food</a>
                <?php elseif ($_SESSION['role'] === 'receiver'): ?>
                    <a href="/food_waste_project/receiver/view_food.php">Browse Food</a>
                <?php elseif ($_SESSION['role'] === 'admin'): ?>
                    <a href="/food_waste_project/admin/dashboard.php">Dashboard</a>
                    <a href="/food_waste_project/admin/view_requests.php">Requests</a>
                <?php endif; ?>
                <a href="/food_waste_project/auth/logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="/food_waste_project/auth/login.php">Login</a>
                <a href="/food_waste_project/auth/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-wrapper">

        <div class="hero">
            <h1>Fight Food Waste.<br>Feed Communities.</h1>
            <p>Connect surplus food with people who need it. Every donation makes a difference.</p>
            <div class="hero-actions">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/food_waste_project/auth/register.php" class="btn btn-white">Get Started</a>
                    <a href="/food_waste_project/auth/login.php" class="btn btn-outline" style="color:white;border-color:rgba(255,255,255,0.5);">Login</a>
                <?php else: ?>
                    <a href="/food_waste_project/<?php echo $_SESSION['role'] === 'donor' ? 'donor/donate_food.php' : 'receiver/view_food.php'; ?>" class="btn btn-white">Go to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="page-header">
            <h1>What would you like to do?</h1>
            <p>Choose your role and get started in minutes.</p>
        </div>

        <div class="quick-links">
            <a href="/food_waste_project/auth/register.php" class="quick-link">
                <div class="ql-icon">🍱</div>
                <div class="ql-title">Donate Food</div>
                <div class="ql-desc">Have surplus food? List it and help those in need.</div>
            </a>
            <a href="/food_waste_project/receiver/view_food.php" class="quick-link">
                <div class="ql-icon">🤲</div>
                <div class="ql-title">Receive Food</div>
                <div class="ql-desc">Browse available food donations near you.</div>
            </a>
            <a href="/food_waste_project/auth/login.php" class="quick-link">
                <div class="ql-icon">🔑</div>
                <div class="ql-title">Login</div>
                <div class="ql-desc">Already have an account? Sign in here.</div>
            </a>
            <a href="/food_waste_project/admin/view_requests.php" class="quick-link">
                <div class="ql-icon">⚙️</div>
                <div class="ql-title">Admin Panel</div>
                <div class="ql-desc">Manage donations and approve requests.</div>
            </a>
        </div>

    </div>
</body>

</html>