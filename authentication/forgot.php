<?php
// authentication/forgot.php
// Student Password Recovery

require_once dirname(__DIR__) . '/config/config.php';

// Redirect if already logged in
if (is_student_logged_in()) {
    header('Location: ' . BASE_URL . 'student/dashboard.php');
    exit;
}

$errors = [];
$success = false;
$email = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($new_password) || strlen($new_password) < 6) $errors[] = "New password must be at least 6 characters.";
    if ($new_password !== $confirm_password) $errors[] = "Passwords do not match.";
    
    if (empty($errors)) {
        try {
            // Find student with matching email and phone
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? AND phone = ?");
            $stmt->execute([$email, $phone]);
            $student = $stmt->fetch();
            
            if ($student) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE students SET password = ? WHERE id = ?");
                $update_stmt->execute([$hashed_password, $student['id']]);
                
                $success = true;
                set_flash_message('success', 'Password reset successfully! You can now log in.');
                header('Location: ' . BASE_URL . 'authentication/login.php');
                exit;
            } else {
                $errors[] = "No student record matches the provided email and phone number.";
            }
        } catch (PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}

$page_title = "Forgot Password";
include ROOT_PATH . 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7" data-aos="fade-up">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-header text-center py-4 text-white" style="background: linear-gradient(135deg, var(--navy-blue), var(--light-blue)); border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <i class="fa fa-key fa-3x mb-3"></i>
                    <h3 class="fw-bold mb-0">Reset Password</h3>
                    <p class="mb-0 text-white-50">Recover your account password securely</p>
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

                    <form action="<?php echo BASE_URL; ?>authentication/forgot.php" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Registered Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize($email); ?>" required placeholder="e.g. name@example.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Registered Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-phone text-muted"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo sanitize($phone); ?>" required placeholder="e.g. 03001234567">
                            </div>
                            <div class="form-text">Used for identity validation.</div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                                <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="fa fa-check-circle me-2"></i> Reset Password
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Remembered credentials? <a href="<?php echo BASE_URL; ?>authentication/login.php" class="text-primary fw-semibold">Login Here</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . 'includes/footer.php'; ?>
