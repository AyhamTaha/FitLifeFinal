<?php

declare(strict_types=1);

$pageTitle = 'Add Membership Plan';
$activeDashboardNav = 'Membership Plans';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/membership-plans.php';

fitlife_require_plan_manager($currentStaff);

$gymId = (int)$currentStaff['gym_id'];
$userId = (int)$_SESSION['user_id'];
$values = fitlife_plan_empty_values();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
    $validation = fitlife_validate_plan_input($_POST);
    $values = $validation['values'];
    $errors = $validation['errors'];

    if ($errors === []) {
        try {
            if (fitlife_plan_name_exists($conn, $gymId, $values['name'])) {
                $errors[] = 'A membership plan with this name already exists for your gym.';
            } else {
                $description = $values['description'] !== '' ? $values['description'] : null;
                $durationValue = (int)$values['duration_value'];
                $price = $values['price'];
                $freezeDays = (int)$values['freeze_days_allowed'];
                $visitLimit = $values['visit_limit'] !== '' ? (int)$values['visit_limit'] : null;
                $isActive = (int)$values['is_active'];

                $stmt = $conn->prepare(
                    'INSERT INTO membership_plans
                        (gym_id, name, description, duration_value, duration_unit, price, currency,
                         freeze_days_allowed, visit_limit, is_active, created_by, updated_by)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->bind_param(
                    'ississsiiiii',
                    $gymId,
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
                    $userId
                );
                $stmt->execute();
                $planId = $conn->insert_id;
                $stmt->close();

                fitlife_flash('success', 'Membership plan was added successfully.');
                fitlife_redirect('views/dashboard/membership-plan.php?id=' . $planId);
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('FitLife membership plan creation failed: ' . $exception->getMessage());
            $errors[] = $exception->getCode() === 1062
                ? 'A membership plan with this name already exists for your gym.'
                : 'The membership plan could not be saved right now. Please try again.';
        }
    }
}

$planFormAction = fitlife_url('views/dashboard/membership-plan-add.php');
$planFormSubmit = 'Add Plan';
$planCancelUrl = fitlife_url('views/dashboard/membership-plans.php');
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow">Membership Plans</span><h1>Add Membership Plan</h1><p>Create a reusable plan for <?= fitlife_escape($currentStaff['gym_name']) ?>.</p></div></section>
<section class="content-card form-card"><?php require __DIR__ . '/includes/membership-plan-form.php'; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
