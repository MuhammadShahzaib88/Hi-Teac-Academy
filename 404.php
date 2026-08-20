<?php
// 404.php
// Custom 404 Page

$page_title = "404 - Page Not Found";
require_once 'config/config.php';
include 'includes/header.php';
?>

<div class="container py-5 text-center my-5" data-aos="zoom-in">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="display-1 fw-bold text-primary mb-3">404</div>
            <i class="fa fa-unlink text-muted fa-4x mb-4"></i>
            <h2 class="fw-bold text-dark">Page Not Found</h2>
            <p class="text-muted mb-4 lead">Oops! The URL you requested does not exist or has been moved. Use the options below to navigate back to safe areas.</p>
            <div class="d-grid d-sm-flex gap-3 justify-content-center">
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary rounded-pill px-4 py-2 text-white">
                    <i class="fa fa-home me-2"></i> Go to Homepage
                </a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                    <i class="fa fa-envelope me-2"></i> Contact Academy
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
