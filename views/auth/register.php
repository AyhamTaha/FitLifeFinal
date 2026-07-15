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
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/styleauth.css">
</head>
<body class="register">

<div class="auth-card">

    <h2>Create Account</h2>

    <div class="back-home">
        <a href="<?= $fitlifeBasePath ?>/views/auth/home.php"><span>&larr;</span> Back to Home</a>
    </div>

    <?php foreach ($flashes as $flash): ?>
        <div class="<?= $flash['type'] === 'success' ? 'auth-success' : 'auth-error' ?>">
            <?= fitlife_escape($flash['message']) ?>
        </div>
    <?php endforeach; ?>

    <form method="POST" action="<?= $fitlifeBasePath ?>/views/auth/regproc.php">
        <?= fitlife_csrf_input() ?>

        <input type="text" name="name" placeholder="Full Name" maxlength="100" autocomplete="name" required>

        <input type="email" name="email" placeholder="Email" maxlength="150" autocomplete="email" required>

        <input type="password" name="password" placeholder="Password (8+ characters)" minlength="8" autocomplete="new-password" required>

        <input type="password" name="Repassword" placeholder="Re-Type Password" minlength="8" autocomplete="new-password" required>

        <input type="submit" value="Register">

        <p class="auth-link">Already have an account?
            <a href="<?= $fitlifeBasePath ?>/views/auth/login.php">Login</a>
        </p>

    </form>

</div>

</body>
</html>
