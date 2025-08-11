-- SapienceCA Database Setup
-- Create database
CREATE DATABASE IF NOT EXISTS sapienceca;
USE sapienceca;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Team members table
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    title VARCHAR(200),
    description LONGTEXT,
    email VARCHAR(100),
    phone VARCHAR(20),
    linkedin_profile VARCHAR(255),
    bio TEXT,
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('planning', 'in_progress', 'completed', 'on_hold') DEFAULT 'planning',
    start_date DATE,
    end_date DATE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    project_id INT,
    assigned_to INT,
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@sapienceca.com', '$2y$10$PqmAgjOVZNkgV2Uhf14Mx.CpR2nLo3kEeaN8lDO/pdIjfsOelf2Qi', 'admin');

-- Insert sample team members
INSERT INTO team_members (name, position, title, description, email, phone, linkedin_profile, bio) VALUES 
('John Doe', 'CEO & Founder', 'Chief Executive Officer', '<p>John is a visionary leader with over 15 years of experience in technology and business development. He has successfully led multiple startups from concept to market success, specializing in digital transformation and innovative business solutions.</p><p>His expertise includes:</p><ul><li>Strategic planning and execution</li><li>Team building and leadership</li><li>Business development and partnerships</li><li>Technology innovation and implementation</li></ul>', 'john.doe@sapienceca.com', '+1 (555) 123-4567', 'https://linkedin.com/in/johndoe', 'Visionary leader with 15+ years of experience in technology and business development.'),
('Jane Smith', 'CTO', 'Chief Technology Officer', '<p>Jane is a technology expert specializing in scalable architecture and innovative solutions. With a strong background in software engineering and system design, she has architected solutions that serve millions of users worldwide.</p><p>Key achievements:</p><ul><li>Led development of cloud-native applications</li><li>Implemented microservices architecture</li><li>Reduced system downtime by 99.9%</li><li>Mentored 50+ developers</li></ul>', 'jane.smith@sapienceca.com', '+1 (555) 234-5678', 'https://linkedin.com/in/janesmith', 'Technology expert specializing in scalable architecture and innovative solutions.'),
('Mike Johnson', 'Lead Developer', 'Senior Full-Stack Developer', '<p>Mike is a passionate full-stack developer with expertise in modern web technologies. He specializes in creating clean, maintainable code and exceptional user experiences.</p><p>Technical skills:</p><ul><li>Frontend: React, Vue.js, Angular</li><li>Backend: Node.js, PHP, Python</li><li>Database: MySQL, PostgreSQL, MongoDB</li><li>DevOps: Docker, AWS, CI/CD</li></ul>', 'mike.johnson@sapienceca.com', '+1 (555) 345-6789', 'https://linkedin.com/in/mikejohnson', 'Full-stack developer passionate about clean code and user experience.'),
('Sarah Wilson', 'UX Designer', 'Senior User Experience Designer', '<p>Sarah is a creative designer focused on creating intuitive and beautiful user interfaces. Her designs prioritize user needs while maintaining aesthetic appeal and brand consistency.</p><p>Design philosophy:</p><ul><li>User-centered design approach</li><li>Accessibility and inclusivity</li><li>Data-driven design decisions</li><li>Continuous iteration and improvement</li></ul>', 'sarah.wilson@sapienceca.com', '+1 (555) 456-7890', 'https://linkedin.com/in/sarahwilson', 'Creative designer focused on intuitive and beautiful user interfaces.'),
('David Brown', 'Project Manager', 'Senior Project Manager', '<p>David is an experienced project manager who ensures timely delivery and quality standards. He has successfully managed projects ranging from small team initiatives to large enterprise solutions.</p><p>Project management expertise:</p><ul><li>Agile and Scrum methodologies</li><li>Risk management and mitigation</li><li>Stakeholder communication</li><li>Resource allocation and optimization</li></ul>', 'david.brown@sapienceca.com', '+1 (555) 567-8901', 'https://linkedin.com/in/davidbrown', 'Experienced project manager ensuring timely delivery and quality standards.');

-- Insert sample projects
INSERT INTO projects (name, description, status, start_date, end_date, created_by) VALUES 
('E-commerce Platform', 'Modern e-commerce solution with advanced features', 'completed', '2024-01-01', '2024-03-15', 1),
('Mobile App Development', 'Cross-platform mobile application for iOS and Android', 'in_progress', '2024-02-01', '2024-05-30', 1),
('AI Analytics Dashboard', 'Intelligent analytics platform with machine learning', 'planning', '2024-04-01', '2024-07-30', 1);

-- Insert sample tasks
INSERT INTO tasks (title, description, status, priority, project_id, assigned_to, due_date) VALUES 
('Design User Interface', 'Create wireframes and mockups for the main dashboard', 'completed', 'high', 1, 4, '2024-01-15'),
('Database Schema Design', 'Design and implement the database structure', 'completed', 'high', 1, 3, '2024-01-20'),
('Frontend Development', 'Implement the user interface using React', 'in_progress', 'medium', 2, 3, '2024-04-15'),
('Backend API Development', 'Develop RESTful APIs for the mobile app', 'pending', 'high', 2, 3, '2024-04-30'),
('Requirements Analysis', 'Gather and analyze requirements for AI dashboard', 'pending', 'medium', 3, 5, '2024-04-15');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_team_members_position ON team_members(position);
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_tasks_project_id ON tasks(project_id);
CREATE INDEX idx_tasks_assigned_to ON tasks(assigned_to);
