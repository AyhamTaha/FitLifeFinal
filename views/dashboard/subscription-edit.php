<?php

declare(strict_types=1);

$pageTitle = 'Edit Subscription';
$activeDashboardNav = 'Subscriptions';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/subscriptions.php';

fitlife_require_subscription_manager($currentStaff);
$gymId = (int)$currentStaff['gym_id'];
$subscriptionId = fitlife_subscription_id($_GET['id'] ?? null);
$subscription = null;
$dataUnavailable = false;

if ($subscriptionId !== null) {
    try {
        fitlife_expire_subscriptions($conn, $gymId);
        $subscription = fitlife_find_subscription($conn, $subscriptionId, $gymId);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife subscription edit lookup failed: ' . $exception->getMessage());
        $dataUnavailable = true;
    }
}

if ($dataUnavailable) {
    require __DIR__ . '/includes/header.php';
    echo '<section class="content-card unavailable-card"><h1>Subscription temporarily unavailable</h1><p>Please try again later.</p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
if ($subscription === null) {
    require __DIR__ . '/includes/header.php';
    fitlife_subscription_unavailable();
    require __DIR__ . '/includes/footer.php';
    exit;
}

$values = fitlife_subscription_empty_values();
foreach (array_keys($values) as $field) {
    $values[$field] = $subscription[$field] === null ? '' : (string)$subscription[$field];
}
$errors = [];
try {
    $subscriptionMembers = fitlife_subscription_members($conn, $gymId);
    $subscriptionPlans = fitlife_subscription_plans($conn, $gymId, (int)$subscription['membership_plan_id']);
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife subscription edit choices failed: ' . $exception->getMessage());
    $dataUnavailable = true;
    $subscriptionMembers = [];
    $subscriptionPlans = [];
}

if (!$dataUnavailable && $_SERVER['REQUEST_METHOD'] === 'POST') {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
    $validation = fitlife_validate_subscription_input($_POST);
    $values = $validation['values'];
    $errors = $validation['errors'];

    if ($errors === []) {
        try {
            $memberId = (int)$values['member_id'];
            $planId = (int)$values['membership_plan_id'];
            $member = fitlife_subscription_member($conn, $memberId, $gymId);
            $planChanged = $planId !== (int)$subscription['membership_plan_id'];
            $plan = fitlife_subscription_plan($conn, $planId, $gymId, $planChanged);
            if ($member === null) {
                $errors[] = 'The selected member is unavailable for this gym.';
            }
            if ($plan === null) {
                $errors[] = 'The selected membership plan is unavailable for this gym.';
            }

            if ($errors === [] && $plan !== null) {
                $status = (string)$subscription['status'];
                if ($status === 'active' && $values['end_date'] < date('Y-m-d')) {
                    $status = 'expired';
                }
                $price = $planChanged ? (string)$plan['price'] : (string)$subscription['price_snapshot'];
                $currency = $planChanged ? (string)$plan['currency'] : (string)$subscription['currency_snapshot'];
                $notes = $values['notes'] !== '' ? $values['notes'] : null;
                $userId = (int)$_SESSION['user_id'];
                $stmt = $conn->prepare(
                    'UPDATE subscriptions
                     SET member_id = ?, membership_plan_id = ?, start_date = ?, end_date = ?, status = ?,
                         price_snapshot = ?, currency_snapshot = ?, notes = ?, updated_by = ?
                     WHERE id = ? AND gym_id = ?'
                );
                $stmt->bind_param(
                    'iissssssiii',
                    $memberId,
                    $planId,
                    $values['start_date'],
                    $values['end_date'],
                    $status,
                    $price,
                    $currency,
                    $notes,
                    $userId,
                    $subscriptionId,
                    $gymId
                );
                $stmt->execute();
                $stmt->close();

                fitlife_flash('success', 'Subscription details were updated successfully.');
                fitlife_redirect('views/dashboard/subscription.php?id=' . $subscriptionId);
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('FitLife subscription update failed: ' . $exception->getMessage());
            $errors[] = 'The subscription could not be updated right now. Please try again.';
        }
    }
}

$subscriptionFormAction = fitlife_url('views/dashboard/subscription-edit.php?id=' . $subscriptionId);
$subscriptionFormSubmit = 'Save Changes';
$subscriptionCancelUrl = fitlife_url('views/dashboard/subscription.php?id=' . $subscriptionId);
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow">Subscriptions</span><h1>Edit Subscription</h1><p>Update its member, plan, dates, or internal notes.</p></div></section>
<?php if ($dataUnavailable): ?>
    <section class="content-card unavailable-card"><h2>Subscription choices are temporarily unavailable</h2><p>Please try again later.</p></section>
<?php else: ?>
    <section class="content-card form-card"><?php require __DIR__ . '/includes/subscription-form.php'; ?></section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
