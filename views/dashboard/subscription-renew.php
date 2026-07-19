<?php

declare(strict_types=1);

$pageTitle = 'Renew Subscription';
$activeDashboardNav = 'Subscriptions';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/subscriptions.php';

fitlife_require_subscription_manager($currentStaff);
$gymId = (int)$currentStaff['gym_id'];
$subscriptionId = fitlife_subscription_id($_GET['id'] ?? null);
$subscription = null;
$subscriptionPlans = [];
$dataUnavailable = false;
if ($subscriptionId !== null) {
    try {
        fitlife_expire_subscriptions($conn, $gymId);
        $subscription = fitlife_find_subscription($conn, $subscriptionId, $gymId);
        $subscriptionPlans = fitlife_subscription_plans($conn, $gymId);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife subscription renewal lookup failed: ' . $exception->getMessage());
        $dataUnavailable = true;
    }
}

if ($dataUnavailable) {
    require __DIR__ . '/includes/header.php';
    echo '<section class="content-card unavailable-card"><h1>Subscription renewal temporarily unavailable</h1><p>Please try again later.</p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
if ($subscription === null) {
    require __DIR__ . '/includes/header.php';
    fitlife_subscription_unavailable();
    require __DIR__ . '/includes/footer.php';
    exit;
}

$selectedPlan = null;
foreach ($subscriptionPlans as $plan) {
    if ((int)$plan['id'] === (int)$subscription['membership_plan_id']) {
        $selectedPlan = $plan;
        break;
    }
}
if ($selectedPlan === null && $subscriptionPlans !== []) {
    $selectedPlan = $subscriptionPlans[0];
}
$values = [
    'membership_plan_id' => $selectedPlan === null ? '' : (string)$selectedPlan['id'],
    'end_date' => $selectedPlan === null ? '' : fitlife_subscription_renewal_end((string)$subscription['end_date'], $selectedPlan),
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
    $values['membership_plan_id'] = trim((string)($_POST['membership_plan_id'] ?? ''));
    $values['end_date'] = trim((string)($_POST['end_date'] ?? ''));
    $planId = fitlife_subscription_id($values['membership_plan_id']);
    if ($planId === null) {
        $errors[] = 'Select an active membership plan.';
    }
    if (!fitlife_valid_subscription_date($values['end_date'])) {
        $errors[] = 'Enter a valid new end date.';
    } elseif ($values['end_date'] <= (string)$subscription['end_date']) {
        $errors[] = 'The renewed end date must be after the current end date.';
    } elseif ($values['end_date'] < date('Y-m-d')) {
        $errors[] = 'The renewed end date cannot be in the past.';
    }

    if ($errors === [] && $planId !== null) {
        try {
            $plan = fitlife_subscription_plan($conn, $planId, $gymId);
            if ($plan === null) {
                $errors[] = 'The selected membership plan is unavailable or inactive for this gym.';
            } else {
                $price = (string)$plan['price'];
                $currency = (string)$plan['currency'];
                $userId = (int)$_SESSION['user_id'];
                $stmt = $conn->prepare(
                    "UPDATE subscriptions
                     SET membership_plan_id = ?, end_date = ?, status = 'active',
                         price_snapshot = ?, currency_snapshot = ?, renewed_at = CURRENT_TIMESTAMP,
                         updated_by = ?
                     WHERE id = ? AND gym_id = ?"
                );
                $stmt->bind_param('isssiii', $planId, $values['end_date'], $price, $currency, $userId, $subscriptionId, $gymId);
                $stmt->execute();
                $stmt->close();

                fitlife_flash('success', 'Subscription was renewed successfully. No payment was recorded.');
                fitlife_redirect('views/dashboard/subscription.php?id=' . $subscriptionId);
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('FitLife subscription renewal failed: ' . $exception->getMessage());
            $errors[] = 'The subscription could not be renewed right now. Please try again.';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow"><?= fitlife_escape($subscription['member_number']) ?></span><h1>Renew Subscription</h1><p>Extend the current end date and refresh the plan price snapshot.</p></div></section>
<section class="content-card form-card">
    <?php if ($errors !== []): ?><div class="flash flash-error" role="alert"><strong>Please correct the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= fitlife_escape($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <?php if ($subscriptionPlans === []): ?><div class="flash flash-error" role="alert">At least one active membership plan is required to renew this subscription.</div><?php endif; ?>
    <form method="post" action="<?= fitlife_escape(fitlife_url('views/dashboard/subscription-renew.php?id=' . $subscriptionId)) ?>" class="member-form">
        <?= fitlife_csrf_input() ?>
        <fieldset>
            <legend>Renewal terms</legend>
            <div class="form-grid">
                <div><label>Member</label><input type="text" readonly value="<?= fitlife_escape($subscription['member_number'] . ' — ' . $subscription['first_name'] . ' ' . $subscription['last_name']) ?>"></div>
                <div><label for="membership_plan_id">Membership plan</label><select id="membership_plan_id" name="membership_plan_id" required><option value="">Select a plan</option><?php foreach ($subscriptionPlans as $plan): ?><option value="<?= (int)$plan['id'] ?>"<?= $values['membership_plan_id'] === (string)$plan['id'] ? ' selected' : '' ?>><?= fitlife_escape($plan['name'] . ' — ' . number_format((float)$plan['price'], 2) . ' ' . $plan['currency']) ?></option><?php endforeach; ?></select></div>
                <div><label>Original start date</label><input type="date" readonly value="<?= fitlife_escape($subscription['start_date']) ?>"></div>
                <div><label>Current end date</label><input type="date" readonly value="<?= fitlife_escape($subscription['end_date']) ?>"></div>
                <div><label for="end_date">Renewed end date</label><input id="end_date" name="end_date" type="date" required value="<?= fitlife_escape($values['end_date']) ?>"></div>
            </div>
            <span class="field-help">The suggested end date uses the selected plan's duration. Confirm it before submitting if you choose a different plan.</span>
        </fieldset>
        <div class="form-actions"><button class="primary-button" type="submit"<?= $subscriptionPlans === [] ? ' disabled' : '' ?>>Renew Subscription</button><a class="secondary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscription.php?id=' . $subscriptionId)) ?>">Cancel</a></div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
