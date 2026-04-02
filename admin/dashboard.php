<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /food_waste_project/auth/login.php");
    exit();
}

$total_donations = $conn->query("SELECT COUNT(*) AS cnt FROM food_donations")->fetch_assoc()['cnt'];
$available       = $conn->query("SELECT COUNT(*) AS cnt FROM food_donations WHERE status='available'")->fetch_assoc()['cnt'];
$total_requests  = $conn->query("SELECT COUNT(*) AS cnt FROM requests")->fetch_assoc()['cnt'];
$pending         = $conn->query("SELECT COUNT(*) AS cnt FROM requests WHERE status='requested'")->fetch_assoc()['cnt'];
$total_users     = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'];

// Recent requests
$recent = $conn->query("
    SELECT requests.id, requests.status, users.name AS receiver_name,
           food_donations.food_name
    FROM requests
    JOIN users ON requests.receiver_id = users.id
    JOIN food_donations ON requests.food_id = food_donations.id
    ORDER BY requests.id DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — FoodShare</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <nav>
        <a href="/food_waste_project/index.php" class="nav-brand">FoodShare</a>
        <div class="nav-links">
            <a href="/food_waste_project/admin/dashboard.php" class="active">Dashboard</a>
            <a href="/food_waste_project/admin/view_requests.php">Requests</a>
            <a href="/food_waste_project/auth/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>Admin Dashboard</h1>
            <p>Overview of all platform activity.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-num"><?php echo $total_donations; ?></div>
                <div class="stat-label">Total Donations</div>
            </div>
            <div class="stat-card">
                <div class="stat-num"><?php echo $available; ?></div>
                <div class="stat-label">Available Now</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" style="color:#d4860a;"><?php echo $pending; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-num"><?php echo $total_requests; ?></div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-num"><?php echo $total_users; ?></div>
                <div class="stat-label">Registered Users</div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-family:'Fraunces',serif;font-size:1.2rem;">Recent Requests</h2>
            <a href="/food_waste_project/admin/view_requests.php" class="btn btn-outline btn-sm">View All</a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Receiver</th>
                        <th>Food Item</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent->num_rows > 0): ?>
                        <?php while ($row = $recent->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo intval($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                                <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;color:#6b7280;padding:2rem;">No requests yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>