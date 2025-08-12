<?php
session_start();

// Include configuration and utilities first
require_once 'config.php';
require_once 'includes/utils.php';

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     

    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '', 'email');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields';
        } else {
            try {
                $pdo = getDBConnection();
                
                // Check for login attempts
                $attempts = checkLoginAttempts($_SERVER['REMOTE_ADDR'] ?? '');
                if ($attempts >= LOGIN_MAX_ATTEMPTS) {
                    $error = 'Too many login attempts. Please try again later.';
                } else {
                    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();
                    
                    if ($user && password_verify($password, $user['password'])) {
                        // Reset login attempts on successful login
                        resetLoginAttempts($_SERVER['REMOTE_ADDR'] ?? '');
                        
                        // Set session data
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['last_activity'] = time();
                        
                        // Log successful login
                        logActivity($user['id'], 'login', 'User logged in successfully');
                        
                        // Redirect to dashboard
                        redirect('dashboard.php');
                    } else {
                        // Increment login attempts
                        incrementLoginAttempts($_SERVER['REMOTE_ADDR'] ?? '');
                        $error = 'Invalid email or password';
                    }
                }
            } catch (Exception $e) {
              
                error_log("Login error: " . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}

// Generate CSRF token for the form
$csrf_token = generateCSRFToken();

// Helper functions for login attempts
function checkLoginAttempts($ip) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_attempts 
            WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");
        $stmt->execute([$ip, LOGIN_LOCKOUT_TIME]);
        $result = $stmt->fetch();
        return $result ? (int)$result['attempts'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function incrementLoginAttempts($ip) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO login_attempts (ip_address, created_at) 
            VALUES (?, NOW())
        ");
        $stmt->execute([$ip]);
    } catch (Exception $e) {
        error_log("Failed to log login attempt: " . $e->getMessage());
    }
}

function resetLoginAttempts($ip) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            DELETE FROM login_attempts 
            WHERE ip_address = ?
        ");
        $stmt->execute([$ip]);
    } catch (Exception $e) {
        error_log("Failed to reset login attempts: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to SapienceCA - Your AI-powered business solution">
    <meta name="robots" content="noindex, nofollow">
    <title>SapienceCA - Login</title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --border-radius: 20px;
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .login-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 400px;
            width: 100%;
            margin: 1rem;
        }
        
        .login-header {
            text-align: center;
            padding: 2rem 0 1rem;
        }
        
        .login-header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        
        .login-header p {
            color: #666;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: var(--transition);
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: var(--transition);
            font-size: 1rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 0.9rem;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .text-muted a {
            color: #667eea;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .text-muted a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-card {
                margin: 0.5rem;
                border-radius: 15px;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-4">
                        <div class="login-header">
                            <h1><i class="fas fa-brain text-primary"></i> SapienceCA</h1>
                            <p>Welcome back! Please login to your account.</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="loginForm" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                           required autocomplete="email">
                                </div>
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           required autocomplete="current-password">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" 
                                            style="border-left: none; border-radius: 0 10px 10px 0;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="passwordError"></div>
                            </div>
                            
                            <!-- <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div> -->
                            
                            <button type="submit" class="btn btn-primary btn-login w-100" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </form>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    // Show loading state
                    loginBtn.classList.add('btn-loading');
                    loginBtn.disabled = true;
                }
                
                form.classList.add('was-validated');
            });
            
            // Real-time validation
            const emailInput = document.getElementById('email');
            // const passwordInput = document.getElementById('password');
            
            emailInput.addEventListener('input', function() {
                validateEmail(this);
            });
            
            passwordInput.addEventListener('input', function() {
                validatePassword(this);
            });
            
            function validateEmail(input) {
                const email = input.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email === '') {
                    showError(input, 'Email is required');
                } else if (!emailRegex.test(email)) {
                    showError(input, 'Please enter a valid email address');
                } else {
                    showSuccess(input);
                }
            }
            
            function validatePassword(input) {
                const password = input.value;
                
                if (password === '') {
                    showError(input, 'Password is required');
                } else if (password.length < 6) {
                    showError(input, 'Password must be at least 6 characters');
                } else {
                    showSuccess(input);
                }
            }
            
            function showError(input, message) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                
                const errorElement = document.getElementById(input.id + 'Error');
                if (errorElement) {
                    errorElement.textContent = message;
                }
            }
            
            function showSuccess(input) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                
                const errorElement = document.getElementById(input.id + 'Error');
                if (errorElement) {
                    errorElement.textContent = '';
                }
            }
            
            // Auto-focus email field
            emailInput.focus();
            
            // Handle Enter key
            form.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</body>
</html>
