<?php
// includes/admin_header.php
// Header and Sidebar for Administrator Dashboard
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Dashboard</title>
    
    <!-- CSS CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <nav class="sidebar no-print">
        <div class="sidebar-header text-center">
            <a href="<?php echo BASE_URL; ?>index.php" class="text-white text-decoration-none">
                <h4><i class="fa fa-graduation-cap text-primary me-2"></i>Hi Teac</h4>
            </a>
            <div class="mt-3">
                <img src="<?php echo BASE_URL; ?>assets/images/default-avatar.png" alt="Admin Avatar" class="rounded-circle border border-2 border-primary" style="width: 70px; height: 70px; object-fit: cover; onerror: this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png';">
                <h6 class="mt-2 mb-0 text-white"><?php echo sanitize($_SESSION['admin_username'] ?? 'Admin'); ?></h6>
                <small class="text-warning">System Administrator</small>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/dashboard.php">
                    <i class="fa fa-gauge-high"></i> Dashboard
                </a>
            </li>
            <li class="<?php echo ($current_page == 'students.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/students.php">
                    <i class="fa fa-user-graduate"></i> Manage Students
                </a>
            </li>
            <li class="<?php echo ($current_page == 'courses.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/courses.php">
                    <i class="fa fa-book"></i> Manage Courses
                </a>
            </li>
            <li class="<?php echo ($current_page == 'admissions.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/admissions.php">
                    <i class="fa fa-file-invoice"></i> Manage Admissions
                    <?php
                    // Count pending admissions
                    $adm_stmt = $pdo->query("SELECT COUNT(*) FROM admissions WHERE status = 'Pending'");
                    $pending_admissions = $adm_stmt->fetchColumn();
                    if ($pending_admissions > 0) {
                        echo '<span class="badge bg-warning text-dark ms-auto">' . $pending_admissions . '</span>';
                    }
                    ?>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'questions.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/questions.php">
                    <i class="fa fa-question-circle"></i> Manage Questions
                    <?php
                    // Count pending questions
                    $q_stmt = $pdo->query("SELECT COUNT(*) FROM questions WHERE status = 'Pending'");
                    $pending_questions = $q_stmt->fetchColumn();
                    if ($pending_questions > 0) {
                        echo '<span class="badge bg-danger ms-auto">' . $pending_questions . '</span>';
                    }
                    ?>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'teachers.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/teachers.php">
                    <i class="fa fa-user-tie"></i> Manage Teachers
                </a>
            </li>
            <li class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/gallery.php">
                    <i class="fa fa-image"></i> Manage Gallery
                </a>
            </li>
            <li class="<?php echo ($current_page == 'announcements.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/announcements.php">
                    <i class="fa fa-bullhorn"></i> Announcements
                </a>
            </li>
            <li class="<?php echo ($current_page == 'contacts.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/contacts.php">
                    <i class="fa fa-envelope"></i> Contact Messages
                    <?php
                    // Count pending contacts
                    $c_stmt = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'Pending'");
                    $pending_contacts = $c_stmt->fetchColumn();
                    if ($pending_contacts > 0) {
                        echo '<span class="badge bg-info ms-auto">' . $pending_contacts . '</span>';
                    }
                    ?>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/settings.php">
                    <i class="fa fa-cogs"></i> System Settings
                </a>
            </li>
            <li class="<?php echo ($current_page == 'backup.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin/backup.php">
                    <i class="fa fa-database"></i> Backup DB
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>authentication/logout.php" class="text-danger">
                    <i class="fa fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content Area -->
    <div class="dashboard-content">
        <!-- Top Bar inside Dashboard -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h4 class="mb-0 fw-bold"><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></h4>
            <div>
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary btn-sm me-2" target="_blank">
                    <i class="fa fa-globe me-1"></i> View Website
                </a>
                <span class="text-muted"><i class="fa fa-calendar me-1"></i> <?php echo date('d M Y'); ?></span>
            </div>
        </div>
        
        <!-- Alerts output area -->
        <?php display_flash_message(); ?>
