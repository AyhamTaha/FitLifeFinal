<?php

declare(strict_types=1);

$pageTitle = 'Subscriptions';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/subscriptions.php';

$gymId = (int)$currentStaff['gym_id'];
$canManageSubscriptions = fitlife_can_manage_subscriptions($currentStaff['role']);
$search = trim((string)($_GET['q'] ?? ''));
if (strlen($search) > 100) {
    $search = substr($search, 0, 100);
}
$status = (string)($_GET['status'] ?? '');
if (!in_array($status, FITLIFE_SUBSCRIPTION_STATUSES, true)) {
    $status = '';
}
$requestedPage = fitlife_subscription_id($_GET['page'] ?? 1) ?? 1;
$perPage = 20;
$subscriptionCount = 0;
$pageCount = 1;
$currentPage = 1;
$subscriptions = [];
$dataUnavailable = false;

try {
    fitlife_expire_subscriptions($conn, $gymId);
    $where = "s.gym_id = ? AND CONCAT_WS(' ', m.member_number, m.first_name, m.last_name, mp.name) LIKE CONCAT('%', ?, '%')";
    if ($status !== '') {
        $where .= ' AND s.status = ?';
    }

    $countStmt = $conn->prepare(
        'SELECT COUNT(*) AS total
         FROM subscriptions AS s
         INNER JOIN members AS m ON m.id = s.member_id AND m.gym_id = s.gym_id
         INNER JOIN membership_plans AS mp ON mp.id = s.membership_plan_id AND mp.gym_id = s.gym_id
         WHERE ' . $where
    );
    if ($status === '') {
        $countStmt->bind_param('is', $gymId, $search);
    } else {
        $countStmt->bind_param('iss', $gymId, $search, $status);
    }
    $countStmt->execute();
    $subscriptionCount = (int)$countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();

    $pageCount = max(1, (int)ceil($subscriptionCount / $perPage));
    $currentPage = min($requestedPage, $pageCount);
    $offset = ($currentPage - 1) * $perPage;
    $listStmt = $conn->prepare(
        'SELECT s.id, s.start_date, s.end_date, s.status, s.price_snapshot, s.currency_snapshot,
                m.member_number, m.first_name, m.last_name, mp.name AS plan_name
         FROM subscriptions AS s
         INNER JOIN members AS m ON m.id = s.member_id AND m.gym_id = s.gym_id
         INNER JOIN membership_plans AS mp ON mp.id = s.membership_plan_id AND mp.gym_id = s.gym_id
         WHERE ' . $where . '
         ORDER BY FIELD(s.status, \'active\', \'frozen\', \'expired\', \'cancelled\'), s.end_date DESC, s.id DESC
         LIMIT ? OFFSET ?'
    );
    if ($status === '') {
        $listStmt->bind_param('isii', $gymId, $search, $perPage, $offset);
    } else {
        $listStmt->bind_param('issii', $gymId, $search, $status, $perPage, $offset);
    }
    $listStmt->execute();
    $subscriptions = $listStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $listStmt->close();
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife subscription list failed: ' . $exception->getMessage());
    $dataUnavailable = true;
}

$subscriptionListUrl = static function (int $page) use ($search, $status): string {
    $params = [];
    if ($search !== '') {
        $params['q'] = $search;
    }
    if ($status !== '') {
        $params['status'] = $status;
    }
    if ($page > 1) {
        $params['page'] = $page;
    }

    $url = fitlife_url('views/dashboard/subscriptions.php');
    return $params === [] ? $url : $url . '?' . http_build_query($params);
};

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><span class="eyebrow">Membership records</span><h1>Subscriptions</h1><p>Manage member access periods for <?= fitlife_escape($currentStaff['gym_name']) ?>.</p></div>
    <?php if ($canManageSubscriptions): ?><a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-add.php')) ?>">Add Subscription</a><?php endif; ?>
</section>

<?php if ($dataUnavailable): ?>
    <section class="content-card unavailable-card"><h2>Subscriptions are temporarily unavailable</h2><p>Please confirm the Phase 1 Step 4 migration has been imported, then try again.</p></section>
<?php else: ?>
    <section class="content-card filter-card">
        <form method="get" action="<?= fitlife_escape(fitlife_url('views/dashboard/subscriptions.php')) ?>" class="member-filters">
            <div class="filter-search"><label for="q">Search subscriptions</label><input id="q" name="q" type="search" maxlength="100" value="<?= fitlife_escape($search) ?>" placeholder="Member number, member, or plan"></div>
            <div><label for="status">Status</label><select id="status" name="status"><option value=""<?= $status === '' ? ' selected' : '' ?>>All statuses</option><?php foreach (FITLIFE_SUBSCRIPTION_STATUSES as $option): ?><option value="<?= fitlife_escape($option) ?>"<?= $status === $option ? ' selected' : '' ?>><?= fitlife_escape(fitlife_subscription_status_label($option)) ?></option><?php endforeach; ?></select></div>
            <button class="secondary-button" type="submit">Apply filters</button>
            <a class="text-button" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscriptions.php')) ?>">Clear filters</a>
        </form>
    </section>

    <section class="content-card member-list-card">
        <div class="list-summary"><h2>Subscription records</h2><span><?= number_format($subscriptionCount) ?> result<?= $subscriptionCount === 1 ? '' : 's' ?></span></div>
        <?php if ($subscriptions === []): ?>
            <div class="empty-state">
                <span aria-hidden="true">&#128203;</span>
                <h3><?= ($search !== '' || $status !== '') ? 'No subscriptions match these filters' : 'No subscriptions yet' ?></h3>
                <p><?= ($search !== '' || $status !== '') ? 'Try changing or clearing the filters.' : 'Create the first subscription to connect a member with a membership plan.' ?></p>
                <?php if ($canManageSubscriptions && $search === '' && $status === ''): ?><a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-add.php')) ?>">Add Subscription</a><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="member-table subscription-table">
                    <thead><tr><th>Member</th><th>Plan</th><th>Period</th><th>Price snapshot</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <tr>
                            <td data-label="Member"><strong><?= fitlife_escape($subscription['first_name'] . ' ' . $subscription['last_name']) ?></strong><span><?= fitlife_escape($subscription['member_number']) ?></span></td>
                            <td data-label="Plan"><?= fitlife_escape($subscription['plan_name']) ?></td>
                            <td data-label="Period"><?= fitlife_escape(fitlife_subscription_display_date($subscription['start_date'])) ?><br><span class="muted">to <?= fitlife_escape(fitlife_subscription_display_date($subscription['end_date'])) ?></span></td>
                            <td data-label="Price snapshot"><?= fitlife_escape(fitlife_subscription_price($subscription)) ?></td>
                            <td data-label="Status"><span class="status-badge status-<?= fitlife_escape($subscription['status']) ?>"><?= fitlife_escape(fitlife_subscription_status_label($subscription['status'])) ?></span></td>
                            <td data-label="Actions"><div class="table-actions"><a href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription.php?id=' . (int)$subscription['id'])) ?>">View</a><?php if ($canManageSubscriptions): ?><a href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-edit.php?id=' . (int)$subscription['id'])) ?>">Edit</a><a href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-renew.php?id=' . (int)$subscription['id'])) ?>">Renew</a><?php endif; ?></div></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pageCount > 1): ?>
                <nav class="pagination" aria-label="Subscription list pages">
                    <?php if ($currentPage > 1): ?><a href="<?= fitlife_escape($subscriptionListUrl($currentPage - 1)) ?>">Previous</a><?php endif; ?>
                    <span>Page <?= $currentPage ?> of <?= $pageCount ?></span>
                    <?php if ($currentPage < $pageCount): ?><a href="<?= fitlife_escape($subscriptionListUrl($currentPage + 1)) ?>">Next</a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
