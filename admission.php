<?php
// admission.php
// Admission Portal Redirect / Info Page

require_once 'config/config.php';

// Redirect to dashboard application if already logged in
if (is_student_logged_in()) {
    header('Location: ' . BASE_URL . 'student/admission.php');
    exit;
}

$page_title = "Online Admission Info";
include 'includes/header.php';
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">Online Admission Portal</h1>
        <p class="lead mb-0">Apply online for government certified DIT and CIT programs</p>
    </div>
</section>

<!-- Admission Information -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Info Column -->
            <div class="col-lg-7" data-aos="fade-right">
                <h2 class="fw-bold mb-4">How to Apply Online</h2>
                <p class="text-muted">Hi Teac Academy provides an entirely digital admission application process. Follow these simple steps to secure your enrollment:</p>
                
                <div class="mb-4 d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; font-weight: 600;">1</div>
                    <div>
                        <h5 class="fw-bold mb-1">Create Student Account</h5>
                        <p class="text-muted small">Register yourself with a valid email address and active mobile phone number.</p>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; font-weight: 600;">2</div>
                    <div>
                        <h5 class="fw-bold mb-1">Upload Education Credentials</h5>
                        <p class="text-muted small">Log in, enter your personal details, and upload digital scans of your Matric certificate and CNIC or B-Form.</p>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; font-weight: 600;">3</div>
                    <div>
                        <h5 class="fw-bold mb-1">Review & Status Tracking</h5>
                        <p class="text-muted small">The academy admins will review your documents. You can track the progress (Pending / Approved / Rejected) directly in your dashboard.</p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; font-weight: 600;">4</div>
                    <div>
                        <h5 class="fw-bold mb-1">Print Admission Slip</h5>
                        <p class="text-muted small">Once approved, download and print your official Admission Slip containing your roll code and batch timings.</p>
                    </div>
                </div>
            </div>

            <!-- Right Options Column -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="card border-0 shadow-lg p-4 p-md-5 bg-light rounded-3 text-center">
                    <i class="fa fa-file-signature fa-4x text-primary mb-3"></i>
                    <h3 class="fw-bold text-dark">Get Started</h3>
                    <p class="text-muted">You must be logged in as a registered student to access the admission form.</p>
                    
                    <div class="d-grid gap-3 mt-4">
                        <a href="<?php echo BASE_URL; ?>authentication/register.php" class="btn btn-primary btn-lg rounded-pill text-white py-2 shadow-sm hover-lift">
                            <i class="fa fa-user-plus me-2"></i> Register New Account
                        </a>
                        <a href="<?php echo BASE_URL; ?>authentication/login.php" class="btn btn-outline-primary btn-lg rounded-pill py-2 hover-lift">
                            <i class="fa fa-sign-in-alt me-2"></i> Log In Existing Student
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <h6 class="fw-bold"><i class="fa fa-info-circle text-primary me-2"></i>Required Documents Checklist:</h6>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li class="mb-1"><i class="fa fa-check text-success me-2"></i>Matriculation Marksheet / Certificate (Scan Copy)</li>
                            <li class="mb-1"><i class="fa fa-check text-success me-2"></i>CNIC (Student or Parent) / B-Form (Scan Copy)</li>
                            <li><i class="fa fa-check text-success me-2"></i>Passport Size Blue-Background Photograph</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
