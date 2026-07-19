<?php

declare(strict_types=1);

const FITLIFE_SUBSCRIPTION_MANAGERS = ['owner', 'manager', 'receptionist'];
const FITLIFE_SUBSCRIPTION_STATUSES = ['active', 'expired', 'frozen', 'cancelled'];

function fitlife_can_manage_subscriptions(string $role): bool
{
    return in_array($role, FITLIFE_SUBSCRIPTION_MANAGERS, true);
}

function fitlife_require_subscription_manager(array $currentStaff): void
{
    if (!fitlife_can_manage_subscriptions((string)($currentStaff['role'] ?? ''))) {
        http_response_code(403);
        echo '<h1>Access denied</h1><p>Your staff role does not allow this action.</p>';
        exit;
    }
}

function fitlife_subscription_id(mixed $value): ?int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : (int)$id;
}

/** @return array<string, string> */
function fitlife_subscription_empty_values(): array
{
    return [
        'member_id' => '',
        'membership_plan_id' => '',
        'start_date' => date('Y-m-d'),
        'end_date' => date('Y-m-d', strtotime('+1 month -1 day')),
        'notes' => '',
    ];
}

/**
 * @param array<string, mixed> $source
 * @return array{values: array<string, string>, errors: array<int, string>}
 */
function fitlife_validate_subscription_input(array $source): array
{
    $values = fitlife_subscription_empty_values();
    foreach (array_keys($values) as $field) {
        $values[$field] = trim((string)($source[$field] ?? ''));
    }

    $errors = [];
    if (fitlife_subscription_id($values['member_id']) === null) {
        $errors[] = 'Select a valid member.';
    }
    if (fitlife_subscription_id($values['membership_plan_id']) === null) {
        $errors[] = 'Select a valid membership plan.';
    }
    if (!fitlife_valid_subscription_date($values['start_date'])) {
        $errors[] = 'Enter a valid start date.';
    }
    if (!fitlife_valid_subscription_date($values['end_date'])) {
        $errors[] = 'Enter a valid end date.';
    }
    if (fitlife_valid_subscription_date($values['start_date'])
        && fitlife_valid_subscription_date($values['end_date'])
        && $values['end_date'] < $values['start_date']) {
        $errors[] = 'End date must be on or after the start date.';
    }
    if (strlen($values['notes']) > 5000) {
        $errors[] = 'Notes must be 5,000 characters or fewer.';
    }

    return ['values' => $values, 'errors' => $errors];
}

function fitlife_valid_subscription_date(string $date): bool
{
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    $dateErrors = DateTimeImmutable::getLastErrors();

    return $parsed !== false
        && ($dateErrors === false || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0))
        && $parsed->format('Y-m-d') === $date;
}

/** @return array<string, mixed>|null */
function fitlife_find_subscription(mysqli $conn, int $subscriptionId, int $gymId): ?array
{
    $stmt = $conn->prepare(
        'SELECT s.id, s.member_id, s.membership_plan_id, s.start_date, s.end_date, s.status,
                s.price_snapshot, s.currency_snapshot, s.notes, s.renewed_at, s.frozen_at,
                s.cancelled_at, s.created_at, s.updated_at,
                m.member_number, m.first_name, m.last_name, m.status AS member_status,
                mp.name AS plan_name, mp.duration_value, mp.duration_unit,
                mp.freeze_days_allowed, mp.is_active AS plan_is_active
         FROM subscriptions AS s
         INNER JOIN members AS m
             ON m.id = s.member_id AND m.gym_id = s.gym_id
         INNER JOIN membership_plans AS mp
             ON mp.id = s.membership_plan_id AND mp.gym_id = s.gym_id
         WHERE s.id = ? AND s.gym_id = ?
         LIMIT 1'
    );
    $stmt->bind_param('ii', $subscriptionId, $gymId);
    $stmt->execute();
    $subscription = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $subscription ?: null;
}

/** @return array<string, mixed>|null */
function fitlife_subscription_member(mysqli $conn, int $memberId, int $gymId): ?array
{
    $stmt = $conn->prepare(
        "SELECT id, member_number, first_name, last_name, status
         FROM members
         WHERE id = ? AND gym_id = ? AND status <> 'archived'
         LIMIT 1"
    );
    $stmt->bind_param('ii', $memberId, $gymId);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $member ?: null;
}

/** @return array<string, mixed>|null */
function fitlife_subscription_plan(mysqli $conn, int $planId, int $gymId, bool $requireActive = true): ?array
{
    $sql = 'SELECT id, name, duration_value, duration_unit, price, currency, freeze_days_allowed, is_active
            FROM membership_plans
            WHERE id = ? AND gym_id = ?';
    if ($requireActive) {
        $sql .= ' AND is_active = 1';
    }
    $sql .= ' LIMIT 1';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $planId, $gymId);
    $stmt->execute();
    $plan = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $plan ?: null;
}

/** @return array<int, array<string, mixed>> */
function fitlife_subscription_members(mysqli $conn, int $gymId): array
{
    $stmt = $conn->prepare(
        "SELECT id, member_number, first_name, last_name, status
         FROM members
         WHERE gym_id = ? AND status <> 'archived'
         ORDER BY last_name ASC, first_name ASC, id ASC"
    );
    $stmt->bind_param('i', $gymId);
    $stmt->execute();
    $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $members;
}

/** @return array<int, array<string, mixed>> */
function fitlife_subscription_plans(mysqli $conn, int $gymId, ?int $includePlanId = null): array
{
    if ($includePlanId === null) {
        $stmt = $conn->prepare(
            'SELECT id, name, duration_value, duration_unit, price, currency, freeze_days_allowed, is_active
             FROM membership_plans
             WHERE gym_id = ? AND is_active = 1
             ORDER BY name ASC, id ASC'
        );
        $stmt->bind_param('i', $gymId);
    } else {
        $stmt = $conn->prepare(
            'SELECT id, name, duration_value, duration_unit, price, currency, freeze_days_allowed, is_active
             FROM membership_plans
             WHERE gym_id = ? AND (is_active = 1 OR id = ?)
             ORDER BY is_active DESC, name ASC, id ASC'
        );
        $stmt->bind_param('ii', $gymId, $includePlanId);
    }
    $stmt->execute();
    $plans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $plans;
}

function fitlife_expire_subscriptions(mysqli $conn, int $gymId): void
{
    $stmt = $conn->prepare(
        "UPDATE subscriptions
         SET status = 'expired'
         WHERE gym_id = ? AND status = 'active' AND end_date < CURRENT_DATE"
    );
    $stmt->bind_param('i', $gymId);
    $stmt->execute();
    $stmt->close();
}

function fitlife_subscription_unavailable(): void
{
    http_response_code(404);
    echo '<section class="page-heading"><div><span class="eyebrow">Subscriptions</span>'
        . '<h1>Subscription unavailable</h1><p>The requested subscription could not be found or is unavailable.</p></div></section>';
    echo '<section class="content-card"><a class="secondary-button button-link" href="'
        . fitlife_escape(fitlife_url('views/dashboard/subscriptions.php'))
        . '">&larr; Back to Subscriptions</a></section>';
}

function fitlife_subscription_status_label(string $status): string
{
    return ucfirst($status);
}

function fitlife_subscription_display_date(?string $value, bool $withTime = false): string
{
    if ($value === null || $value === '') {
        return 'Not available';
    }

    $timestamp = strtotime($value);
    return $timestamp === false ? $value : date($withTime ? 'M j, Y, g:i a' : 'M j, Y', $timestamp);
}

function fitlife_subscription_price(array $subscription): string
{
    return number_format((float)$subscription['price_snapshot'], 2)
        . ' ' . (string)$subscription['currency_snapshot'];
}

function fitlife_subscription_renewal_end(string $currentEnd, array $plan): string
{
    $today = new DateTimeImmutable('today');
    $currentEndDate = new DateTimeImmutable($currentEnd);
    $renewalStart = $currentEndDate >= $today ? $currentEndDate->modify('+1 day') : $today;
    $value = (int)$plan['duration_value'];
    $unit = (string)$plan['duration_unit'];

    return $renewalStart->modify('+' . $value . ' ' . $unit)->modify('-1 day')->format('Y-m-d');
}
