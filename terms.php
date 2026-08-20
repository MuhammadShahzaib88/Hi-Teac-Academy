<?php
// terms.php
// Terms of Service Page

$page_title = "Terms of Service";
require_once 'config/config.php';
include 'includes/header.php';
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">Terms of Service</h1>
        <p class="lead mb-0">Rules and guidelines governing student enrollment</p>
    </div>
</section>

<!-- Terms Content -->
<section class="py-5">
    <div class="container" style="max-width: 800px;" data-aos="fade-up">
        <h2 class="fw-bold mb-3">1. Admission Approval</h2>
        <p class="text-muted">Submitting the online admission application form does not guarantee enrollment. Admittance is finalized once the administration validates the authenticity of Matric certification and parent CNIC scans and marks the application status as 'Approved'.</p>
        
        <h2 class="fw-bold mt-4 mb-3">2. Tuition Fee Payment</h2>
        <p class="text-muted">Approved students must deposit their first month's installment or full semester fee within 7 days of approval to preserve their seat in the chosen batch. Failure to submit fees on schedule can result in registration deactivation.</p>
        
        <h2 class="fw-bold mt-4 mb-3">3. Code of Conduct</h2>
        <p class="text-muted">Students must maintain minimum 75% attendance in both lectures and practical labs to satisfy technical education board examination requirements. Disruption of lab equipment, plagiarism in coding projects, or harassment of staff will result in immediate suspension.</p>
        
        <h2 class="fw-bold mt-4 mb-3">4. Question Tickets Desk</h2>
        <p class="text-muted">Our student portal features a question submission section. Tickets must remain restricted to academic queries related to DIT/CIT syllabus modules. Spamming, offensive language, or ticket abuse is strictly prohibited.</p>
        
        <div class="p-3 border rounded bg-light mt-5">
            <p class="mb-0 text-muted small"><i class="fa fa-info-circle text-primary me-2"></i>Last Updated: July 2026. For inquiries regarding course terms, call: <strong><?php echo get_setting('contact_phone', '03304347547'); ?></strong></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
