<?php

declare(strict_types=1);

$pageTitle = 'Add Member';
$activeDashboardNav = 'Members';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/members.php';

fitlife_require_member_manager($currentStaff);

$gymId = (int)$currentStaff['gym_id'];
$userId = (int)$_SESSION['user_id'];
$values = fitlife_member_empty_values();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
    $validation = fitlife_validate_member_input($_POST);
    $values = $validation['values'];
    $errors = $validation['errors'];

    if ($errors === []) {
        try {
            $conn->begin_transaction();

            $gymLockStmt = $conn->prepare('SELECT id FROM gyms WHERE id = ? FOR UPDATE');
            $gymLockStmt->bind_param('i', $gymId);
            $gymLockStmt->execute();
            $gymExists = $gymLockStmt->get_result()->fetch_assoc();
            $gymLockStmt->close();
            if (!$gymExists) {
                throw new DomainException('The gym is unavailable.');
            }

            $numberStmt = $conn->prepare(
                "SELECT COALESCE(MAX(CAST(SUBSTRING(member_number, 5) AS UNSIGNED)), 0) + 1 AS next_number
                 FROM members
                 WHERE gym_id = ? AND member_number REGEXP '^FIT-[0-9]+$'"
            );
            $numberStmt->bind_param('i', $gymId);
            $numberStmt->execute();
            $nextNumber = (int)$numberStmt->get_result()->fetch_assoc()['next_number'];
            $numberStmt->close();
            $memberNumber = sprintf('FIT-%06d', $nextNumber);

            $email = $values['email'] !== '' ? $values['email'] : null;
            $dateOfBirth = $values['date_of_birth'] !== '' ? $values['date_of_birth'] : null;
            $gender = $values['gender'] !== '' ? $values['gender'] : null;
            $emergencyName = $values['emergency_contact_name'] !== '' ? $values['emergency_contact_name'] : null;
            $emergencyPhone = $values['emergency_contact_phone'] !== '' ? $values['emergency_contact_phone'] : null;
            $notes = $values['notes'] !== '' ? $values['notes'] : null;

            $insertStmt = $conn->prepare(
                'INSERT INTO members
                    (gym_id, member_number, first_name, last_name, phone, email, date_of_birth, gender,
                     emergency_contact_name, emergency_contact_phone, join_date, status, notes, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insertStmt->bind_param(
                'issssssssssssi',
                $gymId,
                $memberNumber,
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
                $userId
            );
            $insertStmt->execute();
            $memberId = $conn->insert_id;
            $insertStmt->close();
            $conn->commit();

            fitlife_flash('success', 'Member ' . $memberNumber . ' was added successfully.');
            fitlife_redirect('views/dashboard/member.php?id=' . $memberId);
        } catch (DomainException $exception) {
            $conn->rollback();
            $errors[] = 'The member could not be added because the gym is unavailable.';
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            error_log('FitLife member creation failed: ' . $exception->getMessage());
            $errors[] = $exception->getCode() === 1062
                ? 'A member number conflict occurred. Please submit the form again.'
                : 'The member could not be saved right now. Please try again.';
        }
    }
}

$memberFormAction = fitlife_url('views/dashboard/member-add.php');
$memberFormSubmit = 'Add Member';
$memberCancelUrl = fitlife_url('views/dashboard/members.php');
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow">Member directory</span><h1>Add Member</h1><p>The member number will be generated securely after submission.</p></div></section>
<section class="content-card form-card">
    <?php require __DIR__ . '/includes/member-form.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
