<?php
// includes/header.php
// Global Header for Public Pages
require_once dirname(__DIR__) . '/config/config.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hi Teac Academy - Premier Academy Management System for DIT & CIT courses. Apply online, track admission status, and connect with expert instructors.">
    <meta name="author" content="Hi Teac Academy">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Hi Teac Academy</title>
    
    <!-- CSS CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Top Info Bar -->
<div class="bg-dark text-white py-2 px-3 d-none d-md-block no-print">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <small class="me-3"><i class="fa fa-envelope text-primary me-2"></i><?php echo get_setting('contact_email', 'shahzaibbangash24@gmail.com'); ?></small>
            <small><i class="fa fa-phone text-primary me-2"></i><?php echo get_setting('contact_phone', '03304347547'); ?></small>
        </div>
        <div>
            <a href="<?php echo get_setting('facebook_url', '#'); ?>" class="text-white me-3" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="<?php echo get_setting('twitter_url', '#'); ?>" class="text-white me-3" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="<?php echo get_setting('instagram_url', '#'); ?>" class="text-white me-3" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="<?php echo get_setting('youtube_url', '#'); ?>" class="text-white" target="_blank"><i class="fab fa-youtube"></i></a>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top no-print">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
            <i class="fa fa-graduation-cap text-primary me-2"></i>Hi Teac <span>Academy</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>about.php">About</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($current_page, ['courses.php', 'dit.php', 'cit.php']) ? 'active' : ''; ?>" href="#" id="coursesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Courses
                    </a>
                    <ul class="dropdown-menu border-0 shadow" aria-labelledby="coursesDropdown">
                        <li><a class="dropdown-menu-item dropdown-item" href="<?php echo BASE_URL; ?>courses.php">All Courses</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>dit.php">Diploma in IT (DIT)</a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>cit.php">Certificate in IT (CIT)</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>gallery.php">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'faq.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>faq.php">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>contact.php">Contact</a>
                </li>
                
                <?php if (is_student_logged_in()): ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-primary rounded-pill px-4 me-2" href="<?php echo BASE_URL; ?>student/dashboard.php">
                            <i class="fa fa-gauge-high me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a class="btn btn-danger rounded-pill px-4" href="<?php echo BASE_URL; ?>authentication/logout.php">
                            <i class="fa fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                <?php elseif (is_admin_logged_in()): ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-primary rounded-pill px-4 me-2" href="<?php echo BASE_URL; ?>admin/dashboard.php">
                            <i class="fa fa-gauge-high me-1"></i> Admin Panel
                        </a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a class="btn btn-danger rounded-pill px-4" href="<?php echo BASE_URL; ?>authentication/logout.php">
                            <i class="fa fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-outline-primary rounded-pill px-4 me-2 w-100 w-lg-auto mb-2 mb-lg-0" href="<?php echo BASE_URL; ?>authentication/login.php">
                            <i class="fa fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary rounded-pill px-4 text-white w-100 w-lg-auto" href="<?php echo BASE_URL; ?>authentication/register.php">
                            <i class="fa fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Container for Toast Notifications & Flash Messages -->
<div class="container mt-3 no-print">
    <?php display_flash_message(); ?>
</div>
