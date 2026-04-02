<?php
include("../config/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role  = $_POST['role'];

    // ✅ Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!in_array($role, ['donor', 'receiver'])) {
        // ✅ Whitelist roles — prevents someone injecting 'admin' via dev tools
        $error = "Invalid role selected.";
    } else {

        // ✅ Check for duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered.";
        } else {

            // ✅ Prepared statement — no SQL injection
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("ssss", $name, $email, $hashed, $role);

            if ($stmt2->execute()) {
                $success = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" href="/food_waste_project/css/style.css">
</head>

<body>

    <h2>Register</h2>

    <?php if ($error)   echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="POST">
        Name:<br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required><br><br>

        Email:<br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required><br><br>

        Password:<br>
        <input type="password" name="password" required><br><br>

        Role:<br>
        <select name="role">
            <option value="donor" <?php echo (($role ?? '') == 'donor')    ? 'selected' : ''; ?>>Donor</option>
            <option value="receiver" <?php echo (($role ?? '') == 'receiver') ? 'selected' : ''; ?>>Receiver</option>
        </select><br><br>

        <button type="submit">Register</button>
    </form>

</body>

</html>