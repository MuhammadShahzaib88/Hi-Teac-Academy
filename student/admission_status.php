<?php
// student/admission_status.php
// Track admission status & print admission slip

$page_title = "Admission Status";
require_once dirname(__DIR__) . '/includes/student_header.php';

$student_id = $_SESSION['student_id'];

// Fetch student details
$stud_stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stud_stmt->execute([$student_id]);
$student_info = $stud_stmt->fetch();

// Fetch admissions history with course details
$stmt = $pdo->prepare("
    SELECT a.*, c.name as course_name, c.code as course_code, c.duration, c.fee, t.name as teacher_name
    FROM admissions a
    JOIN courses c ON a.course_id = c.id
    LEFT JOIN teachers t ON c.instructor_id = t.id
    WHERE a.student_id = ?
    ORDER BY a.id DESC
");
$stmt->execute([$student_id]);
$admissions = $stmt->fetchAll();
?>

<!-- Admission Tracking Grid -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom no-print">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-clock-rotate-left text-primary me-2"></i>My Admission History</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($admissions)): ?>
                    <div class="text-center py-5 no-print">
                        <i class="fa fa-file-invoice text-muted fa-4x mb-3"></i>
                        <p class="text-muted">You have not submitted any admission applications.</p>
                        <a href="<?php echo BASE_URL; ?>student/admission.php" class="btn btn-primary rounded-pill px-4 text-white">Apply Now</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive no-print">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Application Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admissions as $adm): 
                                    $badge = 'badge-pending';
                                    if ($adm['status'] === 'Approved') $badge = 'badge-approved';
                                    if ($adm['status'] === 'Rejected') $badge = 'badge-rejected';
                                ?>
                                    <tr>
                                        <td><span class="fw-bold"><?php echo sanitize($adm['course_code']); ?></span></td>
                                        <td><?php echo sanitize($adm['course_name']); ?></td>
                                        <td><?php echo format_date($adm['apply_date'], 'd M Y'); ?></td>
                                        <td><span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><?php echo $adm['status']; ?></span></td>
                                        <td>
                                            <?php if ($adm['status'] === 'Approved'): ?>
                                                <button class="btn btn-sm btn-primary rounded-pill text-white btn-view-slip" data-id="<?php echo $adm['id']; ?>">
                                                    <i class="fa fa-print me-1"></i> Print Slip
                                                </button>
                                            <?php elseif ($adm['status'] === 'Rejected'): ?>
                                                <button class="btn btn-sm btn-outline-danger rounded-pill btn-view-comment" data-comment="<?php echo sanitize($adm['review_comments'] ?? 'No reasons specified.'); ?>">
                                                    <i class="fa fa-exclamation-circle me-1"></i> Review Reason
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">Awaiting Review</span>
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

<!-- Rejection Modal -->
<div class="modal fade no-print" id="rejectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-bold modal-title text-danger"><i class="fa fa-exclamation-triangle me-2"></i>Application Rejection Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="rejection-comment-text" class="text-muted"></p>
                <div class="p-3 bg-light rounded mt-3 text-start small">
                    <span class="fw-semibold text-dark">How to re-apply:</span>
                    <p class="mb-0 text-muted">Submit a brand new admission form in the dashboard after fixing the document or detail issues listed above.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="<?php echo BASE_URL; ?>student/admission.php" class="btn btn-primary rounded-pill px-4 text-white">Re-apply Admission</a>
            </div>
        </div>
    </div>
</div>

<!-- Printable Slip Generator Area (Hidden on screen unless print class active or viewed) -->
<?php foreach ($admissions as $adm): 
    if ($adm['status'] !== 'Approved') continue;
?>
    <div class="print-area d-none" id="slip-container-<?php echo $adm['id']; ?>">
        <div class="card border border-dark rounded p-4" style="max-width: 800px; margin: 0 auto; background: #fff;">
            <div class="row align-items-center mb-4 border-bottom pb-3">
                <div class="col-8">
                    <h2 class="fw-bold text-dark mb-0"><i class="fa fa-graduation-cap text-primary me-2"></i>Hi Teac Academy</h2>
                    <p class="text-muted small mb-0">Board of Technical Education Certified Institute</p>
                </div>
                <div class="col-4 text-end">
                    <span class="badge bg-success p-2 fs-6">OFFICIAL ADMISSION SLIP</span>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <small class="text-muted d-block">Student Name</small>
                    <span class="fw-bold text-dark fs-5"><?php echo sanitize($student_info['first_name'] . ' ' . $student_info['last_name']); ?></span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Student ID / Roll Code</small>
                    <span class="fw-bold text-dark fs-5">HTA-STUD-<?php echo str_pad($student_id, 4, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Enrolled Course</small>
                    <span class="fw-bold text-primary fs-5"><?php echo sanitize($adm['course_name']); ?> (<?php echo sanitize($adm['course_code']); ?>)</span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Course Duration</small>
                    <span class="fw-bold text-dark fs-5"><?php echo sanitize($adm['duration']); ?></span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Email Address</small>
                    <span class="fw-medium text-dark"><?php echo sanitize($student_info['email']); ?></span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Mobile Contact</small>
                    <span class="fw-medium text-dark"><?php echo sanitize($student_info['phone'] ?? 'N/A'); ?></span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Approved Date</small>
                    <span class="fw-medium text-dark"><?php echo format_date($adm['updated_at'], 'd M Y'); ?></span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Course Instructor</small>
                    <span class="fw-medium text-dark"><?php echo sanitize($adm['teacher_name'] ?? 'Academy Instructor'); ?></span>
                </div>
            </div>

            <div class="p-3 bg-light rounded border mb-4">
                <h6 class="fw-bold text-dark"><i class="fa fa-info-circle text-primary me-2"></i>Class Schedule Regulations:</h6>
                <p class="small text-muted mb-0">Please present this slip at the administration counter to verify your details, settle your initial fee installment, and confirm your preferred class batch timing selection.</p>
            </div>
            
            <div class="row pt-4 border-top">
                <div class="col-6">
                    <div style="height: 60px; border-bottom: 1px solid #ddd; width: 200px;"></div>
                    <small class="text-muted">Student Signature</small>
                </div>
                <div class="col-6 text-end">
                    <div style="height: 60px; border-bottom: 1px solid #ddd; width: 200px; margin-left: auto;"></div>
                    <small class="text-muted">Registrar Stamp / Authority</small>
                </div>
            </div>
            
            <div class="text-center mt-4 border-top pt-2 no-print">
                <button class="btn btn-warning rounded-pill px-4 btn-print-slip"><i class="fa fa-print me-1"></i> Print This Slip Now</button>
                <button class="btn btn-outline-secondary rounded-pill px-4 btn-close-slip"><i class="fa fa-times me-1"></i> Close Slip View</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Show Rejection modal
    $('.btn-view-comment').on('click', function() {
        var comment = $(this).data('comment');
        $('#rejection-comment-text').text(comment);
        var modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
        modal.show();
    });

    // 2. View Printable Admission Slip
    $('.btn-view-slip').on('click', function() {
        var id = $(this).data('id');
        
        // Hide standard grid elements
        $('.no-print').addClass('d-none');
        
        // Show printable area
        $('#slip-container-' + id).removeClass('d-none');
    });

    // 3. Close Printable Admission Slip
    $('.btn-close-slip').on('click', function() {
        // Hide printable areas
        $('.print-area').addClass('d-none');
        
        // Show standard grid elements
        $('.no-print').removeClass('d-none');
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
