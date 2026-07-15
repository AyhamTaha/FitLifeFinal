<?php

require_once __DIR__ . '/../../includes/security.php';

fitlife_start_session();
fitlife_send_private_cache_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fitlife_redirect('views/auth/home.php');
}

fitlife_require_csrf(isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : null);

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => 'Lax',
    ]);
}

session_destroy();
fitlife_redirect('views/auth/home.php');
