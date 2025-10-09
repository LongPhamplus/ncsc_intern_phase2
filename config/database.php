<?php
$servername = getenv('DB_HOST') ?: 'mysql';
$username   = getenv('DB_USER') ?: 'myuser';
$password   = getenv('DB_PASS') ?: 'mypassword';
$dbname     = getenv('DB_NAME') ?: 'myapp';
// Optional port (defaults to 3306)
$dbport     = getenv('DB_PORT') ?: '3306';

// Try multiple hosts to support running inside Docker (service name 'mysql')
// and running on the host (127.0.0.1 / localhost)
$hostsToTry = array_unique([$servername, '127.0.0.1', 'localhost']);
$conn = null;
$lastError = null;

foreach ($hostsToTry as $host) {
    try {
        $dsn = "mysql:host={$host};port={$dbport};dbname={$dbname};charset=utf8mb4";
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Ensure required tables exist and seed admin if needed (idempotent)
        require_once __DIR__ . '/bootstrap.php';
        $lastError = null;
        break;
    } catch (PDOException $e) {
        $lastError = $e;
        // try next host
    }
}

if (!$conn) {
    echo "Connection failed: " . ($lastError ? $lastError->getMessage() : 'Unknown error');
}