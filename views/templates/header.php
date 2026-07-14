<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FitLife – Workout Library</title>
  <link rel="stylesheet" href="/fitness-website/public/css/styleauth.css">
</head>
<body>
<header id="header">
    <div class="logo">
        <img src="/fitness-website/public/images2/webfitlogo.png" alt="logo">
        <h2 style="color:#ff6600;">FitLife</h2>
    </div>

    <nav>
        <a href="/fitness-website/views/auth/home.php">Home</a>
        <a href="/fitness-website/views/workouts/muscles.php">Workouts</a>
        <a href="/fitness-website/views/nutrition/calculator.php">Nutrition</a>
        <a href="/fitness-website/views/programs/programs.php">Programs</a>
        <a href="/fitness-website/views/contact/contact.php">Contact</a>

        <?php if (!empty($_SESSION['logged_in'])): ?>
            <!-- When logged in: show username + Logout -->
            <span style="margin-right:10px; color:#fff;">
                Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
            <a href="/fitness-website/views/auth/logout.php" class="btn-white">Logout</a>
        <?php else: ?>
            <!-- When NOT logged in: show Login + Sign Up -->
            <a href="/fitness-website/views/auth/login.php" class="btn-white">Login</a>
            <a href="/fitness-website/views/auth/register.php" class="btn-white">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page">
