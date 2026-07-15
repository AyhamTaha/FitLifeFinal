<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/bootstrap.php';

$activeMemberCount = null;
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
    <article class="stat-card"><span>Expiring soon</span><strong>0</strong></article>
    <article class="stat-card"><span>Expired</span><strong>0</strong></article>
    <article class="stat-card"><span>Revenue this month</span><strong>$0</strong></article>
</section>

<section class="content-card">
    <h2>Gym management ready</h2>
    <p>Your gym account, staff authorization, secure member directory, and membership plan catalog are active. Subscriptions, payments, and attendance remain future features.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
