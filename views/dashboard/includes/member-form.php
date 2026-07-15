<?php
/** @var array<string, string> $values */
/** @var array<int, string> $errors */
?>
<?php if ($errors !== []): ?>
    <div class="flash flash-error" role="alert">
        <strong>Please correct the following:</strong>
        <ul><?php foreach ($errors as $error): ?><li><?= fitlife_escape($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= fitlife_escape($memberFormAction) ?>" class="member-form">
    <?= fitlife_csrf_input() ?>
    <fieldset>
        <legend>Personal details</legend>
        <div class="form-grid">
            <div><label for="first_name">First name</label><input id="first_name" name="first_name" type="text" maxlength="100" autocomplete="given-name" required value="<?= fitlife_escape($values['first_name']) ?>"></div>
            <div><label for="last_name">Last name</label><input id="last_name" name="last_name" type="text" maxlength="100" autocomplete="family-name" required value="<?= fitlife_escape($values['last_name']) ?>"></div>
            <div><label for="phone">Phone</label><input id="phone" name="phone" type="tel" maxlength="30" autocomplete="tel" required value="<?= fitlife_escape($values['phone']) ?>"></div>
            <div><label for="email">Email <span>(optional)</span></label><input id="email" name="email" type="email" maxlength="150" autocomplete="email" value="<?= fitlife_escape($values['email']) ?>"></div>
            <div><label for="date_of_birth">Date of birth <span>(optional)</span></label><input id="date_of_birth" name="date_of_birth" type="date" max="<?= date('Y-m-d') ?>" value="<?= fitlife_escape($values['date_of_birth']) ?>"></div>
            <div><label for="gender">Gender <span>(optional)</span></label><select id="gender" name="gender"><option value="">Not provided</option><option value="male"<?= $values['gender'] === 'male' ? ' selected' : '' ?>>Male</option><option value="female"<?= $values['gender'] === 'female' ? ' selected' : '' ?>>Female</option><option value="other"<?= $values['gender'] === 'other' ? ' selected' : '' ?>>Other</option><option value="prefer_not_to_say"<?= $values['gender'] === 'prefer_not_to_say' ? ' selected' : '' ?>>Prefer not to say</option></select></div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Emergency contact</legend>
        <div class="form-grid">
            <div><label for="emergency_contact_name">Contact name <span>(optional)</span></label><input id="emergency_contact_name" name="emergency_contact_name" type="text" maxlength="150" value="<?= fitlife_escape($values['emergency_contact_name']) ?>"></div>
            <div><label for="emergency_contact_phone">Contact phone <span>(optional)</span></label><input id="emergency_contact_phone" name="emergency_contact_phone" type="tel" maxlength="30" value="<?= fitlife_escape($values['emergency_contact_phone']) ?>"></div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Membership record</legend>
        <div class="form-grid">
            <div><label for="join_date">Join date</label><input id="join_date" name="join_date" type="date" required value="<?= fitlife_escape($values['join_date']) ?>"></div>
            <div><label for="status">Status</label><select id="status" name="status" required><option value="active"<?= $values['status'] === 'active' ? ' selected' : '' ?>>Active</option><option value="inactive"<?= $values['status'] === 'inactive' ? ' selected' : '' ?>>Inactive</option></select></div>
        </div>
        <label for="notes">Notes <span>(optional, 5,000 characters maximum)</span></label>
        <textarea id="notes" name="notes" maxlength="5000" rows="6"><?= fitlife_escape($values['notes']) ?></textarea>
    </fieldset>

    <div class="form-actions">
        <button class="primary-button" type="submit"><?= fitlife_escape($memberFormSubmit) ?></button>
        <a class="secondary-button button-link" href="<?= fitlife_escape($memberCancelUrl) ?>">Cancel</a>
    </div>
</form>
