<?php
/**
 * Database Migration Runner
 * 
 * Run this file to apply pending migrations
 * URL: https://php-cicd.free.nf/migrate.php
 * 
 * Security: Add password protection or delete after use!
 */

// Database configuration
$host = 'sql212.infinityfree.com'; // Update with your host
$db = 'if0_41129394_myapp';
$user = 'if0_41129394';
$pass = 'YOUR_DATABASE_PASSWORD';

// Simple password protection (change this!)
$migration_password = 'your_secret_password_123';

// Check password
if (!isset($_GET['password']) || $_GET['password'] !== $migration_password) {
    die('❌ Unauthorized. Use: migrate.php?password=your_secret_password_123');
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
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
    echo "✓ Migrations table ready\n\n";
    
    // Get executed migrations
    $stmt = $pdo->query("SELECT migration_name FROM migrations");
    $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Scan migrations directory
    $migrations_dir = __DIR__ . '/migrations';
    
    if (!is_dir($migrations_dir)) {
        echo "❌ Migrations directory not found!\n";
        echo "Create folder: /migrations/\n";
        exit;
    }
    
    $files = glob($migrations_dir . '/*.sql');
    sort($files); // Execute in order
    
    $pending = 0;
    $applied = 0;
    
    foreach ($files as $file) {
        $migration_name = basename($file);
        
        if (in_array($migration_name, $executed)) {
            echo "⏭️  SKIP: {$migration_name} (already executed)\n";
            continue;
        }
        
        $pending++;
        echo "🔄 Running: {$migration_name}\n";
        
        try {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            
            // Record migration
            $stmt = $pdo->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
            $stmt->execute([$migration_name]);
            
            echo "✅ SUCCESS: {$migration_name}\n\n";
            $applied++;
            
        } catch (PDOException $e) {
            echo "❌ FAILED: {$migration_name}\n";
            echo "Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 Summary:\n";
    echo "   Total migrations: " . count($files) . "\n";
    echo "   Already executed: " . count($executed) . "\n";
    echo "   Pending: {$pending}\n";
    echo "   Applied now: {$applied}\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "\n✨ Migration complete!\n";
    echo "\n⚠️  SECURITY: Delete this file after use or add better authentication!\n";
    
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>