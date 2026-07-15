<?php

declare(strict_types=1);

$pageTitle = 'Edit Membership Plan';
$activeDashboardNav = 'Membership Plans';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/membership-plans.php';

fitlife_require_plan_manager($currentStaff);
$gymId = (int)$currentStaff['gym_id'];
$planId = fitlife_plan_id($_GET['id'] ?? null);
$plan = null;
$dataUnavailable = false;

if ($planId !== null) {
    try {
        $plan = fitlife_find_plan($conn, $planId, $gymId);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife membership plan edit lookup failed: ' . $exception->getMessage());
        $dataUnavailable = true;
    }
}

if ($dataUnavailable) {
    require __DIR__ . '/includes/header.php';
    echo '<section class="content-card unavailable-card"><h1>Membership plan temporarily unavailable</h1><p>Please try again later.</p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
if ($plan === null) {
    require __DIR__ . '/includes/header.php';
    fitlife_plan_unavailable();
    require __DIR__ . '/includes/footer.php';
    exit;
}

$values = fitlife_plan_empty_values();
foreach (array_keys($values) as $field) {
    $values[$field] = $plan[$field] === null ? '' : (string)$plan[$field];
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
    $validation = fitlife_validate_plan_input($_POST);
    $values = $validation['values'];
    $errors = $validation['errors'];

    if ($errors === []) {
        try {
            if (fitlife_plan_name_exists($conn, $gymId, $values['name'], $planId)) {
                $errors[] = 'A membership plan with this name already exists for your gym.';
            } else {
                $description = $values['description'] !== '' ? $values['description'] : null;
                $durationValue = (int)$values['duration_value'];
                $price = $values['price'];
                $freezeDays = (int)$values['freeze_days_allowed'];
                $visitLimit = $values['visit_limit'] !== '' ? (int)$values['visit_limit'] : null;
                $isActive = (int)$values['is_active'];
                $userId = (int)$_SESSION['user_id'];

                $stmt = $conn->prepare(
                    'UPDATE membership_plans
                     SET name = ?, description = ?, duration_value = ?, duration_unit = ?, price = ?,
                         currency = ?, freeze_days_allowed = ?, visit_limit = ?, is_active = ?,
                         updated_by = ?, updated_at = CURRENT_TIMESTAMP
                     WHERE id = ? AND gym_id = ?'
                );
                $stmt->bind_param(
                    'ssisssiiiiii',
                    $values['name'],
                    $description,
                    $durationValue,
                    $values['duration_unit'],
                    $price,
                    $values['currency'],
                    $freezeDays,
                    $visitLimit,
                    $isActive,
                    $userId,
                    $planId,
                    $gymId
                );
                $stmt->execute();
                $stmt->close();

                fitlife_flash('success', 'Membership plan was updated successfully.');
                fitlife_redirect('views/dashboard/membership-plan.php?id=' . $planId);
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('FitLife membership plan update failed: ' . $exception->getMessage());
            $errors[] = $exception->getCode() === 1062
                ? 'A membership plan with this name already exists for your gym.'
                : 'The membership plan could not be updated right now. Please try again.';
        }
    }
}

$planFormAction = fitlife_url('views/dashboard/membership-plan-edit.php?id=' . $planId);
$planFormSubmit = 'Save Changes';
$planCancelUrl = fitlife_url('views/dashboard/membership-plan.php?id=' . $planId);
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow">Membership Plans</span><h1>Edit <?= fitlife_escape($plan['name']) ?></h1><p>Update this plan without changing its gym assignment.</p></div></section>
<section class="content-card form-card"><?php require __DIR__ . '/includes/membership-plan-form.php'; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
