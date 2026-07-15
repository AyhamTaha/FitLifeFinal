<?php

declare(strict_types=1);

$pageTitle = 'Edit Member';
$activeDashboardNav = 'Members';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/members.php';

fitlife_require_member_manager($currentStaff);
$memberId = fitlife_member_id($_GET['id'] ?? null);
$member = null;
$dataUnavailable = false;
if ($memberId !== null) {
    try {
        $member = fitlife_find_member($conn, $memberId, (int)$currentStaff['gym_id']);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife member edit lookup failed: ' . $exception->getMessage());
        $dataUnavailable = true;
    }
}

if ($dataUnavailable) {
    require __DIR__ . '/includes/header.php';
    echo '<section class="content-card unavailable-card"><h1>Member temporarily unavailable</h1><p>Please try again later.</p></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}
if ($member === null || $member['status'] === 'archived') {
    require __DIR__ . '/includes/header.php';
    fitlife_member_unavailable();
    require __DIR__ . '/includes/footer.php';
    exit;
}

$values = array_intersect_key(array_map(static fn($value): string => $value === null ? '' : (string)$value, $member), fitlife_member_empty_values());
$values = array_merge(fitlife_member_empty_values(), $values);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
    $validation = fitlife_validate_member_input($_POST);
    $values = $validation['values'];
    $errors = $validation['errors'];

    if ($errors === []) {
        try {
            $email = $values['email'] !== '' ? $values['email'] : null;
            $dateOfBirth = $values['date_of_birth'] !== '' ? $values['date_of_birth'] : null;
            $gender = $values['gender'] !== '' ? $values['gender'] : null;
            $emergencyName = $values['emergency_contact_name'] !== '' ? $values['emergency_contact_name'] : null;
            $emergencyPhone = $values['emergency_contact_phone'] !== '' ? $values['emergency_contact_phone'] : null;
            $notes = $values['notes'] !== '' ? $values['notes'] : null;
            $gymId = (int)$currentStaff['gym_id'];

            $updateStmt = $conn->prepare(
                "UPDATE members
                 SET first_name = ?, last_name = ?, phone = ?, email = ?, date_of_birth = ?, gender = ?,
                     emergency_contact_name = ?, emergency_contact_phone = ?, join_date = ?, status = ?, notes = ?,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = ? AND gym_id = ? AND status <> 'archived'"
            );
            $updateStmt->bind_param(
                'sssssssssssii',
                $values['first_name'],
                $values['last_name'],
                $values['phone'],
                $email,
                $dateOfBirth,
                $gender,
                $emergencyName,
                $emergencyPhone,
                $values['join_date'],
                $values['status'],
                $notes,
                $memberId,
                $gymId
            );
            $updateStmt->execute();
            $updateStmt->close();

            fitlife_flash('success', 'Member details were updated successfully.');
            fitlife_redirect('views/dashboard/member.php?id=' . $memberId);
        } catch (mysqli_sql_exception $exception) {
            error_log('FitLife member update failed: ' . $exception->getMessage());
            $errors[] = 'The member could not be updated right now. Please try again.';
        }
    }
}

$memberFormAction = fitlife_url('views/dashboard/member-edit.php?id=' . $memberId);
$memberFormSubmit = 'Save Changes';
$memberCancelUrl = fitlife_url('views/dashboard/member.php?id=' . $memberId);
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow"><?= fitlife_escape($member['member_number']) ?></span><h1>Edit Member</h1><p>Update contact and member details. The member number cannot be changed.</p></div></section>
<section class="content-card form-card"><?php require __DIR__ . '/includes/member-form.php'; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
