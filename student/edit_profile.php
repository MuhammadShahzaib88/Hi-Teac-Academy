<?php
// student/edit_profile.php
// Student Edit Profile & Avatar Upload

$page_title = "Edit Profile Info";
require_once dirname(__DIR__) . '/includes/student_header.php';

$student_id = $_SESSION['student_id'];
$errors = [];
$success = false;

// Fetch current details
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $gender = sanitize($_POST['gender'] ?? '');
    $dob = sanitize($_POST['dob'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    
    $profile_pic_path = $student['profile_pic'];
    
    // Process profile picture upload
    if (empty($errors) && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_pic'];
        
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_exts)) {
            $errors[] = "Invalid image extension. Only JPG, JPEG, and PNG are allowed.";
        }
        
        if ($file_size > 2 * 1024 * 1024) { // 2MB
            $errors[] = "Image size cannot exceed 2MB.";
        }
        
        if (empty($errors)) {
            // Ensure uploads directory exists
            $upload_dir = ROOT_PATH . 'uploads/profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Delete old profile picture if exists
            if (!empty($student['profile_pic']) && file_exists(ROOT_PATH . $student['profile_pic'])) {
                unlink(ROOT_PATH . $student['profile_pic']);
            }
            
            // Generate unique filename
            $new_filename = 'avatar_' . $student_id . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                $profile_pic_path = 'uploads/profiles/' . $new_filename;
            } else {
                $errors[] = "Failed to upload profile photo.";
            }
        }
    }
    
    // Update DB
    if (empty($errors)) {
        try {
            $update_stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, phone = ?, gender = ?, dob = ?, address = ?, profile_pic = ? WHERE id = ?");
            $update_stmt->execute([$first_name, $last_name, $phone, $gender, $dob, $address, $profile_pic_path, $student_id]);
            
            // Update session vars
            $_SESSION['student_first_name'] = $first_name;
            $_SESSION['student_last_name'] = $last_name;
            
            set_flash_message('success', 'Profile information updated successfully!');
            header('Location: ' . BASE_URL . 'student/profile.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "System error updating profile details. Please try again.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-user-edit text-primary me-2"></i>Edit Profile Information</h5>
            </div>
            
            <div class="card-body p-4 p-md-5">
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

                <form action="<?php echo BASE_URL; ?>student/edit_profile.php" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="row g-4 mb-4">
                        <!-- Profile Pic Preview and Input -->
                        <div class="col-md-12 text-center mb-3">
                            <img src="<?php echo $student_avatar; ?>" alt="Preview" id="avatar-preview" class="rounded-circle border border-3 border-primary mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            <div class="mx-auto" style="max-width: 300px;">
                                <label for="profile_pic" class="form-label small text-muted">Upload Profile Picture (JPG/PNG, Max 2MB)</label>
                                <input type="file" class="form-control form-control-sm image-preview-input" id="profile_pic" name="profile_pic" data-preview-target="#avatar-preview">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo sanitize($student['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo sanitize($student['last_name']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo sanitize($student['phone']); ?>" placeholder="e.g. 03001234567">
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label fw-semibold">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="" <?php echo is_null($student['gender']) || $student['gender'] === '' ? 'selected' : ''; ?>>Select Gender</option>
                                <option value="Male" <?php echo $student['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $student['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $student['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo sanitize($student['dob'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-12">
                            <label for="address" class="form-label fw-semibold">Postal Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your full mailing address..."><?php echo sanitize($student['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?php echo BASE_URL; ?>student/profile.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fa fa-save me-1"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
