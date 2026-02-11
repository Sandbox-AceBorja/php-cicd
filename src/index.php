<?php

$isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost:8080', '127.0.0.1']);

if ($isLocal) {
    // LOCAL
    $host = 'mysql';
    $db = 'myapp_db';
    $user = 'myapp_user';
    $pass = 'myapp_password';
} else {
    // PRODUCTION (InfinityFree)
    $host = 'sql309.infinityfree.com';
    $db = 'if0_41129394_myapp_db';
    $user = 'if0_41129394';
    $pass = 'mTwxVdLtdP';
}

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $connection_status = "✓ Database connected successfully!";
} catch (\PDOException $e) {
    $connection_status = "✗ Connection failed: " . $e->getMessage();
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CI/CD on InfinityFree</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 800px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-box h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-item:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
        }
        tr:hover { background: #f5f5f5; }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .badge-success { background: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 PHP CI/CD on InfinityFree</h1>
        
        <div class="status <?php echo $pdo ? 'success' : 'error'; ?>">
            <?php echo $connection_status; ?>
        </div>

        <div class="info-box">
            <h3>📋 System Information</h3>
            <div class="info-item">
                <span class="label">PHP Version:</span> <?php echo phpversion(); ?>
            </div>
            <div class="info-item">
                <span class="label">Server Software:</span> <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
            </div>
            <div class="info-item">
                <span class="label">Host:</span> <?php echo $_SERVER['HTTP_HOST']; ?>
            </div>
            <div class="info-item">
                <span class="label">Deployment:</span> 
                <span class="badge badge-success">GitHub Actions</span>
            </div>
        </div>

        <?php if ($pdo): ?>
        <div class="info-box">
            <h3>🗄️ Database Information</h3>
            <div class="info-item">
                <span class="label">Database Name:</span> <?php echo $db; ?>
            </div>
            <div class="info-item">
                <span class="label">MySQL Version:</span> 
                <?php 
                    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
                    echo $version;
                ?>
            </div>
        </div>

        <?php
        // Fetch users from database
        try {
            $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
            $users = $stmt->fetchAll();
        } catch (\PDOException $e) {
            $users = [];
            echo '<div class="info-box"><p>No users table found. Run init.sql in phpMyAdmin to create sample data.</p></div>';
        }

        if (count($users) > 0):
        ?>
        <div class="info-box">
            <h3>👥 Users in Database</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <div class="info-box">
            <h3>ℹ️ About this deployment</h3>
            <p>This site is automatically deployed from GitHub using GitHub Actions whenever code is pushed to the main branch.</p>
            <br>
            <p><strong>Features:</strong></p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>✓ Automated testing on every push</li>
                <li>✓ Automatic FTP deployment to InfinityFree</li>
                <li>✓ PHP syntax validation</li>
                <li>✓ MySQL database integration</li>
            </ul>
        </div>
    </div>
</body>
</html>