<?php
// admin/teachers.php
// Administrator Instructor profiles management desk

$page_title = "Manage Instructors";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

$errors = [];
$success = false;

// 1. Process Add Instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $name = sanitize($_POST['name'] ?? '');
    $designation = sanitize($_POST['designation'] ?? '');
    $specialization = sanitize($_POST['specialization'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($designation)) $errors[] = "Designation is required.";
    
    $photo_path = '';
    
    // Photo upload check
    if (empty($errors) && isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_exts)) {
            $errors[] = "Invalid photo format. Only JPG, JPEG, and PNG are allowed.";
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "Photo size cannot exceed 2MB.";
        }
        
        if (empty($errors)) {
            $upload_dir = ROOT_PATH . 'uploads/teachers/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = 'teacher_' . time() . '.' . $file_ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                $photo_path = 'uploads/teachers/' . $new_filename;
            } else {
                $errors[] = "Failed to upload instructor photo.";
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO teachers (name, designation, specialization, email, phone, photo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $designation, $specialization, $email, $phone, $photo_path]);
            set_flash_message('success', "Instructor profile created successfully.");
            header('Location: ' . BASE_URL . 'admin/teachers.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database insertion error: " . $e->getMessage();
        }
    }
}

// 2. Process Edit Instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_teacher'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $teacher_id = intval($_POST['teacher_id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $designation = sanitize($_POST['designation'] ?? '');
    $specialization = sanitize($_POST['specialization'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($designation)) $errors[] = "Designation is required.";
    
    // Get existing record to check photo
    $t_stmt = $pdo->prepare("SELECT photo FROM teachers WHERE id = ?");
    $t_stmt->execute([$teacher_id]);
    $existing_photo = $t_stmt->fetchColumn();
    $photo_path = $existing_photo;
    
    // Photo edit upload check
    if (empty($errors) && isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_exts)) {
            $errors[] = "Invalid photo format. Only JPG, JPEG, and PNG are allowed.";
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = "Photo size cannot exceed 2MB.";
        }
        
        if (empty($errors)) {
            $upload_dir = ROOT_PATH . 'uploads/teachers/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Delete old file
            if (!empty($existing_photo) && file_exists(ROOT_PATH . $existing_photo)) {
                unlink(ROOT_PATH . $existing_photo);
            }
            
            $new_filename = 'teacher_' . time() . '.' . $file_ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                $photo_path = 'uploads/teachers/' . $new_filename;
            } else {
                $errors[] = "Failed to upload instructor photo.";
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE teachers SET name = ?, designation = ?, specialization = ?, email = ?, phone = ?, photo = ? WHERE id = ?");
            $stmt->execute([$name, $designation, $specialization, $email, $phone, $photo_path, $teacher_id]);
            set_flash_message('success', "Instructor profile updated successfully.");
            header('Location: ' . BASE_URL . 'admin/teachers.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database update error: " . $e->getMessage();
        }
    }
}

// 3. Process Delete Instructor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_teacher'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $teacher_id = intval($_POST['teacher_id'] ?? 0);
    
    try {
        // Delete photo
        $pic_stmt = $pdo->prepare("SELECT photo FROM teachers WHERE id = ?");
        $pic_stmt->execute([$teacher_id]);
        $pic = $pic_stmt->fetchColumn();
        if (!empty($pic) && file_exists(ROOT_PATH . $pic)) {
            unlink(ROOT_PATH . $pic);
        }
        
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt->execute([$teacher_id]);
        set_flash_message('success', "Instructor profile deleted successfully.");
        header('Location: ' . BASE_URL . 'admin/teachers.php');
        exit;
    } catch (PDOException $e) {
        set_flash_message('danger', "Failed to delete instructor. Make sure they are not linked to active courses.");
    }
}

// Fetch all instructors
try {
    $teachers = $pdo->query("SELECT * FROM teachers ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
}

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">
    <!-- List of Instructors -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-user-tie text-primary me-2"></i>Roster of Instructors</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($teachers)): ?>
                    <p class="text-muted text-center py-5">No instructors registered.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Specialization</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers as $t): 
                                    $photo = !empty($t['photo']) && file_exists(ROOT_PATH . $t['photo']) 
                                        ? BASE_URL . $t['photo'] 
                                        : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
                                ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $photo; ?>" alt="Teacher Photo" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid var(--primary-color);">
                                        </td>
                                        <td><span class="fw-bold text-dark"><?php echo sanitize($t['name']); ?></span></td>
                                        <td><?php echo sanitize($t['designation']); ?></td>
                                        <td><?php echo sanitize($t['specialization']); ?></td>
                                        <td>
                                            <small class="d-block text-muted"><i class="fa fa-envelope me-1"></i> <?php echo sanitize($t['email'] ?? 'N/A'); ?></small>
                                            <small class="d-block text-muted"><i class="fa fa-phone me-1"></i> <?php echo sanitize($t['phone'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-outline-primary rounded-circle btn-edit-teacher" 
                                                        data-id="<?php echo $t['id']; ?>"
                                                        data-name="<?php echo sanitize($t['name']); ?>"
                                                        data-desig="<?php echo sanitize($t['designation']); ?>"
                                                        data-spec="<?php echo sanitize($t['specialization']); ?>"
                                                        data-email="<?php echo sanitize($t['email']); ?>"
                                                        data-phone="<?php echo sanitize($t['phone']); ?>"
                                                        data-photo="<?php echo $photo; ?>">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('WARNING: Are you sure you want to delete this instructor?');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="teacher_id" value="<?php echo $t['id']; ?>">
                                                    <button type="submit" name="delete_teacher" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa fa-trash"></i></button>
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
    
    <!-- Form column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark" id="form-title-teacher"><i class="fa fa-plus-circle text-primary me-2"></i>Add Instructor</h5>
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

                <form id="teacherForm" action="" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="teacher_id" name="teacher_id" value="">
                    
                    <div class="mb-3 text-center" id="photo-preview-container" style="display: none;">
                        <img src="" id="photo-preview" alt="Preview" class="rounded-circle border border-2 border-primary" style="width: 80px; height: 80px; object-fit: cover;">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Instructor Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Engr. Kashif Khan">
                    </div>
                    
                    <div class="mb-3">
                        <label for="designation" class="form-label fw-semibold">Designation</label>
                        <input type="text" class="form-control" id="designation" name="designation" required placeholder="e.g. Senior IT Lecturer">
                    </div>

                    <div class="mb-3">
                        <label for="specialization" class="form-label fw-semibold">Specialization</label>
                        <input type="text" class="form-control" id="specialization" name="specialization" placeholder="e.g. Web Dev / DBMS">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="e.g. kashif@example.com">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g. 03211234567">
                    </div>

                    <div class="mb-4">
                        <label for="photo" class="form-label fw-semibold">Profile Photo (JPG/PNG, Max 2MB)</label>
                        <input type="file" class="form-control" id="photo" name="photo">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" id="btn-submit-teacher" name="add_teacher" class="btn btn-primary rounded-pill py-2 text-white">
                            <i class="fa fa-save me-1"></i> Save Instructor
                        </button>
                        <button type="button" id="btn-cancel-edit-teacher" class="btn btn-outline-secondary rounded-pill py-2" style="display: none;">
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
    // Edit trigger
    $('.btn-edit-teacher').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var desig = $(this).data('desig');
        var spec = $(this).data('spec');
        var email = $(this).data('email');
        var phone = $(this).data('phone');
        var photo = $(this).data('photo');
        
        $('#form-title-teacher').html('<i class="fa fa-edit text-primary me-2"></i>Edit Instructor Profile');
        $('#teacher_id').val(id);
        $('#name').val(name);
        $('#designation').val(desig);
        $('#specialization').val(spec);
        $('#email').val(email);
        $('#phone').val(phone);
        
        $('#photo-preview').attr('src', photo);
        $('#photo-preview-container').show();
        $('#btn-cancel-edit-teacher').show();
        
        $('#btn-submit-teacher').attr('name', 'edit_teacher');
    });

    // Cancel edit
    $('#btn-cancel-edit-teacher').on('click', function() {
        $('#form-title-teacher').html('<i class="fa fa-plus-circle text-primary me-2"></i>Add Instructor');
        $('#teacher_id').val('');
        $('#teacherForm')[0].reset();
        
        $('#photo-preview-container').hide();
        $('#btn-cancel-edit-teacher').hide();
        
        $('#btn-submit-teacher').attr('name', 'add_teacher');
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
