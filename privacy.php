<?php
// privacy.php
// Privacy Policy Page

$page_title = "Privacy Policy";
require_once 'config/config.php';
include 'includes/header.php';
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">Privacy Policy</h1>
        <p class="lead mb-0">How we protect your personal and educational records</p>
    </div>
</section>

<!-- Policy Content -->
<section class="py-5">
    <div class="container" style="max-width: 800px;" data-aos="fade-up">
        <h2 class="fw-bold mb-3">1. Information Collection</h2>
        <p class="text-muted">Hi Teac Academy collects student registration profiles (including names, email addresses, phone contacts) and verification documents (including Matric certificates, parent CNIC, photographs) exclusively to validate eligibility for enrollment in Board of Technical Education courses.</p>
        
        <h2 class="fw-bold mt-4 mb-3">2. Document Storage & Security</h2>
        <p class="text-muted">Scanned document copies are uploaded into a secure directory on our local server. Access is strictly restricted to certified administrative officers. Document scans are not shared, distributed, or sold under any conditions.</p>
        
        <h2 class="fw-bold mt-4 mb-3">3. Database Backups</h2>
        <p class="text-muted">The system supports direct SQL database export features to guarantee security against server crashes. Backups are stored on local administrative drives behind secure firewalls.</p>
        
        <h2 class="fw-bold mt-4 mb-3">4. Cookies & Sessions</h2>
        <p class="text-muted">Our website implements secure PHP session cookies (`cookie_secure`, `cookie_httponly`, `cookie_samesite`) to maintain user authentication. Cookies do not track third-party activities or store user preferences beyond login details.</p>
        
        <div class="p-3 border rounded bg-light mt-5">
            <p class="mb-0 text-muted small"><i class="fa fa-info-circle text-primary me-2"></i>Last Updated: July 2026. For questions regarding privacy regulations, email: <strong>privacy@hiteacademy.edu.pk</strong></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
