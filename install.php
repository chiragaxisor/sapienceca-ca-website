<?php
session_start();

// Check if already installed
if (file_exists('installed.txt')) {
    die('SapienceCA is already installed. Delete installed.txt to reinstall.');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 1) {
        // Database connection test
        $host = $_POST['db_host'] ?? 'localhost';
        $dbname = $_POST['db_name'] ?? 'sapienceca';
        $username = $_POST['db_user'] ?? 'root';
        $password = $_POST['db_pass'] ?? 'password';
        
        try {
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
            $pdo->exec("USE `$dbname`");
            
            // Store connection details for next step
            $_SESSION['install_db'] = [
                'host' => $host,
                'dbname' => $dbname,
                'username' => $username,
                'password' => $password
            ];
            
            header("Location: install.php?step=2");
            exit();
        } catch(PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
        }
    } elseif ($step == 2) {
        // Create tables
        $db = $_SESSION['install_db'];
        try {
            $pdo = new PDO("mysql:host={$db['host']};dbname={$db['dbname']}", $db['username'], $db['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Read and execute SQL file
            $sql = file_get_contents('database.sql');
            $pdo->exec($sql);
            
            // Create uploads directory
            if (!is_dir('uploads/team')) {
                mkdir('uploads/team', 0755, true);
            }
            
                   // Create config file
                   $config_content = "<?php
// Database configuration
define('DB_HOST', '{$db['host']}');
define('DB_NAME', '{$db['dbname']}');
define('DB_USER', '{$db['username']}');
define('DB_PASS', '{$db['password']}');

// Application configuration
define('APP_NAME', 'SapienceCA');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://' . \$_SERVER['HTTP_HOST'] . dirname(\$_SERVER['REQUEST_URI']));

// Session configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Security configuration
define('PASSWORD_MIN_LENGTH', 6);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Database connection function
function getDBConnection() {
    try {
        \$pdo = new PDO(
            \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8mb4\",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return \$pdo;
    } catch(PDOException \$e) {
        die(\"Connection failed: \" . \$e->getMessage());
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset(\$_SESSION['user_id']);
}

// Redirect function
function redirect(\$url) {
    header(\"Location: \" . \$url);
    exit();
}

// Sanitize output
function sanitize(\$data) {
    return htmlspecialchars(\$data, ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset(\$_SESSION['csrf_token'])) {
        \$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return \$_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken(\$token) {
    return isset(\$_SESSION['csrf_token']) && hash_equals(\$_SESSION['csrf_token'], \$token);
}

// Log activity
function logActivity(\$user_id, \$action, \$details = '') {
    try {
        \$pdo = getDBConnection();
        \$stmt = \$pdo->prepare(\"INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())\");
        \$stmt->execute([\$user_id, \$action, \$details, \$_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Exception \$e) {
        // Log error silently
        error_log(\"Failed to log activity: \" . \$e->getMessage());
    }
}

// Get user role
function getUserRole(\$user_id) {
    try {
        \$pdo = getDBConnection();
        \$stmt = \$pdo->prepare(\"SELECT role FROM users WHERE id = ?\");
        \$stmt->execute([\$user_id]);
        \$result = \$stmt->fetch();
        return \$result ? \$result['role'] : 'user';
    } catch (Exception \$e) {
        return 'user';
    }
}

// Check if user has permission
function hasPermission(\$user_id, \$permission) {
    \$role = getUserRole(\$user_id);

    if (\$role === 'admin') {
        return true;
    }

    // Add more role-based permissions here
    \$permissions = [
        'user' => ['view_dashboard', 'view_team', 'edit_profile'],
        'manager' => ['view_dashboard', 'view_team', 'edit_team', 'view_projects', 'edit_projects'],
    ];

    return isset(\$permissions[\$role]) && in_array(\$permission, \$permissions[\$role]);
}
?>";
            
            file_put_contents('config.php', $config_content);
            
            // Mark as installed
            file_put_contents('installed.txt', date('Y-m-d H:i:s'));
            
            $success = 'Installation completed successfully!';
            $step = 3;
        } catch(Exception $e) {
            $error = "Installation failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SapienceCA - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .install-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
            width: 100%;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 1rem;
            font-weight: bold;
            color: white;
        }
        .step.active {
            background: #667eea;
        }
        .step.completed {
            background: #28a745;
        }
        .step.pending {
            background: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card install-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1><i class="fas fa-brain text-primary"></i> SapienceCA</h1>
                            <p class="lead">Installation Wizard</p>
                        </div>
                        
                        <!-- Step Indicator -->
                        <div class="step-indicator">
                            <div class="step <?php echo $step >= 1 ? 'completed' : 'pending'; ?>">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="step <?php echo $step >= 2 ? 'completed' : ($step == 2 ? 'active' : 'pending'); ?>">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="step <?php echo $step >= 3 ? 'completed' : 'pending'; ?>">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($step == 1): ?>
                            <!-- Step 1: Database Configuration -->
                            <h4 class="mb-3">Database Configuration</h4>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" value="sapienceca" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-arrow-right"></i> Continue
                                </button>
                            </form>
                            
                        <?php elseif ($step == 2): ?>
                            <!-- Step 2: Installation Progress -->
                            <h4 class="mb-3">Installing SapienceCA</h4>
                            <p>Please wait while we set up your database and configuration...</p>
                            
                            <div class="progress mb-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                            </div>
                            
                            <form method="POST">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-cogs"></i> Install Now
                                </button>
                            </form>
                            
                        <?php elseif ($step == 3): ?>
                            <!-- Step 3: Installation Complete -->
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h4 class="text-success">Installation Complete!</h4>
                                <p class="mb-4">SapienceCA has been successfully installed on your system.</p>
                                
                                <div class="alert alert-info">
                                    <h6>Default Login Credentials:</h6>
                                    <p class="mb-1"><strong>Email:</strong> admin@sapienceca.com</p>
                                    <p class="mb-0"><strong>Password:</strong> admin123</p>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i> Go to Login
                                    </a>
                                    <a href="dashboard.php" class="btn btn-outline-primary">
                                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                                    </a>
                                </div>
                                
                                <div class="mt-4">
                                    <small class="text-muted">
                                        <strong>Important:</strong> Delete the install.php file for security reasons.
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
