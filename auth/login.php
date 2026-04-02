<?php
session_start();
include("../config/db.php");

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        // DEBUG (temporary)
        echo "Entered Password: " . $password . "<br>";
        echo "Stored Password: " . $user['password'] . "<br><br>";

        // Try BOTH methods
        if ($password == $user['password'] || password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == "admin") {
                header("Location: ../admin/view_requests.php");
                exit();
            } elseif ($user['role'] == "donor") {
                header("Location: ../donor/add_food.php");
                exit();
            } else {
                header("Location: ../receiver/view_food.php");
                exit();
            }
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Login</h2>

    <form method="POST">

        Email:<br>
        <input type="email" name="email" required><br><br>

        Password:<br>
        <input type="password" name="password" required><br><br>

        <input type="submit" name="login" value="Login">

    </form>

</body>

</html>