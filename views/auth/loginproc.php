<?php
session_start();
require("dbconn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password_hash'])) {

            $_SESSION["logged_in"] = true;
            $_SESSION["user_name"] = $user["name"];

            header("Location: home.php");
            exit;
        } else {
            echo "Wrong password";
        }
    } else {
        echo "Email not found";
    }
}
?>
