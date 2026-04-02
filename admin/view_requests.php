<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /food_waste_project/auth/login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['request_id'])) {
    $request_id = intval($_GET['request_id']);
    $action     = $_GET['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $request_id);
        $stmt->execute();

        if ($action === 'rejected') {
            $gf = $conn->prepare("SELECT food_id FROM requests WHERE id = ?");
            $gf->bind_param("i", $request_id);
            $gf->execute();
            $gf->bind_result($food_id);
            $gf->fetch();
            $gf->close();

            $restore = $conn->prepare("UPDATE food_donations SET status = 'available' WHERE id = ?");
            $restore->bind_param("i", $food_id);
            $restore->execute();
        }
    }
    header("Location: /food_waste_project/admin/view_requests.php");
    exit();
}

$sql = "SELECT requests.id, requests.status,
               users.name AS receiver_name, users.email,
               food_donations.food_name, food_donations.quantity,
               food_donations.location, food_donations.expiry_time
        FROM requests
        JOIN users ON requests.receiver_id = users.id
        JOIN food_donations ON requests.food_id = food_donations.id
        ORDER BY requests.id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Requests — FoodShare Admin</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <nav>
        <a href="/food_waste_project/index.php" class="nav-brand">FoodShare</a>
        <div class="nav-links">
            <a href="/food_waste_project/admin/dashboard.php">Dashboard</a>
            <a href="/food_waste_project/admin/view_requests.php" class="active">Requests</a>
            <a href="/food_waste_project/auth/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>All Food Requests</h1>
            <p>Review and approve or reject incoming food requests.</p>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Receiver</th>
                            <th>Email</th>
                            <th>Food Item</th>
                            <th>Qty</th>
                            <th>Location</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo intval($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                                <td style="color:#6b7280;font-size:13px;"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['food_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td style="font-size:13px;"><?php echo htmlspecialchars($row['expiry_time']); ?></td>
                                <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <?php if ($row['status'] === 'requested'): ?>
                                        <div style="display:flex;gap:6px;">
                                            <a href="?action=approved&request_id=<?php echo intval($row['id']); ?>"
                                                class="btn btn-primary btn-sm"
                                                onclick="return confirm('Approve this request?')">Approve</a>
                                            <a href="?action=rejected&request_id=<?php echo intval($row['id']); ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Reject this request?')">Reject</a>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size:13px;color:#6b7280;"><?php echo ucfirst($row['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p>No requests have been made yet.</p>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>