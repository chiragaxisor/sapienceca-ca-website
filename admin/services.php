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
require_once 'includes/utils.php';

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
$itemsPerPage = 10; // Number of services per page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Get total count of services
try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM services WHERE user_id = ?");
    $countStmt->execute([$_SESSION['user_id']]);
    $totalServices = $countStmt->fetch()['total'];
    $totalPages = ceil($totalServices / $itemsPerPage);
    
    // Ensure current page is within valid range
    if ($currentPage > $totalPages && $totalPages > 0) {
        $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $itemsPerPage;
    }
} catch (PDOException $e) {
    $totalServices = 0;
    $totalPages = 1;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Add new service
                $title = trim($_POST['title'] ?? '');
                $description = $_POST['description'] ?? '';
                
                if (!empty($title)) {
                    // Handle image upload
                    $image = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $validationErrors = validateImageUpload($_FILES['image']);
                        
                        if (empty($validationErrors)) {
                            $filename = $_FILES['image']['name'];
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $newname = generateSecureFilename($filename, $ext);
                            $upload_path = 'uploads/services/' . $newname;
                            
                            // Create directory if it doesn't exist
                            if (!is_dir('uploads/services/')) {
                                mkdir('uploads/services/', 0755, true);
                            }
                            
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                                $image = $upload_path;
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


                    $icon_image = '';
                    if (isset($_FILES['icon_image']) && $_FILES['icon_image']['error'] == 0) {
                        $validationErrorsicon_image = validateImageUpload($_FILES['icon_image']);
                        
                        if (empty($validationErrorsicon_image)) {
                            $filenameicon_image = $_FILES['icon_image']['name'];
                            $exticon_image = strtolower(pathinfo($filenameicon_image, PATHINFO_EXTENSION));
                            $newnameicon_image = generateSecureFilename($filenameicon_image, $exticon_image);
                            $upload_pathicon_image = 'uploads/services/' . $newnameicon_image;
                            
                            // Create directory if it doesn't exist
                            if (!is_dir('uploads/services/')) {
                                mkdir('uploads/services/', 0755, true);
                            }
                            
                            if (move_uploaded_file($_FILES['icon_image']['tmp_name'], $upload_pathicon_image)) {
                                $icon_image = $upload_pathicon_image;
                            } else {
                                $messageicon_image = 'Failed to upload image.';
                                $messageTypeicon_image = 'danger';
                                break;
                            }
                        } else {
                            $messageicon_image = 'Image validation failed: ' . implode(', ', $validationErrorsicon_image);
                            $messageTypeicon_image = 'danger';
                            break;
                        }
                    }
                    
                    try {
                        $stmt = $pdo->prepare("INSERT INTO services (user_id, title, description, image,icon_image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                        if ($stmt->execute([$_SESSION['user_id'], $title, $description, $image, $icon_image])) {
                            $message = 'Service added successfully!';
                            $messageType = 'success';
                            
                            // Log the activity
                            logActivity($_SESSION['user_id'], 'add_service', "Added service: {$title}");
                            
                            // Redirect to first page after adding
                            header("Location: services.php?page=1&success=added");
                            exit();
                        } else {
                            $message = 'Failed to add service.';
                            $messageType = 'danger';
                        }
                    } catch (PDOException $e) {
                        $message = 'Database error: ' . $e->getMessage();
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Title is required.';
                    $messageType = 'danger';
                }
                break;
                
            case 'edit':
                // Edit service
                $id = $_POST['id'] ?? 0;
                $title = trim($_POST['title'] ?? '');
                $description = $_POST['description'] ?? '';
                
                if (!empty($title) && $id > 0) {
                    // Handle image upload
                    $image_update = '';
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $validationErrors = validateImageUpload($_FILES['image']);
                        
                        if (empty($validationErrors)) {
                            $filename = $_FILES['image']['name'];
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $newname = generateSecureFilename($filename, $ext);
                            $upload_path = 'uploads/services/' . $newname;
                            
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                                $image_update = $upload_path;
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
                    
                    $image_updateicon_image = '';
                    if (isset($_FILES['icon_image']) && $_FILES['icon_image']['error'] == 0) {
                        $validationErrorsicon_image = validateImageUpload($_FILES['icon_image']);
                        
                        if (empty($validationErrorsicon_image)) {
                            $filenameicon_image = $_FILES['icon_image']['name'];
                            $exticon_image = strtolower(pathinfo($filenameicon_image, PATHINFO_EXTENSION));
                            $newnameicon_image = generateSecureFilename($filenameicon_image, $exticon_image);
                            $upload_pathicon_image = 'uploads/services/' . $newnameicon_image;
                            
                            if (move_uploaded_file($_FILES['icon_image']['tmp_name'], $upload_pathicon_image)) {
                                $image_updateicon_image = $upload_pathicon_image;
                            } else {
                                $messageicon_image = 'Failed to upload image.';
                                $messageTypeicon_image = 'danger';
                                break;
                            }
                        } else {
                            $messageicon_image = 'Image validation failed: ' . implode(', ', $validationErrorsicon_image);
                            $messageTypeicon_image = 'danger';
                            break;
                        }
                    }
                    
                    try {
                        if ($image_update) {
                            $stmt = $pdo->prepare("UPDATE services SET title=?, description=?, image=?, updated_at=NOW() WHERE id=?");
                            $stmt->execute([$title, $description, $image_update, $id]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE services SET title=?, description=?, updated_at=NOW() WHERE id=?");
                            $stmt->execute([$title, $description, $id]);
                        }
                        if ($image_updateicon_image) {
                            $stmt = $pdo->prepare("UPDATE services SET title=?, description=?, icon_image=?, updated_at=NOW() WHERE id=?");
                            $stmt->execute([$title, $description, $image_updateicon_image, $id]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE services SET title=?, description=?, updated_at=NOW() WHERE id=?");
                            $stmt->execute([$title, $description, $id]);
                        }
                        
                        if ($stmt->rowCount() > 0) {
                            $message = 'Service updated successfully!';
                            $messageType = 'success';
                            
                            // Log the activity
                            logActivity($_SESSION['user_id'], 'update_service', "Updated service: {$title}");
                        } else {
                            $message = 'No changes made or service not found.';
                            $messageType = 'warning';
                        }
                    } catch (PDOException $e) {
                        $message = 'Database error: ' . $e->getMessage();
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Title is required.';
                    $messageType = 'danger';
                }
                break;
                
            case 'delete':
                // Delete service
                $id = $_POST['id'] ?? 0;
                if ($id > 0) {
                    try {
                        // Get service details for logging
                        $stmt = $pdo->prepare("SELECT title, image FROM services WHERE id = ? AND user_id = ?");
                        $stmt->execute([$id, $_SESSION['user_id']]);
                        $service = $stmt->fetch();
                        
                        if (!$service) {
                            $message = 'Service not found.';
                            $messageType = 'danger';
                            break;
                        }
                        
                        // Get image path to delete file
                        if ($service['image'] && file_exists($service['image'])) {
                            unlink($service['image']);
                        }
                        
                        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                        if ($stmt->execute([$id])) {
                            $message = 'Service deleted successfully!';
                            $messageType = 'success';
                            
                            // Log the activity
                            logActivity($_SESSION['user_id'], 'delete_service', "Deleted service: {$service['title']}");
                            
                            // Redirect to appropriate page after deletion
                            $remainingServices = $totalServices - 1;
                            $newTotalPages = ceil($remainingServices / $itemsPerPage);
                            if ($currentPage > $newTotalPages && $newTotalPages > 0) {
                                header("Location: services.php?page=" . $newTotalPages . "&success=deleted");
                            } else {
                                header("Location: services.php?page=" . $currentPage . "&success=deleted");
                            }
                            exit();
                        } else {
                            $message = 'Failed to delete service.';
                            $messageType = 'danger';
                        }
                    } catch (PDOException $e) {
                        $message = 'Database error: ' . $e->getMessage();
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Invalid service ID.';
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
            $message = 'Service added successfully!';
            $messageType = 'success';
            break;
        case 'deleted':
            $message = 'Service deleted successfully!';
            $messageType = 'success';
            break;
    }
}

// Fetch services with pagination
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE user_id = ? ORDER BY id ASC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(2, $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
    $message = 'Failed to load services. Please try again.';
    $messageType = 'warning';
}

// Generate pagination links
function generatePaginationLinks($currentPage, $totalPages, $baseUrl = 'services.php') {
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

    <?php include 'includes/header.php'; ?>
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
                    <h2><i class="fas fa-cogs me-2"></i>Our Services</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                        <i class="fas fa-plus me-2"></i>Add Service
                    </button>
                </div>

                
                <!-- Message Display -->
                <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Services Controls -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5><i class="fas fa-cogs me-2"></i>Services</h5>
                        <p class="text-muted mb-0">
                            Showing <?php echo count($services); ?> of <?php echo $totalServices; ?> services
                            <?php if ($totalPages > 1): ?>
                            (Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>)
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-outline-primary" onclick="exportServicesData()">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>

                <!-- Services Table -->
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="servicesTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="100">Image</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Created</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($services)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-cogs fa-3x mb-3"></i>
                                                <h5>No Services Found</h5>
                                                <p>Start by adding your first service using the "Add Service" button
                                                    above.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($services as $service): ?>
                                    <tr data-service-id="<?php echo $service['id']; ?>">
                                        <td>
                                            <?php if ($service['image']): ?>
                                            <img src="<?php echo htmlspecialchars($service['image']); ?>"
                                                alt="<?php echo htmlspecialchars($service['title']); ?>"
                                                class="service-image">
                                            <?php else: ?>
                                            <div class="service-image-placeholder">
                                                <i class="fas fa-cogs"></i>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($service['title']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="description-preview" onclick="toggleDescription(this)">
                                                <?php 
                                                        $description = strip_tags($service['description']);
                                                        echo htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : ''); 
                                                        ?>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($service['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="viewService(<?php echo $service['id']; ?>)"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="deleteService(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars($service['title']); ?>')"
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
                                <?php echo min($offset + count($services), $totalServices); ?>
                                of <?php echo $totalServices; ?> services
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Services pagination">
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

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label for="title" class="form-label">Service Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required
                                placeholder="e.g., Web Development, Mobile App Development">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Service Description</label>
                            <textarea class="form-control" id="description" name="description" rows="6"
                                placeholder="Describe your service in detail..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Service Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                        </div>
                        <div class="mb-3">
                            <label for="icon_image" class="form-label">Service Icon Image</label>
                            <input type="file" class="form-control" id="icon_image" name="icon_image" accept="image/*">
                            <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Service Title *</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Service Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="6"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Service Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <div class="form-text">Leave empty to keep current image</div>
                        </div>
                        <div class="mb-3">
                            <label for="icon_image" class="form-label">Service Icon Image</label>
                            <input type="file" class="form-control" id="icon_image" name="icon_image" accept="image/*">
                            <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete_service_title"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_service_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    // Edit service function
    function editService(service) {
        document.getElementById('edit_id').value = service.id;
        document.getElementById('edit_title').value = service.title;

        if (editEditor) {
            editEditor.setData(service.description || '');
        }

        new bootstrap.Modal(document.getElementById('editServiceModal')).show();
    }

    // View service function
    function viewService(id) {
        // You can implement a view modal here or redirect to a detail page
        alert('View service with ID: ' + id +
            '\n\nThis function can be expanded to show detailed service information in a modal or separate page.');
    }

    // Delete service function
    function deleteService(id, title) {
        document.getElementById('delete_service_id').value = id;
        document.getElementById('delete_service_title').textContent = title;
        new bootstrap.Modal(document.getElementById('deleteServiceModal')).show();
    }

    // Toggle description preview
    function toggleDescription(element) {
        element.classList.toggle('expanded');
    }

    // Clear form when add modal is closed
    document.getElementById('addServiceModal').addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
        if (addEditor) {
            addEditor.setData('');
        }
    });

    // Clear form when edit modal is closed
    document.getElementById('editServiceModal').addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
        if (editEditor) {
            editEditor.setData('');
        }
    });

    function exportServicesData() {
        const visibleRows = document.querySelectorAll('#servicesTable tbody tr:not([style*="display: none"])');
        const exportData = [];

        visibleRows.forEach(row => {
            const service = {
                title: row.querySelector('td:nth-child(2) .fw-bold').textContent,
                description: row.querySelector('td:nth-child(3) .description-preview').textContent,
                created: row.querySelector('td:nth-child(4) small').textContent
            };
            exportData.push(service);
        });

        const csvContent = "data:text/csv;charset=utf-8," +
            "Title,Description,Created\n" +
            exportData.map(row => Object.values(row).map(value => `"${value}"`).join(',')).join('\n');

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "services.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Auto-refresh services data every 5 minutes
    setInterval(() => {
        // You can implement AJAX refresh here if needed
        // For now, just update the timestamp
        const now = new Date();
        console.log('Services data last updated:', now.toLocaleTimeString());
    }, 300000);
    </script>
</body>

</html>