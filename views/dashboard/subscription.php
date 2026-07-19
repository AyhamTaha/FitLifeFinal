<?php

declare(strict_types=1);

$pageTitle = 'Subscription';
$activeDashboardNav = 'Subscriptions';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/subscriptions.php';

$gymId = (int)$currentStaff['gym_id'];
$subscriptionId = fitlife_subscription_id($_GET['id'] ?? null);
$subscription = null;
$dataUnavailable = false;
if ($subscriptionId !== null) {
    try {
        fitlife_expire_subscriptions($conn, $gymId);
        $subscription = fitlife_find_subscription($conn, $subscriptionId, $gymId);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife subscription profile failed: ' . $exception->getMessage());
        $dataUnavailable = true;
    }
}

require __DIR__ . '/includes/header.php';
if ($dataUnavailable):
?>
<section class="content-card unavailable-card"><h1>Subscription temporarily unavailable</h1><p>Please try again later.</p></section>
<?php
    require __DIR__ . '/includes/footer.php';
    exit;
endif;
if ($subscription === null):
    fitlife_subscription_unavailable();
    require __DIR__ . '/includes/footer.php';
    exit;
endif;

$canManageSubscriptions = fitlife_can_manage_subscriptions($currentStaff['role']);
$status = (string)$subscription['status'];
$canFreeze = $status === 'active' && (int)$subscription['freeze_days_allowed'] > 0;
$canCancel = in_array($status, ['active', 'frozen'], true);
$canReactivate = in_array($status, ['frozen', 'cancelled'], true) && $subscription['end_date'] >= date('Y-m-d');
?>
<section class="page-heading member-profile-heading">
    <div><span class="eyebrow"><?= fitlife_escape($subscription['member_number']) ?></span><h1><?= fitlife_escape($subscription['first_name'] . ' ' . $subscription['last_name']) ?></h1><p>Subscription details and lifecycle controls.</p></div>
    <div class="heading-actions">
        <?php if ($canManageSubscriptions): ?><a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-edit.php?id=' . (int)$subscription['id'])) ?>">Edit Subscription</a><a class="secondary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-renew.php?id=' . (int)$subscription['id'])) ?>">Renew</a><?php endif; ?>
        <a class="secondary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscriptions.php')) ?>">Back to Subscriptions</a>
    </div>
</section>

<section class="profile-grid">
    <article class="content-card profile-card">
        <div class="profile-card-heading"><h2>Subscription details</h2><span class="status-badge status-<?= fitlife_escape($status) ?>"><?= fitlife_escape(fitlife_subscription_status_label($status)) ?></span></div>
        <dl class="detail-list">
            <div><dt>Member</dt><dd><a class="detail-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/member.php?id=' . (int)$subscription['member_id'])) ?>"><?= fitlife_escape($subscription['member_number'] . ' — ' . $subscription['first_name'] . ' ' . $subscription['last_name']) ?></a></dd></div>
            <div><dt>Membership plan</dt><dd><a class="detail-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/membership-plan.php?id=' . (int)$subscription['membership_plan_id'])) ?>"><?= fitlife_escape($subscription['plan_name']) ?></a></dd></div>
            <div><dt>Start date</dt><dd><?= fitlife_escape(fitlife_subscription_display_date($subscription['start_date'])) ?></dd></div>
            <div><dt>End date</dt><dd><?= fitlife_escape(fitlife_subscription_display_date($subscription['end_date'])) ?></dd></div>
            <div><dt>Price snapshot</dt><dd><?= fitlife_escape(fitlife_subscription_price($subscription)) ?></dd></div>
            <div><dt>Freeze allowance</dt><dd><?= number_format((int)$subscription['freeze_days_allowed']) ?> day<?= (int)$subscription['freeze_days_allowed'] === 1 ? '' : 's' ?></dd></div>
            <div><dt>Created</dt><dd><?= fitlife_escape(fitlife_subscription_display_date($subscription['created_at'], true)) ?></dd></div>
            <div><dt>Updated</dt><dd><?= fitlife_escape(fitlife_subscription_display_date($subscription['updated_at'], true)) ?></dd></div>
            <?php if ($subscription['renewed_at']): ?><div><dt>Last renewed</dt><dd><?= fitlife_escape(fitlife_subscription_display_date($subscription['renewed_at'], true)) ?></dd></div><?php endif; ?>
            <?php if ($subscription['frozen_at']): ?><div><dt>Last frozen</dt><dd><?= fitlife_escape(fitlife_subscription_display_date($subscription['frozen_at'], true)) ?></dd></div><?php endif; ?>
            <?php if ($subscription['cancelled_at']): ?><div><dt>Last cancelled</dt><dd><?= fitlife_escape(fitlife_subscription_display_date($subscription['cancelled_at'], true)) ?></dd></div><?php endif; ?>
        </dl>
        <div class="notes-block"><h3>Notes</h3><p><?= $subscription['notes'] ? nl2br(fitlife_escape($subscription['notes'])) : 'No notes have been added.' ?></p></div>

        <?php if ($canManageSubscriptions): ?>
            <div class="lifecycle-actions">
                <?php if ($canFreeze): ?>
                    <form method="post" action="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-status.php')) ?>" onsubmit="return confirm('Freeze this subscription? Access remains suspended until it is reactivated.');"><?= fitlife_csrf_input() ?><input type="hidden" name="id" value="<?= (int)$subscription['id'] ?>"><input type="hidden" name="action" value="freeze"><button class="secondary-button" type="submit">Freeze</button></form>
                <?php endif; ?>
                <?php if ($canCancel): ?>
                    <form method="post" action="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-status.php')) ?>" onsubmit="return confirm('Cancel this subscription?');"><?= fitlife_csrf_input() ?><input type="hidden" name="id" value="<?= (int)$subscription['id'] ?>"><input type="hidden" name="action" value="cancel"><button class="danger-button" type="submit">Cancel</button></form>
                <?php endif; ?>
                <?php if ($canReactivate): ?>
                    <form method="post" action="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-status.php')) ?>"><?= fitlife_csrf_input() ?><input type="hidden" name="id" value="<?= (int)$subscription['id'] ?>"><input type="hidden" name="action" value="reactivate"><button class="secondary-button" type="submit">Reactivate</button></form>
                <?php endif; ?>
                <?php if (in_array($status, ['frozen', 'cancelled'], true) && !$canReactivate): ?><p class="muted">This subscription has ended. Renew it to restore access.</p><?php endif; ?>
                <?php if ($status === 'active' && (int)$subscription['freeze_days_allowed'] === 0): ?><p class="muted">This plan does not allow subscription freezing.</p><?php endif; ?>
            </div>
        <?php endif; ?>
    </article>

    <aside class="history-grid">
        <article class="content-card future-card"><span>Billing</span><h2>No payment recorded</h2><p>The subscription stores only the plan price and currency snapshot. Payment processing is not part of this module.</p></article>
    </aside>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
