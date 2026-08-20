<?php
// student/profile.php
// Student Profile Details Page

$page_title = "My Profile";
require_once dirname(__DIR__) . '/includes/student_header.php';

// Fetch detailed record
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$profile = $stmt->fetch();
?>

<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <!-- Blue header section -->
            <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, var(--navy-blue), var(--light-blue));">
                <img src="<?php echo $student_avatar; ?>" alt="Avatar" class="rounded-circle border border-3 border-white mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                <h4 class="fw-bold mb-1"><?php echo sanitize($profile['first_name'] . ' ' . $profile['last_name']); ?></h4>
                <p class="mb-0 text-white-50"><i class="fa fa-envelope me-1"></i> <?php echo sanitize($profile['email']); ?></p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">First Name</small>
                        <span class="fw-semibold text-dark fs-5"><?php echo sanitize($profile['first_name']); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Last Name</small>
                        <span class="fw-semibold text-dark fs-5"><?php echo sanitize($profile['last_name']); ?></span>
                    </div>
                    
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Phone Number</small>
                        <span class="fw-semibold text-dark fs-5"><?php echo sanitize($profile['phone'] ?? 'Not Specified'); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Gender</small>
                        <span class="fw-semibold text-dark fs-5"><?php echo sanitize($profile['gender'] ?? 'Not Specified'); ?></span>
                    </div>
                    
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Date of Birth</small>
                        <span class="fw-semibold text-dark fs-5">
                            <?php echo !empty($profile['dob']) ? format_date($profile['dob'], 'd M Y') : 'Not Specified'; ?>
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Account Status</small>
                        <span class="badge bg-success px-3 py-2 rounded-pill mt-1"><?php echo sanitize($profile['status']); ?></span>
                    </div>
                    
                    <div class="col-12">
                        <small class="text-muted d-block">Postal Address</small>
                        <span class="fw-semibold text-dark fs-5"><?php echo sanitize($profile['address'] ?? 'Not Specified'); ?></span>
                    </div>
                    
                    <div class="col-12 border-top pt-4 mt-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo BASE_URL; ?>student/edit_profile.php" class="btn btn-primary rounded-pill px-4">
                                <i class="fa fa-edit me-2"></i> Edit Profile Info
                            </a>
                            <a href="<?php echo BASE_URL; ?>student/change_password.php" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-key me-2"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
