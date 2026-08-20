<?php
// admin/courses.php
// Administrator Course Profiles Manager

$page_title = "Manage Courses";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

$errors = [];
$success = false;

// Fetch instructors list for dropdown
$teachers = $pdo->query("SELECT id, name FROM teachers ORDER BY name ASC")->fetchAll();

// 1. Process Add Course POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $name = sanitize($_POST['name'] ?? '');
    $code = sanitize($_POST['code'] ?? '');
    $duration = sanitize($_POST['duration'] ?? '');
    $fee = floatval($_POST['fee'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $modules = sanitize($_POST['modules'] ?? '');
    $instructor_id = intval($_POST['instructor_id'] ?? 0);
    
    if (empty($name)) $errors[] = "Course name is required.";
    if (empty($code)) $errors[] = "Course code is required.";
    if (empty($duration)) $errors[] = "Course duration is required.";
    if ($fee <= 0) $errors[] = "Fee must be a valid positive amount.";
    
    // Check code unique
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE code = ?");
        $stmt->execute([$code]);
        if ($stmt->fetch()) {
            $errors[] = "A course with this code already exists.";
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (name, code, duration, fee, description, modules, instructor_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')");
            $stmt->execute([$name, $code, $duration, $fee, $description, $modules, $instructor_id ?: null]);
            set_flash_message('success', "Course profile created successfully.");
            header('Location: ' . BASE_URL . 'admin/courses.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database insertion error: " . $e->getMessage();
        }
    }
}

// 2. Process Edit Course POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_course'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $course_id = intval($_POST['course_id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $code = sanitize($_POST['code'] ?? '');
    $duration = sanitize($_POST['duration'] ?? '');
    $fee = floatval($_POST['fee'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $modules = sanitize($_POST['modules'] ?? '');
    $instructor_id = intval($_POST['instructor_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? 'Active');
    
    if (empty($name)) $errors[] = "Course name is required.";
    if (empty($code)) $errors[] = "Course code is required.";
    if (empty($duration)) $errors[] = "Course duration is required.";
    if ($fee <= 0) $errors[] = "Fee must be a valid positive amount.";
    
    // Check code unique for other courses
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE code = ? AND id != ?");
        $stmt->execute([$code, $course_id]);
        if ($stmt->fetch()) {
            $errors[] = "A course with this code already exists.";
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE courses SET name = ?, code = ?, duration = ?, fee = ?, description = ?, modules = ?, instructor_id = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $code, $duration, $fee, $description, $modules, $instructor_id ?: null, $status, $course_id]);
            set_flash_message('success', "Course profile updated successfully.");
            header('Location: ' . BASE_URL . 'admin/courses.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database update error: " . $e->getMessage();
        }
    }
}

// 3. Process Delete Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $course_id = intval($_POST['course_id'] ?? 0);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        set_flash_message('success', "Course deleted successfully.");
    } catch (PDOException $e) {
        $errors[] = "Failed to delete course. It may be referenced in student admissions.";
    }
}

// Fetch all courses
try {
    $courses_stmt = $pdo->query("SELECT c.*, t.name as instructor_name FROM courses c LEFT JOIN teachers t ON c.instructor_id = t.id ORDER BY c.id ASC");
    $courses = $courses_stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
}

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">
    <!-- List of Courses -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-book text-primary me-2"></i>Active Programs</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($courses)): ?>
                    <p class="text-muted text-center py-5">No courses registered.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Course Name</th>
                                    <th>Duration</th>
                                    <th>Fee</th>
                                    <th>Instructor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $c): ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo sanitize($c['code']); ?></span></td>
                                        <td><span class="fw-bold text-dark"><?php echo sanitize($c['name']); ?></span></td>
                                        <td><?php echo sanitize($c['duration']); ?></td>
                                        <td><?php echo format_currency($c['fee']); ?></td>
                                        <td><?php echo sanitize($c['instructor_name'] ?? 'Not Assigned'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($c['status'] === 'Active') ? 'success' : 'secondary'; ?> px-2 py-1 rounded-pill">
                                                <?php echo $c['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-outline-primary rounded-circle btn-edit-course" 
                                                        data-id="<?php echo $c['id']; ?>"
                                                        data-name="<?php echo sanitize($c['name']); ?>"
                                                        data-code="<?php echo sanitize($c['code']); ?>"
                                                        data-duration="<?php echo sanitize($c['duration']); ?>"
                                                        data-fee="<?php echo $c['fee']; ?>"
                                                        data-desc="<?php echo sanitize($c['description']); ?>"
                                                        data-mods="<?php echo sanitize($c['modules']); ?>"
                                                        data-inst="<?php echo intval($c['instructor_id']); ?>"
                                                        data-status="<?php echo $c['status']; ?>">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('WARNING: Are you sure you want to delete this course profile?');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                                                    <button type="submit" name="delete_course" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
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
    
    <!-- Add/Edit course form side column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark" id="form-title-card"><i class="fa fa-plus-circle text-primary me-2"></i>Add Course</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0 ps-3 small">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form id="courseForm" action="" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="course_id" name="course_id" value="">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Course Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Diploma in IT">
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label fw-semibold">Course Code</label>
                        <input type="text" class="form-control" id="code" name="code" required placeholder="e.g. DIT-01">
                    </div>

                    <div class="mb-3">
                        <label for="duration" class="form-label fw-semibold">Duration</label>
                        <input type="text" class="form-control" id="duration" name="duration" required placeholder="e.g. 1 Year (2 Semesters)">
                    </div>

                    <div class="mb-3">
                        <label for="fee" class="form-label fw-semibold">Total Fee (Rs.)</label>
                        <input type="number" step="0.01" class="form-control" id="fee" name="fee" required placeholder="e.g. 24000">
                    </div>

                    <div class="mb-3">
                        <label for="instructor_id" class="form-label fw-semibold">Instructor</label>
                        <select class="form-select" id="instructor_id" name="instructor_id">
                            <option value="">-- Assign Instructor --</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo sanitize($t['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="status-group" style="display: none;">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Provide general details..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="modules" class="form-label fw-semibold">Modules List</label>
                        <textarea class="form-control" id="modules" name="modules" rows="4" placeholder="List modules or sem structure..."></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" id="btn-submit-form" name="add_course" class="btn btn-primary rounded-pill py-2 text-white">
                            <i class="fa fa-save me-1"></i> Save Course
                        </button>
                        <button type="button" id="btn-cancel-edit" class="btn btn-outline-secondary rounded-pill py-2" style="display: none;">
                            Cancel Edit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Click on Edit course profile
    $('.btn-edit-course').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var code = $(this).data('code');
        var duration = $(this).data('duration');
        var fee = $(this).data('fee');
        var desc = $(this).data('desc');
        var mods = $(this).data('mods');
        var inst = $(this).data('inst');
        var status = $(this).data('status');
        
        // Change Form properties
        $('#form-title-card').html('<i class="fa fa-edit text-primary me-2"></i>Edit Course Profile');
        $('#course_id').val(id);
        $('#name').val(name);
        $('#code').val(code);
        $('#duration').val(duration);
        $('#fee').val(fee);
        $('#description').val(desc);
        $('#modules').val(mods);
        $('#instructor_id').val(inst);
        $('#status').val(status);
        
        $('#status-group').show();
        $('#btn-cancel-edit').show();
        
        $('#btn-submit-form').attr('name', 'edit_course');
    });

    // Cancel edit
    $('#btn-cancel-edit').on('click', function() {
        $('#form-title-card').html('<i class="fa fa-plus-circle text-primary me-2"></i>Add Course');
        $('#course_id').val('');
        $('#courseForm')[0].reset();
        
        $('#status-group').hide();
        $('#btn-cancel-edit').hide();
        
        $('#btn-submit-form').attr('name', 'add_course');
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
