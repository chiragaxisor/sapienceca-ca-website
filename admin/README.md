# SapienceCA - Team Management System

A comprehensive team management system built with PHP, MySQL, and modern web technologies.

## Features

### Team Management
- **CRUD Operations**: Create, Read, Update, and Delete team members
- **Rich Profile Information**: Name, position, title, description, contact details, LinkedIn profile, bio
- **Image Upload**: Profile photo upload with validation and secure file handling
- **Search & Filter**: Real-time search and position-based filtering
- **Data Export**: Export filtered team data to CSV format
- **Responsive Design**: Mobile-friendly interface with Bootstrap 5

### Security Features
- **Session Management**: Secure user authentication and authorization
- **Input Validation**: Comprehensive form validation and sanitization
- **CSRF Protection**: Cross-Site Request Forgery protection
- **Secure File Uploads**: Image validation and secure filename generation
- **SQL Injection Prevention**: Prepared statements and parameterized queries

### Database Features
- **Optimized Queries**: Efficient database queries with proper indexing
- **Activity Logging**: Track all team member operations
- **Data Integrity**: Foreign key constraints and data validation
- **Performance**: Database indexes for better query performance

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Steps

1. **Clone or download the project files**

2. **Configure Database**
   - Edit `config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'sapienceca');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Run Database Setup**
   - Navigate to `setup_database.php` in your browser
   - This will create all necessary tables and sample data
   - Default admin user: `admin@sapienceca.com` / `admin123`

4. **Set Permissions**
   - Ensure the `uploads/team/` directory is writable by your web server

5. **Access the Application**
   - Login at `index.php`
   - Navigate to the team page at `team.php`

## Database Structure

### Team Members Table
```sql
CREATE TABLE team_members (
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
```

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Activity Logs Table
```sql
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

## API Endpoints

The system includes a RESTful API for team operations:

- `GET /team_api.php?action=list` - Get all team members
- `GET /team_api.php?action=member&id={id}` - Get specific team member
- `GET /team_api.php?action=stats` - Get team statistics
- `POST /team_api.php?action=add` - Add new team member
- `PUT /team_api.php?action=update` - Update team member
- `DELETE /team_api.php?action=delete` - Delete team member

## Usage

### Adding Team Members
1. Click "Add Member" button
2. Fill in required fields (Name and Position are mandatory)
3. Upload profile photo (optional)
4. Submit the form

### Editing Team Members
1. Click "Edit" button on any team member card
2. Modify the information as needed
3. Upload new photo if desired
4. Save changes

### Searching and Filtering
- Use the search box to find members by name, position, or description
- Use the position filter to show only members in specific roles
- Search and filter work together for precise results

### Exporting Data
1. Apply any desired filters
2. Click "Export" button
3. Download CSV file with filtered team data

## Security Considerations

- All user inputs are validated and sanitized
- File uploads are restricted to image types only
- Maximum file size is limited to 5MB
- Secure session management with timeout
- Activity logging for audit trails

## Performance Features

- Database indexes on frequently queried columns
- Efficient SQL queries with proper JOINs
- Client-side filtering for better user experience
- Optimized image handling and storage

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Image Upload Failed**
   - Check directory permissions for `uploads/team/`
   - Verify file size is under 5MB
   - Ensure file is a valid image format

3. **Session Issues**
   - Check PHP session configuration
   - Verify session storage directory permissions

### Error Logs
- Check your web server error logs
- Enable PHP error reporting in development
- Review database error logs

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For support and questions, please create an issue in the repository or contact the development team.
