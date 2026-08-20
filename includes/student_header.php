<?php
// includes/student_header.php
// Header and Sidebar for Student Dashboard
require_once dirname(__DIR__) . '/config/config.php';
require_student_login();

// Fetch student profile details from DB for display
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT first_name, last_name, email, profile_pic FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student_user = $stmt->fetch();

$student_full_name = $student_user['first_name'] . ' ' . $student_user['last_name'];
$student_avatar = !empty($student_user['profile_pic']) && file_exists(ROOT_PATH . $student_user['profile_pic']) 
    ? BASE_URL . $student_user['profile_pic'] 
    : BASE_URL . 'assets/images/default-avatar.png';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Student Dashboard</title>
    
    <!-- CSS CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <nav class="sidebar no-print">
        <div class="sidebar-header text-center">
            <a href="<?php echo BASE_URL; ?>index.php" class="text-white text-decoration-none">
                <h4><i class="fa fa-graduation-cap text-primary me-2"></i>Hi Teac</h4>
            </a>
            <div class="mt-3">
                <img src="<?php echo $student_avatar; ?>" alt="Avatar" class="rounded-circle border border-2 border-primary" style="width: 70px; height: 70px; object-fit: cover;">
                <h6 class="mt-2 mb-0 text-white"><?php echo sanitize($student_full_name); ?></h6>
                <small class="text-muted">Student Account</small>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>student/dashboard.php">
                    <i class="fa fa-gauge-high"></i> Dashboard
                </a>
            </li>
            <li class="<?php echo ($current_page == 'profile.php' || $current_page == 'edit_profile.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>student/profile.php">
                    <i class="fa fa-user"></i> My Profile
                </a>
            </li>
            <li class="<?php echo ($current_page == 'admission.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>student/admission.php">
                    <i class="fa fa-file-invoice"></i> Apply Admission
                </a>
            </li>
            <li class="<?php echo ($current_page == 'admission_status.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>student/admission_status.php">
                    <i class="fa fa-clock-rotate-left"></i> Track Status
                </a>
            </li>
            <li class="<?php echo ($current_page == 'ask_question.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>student/ask_question.php">
                    <i class="fa fa-question-circle"></i> Ask Questions
                </a>
            </li>
            <li class="<?php echo ($current_page == 'notifications.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>student/notifications.php">
                    <i class="fa fa-bell"></i> Notifications
                    <?php
                    // Count unread notifications
                    $notif_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_type = 'Student' AND user_id = ? AND is_read = 0");
                    $notif_stmt->execute([$student_id]);
                    $unread_count = $notif_stmt->fetchColumn();
                    if ($unread_count > 0) {
                        echo '<span class="badge bg-danger ms-auto">' . $unread_count . '</span>';
                    }
                    ?>
                </a>
            </li>
            <li class="<?php echo ($current_page == 'change_password.php') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>student/change_password.php">
                    <i class="fa fa-key"></i> Security
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
            <h4 class="mb-0 fw-bold"><?php echo isset($page_title) ? $page_title : 'Student Panel'; ?></h4>
            <div>
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fa fa-globe me-1"></i> Public Site
                </a>
                <span class="text-muted"><i class="fa fa-calendar me-1"></i> <?php echo date('d M Y'); ?></span>
            </div>
        </div>
        
        <!-- Alerts output area -->
        <?php display_flash_message(); ?>
