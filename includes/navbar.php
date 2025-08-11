<?php
// Ensure this file is included, not accessed directly
if (!defined('SECURE_ACCESS')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

// Get current user data
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
}

// Get notification count
$notificationCount = 0;
if ($currentUser) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count FROM user_activities 
            WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute([$currentUser['id']]);
        $notificationCount = $stmt->fetch()['count'] ?? 0;
    } catch (Exception $e) {
        error_log("Error fetching notification count: " . $e->getMessage());
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-brain me-2"></i>
            <?php echo htmlspecialchars(APP_NAME); ?>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="projects.php">
                        <i class="fas fa-project-diagram me-1"></i>Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="team.php">
                        <i class="fas fa-users me-1"></i>Team
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="analytics.php">
                        <i class="fas fa-chart-line me-1"></i>Analytics
                    </a>
                </li>
            </ul>

            <!-- Right Side Navigation -->
            <ul class="navbar-nav ms-auto">
                <!-- Search -->
                <li class="nav-item me-2">
                    <form class="d-flex" role="search" id="globalSearchForm">
                        <div class="input-group">
                            <input class="form-control form-control-sm" type="search" 
                                   placeholder="Search..." aria-label="Search" id="globalSearchInput">
                            <button class="btn btn-outline-light btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </li>

                <!-- Notifications -->
                <li class="nav-item dropdown me-2">
                    <a class="nav-link dropdown-toggle position-relative" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <?php if ($notificationCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $notificationCount > 99 ? '99+' : $notificationCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="navbarDropdown">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li id="notificationsList">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-muted" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="notifications.php">View All</a></li>
                    </ul>
                </li>

                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if ($currentUser && $currentUser['avatar']): ?>
                            <img src="<?php echo htmlspecialchars($currentUser['avatar']); ?>" 
                                 alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                        <?php else: ?>
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                                 style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <span class="d-none d-lg-inline">
                            <?php echo htmlspecialchars($currentUser['name'] ?? 'User'); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="settings.php">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                    </li>
                </ul>
        </div>
    </div>
</nav>

<!-- Global Search Modal -->
<div class="modal fade" id="globalSearchModal" tabindex="-1" aria-labelledby="globalSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalSearchModalLabel">Search Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="searchResults">
                    <div class="text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Enter your search query above to find projects, tasks, and team members.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('globalSearchForm');
    const searchInput = document.getElementById('globalSearchInput');
    const searchModal = new bootstrap.Modal(document.getElementById('globalSearchModal'));
    const searchResults = document.getElementById('searchResults');
    
    // Global search functionality
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        
        if (query.length < 2) {
            return;
        }
        
        // Show loading state
        searchResults.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Searching...</span>
                </div>
                <p class="mt-2 text-muted">Searching for "${query}"...</p>
            </div>
        `;
        
        // Perform search via AJAX
        fetch('search_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ query: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.results.length > 0) {
                displaySearchResults(data.results, query);
            } else {
                searchResults.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No results found for "${query}"</p>
                        <small class="text-muted">Try different keywords or check your spelling.</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResults.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p class="text-muted">An error occurred while searching.</p>
                    <small class="text-muted">Please try again later.</small>
                </div>
            `;
        });
        
        searchModal.show();
    });
    
    // Load notifications when dropdown is opened
    const notificationDropdown = document.querySelector('.notification-dropdown');
    const notificationsList = document.getElementById('notificationsList');
    
    notificationDropdown.addEventListener('show.bs.dropdown', function() {
        loadNotifications();
    });
    
    function loadNotifications() {
        fetch('notifications_api.php', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.notifications.length > 0) {
                displayNotifications(data.notifications);
            } else {
                notificationsList.innerHTML = `
                    <li class="px-3 py-2 text-muted text-center">
                        <small>No new notifications</small>
                    </li>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationsList.innerHTML = `
                <li class="px-3 py-2 text-muted text-center">
                    <small>Error loading notifications</small>
                </li>
            `;
        });
    }
    
    function displayNotifications(notifications) {
        const html = notifications.map(notification => `
            <li>
                <a class="dropdown-item" href="${notification.link || '#'}">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-2">
                            <i class="fas fa-${getNotificationIcon(notification.type)} text-${getNotificationColor(notification.type)}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">${notification.title}</div>
                            <div class="text-muted small">${notification.message}</div>
                            <div class="text-muted small">${formatTimeAgo(notification.created_at)}</div>
                        </div>
                    </div>
                </a>
            </li>
        `).join('');
        
        notificationsList.innerHTML = html;
    }
    
    function displaySearchResults(results, query) {
        const html = results.map(result => `
            <div class="search-result-item p-3 border-bottom">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <i class="fas fa-${getResultIcon(result.type)} text-${getResultColor(result.type)} fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            <a href="${result.link}" class="text-decoration-none">${highlightQuery(result.title, query)}</a>
                        </h6>
                        <p class="text-muted small mb-1">${highlightQuery(result.description, query)}</p>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-${getResultColor(result.type)} me-2">${result.type}</span>
                            <small class="text-muted">${formatTimeAgo(result.created_at)}</small>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
        searchResults.innerHTML = html;
    }
    
    function getNotificationIcon(type) {
        const icons = {
            'project': 'project-diagram',
            'task': 'tasks',
            'team': 'users',
            'system': 'cog',
            'default': 'bell'
        };
        return icons[type] || icons.default;
    }
    
    function getNotificationColor(type) {
        const colors = {
            'project': 'primary',
            'task': 'success',
            'team': 'info',
            'system': 'secondary',
            'default': 'primary'
        };
        return colors[type] || colors.default;
    }
    
    function getResultIcon(type) {
        const icons = {
            'project': 'project-diagram',
            'task': 'tasks',
            'user': 'user',
            'team': 'users',
            'default': 'file'
        };
        return icons[type] || icons.default;
    }
    
    function getResultColor(type) {
        const colors = {
            'project': 'primary',
            'task': 'success',
            'user': 'info',
            'team': 'warning',
            'default': 'secondary'
        };
        return colors[type] || colors.default;
    }
    
    function highlightQuery(text, query) {
        if (!text) return '';
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }
    
    function formatTimeAgo(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diffInSeconds = Math.floor((now - time) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        return time.toLocaleDateString();
    }
    
    // Auto-focus search input when modal opens
    searchModal._element.addEventListener('shown.bs.modal', function() {
        searchInput.focus();
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
        
        // Escape to close search modal
        if (e.key === 'Escape' && searchModal._element.classList.contains('show')) {
            searchModal.hide();
        }
    });
});
</script>

<style>
.notification-dropdown {
    min-width: 300px;
    max-height: 400px;
    overflow-y: auto;
}

.search-result-item:hover {
    background-color: #f8f9fa;
}

.search-result-item a:hover {
    color: var(--primary-color) !important;
}

mark {
    background-color: #fff3cd;
    color: #856404;
    padding: 0.1em 0.2em;
    border-radius: 0.2em;
}

@media (max-width: 768px) {
    .notification-dropdown {
        min-width: 280px;
    }
    
    .navbar-nav .nav-link {
        padding: 0.5rem 0.75rem;
    }
}
</style>