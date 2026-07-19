<?php

declare(strict_types=1);

$pageTitle = 'Add Subscription';
$activeDashboardNav = 'Subscriptions';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/subscriptions.php';

fitlife_require_subscription_manager($currentStaff);
$gymId = (int)$currentStaff['gym_id'];
$userId = (int)$_SESSION['user_id'];
$values = fitlife_subscription_empty_values();
$requestedMemberId = fitlife_subscription_id($_GET['member_id'] ?? null);
if ($requestedMemberId !== null) {
    $values['member_id'] = (string)$requestedMemberId;
}
$errors = [];
$subscriptionMembers = [];
$subscriptionPlans = [];
$dataUnavailable = false;

try {
    $subscriptionMembers = fitlife_subscription_members($conn, $gymId);
    $subscriptionPlans = fitlife_subscription_plans($conn, $gymId);
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife subscription form choices failed: ' . $exception->getMessage());
    $dataUnavailable = true;
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
            $plan = fitlife_subscription_plan($conn, $planId, $gymId);
            if ($member === null) {
                $errors[] = 'The selected member is unavailable for this gym.';
            }
            if ($plan === null) {
                $errors[] = 'The selected membership plan is unavailable or inactive for this gym.';
            }

            if ($errors === [] && $plan !== null) {
                $status = $values['end_date'] < date('Y-m-d') ? 'expired' : 'active';
                $notes = $values['notes'] !== '' ? $values['notes'] : null;
                $price = (string)$plan['price'];
                $currency = (string)$plan['currency'];
                $stmt = $conn->prepare(
                    'INSERT INTO subscriptions
                        (gym_id, member_id, membership_plan_id, start_date, end_date, status,
                         price_snapshot, currency_snapshot, notes, created_by, updated_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->bind_param(
                    'iiissssssii',
                    $gymId,
                    $memberId,
                    $planId,
                    $values['start_date'],
                    $values['end_date'],
                    $status,
                    $price,
                    $currency,
                    $notes,
                    $userId,
                    $userId
                );
                $stmt->execute();
                $subscriptionId = $conn->insert_id;
                $stmt->close();

                fitlife_flash('success', 'Subscription was created successfully. No payment was recorded.');
                fitlife_redirect('views/dashboard/subscription.php?id=' . $subscriptionId);
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('FitLife subscription creation failed: ' . $exception->getMessage());
            $errors[] = 'The subscription could not be saved right now. Please try again.';
        }
    }
}

$subscriptionFormAction = fitlife_url('views/dashboard/subscription-add.php');
$subscriptionFormSubmit = 'Create Subscription';
$subscriptionCancelUrl = fitlife_url('views/dashboard/subscriptions.php');
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow">Subscriptions</span><h1>Add Subscription</h1><p>Connect one member to a plan and preserve the plan price at the time of creation.</p></div></section>
<?php if ($dataUnavailable): ?>
    <section class="content-card unavailable-card"><h2>Subscriptions are temporarily unavailable</h2><p>Please confirm the Phase 1 Step 4 migration has been imported, then try again.</p></section>
<?php else: ?>
    <section class="content-card form-card"><?php require __DIR__ . '/includes/subscription-form.php'; ?></section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
