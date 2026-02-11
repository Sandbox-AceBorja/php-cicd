<?php
/**
 * Database Configuration Template
 * 
 * Copy this file to config.php and fill in your actual credentials
 * 
 * Instructions:
 * 1. Copy: cp config.example.php config.php
 * 2. Edit config.php with your real credentials
 * 3. config.php is git-ignored and won't be committed
 */

$isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost:8080', '127.0.0.1']);

// Database credentials
if ($isLocal) {
    // LOCAL
    define('DB_HOST', 'mysql');
    define('DB_NAME', 'myapp_db');
    define('DB_USER', 'myapp_user');
    define('DB_PASS', 'myapp_password');
} else {
    define('DB_HOST', 'sql212.infinityfree.com');
    define('DB_NAME', 'if0_41129394_myapp');
    define('DB_USER', 'if0_41129394');
    define('DB_PASS', 'YOUR_DATABASE_PASSWORD_HERE'); // ← Change this!
}

define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
function getDBConnection() {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (\PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        throw new \PDOException('Database connection failed');
    }
}
?>