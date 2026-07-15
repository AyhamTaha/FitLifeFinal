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

<form method="post" action="<?= fitlife_escape($planFormAction) ?>" class="member-form plan-form">
    <?= fitlife_csrf_input() ?>
    <fieldset>
        <legend>Plan details</legend>
        <div class="form-grid">
            <div><label for="name">Plan name</label><input id="name" name="name" type="text" maxlength="150" required value="<?= fitlife_escape($values['name']) ?>"></div>
            <div><label for="is_active">Status</label><select id="is_active" name="is_active" required><option value="1"<?= $values['is_active'] === '1' ? ' selected' : '' ?>>Active</option><option value="0"<?= $values['is_active'] === '0' ? ' selected' : '' ?>>Inactive</option></select></div>
        </div>
        <div>
            <label for="description">Description <span>(optional, 5,000 characters maximum)</span></label>
            <textarea id="description" name="description" maxlength="5000" rows="6"><?= fitlife_escape($values['description']) ?></textarea>
        </div>
    </fieldset>

    <fieldset>
        <legend>Duration and access</legend>
        <div class="form-grid">
            <div><label for="duration_value">Duration value</label><input id="duration_value" name="duration_value" type="number" min="1" max="2147483647" step="1" required value="<?= fitlife_escape($values['duration_value']) ?>"></div>
            <div><label for="duration_unit">Duration unit</label><select id="duration_unit" name="duration_unit" required><?php foreach (FITLIFE_PLAN_DURATION_UNITS as $unit): ?><option value="<?= fitlife_escape($unit) ?>"<?= $values['duration_unit'] === $unit ? ' selected' : '' ?>><?= fitlife_escape(ucfirst($unit)) ?></option><?php endforeach; ?></select></div>
            <div><label for="freeze_days_allowed">Freeze days allowed</label><input id="freeze_days_allowed" name="freeze_days_allowed" type="number" min="0" max="2147483647" step="1" required value="<?= fitlife_escape($values['freeze_days_allowed']) ?>"></div>
            <div><label for="visit_limit">Visit limit <span>(blank means unlimited)</span></label><input id="visit_limit" name="visit_limit" type="number" min="1" max="2147483647" step="1" value="<?= fitlife_escape($values['visit_limit']) ?>"></div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Pricing</legend>
        <div class="form-grid">
            <div><label for="price">Price</label><input id="price" name="price" type="number" min="0" max="9999999999.99" step="0.01" inputmode="decimal" required value="<?= fitlife_escape($values['price']) ?>"></div>
            <div><label for="currency">Currency</label><select id="currency" name="currency" required><?php foreach (FITLIFE_PLAN_CURRENCIES as $currency): ?><option value="<?= fitlife_escape($currency) ?>"<?= $values['currency'] === $currency ? ' selected' : '' ?>><?= fitlife_escape($currency) ?></option><?php endforeach; ?></select></div>
        </div>
    </fieldset>

    <div class="form-actions">
        <button class="primary-button" type="submit"><?= fitlife_escape($planFormSubmit) ?></button>
        <a class="secondary-button button-link" href="<?= fitlife_escape($planCancelUrl) ?>">Cancel</a>
    </div>
</form>
