<?php
$dashboardNav = [
    'Dashboard' => 'views/dashboard/index.php',
    'Members' => 'views/dashboard/members.php',
    'Membership Plans' => 'views/dashboard/membership-plans.php',
    'Subscriptions' => 'views/dashboard/subscriptions.php',
    'Payments' => 'views/dashboard/payments.php',
];
$activeDashboardNav = isset($activeDashboardNav) ? (string)$activeDashboardNav : $pageTitle;
?>
<aside class="dashboard-sidebar">
    <nav aria-label="Dashboard navigation">
        <?php foreach ($dashboardNav as $label => $path): ?>
            <a href="<?= $fitlifeBasePath ?>/<?= fitlife_escape($path) ?>"<?= $activeDashboardNav === $label ? ' class="active" aria-current="page"' : '' ?>>
                <?= fitlife_escape($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <form method="post" action="<?= $fitlifeBasePath ?>/views/auth/logout.php" class="dashboard-logout">
        <?= fitlife_csrf_input() ?>
        <button type="submit">Logout</button>
    </form>
</aside>
