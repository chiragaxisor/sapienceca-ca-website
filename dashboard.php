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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SapienceCA Dashboard - Manage your projects and team">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/chart.js@4.0.0/dist/chart.min.js" as="script">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 10px;
            --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: var(--box-shadow);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .sidebar {
            background: white;
            box-shadow: var(--box-shadow);
            min-height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
        }
        
        .sidebar .nav-link {
            color: var(--dark-color);
            padding: 12px 20px;
            border-radius: var(--border-radius);
            margin: 2px 10px;
            transition: var(--transition);
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .main-content {
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            border: none;
            padding: 15px 20px;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
        }
        
        .stat-card .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .task-item {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .task-item:last-child {
            border-bottom: none;
        }
        
        .task-priority {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .priority-high { background-color: #ffe6e6; color: #d63384; }
        .priority-medium { background-color: #fff3cd; color: #856404; }
        .priority-low { background-color: #d1ecf1; color: #0c5460; }
        
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .welcome-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .quick-action-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }
        
        .quick-action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: var(--border-radius);
            border: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: static;
                min-height: auto;
            }
            
            .welcome-section h1 {
                font-size: 2rem;
            }
            
            .quick-actions {
                flex-direction: column;
            }
            
            .stat-card .stat-number {
                font-size: 1.5rem;
            }
        }
        
        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Flash Messages -->
                <?php if (FlashMessage::has()): ?>
                    <?php $message = FlashMessage::get(); ?>
                    <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>! 👋</h1>
                    <p>Here's what's happening with your projects today.</p>
                    
                    <div class="quick-actions">
                        <a href="projects.php?action=new" class="quick-action-btn">
                            <i class="fas fa-plus me-2"></i>New Project
                        </a>
                        <a href="team.php?action=invite" class="quick-action-btn">
                            <i class="fas fa-user-plus me-2"></i>Invite Team Member
                        </a>
                        <a href="analytics.php" class="quick-action-btn">
                            <i class="fas fa-chart-line me-2"></i>View Analytics
                        </a>
                        <a href="settings.php" class="quick-action-btn">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="stat-icon text-primary">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="stat-number"><?php echo number_format($stats['total_projects'] ?? 0); ?></div>
                            <div class="stat-label">Total Projects</div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="stat-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-number"><?php echo number_format($stats['active_projects'] ?? 0); ?></div>
                            <div class="stat-label">Active Projects</div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="stat-icon text-info">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number"><?php echo number_format($stats['team_members'] ?? 0); ?></div>
                            <div class="stat-label">Team Members</div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6">
                        <div class="card stat-card">
                            <div class="stat-icon text-warning">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-number">$<?php echo number_format($stats['recent_earnings'] ?? 0, 2); ?></div>
                            <div class="stat-label">Recent Earnings (30d)</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Recent Activities -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activities</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recentActivities)): ?>
                                    <p class="text-muted text-center py-3">No recent activities</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($recentActivities, 0, 5) as $activity): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($activity['action'] ?? 'Activity'); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($activity['description'] ?? ''); ?></small>
                                                </div>
                                                <small class="activity-time">
                                                    <?php echo formatTimeAgo($activity['created_at'] ?? ''); ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <div class="text-center mt-3">
                                    <a href="analytics.php" class="btn btn-outline-primary btn-sm">View All Activities</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Tasks -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Upcoming Tasks</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($upcomingTasks)): ?>
                                    <p class="text-muted text-center py-3">No upcoming tasks</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($upcomingTasks, 0, 5) as $task): ?>
                                        <div class="task-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($task['title'] ?? 'Task'); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($task['description'] ?? ''); ?></small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="task-priority priority-<?php echo strtolower($task['priority'] ?? 'medium'); ?>">
                                                        <?php echo ucfirst($task['priority'] ?? 'Medium'); ?>
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        Due: <?php echo formatDate($task['due_date'] ?? ''); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <div class="text-center mt-3">
                                    <a href="projects.php" class="btn btn-outline-primary btn-sm">View All Tasks</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Project Progress Chart -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Project Progress Overview</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="projectProgressChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.0/dist/chart.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize project progress chart
            const ctx = document.getElementById('projectProgressChart').getContext('2d');
            
            // Sample data - replace with actual data from your database
            const projectData = {
                labels: ['Planning', 'Development', 'Testing', 'Deployment', 'Completed'],
                datasets: [{
                    label: 'Projects',
                    data: [
                        <?php echo $stats['total_projects'] ?? 0; ?>,
                        <?php echo $stats['active_projects'] ?? 0; ?>,
                        <?php echo max(0, ($stats['total_projects'] ?? 0) - ($stats['active_projects'] ?? 0) - ($stats['completed_projects'] ?? 0)); ?>,
                        <?php echo max(0, ($stats['total_projects'] ?? 0) - ($stats['active_projects'] ?? 0) - ($stats['completed_projects'] ?? 0)); ?>,
                        <?php echo $stats['completed_projects'] ?? 0; ?>
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8',
                        '#6f42c1',
                        '#fd7e14',
                        '#28a745'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };
            
            new Chart(ctx, {
                type: 'doughnut',
                data: projectData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
            
            // Auto-refresh dashboard every 5 minutes
            setInterval(function() {
                // You can implement AJAX refresh here if needed
                console.log('Dashboard auto-refresh check');
            }, 300000);
            
            // Add loading states to buttons
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.classList.contains('btn-loading')) {
                        this.classList.add('loading');
                        setTimeout(() => {
                            this.classList.remove('loading');
                        }, 2000);
                    }
                });
            });
        });
    </script>
</body>
</html>