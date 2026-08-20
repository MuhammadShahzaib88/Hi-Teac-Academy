<?php
// student/dashboard.php
// Student Dashboard Home

$page_title = "Student Dashboard";
require_once dirname(__DIR__) . '/includes/student_header.php';

$student_id = $_SESSION['student_id'];

// 1. Fetch Admission status
$stmt = $pdo->prepare("SELECT a.*, c.name as course_name FROM admissions a JOIN courses c ON a.course_id = c.id WHERE a.student_id = ? ORDER BY a.id DESC LIMIT 1");
$stmt->execute([$student_id]);
$admission = $stmt->fetch();

// 2. Count asked questions
$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE student_id = ?");
$stmt->execute([$student_id]);
$total_questions = $stmt->fetchColumn();

// 3. Count unread notifications
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_type = 'Student' AND user_id = ? AND is_read = 0");
$stmt->execute([$student_id]);
$unread_notif = $stmt->fetchColumn();

// Fetch Latest Announcements
$ann_stmt = $pdo->query("SELECT * FROM announcements WHERE status = 'Active' ORDER BY created_at DESC LIMIT 3");
$announcements = $ann_stmt->fetchAll();
?>

<!-- Info Widgets Row -->
<div class="row g-4 mb-4">
    <!-- Admission Tracker Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 text-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa fa-file-invoice fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">Admission Status</h5>
                <?php if ($admission): ?>
                    <p class="text-muted small mb-3"><?php echo sanitize($admission['course_name']); ?></p>
                    <?php 
                    $badge_class = 'badge-pending';
                    if ($admission['status'] === 'Approved') $badge_class = 'badge-approved';
                    if ($admission['status'] === 'Rejected') $badge_class = 'badge-rejected';
                    ?>
                    <span class="badge <?php echo $badge_class; ?> px-3 py-2 rounded-pill mb-2"><?php echo $admission['status']; ?></span>
                    <div class="mt-2">
                        <a href="<?php echo BASE_URL; ?>student/admission_status.php" class="small text-decoration-none fw-semibold">View Details <i class="fa fa-arrow-right ms-1"></i></a>
                    </div>
                <?php else: ?>
                    <p class="text-muted small mb-3">You haven't applied for any courses yet.</p>
                    <a href="<?php echo BASE_URL; ?>student/admission.php" class="btn btn-sm btn-primary rounded-pill text-white px-3">
                        <i class="fa fa-plus me-1"></i> Apply Admission
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Questions Count Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 text-center">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa fa-question-circle fs-3"></i>
                </div>
                <h5 class="fw-bold mb-1">Academic Questions</h5>
                <p class="text-muted small mb-3">Total tickets asked: <strong><?php echo $total_questions; ?></strong></p>
                <a href="<?php echo BASE_URL; ?>student/ask_question.php" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    <i class="fa fa-envelope-open me-1"></i> Open Tickets Q&A
                </a>
            </div>
        </div>
    </div>

    <!-- Notifications Count Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 text-center">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fa fa-bell fs-3 text-warning"></i>
                </div>
                <h5 class="fw-bold mb-1">Unread Alerts</h5>
                <p class="text-muted small mb-3">Pending notifications: <strong><?php echo $unread_notif; ?></strong></p>
                <a href="<?php echo BASE_URL; ?>student/notifications.php" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                    <i class="fa fa-eye me-1"></i> View Notifications
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Announcements List -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-bullhorn text-primary me-2"></i>Latest Announcements</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($announcements)): ?>
                    <p class="text-muted p-4">No announcements posted at this time.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($announcements as $announce): ?>
                            <div class="list-group-item p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-primary text-uppercase"><?php echo sanitize($announce['type']); ?></span>
                                    <small class="text-muted"><i class="fa fa-clock me-1"></i> <?php echo format_date($announce['created_at'], 'd M Y'); ?></small>
                                </div>
                                <h6 class="fw-bold text-dark"><?php echo sanitize($announce['title']); ?></h6>
                                <p class="text-muted small mb-0"><?php echo sanitize($announce['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Profile Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-user-circle text-primary me-2"></i>Profile Summary</h5>
            </div>
            <div class="card-body p-4 text-center">
                <img src="<?php echo $student_avatar; ?>" alt="Profile avatar" class="rounded-circle border border-2 border-primary mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                <h5 class="fw-bold mb-1"><?php echo sanitize($student_full_name); ?></h5>
                <p class="text-muted small mb-4"><?php echo sanitize($student_user['email']); ?></p>
                
                <div class="d-grid gap-2">
                    <a href="<?php echo BASE_URL; ?>student/profile.php" class="btn btn-outline-primary rounded-pill"><i class="fa fa-eye me-2"></i> View Profile Details</a>
                    <a href="<?php echo BASE_URL; ?>student/edit_profile.php" class="btn btn-primary text-white rounded-pill"><i class="fa fa-edit me-2"></i> Edit Account Info</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
