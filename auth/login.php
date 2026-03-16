<?php
session_start();
include("../config/db.php");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == "admin") {
            header("Location: ../admin/view_requests.php");
        } elseif ($user['role'] == "donor") {
            header("Location: ../donor/add_food.php");
        } else {
            header("Location: ../receiver/view_food.php");
        }
    } else {
        echo "Invalid login";
    }
}
?>

<h2>Login</h2>

<form method="POST">

    Email:<br>
    <input type="email" name="email"><br><br>

    Password:<br>
    <input type="password" name="password"><br><br>

    <input type="submit" name="login" value="Login">

</form>