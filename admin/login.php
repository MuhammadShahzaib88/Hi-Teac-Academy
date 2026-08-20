<?php
// admin/login.php
// Administrator Login Gateway

require_once dirname(__DIR__) . '/config/config.php';

// Redirect if already logged in as admin
if (is_admin_logged_in()) {
    header('Location: ' . BASE_URL . 'admin/dashboard.php');
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";
    
    if (empty($errors)) {
        try {
            // Find admin username
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Set Admin Sessions
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];
                
                set_flash_message('success', 'Welcome, Administrator!');
                header('Location: ' . BASE_URL . 'admin/dashboard.php');
                exit;
            } else {
                $errors[] = "Invalid administrator username or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "An error occurred. Please check database settings.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hi Teac Academy</title>
    
    <!-- CSS CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="text-center text-white mb-4">
                <h2><i class="fa fa-graduation-cap text-primary me-2"></i>Hi Teac Academy</h2>
                <span class="text-warning">Management Control Portal</span>
            </div>
            
            <div class="card border-0 shadow-lg rounded-3 bg-white">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold mb-4 text-center text-dark"><i class="fa fa-lock me-2 text-primary"></i>Admin Access</h4>
                    
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

                    <form action="<?php echo BASE_URL; ?>admin/login.php" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label small fw-semibold">Admin Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa fa-user text-muted"></i></span>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo sanitize($username); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label small fw-semibold">Admin Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa fa-key text-muted"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold">
                                <i class="fa fa-sign-in-alt me-1"></i> Authorize Portal
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none small text-muted"><i class="fa fa-arrow-left me-1"></i> Public Website</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
