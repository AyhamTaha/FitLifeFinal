<?php
session_start();
$error = isset($_GET['error']) ? $_GET['error'] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="/fitness-website/public/css/styleauth.css">
</head>
<body class="register">

<div class="auth-card">

    <h2>Create Account</h2>

    <div class="back-home">
        <a href="home.php"><span>&larr;</span> Back to Home</a>
    </div>

    <?php if ($error): ?>
        <div class="auth-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="regproc.php">

        <input type="text" name="name" placeholder="Full Name">

        <input type="text" name="email" placeholder="Email">

        <input type="password" name="password" placeholder="Password">

        <input type="password" name="Repassword" placeholder="Re-Type Password">

        <input type="submit" value="Register">

        <p class="auth-link">Already have an account?
            <a href="login.php">Login</a>
        </p>

    </form>

</div>

</body>
</html>
