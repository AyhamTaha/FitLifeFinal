<?php

declare(strict_types=1);

const FITLIFE_PLAN_MANAGERS = ['owner', 'manager'];
const FITLIFE_PLAN_DURATION_UNITS = ['day', 'week', 'month', 'year'];
const FITLIFE_PLAN_CURRENCIES = ['USD', 'LBP'];

function fitlife_can_manage_plans(string $role): bool
{
    return in_array($role, FITLIFE_PLAN_MANAGERS, true);
}

function fitlife_require_plan_manager(array $currentStaff): void
{
    if (!fitlife_can_manage_plans((string)($currentStaff['role'] ?? ''))) {
        http_response_code(403);
        echo '<h1>Access denied</h1><p>Your staff role does not allow this action.</p>';
        exit;
    }
}

/** @return array<string, string> */
function fitlife_plan_empty_values(): array
{
    return [
        'name' => '',
        'description' => '',
        'duration_value' => '',
        'duration_unit' => 'month',
        'price' => '',
        'currency' => 'USD',
        'freeze_days_allowed' => '0',
        'visit_limit' => '',
        'is_active' => '1',
    ];
}

/**
 * @param array<string, mixed> $source
 * @return array{values: array<string, string>, errors: array<int, string>}
 */
function fitlife_validate_plan_input(array $source): array
{
    $values = fitlife_plan_empty_values();
    foreach (array_keys($values) as $field) {
        $values[$field] = trim((string)($source[$field] ?? ''));
    }

    $errors = [];
    if ($values['name'] === '' || strlen($values['name']) > 150) {
        $errors[] = 'Plan name is required and must be 150 characters or fewer.';
    }
    if (strlen($values['description']) > 5000) {
        $errors[] = 'Description must be 5,000 characters or fewer.';
    }
    if (!fitlife_plan_integer_in_range($values['duration_value'], 1)) {
        $errors[] = 'Duration value must be a positive whole number.';
    }
    if (!in_array($values['duration_unit'], FITLIFE_PLAN_DURATION_UNITS, true)) {
        $errors[] = 'Select a valid duration unit.';
    }
    if (!preg_match('/^\d{1,10}(?:\.\d{1,2})?$/', $values['price'])
        || (float)$values['price'] > 9999999999.99) {
        $errors[] = 'Price must be zero or greater, with no more than two decimal places.';
    }
    if (!in_array($values['currency'], FITLIFE_PLAN_CURRENCIES, true)) {
        $errors[] = 'Select USD or LBP as the currency.';
    }
    if (!fitlife_plan_integer_in_range($values['freeze_days_allowed'], 0)) {
        $errors[] = 'Freeze days allowed must be zero or a positive whole number.';
    }
    if ($values['visit_limit'] !== '' && !fitlife_plan_integer_in_range($values['visit_limit'], 1)) {
        $errors[] = 'Visit limit must be blank for unlimited visits or a positive whole number.';
    }
    if (!in_array($values['is_active'], ['0', '1'], true)) {
        $errors[] = 'Select a valid plan status.';
    }

    return ['values' => $values, 'errors' => $errors];
}

function fitlife_plan_integer_in_range(string $value, int $minimum): bool
{
    return filter_var(
        $value,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => $minimum, 'max_range' => 2147483647]]
    ) !== false;
}

function fitlife_plan_id(mixed $value): ?int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : (int)$id;
}

/** @return array<string, mixed>|null */
function fitlife_find_plan(mysqli $conn, int $planId, int $gymId): ?array
{
    $stmt = $conn->prepare(
        'SELECT id, name, description, duration_value, duration_unit, price, currency,
                freeze_days_allowed, visit_limit, is_active, created_at, updated_at
         FROM membership_plans
         WHERE id = ? AND gym_id = ?
         LIMIT 1'
    );
    $stmt->bind_param('ii', $planId, $gymId);
    $stmt->execute();
    $plan = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $plan ?: null;
}

function fitlife_plan_name_exists(mysqli $conn, int $gymId, string $name, ?int $exceptId = null): bool
{
    if ($exceptId === null) {
        $stmt = $conn->prepare('SELECT id FROM membership_plans WHERE gym_id = ? AND name = ? LIMIT 1');
        $stmt->bind_param('is', $gymId, $name);
    } else {
        $stmt = $conn->prepare('SELECT id FROM membership_plans WHERE gym_id = ? AND name = ? AND id <> ? LIMIT 1');
        $stmt->bind_param('isi', $gymId, $name, $exceptId);
    }
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc() !== null;
    $stmt->close();

    return $exists;
}

function fitlife_plan_unavailable(): void
{
    http_response_code(404);
    echo '<section class="page-heading"><div><span class="eyebrow">Membership Plans</span>'
        . '<h1>Plan unavailable</h1><p>The requested plan could not be found or is unavailable.</p></div></section>';
    echo '<section class="content-card"><a class="secondary-button button-link" href="'
        . fitlife_escape(fitlife_url('views/dashboard/membership-plans.php'))
        . '">&larr; Back to Membership Plans</a></section>';
}

function fitlife_plan_duration(array $plan): string
{
    $value = (int)$plan['duration_value'];
    $unit = (string)$plan['duration_unit'];
    return $value . ' ' . $unit . ($value === 1 ? '' : 's');
}

function fitlife_plan_price(array $plan): string
{
    return number_format((float)$plan['price'], 2) . ' ' . (string)$plan['currency'];
}

function fitlife_plan_display_date(?string $value): string
{
    if ($value === null || $value === '') {
        return 'Not available';
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? $value : date('M j, Y, g:i a', $timestamp);
}
