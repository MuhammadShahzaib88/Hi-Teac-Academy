<?php
// config/config.php
// Global Configuration & Security Functions

// Prevent direct access to config file
if (count(get_included_files()) == 1) exit("Direct access not permitted.");

// 1. Session Setup
if (session_status() == PHP_SESSION_NONE) {
    // Set secure session cookie parameters if HTTPS is available, but maintain compatibility for local HTTP
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_start([
        'cookie_lifetime' => 0,
        'cookie_path' => '/',
        'cookie_secure' => $secure,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// 2. Define System Paths
define('ROOT_PATH', dirname(__DIR__) . '/');

// Auto-detect BASE_URL for redirects and asset links (works under subfolders like /Hi teac academy/)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$projectDir = str_replace('\\', '/', dirname(dirname($scriptName)));
if ($projectDir !== '/') {
    $projectDir = rtrim($projectDir, '/') . '/';
}
define('BASE_URL', $protocol . '://' . $host . $projectDir);

// Include Database connection
require_once ROOT_PATH . 'config/db.php';

// 3. Security Helpers

// Generate CSRF Token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Render CSRF Hidden Input field
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// XSS Sanitization
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

// 4. Session Flash Messaging (Toast/Alert)
function set_flash_message($type, $message) {
    // types: success, danger, warning, info
    $_SESSION['flash_msg'] = [
        'type' => $type,
        'message' => $message
    ];
}

function display_flash_message() {
    if (isset($_SESSION['flash_msg'])) {
        $msg = $_SESSION['flash_msg'];
        unset($_SESSION['flash_msg']);
        
        $class = $msg['type'];
        if ($class === 'error') $class = 'danger'; // map error to bootstrap danger
        
        echo '<div class="alert alert-' . $class . ' alert-dismissible fade show" role="alert">
                ' . $msg['message'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

// 5. Authentication Helpers
function is_student_logged_in() {
    return isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true;
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_student_login() {
    if (!is_student_logged_in()) {
        set_flash_message('danger', 'Please log in as a student to access this page.');
        header('Location: ' . BASE_URL . 'authentication/login.php');
        exit;
    }
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        set_flash_message('danger', 'Unauthorized access! Please log in as Administrator.');
        header('Location: ' . BASE_URL . 'admin/login.php');
        exit;
    }
}

// 6. Dynamic Site Settings Fetcher
function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

// 7. Notification Creator helper
function create_notification($user_type, $user_id, $title, $message) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_type, user_id, title, message) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_type, $user_id, $title, $message]);
    } catch (PDOException $e) {
        return false;
    }
}

// 8. Custom General Helpers
function format_currency($amount) {
    return 'Rs. ' . number_format($amount, 2);
}

function format_date($date_str, $format = 'd M Y, h:i A') {
    if (empty($date_str)) return 'N/A';
    return date($format, strtotime($date_str));
}
?>
