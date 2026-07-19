<?php

declare(strict_types=1);

$pageTitle = 'Update Subscription';
$activeDashboardNav = 'Subscriptions';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/subscriptions.php';

fitlife_require_subscription_manager($currentStaff);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '<h1>Method not allowed</h1><p>Subscription status can only be changed from the dashboard.</p>';
    exit;
}

fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
$subscriptionId = fitlife_subscription_id($_POST['id'] ?? null);
$action = (string)($_POST['action'] ?? '');
if ($subscriptionId === null || !in_array($action, ['freeze', 'cancel', 'reactivate'], true)) {
    fitlife_flash('error', 'The requested subscription action is unavailable.');
    fitlife_redirect('views/dashboard/subscriptions.php');
}

$gymId = (int)$currentStaff['gym_id'];
$userId = (int)$_SESSION['user_id'];
try {
    fitlife_expire_subscriptions($conn, $gymId);
    $conn->begin_transaction();
    $stmt = $conn->prepare(
        'SELECT s.status, s.end_date, mp.freeze_days_allowed
         FROM subscriptions AS s
         INNER JOIN membership_plans AS mp ON mp.id = s.membership_plan_id AND mp.gym_id = s.gym_id
         WHERE s.id = ? AND s.gym_id = ?
         FOR UPDATE'
    );
    $stmt->bind_param('ii', $subscriptionId, $gymId);
    $stmt->execute();
    $subscription = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$subscription) {
        throw new DomainException('unavailable');
    }

    $currentStatus = (string)$subscription['status'];
    if ($action === 'freeze') {
        if ($currentStatus !== 'active' || (int)$subscription['freeze_days_allowed'] < 1) {
            throw new DomainException('freeze');
        }
        $update = $conn->prepare(
            "UPDATE subscriptions SET status = 'frozen', frozen_at = CURRENT_TIMESTAMP, updated_by = ?
             WHERE id = ? AND gym_id = ?"
        );
        $successMessage = 'Subscription was frozen successfully.';
    } elseif ($action === 'cancel') {
        if (!in_array($currentStatus, ['active', 'frozen'], true)) {
            throw new DomainException('cancel');
        }
        $update = $conn->prepare(
            "UPDATE subscriptions SET status = 'cancelled', cancelled_at = CURRENT_TIMESTAMP, updated_by = ?
             WHERE id = ? AND gym_id = ?"
        );
        $successMessage = 'Subscription was cancelled successfully.';
    } else {
        if (!in_array($currentStatus, ['frozen', 'cancelled'], true)
            || (string)$subscription['end_date'] < date('Y-m-d')) {
            throw new DomainException('reactivate');
        }
        $update = $conn->prepare(
            "UPDATE subscriptions SET status = 'active', updated_by = ?
             WHERE id = ? AND gym_id = ?"
        );
        $successMessage = 'Subscription was reactivated successfully.';
    }

    $update->bind_param('iii', $userId, $subscriptionId, $gymId);
    $update->execute();
    $update->close();
    $conn->commit();
    fitlife_flash('success', $successMessage);
    fitlife_redirect('views/dashboard/subscription.php?id=' . $subscriptionId);
} catch (DomainException $exception) {
    $conn->rollback();
    $messages = [
        'freeze' => 'Only a current active subscription whose plan includes freeze days can be frozen.',
        'cancel' => 'Only an active or frozen subscription can be cancelled.',
        'reactivate' => 'Only a current frozen or cancelled subscription can be reactivated. Renew an ended subscription instead.',
        'unavailable' => 'The requested subscription is unavailable.',
    ];
    fitlife_flash('error', $messages[$exception->getMessage()] ?? 'The subscription status could not be changed.');
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    error_log('FitLife subscription status update failed: ' . $exception->getMessage());
    fitlife_flash('error', 'The subscription status could not be changed right now. Please try again.');
}

fitlife_redirect('views/dashboard/subscriptions.php');
