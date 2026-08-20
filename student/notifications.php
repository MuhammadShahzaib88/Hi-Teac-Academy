<?php
// student/notifications.php
// Student Alerts and Notification Feed

$page_title = "Notifications";
require_once dirname(__DIR__) . '/includes/student_header.php';

$student_id = $_SESSION['student_id'];

// Process mark all as read
if (isset($_GET['mark_read'])) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_type = 'Student' AND user_id = ?");
        $stmt->execute([$student_id]);
        set_flash_message('success', 'All notifications marked as read.');
        header('Location: ' . BASE_URL . 'student/notifications.php');
        exit;
    } catch (PDOException $e) {
        set_flash_message('danger', 'Failed to update notifications.');
    }
}

// Process delete notifications
if (isset($_GET['delete_all'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_type = 'Student' AND user_id = ?");
        $stmt->execute([$student_id]);
        set_flash_message('success', 'Notifications cleared.');
        header('Location: ' . BASE_URL . 'student/notifications.php');
        exit;
    } catch (PDOException $e) {
        set_flash_message('danger', 'Failed to clear notifications.');
    }
}

// Fetch notifications
try {
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_type = 'Student' AND user_id = ? ORDER BY id DESC");
    $stmt->execute([$student_id]);
    $notifications = $stmt->fetchAll();
} catch (PDOException $e) {
    $notifications = [];
}
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-bell text-primary me-2"></i>My Notification Feed</h5>
                <div>
                    <?php if (!empty($notifications)): ?>
                        <a href="?mark_read=1" class="btn btn-sm btn-outline-primary rounded-pill me-2"><i class="fa fa-check-double me-1"></i> Mark All Read</a>
                        <a href="?delete_all=1" class="btn btn-sm btn-outline-danger rounded-pill"><i class="fa fa-trash me-1"></i> Clear Feed</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-5">
                        <i class="fa fa-bell-slash text-muted fa-4x mb-3"></i>
                        <p class="text-muted mb-0">Your notification feed is currently empty.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                        <?php foreach ($notifications as $notif): 
                            $bg_class = ($notif['is_read'] == 0) ? 'bg-light border-start border-primary border-4' : '';
                        ?>
                            <div class="list-group-item p-3 <?php echo $bg_class; ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-dark"><?php echo sanitize($notif['title']); ?></h6>
                                    <small class="text-muted small"><i class="fa fa-clock me-1"></i> <?php echo format_date($notif['created_at']); ?></small>
                                </div>
                                <p class="mb-0 text-muted small"><?php echo sanitize($notif['message']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
