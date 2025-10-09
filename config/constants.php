<?php
session_start(); //Because session is used
// Determine base URL dynamically to support both subfolder (/myapp) and root (/)
// Prefer deriving from DOCUMENT_ROOT and the filesystem path to this project
$docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') : '';
$appRoot = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/');

$basePath = '/';
if ($docRoot && $appRoot && strpos($appRoot, $docRoot) === 0) {
    $relative = substr($appRoot, strlen($docRoot));
    $basePath = $relative === '' ? '/' : '/' . trim($relative, '/') . '/';
} else {
    // Fallback to SCRIPT_NAME dirname if DOCUMENT_ROOT is unavailable
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/';
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $basePath = ($dir === '' || $dir === '.') ? '/' : $dir . '/';
}

define('ROOT_URL', $basePath);
