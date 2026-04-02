<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// ✅ Fetch summary counts
$total_donations = $conn->query("SELECT COUNT(*) AS cnt FROM food_donations")->fetch_assoc()['cnt'];
$available       = $conn->query("SELECT COUNT(*) AS cnt FROM food_donations WHERE status='available'")->fetch_assoc()['cnt'];
$total_requests  = $conn->query("SELECT COUNT(*) AS cnt FROM requests")->fetch_assoc()['cnt'];
$pending         = $conn->query("SELECT COUNT(*) AS cnt FROM requests WHERE status='requested'")->fetch_assoc()['cnt'];
$total_users     = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Admin Dashboard</h2>
    <a href="view_requests.php">View Requests</a> |
    <a href="../auth/logout.php">Logout</a>
    <br><br>

    <table border="1" cellpadding="10">
        <tr>
            <th>Metric</th>
            <th>Count</th>
        </tr>
        <tr>
            <td>Total Food Donations</td>
            <td><?php echo $total_donations; ?></td>
        </tr>
        <tr>
            <td>Currently Available</td>
            <td><?php echo $available; ?></td>
        </tr>
        <tr>
            <td>Total Requests</td>
            <td><?php echo $total_requests; ?></td>
        </tr>
        <tr>
            <td>Pending Requests</td>
            <td><?php echo $pending; ?></td>
        </tr>
        <tr>
            <td>Total Users</td>
            <td><?php echo $total_users; ?></td>
        </tr>
    </table>

</body>

</html>