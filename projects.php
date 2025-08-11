<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

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

// Get projects data (placeholder for now)
$projects = [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SapienceCA - Projects</title>
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

    .project-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border: none;
        transition: transform 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .project-card:hover {
        transform: translateY(-5px);
    }

    .project-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-completed {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-brain"></i> SapienceCA
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1 class="display-6">Projects Management 📋</h1>
                    <p class="lead mb-0">Track and manage your ongoing projects and initiatives.</p>
                </div>

                <!-- Add Project Button -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-project-diagram me-2"></i>Our Projects</h2>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Project
                    </button>
                </div>

                <!-- Projects Grid -->
                <div class="row">
                    <?php if (empty($projects)): ?>
                    <div class="col-12">
                        <div class="project-card text-center">
                            <div class="text-muted">
                                <i class="fas fa-project-diagram fa-3x mb-3"></i>
                                <h5>No Projects Found</h5>
                                <p>Start by adding your first project using the "Add Project" button above.</p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="project-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($project['name']); ?></h5>
                                <span class="project-status status-<?php echo $project['status']; ?>">
                                    <?php echo ucfirst($project['status']); ?>
                                </span>
                            </div>
                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars($project['description']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Due: <?php echo date('M d, Y', strtotime($project['due_date'])); ?>
                                </small>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                    <button class="btn btn-sm btn-outline-success">Edit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>