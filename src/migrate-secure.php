<?php
/**
 * Database Migration Runner
 * 
 * Run this file to apply pending migrations
 * URL: https://php-cicd.free.nf/migrate.php?password=your_secret_password_123
 * 
 * Security: Password protected - DELETE after use!
 */

// Load database configuration
require_once __DIR__ . '/config.php';

// Simple password protection (change this!)
$migration_password = 'your_secret_password_123';

// Check password
if (!isset($_GET['password']) || $_GET['password'] !== $migration_password) {
    http_response_code(403);
    die('❌ Unauthorized. Use: migrate.php?password=your_secret_password_123');
}

try {
    $pdo = getDBConnection();
    
    echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Migration Runner</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .info { color: #569cd6; }
        .warning { color: #dcdcaa; }
        pre { background: #2d2d2d; padding: 15px; border-radius: 5px; }
        h2 { color: #569cd6; }
    </style>
</head>
<body>";
    
    echo "<h2>🔄 Database Migration Runner</h2>";
    echo "<pre>";
    
    // Create migrations tracking table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration_name VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<span class='success'>✓ Migrations table ready</span>\n\n";
    
    // Get executed migrations
    $stmt = $pdo->query("SELECT migration_name FROM migrations");
    $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Scan migrations directory
    $migrations_dir = __DIR__ . '/migrations';
    
    if (!is_dir($migrations_dir)) {
        echo "<span class='error'>❌ Migrations directory not found!</span>\n";
        echo "<span class='info'>Create folder: /migrations/</span>\n";
        echo "</pre></body></html>";
        exit;
    }
    
    $files = glob($migrations_dir . '/*.sql');
    sort($files); // Execute in order
    
    $pending = 0;
    $applied = 0;
    
    foreach ($files as $file) {
        $migration_name = basename($file);
        
        if (in_array($migration_name, $executed)) {
            echo "<span class='info'>⏭️  SKIP: {$migration_name} (already executed)</span>\n";
            continue;
        }
        
        $pending++;
        echo "<span class='warning'>🔄 Running: {$migration_name}</span>\n";
        
        try {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            
            // Record migration
            $stmt = $pdo->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
            $stmt->execute([$migration_name]);
            
            echo "<span class='success'>✅ SUCCESS: {$migration_name}</span>\n\n";
            $applied++;
            
        } catch (PDOException $e) {
            echo "<span class='error'>❌ FAILED: {$migration_name}</span>\n";
            echo "<span class='error'>Error: " . $e->getMessage() . "</span>\n\n";
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "<span class='info'>📊 Summary:</span>\n";
    echo "   Total migrations: " . count($files) . "\n";
    echo "   Already executed: " . count($executed) . "\n";
    echo "   Pending: {$pending}\n";
    echo "   Applied now: {$applied}\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "\n<span class='success'>✨ Migration complete!</span>\n";
    echo "\n<span class='error'>⚠️  SECURITY: Delete this file after use!</span>\n";
    
    echo "</pre></body></html>";
    
} catch (PDOException $e) {
    echo "<pre>";
    echo "<span class='error'>❌ Database connection failed: " . $e->getMessage() . "</span>";
    echo "\n<span class='info'>Check your config.php file!</span>";
    echo "</pre>";
}
?>