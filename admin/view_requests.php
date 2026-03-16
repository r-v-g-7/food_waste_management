<?php
include("../config/db.php");

$sql = "SELECT users.name, food_donations.food_name
        FROM requests
        JOIN users ON requests.receiver_id = users.id
        JOIN food_donations ON requests.food_id = food_donations.id";
$result = $conn->query($sql);
?>

<h2>Food Requests</h2>

<table border="1">
    <tr>
        <th>User</th>
        <th>Food</th>
    </tr>

    <?php
    while ($row = $result->fetch_assoc()) {
    ?>

        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['food_name']; ?></td>
        </tr>

    <?php } ?>

</table>