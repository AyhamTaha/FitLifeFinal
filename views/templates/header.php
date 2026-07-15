<?php
require_once __DIR__ . '/../../includes/security.php';

fitlife_start_session();
$isAuthenticated = fitlife_is_authenticated();
if ($isAuthenticated) {
    fitlife_send_private_cache_headers();
}

$hasGymAssignment = $isAuthenticated
    && isset($_SESSION['gym_role'])
    && in_array($_SESSION['gym_role'], ['owner', 'manager', 'receptionist', 'trainer'], true);
$fitlifeBasePath = fitlife_escape(FITLIFE_BASE_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FitLife – Workout Library</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/styleauth.css">
</head>
<body>
<header id="header">
    <div class="logo">
        <img src="<?= $fitlifeBasePath ?>/public/images2/webfitlogo.png" alt="logo">
        <h2 style="color:#ff6600;">FitLife</h2>
    </div>

    <nav>
        <a href="<?= $fitlifeBasePath ?>/views/auth/home.php">Home</a>
        <a href="<?= $fitlifeBasePath ?>/views/workouts/muscles.php">Workouts</a>
        <a href="<?= $fitlifeBasePath ?>/views/nutrition/calculator.php">Nutrition</a>
        <a href="<?= $fitlifeBasePath ?>/views/programs/programs.php">Programs</a>
        <a href="<?= $fitlifeBasePath ?>/views/contact/contact.php">Contact</a>

        <?php if ($isAuthenticated): ?>
            <span style="margin-right:10px; color:#fff;">
                Hi, <?= fitlife_escape($_SESSION['user_name'] ?? 'User') ?>
            </span>
            <?php if ($hasGymAssignment): ?>
                <a href="<?= $fitlifeBasePath ?>/views/dashboard/index.php">Dashboard</a>
            <?php else: ?>
                <a href="<?= $fitlifeBasePath ?>/views/gym/setup.php">Set up your gym</a>
            <?php endif; ?>
            <form method="post" action="<?= $fitlifeBasePath ?>/views/auth/logout.php" class="nav-logout-form">
                <?= fitlife_csrf_input() ?>
                <button type="submit" class="btn-white nav-logout-button">Logout</button>
            </form>
        <?php else: ?>
            <a href="<?= $fitlifeBasePath ?>/views/auth/login.php" class="btn-white">Login</a>
            <a href="<?= $fitlifeBasePath ?>/views/auth/register.php" class="btn-white">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page">
