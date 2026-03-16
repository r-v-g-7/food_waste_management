<?php
include("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users(name,email,password,role)
VALUES('$name','$email','$password','$role')";

    if ($conn->query($sql)) {
        echo "User registered successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST">
    Name: <input type="text" name="name"><br><br>
    Email: <input type="email" name="email"><br><br>
    Password: <input type="password" name="password"><br><br>

    Role:
    <select name="role">
        <option value="donor">Donor</option>
        <option value="receiver">Receiver</option>
    </select>

    <br><br>

    <button type="submit">Register</button>

</form>