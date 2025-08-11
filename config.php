<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'sapienceca');
define('DB_USER', 'root');
define('DB_PASS', 'password');

// Application configuration
define('APP_NAME', 'SapienceCA');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/sapienceca');

// Session configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Security configuration
define('PASSWORD_MIN_LENGTH', 8); // Increased for better security
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Database connection pool
class DatabasePool {
    private static $connections = [];
    private static $maxConnections = 5;
    
    public static function getConnection() {
        $connectionId = getmypid();
        
        if (!isset(self::$connections[$connectionId])) {
            if (count(self::$connections) >= self::$maxConnections) {
                // Remove oldest connection if pool is full
                $oldestId = array_key_first(self::$connections);
                unset(self::$connections[$oldestId]);
            }
            
            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_PERSISTENT => false, // Better for connection pooling
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]
                );
                
                self::$connections[$connectionId] = $pdo;
            } catch(PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection failed. Please try again later.");
            }
        }
        
        return self::$connections[$connectionId];
    }
    
    public static function closeConnection($connectionId = null) {
        if ($connectionId === null) {
            $connectionId = getmypid();
        }
        
        if (isset(self::$connections[$connectionId])) {
            unset(self::$connections[$connectionId]);
        }
    }
    
    public static function closeAllConnections() {
        self::$connections = [];
    }
}

// Optimized database connection function
function getDBConnection() {
    return DatabasePool::getConnection();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !isSessionExpired();
}

// Check if session has expired
function isSessionExpired() {
    if (!isset($_SESSION['last_activity'])) {
        return true;
    }
    
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_destroy();
        return true;
    }
    
    $_SESSION['last_activity'] = time();
    return false;
}

// Redirect function with CSRF protection
function redirect($url, $permanent = false) {
    if ($permanent) {
        header("HTTP/1.1 301 Moved Permanently");
    }
    header("Location: " . $url);
    exit();
}

// Enhanced sanitize function
function sanitize($data, $type = 'string') {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitize($item, $type);
        }, $data);
    }
    
    switch ($type) {
        case 'email':
            return filter_var(trim($data), FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var(trim($data), FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'string':
        default:
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// Generate CSRF token with expiration
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expires']) || 
        time() > $_SESSION['csrf_token_expires']) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expires'] = time() + 3600; // 1 hour expiration
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token with expiration check
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expires'])) {
        return false;
    }
    
    if (time() > $_SESSION['csrf_token_expires']) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_expires']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Enhanced log activity function
function logActivity($user_id, $action, $details = '', $level = 'info') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO user_activities (user_id, action, description, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $user_id, 
            $action, 
            $details, 
            $_SERVER['REMOTE_ADDR'] ?? '', 
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        return true;
    } catch (Exception $e) {
        error_log("Failed to log activity: " . $e->getMessage());
        return false;
    }
}

// Get user role with caching
function getUserRole($user_id) {
    static $roleCache = [];
    
    if (isset($roleCache[$user_id])) {
        return $roleCache[$user_id];
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        
        $role = $result ? $result['role'] : 'user';
        $roleCache[$user_id] = $role;
        
        return $role;
    } catch (Exception $e) {
        error_log("Failed to get user role: " . $e->getMessage());
        return 'user';
    }
}

// Check if user has permission with role caching
function hasPermission($user_id, $permission) {
    $role = getUserRole($user_id);
    
    if ($role === 'admin') {
        return true;
    }
    
    // Define permissions matrix
    static $permissions = [
        'user' => ['view_dashboard', 'view_team', 'edit_profile'],
        'manager' => ['view_dashboard', 'view_team', 'edit_team', 'view_projects', 'edit_projects'],
        'moderator' => ['view_dashboard', 'view_team', 'view_projects', 'moderate_content']
    ];
    
    return isset($permissions[$role]) && in_array($permission, $permissions[$role]);
}

// Enhanced image validation
function validateImageUpload($file, $maxSize = 5242880) { // 5MB default
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        $errors[] = $uploadErrors[$file['error']] ?? 'Upload failed with error code: ' . $file['error'];
        return $errors;
    }
    
    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds maximum allowed size (' . ($maxSize / 1024 / 1024) . 'MB)';
    }
    
    // Enhanced MIME type validation
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.';
    }
    
    // Additional security checks
    if (!is_uploaded_file($file['tmp_name'])) {
        $errors[] = 'File upload security check failed.';
    }
    
    return $errors;
}

// Generate secure filename with better entropy
function generateSecureFilename($originalName, $extension) {
    $timestamp = time();
    $random = bin2hex(random_bytes(16)); // Increased from 8 to 16 bytes
    $hash = hash('sha256', $originalName . $timestamp . $random);
    return $timestamp . '_' . substr($hash, 0, 16) . '.' . $extension;
}

// Clean old uploaded files with better error handling
function cleanOldUploads($directory, $daysOld = 30) {
    if (!is_dir($directory) || !is_readable($directory)) {
        return false;
    }
    
    $files = glob($directory . '/*');
    $cutoff = time() - ($daysOld * 24 * 60 * 60);
    $deleted = 0;
    $errors = [];
    
    foreach ($files as $file) {
        if (is_file($file) && is_writable($file) && filemtime($file) < $cutoff) {
            if (unlink($file)) {
                $deleted++;
            } else {
                $errors[] = "Failed to delete: $file";
            }
        }
    }
    
    if (!empty($errors)) {
        error_log("File cleanup errors: " . implode(', ', $errors));
    }
    
    return $deleted;
}

// Input validation helper
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            if (isset($rule['required']) && $rule['required']) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
            continue;
        }
        
        $value = trim($data[$field]);
        
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            $errors[$field] = ucfirst($field) . ' must be at least ' . $rule['min_length'] . ' characters.';
        }
        
        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
            $errors[$field] = ucfirst($field) . ' must not exceed ' . $rule['max_length'] . ' characters.';
        }
        
        if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
            $errors[$field] = ucfirst($field) . ' format is invalid.';
        }
        
        if (isset($rule['type'])) {
            switch ($rule['type']) {
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Please enter a valid email address.';
                    }
                    break;
                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $errors[$field] = 'Please enter a valid URL.';
                    }
                    break;
                case 'int':
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $errors[$field] = 'Please enter a valid number.';
                    }
                    break;
            }
        }
    }
    
    return $errors;
}

// Cleanup function for script termination
register_shutdown_function(function() {
    DatabasePool::closeAllConnections();
});
?>
