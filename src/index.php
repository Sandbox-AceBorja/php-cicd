<?php
// Load database configuration
require_once __DIR__ . '/config.php';

// Get database connection
try {
    $pdo = getDBConnection();
    $connection_status = true;
} catch (\PDOException $e) {
    $connection_status = false;
    $pdo = null;
}

// Fetch users from database
$users = [];
if ($pdo) {
    try {
        $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 10');
        $users = $stmt->fetchAll();
    } catch (\PDOException $e) {
        $users = [];
    }
}

// Get database info
$db_info = [];
if ($pdo) {
    try {
        $db_info['version'] = $pdo->query('SELECT VERSION()')->fetchColumn();
        $db_info['name'] = DB_NAME;
    } catch (\PDOException $e) {
        $db_info = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CI/CD Platform | Modern Deployment Solution</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #14b8a6;
            --primary-dark: #0d9488;
            --secondary: #06b6d4;
            --accent: #22d3ee;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --gray-light: #cbd5e1;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--dark);
            color: var(--white);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .loading-screen.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 3px solid var(--dark-light);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Animated Background */
        .bg-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a1628 0%, #0f172a 50%, #134e4a 100%);
            z-index: -2;
        }

        .bg-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(20, 184, 166, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(6, 182, 212, 0.15) 0%, transparent 50%);
            animation: bgShift 15s ease infinite;
        }

        @keyframes bgShift {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.1); }
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(20, 184, 166, 0.2);
            padding: 1rem 0;
            z-index: 100;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--gray-light);
            text-decoration: none;
            transition: color 0.3s;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 6rem 2rem 2rem;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 4rem 0;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--white), var(--gray-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .hero .gradient-text {
            background: linear-gradient(135deg, var(--primary), var(--secondary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--gray-light);
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            animation: fadeInUp 0.8s ease 0.2s backwards;
        }

        .status-badge.success {
            background: rgba(20, 184, 166, 0.15);
            border: 1px solid rgba(20, 184, 166, 0.3);
            color: var(--primary);
        }

        .status-badge.error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--error);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s ease infinite;
        }

        .status-dot.success {
            background: var(--primary);
        }

        .status-dot.error {
            background: var(--error);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(20, 184, 166, 0.2);
            border-radius: 1rem;
            padding: 2rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease backwards;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }

        .card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(20, 184, 166, 0.3);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--white);
        }

        .card p {
            color: var(--gray-light);
            font-size: 0.95rem;
        }

        .card .value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 0.5rem;
        }

        /* Features Section */
        .features {
            margin: 4rem 0;
            animation: fadeInUp 0.8s ease 0.5s backwards;
        }

        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, var(--white), var(--gray-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            background: rgba(30, 41, 59, 0.3);
            border: 1px solid rgba(20, 184, 166, 0.1);
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(30, 41, 59, 0.5);
            border-color: rgba(20, 184, 166, 0.3);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(20, 184, 166, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--primary);
        }

        .feature-content h4 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--white);
        }

        .feature-content p {
            font-size: 0.875rem;
            color: var(--gray);
        }

        /* Data Table */
        .data-section {
            margin: 4rem 0;
            animation: fadeInUp 0.8s ease 0.6s backwards;
        }

        .data-section h2 {
            font-size: 2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .table-container {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(20, 184, 166, 0.2);
            border-radius: 1rem;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: rgba(20, 184, 166, 0.15);
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 1px solid rgba(20, 184, 166, 0.2);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--gray-light);
        }

        tbody tr {
            transition: background 0.2s;
        }

        tbody tr:hover {
            background: rgba(20, 184, 166, 0.08);
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 3rem 0;
            margin-top: 4rem;
            border-top: 1px solid rgba(20, 184, 166, 0.2);
            color: var(--gray);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .nav-links {
                display: none;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-screen" id="loader">
        <div class="loader"></div>
    </div>

    <!-- Animated Background -->
    <div class="bg-gradient"></div>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo">⚡ PHP CI/CD</div>
            <ul class="nav-links">
                <li><a href="#overview">Overview</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#data">Data</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Hero Section -->
        <section class="hero">
            <h1>
                Automated Deployment
                <br>
                <span class="gradient-text">Made Simple</span>
            </h1>
            <p>
                Modern CI/CD platform powered by GitHub Actions, PHP, and MySQL.
                Deploy with confidence, scale with ease.
            </p>
            <div class="status-badge <?php echo $connection_status ? 'success' : 'error'; ?>">
                <span class="status-dot <?php echo $connection_status ? 'success' : 'error'; ?>"></span>
                <?php echo $connection_status ? 'System Online' : 'System Offline'; ?>
            </div>
        </section>

        <!-- Stats Cards -->
        <div class="cards-grid" id="overview">
            <div class="card">
                <div class="card-icon">🚀</div>
                <h3>PHP Version</h3>
                <p>Runtime Environment</p>
                <div class="value"><?php echo phpversion(); ?></div>
            </div>

            <div class="card">
                <div class="card-icon">🗄️</div>
                <h3>Database</h3>
                <p>MySQL Connection</p>
                <div class="value"><?php echo $connection_status ? 'Connected' : 'Offline'; ?></div>
            </div>

            <div class="card">
                <div class="card-icon">⚙️</div>
                <h3>Deployment</h3>
                <p>CI/CD Pipeline</p>
                <div class="value">Active</div>
            </div>
        </div>

        <!-- Features Section -->
        <section class="features" id="features">
            <h2>Platform Features</h2>
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon">✨</div>
                    <div class="feature-content">
                        <h4>Automated Testing</h4>
                        <p>PHP syntax validation on every push</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🔄</div>
                    <div class="feature-content">
                        <h4>Continuous Deployment</h4>
                        <p>Automatic FTP deployment to InfinityFree</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-content">
                        <h4>Secure Configuration</h4>
                        <p>Config-based credentials management</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <div class="feature-content">
                        <h4>Database Integration</h4>
                        <p>MySQL with migration support</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-content">
                        <h4>Fast Performance</h4>
                        <p>Optimized for speed and reliability</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🎯</div>
                    <div class="feature-content">
                        <h4>Version Control</h4>
                        <p>Git-based workflow with GitHub Actions</p>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($connection_status && count($users) > 0): ?>
        <!-- Data Section -->
        <section class="data-section" id="data">
            <h2>
                <span>👥</span>
                User Database
            </h2>
            <div class="table-container">
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
        </section>
        <?php endif; ?>

        <!-- Footer -->
        <footer>
            <p>Powered by GitHub Actions × PHP × MySQL</p>
            <p style="margin-top: 0.5rem; font-size: 0.875rem;">
                <?php echo $connection_status && !empty($db_info) ? "MySQL {$db_info['version']} | Database: {$db_info['name']}" : 'Awaiting database connection'; ?>
            </p>
            <p>Developed by <b>Ace Borja</b></p>
            <p style="display: flex; justify-content: center; gap: 1rem; margin-top: 1rem;">
                <a href="https://github.com/Sandbox-AceBorja" title="GitHub" style="color: var(--primary); font-size: 1.5rem; text-decoration: none; transition: transform 0.3s;">🐙</a>
                <a href="https://aceborja.vercel.app/" title="Portfolio" style="color: var(--primary); font-size: 1.5rem; text-decoration: none; transition: transform 0.3s;">🌐</a>
            </p>
        </footer>
    </div>

    <script>
        // Loading screen animation
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loader').classList.add('hidden');
            }, 800);
        });

        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add intersection observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        document.querySelectorAll('.card, .feature-item, .data-section').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>