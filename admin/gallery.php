<?php
// admin/gallery.php
// Administrator Gallery Media Manager Panel

$page_title = "Manage Gallery";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

$errors = [];
$success = false;

// 1. Process Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $title = sanitize($_POST['title'] ?? '');
    $category = sanitize($_POST['category'] ?? 'Campus');
    
    if (empty($title)) $errors[] = "Title is required.";
    if (!isset($_FILES['gallery_file']) || $_FILES['gallery_file']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Please select an image file to upload.";
    }
    
    if (empty($errors)) {
        $file = $_FILES['gallery_file'];
        $allowed_exts = ['jpg', 'jpeg', 'png'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_exts)) {
            $errors[] = "Invalid image extension. Only JPG, JPEG, and PNG are allowed.";
        }
        if ($file['size'] > 3 * 1024 * 1024) {
            $errors[] = "Image size cannot exceed 3MB.";
        }
        
        if (empty($errors)) {
            $upload_dir = ROOT_PATH . 'uploads/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = 'gallery_' . time() . '.' . $file_ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
                $image_path = 'uploads/gallery/' . $new_filename;
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_path) VALUES (?, ?, ?)");
                    $stmt->execute([$title, $category, $image_path]);
                    set_flash_message('success', "Image published to gallery.");
                    header('Location: ' . BASE_URL . 'admin/gallery.php');
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Failed to record image details in database.";
                }
            } else {
                $errors[] = "Failed to upload image file.";
            }
        }
    }
}

// 2. Process Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $image_id = intval($_POST['image_id'] ?? 0);
    
    try {
        // Fetch path to delete from disk
        $path_stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
        $path_stmt->execute([$image_id]);
        $image_path = $path_stmt->fetchColumn();
        
        if (!empty($image_path) && file_exists(ROOT_PATH . $image_path)) {
            unlink(ROOT_PATH . $image_path);
        }
        
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$image_id]);
        
        set_flash_message('success', "Image removed from gallery.");
        header('Location: ' . BASE_URL . 'admin/gallery.php');
        exit;
    } catch (PDOException $e) {
        set_flash_message('danger', "Failed to delete image.");
    }
}

// Fetch all gallery items
try {
    $gallery = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $gallery = [];
}

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">
    <!-- List of Gallery Items -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-images text-primary me-2"></i>Published Gallery Photos</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($gallery)): ?>
                    <p class="text-muted text-center py-5">No gallery images published.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($gallery as $item): 
                            $img = !empty($item['image_path']) && file_exists(ROOT_PATH . $item['image_path']) 
                                ? BASE_URL . $item['image_path'] 
                                : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=800&auto=format&fit=crop&q=60';
                        ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card border h-100 overflow-hidden shadow-sm">
                                    <img src="<?php echo $img; ?>" alt="<?php echo sanitize($item['title']); ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-3">
                                        <span class="badge bg-secondary mb-1 small"><?php echo sanitize($item['category']); ?></span>
                                        <h6 class="fw-bold text-dark mb-2"><?php echo sanitize($item['title']); ?></h6>
                                        <form action="" method="POST" onsubmit="return confirm('Delete this image from gallery?');">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="image_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" name="delete_image" class="btn btn-sm btn-outline-danger w-100"><i class="fa fa-trash me-1"></i> Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Upload Panel Side Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-plus-circle text-primary me-2"></i>Publish Photo</h5>
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

                <form action="" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Photo Title</label>
                        <input type="text" class="form-control" id="title" name="title" required placeholder="e.g. Technical Computer Laboratory A">
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label fw-semibold">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="Labs">Labs</option>
                            <option value="Classrooms">Classrooms</option>
                            <option value="Events">Events</option>
                            <option value="Campus">Campus</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="gallery_file" class="form-label fw-semibold">Select File (JPG/PNG, Max 3MB)</label>
                        <input type="file" class="form-control" id="gallery_file" name="gallery_file" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="upload_image" class="btn btn-primary rounded-pill py-2 text-white">
                            <i class="fa fa-upload me-1"></i> Upload Image
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
