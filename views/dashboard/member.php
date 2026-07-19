<?php

declare(strict_types=1);

$pageTitle = 'Member Profile';
$activeDashboardNav = 'Members';
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../../includes/members.php';

$memberId = fitlife_member_id($_GET['id'] ?? null);
$member = null;
$dataUnavailable = false;
if ($memberId !== null) {
    try {
        $member = fitlife_find_member($conn, $memberId, (int)$currentStaff['gym_id']);
    } catch (mysqli_sql_exception $exception) {
        error_log('FitLife member profile failed: ' . $exception->getMessage());
        $dataUnavailable = true;
    }
}

require __DIR__ . '/includes/header.php';
if ($dataUnavailable):
?>
<section class="content-card unavailable-card"><h1>Member profile temporarily unavailable</h1><p>Please try again later.</p></section>
<?php
    require __DIR__ . '/includes/footer.php';
    exit;
endif;
if ($member === null):
    fitlife_member_unavailable();
    require __DIR__ . '/includes/footer.php';
    exit;
endif;

$canManageMember = fitlife_can_manage_members($currentStaff['role']) && $member['status'] !== 'archived';
$genderLabel = $member['gender'] ? ucfirst(str_replace('_', ' ', (string)$member['gender'])) : 'Not provided';
?>
<section class="page-heading member-profile-heading">
    <div><span class="eyebrow"><?= fitlife_escape($member['member_number']) ?></span><h1><?= fitlife_escape($member['first_name'] . ' ' . $member['last_name']) ?></h1><p>Member profile and contact information.</p></div>
    <div class="heading-actions">
        <?php if ($canManageMember): ?><a class="primary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/member-edit.php?id=' . (int)$member['id'])) ?>">Edit Member</a><?php endif; ?>
        <a class="secondary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/members.php')) ?>">Back to Members</a>
    </div>
</section>

<section class="profile-grid">
    <article class="content-card profile-card">
        <div class="profile-card-heading"><h2>Member details</h2><span class="status-badge status-<?= fitlife_escape($member['status']) ?>"><?= fitlife_escape(fitlife_member_status_label($member['status'])) ?></span></div>
        <dl class="detail-list">
            <div><dt>Member number</dt><dd><?= fitlife_escape($member['member_number']) ?></dd></div>
            <div><dt>Phone</dt><dd><?= fitlife_escape($member['phone']) ?></dd></div>
            <div><dt>Email</dt><dd><?= $member['email'] ? fitlife_escape($member['email']) : 'Not provided' ?></dd></div>
            <div><dt>Date of birth</dt><dd><?= fitlife_escape(fitlife_member_display_date($member['date_of_birth'])) ?></dd></div>
            <div><dt>Gender</dt><dd><?= fitlife_escape($genderLabel) ?></dd></div>
            <div><dt>Join date</dt><dd><?= fitlife_escape(fitlife_member_display_date($member['join_date'])) ?></dd></div>
            <div><dt>Emergency contact</dt><dd><?= $member['emergency_contact_name'] ? fitlife_escape($member['emergency_contact_name']) : 'Not provided' ?></dd></div>
            <div><dt>Emergency phone</dt><dd><?= $member['emergency_contact_phone'] ? fitlife_escape($member['emergency_contact_phone']) : 'Not provided' ?></dd></div>
            <div><dt>Created</dt><dd><?= fitlife_escape(fitlife_member_display_date($member['created_at'], true)) ?></dd></div>
            <div><dt>Updated</dt><dd><?= fitlife_escape(fitlife_member_display_date($member['updated_at'], true)) ?></dd></div>
        </dl>
        <div class="notes-block"><h3>Notes</h3><p><?= $member['notes'] ? nl2br(fitlife_escape($member['notes'])) : 'No notes have been added.' ?></p></div>

        <?php if ($canManageMember): ?>
            <form method="post" action="<?= fitlife_escape(fitlife_url('views/dashboard/member-archive.php')) ?>" class="archive-form" onsubmit="return confirm('Archive this member? They will leave the default member list.');">
                <?= fitlife_csrf_input() ?><input type="hidden" name="id" value="<?= (int)$member['id'] ?>">
                <button class="danger-button" type="submit">Archive Member</button>
            </form>
        <?php elseif ($member['status'] === 'archived'): ?>
            <p class="archive-note">Archived <?= fitlife_escape(fitlife_member_display_date($member['archived_at'], true)) ?>. This record is retained and cannot be modified.</p>
        <?php endif; ?>
    </article>

    <aside class="history-grid" aria-label="Member history">
        <article class="content-card future-card"><span>Subscriptions</span><h2>Membership history</h2><p>View this member's plan subscriptions and current access status.</p><a class="secondary-button button-link" href="<?= fitlife_escape(fitlife_url('views/dashboard/subscriptions.php?q=' . rawurlencode($member['member_number']))) ?>">View Subscriptions</a></article>
        <article class="content-card future-card"><span>Coming later</span><h2>Payment history</h2><p>Payments and balances will appear here after billing is implemented.</p></article>
        <article class="content-card future-card"><span>Coming later</span><h2>Attendance history</h2><p>Check-ins and attendance activity will appear here in a future phase.</p></article>
    </aside>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
