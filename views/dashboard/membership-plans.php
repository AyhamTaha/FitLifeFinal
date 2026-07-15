<?php

declare(strict_types=1);

$pageTitle = 'Membership Plans';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/membership-plans.php';

$gymId = (int)$currentStaff['gym_id'];
$canManagePlans = fitlife_can_manage_plans($currentStaff['role']);
$search = trim((string)($_GET['q'] ?? ''));
if (strlen($search) > 100) {
    $search = substr($search, 0, 100);
}
$status = (string)($_GET['status'] ?? '');
if (!in_array($status, ['active', 'inactive'], true)) {
    $status = '';
}

$plans = [];
$planCount = 0;
$dataUnavailable = false;
try {
    $where = "gym_id = ? AND name LIKE CONCAT('%', ?, '%')";
    if ($status !== '') {
        $where .= ' AND is_active = ?';
    }

    $stmt = $conn->prepare(
        'SELECT id, name, duration_value, duration_unit, price, currency,
                freeze_days_allowed, visit_limit, is_active
         FROM membership_plans
         WHERE ' . $where . '
         ORDER BY is_active DESC, name ASC, id ASC'
    );
    if ($status === '') {
        $stmt->bind_param('is', $gymId, $search);
    } else {
        $isActiveFilter = $status === 'active' ? 1 : 0;
        $stmt->bind_param('isi', $gymId, $search, $isActiveFilter);
    }
    $stmt->execute();
    $plans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $planCount = count($plans);
    $stmt->close();
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife membership plan list failed: ' . $exception->getMessage());
    $dataUnavailable = true;
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><span class="eyebrow">Plan catalog</span><h1>Membership Plans</h1><p>Manage the plans offered by <?= fitlife_escape($currentStaff['gym_name']) ?>.</p></div>
    <?php if ($canManagePlans): ?><a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan-add.php')) ?>">Add Plan</a><?php endif; ?>
</section>

<?php if ($dataUnavailable): ?>
    <section class="content-card unavailable-card"><h2>Membership plans are temporarily unavailable</h2><p>Please confirm the Phase 1 Step 3 migration has been imported, then try again.</p></section>
<?php else: ?>
    <section class="content-card filter-card">
        <form method="get" action="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plans.php')) ?>" class="member-filters plan-filters">
            <div class="filter-search"><label for="q">Search plans</label><input id="q" name="q" type="search" maxlength="100" value="<?= fitlife_escape($search) ?>" placeholder="Plan name"></div>
            <div><label for="status">Status</label><select id="status" name="status"><option value=""<?= $status === '' ? ' selected' : '' ?>>All plans</option><option value="active"<?= $status === 'active' ? ' selected' : '' ?>>Active</option><option value="inactive"<?= $status === 'inactive' ? ' selected' : '' ?>>Inactive</option></select></div>
            <button class="secondary-button" type="submit">Apply filters</button>
            <a class="text-button" href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plans.php')) ?>">Clear filters</a>
        </form>
    </section>

    <section class="content-card member-list-card">
        <div class="list-summary"><h2>Plan catalog</h2><span><?= number_format($planCount) ?> result<?= $planCount === 1 ? '' : 's' ?></span></div>
        <?php if ($plans === []): ?>
            <div class="empty-state">
                <span aria-hidden="true">&#128203;</span>
                <h3><?= ($search !== '' || $status !== '') ? 'No plans match these filters' : 'No membership plans yet' ?></h3>
                <p><?= ($search !== '' || $status !== '') ? 'Try changing or clearing the search filters.' : 'Create the first plan to define duration, pricing, and access limits.' ?></p>
                <?php if ($canManagePlans && $search === '' && $status === ''): ?><a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan-add.php')) ?>">Add Plan</a><?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="member-table plan-table">
                    <thead><tr><th>Plan</th><th>Duration</th><th>Price</th><th>Freeze</th><th>Visits</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($plans as $plan): $isActive = (int)$plan['is_active'] === 1; ?>
                        <tr>
                            <td data-label="Plan"><strong><?= fitlife_escape($plan['name']) ?></strong></td>
                            <td data-label="Duration"><?= fitlife_escape(fitlife_plan_duration($plan)) ?></td>
                            <td data-label="Price"><?= fitlife_escape(fitlife_plan_price($plan)) ?></td>
                            <td data-label="Freeze"><?= number_format((int)$plan['freeze_days_allowed']) ?> day<?= (int)$plan['freeze_days_allowed'] === 1 ? '' : 's' ?></td>
                            <td data-label="Visits"><?= $plan['visit_limit'] === null ? 'Unlimited' : number_format((int)$plan['visit_limit']) ?></td>
                            <td data-label="Status"><span class="status-badge status-<?= $isActive ? 'active' : 'inactive' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span></td>
                            <td data-label="Actions"><div class="table-actions"><a href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan.php?id=' . (int)$plan['id'])) ?>">View</a><?php if ($canManagePlans): ?><a href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan-edit.php?id=' . (int)$plan['id'])) ?>">Edit</a><form method="post" action="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan-status.php')) ?>"<?= $isActive ? ' onsubmit="return confirm(\'Deactivate this plan? Existing records will be retained.\');"' : '' ?>><?= fitlife_csrf_input() ?><input type="hidden" name="id" value="<?= (int)$plan['id'] ?>"><input type="hidden" name="action" value="<?= $isActive ? 'deactivate' : 'reactivate' ?>"><button class="table-action-button" type="submit"><?= $isActive ? 'Deactivate' : 'Reactivate' ?></button></form><?php endif; ?></div></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
