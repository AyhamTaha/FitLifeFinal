<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/subscriptions.php';

$activeMemberCount = null;
$activeSubscriptionCount = null;
$expiringSubscriptionCount = null;
$expiredSubscriptionCount = null;
try {
    $dashboardGymId = (int)$currentStaff['gym_id'];
    $memberCountStmt = $conn->prepare("SELECT COUNT(*) AS total FROM members WHERE gym_id = ? AND status = 'active'");
    $memberCountStmt->bind_param('i', $dashboardGymId);
    $memberCountStmt->execute();
    $activeMemberCount = (int)$memberCountStmt->get_result()->fetch_assoc()['total'];
    $memberCountStmt->close();
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife dashboard member count failed: ' . $exception->getMessage());
}

try {
    fitlife_expire_subscriptions($conn, $dashboardGymId);
    $subscriptionCountStmt = $conn->prepare(
        "SELECT
            SUM(status = 'active') AS active_total,
            SUM(status = 'active' AND end_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)) AS expiring_total,
            SUM(status = 'expired') AS expired_total
         FROM subscriptions
         WHERE gym_id = ?"
    );
    $subscriptionCountStmt->bind_param('i', $dashboardGymId);
    $subscriptionCountStmt->execute();
    $subscriptionCounts = $subscriptionCountStmt->get_result()->fetch_assoc();
    $subscriptionCountStmt->close();
    $activeSubscriptionCount = (int)$subscriptionCounts['active_total'];
    $expiringSubscriptionCount = (int)$subscriptionCounts['expiring_total'];
    $expiredSubscriptionCount = (int)$subscriptionCounts['expired_total'];
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife dashboard subscription counts failed: ' . $exception->getMessage());
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">Overview</span>
        <h1>Dashboard</h1>
        <p>Welcome back, <?= fitlife_escape($_SESSION['user_name'] ?? 'User') ?>. Here is the current snapshot for <?= fitlife_escape($currentStaff['gym_name']) ?>.</p>
    </div>
    <span class="role-badge"><?= fitlife_escape(ucfirst($currentStaff['role'])) ?></span>
</section>

<section class="stat-grid" aria-label="Gym statistics">
    <article class="stat-card"><span>Active members</span><strong><?= $activeMemberCount === null ? '&mdash;' : number_format($activeMemberCount) ?></strong></article>
    <article class="stat-card"><span>Active subscriptions</span><strong><?= $activeSubscriptionCount === null ? '&mdash;' : number_format($activeSubscriptionCount) ?></strong></article>
    <article class="stat-card"><span>Expiring in 30 days</span><strong><?= $expiringSubscriptionCount === null ? '&mdash;' : number_format($expiringSubscriptionCount) ?></strong></article>
    <article class="stat-card"><span>Expired subscriptions</span><strong><?= $expiredSubscriptionCount === null ? '&mdash;' : number_format($expiredSubscriptionCount) ?></strong></article>
</section>

<section class="content-card">
    <h2>Gym management ready</h2>
    <p>Your gym account, staff authorization, secure member directory, membership plan catalog, and subscription management are active. Payments and attendance remain future features.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
