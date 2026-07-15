<?php
require_once __DIR__ . '/../../includes/security.php';

fitlife_start_session();
if (fitlife_is_authenticated()) {
    fitlife_redirect(isset($_SESSION['gym_role']) ? 'views/dashboard/index.php' : 'views/auth/home.php');
}

$flashes = fitlife_take_flashes();
$fitlifeBasePath = fitlife_escape(FITLIFE_BASE_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/styleauth.css">
</head>

<body class="login">

<div class="auth-card">

    <h2>Welcome Back</h2>

    <div class="back-home">
        <a href="<?= $fitlifeBasePath ?>/views/auth/home.php"><span>&larr;</span> Back to Home</a>
    </div>

    <?php foreach ($flashes as $flash): ?>
        <div class="<?= $flash['type'] === 'success' ? 'auth-success' : 'auth-error' ?>">
            <?= fitlife_escape($flash['message']) ?>
        </div>
    <?php endforeach; ?>

    <form method="POST" action="<?= $fitlifeBasePath ?>/views/auth/loginproc.php">
        <?= fitlife_csrf_input() ?>

        <input type="email" name="email" placeholder="Email" maxlength="150" autocomplete="email" required>

        <input type="password" name="password" placeholder="Password" autocomplete="current-password" required>

        <input type="submit" value="Login">

        <p class="auth-link">
            Don’t have an account?
            <a href="<?= $fitlifeBasePath ?>/views/auth/register.php">Register</a>
        </p>

    </form>

</div>

</body>
</html>
