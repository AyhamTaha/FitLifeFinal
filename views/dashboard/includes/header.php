<?php
$pageTitle = isset($pageTitle) ? (string)$pageTitle : 'Dashboard';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= fitlife_escape($pageTitle) ?> | FitLife</title>
    <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/dashboard.css">
</head>
<body class="dashboard-body">
<header class="dashboard-header">
    <a class="dashboard-brand" href="<?= $fitlifeBasePath ?>/views/dashboard/index.php">
        <span class="brand-mark">F</span>
        <span>FitLife Management</span>
    </a>
    <div class="staff-summary">
        <div>
            <strong><?= fitlife_escape($currentStaff['gym_name']) ?></strong>
            <span><?= fitlife_escape($_SESSION['user_name'] ?? 'User') ?> &middot; <?= fitlife_escape(ucfirst($currentStaff['role'])) ?></span>
        </div>
        <a href="<?= $fitlifeBasePath ?>/views/auth/home.php" class="public-site-link">Public site</a>
    </div>
</header>
<div class="dashboard-layout">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="dashboard-main">
        <?php foreach ($dashboardFlashes as $flash): ?>
            <div class="flash <?= $flash['type'] === 'success' ? 'flash-success' : 'flash-error' ?>">
                <?= fitlife_escape($flash['message']) ?>
            </div>
        <?php endforeach; ?>
