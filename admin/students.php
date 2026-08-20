<?php
// admin/students.php
// Administrator Student Management Panel

$page_title = "Manage Students";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

// 1. Export Students to CSV Flow
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=students_records_' . time() . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Gender', 'DOB', 'Status', 'Registered Date']);
    
    try {
        $stmt = $pdo->query("SELECT id, first_name, last_name, email, phone, gender, dob, status, created_at FROM students ORDER BY id ASC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
    } catch (PDOException $e) {
        error_log("Failed to export: " . $e->getMessage());
    }
    fclose($output);
    exit;
}

// 2. Process Student Status Toggles (Activate / Suspend)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $student_id = intval($_POST['student_id'] ?? 0);
    $new_status = sanitize($_POST['status'] ?? 'Active');
    
    try {
        $stmt = $pdo->prepare("UPDATE students SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $student_id]);
        
        // Notify student of status update
        create_notification('Student', $student_id, 'Account Status Updated', "Your student account status has been set to: $new_status.");
        
        set_flash_message('success', "Student account status modified to: $new_status.");
    } catch (PDOException $e) {
        set_flash_message('danger', "Failed to update account status.");
    }
    
    header('Location: ' . BASE_URL . 'admin/students.php');
    exit;
}

// 3. Process Delete Student Account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $student_id = intval($_POST['student_id'] ?? 0);
    
    try {
        // Fetch student profile picture to delete from disk
        $pic_stmt = $pdo->prepare("SELECT profile_pic FROM students WHERE id = ?");
        $pic_stmt->execute([$student_id]);
        $pic = $pic_stmt->fetchColumn();
        if (!empty($pic) && file_exists(ROOT_PATH . $pic)) {
            unlink(ROOT_PATH . $pic);
        }
        
        // Admissions files deletion
        $adm_stmt = $pdo->prepare("SELECT matric_certificate, cnic_copy FROM admissions WHERE student_id = ?");
        $adm_stmt->execute([$student_id]);
        $adm_docs = $adm_stmt->fetchAll();
        foreach ($adm_docs as $doc) {
            if (!empty($doc['matric_certificate']) && file_exists(ROOT_PATH . $doc['matric_certificate'])) {
                unlink(ROOT_PATH . $doc['matric_certificate']);
            }
            if (!empty($doc['cnic_copy']) && file_exists(ROOT_PATH . $doc['cnic_copy'])) {
                unlink(ROOT_PATH . $doc['cnic_copy']);
            }
        }
        
        $del_stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $del_stmt->execute([$student_id]);
        set_flash_message('success', "Student record deleted successfully.");
    } catch (PDOException $e) {
        set_flash_message('danger', "Failed to delete student records.");
    }
    
    header('Location: ' . BASE_URL . 'admin/students.php');
    exit;
}

// 4. Pagination & Search settings
$search = sanitize($_GET['search'] ?? '');
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    if ($search !== '') {
        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?");
        $count_stmt->execute(["%$search%", "%$search%", "%$search%"]);
        $total_records = $count_stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT * FROM students WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(3, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);
        $stmt->bindValue(5, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll();
    } else {
        $total_records = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT * FROM students ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $students = [];
    $total_records = 0;
}

$total_pages = ceil($total_records / $limit);

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-user-graduate text-primary me-2"></i>Students Roster</h5>
                <div>
                    <a href="?export=csv" class="btn btn-success btn-sm rounded-pill text-white px-3"><i class="fa fa-file-excel me-1"></i> Export Data CSV</a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <!-- Search Box -->
                <form action="" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search text-muted"></i></span>
                            <input type="text" class="form-control" name="search" value="<?php echo sanitize($search); ?>" placeholder="Search student name or email...">
                            <button type="submit" class="btn btn-primary text-white">Search</button>
                        </div>
                    </div>
                    <?php if ($search !== ''): ?>
                        <div class="col-md-2">
                            <a href="<?php echo BASE_URL; ?>admin/students.php" class="btn btn-outline-secondary">Reset View</a>
                        </div>
                    <?php endif; ?>
                </form>
                
                <?php if (empty($students)): ?>
                    <p class="text-muted text-center py-5">No student records found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): 
                                    $avatar = !empty($student['profile_pic']) && file_exists(ROOT_PATH . $student['profile_pic']) 
                                        ? BASE_URL . $student['profile_pic'] 
                                        : BASE_URL . 'assets/images/default-avatar.png';
                                ?>
                                    <tr>
                                        <td>HTA-<?php echo str_pad($student['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <img src="<?php echo $avatar; ?>" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        </td>
                                        <td><span class="fw-semibold text-dark"><?php echo sanitize($student['first_name'] . ' ' . $student['last_name']); ?></span></td>
                                        <td><?php echo sanitize($student['email']); ?></td>
                                        <td><?php echo sanitize($student['phone'] ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($student['status'] === 'Active') ? 'success' : 'danger'; ?> px-2 py-1 rounded-pill small">
                                                <?php echo $student['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <!-- View Student details modal trigger -->
                                                <button class="btn btn-sm btn-outline-primary rounded-circle btn-view-profile" 
                                                        data-first-name="<?php echo sanitize($student['first_name']); ?>"
                                                        data-last-name="<?php echo sanitize($student['last_name']); ?>"
                                                        data-email="<?php echo sanitize($student['email']); ?>"
                                                        data-phone="<?php echo sanitize($student['phone'] ?? 'N/A'); ?>"
                                                        data-gender="<?php echo sanitize($student['gender'] ?? 'N/A'); ?>"
                                                        data-dob="<?php echo !empty($student['dob']) ? format_date($student['dob'], 'd M Y') : 'N/A'; ?>"
                                                        data-address="<?php echo sanitize($student['address'] ?? 'N/A'); ?>"
                                                        data-joined="<?php echo format_date($student['created_at']); ?>"
                                                        data-avatar="<?php echo $avatar; ?>">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <!-- Suspend / Activate toggles -->
                                                <form action="" method="POST" style="display:inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                    <?php if ($student['status'] === 'Active'): ?>
                                                        <input type="hidden" name="status" value="Suspended">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-warning rounded-circle" title="Suspend Account"><i class="fa fa-ban"></i></button>
                                                    <?php else: ?>
                                                        <input type="hidden" name="status" value="Active">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-success rounded-circle" title="Activate Account"><i class="fa fa-check"></i></button>
                                                    <?php endif; ?>
                                                </form>

                                                <!-- Delete button -->
                                                <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('WARNING: Are you sure you want to delete this student account? All corresponding admissions and questions will be permanently deleted.');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                    <button type="submit" name="delete_student" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete Account"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Profile Modal -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-bold modal-title"><i class="fa fa-user-circle me-2 text-primary"></i>Student Profile Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modal-avatar" alt="Avatar" class="rounded-circle border border-2 border-primary mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                <h5 class="fw-bold mb-1" id="modal-name"></h5>
                <p class="text-muted small mb-4" id="modal-email"></p>
                
                <div class="row g-3 text-start small border-top pt-3">
                    <div class="col-6">
                        <span class="text-muted d-block">Phone:</span>
                        <strong class="text-dark" id="modal-phone"></strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">Gender:</span>
                        <strong class="text-dark" id="modal-gender"></strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">Date of Birth:</span>
                        <strong class="text-dark" id="modal-dob"></strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">Registered Date:</span>
                        <strong class="text-dark" id="modal-joined"></strong>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block">Postal Address:</span>
                        <strong class="text-dark" id="modal-address"></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-view-profile').on('click', function() {
        // Load details from data attributes
        $('#modal-avatar').attr('src', $(this).data('avatar'));
        $('#modal-name').text($(this).data('first-name') + ' ' + $(this).data('last-name'));
        $('#modal-email').text($(this).data('email'));
        $('#modal-phone').text($(this).data('phone'));
        $('#modal-gender').text($(this).data('gender'));
        $('#modal-dob').text($(this).data('dob'));
        $('#modal-joined').text($(this).data('joined'));
        $('#modal-address').text($(this).data('address'));
        
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
        modal.show();
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
