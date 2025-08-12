<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Define secure access constant for navbar
define('SECURE_ACCESS', true);

// Include configuration
require_once 'config.php';

// Database connection
try {
    $pdo = getDBConnection();
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        
        if (empty($name) || empty($email)) {
            $error = 'Name and email are required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $_SESSION['user_id']]);
                $message = 'Profile updated successfully!';
                
                // Update session data
                $_SESSION['user_name'] = $name;
                $user['name'] = $name;
                $user['email'] = $email;
            } catch(PDOException $e) {
                $error = 'Failed to update profile. Please try again.';
            }
        }
    } elseif (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'New password must be at least 6 characters long.';
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $currentHash = $stmt->fetchColumn();
            
            if (password_verify($currentPassword, $currentHash)) {
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$newHash, $_SESSION['user_id']]);
                $message = 'Password changed successfully!';
            } else {
                $error = 'Current password is incorrect.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SapienceCA - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
    }

    .sidebar {
        min-height: calc(100vh - 56px);
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 20px;
        border-radius: 8px;
        margin: 2px 10px;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
    }

    .main-content {
        padding: 2rem;
    }

    .settings-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 1.5rem;
    }

    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1 class="display-6">Settings ⚙️</h1>
                    <p class="lead mb-0">Manage your account preferences and security settings.</p>
                </div>

                <!-- Message Display -->
                <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Profile Settings -->
                <div class="settings-card">
                    <h4 class="mb-3"><i class="fas fa-user me-2"></i>Profile Settings</h4>
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>

                <!-- Password Change -->
                <div class="settings-card">
                    <h4 class="mb-3"><i class="fas fa-lock me-2"></i>Change Password</h4>
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>

                <!-- System Settings -->
                <div class="settings-card">
                    <h4 class="mb-3"><i class="fas fa-cogs me-2"></i>System Settings</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="language" class="form-label">Language</label>
                            <select class="form-select" id="language">
                                <option value="en" selected>English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone">
                                <option value="UTC" selected>UTC</option>
                                <option value="EST">Eastern Time</option>
                                <option value="PST">Pacific Time</option>
                                <option value="GMT">Greenwich Mean Time</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifications" checked>
                        <label class="form-check-label" for="notifications">
                            Enable Email Notifications
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="dark_mode">
                        <label class="form-check-label" for="dark_mode">
                            Dark Mode
                        </label>
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>