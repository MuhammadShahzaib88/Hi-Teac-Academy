<?php
// student/change_password.php
// Student Password Change form

$page_title = "Change Password";
require_once dirname(__DIR__) . '/includes/student_header.php';

$student_id = $_SESSION['student_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($old_password)) $errors[] = "Current password is required.";
    if (empty($new_password) || strlen($new_password) < 6) $errors[] = "New password must be at least 6 characters.";
    if ($new_password !== $confirm_password) $errors[] = "New passwords do not match.";
    
    if (empty($errors)) {
        try {
            // Get student password from DB
            $stmt = $pdo->prepare("SELECT password FROM students WHERE id = ?");
            $stmt->execute([$student_id]);
            $student_pwd = $stmt->fetchColumn();
            
            if (password_verify($old_password, $student_pwd)) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE students SET password = ? WHERE id = ?");
                $update_stmt->execute([$new_hash, $student_id]);
                
                set_flash_message('success', 'Your password has been changed successfully!');
                header('Location: ' . BASE_URL . 'student/profile.php');
                exit;
            } else {
                $errors[] = "Incorrect current password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Failed to update password due to a system error.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-key text-primary me-2"></i>Change Password</h5>
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

                <form action="<?php echo BASE_URL; ?>student/change_password.php" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="mb-3">
                        <label for="old_password" class="form-label fw-semibold">Current Password</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                        <div class="form-text">Minimum length of 6 characters.</div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?php echo BASE_URL; ?>student/profile.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fa fa-save me-1"></i> Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
