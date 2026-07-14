<?php

require("dbconn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $Repassword = $_POST["Repassword"];

    if (empty($name) || empty($email) || empty($password)) {
        header("Location: register.php?error=Missing fields");
        exit;
    }

    if ($password !== $Repassword) {
        header("Location: register.php?error=Passwords do not match");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=Invalid email");
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Save to DB
    $sql = "INSERT INTO users (name, email, password_hash) VALUES ('$name', '$email', '$hashedPassword')";
    mysqli_query($conn, $sql);

    header("Location: login.php?msg=Registered successfully");
    exit;
}
?>
