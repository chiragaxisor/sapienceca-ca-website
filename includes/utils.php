<?php
/**
 * Utility functions for common operations
 * Reduces code duplication and improves maintainability
 */

// Flash message system
class FlashMessage {
    public static function set($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message,
            'timestamp' => time()
        ];
    }
    
    public static function get() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }
    
    public static function has() {
        return isset($_SESSION['flash_message']);
    }
}

// Pagination helper class
class Pagination {
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    private $offset;
    
    public function __construct($totalItems, $itemsPerPage = 10, $currentPage = 1) {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = ceil($totalItems / $itemsPerPage);
        
        // Ensure current page is within valid range
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
        
        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    public function getOffset() {
        return $this->offset;
    }
    
    public function getLimit() {
        return $this->itemsPerPage;
    }
    
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    public function getTotalItems() {
        return $this->totalItems;
    }
    
    public function hasPages() {
        return $this->totalPages > 1;
    }
    
    public function generateLinks($baseUrl, $pageParam = 'page') {
        if (!$this->hasPages()) {
            return '';
        }
        
        $links = '';
        
        // Previous button
        if ($this->currentPage > 1) {
            $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . $pageParam . '=' . ($this->currentPage - 1) . '"><i class="fas fa-chevron-left"></i></a></li>';
        } else {
            $links .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>';
        }
        
        // Page numbers
        $startPage = max(1, $this->currentPage - 2);
        $endPage = min($this->totalPages, $this->currentPage + 2);
        
        if ($startPage > 1) {
            $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . $pageParam . '=1">1</a></li>';
            if ($startPage > 2) {
                $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $this->currentPage) {
                $links .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . $pageParam . '=' . $i . '">' . $i . '</a></li>';
            }
        }
        
        if ($endPage < $this->totalPages) {
            if ($endPage < $this->totalPages - 1) {
                $links .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . $pageParam . '=' . $this->totalPages . '">' . $this->totalPages . '</a></li>';
        }
        
        // Next button
        if ($this->currentPage < $this->totalPages) {
            $links .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?' . $pageParam . '=' . ($this->currentPage + 1) . '"><i class="fas fa-chevron-right"></i></a></li>';
        } else {
            $links .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>';
        }
        
        return $links;
    }
    
    public function getInfo() {
        $start = $this->offset + 1;
        $end = min($this->offset + $this->itemsPerPage, $this->totalItems);
        
        return [
            'start' => $start,
            'end' => $end,
            'total' => $this->totalItems,
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages
        ];
    }
}

// Database query builder for common operations
class QueryBuilder {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getPaginatedResults($table, $pagination, $columns = '*', $where = '', $params = [], $orderBy = '') {
        $sql = "SELECT {$columns} FROM {$table}";
        
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        
        $stmt = $this->pdo->prepare($sql);
        
        // Bind parameters
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
        
        $stmt->bindValue($paramIndex++, $pagination->getLimit(), PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, $pagination->getOffset(), PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getCount($table, $where = '', $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$table}";
        
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result ? (int)$result['total'] : 0;
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $stmt = $this->pdo->prepare($sql);
        
        $params = array_values($data);
        foreach ($whereParams as $param) {
            $params[] = $param;
        }
        
        return $stmt->execute($params);
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute($params);
    }
}

// Form validation helper
class FormValidator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field] = $message ?: ucfirst($field) . ' is required.';
        }
        return $this;
    }
    
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?: 'Please enter a valid email address.';
            }
        }
        return $this;
    }
    
    public function url($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                $this->errors[$field] = $message ?: 'Please enter a valid URL.';
            }
        }
        return $this;
    }
    
    public function minLength($field, $min, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $message ?: ucfirst($field) . " must be at least {$min} characters.";
        }
        return $this;
    }
    
    public function maxLength($field, $max, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $message ?: ucfirst($field) . " must not exceed {$max} characters.";
        }
        return $this;
    }
    
    public function pattern($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->errors[$field] = $message ?: ucfirst($field) . ' format is invalid.';
        }
        return $this;
    }
    
    public function custom($field, $callback, $message = null) {
        if (isset($this->data[$field])) {
            if (!$callback($this->data[$field])) {
                $this->errors[$field] = $message ?: ucfirst($field) . ' validation failed.';
            }
        }
        return $this;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getFirstError() {
        return reset($this->errors);
    }
}

// File upload handler
class FileUploader {
    private $uploadDir;
    private $maxSize;
    private $allowedTypes;
    
    public function __construct($uploadDir, $maxSize = 5242880, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']) {
        $this->uploadDir = rtrim($uploadDir, '/');
        $this->maxSize = $maxSize;
        $this->allowedTypes = $allowedTypes;
    }
    
    public function upload($file, $customName = null) {
        $errors = $this->validate($file);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $filename = $customName ?: $this->generateSecureFilename($file['name']);
        $uploadPath = $this->uploadDir . '/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => false, 'errors' => ['Failed to move uploaded file.']];
        }
        
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $uploadPath,
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }
    
    private function validate($file) {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
            ];
            
            $errors[] = $uploadErrors[$file['error']] ?? 'Upload failed with error code: ' . $file['error'];
            return $errors;
        }
        
        if ($file['size'] > $this->maxSize) {
            $errors[] = 'File size exceeds maximum allowed size (' . ($this->maxSize / 1024 / 1024) . 'MB)';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $this->allowedTypes);
        }
        
        if (!is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'File upload security check failed.';
        }
        
        return $errors;
    }
    
    private function generateSecureFilename($originalName) {
        $timestamp = time();
        $random = bin2hex(random_bytes(16));
        $hash = hash('sha256', $originalName . $timestamp . $random);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        return $timestamp . '_' . substr($hash, 0, 16) . '.' . $extension;
    }
}

// Response helper for consistent API responses
class Response {
    public static function json($data, $status = 200, $message = '') {
        http_response_code($status);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => $status < 400,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ]);
        exit;
    }
    
    public static function success($data = null, $message = 'Success') {
        self::json($data, 200, $message);
    }
    
    public static function error($message = 'Error', $status = 400, $data = null) {
        self::json($data, $status, $message);
    }
    
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }
    
    public static function forbidden($message = 'Forbidden') {
        self::error($message, 403);
    }
}

// Security helper functions
class Security {
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public static function sanitizeInput($input, $type = 'string') {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::sanitizeInput($item, $type);
            }, $input);
        }
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    public static function validateCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}


?>
