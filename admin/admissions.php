<?php
// admin/admissions.php
// Administrator Admissions Application Reviewer Desk

$page_title = "Review Admissions";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

$errors = [];
$success = false;

// 1. Process Approve Admission POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_admission'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $admission_id = intval($_POST['admission_id'] ?? 0);
    
    try {
        $stmt = $pdo->prepare("UPDATE admissions SET status = 'Approved', review_comments = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$admission_id]);
        
        // Fetch details for student notification
        $details_stmt = $pdo->prepare("
            SELECT a.student_id, c.name as course_name 
            FROM admissions a 
            JOIN courses c ON a.course_id = c.id 
            WHERE a.id = ?
        ");
        $details_stmt->execute([$admission_id]);
        $details = $details_stmt->fetch();
        
        if ($details) {
            create_notification('Student', $details['student_id'], 'Admission Approved!', "Congratulations! Your admission application for the " . $details['course_name'] . " has been APPROVED. You can now download and print your Admission Slip.");
        }
        
        set_flash_message('success', 'Admission application has been approved.');
    } catch (PDOException $e) {
        set_flash_message('danger', 'Failed to approve application.');
    }
    
    header('Location: ' . BASE_URL . 'admin/admissions.php');
    exit;
}

// 2. Process Reject Admission POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_admission'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $admission_id = intval($_POST['admission_id'] ?? 0);
    $comments = sanitize($_POST['comments'] ?? '');
    
    if (empty($comments)) {
        set_flash_message('danger', 'Rejection comments / reason must be specified.');
        header('Location: ' . BASE_URL . 'admin/admissions.php');
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE admissions SET status = 'Rejected', review_comments = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$comments, $admission_id]);
        
        // Fetch details for notification
        $details_stmt = $pdo->prepare("
            SELECT a.student_id, c.name as course_name 
            FROM admissions a 
            JOIN courses c ON a.course_id = c.id 
            WHERE a.id = ?
        ");
        $details_stmt->execute([$admission_id]);
        $details = $details_stmt->fetch();
        
        if ($details) {
            create_notification('Student', $details['student_id'], 'Admission Rejected', "Your admission application for " . $details['course_name'] . " was rejected. Reason: $comments");
        }
        
        set_flash_message('success', 'Admission application marked as Rejected.');
    } catch (PDOException $e) {
        set_flash_message('danger', 'Failed to reject application.');
    }
    
    header('Location: ' . BASE_URL . 'admin/admissions.php');
    exit;
}

// Fetch all admissions with course & student details
try {
    $stmt = $pdo->query("
        SELECT a.*, s.first_name, s.last_name, s.email, c.name as course_name, c.code as course_code
        FROM admissions a
        JOIN students s ON a.student_id = s.id
        JOIN courses c ON a.course_id = c.id
        ORDER BY a.status DESC, a.id DESC
    ");
    $admissions = $stmt->fetchAll();
} catch (PDOException $e) {
    $admissions = [];
}

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-file-signature text-primary me-2"></i>Review Course Admissions</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($admissions)): ?>
                    <p class="text-muted text-center py-5">No admissions applications have been submitted yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Program Code</th>
                                    <th>Date Applied</th>
                                    <th>Matric Cert</th>
                                    <th>CNIC scan</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admissions as $adm): 
                                    $badge = 'badge-pending';
                                    if ($adm['status'] === 'Approved') $badge = 'badge-approved';
                                    if ($adm['status'] === 'Rejected') $badge = 'badge-rejected';
                                    
                                    $matric_url = !empty($adm['matric_certificate']) ? BASE_URL . $adm['matric_certificate'] : '#';
                                    $cnic_url = !empty($adm['cnic_copy']) ? BASE_URL . $adm['cnic_copy'] : '#';
                                ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark d-block"><?php echo sanitize($adm['first_name'] . ' ' . $adm['last_name']); ?></span>
                                            <small class="text-muted"><?php echo sanitize($adm['email']); ?></small>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark"><?php echo sanitize($adm['course_name']); ?></span>
                                            <small class="text-primary d-block fw-semibold">[<?php echo sanitize($adm['course_code']); ?>]</small>
                                        </td>
                                        <td><?php echo format_date($adm['apply_date'], 'd M Y'); ?></td>
                                        <td>
                                            <a href="<?php echo $matric_url; ?>" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2 rounded-pill small"><i class="fa fa-external-link-alt me-1"></i> View File</a>
                                        </td>
                                        <td>
                                            <a href="<?php echo $cnic_url; ?>" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2 rounded-pill small"><i class="fa fa-external-link-alt me-1"></i> View File</a>
                                        </td>
                                        <td><span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><?php echo $adm['status']; ?></span></td>
                                        <td>
                                            <?php if ($adm['status'] === 'Pending'): ?>
                                                <div class="d-flex gap-1">
                                                    <!-- Approve -->
                                                    <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('Confirm approve student admission?');">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="admission_id" value="<?php echo $adm['id']; ?>">
                                                        <button type="submit" name="approve_admission" class="btn btn-sm btn-success rounded-pill text-white px-2 py-1 small">
                                                            <i class="fa fa-check me-1"></i> Approve
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Reject trigger -->
                                                    <button class="btn btn-sm btn-danger rounded-pill text-white px-2 py-1 small btn-reject-trigger" data-id="<?php echo $adm['id']; ?>">
                                                        <i class="fa fa-times me-1"></i> Reject
                                                    </button>
                                                </div>
                                            <?php elseif ($adm['status'] === 'Rejected'): ?>
                                                <small class="text-muted d-block" style="max-width: 150px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?php echo sanitize($adm['review_comments']); ?>">
                                                    <strong>Reason:</strong> <?php echo sanitize($adm['review_comments']); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-success small"><i class="fa fa-check-circle me-1"></i> Enrolled</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Reject Comments Modal -->
<div class="modal fade" id="rejectCommentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-bold modal-title text-danger"><i class="fa fa-times-circle me-2"></i>Reject Admission Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="reject_admission_id" name="admission_id" value="">
                
                <div class="modal-body">
                    <p class="small text-muted">Enter comments detailing why this application is being rejected (e.g. document scans illegible, incorrect names). The student will see this comment in their status dashboard so they can correct and re-apply.</p>
                    <div class="mb-3">
                        <label for="comments" class="form-label fw-semibold">Reviewer Comments / Reasons <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="comments" name="comments" rows="4" required placeholder="e.g. Matric marksheet copy blurry. Please re-upload a clean JPG/PNG image."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="reject_admission" class="btn btn-danger rounded-pill px-4 text-white">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-reject-trigger').on('click', function() {
        var id = $(this).data('id');
        $('#reject_admission_id').val(id);
        
        var modal = new bootstrap.Modal(document.getElementById('rejectCommentsModal'));
        modal.show();
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
