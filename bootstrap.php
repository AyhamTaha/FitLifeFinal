<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

define('FITLIFE_ROOT', __DIR__);

$documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
$resolvedDocumentRoot = $documentRoot !== '' ? realpath($documentRoot) : false;
$resolvedProjectRoot = realpath(FITLIFE_ROOT);

if ($resolvedDocumentRoot !== false && $resolvedProjectRoot !== false) {
    $documentRootPath = rtrim(str_replace('\\', '/', $resolvedDocumentRoot), '/');
    $projectRootPath = rtrim(str_replace('\\', '/', $resolvedProjectRoot), '/');

    if (strcasecmp($projectRootPath, $documentRootPath) === 0) {
        define('FITLIFE_BASE_PATH', '');
    } elseif (str_starts_with(strtolower($projectRootPath), strtolower($documentRootPath . '/'))) {
        $relativeProjectPath = trim(substr($projectRootPath, strlen($documentRootPath)), '/');
        $encodedSegments = array_map('rawurlencode', explode('/', $relativeProjectPath));
        define('FITLIFE_BASE_PATH', '/' . implode('/', $encodedSegments));
    }
}

if (!defined('FITLIFE_BASE_PATH')) {
    define('FITLIFE_BASE_PATH', '');
}

function fitlife_url(string $path = ''): string
{
    $basePath = rtrim(FITLIFE_BASE_PATH, '/');
    $relativePath = ltrim($path, '/');

    return $relativePath === '' ? ($basePath !== '' ? $basePath . '/' : '/') : $basePath . '/' . $relativePath;
}
