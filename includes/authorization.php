<?php

declare(strict_types=1);

require_once __DIR__ . '/security.php';

/**
 * @return array{gym_id: int, gym_name: string, role: string}|null
 */
function fitlife_active_staff_assignment(mysqli $conn, int $userId): ?array
{
    $stmt = $conn->prepare(
        "SELECT gs.gym_id, g.name AS gym_name, gs.role
         FROM gym_staff AS gs
         INNER JOIN gyms AS g ON g.id = gs.gym_id
         WHERE gs.user_id = ? AND gs.is_active = 1 AND g.status = 'active'
         ORDER BY gs.id ASC
         LIMIT 1"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $assignment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$assignment) {
        return null;
    }

    return [
        'gym_id' => (int)$assignment['gym_id'],
        'gym_name' => (string)$assignment['gym_name'],
        'role' => (string)$assignment['role'],
    ];
}

/**
 * @param array<int, string> $allowedRoles
 * @return array{gym_id: int, gym_name: string, role: string}
 */
function fitlife_require_gym_staff(mysqli $conn, array $allowedRoles = []): array
{
    fitlife_require_login();

    try {
        $assignment = fitlife_active_staff_assignment($conn, (int)$_SESSION['user_id']);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife authorization lookup failed: ' . $exception->getMessage());
        http_response_code(503);
        echo '<h1>Dashboard temporarily unavailable</h1><p>Please try again later.</p>';
        exit;
    }

    if ($assignment === null) {
        unset($_SESSION['gym_id'], $_SESSION['gym_name'], $_SESSION['gym_role']);
        fitlife_flash('error', 'Set up your gym before opening the dashboard.');
        fitlife_redirect('views/gym/setup.php');
    }

    if ($allowedRoles !== [] && !in_array($assignment['role'], $allowedRoles, true)) {
        http_response_code(403);
        echo '<h1>Access denied</h1><p>Your staff role does not allow this action.</p>';
        exit;
    }

    $_SESSION['gym_id'] = $assignment['gym_id'];
    $_SESSION['gym_name'] = $assignment['gym_name'];
    $_SESSION['gym_role'] = $assignment['role'];

    return $assignment;
}
