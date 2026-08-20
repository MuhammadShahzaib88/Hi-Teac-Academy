<?php
// authentication/login.php
// Student Login Page

require_once dirname(__DIR__) . '/config/config.php';

// Redirect if already logged in
if (is_student_logged_in()) {
    header('Location: ' . BASE_URL . 'student/dashboard.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    // Sanitize inputs
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email)) $errors[] = "Email address is required.";
    if (empty($password)) $errors[] = "Password is required.";
    
    if (empty($errors)) {
        try {
            // Find student by email
            $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch();
            
            if ($student && password_verify($password, $student['password'])) {
                if ($student['status'] !== 'Active') {
                    $errors[] = "Your account is currently deactivated. Please contact administration.";
                } else {
                    // Start student session
                    $_SESSION['student_logged_in'] = true;
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_email'] = $student['email'];
                    $_SESSION['student_first_name'] = $student['first_name'];
                    $_SESSION['student_last_name'] = $student['last_name'];
                    
                    set_flash_message('success', 'Welcome back, ' . $student['first_name'] . '!');
                    header('Location: ' . BASE_URL . 'student/dashboard.php');
                    exit;
                }
            } else {
                $errors[] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}

$page_title = "Student Login";
include ROOT_PATH . 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7" data-aos="fade-up">
            <div class="card border-0 shadow-lg rounded-3">
                <div class="card-header text-center py-4 text-white" style="background: linear-gradient(135deg, var(--navy-blue), var(--light-blue)); border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <i class="fa fa-graduation-cap fa-3x mb-3"></i>
                    <h3 class="fw-bold mb-0">Student Login</h3>
                    <p class="mb-0 text-white-50">Sign in to manage your studies & admissions</p>
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

                    <form action="<?php echo BASE_URL; ?>authentication/login.php" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize($email); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <label for="password" class="form-label mb-0">Password</label>
                                <a href="<?php echo BASE_URL; ?>authentication/forgot.php" class="small text-decoration-none">Forgot Password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="fa fa-sign-in-alt me-2"></i> Log In
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account? <a href="<?php echo BASE_URL; ?>authentication/register.php" class="text-primary fw-semibold">Register Here</a></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . 'includes/footer.php'; ?>
