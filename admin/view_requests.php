<?php
session_start();
require_once("../config/db.php");

// ✅ CRITICAL — enforce admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /food_waste_project/auth/login.php");
    exit();
}

// ✅ Handle approve/reject actions
if (isset($_GET['action']) && isset($_GET['request_id'])) {
    $request_id = intval($_GET['request_id']);
    $action     = $_GET['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $request_id);
        $stmt->execute();

        // ✅ If rejected, set food back to available
        if ($action === 'rejected') {
            $get_food = $conn->prepare("SELECT food_id FROM requests WHERE id = ?");
            $get_food->bind_param("i", $request_id);
            $get_food->execute();
            $get_food->bind_result($food_id);
            $get_food->fetch();
            $get_food->close();

            $restore = $conn->prepare("UPDATE food_donations SET status = 'available' WHERE id = ?");
            $restore->bind_param("i", $food_id);
            $restore->execute();
        }
    }

    header("Location: /food_waste_project/admin/view_requests.php");
    exit();
}

// ✅ Fetch all requests with full details
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
<html>

<head>
    <title>Food Requests — Admin</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Food Requests</h2>
    <a href="dashboard.php">Dashboard</a> |
    <a href="../auth/logout.php">Logout</a>
    <br><br>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Request ID</th>
                <th>Receiver</th>
                <th>Email</th>
                <th>Food</th>
                <th>Quantity</th>
                <th>Location</th>
                <th>Expiry</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <!-- ✅ htmlspecialchars on all output -->
                    <td><?php echo intval($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['expiry_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] === 'requested'): ?>
                            <a href="?action=approved&request_id=<?php echo intval($row['id']); ?>"
                                onclick="return confirm('Approve this request?')">Approve</a>
                            &nbsp;|&nbsp;
                            <a href="?action=rejected&request_id=<?php echo intval($row['id']); ?>"
                                onclick="return confirm('Reject this request?')">Reject</a>
                        <?php else: ?>
                            <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No requests yet.</p>
    <?php endif; ?>

</body>

</html>