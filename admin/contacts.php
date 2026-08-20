<?php
// admin/contacts.php
// Administrator Contact Form Inquiry Review Desk

$page_title = "Manage Inquiries";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

// 1. Toggle status
if (isset($_GET['action']) && $_GET['action'] === 'toggle') {
    $id = intval($_GET['id'] ?? 0);
    $current_status = sanitize($_GET['status'] ?? 'Pending');
    $new_status = ($current_status === 'Pending') ? 'Resolved' : 'Pending';
    
    try {
        $stmt = $pdo->prepare("UPDATE contacts SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        set_flash_message('success', "Inquiry marked as $new_status.");
    } catch (PDOException $e) {
        set_flash_message('danger', "Failed to update inquiry status.");
    }
    
    header('Location: ' . BASE_URL . 'admin/contacts.php');
    exit;
}

// 2. Delete inquiry record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_inquiry'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $id = intval($_POST['inquiry_id'] ?? 0);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', "Inquiry deleted successfully.");
        header('Location: ' . BASE_URL . 'admin/contacts.php');
        exit;
    } catch (PDOException $e) {
        set_flash_message('danger', "Failed to delete inquiry.");
    }
}

// Fetch inquiries
try {
    $inquiries = $pdo->query("SELECT * FROM contacts ORDER BY status ASC, id DESC")->fetchAll();
} catch (PDOException $e) {
    $inquiries = [];
}

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-envelope text-primary me-2"></i>Contact Inquiries</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($inquiries)): ?>
                    <p class="text-muted text-center py-5">No contact form messages submitted.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sender Details</th>
                                    <th>Subject</th>
                                    <th>Message Details</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inquiries as $inq): 
                                    $badge = ($inq['status'] === 'Resolved') ? 'bg-success' : 'bg-warning text-dark';
                                ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark d-block"><?php echo sanitize($inq['name']); ?></span>
                                            <small class="text-muted"><?php echo sanitize($inq['email']); ?></small>
                                        </td>
                                        <td><span class="fw-medium text-dark"><?php echo sanitize($inq['subject']); ?></span></td>
                                        <td>
                                            <p class="small text-muted mb-0" style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?php echo sanitize($inq['message']); ?>">
                                                <?php echo sanitize($inq['message']); ?>
                                            </p>
                                        </td>
                                        <td><?php echo format_date($inq['created_at']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $badge; ?> px-2 py-1 rounded-pill small"><?php echo $inq['status']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <!-- View Details modal trigger -->
                                                <button class="btn btn-sm btn-outline-primary rounded-circle btn-view-inquiry"
                                                        data-name="<?php echo sanitize($inq['name']); ?>"
                                                        data-email="<?php echo sanitize($inq['email']); ?>"
                                                        data-subject="<?php echo sanitize($inq['subject']); ?>"
                                                        data-message="<?php echo sanitize($inq['message']); ?>"
                                                        data-date="<?php echo format_date($inq['created_at']); ?>">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                
                                                <!-- Toggle Resolve -->
                                                <a href="?action=toggle&id=<?php echo $inq['id']; ?>&status=<?php echo $inq['status']; ?>" class="btn btn-sm btn-outline-success rounded-circle" title="Mark Resolved/Pending">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                                
                                                <!-- Delete inquiry -->
                                                <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this inquiry record?');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="inquiry_id" value="<?php echo $inq['id']; ?>">
                                                    <button type="submit" name="delete_inquiry" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa fa-trash"></i></button>
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
</div>

<!-- Detailed Inquiry Modal -->
<div class="modal fade" id="inquiryDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-bold modal-title"><i class="fa fa-envelope-open me-2 text-primary"></i>Inquiry Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <span class="text-muted small d-block">From:</span>
                    <strong class="text-dark" id="modal-sender-name"></strong> <span class="text-muted" id="modal-sender-email"></span>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Subject:</span>
                    <strong class="text-dark fs-6" id="modal-inq-subject"></strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Date Received:</span>
                    <strong class="text-muted small" id="modal-inq-date"></strong>
                </div>
                <div class="mb-0 border-top pt-3">
                    <span class="text-muted small d-block mb-1">Message Content:</span>
                    <p class="text-muted bg-light p-3 border rounded mb-0" id="modal-inq-message" style="white-space: pre-line;"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-view-inquiry').on('click', function() {
        // Load details from data attributes
        $('#modal-sender-name').text($(this).data('name'));
        $('#modal-sender-email').text('(' + $(this).data('email') + ')');
        $('#modal-inq-subject').text($(this).data('subject'));
        $('#modal-inq-date').text($(this).data('date'));
        $('#modal-inq-message').text($(this).data('message'));
        
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('inquiryDetailModal'));
        modal.show();
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
