-- Optimized Database Schema for SapienceCA
-- This schema includes new tables and improved structure for better performance and security

-- Drop existing tables if they exist (for fresh installation)
DROP TABLE IF EXISTS user_activities;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS project_members;
DROP TABLE IF EXISTS team_members;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

-- Users table with improved security
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'member') DEFAULT 'member',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    avatar VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    email_verified_at TIMESTAMP NULL,
    email_verification_token VARCHAR(100) DEFAULT NULL,
    password_reset_token VARCHAR(100) DEFAULT NULL,
    password_reset_expires_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    login_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at)
);

-- Login attempts tracking for security
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_ip_address (ip_address),
    INDEX idx_created_at (created_at),
    INDEX idx_ip_created (ip_address, created_at)
);

-- User activities for audit trail
CREATE TABLE user_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Projects table with improved structure
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled', 'deleted') DEFAULT 'planning',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    start_date DATE DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    completion_percentage DECIMAL(5,2) DEFAULT 0.00,
    budget DECIMAL(10,2) DEFAULT 0.00,
    actual_cost DECIMAL(10,2) DEFAULT 0.00,
    tags JSON DEFAULT NULL,
    settings JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_due_date (due_date),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_search (title, description)
);

-- Project members for team collaboration
CREATE TABLE project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'manager', 'member', 'viewer') DEFAULT 'member',
    permissions JSON DEFAULT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_user (project_id, user_id),
    INDEX idx_project_id (project_id),
    INDEX idx_user_id (user_id),
    INDEX idx_role (role)
);

-- Tasks table for project management
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    assigned_to INT DEFAULT NULL,
    created_by INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    status ENUM('pending', 'in_progress', 'review', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    estimated_hours DECIMAL(5,2) DEFAULT NULL,
    actual_hours DECIMAL(5,2) DEFAULT NULL,
    start_date DATE DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    completed_at TIMESTAMP NULL,
    tags JSON DEFAULT NULL,
    attachments JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_due_date (due_date),
    FULLTEXT idx_search (title, description)
);

-- Team members for organization management
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    invited_by INT DEFAULT NULL,
    role ENUM('owner', 'admin', 'manager', 'member') DEFAULT 'member',
    permissions JSON DEFAULT NULL,
    status ENUM('pending', 'active', 'inactive', 'removed') DEFAULT 'pending',
    invitation_token VARCHAR(100) DEFAULT NULL,
    invitation_expires_at TIMESTAMP NULL,
    joined_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_role (role),
    INDEX idx_invitation_token (invitation_token)
);

-- Services table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_title (title),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_search (title, description)
);

-- Financial transactions
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT DEFAULT NULL,
    type ENUM('income', 'expense', 'refund') NOT NULL,
    category VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('pending', 'completed', 'cancelled', 'failed') DEFAULT 'pending',
    reference_id VARCHAR(100) DEFAULT NULL,
    metadata JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_project_id (project_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role, status, email_verified_at) VALUES 
('Admin User', 'admin@sapienceca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW());

-- Insert sample data for testing
INSERT INTO projects (user_id, title, description, status, priority, start_date, due_date, completion_percentage, budget) VALUES
(1, 'Website Redesign', 'Complete redesign of company website with modern UI/UX', 'active', 'high', '2024-01-01', '2024-03-31', 65.00, 15000.00),
(1, 'Mobile App Development', 'iOS and Android app for customer management', 'planning', 'medium', '2024-02-01', '2024-06-30', 0.00, 25000.00),
(1, 'Database Optimization', 'Improve database performance and structure', 'completed', 'low', '2023-12-01', '2024-01-15', 100.00, 5000.00);

INSERT INTO tasks (project_id, assigned_to, created_by, title, description, status, priority, due_date) VALUES
(1, 1, 1, 'Design Homepage', 'Create modern homepage design with responsive layout', 'in_progress', 'high', '2024-02-15'),
(1, 1, 1, 'Implement Navigation', 'Build responsive navigation menu', 'pending', 'medium', '2024-02-20'),
(2, 1, 1, 'App Wireframes', 'Create wireframes for mobile app screens', 'pending', 'medium', '2024-02-28');

INSERT INTO transactions (user_id, project_id, type, category, description, amount, status) VALUES
(1, 1, 'income', 'Web Development', 'Payment for website redesign project', 5000.00, 'completed'),
(1, 1, 'expense', 'Design Tools', 'Purchase of design software licenses', 299.00, 'completed'),
(1, 2, 'income', 'Mobile Development', 'Advance payment for mobile app', 10000.00, 'completed');

-- Insert sample services
INSERT INTO services (user_id, title, description, status) VALUES
(1, 'Web Development', 'Custom website development with modern technologies including responsive design, SEO optimization, and content management systems.', 'active'),
(1, 'Mobile App Development', 'Native and cross-platform mobile application development for iOS and Android platforms.', 'active'),
(1, 'UI/UX Design', 'User interface and user experience design services focusing on intuitive and beautiful user interfaces.', 'active'),
(1, 'Database Design', 'Database architecture, optimization, and management services for improved performance and scalability.', 'active'),
(1, 'Cloud Solutions', 'Cloud infrastructure setup, migration, and management services for scalable and reliable applications.', 'active');

-- Create indexes for better performance
CREATE INDEX idx_projects_user_status ON projects(user_id, status);
CREATE INDEX idx_tasks_project_status ON tasks(project_id, status);
CREATE INDEX idx_tasks_assigned_status ON tasks(assigned_to, status);
CREATE INDEX idx_transactions_user_type ON transactions(user_id, type);
CREATE INDEX idx_activities_user_action ON user_activities(user_id, action);
CREATE INDEX idx_services_user_status ON services(user_id, status);

-- Create views for common queries
CREATE VIEW project_overview AS
SELECT 
    p.id,
    p.title,
    p.status,
    p.priority,
    p.completion_percentage,
    p.due_date,
    COUNT(t.id) as total_tasks,
    COUNT(CASE WHEN t.status = 'completed' THEN 1 END) as completed_tasks,
    p.budget,
    p.actual_cost
FROM projects p
LEFT JOIN tasks t ON p.id = t.project_id
WHERE p.status != 'deleted'
GROUP BY p.id;

CREATE VIEW user_dashboard_stats AS
SELECT 
    u.id,
    u.name,
    COUNT(DISTINCT p.id) as total_projects,
    COUNT(DISTINCT CASE WHEN p.status = 'active' THEN p.id END) as active_projects,
    COUNT(DISTINCT CASE WHEN p.status = 'completed' THEN p.id END) as completed_projects,
    COUNT(DISTINCT t.id) as total_tasks,
    COUNT(DISTINCT CASE WHEN t.status = 'completed' THEN t.id END) as completed_tasks,
    COALESCE(SUM(CASE WHEN tr.type = 'income' THEN tr.amount ELSE 0 END), 0) as total_income,
    COALESCE(SUM(CASE WHEN tr.type = 'expense' THEN tr.amount ELSE 0 END), 0) as total_expenses
FROM users u
LEFT JOIN projects p ON u.id = p.user_id AND p.status != 'deleted'
LEFT JOIN tasks t ON u.id = t.assigned_to
LEFT JOIN transactions tr ON u.id = tr.user_id
WHERE u.status = 'active'
GROUP BY u.id;

-- Create stored procedures for common operations
DELIMITER //

CREATE PROCEDURE GetUserProjects(IN userId INT, IN projectStatus VARCHAR(20))
BEGIN
    IF projectStatus IS NULL OR projectStatus = '' THEN
        SELECT * FROM projects WHERE user_id = userId AND status != 'deleted' ORDER BY created_at DESC;
    ELSE
        SELECT * FROM projects WHERE user_id = userId AND status = projectStatus ORDER BY created_at DESC;
    END IF;
END //

CREATE PROCEDURE GetProjectTasks(IN projectId INT, IN taskStatus VARCHAR(20))
BEGIN
    IF taskStatus IS NULL OR taskStatus = '' THEN
        SELECT t.*, u.name as assigned_to_name 
        FROM tasks t 
        LEFT JOIN users u ON t.assigned_to = u.id 
        WHERE t.project_id = projectId 
        ORDER BY t.due_date ASC, t.priority DESC;
    ELSE
        SELECT t.*, u.name as assigned_to_name 
        FROM tasks t 
        LEFT JOIN users u ON t.assigned_to = u.id 
        WHERE t.project_id = projectId AND t.status = taskStatus 
        ORDER BY t.due_date ASC, t.priority DESC;
    END IF;
END //

CREATE PROCEDURE UpdateProjectProgress(IN projectId INT)
BEGIN
    DECLARE totalTasks INT;
    DECLARE completedTasks INT;
    
    SELECT COUNT(*) INTO totalTasks FROM tasks WHERE project_id = projectId;
    SELECT COUNT(*) INTO completedTasks FROM tasks WHERE project_id = projectId AND status = 'completed';
    
    IF totalTasks > 0 THEN
        UPDATE projects 
        SET completion_percentage = (completedTasks / totalTasks) * 100 
        WHERE id = projectId;
    END IF;
END //

DELIMITER ;

-- Grant permissions (adjust as needed for your setup)
-- GRANT ALL PRIVILEGES ON sapienceca.* TO 'your_username'@'localhost';
-- FLUSH PRIVILEGES;
