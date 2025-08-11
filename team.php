<?php
session_start();
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

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$message = '';
$messageType = '';

// Pagination settings
$itemsPerPage = 10; // Number of team members per page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get total count of team members
try {
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM team_members");
    $totalMembers = $countStmt->fetch()['total'];
    $totalPages = ceil($totalMembers / $itemsPerPage);
    
    // Ensure current page is within valid range
    if ($currentPage > $totalPages && $totalPages > 0) {
        $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $itemsPerPage;
    }
} catch (PDOException $e) {
    $totalMembers = 0;
    $totalPages = 1;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Add new team member
                $name = trim($_POST['name'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $description = $_POST['description'] ?? '';
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $linkedin_profile = trim($_POST['linkedin_profile'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                
                if (!empty($name) && !empty($position)) {
                    // Validate email if provided
                    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $message = 'Please enter a valid email address.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    // Validate LinkedIn URL if provided
                    if (!empty($linkedin_profile) && !filter_var($linkedin_profile, FILTER_VALIDATE_URL)) {
                        $message = 'Please enter a valid LinkedIn profile URL.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    // Handle image upload
                    $avatar = '';
                    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                        $validationErrors = validateImageUpload($_FILES['avatar']);
                        
                        if (empty($validationErrors)) {
                            $filename = $_FILES['avatar']['name'];
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $newname = generateSecureFilename($filename, $ext);
                            $upload_path = 'uploads/team/' . $newname;
                            
                            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                                $avatar = $upload_path;
                            } else {
                                $message = 'Failed to upload image.';
                                $messageType = 'danger';
                                break;
                            }
                        } else {
                            $message = 'Image validation failed: ' . implode(', ', $validationErrors);
                            $messageType = 'danger';
                            break;
                        }
                    }
                    
                    try {
                        $stmt = $pdo->prepare("INSERT INTO team_members (name, position, title, description, email, phone, linkedin_profile, bio, avatar, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                        if ($stmt->execute([$name, $position, $title, $description, $email, $phone, $linkedin_profile, $bio, $avatar])) {
                            $message = 'Team member added successfully!';
                            $messageType = 'success';
                            
                            // Log the activity
                            logActivity($_SESSION['user_id'], 'add_team_member', "Added team member: {$name}");
                            
                            // Redirect to first page after adding
                            header("Location: team.php?page=1&success=added");
                            exit();
                        } else {
                            $message = 'Failed to add team member.';
                            $messageType = 'danger';
                        }
                    } catch (PDOException $e) {
                        $message = 'Database error: ' . $e->getMessage();
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Name and position are required.';
                    $messageType = 'danger';
                }
                break;
                
            case 'edit':
                // Edit team member
                $id = $_POST['id'] ?? 0;
                $name = trim($_POST['name'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $description = $_POST['description'] ?? '';
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $linkedin_profile = trim($_POST['linkedin_profile'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                
                if (!empty($name) && !empty($position) && $id > 0) {
                    // Validate email if provided
                    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $message = 'Please enter a valid email address.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    // Validate LinkedIn URL if provided
                    if (!empty($linkedin_profile) && !filter_var($linkedin_profile, FILTER_VALIDATE_URL)) {
                        $message = 'Please enter a valid LinkedIn profile URL.';
                        $messageType = 'danger';
                        break;
                    }
                    
                    // Handle image upload
                    $avatar_update = '';
                    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                        $validationErrors = validateImageUpload($_FILES['avatar']);
                        
                        if (empty($validationErrors)) {
                            $filename = $_FILES['avatar']['name'];
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $newname = generateSecureFilename($filename, $ext);
                            $upload_path = 'uploads/team/' . $newname;
                            
                            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                                $avatar_update = $upload_path;
                            } else {
                                $message = 'Failed to upload image.';
                                $messageType = 'danger';
                                break;
                            }
                        } else {
                            $message = 'Image validation failed: ' . implode(', ', $validationErrors);
                            $messageType = 'danger';
                            break;
                        }
                    }
                    
                    try {
                        if ($avatar_update) {
                            $stmt = $pdo->prepare("UPDATE team_members SET name=?, position=?, title=?, description=?, email=?, phone=?, linkedin_profile=?, bio=?, avatar=?, updated_at=NOW() WHERE id=?");
                            $stmt->execute([$name, $position, $title, $description, $email, $phone, $linkedin_profile, $bio, $avatar_update, $id]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE team_members SET name=?, position=?, title=?, description=?, email=?, phone=?, linkedin_profile=?, bio=?, updated_at=NOW() WHERE id=?");
                            $stmt->execute([$name, $position, $title, $description, $email, $phone, $linkedin_profile, $bio, $id]);
                        }
                        
                        if ($stmt->rowCount() > 0) {
                            $message = 'Team member updated successfully!';
                            $messageType = 'success';
                            
                            // Log the activity
                            logActivity($_SESSION['user_id'], 'update_team_member', "Updated team member: {$name}");
                        } else {
                            $message = 'No changes made or team member not found.';
                            $messageType = 'warning';
                        }
                    } catch (PDOException $e) {
                        $message = 'Database error: ' . $e->getMessage();
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Name and position are required.';
                    $messageType = 'danger';
                }
                break;
                
            case 'delete':
                // Delete team member
                $id = $_POST['id'] ?? 0;
                if ($id > 0) {
                    try {
                        // Get member details for logging
                        $stmt = $pdo->prepare("SELECT name, avatar FROM team_members WHERE id = ?");
                        $stmt->execute([$id]);
                        $member = $stmt->fetch();
                        
                        if (!$member) {
                            $message = 'Team member not found.';
                            $messageType = 'danger';
                            break;
                        }
                        
                        // Get avatar path to delete file
                        if ($member['avatar'] && file_exists($member['avatar'])) {
                            unlink($member['avatar']);
                        }
                        
                        $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
                        if ($stmt->execute([$id])) {
                            $message = 'Team member deleted successfully!';
                            $messageType = 'success';
                            
                            // Log the activity
                            logActivity($_SESSION['user_id'], 'delete_team_member', "Deleted team member: {$member['name']}");
                            
                            // Redirect to appropriate page after deletion
                            $remainingMembers = $totalMembers - 1;
                            $newTotalPages = ceil($remainingMembers / $itemsPerPage);
                            if ($currentPage > $newTotalPages && $newTotalPages > 0) {
                                header("Location: team.php?page=" . $newTotalPages . "&success=deleted");
                            } else {
                                header("Location: team.php?page=" . $currentPage . "&success=deleted");
                            }
                            exit();
                        } else {
                            $message = 'Failed to delete team member.';
                            $messageType = 'danger';
                        }
                    } catch (PDOException $e) {
                        $message = 'Database error: ' . $e->getMessage();
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Invalid team member ID.';
                    $messageType = 'danger';
                }
                break;
        }
    }
}

// Check for success messages from redirects
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added':
            $message = 'Team member added successfully!';
            $messageType = 'success';
            break;
        case 'deleted':
            $message = 'Team member deleted successfully!';
            $messageType = 'success';
            break;
    }
}

// Fetch team members with pagination
try {
    $stmt = $pdo->prepare("SELECT * FROM team_members ORDER BY position, name LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $teamMembers = $stmt->fetchAll();
} catch (PDOException $e) {
    $teamMembers = [];
    $message = 'Failed to load team members. Please try again.';
    $messageType = 'warning';
}

// Generate pagination links
function generatePaginationLinks($currentPage, $totalPages, $baseUrl = 'team.php') {
    $links = '';
    
    if ($totalPages <= 1) {
        return $links;
    }
    
    // Previous button
    if ($currentPage > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage - 1) . '"><i class="fas fa-chevron-left"></i></a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
        if ($startPage > 2) {
            $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $links .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage + 1) . '"><i class="fas fa-chevron-right"></i></a></li>';
    } else {
        $links .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>';
    }
    
    return $links;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SapienceCA - Our Team</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    <style>
    body {
        background: #f8f9fa;
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

    /* Table Styles */
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 15px 12px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
        transform: scale(1.01);
    }

    .table tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border-color: #f8f9fa;
    }

    .badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
    }

    .btn-group .btn {
        border-radius: 6px;
        margin: 0 2px;
        padding: 6px 10px;
        font-size: 0.8rem;
    }

    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Enhanced table styling */
    .table tbody tr:nth-child(even) {
        background-color: rgba(248, 249, 250, 0.5);
    }

    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.08) !important;
    }

    /* Contact links styling */
    .table a {
        text-decoration: none;
        color: #667eea;
        transition: color 0.2s ease;
    }

    .table a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Empty state styling */
    .table tbody tr td:empty::before {
        content: "-";
        color: #6c757d;
        font-style: italic;
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
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

    .ck-editor__editable {
        min-height: 200px;
        border-radius: 8px;
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stats-label {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #667eea;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-1px);
    }

    .pagination .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .pagination-controls {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-users me-2"></i>Our Team</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="fas fa-plus me-2"></i>Add Member
                    </button>
                </div>

                <!-- Stats -->
                <!-- <div class="row">
                    <div class="col-md-4">
                        <div class="stats-card text-center">
                            <div class="stats-number"><?php echo $totalMembers; ?></div>
                            <div class="stats-label">Total Team Members</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card text-center">
                            <div class="stats-number"><?php echo $totalPages; ?></div>
                            <div class="stats-label">Total Pages</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card text-center">
                            <div class="stats-number"><?php echo count($teamMembers); ?></div>
                            <div class="stats-label">Showing on Page <?php echo $currentPage; ?></div>
                        </div>
                    </div>
                </div> -->

                <!-- Message Display -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- DataTable Controls -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- <h5><i class="fas fa-users me-2"></i>Team Members</h5> -->
                        <p class="text-muted mb-0">
                            Showing <?php echo count($teamMembers); ?> of <?php echo $totalMembers; ?> members
                            <?php if ($totalPages > 1): ?>
                            (Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>)
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-outline-primary" onclick="exportTeamData()">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>

                <!-- Team Members Table -->
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="teamMembersTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50">Photo</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Title</th>
                                        <th>Contact</th>
                                        <th>LinkedIn</th>
                                        <th>Created</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($teamMembers)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <h5>No Team Members Found</h5>
                                                <p>Start by adding your first team member using the "Add Member" button
                                                    above.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($teamMembers as $member): ?>
                                    <tr data-member-id="<?php echo $member['id']; ?>">
                                        <td>
                                            <?php if ($member['avatar']): ?>
                                            <img src="<?php echo htmlspecialchars($member['avatar']); ?>"
                                                alt="<?php echo htmlspecialchars($member['name']); ?>"
                                                class="rounded-circle" width="40" height="40"
                                                style="object-fit: cover;">
                                            <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($member['name']); ?></div>
                                            <?php if ($member['bio']): ?>
                                            <small
                                                class="text-muted"><?php echo htmlspecialchars(substr($member['bio'], 0, 50)) . (strlen($member['bio']) > 50 ? '...' : ''); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-primary"><?php echo htmlspecialchars($member['position']); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($member['title']): ?>
                                            <span
                                                class="text-muted"><?php echo htmlspecialchars($member['title']); ?></span>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <?php if ($member['email']): ?>
                                                <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>"
                                                    class="text-decoration-none" title="Email">
                                                    <i
                                                        class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($member['email']); ?>
                                                </a>
                                                <?php endif; ?>
                                                <?php if ($member['phone']): ?>
                                                <a href="tel:<?php echo htmlspecialchars($member['phone']); ?>"
                                                    class="text-decoration-none" title="Phone">
                                                    <i
                                                        class="fas fa-phone me-1"></i><?php echo htmlspecialchars($member['phone']); ?>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($member['linkedin_profile']): ?>
                                            <a href="<?php echo htmlspecialchars($member['linkedin_profile']); ?>"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fab fa-linkedin me-1"></i>Profile
                                            </a>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($member['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="viewMember(<?php echo $member['id']; ?>)"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="editMember(<?php echo htmlspecialchars(json_encode($member)); ?>)"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteMember(<?php echo $member['id']; ?>, '<?php echo htmlspecialchars($member['name']); ?>')"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination Controls -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination-controls mt-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Showing <?php echo ($offset + 1); ?> to
                                <?php echo min($offset + count($teamMembers), $totalMembers); ?>
                                of <?php echo $totalMembers; ?> team members
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Team members pagination">
                                <ul class="pagination justify-content-end mb-0">
                                    <?php echo generatePaginationLinks($currentPage, $totalPages); ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Team Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position *</label>
                                    <input type="text" class="form-control" id="position" name="position" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Professional Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="e.g., Senior Developer, Lead Designer">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Detailed Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="linkedin_profile" class="form-label">LinkedIn Profile URL</label>
                            <input type="url" class="form-control" id="linkedin_profile" name="linkedin_profile"
                                placeholder="https://linkedin.com/in/username">
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Short Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"
                                placeholder="Brief professional summary"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                            <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div class="modal fade" id="editMemberModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Team Member</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">Name *</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_position" class="form-label">Position *</label>
                                    <input type="text" class="form-control" id="edit_position" name="position" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Professional Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title">
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Detailed Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="edit_phone" name="phone">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_linkedin_profile" class="form-label">LinkedIn Profile URL</label>
                            <input type="url" class="form-control" id="edit_linkedin_profile" name="linkedin_profile">
                        </div>

                        <div class="mb-3">
                            <label for="edit_bio" class="form-label">Short Bio</label>
                            <textarea class="form-control" id="edit_bio" name="bio" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_avatar" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" id="edit_avatar" name="avatar" accept="image/*">
                            <div class="form-text">Leave empty to keep current photo</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete_member_name"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_member_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Initialize CKEditor for description fields
    let addEditor, editEditor;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize CKEditor for add modal
        if (document.getElementById('description')) {
            ClassicEditor
                .create(document.getElementById('description'))
                .then(editor => {
                    addEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Initialize CKEditor for edit modal
        if (document.getElementById('edit_description')) {
            ClassicEditor
                .create(document.getElementById('edit_description'))
                .then(editor => {
                    editEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }
    });

    // Edit member function
    function editMember(member) {
        document.getElementById('edit_id').value = member.id;
        document.getElementById('edit_name').value = member.name;
        document.getElementById('edit_position').value = member.position;
        document.getElementById('edit_title').value = member.title || '';
        document.getElementById('edit_email').value = member.email || '';
        document.getElementById('edit_phone').value = member.phone || '';
        document.getElementById('edit_linkedin_profile').value = member.linkedin_profile || '';
        document.getElementById('edit_bio').value = member.bio || '';

        if (editEditor) {
            editEditor.setData(member.description || '');
        }

        new bootstrap.Modal(document.getElementById('editMemberModal')).show();
    }

    // View member function
    function viewMember(id) {
        // You can implement a view modal here or redirect to a detail page
        // For now, we'll just show an alert with the member ID
        alert('View member with ID: ' + id +
            '\n\nThis function can be expanded to show detailed member information in a modal or separate page.');
    }

    // Delete member function
    function deleteMember(id, name) {
        document.getElementById('delete_member_id').value = id;
        document.getElementById('delete_member_name').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteMemberModal')).show();
    }

    // Clear form when add modal is closed
    document.getElementById('addMemberModal').addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
        if (addEditor) {
            addEditor.setData('');
        }
    });

    // Clear form when edit modal is closed
    document.getElementById('editMemberModal').addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
        if (editEditor) {
            editEditor.setData('');
        }
    });

    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', filterTeamMembers);
    document.getElementById('positionFilter').addEventListener('change', filterTeamMembers);

    function filterTeamMembers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const positionFilter = document.getElementById('positionFilter').value.toLowerCase();
        const tableRows = document.querySelectorAll('#teamMembersTable tbody tr');

        tableRows.forEach(row => {
            const name = row.querySelector('td:nth-child(2) .fw-bold').textContent.toLowerCase();
            const position = row.querySelector('td:nth-child(3) .badge').textContent.toLowerCase();
            const title = row.querySelector('td:nth-child(4) span').textContent.toLowerCase();
            const bio = row.querySelector('td:nth-child(2) small')?.textContent.toLowerCase() || '';

            const matchesSearch = name.includes(searchTerm) ||
                position.includes(searchTerm) ||
                title.includes(searchTerm) ||
                bio.includes(searchTerm);

            const matchesPosition = !positionFilter || position === positionFilter;

            if (matchesSearch && matchesPosition) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        updateFilteredStats();
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('positionFilter').value = '';
        filterTeamMembers();
    }

    function updateFilteredStats() {
        const visibleRows = document.querySelectorAll('#teamMembersTable tbody tr:not([style*="display: none"])');
        const totalVisible = visibleRows.length;

        // Update stats to show filtered count
        const statsNumber = document.querySelector('.stats-number');
        if (statsNumber) {
            statsNumber.textContent = totalVisible;
        }
    }

    function exportTeamData() {
        const visibleRows = document.querySelectorAll('#teamMembersTable tbody tr:not([style*="display: none"])');
        const exportData = [];

        visibleRows.forEach(row => {
            const member = {
                name: row.querySelector('td:nth-child(2) .fw-bold').textContent,
                position: row.querySelector('td:nth-child(3) .badge').textContent,
                title: row.querySelector('td:nth-child(4) span').textContent,
                email: row.querySelector('td:nth-child(5) a[href^="mailto:"]')?.textContent.replace(/^.*?@/,
                    '@') || '',
                phone: row.querySelector('td:nth-child(5) a[href^="tel:"]')?.textContent || '',
                linkedin: row.querySelector('td:nth-child(6) a')?.href || ''
            };
            exportData.push(member);
        });

        const csvContent = "data:text/csv;charset=utf-8," +
            "Name,Position,Title,Email,Phone,LinkedIn\n" +
            exportData.map(row => Object.values(row).map(value => `"${value}"`).join(',')).join('\n');

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "team_members.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Auto-refresh team data every 5 minutes
    setInterval(() => {
        // You can implement AJAX refresh here if needed
        // For now, just update the timestamp
        const now = new Date();
        console.log('Team data last updated:', now.toLocaleTimeString());
    }, 300000);
    </script>
</body>

</html>