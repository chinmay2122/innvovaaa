<?php
// ========== SECURITY CONFIGURATION ==========

// Production error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Create logs directory if it doesn't exist
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
ini_set('error_log', $log_dir . '/error.log');

// Session security configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

// Secure session for HTTPS (enable in production)
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

// ========== SECURITY HEADERS ==========
// Prevent clickjacking attacks
header('X-Frame-Options: DENY');

// Prevent MIME type sniffing
header('X-Content-Type-Options: nosniff');

// Enable browser XSS protection
header('X-XSS-Protection: 1; mode=block');

// Referrer Policy
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy - Tailwind + fonts from CDN
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; object-src 'none'; base-uri 'self'; form-action 'self';");

// ========== DATABASE CONFIGURATION ==========
// Support environment variables for production deployment
define('DB_HOST', getenv('mysql.railway.internal'));
define('DB_USER', getenv('root'));
define('DB_PASS', getenv('McWQWfspnkEckLGtoqvTbsMqOwrWokVW'));
define('DB_NAME', getenv('railway'));
define('DB_PORT', getenv('3306');

// Create connection with security options
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Connection failed. Please try again later.");
    }

    $conn->set_charset("utf8mb4");

    return $conn;
}

// ========== SESSION MANAGEMENT ==========

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Check session timeout
if (isLoggedIn()) {
    if (isset($_SESSION['login_time']) && time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: admin_login.php?timeout=1');
        exit();
    }
    
    // Validate session IP address (prevent session hijacking)
    if (isset($_SESSION['login_ip']) && $_SESSION['login_ip'] !== $_SERVER['REMOTE_ADDR']) {
        session_destroy();
        header('Location: admin_login.php?ip_mismatch=1');
        exit();
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: admin_login.php');
        exit();
    }
}

// ========== INPUT VALIDATION & SANITIZATION ==========

// Sanitize input (trim + escape)
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ========== CSRF PROTECTION ==========

// Generate CSRF token
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token (call at start of form processing)
function verifyCsrfToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token'])) {
            http_response_code(403);
            die('CSRF token missing');
        }
        
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
        
        // Regenerate token after validation
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// ========== RATE LIMITING ==========

// Check login attempt rate limiting (5 attempts per 15 minutes)
function checkLoginAttempts() {
    $client_ip = $_SERVER['REMOTE_ADDR'];
    $attempt_key = 'login_attempts_' . md5($client_ip);
    $lockout_key = 'login_lockout_' . md5($client_ip);
    
    if (!isset($_SESSION[$lockout_key])) {
        $_SESSION[$lockout_key] = 0;
    }
    
    if (!isset($_SESSION[$attempt_key])) {
        $_SESSION[$attempt_key] = ['count' => 0, 'time' => time()];
    }
    
    $attempt_data = $_SESSION[$attempt_key];
    
    // Reset attempts after 15 minutes
    if (time() - $attempt_data['time'] > 900) {
        $_SESSION[$attempt_key] = ['count' => 0, 'time' => time()];
        $_SESSION[$lockout_key] = 0;
    }
    
    return $_SESSION[$attempt_key]['count'];
}

// Record failed login attempt
function recordLoginAttempt() {
    $client_ip = $_SERVER['REMOTE_ADDR'];
    $attempt_key = 'login_attempts_' . md5($client_ip);
    
    if (!isset($_SESSION[$attempt_key])) {
        $_SESSION[$attempt_key] = ['count' => 0, 'time' => time()];
    }
    
    $_SESSION[$attempt_key]['count']++;
    
    if ($_SESSION[$attempt_key]['count'] >= 5) {
        $_SESSION['login_lockout_' . md5($client_ip)] = time() + 900; // 15 minute lockout
    }
}

// Check if login is locked
function isLoginLocked() {
    $client_ip = $_SERVER['REMOTE_ADDR'];
    $lockout_key = 'login_lockout_' . md5($client_ip);
    
    return isset($_SESSION[$lockout_key]) && $_SESSION[$lockout_key] > time();
}

// File upload handler with security validation
function handleFileUpload($file, $uploadDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'pdf']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // ========== SECURITY VALIDATION ==========
    
    // Check file extension whitelist
    if (!in_array($fileExt, $allowedTypes, true)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Check file size (5MB limit)
    if ($fileSize > 5242880) { // 5MB in bytes
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    // ========== MIME TYPE VALIDATION ==========
    // Validate MIME type using finfo_file
    if (!function_exists('finfo_file')) {
        return ['success' => false, 'message' => 'Server does not support MIME type checking'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);
    
    // Map of allowed MIME types to extensions
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf'
    ];
    
    // Verify MIME type is allowed
    if (!isset($allowedMimes[$mimeType])) {
        return ['success' => false, 'message' => 'Invalid file MIME type: ' . $mimeType];
    }
    
    // Verify extension matches MIME type
    $expectedExt = $allowedMimes[$mimeType];
    if ($fileExt !== $expectedExt) {
        return ['success' => false, 'message' => 'File extension does not match MIME type'];
    }
    
    // ========== GENERATE SAFE FILENAME ==========
    // Use random filename to prevent directory traversal and conflicts
    $newFileName = bin2hex(random_bytes(16)) . '.' . $fileExt;
    $uploadPath = $uploadDir . $newFileName;
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory'];
        }
    }
    
    // ========== MOVE FILE ==========
    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
    
    // Set secure file permissions
    chmod($uploadPath, 0644);
    
    return ['success' => true, 'fileName' => $newFileName, 'path' => $uploadPath];
}
?>
