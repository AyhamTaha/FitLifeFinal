<?php

declare(strict_types=1);

const FITLIFE_MEMBER_MANAGERS = ['owner', 'manager', 'receptionist'];
const FITLIFE_MEMBER_STATUSES = ['active', 'inactive', 'archived'];
const FITLIFE_MEMBER_GENDERS = ['male', 'female', 'other', 'prefer_not_to_say'];

function fitlife_can_manage_members(string $role): bool
{
    return in_array($role, FITLIFE_MEMBER_MANAGERS, true);
}

function fitlife_require_member_manager(array $currentStaff): void
{
    if (!fitlife_can_manage_members((string)($currentStaff['role'] ?? ''))) {
        http_response_code(403);
        echo '<h1>Access denied</h1><p>Your staff role does not allow this action.</p>';
        exit;
    }
}

/** @return array<string, string> */
function fitlife_member_empty_values(): array
{
    return [
        'first_name' => '',
        'last_name' => '',
        'phone' => '',
        'email' => '',
        'date_of_birth' => '',
        'gender' => '',
        'emergency_contact_name' => '',
        'emergency_contact_phone' => '',
        'join_date' => date('Y-m-d'),
        'status' => 'active',
        'notes' => '',
    ];
}

/**
 * @param array<string, mixed> $source
 * @return array{values: array<string, string>, errors: array<int, string>}
 */
function fitlife_validate_member_input(array $source): array
{
    $values = fitlife_member_empty_values();
    foreach (array_keys($values) as $field) {
        $values[$field] = trim((string)($source[$field] ?? ''));
    }

    $errors = [];
    if ($values['first_name'] === '' || strlen($values['first_name']) > 100) {
        $errors[] = 'First name is required and must be 100 characters or fewer.';
    }
    if ($values['last_name'] === '' || strlen($values['last_name']) > 100) {
        $errors[] = 'Last name is required and must be 100 characters or fewer.';
    }
    if (!fitlife_valid_phone($values['phone'], true)) {
        $errors[] = 'Enter a valid phone number using 5 to 30 digits and common phone symbols.';
    }
    if ($values['email'] !== ''
        && (strlen($values['email']) > 150 || filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false)) {
        $errors[] = 'Enter a valid email address or leave it blank.';
    }
    if (!fitlife_valid_member_date($values['date_of_birth'], true)) {
        $errors[] = 'Enter a valid date of birth.';
    } elseif ($values['date_of_birth'] !== '' && $values['date_of_birth'] > date('Y-m-d')) {
        $errors[] = 'Date of birth cannot be in the future.';
    }
    if ($values['gender'] !== '' && !in_array($values['gender'], FITLIFE_MEMBER_GENDERS, true)) {
        $errors[] = 'Select a valid gender option.';
    }
    if (strlen($values['emergency_contact_name']) > 150) {
        $errors[] = 'Emergency contact name must be 150 characters or fewer.';
    }
    if (!fitlife_valid_phone($values['emergency_contact_phone'], false)) {
        $errors[] = 'Enter a valid emergency contact phone number or leave it blank.';
    }
    if (!fitlife_valid_member_date($values['join_date'], false)) {
        $errors[] = 'Enter a valid join date.';
    }
    if (!in_array($values['status'], ['active', 'inactive'], true)) {
        $errors[] = 'Select active or inactive as the member status.';
    }
    if (strlen($values['notes']) > 5000) {
        $errors[] = 'Notes must be 5,000 characters or fewer.';
    }

    return ['values' => $values, 'errors' => $errors];
}

function fitlife_valid_phone(string $phone, bool $required): bool
{
    if ($phone === '') {
        return !$required;
    }

    return strlen($phone) <= 30 && preg_match('/^[0-9+()\-\s.]{5,30}$/', $phone) === 1;
}

function fitlife_valid_member_date(string $date, bool $optional): bool
{
    if ($date === '') {
        return $optional;
    }

    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    $dateErrors = DateTimeImmutable::getLastErrors();

    return $parsed !== false
        && ($dateErrors === false || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0))
        && $parsed->format('Y-m-d') === $date;
}

function fitlife_member_id(mixed $value): ?int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : (int)$id;
}

/** @return array<string, mixed>|null */
function fitlife_find_member(mysqli $conn, int $memberId, int $gymId): ?array
{
    $stmt = $conn->prepare(
        'SELECT id, member_number, first_name, last_name, phone, email, date_of_birth, gender,
                emergency_contact_name, emergency_contact_phone, join_date, status, notes,
                created_at, updated_at, archived_at
         FROM members
         WHERE id = ? AND gym_id = ?
         LIMIT 1'
    );
    $stmt->bind_param('ii', $memberId, $gymId);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $member ?: null;
}

function fitlife_member_unavailable(): void
{
    http_response_code(404);
    echo '<section class="page-heading"><div><span class="eyebrow">Members</span>'
        . '<h1>Member unavailable</h1><p>The requested member could not be found or is unavailable.</p></div></section>';
    echo '<section class="content-card"><a class="secondary-button" href="'
        . fitlife_escape(fitlife_url('views/dashboard/members.php')) . '">&larr; Back to Members</a></section>';
}

function fitlife_member_display_date(?string $value, bool $withTime = false): string
{
    if ($value === null || $value === '') {
        return 'Not provided';
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? $value : date($withTime ? 'M j, Y, g:i a' : 'M j, Y', $timestamp);
}

function fitlife_member_status_label(string $status): string
{
    return ucfirst(str_replace('_', ' ', $status));
}

