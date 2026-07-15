<?php

require_once __DIR__ . '/../../bootstrap.php';

$configPath = __DIR__ . '/../../config/database.local.php';

if (!is_file($configPath)) {
    error_log('FitLife database configuration is missing: ' . $configPath);
    http_response_code(503);
    echo '<section style="max-width:700px;margin:3rem auto;padding:1.5rem;font-family:Arial,sans-serif">'
        . '<h1>FitLife is not configured</h1>'
        . '<p>Create the local database configuration described in SETUP.md, then reload this page.</p>'
        . '</section>';
    exit;
}

try {
    $databaseConfig = require $configPath;
} catch (Throwable $exception) {
    error_log('FitLife could not load the database configuration: ' . $exception->getMessage());
    http_response_code(503);
    echo '<section style="max-width:700px;margin:3rem auto;padding:1.5rem;font-family:Arial,sans-serif">'
        . '<h1>FitLife is not configured</h1>'
        . '<p>The local database configuration could not be loaded. Check SETUP.md and the PHP error log.</p>'
        . '</section>';
    exit;
}

$requiredKeys = ['host', 'port', 'database', 'username', 'password'];
if (!is_array($databaseConfig) || array_diff($requiredKeys, array_keys($databaseConfig)) !== []) {
    error_log('FitLife database configuration is invalid or incomplete.');
    http_response_code(503);
    echo '<section style="max-width:700px;margin:3rem auto;padding:1.5rem;font-family:Arial,sans-serif">'
        . '<h1>FitLife is not configured</h1>'
        . '<p>The local database configuration is incomplete. Check SETUP.md and the PHP error log.</p>'
        . '</section>';
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        (string)$databaseConfig['host'],
        (string)$databaseConfig['username'],
        (string)$databaseConfig['password'],
        (string)$databaseConfig['database'],
        (int)$databaseConfig['port']
    );
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log('FitLife database connection failed: ' . $exception->getMessage());
    http_response_code(503);
    echo '<section style="max-width:700px;margin:3rem auto;padding:1.5rem;font-family:Arial,sans-serif">'
        . '<h1>FitLife is temporarily unavailable</h1>'
        . '<p>We could not connect to the FitLife database. Please make sure MySQL is running and try again.</p>'
        . '</section>';
    exit;
}
