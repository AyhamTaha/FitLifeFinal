<?php
$error = isset($_GET['error']) ? $_GET['error'] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/fitness-website/public/css/styleauth.css">
</head>

<body class="login">

<div class="auth-card">

    <h2>Welcome Back</h2>

    <div class="back-home">
        <a href="home.php"><span>&larr;</span> Back to Home</a>
    </div>

    <?php if ($error): ?>
        <div class="auth-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="loginproc.php">

        <input type="text" name="email" placeholder="Email">

        <input type="password" name="password" placeholder="Password">

        <input type="submit" value="Login">

        <p class="auth-link">
            Don’t have an account?
            <a href="register.php">Register</a>
        </p>

    </form>

</div>

</body>
</html>
