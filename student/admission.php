<?php
// student/admission.php
// Online Admission Form for Student

$page_title = "Apply Admission";
require_once dirname(__DIR__) . '/includes/student_header.php';

$student_id = $_SESSION['student_id'];
$errors = [];
$success = false;

// 1. Fetch available active courses for selection
$courses_stmt = $pdo->query("SELECT * FROM courses WHERE status = 'Active'");
$courses = $courses_stmt->fetchAll();

// Pre-fill course if passed in URL
$prefilled_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    $course_id = intval($_POST['course_id'] ?? 0);
    
    if ($course_id <= 0) {
        $errors[] = "Please select a valid course.";
    }
    
    // Check if the student has already applied for this specific course
    if (empty($errors)) {
        $check_stmt = $pdo->prepare("SELECT id, status FROM admissions WHERE student_id = ? AND course_id = ?");
        $check_stmt->execute([$student_id, $course_id]);
        $existing_application = $check_stmt->fetch();
        
        if ($existing_application) {
            $errors[] = "You have already submitted an admission application for this course. Current status: " . $existing_application['status'] . ".";
        }
    }
    
    // Uploads verification
    if (empty($errors)) {
        if (!isset($_FILES['matric_cert']) || $_FILES['matric_cert']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "Scanned copy of Matriculation Marksheet/Certificate is required.";
        }
        if (!isset($_FILES['cnic_copy']) || $_FILES['cnic_copy']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "Scanned copy of CNIC or B-Form is required.";
        }
    }
    
    if (empty($errors)) {
        $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
        $upload_dir = ROOT_PATH . 'uploads/documents/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $matric_path = '';
        $cnic_path = '';
        
        // 1. Process Matric Certificate
        $matric_file = $_FILES['matric_cert'];
        $matric_ext = strtolower(pathinfo($matric_file['name'], PATHINFO_EXTENSION));
        if (!in_array($matric_ext, $allowed_exts)) {
            $errors[] = "Matric Cert file format not allowed. Only JPG, JPEG, PNG, and PDF are allowed.";
        }
        if ($matric_file['size'] > 3 * 1024 * 1024) { // 3MB
            $errors[] = "Matric Certificate file size must be less than 3MB.";
        }
        
        // 2. Process CNIC
        $cnic_file = $_FILES['cnic_copy'];
        $cnic_ext = strtolower(pathinfo($cnic_file['name'], PATHINFO_EXTENSION));
        if (!in_array($cnic_ext, $allowed_exts)) {
            $errors[] = "CNIC/B-Form file format not allowed. Only JPG, JPEG, PNG, and PDF are allowed.";
        }
        if ($cnic_file['size'] > 3 * 1024 * 1024) { // 3MB
            $errors[] = "CNIC/B-Form file size must be less than 3MB.";
        }
        
        if (empty($errors)) {
            $matric_name = 'matric_' . $student_id . '_' . time() . '.' . $matric_ext;
            $cnic_name = 'cnic_' . $student_id . '_' . time() . '.' . $cnic_ext;
            
            if (move_uploaded_file($matric_file['tmp_name'], $upload_dir . $matric_name) &&
                move_uploaded_file($cnic_file['tmp_name'], $upload_dir . $cnic_name)) {
                $matric_path = 'uploads/documents/' . $matric_name;
                $cnic_path = 'uploads/documents/' . $cnic_name;
            } else {
                $errors[] = "Failed to save uploaded documents on server.";
            }
        }
    }
    
    // Save admission in database
    if (empty($errors)) {
        try {
            $insert_stmt = $pdo->prepare("INSERT INTO admissions (student_id, course_id, status, matric_certificate, cnic_copy) VALUES (?, ?, 'Pending', ?, ?)");
            $insert_stmt->execute([$student_id, $course_id, $matric_path, $cnic_path]);
            
            // Get course name for notification
            $course_name_stmt = $pdo->prepare("SELECT name FROM courses WHERE id = ?");
            $course_name_stmt->execute([$course_id]);
            $course_name = $course_name_stmt->fetchColumn();
            
            // Set notification for admin (Admin ID = 1)
            $student_name = $_SESSION['student_first_name'] . ' ' . $_SESSION['student_last_name'];
            create_notification('Admin', 1, 'New Admission Submitted', "Student $student_name applied for $course_name.");
            
            set_flash_message('success', 'Your admission application has been submitted successfully! Please wait for administration review.');
            header('Location: ' . BASE_URL . 'student/admission_status.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "A system database error occurred. Details: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-file-invoice text-primary me-2"></i>Apply for Admission</h5>
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

                <form action="<?php echo BASE_URL; ?>student/admission.php" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label for="course_id" class="form-label fw-semibold">Select Course / Program <span class="text-danger">*</span></label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">-- Choose academic program --</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo ($prefilled_course_id == $c['id']) ? 'selected' : ''; ?>>
                                        <?php echo sanitize($c['name'] . ' (' . $c['code'] . ') - Fee: ' . format_currency($c['fee'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="matric_cert" class="form-label fw-semibold">Matric Marksheet / Certificate Scan <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="matric_cert" name="matric_cert" required>
                            <div class="form-text small">Accepted formats: JPG, PNG, PDF. Max size: 3MB.</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="cnic_copy" class="form-label fw-semibold">Student CNIC or B-Form Scan <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="cnic_copy" name="cnic_copy" required>
                            <div class="form-text small">Accepted formats: JPG, PNG, PDF. Max size: 3MB.</div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <div class="p-3 bg-light rounded border">
                                <h6 class="fw-bold text-dark"><i class="fa fa-info-circle text-primary me-2"></i>Important Instructions:</h6>
                                <p class="small text-muted mb-0">Please ensure uploaded documents are clear and legible. Rejection will occur if certificates or CNICs cannot be read by administrative verification personnel. The review process usually takes 24 to 48 hours.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4 mt-4">
                        <a href="<?php echo BASE_URL; ?>student/dashboard.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fa fa-paper-plane me-1"></i> Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
