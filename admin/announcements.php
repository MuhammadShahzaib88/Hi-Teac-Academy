<?php
// admin/announcements.php
// Administrator Notice Board Announcements Manager

$page_title = "Manage Announcements";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

$errors = [];
$success = false;

// 1. Process Add Announcement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_announcement'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $title = sanitize($_POST['title'] ?? '');
    $content = sanitize($_POST['content'] ?? '');
    $type = sanitize($_POST['type'] ?? 'General');
    
    if (empty($title)) $errors[] = "Title is required.";
    if (empty($content)) $errors[] = "Content is required.";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, content, type, status) VALUES (?, ?, ?, 'Active')");
            $stmt->execute([$title, $content, $type]);
            
            // Set notification for all students
            $students = $pdo->query("SELECT id FROM students WHERE status = 'Active'")->fetchAll();
            foreach ($students as $student) {
                create_notification('Student', $student['id'], 'New Notice Board announcement', "A new announcement has been posted: '$title'");
            }
            
            set_flash_message('success', "Notice Board announcement created successfully.");
            header('Location: ' . BASE_URL . 'admin/announcements.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database insertion error: " . $e->getMessage();
        }
    }
}

// 2. Process Edit Announcement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_announcement'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $id = intval($_POST['announcement_id'] ?? 0);
    $title = sanitize($_POST['title'] ?? '');
    $content = sanitize($_POST['content'] ?? '');
    $type = sanitize($_POST['type'] ?? 'General');
    $status = sanitize($_POST['status'] ?? 'Active');
    
    if (empty($title)) $errors[] = "Title is required.";
    if (empty($content)) $errors[] = "Content is required.";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ?, type = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $content, $type, $status, $id]);
            set_flash_message('success', "Notice Board announcement updated successfully.");
            header('Location: ' . BASE_URL . 'admin/announcements.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database update error: " . $e->getMessage();
        }
    }
}

// 3. Process Delete Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_announcement'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $id = intval($_POST['announcement_id'] ?? 0);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', "Announcement deleted successfully.");
        header('Location: ' . BASE_URL . 'admin/announcements.php');
        exit;
    } catch (PDOException $e) {
        $errors[] = "Failed to delete announcement: " . $e->getMessage();
    }
}

// Fetch all announcements
try {
    $announcements = $pdo->query("SELECT * FROM announcements ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $announcements = [];
}

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">
    <!-- List of Announcements -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-bullhorn text-primary me-2"></i>Active Announcements</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($announcements)): ?>
                    <p class="text-muted text-center py-5">No notices registered.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Content Preview</th>
                                    <th>Date Published</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($announcements as $announce): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary text-uppercase"><?php echo sanitize($announce['type']); ?></span>
                                        </td>
                                        <td><span class="fw-bold text-dark"><?php echo sanitize($announce['title']); ?></span></td>
                                        <td><span class="small text-muted"><?php echo sanitize(substr($announce['content'], 0, 40)); ?>...</span></td>
                                        <td><?php echo format_date($announce['created_at'], 'd M Y'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($announce['status'] === 'Active') ? 'success' : 'secondary'; ?> px-2 py-1 rounded-pill">
                                                <?php echo $announce['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-outline-primary rounded-circle btn-edit-announcement" 
                                                        data-id="<?php echo $announce['id']; ?>"
                                                        data-title="<?php echo sanitize($announce['title']); ?>"
                                                        data-content="<?php echo sanitize($announce['content']); ?>"
                                                        data-type="<?php echo $announce['type']; ?>"
                                                        data-status="<?php echo $announce['status']; ?>">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                
                                                <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('WARNING: Are you sure you want to delete this announcement?');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="announcement_id" value="<?php echo $announce['id']; ?>">
                                                    <button type="submit" name="delete_announcement" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa fa-trash"></i></button>
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
    
    <!-- Add/Edit form side column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark" id="form-title-ann"><i class="fa fa-plus-circle text-primary me-2"></i>Add Announcement</h5>
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

                <form id="annForm" action="" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="announcement_id" name="announcement_id" value="">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required placeholder="e.g. Midterm Examinations Schedule">
                    </div>
                    
                    <div class="mb-3">
                        <label for="type" class="form-label fw-semibold">Notice Category</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="General">General</option>
                            <option value="Exam">Exam</option>
                            <option value="Holiday">Holiday</option>
                                <option value="Admission">Admission</option>
                        </select>
                    </div>

                    <div class="mb-3" id="ann-status-group" style="display: none;">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-semibold">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="6" required placeholder="Type details about this notice..."></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" id="btn-submit-ann" name="add_announcement" class="btn btn-primary rounded-pill py-2 text-white">
                            <i class="fa fa-save me-1"></i> Publish Notice
                        </button>
                        <button type="button" id="btn-cancel-edit-ann" class="btn btn-outline-secondary rounded-pill py-2" style="display: none;">
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
    // Click on Edit
    $('.btn-edit-announcement').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        var content = $(this).data('content');
        var type = $(this).data('type');
        var status = $(this).data('status');
        
        $('#form-title-ann').html('<i class="fa fa-edit text-primary me-2"></i>Edit Announcement');
        $('#announcement_id').val(id);
        $('#title').val(title);
        $('#content').val(content);
        $('#type').val(type);
        $('#status').val(status);
        
        $('#ann-status-group').show();
        $('#btn-cancel-edit-ann').show();
        
        $('#btn-submit-ann').attr('name', 'edit_announcement');
    });

    // Cancel edit
    $('#btn-cancel-edit-ann').on('click', function() {
        $('#form-title-ann').html('<i class="fa fa-plus-circle text-primary me-2"></i>Add Announcement');
        $('#announcement_id').val('');
        $('#annForm')[0].reset();
        
        $('#ann-status-group').hide();
        $('#btn-cancel-edit-ann').hide();
        
        $('#btn-submit-ann').attr('name', 'add_announcement');
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
