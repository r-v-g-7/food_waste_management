<?php
session_start();

// ✅ Clear all session variables
$_SESSION = [];

// ✅ Destroy the session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ✅ Destroy the session on the server
session_destroy();
header("Location: /food_waste_project/index.php");
exit();
