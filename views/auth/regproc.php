<?php

require_once __DIR__ . '/dbconn.php';
require_once __DIR__ . '/../../includes/security.php';

fitlife_start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fitlife_redirect('views/auth/register.php');
}

fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$repeatedPassword = $_POST['Repassword'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    fitlife_flash('error', 'All fields are required.');
    fitlife_redirect('views/auth/register.php');
}

if ($password !== $repeatedPassword) {
    fitlife_flash('error', 'Passwords do not match.');
    fitlife_redirect('views/auth/register.php');
}

if (strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    fitlife_flash('error', 'Enter a valid name and email address.');
    fitlife_redirect('views/auth/register.php');
}

if (strlen($password) < 8 || strlen($password) > 4096) {
    fitlife_flash('error', 'Password must be at least 8 characters.');
    fitlife_redirect('views/auth/register.php');
}

try {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $hashedPassword);
    $stmt->execute();
    $stmt->close();

    fitlife_flash('success', 'Registered successfully. You can now log in.');
    fitlife_redirect('views/auth/login.php');
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife registration failed: ' . $exception->getMessage());
    $message = $exception->getCode() === 1062
        ? 'An account with that email already exists.'
        : 'Registration is temporarily unavailable. Please try again.';
    fitlife_flash('error', $message);
    fitlife_redirect('views/auth/register.php');
}
