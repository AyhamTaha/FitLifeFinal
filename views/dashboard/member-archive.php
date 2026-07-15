<?php

declare(strict_types=1);

$pageTitle = 'Archive Member';
$activeDashboardNav = 'Members';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/members.php';

fitlife_require_member_manager($currentStaff);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '<h1>Method not allowed</h1><p>Members can only be archived from their profile.</p>';
    exit;
}

fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);
$memberId = fitlife_member_id($_POST['id'] ?? null);
if ($memberId === null) {
    fitlife_flash('error', 'The requested member is unavailable.');
    fitlife_redirect('views/dashboard/members.php');
}

try {
    $gymId = (int)$currentStaff['gym_id'];
    $userId = (int)$_SESSION['user_id'];
    $archiveStmt = $conn->prepare(
        "UPDATE members
         SET status = 'archived', archived_at = CURRENT_TIMESTAMP, archived_by = ?, updated_at = CURRENT_TIMESTAMP
         WHERE id = ? AND gym_id = ? AND status <> 'archived'"
    );
    $archiveStmt->bind_param('iii', $userId, $memberId, $gymId);
    $archiveStmt->execute();
    $archived = $archiveStmt->affected_rows === 1;
    $archiveStmt->close();

    fitlife_flash($archived ? 'success' : 'error', $archived ? 'Member archived successfully.' : 'The requested member is unavailable.');
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife member archive failed: ' . $exception->getMessage());
    fitlife_flash('error', 'The member could not be archived right now. Please try again.');
}

fitlife_redirect('views/dashboard/members.php');
