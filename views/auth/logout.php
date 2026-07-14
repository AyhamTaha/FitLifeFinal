<?php
// logout.php

session_start();

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// (Optional) delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to home page in the same folder
header("Location: home.php");
exit;
