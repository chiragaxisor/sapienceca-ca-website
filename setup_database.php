<?php
/**
 * Database Setup Script for SapienceCA
 * Run this script to set up the database structure
 */

require_once 'config.php';

echo "<h2>SapienceCA Database Setup</h2>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Create team_members table if it doesn't exist
    $createTeamTable = "
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
    )";
    
    $pdo->exec($createTeamTable);
    echo "<p style='color: green;'>✓ Team members table created/verified</p>";
    
    // Create users table if it doesn't exist
    $createUsersTable = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($createUsersTable);
    echo "<p style='color: green;'>✓ Users table created/verified</p>";
    
    // Create activity_logs table if it doesn't exist
    $createActivityLogsTable = "
    CREATE TABLE IF NOT EXISTS activity_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    
    $pdo->exec($createActivityLogsTable);
    echo "<p style='color: green;'>✓ Activity logs table created/verified</p>";
    
    // Create indexes for better performance
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_team_members_position ON team_members(position)",
        "CREATE INDEX IF NOT EXISTS idx_team_members_name ON team_members(name)",
        "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)",
        "CREATE INDEX IF NOT EXISTS idx_activity_logs_user_id ON activity_logs(user_id)",
        "CREATE INDEX IF NOT EXISTS idx_activity_logs_action ON activity_logs(action)"
    ];
    
    foreach ($indexes as $index) {
        try {
            $pdo->exec($index);
        } catch (Exception $e) {
            // Index might already exist, continue
        }
    }
    echo "<p style='color: green;'>✓ Database indexes created/verified</p>";
    
    // Check if admin user exists, if not create one
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin User', 'admin@sapienceca.com', $adminPassword, 'admin']);
        echo "<p style='color: green;'>✓ Default admin user created (email: admin@sapienceca.com, password: admin123)</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Admin user already exists</p>";
    }
    
    // Check if team_members table has data, if not insert sample data
    $stmt = $pdo->query("SELECT COUNT(*) FROM team_members");
    $memberCount = $stmt->fetchColumn();
    
    if ($memberCount == 0) {
        $sampleMembers = [
            ['John Doe', 'CEO & Founder', 'Chief Executive Officer', 'Visionary leader with 15+ years of experience in technology and business development.', 'john.doe@sapienceca.com', '+1 (555) 123-4567', 'https://linkedin.com/in/johndoe', 'Visionary leader with 15+ years of experience in technology and business development.'],
            ['Jane Smith', 'CTO', 'Chief Technology Officer', 'Technology expert specializing in scalable architecture and innovative solutions.', 'jane.smith@sapienceca.com', '+1 (555) 234-5678', 'https://linkedin.com/in/janesmith', 'Technology expert specializing in scalable architecture and innovative solutions.'],
            ['Mike Johnson', 'Lead Developer', 'Senior Full-Stack Developer', 'Passionate full-stack developer with expertise in modern web technologies.', 'mike.johnson@sapienceca.com', '+1 (555) 345-6789', 'https://linkedin.com/in/mikejohnson', 'Full-stack developer passionate about clean code and user experience.']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO team_members (name, position, title, description, email, phone, linkedin_profile, bio, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        foreach ($sampleMembers as $member) {
            $stmt->execute($member);
        }
        
        echo "<p style='color: green;'>✓ Sample team members added</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Team members table already has data</p>";
    }
    
    // Create uploads directory if it doesn't exist
    $uploadsDir = 'uploads/team';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
        echo "<p style='color: green;'>✓ Uploads directory created</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Uploads directory already exists</p>";
    }
    
    echo "<h3 style='color: green;'>Database setup completed successfully!</h3>";
    echo "<p><a href='index.php'>Go to Login</a> | <a href='team.php'>View Team Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config.php</p>";
}
?>
