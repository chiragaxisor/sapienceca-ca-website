<?php
session_start();

// Include configuration and utilities first
require_once 'config.php';
require_once 'includes/utils.php';

// Define secure access constant for navbar
define('SECURE_ACCESS', true);

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

// Check session timeout
if (isSessionExpired()) {
    session_destroy();
    FlashMessage::set('warning', 'Your session has expired. Please login again.');
    redirect('index.php');
}

// Update last activity
$_SESSION['last_activity'] = time();

try {
    $pdo = getDBConnection();
    
    // Get user data
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        FlashMessage::set('error', 'User account not found or inactive.');
        redirect('index.php');
    }
    
    // Get dashboard statistics
    $stats = getDashboardStats($pdo, $userId);
    
    // Get recent activities
    $recentActivities = getRecentActivities($pdo, $userId);
    
    // Get upcoming tasks
    $upcomingTasks = getUpcomingTasks($pdo, $userId);
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    FlashMessage::set('error', 'An error occurred while loading the dashboard.');
    $stats = [];
    $recentActivities = [];
    $upcomingTasks = [];
}

// Helper functions
function getDashboardStats($pdo, $userId) {
    try {
        $stats = [];
        
        // Total projects
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projects WHERE user_id = ? AND status != 'deleted'");
        $stmt->execute([$userId]);
        $stats['total_projects'] = $stmt->fetch()['count'] ?? 0;
        
        // Active projects
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projects WHERE user_id = ? AND status = 'active'");
        $stmt->execute([$userId]);
        $stats['active_projects'] = $stmt->fetch()['count'] ?? 0;
        
        // Completed projects
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projects WHERE user_id = ? AND status = 'completed'");
        $stmt->execute([$userId]);
        $stats['completed_projects'] = $stmt->fetch()['count'] ?? 0;
        
        // Total team members
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM team_members WHERE user_id = ? AND status = 'active'");
        $stmt->execute([$userId]);
        $stats['team_members'] = $stmt->fetch()['count'] ?? 0;
        
        // Recent earnings (last 30 days)
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as total 
            FROM transactions 
            WHERE user_id = ? AND type = 'income' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$userId]);
        $stats['recent_earnings'] = $stmt->fetch()['total'] ?? 0;
        
        return $stats;
    } catch (Exception $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        return [];
    }
}

function getRecentActivities($pdo, $userId, $limit = 10) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM user_activities 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting recent activities: " . $e->getMessage());
        return [];
    }
}

function getUpcomingTasks($pdo, $userId, $limit = 5) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM tasks 
            WHERE assigned_to = ? AND status IN ('pending', 'in_progress') AND due_date >= CURDATE()
            ORDER BY due_date ASC, priority DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting upcoming tasks: " . $e->getMessage());
        return [];
    }
}

// Get page title
$pageTitle = 'Dashboard - ' . APP_NAME;
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <?php include 'includes/header.php'; ?>
    
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Flash Messages -->
                <?php if (FlashMessage::has()): ?>
                    <?php $message = FlashMessage::get(); ?>
                    <div class="alert  alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1>Welcome back, Admin! 👋</h1>
                    <p>Here's what's happening with your projects today.</p>
                </div>
            </main>
        </div>
    </div>
</body>
</html>