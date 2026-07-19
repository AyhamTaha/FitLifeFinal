<?php

declare(strict_types=1);

$pageTitle = 'Membership Plan';
$activeDashboardNav = 'Membership Plans';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/membership-plans.php';

$planId = fitlife_plan_id($_GET['id'] ?? null);
$plan = null;
$dataUnavailable = false;
if ($planId !== null) {
    try {
        $plan = fitlife_find_plan($conn, $planId, (int)$currentStaff['gym_id']);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife membership plan profile failed: ' . $exception->getMessage());
        $dataUnavailable = true;
    }
}

require __DIR__ . '/includes/header.php';
if ($dataUnavailable):
?>
<section class="content-card unavailable-card"><h1>Membership plan temporarily unavailable</h1><p>Please try again later.</p></section>
<?php
    require __DIR__ . '/includes/footer.php';
    exit;
endif;
if ($plan === null):
    fitlife_plan_unavailable();
    require __DIR__ . '/includes/footer.php';
    exit;
endif;

$canManagePlans = fitlife_can_manage_plans($currentStaff['role']);
$isActive = (int)$plan['is_active'] === 1;
?>
<section class="page-heading member-profile-heading">
    <div><span class="eyebrow">Membership Plan</span><h1><?= fitlife_escape($plan['name']) ?></h1><p>Plan terms, access allowance, and current availability.</p></div>
    <div class="heading-actions">
        <?php if ($canManagePlans): ?><a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan-edit.php?id=' . (int)$plan['id'])) ?>">Edit Plan</a><?php endif; ?>
        <a class="secondary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plans.php')) ?>">Back to Membership Plans</a>
    </div>
</section>

<section class="profile-grid">
    <article class="content-card profile-card">
        <div class="profile-card-heading"><h2>Plan details</h2><span class="status-badge status-<?= $isActive ? 'active' : 'inactive' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span></div>
        <dl class="detail-list">
            <div><dt>Duration</dt><dd><?= fitlife_escape(fitlife_plan_duration($plan)) ?></dd></div>
            <div><dt>Price</dt><dd><?= fitlife_escape(fitlife_plan_price($plan)) ?></dd></div>
            <div><dt>Freeze allowance</dt><dd><?= number_format((int)$plan['freeze_days_allowed']) ?> day<?= (int)$plan['freeze_days_allowed'] === 1 ? '' : 's' ?></dd></div>
            <div><dt>Visit limit</dt><dd><?= $plan['visit_limit'] === null ? 'Unlimited' : number_format((int)$plan['visit_limit']) . ' visits' ?></dd></div>
            <div><dt>Created</dt><dd><?= fitlife_escape(fitlife_plan_display_date($plan['created_at'])) ?></dd></div>
            <div><dt>Updated</dt><dd><?= fitlife_escape(fitlife_plan_display_date($plan['updated_at'])) ?></dd></div>
        </dl>
        <div class="notes-block"><h3>Description</h3><p><?= $plan['description'] ? nl2br(fitlife_escape($plan['description'])) : 'No description has been added.' ?></p></div>

        <?php if ($canManagePlans): ?>
            <form method="post" action="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan-status.php')) ?>" class="archive-form"<?= $isActive ? ' onsubmit="return confirm(\'Deactivate this plan? Existing records will be retained.\');"' : '' ?>>
                <?= fitlife_csrf_input() ?><input type="hidden" name="id" value="<?= (int)$plan['id'] ?>"><input type="hidden" name="action" value="<?= $isActive ? 'deactivate' : 'reactivate' ?>">
                <button class="<?= $isActive ? 'danger-button' : 'secondary-button' ?>" type="submit"><?= $isActive ? 'Deactivate Plan' : 'Reactivate Plan' ?></button>
            </form>
        <?php endif; ?>
    </article>

    <aside class="history-grid" aria-label="Subscription connection">
        <article class="content-card future-card"><span>Subscriptions</span><h2>Plan usage</h2><p>View subscriptions that use this membership plan. Billing is not created here.</p><a class="secondary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscriptions.php?q=' . rawurlencode($plan['name']))) ?>">View Subscriptions</a></article>
    </aside>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
