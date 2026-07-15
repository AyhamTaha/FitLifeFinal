<?php

require_once __DIR__ . '/dbconn.php';
require_once __DIR__ . '/../../includes/authorization.php';

fitlife_start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fitlife_redirect('views/auth/login.php');
}

fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150 || $password === '') {
    fitlife_flash('error', 'Invalid email or password.');
    fitlife_redirect('views/auth/login.php');
}

try {
    $stmt = $conn->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $passwordHash = $user['password_hash'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
    $passwordIsValid = password_verify($password, $passwordHash);

    if ($user && $passwordIsValid) {
        $assignment = fitlife_active_staff_assignment($conn, (int)$user['id']);

        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = (string)$user['name'];
        $_SESSION['user_email'] = (string)$user['email'];

        if ($assignment !== null) {
            $_SESSION['gym_id'] = $assignment['gym_id'];
            $_SESSION['gym_name'] = $assignment['gym_name'];
            $_SESSION['gym_role'] = $assignment['role'];
            fitlife_redirect('views/dashboard/index.php');
        }

        fitlife_redirect('views/auth/home.php');
    }

    fitlife_flash('error', 'Invalid email or password.');
    fitlife_redirect('views/auth/login.php');
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife login failed: ' . $exception->getMessage());
    fitlife_flash('error', 'Login is temporarily unavailable. Please try again.');
    fitlife_redirect('views/auth/login.php');
}
