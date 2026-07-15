<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

function fitlife_is_https(): bool
{
    return isset($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
}

function fitlife_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    if (headers_sent($filename, $line)) {
        error_log("FitLife could not start a session because output began in {$filename}:{$line}.");
        throw new RuntimeException('The session could not be started.');
    }

    $cookiePath = FITLIFE_BASE_PATH !== '' ? rtrim(FITLIFE_BASE_PATH, '/') . '/' : '/';

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', fitlife_is_https() ? '1' : '0');

    session_name('FITLIFESESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookiePath,
        'domain' => '',
        'secure' => fitlife_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    if (!session_start()) {
        throw new RuntimeException('The session could not be started.');
    }
}

function fitlife_escape(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function fitlife_redirect(string $path): never
{
    header('Location: ' . fitlife_url($path));
    exit;
}

function fitlife_is_authenticated(): bool
{
    return !empty($_SESSION['logged_in'])
        && isset($_SESSION['user_id'])
        && filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
}

function fitlife_send_private_cache_headers(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function fitlife_csrf_token(): string
{
    fitlife_start_session();

    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function fitlife_csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . fitlife_escape(fitlife_csrf_token()) . '">';
}

function fitlife_verify_csrf(?string $submittedToken): bool
{
    fitlife_start_session();
    $storedToken = $_SESSION['csrf_token'] ?? '';

    return is_string($submittedToken)
        && is_string($storedToken)
        && $storedToken !== ''
        && hash_equals($storedToken, $submittedToken);
}

function fitlife_require_csrf(?string $submittedToken): void
{
    if (!fitlife_verify_csrf($submittedToken)) {
        http_response_code(403);
        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Request expired</title></head>'
            . '<body><main><h1>Request expired</h1><p>Please go back, reload the page, and try again.</p></main></body></html>';
        exit;
    }
}

function fitlife_flash(string $type, string $message): void
{
    fitlife_start_session();
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

/** @return array<int, array{type: string, message: string}> */
function fitlife_take_flashes(): array
{
    fitlife_start_session();
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);

    return is_array($messages) ? $messages : [];
}

function fitlife_require_login(): void
{
    fitlife_start_session();

    if (!fitlife_is_authenticated()) {
        fitlife_flash('error', 'Please log in to continue.');
        fitlife_redirect('views/auth/login.php');
    }

    fitlife_send_private_cache_headers();
}
