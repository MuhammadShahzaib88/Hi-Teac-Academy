<?php
// contact.php
// Public Contact Page

require_once 'config/config.php';

$errors = [];
$success = false;
$name = $email = $subject = $message = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (is_student_logged_in() && isset($_SESSION['student_id'])) {
        $st = $pdo->prepare("SELECT first_name, last_name, email FROM students WHERE id = ?");
        $st->execute([$_SESSION['student_id']]);
        $stu = $st->fetch();
        if ($stu) {
            $name = $stu['first_name'] . ' ' . $stu['last_name'];
            $email = $stu['email'];
        }
    }
    if (empty($name)) $name = 'Muhammad Shahzaib';
    if (empty($email)) $email = 'shahzaibbangash24@gmail.com';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($subject)) $errors[] = "Subject is required.";
    if (empty($message)) $errors[] = "Message content is required.";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message, status) VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->execute([$name, $email, $subject, $message]);
            
            // Set notifications for admin
            create_notification('Admin', 1, 'New Contact Message', "You received a new inquiry from $name: '$subject'");
            
            $success = true;
            set_flash_message('success', 'Thank you! Your message has been sent successfully. We will contact you soon.');
            
            // Reset fields
            $name = $email = $subject = $message = '';
        } catch (PDOException $e) {
            $errors[] = "Failed to store message. Please try again later.";
        }
    }
}

$page_title = "Contact Us";
include 'includes/header.php';
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">Contact Our Office</h1>
        <p class="lead mb-0">Have questions about admissions or technical diplomas? Write to us.</p>
    </div>
</section>

<!-- Contact Form and Details -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Info Cards -->
            <div class="col-lg-5" data-aos="fade-right">
                <h3 class="fw-bold mb-4 text-dark">Get in Touch</h3>
                
                <div class="card border-0 shadow-sm p-4 mb-4 bg-light rounded-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-map-marker-alt fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Campus Location</h6>
                            <small class="text-muted">Physical Office</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0 small"><?php echo get_setting('address', 'Kohat, KPK, Pakistan'); ?></p>
                </div>

                <div class="card border-0 shadow-sm p-4 mb-4 bg-light rounded-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-phone-alt fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Phone Coordinates</h6>
                            <small class="text-muted">Call during office hours (9 AM - 6 PM)</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0 small"><?php echo get_setting('contact_phone', '03304347547'); ?></p>
                </div>

                <div class="card border-0 shadow-sm p-4 bg-light rounded-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fa fa-envelope-open fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Email Queries</h6>
                            <small class="text-muted">General & Admission Support</small>
                        </div>
                    </div>
                    <p class="text-muted mb-0 small"><?php echo get_setting('contact_email', 'shahzaibbangash24@gmail.com'); ?></p>
                </div>
            </div>

            <!-- Right Form Column -->
            <div class="col-lg-7" data-aos="fade-left">
                <div class="card border-0 shadow rounded-3 p-4 p-md-5 bg-white">
                    <h3 class="fw-bold text-dark mb-4">Send a Message</h3>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>contact.php" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($name); ?>" required placeholder="e.g. Ali Ahmed">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize($email); ?>" required placeholder="e.g. name@example.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" value="<?php echo sanitize($subject); ?>" required placeholder="e.g. Inquiry regarding DIT Admission">
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Message / Inquiry Details <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Type your detailed message here..."><?php echo sanitize($message); ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="fa fa-paper-plane me-2"></i> Submit Inquiry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Maps Visual Placeholder -->
        <div class="row mt-5" data-aos="fade-up">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="bg-dark text-white p-3 text-center">
                        <i class="fa fa-map-marked-alt text-primary me-2"></i> <strong>Hi Teac Academy Campus Map Representation</strong>
                    </div>
                    <div style="height: 350px; background-color: #e9ecef;" class="d-flex align-items-center justify-content-center text-muted flex-column">
                        <i class="fa fa-university fa-3x mb-3 text-primary"></i>
                        <p class="mb-0 fw-semibold text-dark">Hi Teac Academy Campus - Kohat, KPK, Pakistan</p>
                        <p class="small text-muted mb-0">Kohat, KPK, Pakistan</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<?php include 'includes/footer.php'; ?>
