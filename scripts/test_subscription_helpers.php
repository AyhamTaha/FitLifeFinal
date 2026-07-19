<?php

declare(strict_types=1);

require __DIR__ . '/../includes/subscriptions.php';

$valid = fitlife_validate_subscription_input([
    'member_id' => '1',
    'membership_plan_id' => '2',
    'start_date' => '2026-07-01',
    'end_date' => '2026-07-31',
    'notes' => '',
]);
if ($valid['errors'] !== []) {
    fwrite(STDERR, 'Valid input failed: ' . implode(' | ', $valid['errors']) . PHP_EOL);
    exit(1);
}

$invalid = fitlife_validate_subscription_input([
    'member_id' => '0',
    'membership_plan_id' => 'x',
    'start_date' => '2026-07-31',
    'end_date' => '2026-07-01',
    'notes' => str_repeat('x', 5001),
]);
if (count($invalid['errors']) !== 4) {
    fwrite(STDERR, 'Expected four validation errors, got ' . count($invalid['errors']) . PHP_EOL);
    exit(1);
}

$renewalEnd = fitlife_subscription_renewal_end('2099-07-31', [
    'duration_value' => 1,
    'duration_unit' => 'month',
]);
if ($renewalEnd !== '2099-08-31') {
    fwrite(STDERR, 'Unexpected renewal end: ' . $renewalEnd . PHP_EOL);
    exit(1);
}

echo 'Subscription helper checks passed.' . PHP_EOL;
