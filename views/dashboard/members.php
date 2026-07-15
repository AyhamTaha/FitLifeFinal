<?php

declare(strict_types=1);

$pageTitle = 'Members';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/members.php';

$gymId = (int)$currentStaff['gym_id'];
$canManageMembers = fitlife_can_manage_members($currentStaff['role']);
$search = trim((string)($_GET['q'] ?? ''));
if (strlen($search) > 100) {
    $search = substr($search, 0, 100);
}
$status = (string)($_GET['status'] ?? '');
if (!in_array($status, FITLIFE_MEMBER_STATUSES, true)) {
    $status = '';
}
$requestedPage = fitlife_member_id($_GET['page'] ?? 1) ?? 1;
$perPage = 20;
$memberCount = 0;
$pageCount = 1;
$currentPage = 1;
$members = [];
$dataUnavailable = false;

try {
    $where = "gym_id = ? AND CONCAT_WS(' ', member_number, first_name, last_name, phone, email) LIKE CONCAT('%', ?, '%')";
    if ($status === '') {
        $where .= " AND status <> 'archived'";
    } else {
        $where .= ' AND status = ?';
    }

    $countStmt = $conn->prepare('SELECT COUNT(*) AS total FROM members WHERE ' . $where);
    if ($status === '') {
        $countStmt->bind_param('is', $gymId, $search);
    } else {
        $countStmt->bind_param('iss', $gymId, $search, $status);
    }
    $countStmt->execute();
    $memberCount = (int)$countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();

    $pageCount = max(1, (int)ceil($memberCount / $perPage));
    $currentPage = min($requestedPage, $pageCount);
    $offset = ($currentPage - 1) * $perPage;

    $listStmt = $conn->prepare(
        'SELECT id, member_number, first_name, last_name, phone, email, join_date, status
         FROM members
         WHERE ' . $where . '
         ORDER BY last_name ASC, first_name ASC, id ASC
         LIMIT ? OFFSET ?'
    );
    if ($status === '') {
        $listStmt->bind_param('isii', $gymId, $search, $perPage, $offset);
    } else {
        $listStmt->bind_param('issii', $gymId, $search, $status, $perPage, $offset);
    }
    $listStmt->execute();
    $members = $listStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $listStmt->close();
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife member list failed: ' . $exception->getMessage());
    $dataUnavailable = true;
}

$memberListUrl = static function (int $page) use ($search, $status): string {
    $params = [];
    if ($search !== '') {
        $params['q'] = $search;
    }
    if ($status !== '') {
        $params['status'] = $status;
    }
    if ($page > 1) {
        $params['page'] = $page;
    }

    $url = fitlife_url('views/dashboard/members.php');
    return $params === [] ? $url : $url . '?' . http_build_query($params);
};

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">Member directory</span>
        <h1>Members</h1>
        <p>Find and manage the people registered with <?= fitlife_escape($currentStaff['gym_name']) ?>.</p>
    </div>
    <?php if ($canManageMembers): ?>
        <a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/member-add.php')) ?>">Add Member</a>
    <?php endif; ?>
</section>

<?php if ($dataUnavailable): ?>
    <section class="content-card unavailable-card">
        <h2>Members are temporarily unavailable</h2>
        <p>Please confirm the Phase 1 Step 2 migration has been imported, then try again.</p>
    </section>
<?php else: ?>
    <section class="content-card filter-card">
        <form method="get" action="<?= fitlife_escape(fitlife_url('views/dashboard/members.php')) ?>" class="member-filters">
            <div class="filter-search">
                <label for="q">Search members</label>
                <input id="q" name="q" type="search" maxlength="100" value="<?= fitlife_escape($search) ?>" placeholder="Number, name, phone, or email">
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value=""<?= $status === '' ? ' selected' : '' ?>>Active and inactive</option>
                    <option value="active"<?= $status === 'active' ? ' selected' : '' ?>>Active</option>
                    <option value="inactive"<?= $status === 'inactive' ? ' selected' : '' ?>>Inactive</option>
                    <option value="archived"<?= $status === 'archived' ? ' selected' : '' ?>>Archived</option>
                </select>
            </div>
            <button class="secondary-button" type="submit">Apply filters</button>
            <a class="text-button" href="<?= fitlife_escape(fitlife_url('views/dashboard/members.php')) ?>">Clear filters</a>
        </form>
    </section>

    <section class="content-card member-list-card">
        <div class="list-summary">
            <h2>Directory</h2>
            <span><?= number_format($memberCount) ?> result<?= $memberCount === 1 ? '' : 's' ?></span>
        </div>

        <?php if ($members === []): ?>
            <div class="empty-state">
                <span aria-hidden="true">&#128100;</span>
                <h3><?= ($search !== '' || $status !== '') ? 'No members match these filters' : 'No members yet' ?></h3>
                <p><?= ($search !== '' || $status !== '') ? 'Try changing or clearing the search filters.' : 'Add the first member to begin building your gym directory.' ?></p>
                <?php if ($canManageMembers && $search === '' && $status === ''): ?>
                    <a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/member-add.php')) ?>">Add Member</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="member-table">
                    <thead><tr><th>Member</th><th>Phone</th><th>Email</th><th>Join date</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td data-label="Member"><strong><?= fitlife_escape($member['first_name'] . ' ' . $member['last_name']) ?></strong><span><?= fitlife_escape($member['member_number']) ?></span></td>
                            <td data-label="Phone"><?= fitlife_escape($member['phone']) ?></td>
                            <td data-label="Email"><?= $member['email'] !== null && $member['email'] !== '' ? fitlife_escape($member['email']) : '<span class="muted">Not provided</span>' ?></td>
                            <td data-label="Join date"><?= fitlife_escape(fitlife_member_display_date($member['join_date'])) ?></td>
                            <td data-label="Status"><span class="status-badge status-<?= fitlife_escape($member['status']) ?>"><?= fitlife_escape(fitlife_member_status_label($member['status'])) ?></span></td>
                            <td data-label="Actions">
                                <div class="table-actions">
                                    <a href="<?= fitlife_escape(fitlife_url('views/dashboard/member.php?id=' . (int)$member['id'])) ?>">View</a>
                                    <?php if ($canManageMembers && $member['status'] !== 'archived'): ?>
                                        <a href="<?= fitlife_escape(fitlife_url('views/dashboard/member-edit.php?id=' . (int)$member['id'])) ?>">Edit</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pageCount > 1): ?>
                <nav class="pagination" aria-label="Member list pages">
                    <?php if ($currentPage > 1): ?><a href="<?= fitlife_escape($memberListUrl($currentPage - 1)) ?>">Previous</a><?php endif; ?>
                    <span>Page <?= $currentPage ?> of <?= $pageCount ?></span>
                    <?php if ($currentPage < $pageCount): ?><a href="<?= fitlife_escape($memberListUrl($currentPage + 1)) ?>">Next</a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

