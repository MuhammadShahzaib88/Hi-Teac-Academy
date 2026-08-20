<?php
// authentication/logout.php
// Global Session Destruction and Logout Handler

require_once dirname(__DIR__) . '/config/config.php';

// Unset all session variables
$_SESSION = [];

// Destroy session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Restart temporary session to set logout flash message
session_start();
set_flash_message('info', 'You have been logged out successfully.');

header('Location: ' . BASE_URL . 'index.php');
exit;
?>
