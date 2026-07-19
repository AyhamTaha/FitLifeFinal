<?php
/** @var array<string, string> $values */
/** @var array<int, string> $errors */
/** @var array<int, array<string, mixed>> $subscriptionMembers */
/** @var array<int, array<string, mixed>> $subscriptionPlans */
?>
<?php if ($errors !== []): ?>
    <div class="flash flash-error" role="alert">
        <strong>Please correct the following:</strong>
        <ul><?php foreach ($errors as $error): ?><li><?= fitlife_escape($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<?php if ($subscriptionMembers === [] || $subscriptionPlans === []): ?>
    <div class="flash flash-error" role="alert">
        <?php if ($subscriptionMembers === []): ?>Add an active or inactive member before creating a subscription.<?php endif; ?>
        <?php if ($subscriptionMembers === [] && $subscriptionPlans === []): ?><br><?php endif; ?>
        <?php if ($subscriptionPlans === []): ?>Create and activate a membership plan before creating a subscription.<?php endif; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= fitlife_escape($subscriptionFormAction) ?>" class="member-form subscription-form">
    <?= fitlife_csrf_input() ?>
    <fieldset>
        <legend>Member and plan</legend>
        <div class="form-grid">
            <div>
                <label for="member_id">Member</label>
                <select id="member_id" name="member_id" required>
                    <option value="">Select a member</option>
                    <?php foreach ($subscriptionMembers as $member): ?>
                        <option value="<?= (int)$member['id'] ?>"<?= $values['member_id'] === (string)$member['id'] ? ' selected' : '' ?>>
                            <?= fitlife_escape($member['member_number'] . ' — ' . $member['first_name'] . ' ' . $member['last_name']) ?><?= $member['status'] !== 'active' ? ' (' . fitlife_escape(ucfirst((string)$member['status'])) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="membership_plan_id">Membership plan</label>
                <select id="membership_plan_id" name="membership_plan_id" required>
                    <option value="">Select a plan</option>
                    <?php foreach ($subscriptionPlans as $plan): ?>
                        <option value="<?= (int)$plan['id'] ?>"<?= $values['membership_plan_id'] === (string)$plan['id'] ? ' selected' : '' ?>>
                            <?= fitlife_escape($plan['name'] . ' — ' . number_format((float)$plan['price'], 2) . ' ' . $plan['currency']) ?><?= (int)$plan['is_active'] !== 1 ? ' (Inactive)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="field-help">The selected plan's price and currency are saved as a snapshot.</span>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Subscription period</legend>
        <div class="form-grid">
            <div><label for="start_date">Start date</label><input id="start_date" name="start_date" type="date" required value="<?= fitlife_escape($values['start_date']) ?>"></div>
            <div><label for="end_date">End date</label><input id="end_date" name="end_date" type="date" required value="<?= fitlife_escape($values['end_date']) ?>"></div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Notes</legend>
        <div>
            <label for="notes">Internal notes <span>(optional, 5,000 characters maximum)</span></label>
            <textarea id="notes" name="notes" maxlength="5000" rows="6"><?= fitlife_escape($values['notes']) ?></textarea>
        </div>
    </fieldset>

    <div class="form-actions">
        <button class="primary-button" type="submit"<?= $subscriptionMembers === [] || $subscriptionPlans === [] ? ' disabled' : '' ?>><?= fitlife_escape($subscriptionFormSubmit) ?></button>
        <a class="secondary-button button-link" href="<?= fitlife_escape($subscriptionCancelUrl) ?>">Cancel</a>
    </div>
</form>
