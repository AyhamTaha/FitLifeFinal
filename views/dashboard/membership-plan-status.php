<?php

declare(strict_types=1);

$pageTitle = 'Update Membership Plan';
$activeDashboardNav = 'Membership Plans';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/membership-plans.php';

fitlife_require_plan_manager($currentStaff);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '<h1>Method not allowed</h1><p>Membership plan status can only be changed from the dashboard.</p>';
    exit;
}

fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
$planId = fitlife_plan_id($_POST['id'] ?? null);
$action = (string)($_POST['action'] ?? '');

if ($planId === null || !in_array($action, ['deactivate', 'reactivate'], true)) {
    fitlife_flash('error', 'The requested membership plan is unavailable.');
    fitlife_redirect('views/dashboard/membership-plans.php');
}

try {
    $gymId = (int)$currentStaff['gym_id'];
    $userId = (int)$_SESSION['user_id'];
    $isActive = $action === 'reactivate' ? 1 : 0;
    $stmt = $conn->prepare(
        'UPDATE membership_plans
         SET is_active = ?, updated_by = ?, updated_at = CURRENT_TIMESTAMP
         WHERE id = ? AND gym_id = ?'
    );
    $stmt->bind_param('iiii', $isActive, $userId, $planId, $gymId);
    $stmt->execute();
    $found = $stmt->affected_rows === 1;
    $stmt->close();

    if ($found) {
        fitlife_flash('success', $isActive === 1 ? 'Membership plan reactivated successfully.' : 'Membership plan deactivated successfully.');
        fitlife_redirect('views/dashboard/membership-plan.php?id=' . $planId);
    }

    $plan = fitlife_find_plan($conn, $planId, $gymId);
    if ($plan !== null && (int)$plan['is_active'] === $isActive) {
        fitlife_flash('success', $isActive === 1 ? 'Membership plan is already active.' : 'Membership plan is already inactive.');
        fitlife_redirect('views/dashboard/membership-plan.php?id=' . $planId);
    }

    fitlife_flash('error', 'The requested membership plan is unavailable.');
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife membership plan status update failed: ' . $exception->getMessage());
    fitlife_flash('error', 'The membership plan status could not be changed right now. Please try again.');
}

fitlife_redirect('views/dashboard/membership-plans.php');
