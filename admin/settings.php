<?php
// admin/settings.php
// Administrator System Settings Manager

$page_title = "System Settings";
require_once dirname(__DIR__) . '/includes/admin_header.php';

$errors = [];

// 1. Process Settings Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $fields = [
        'site_name'        => sanitize($_POST['site_name']        ?? ''),
        'contact_email'    => sanitize($_POST['contact_email']    ?? ''),
        'contact_phone'    => sanitize($_POST['contact_phone']    ?? ''),
        'address'          => sanitize($_POST['address']          ?? ''),
        'facebook_url'     => sanitize($_POST['facebook_url']     ?? ''),
        'twitter_url'      => sanitize($_POST['twitter_url']      ?? ''),
        'instagram_url'    => sanitize($_POST['instagram_url']    ?? ''),
        'youtube_url'      => sanitize($_POST['youtube_url']      ?? ''),
        'admission_status' => sanitize($_POST['admission_status'] ?? 'Open'),
    ];

    if (empty($fields['site_name']))     $errors[] = "Site name is required.";
    if (empty($fields['contact_email'])) $errors[] = "Contact email is required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            foreach ($fields as $key => $value) {
                $stmt->execute([$value, $key]);
            }
            set_flash_message('success', 'System settings updated successfully.');
            header('Location: ' . BASE_URL . 'admin/settings.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// 2. Process Admin Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    $current_pw  = $_POST['current_password']  ?? '';
    $new_pw      = $_POST['new_password']      ?? '';
    $confirm_pw  = $_POST['confirm_password']  ?? '';

    if (empty($current_pw)) $errors[] = "Current password is required.";
    if (strlen($new_pw) < 8) $errors[] = "New password must be at least 8 characters.";
    if ($new_pw !== $confirm_pw) $errors[] = "New passwords do not match.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin_row = $stmt->fetch();

            if (!$admin_row || !password_verify($current_pw, $admin_row['password'])) {
                $errors[] = "Current password is incorrect.";
            } else {
                $hashed = password_hash($new_pw, PASSWORD_BCRYPT);
                $upd = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $upd->execute([$hashed, $_SESSION['admin_id']]);
                set_flash_message('success', 'Admin password changed successfully.');
                header('Location: ' . BASE_URL . 'admin/settings.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// 3. Fetch current settings for display
try {
    $rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    $settings = [];
}

$s = function($key, $default = '') use ($settings) {
    return htmlspecialchars($settings[$key] ?? $default, ENT_QUOTES, 'UTF-8');
};
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0 ps-3 small">
            <?php foreach ($errors as $err): ?>
                <li><?php echo $err; ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- Site Information Settings -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-cogs text-primary me-2"></i>Site & Contact Settings</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="row g-3">
                        <!-- Site Name -->
                        <div class="col-md-6">
                            <label for="site_name" class="form-label fw-semibold">Academy / Site Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="site_name" name="site_name"
                                   value="<?php echo $s('site_name', 'Hi Teac Academy'); ?>" required>
                        </div>

                        <!-- Admission Status -->
                        <div class="col-md-6">
                            <label for="admission_status" class="form-label fw-semibold">Admission Status</label>
                            <select class="form-select" id="admission_status" name="admission_status">
                                <option value="Open"   <?php echo ($settings['admission_status'] ?? 'Open') === 'Open'   ? 'selected' : ''; ?>>Open — Accepting Applications</option>
                                <option value="Closed" <?php echo ($settings['admission_status'] ?? '') === 'Closed' ? 'selected' : ''; ?>>Closed — Not Accepting</option>
                            </select>
                        </div>

                        <!-- Contact Email -->
                        <div class="col-md-6">
                            <label for="contact_email" class="form-label fw-semibold">Contact Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                                <input type="email" class="form-control" id="contact_email" name="contact_email"
                                       value="<?php echo $s('contact_email'); ?>" required>
                            </div>
                        </div>

                        <!-- Contact Phone -->
                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label fw-semibold">Contact Phone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-phone text-muted"></i></span>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                                       value="<?php echo $s('contact_phone'); ?>">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Physical Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"
                                      placeholder="Full postal / physical address..."><?php echo $s('address'); ?></textarea>
                        </div>

                        <hr class="my-2">
                        <h6 class="fw-bold text-muted text-uppercase small">Social Media Links</h6>

                        <!-- Facebook -->
                        <div class="col-md-6">
                            <label for="facebook_url" class="form-label fw-semibold"><i class="fab fa-facebook text-primary me-1"></i> Facebook URL</label>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url"
                                   value="<?php echo $s('facebook_url'); ?>" placeholder="https://facebook.com/...">
                        </div>

                        <!-- Twitter -->
                        <div class="col-md-6">
                            <label for="twitter_url" class="form-label fw-semibold"><i class="fab fa-twitter text-info me-1"></i> Twitter / X URL</label>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                                   value="<?php echo $s('twitter_url'); ?>" placeholder="https://twitter.com/...">
                        </div>

                        <!-- Instagram -->
                        <div class="col-md-6">
                            <label for="instagram_url" class="form-label fw-semibold"><i class="fab fa-instagram text-danger me-1"></i> Instagram URL</label>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url"
                                   value="<?php echo $s('instagram_url'); ?>" placeholder="https://instagram.com/...">
                        </div>

                        <!-- YouTube -->
                        <div class="col-md-6">
                            <label for="youtube_url" class="form-label fw-semibold"><i class="fab fa-youtube text-danger me-1"></i> YouTube URL</label>
                            <input type="url" class="form-control" id="youtube_url" name="youtube_url"
                                   value="<?php echo $s('youtube_url'); ?>" placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="save_settings" class="btn btn-primary rounded-pill px-5 py-2">
                            <i class="fa fa-save me-1"></i> Save All Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar: Change Admin Password & Info -->
    <div class="col-lg-4">
        <!-- Change Password Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-key text-warning me-2"></i>Change Admin Password</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold small">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold small">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password"
                               minlength="8" required placeholder="Minimum 8 characters">
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold small">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="change_password" class="btn btn-warning rounded-pill py-2 fw-semibold">
                            <i class="fa fa-shield-alt me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Info Card -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-info-circle text-info me-2"></i>System Information</h5>
            </div>
            <div class="card-body p-4">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-muted small">PHP Version</td>
                            <td class="fw-semibold small"><?php echo phpversion(); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Server Software</td>
                            <td class="fw-semibold small"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Database</td>
                            <td class="fw-semibold small">MySQL / MariaDB (PDO)</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">System Date</td>
                            <td class="fw-semibold small"><?php echo date('d M Y, H:i A'); ?></td>
                        </tr>
                        <?php
                        try {
                            $db_ver = $pdo->query("SELECT VERSION()")->fetchColumn();
                            echo '<tr><td class="text-muted small">DB Version</td><td class="fw-semibold small">' . htmlspecialchars($db_ver) . '</td></tr>';
                        } catch (Exception $e) {}
                        ?>
                    </tbody>
                </table>

                <hr>
                <p class="text-muted small mb-0">
                    <i class="fa fa-graduation-cap me-1 text-primary"></i>
                    <strong>Hi Teac Academy</strong> — Academy Management System v1.0
                </p>
            </div>
        </div>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
