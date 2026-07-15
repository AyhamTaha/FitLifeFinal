<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/security.php';

fitlife_require_login();

require_once __DIR__ . '/../auth/dbconn.php';
require_once __DIR__ . '/../../includes/authorization.php';

$userId = (int)$_SESSION['user_id'];
$errors = [];
$values = [
    'name' => '',
    'phone' => '',
    'email' => (string)($_SESSION['user_email'] ?? ''),
    'address' => '',
];
$existingAssignment = null;

try {
    $existingStmt = $conn->prepare(
        'SELECT gs.is_active, gs.role, g.status, g.name AS gym_name
         FROM gym_staff AS gs
         INNER JOIN gyms AS g ON g.id = gs.gym_id
         WHERE gs.user_id = ?
         LIMIT 1'
    );
    $existingStmt->bind_param('i', $userId);
    $existingStmt->execute();
    $existingAssignment = $existingStmt->get_result()->fetch_assoc();
    $existingStmt->close();
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife gym setup lookup failed: ' . $exception->getMessage());
    http_response_code(503);
    echo '<h1>Gym setup temporarily unavailable</h1><p>Please confirm the Phase 1 migration was imported, then try again.</p>';
    exit;
}

if ($existingAssignment
    && (int)$existingAssignment['is_active'] === 1
    && $existingAssignment['status'] === 'active') {
    $_SESSION['gym_name'] = (string)$existingAssignment['gym_name'];
    $_SESSION['gym_role'] = (string)$existingAssignment['role'];
    fitlife_redirect('views/dashboard/index.php');
}

$setupBlocked = $existingAssignment !== null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$setupBlocked) {
    fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);

    foreach (array_keys($values) as $field) {
        $values[$field] = trim((string)($_POST[$field] ?? ''));
    }

    if ($values['name'] === '' || strlen($values['name']) > 150) {
        $errors[] = 'Gym name is required and must be 150 characters or fewer.';
    }
    if ($values['phone'] === '' || strlen($values['phone']) > 30
        || !preg_match('/^[0-9+()\-\s.]{5,30}$/', $values['phone'])) {
        $errors[] = 'Enter a valid phone number.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || strlen($values['email']) > 150) {
        $errors[] = 'Enter a valid gym email address.';
    }
    if ($values['address'] === '' || strlen($values['address']) > 255) {
        $errors[] = 'Address is required and must be 255 characters or fewer.';
    }

    if ($errors === []) {
        try {
            $conn->begin_transaction();

            $lockStmt = $conn->prepare('SELECT id FROM gym_staff WHERE user_id = ? LIMIT 1 FOR UPDATE');
            $lockStmt->bind_param('i', $userId);
            $lockStmt->execute();
            $duplicateAssignment = $lockStmt->get_result()->fetch_assoc();
            $lockStmt->close();

            if ($duplicateAssignment) {
                throw new DomainException('This account already has a gym assignment.');
            }

            $gymStmt = $conn->prepare(
                "INSERT INTO gyms (name, phone, email, address, status) VALUES (?, ?, ?, ?, 'active')"
            );
            $gymStmt->bind_param('ssss', $values['name'], $values['phone'], $values['email'], $values['address']);
            $gymStmt->execute();
            $gymId = $conn->insert_id;
            $gymStmt->close();

            $staffStmt = $conn->prepare(
                "INSERT INTO gym_staff (gym_id, user_id, role, is_active) VALUES (?, ?, 'owner', 1)"
            );
            $staffStmt->bind_param('ii', $gymId, $userId);
            $staffStmt->execute();
            $staffStmt->close();

            $conn->commit();

            $_SESSION['gym_id'] = $gymId;
            $_SESSION['gym_name'] = $values['name'];
            $_SESSION['gym_role'] = 'owner';
            fitlife_flash('success', 'Your gym was created successfully. Welcome to your dashboard.');
            fitlife_redirect('views/dashboard/index.php');
        } catch (DomainException $exception) {
            $conn->rollback();
            $errors[] = $exception->getMessage();
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            error_log('FitLife gym setup failed: ' . $exception->getMessage());
            $errors[] = $exception->getCode() === 1062
                ? 'This account already has a gym assignment.'
                : 'Gym setup is temporarily unavailable. Please try again.';
        }
    }
}

$fitlifeBasePath = fitlife_escape(FITLIFE_BASE_PATH);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set up your gym | FitLife</title>
    <link rel="stylesheet" href="<?= $fitlifeBasePath ?>/public/css/dashboard.css">
</head>
<body class="setup-page">
<main class="setup-shell">
    <a class="setup-back" href="<?= $fitlifeBasePath ?>/views/auth/home.php">&larr; Back to FitLife</a>
    <section class="setup-card">
        <div class="setup-heading">
            <span class="eyebrow">Gym management</span>
            <h1>Set up your gym</h1>
            <p>Create your gym account. You will be assigned as its owner.</p>
        </div>

        <?php if ($setupBlocked): ?>
            <div class="flash flash-error">
                This account already has an inactive gym assignment. Ask a gym owner or administrator to reactivate it.
            </div>
        <?php else: ?>
            <?php if ($errors !== []): ?>
                <div class="flash flash-error" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= fitlife_escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= $fitlifeBasePath ?>/views/gym/setup.php" class="setup-form">
                <?= fitlife_csrf_input() ?>
                <label for="name">Gym name</label>
                <input id="name" name="name" type="text" maxlength="150" required value="<?= fitlife_escape($values['name']) ?>">

                <div class="form-grid">
                    <div>
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" type="tel" maxlength="30" required value="<?= fitlife_escape($values['phone']) ?>">
                    </div>
                    <div>
                        <label for="email">Gym email</label>
                        <input id="email" name="email" type="email" maxlength="150" required value="<?= fitlife_escape($values['email']) ?>">
                    </div>
                </div>

                <label for="address">Address</label>
                <textarea id="address" name="address" maxlength="255" rows="4" required><?= fitlife_escape($values['address']) ?></textarea>

                <button type="submit" class="primary-button">Create gym and open dashboard</button>
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
