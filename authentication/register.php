<?php
// authentication/register.php
// Student Registration Page

require_once dirname(__DIR__) . '/config/config.php';

// Redirect if already logged in
if (is_student_logged_in()) {
    header('Location: ' . BASE_URL . 'student/dashboard.php');
    exit;
}

$errors = [];
$first_name = $last_name = $email = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    // Sanitize inputs
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name  = sanitize($_POST['last_name'] ?? '');
    $email      = sanitize($_POST['email'] ?? '');
    $phone      = sanitize($_POST['phone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Input validations
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name))  $errors[] = "Last name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email address is required.";
    if (empty($password) || strlen($password) < 6) $errors[] = "Password must be at least 6 characters long.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "This email address is already registered.";
        }
    }
    
    // Insert if no errors
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, password, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $hashed_password, $phone]);
            
            set_flash_message('success', 'Registration successful! You can now log in.');
            header('Location: ' . BASE_URL . 'authentication/login.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Registration failed due to a system error. Please try again later.";
        }
    }
}

$page_title = "Student Registration";
include ROOT_PATH . 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8" data-aos="fade-up">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-header text-center py-4 text-white" style="background: linear-gradient(135deg, var(--navy-blue), var(--light-blue)); border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <i class="fa fa-user-plus fa-3x mb-3"></i>
                    <h3 class="fw-bold mb-0">Student Registration</h3>
                    <p class="mb-0 text-white-50">Create your account to apply for DIT & CIT courses</p>
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

                    <form action="<?php echo BASE_URL; ?>authentication/register.php" method="POST" class="needs-validation" novalidate>
                        <?php echo csrf_field(); ?>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-user text-muted"></i></span>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo sanitize($first_name); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-user text-muted"></i></span>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo sanitize($last_name); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize($email); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-phone text-muted"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo sanitize($phone); ?>" placeholder="e.g. 03001234567">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                                </div>
                                <div class="form-text">Min 6 characters.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="fa fa-user-plus me-2"></i> Register Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account? <a href="<?php echo BASE_URL; ?>authentication/login.php" class="text-primary fw-semibold">Login Here</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . 'includes/footer.php'; ?>
