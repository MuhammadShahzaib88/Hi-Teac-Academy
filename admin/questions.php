<?php
// admin/questions.php
// Administrator Student Questions Q&A Manager Desk

$page_title = "Manage Questions";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

$errors = [];
$success = false;

// Process Reply Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }
    
    $question_id = intval($_POST['question_id'] ?? 0);
    $reply_text = sanitize($_POST['reply_text'] ?? '');
    
    if (empty($reply_text)) {
        $errors[] = "Reply content cannot be empty.";
    }
    
    if (empty($errors)) {
        try {
            $admin_id = $_SESSION['admin_id'];
            
            // 1. Insert Reply
            $stmt = $pdo->prepare("INSERT INTO replies (question_id, replier_id, replier_type, reply_text) VALUES (?, ?, 'Admin', ?)");
            $stmt->execute([$question_id, $admin_id, $reply_text]);
            
            // 2. Mark Question as Answered
            $upd_stmt = $pdo->prepare("UPDATE questions SET status = 'Answered', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $upd_stmt->execute([$question_id]);
            
            // 3. Fetch student ID for notification
            $stud_stmt = $pdo->prepare("SELECT student_id, subject FROM questions WHERE id = ?");
            $stud_stmt->execute([$question_id]);
            $question_info = $stud_stmt->fetch();
            
            if ($question_info) {
                create_notification('Student', $question_info['student_id'], 'Question Answered', "An instructor replied to your query: '" . $question_info['subject'] . "'.");
            }
            
            set_flash_message('success', 'Your reply has been posted successfully!');
            header('Location: ' . BASE_URL . 'admin/questions.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Failed to submit reply: " . $e->getMessage();
        }
    }
}

// Fetch all questions with student details
try {
    $stmt = $pdo->query("
        SELECT q.*, s.first_name, s.last_name, s.email, s.profile_pic
        FROM questions q
        JOIN students s ON q.student_id = s.id
        ORDER BY q.status ASC, q.id DESC
    ");
    $questions = $stmt->fetchAll();
} catch (PDOException $e) {
    $questions = [];
}
?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-question-circle text-primary me-2"></i>Academic Tickets Desk</h5>
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

                <?php if (empty($questions)): ?>
                    <p class="text-muted text-center py-5">No question tickets asked yet.</p>
                <?php else: ?>
                    <div class="accordion" id="accordionQuestionsAdmin">
                        <?php foreach ($questions as $q): 
                            $badge = ($q['status'] === 'Answered') ? 'bg-success' : 'bg-warning text-dark';
                            
                            // Fetch replies for this question
                            $rep_stmt = $pdo->prepare("SELECT * FROM replies WHERE question_id = ? ORDER BY id ASC");
                            $rep_stmt->execute([$q['id']]);
                            $replies = $rep_stmt->fetchAll();
                        ?>
                            <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden">
                                <h2 class="accordion-header" id="heading-<?php echo $q['id']; ?>">
                                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $q['id']; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $q['id']; ?>">
                                        <div class="d-flex flex-wrap align-items-center w-100 me-3">
                                            <span class="fw-bold text-dark me-2"><?php echo sanitize($q['subject']); ?></span>
                                            <span class="badge <?php echo $badge; ?> me-2 my-1"><?php echo $q['status']; ?></span>
                                            <span class="small text-muted me-auto">By: <?php echo sanitize($q['first_name'] . ' ' . $q['last_name']); ?> (<?php echo sanitize($q['email']); ?>)</span>
                                            <small class="text-muted"><i class="fa fa-calendar me-1"></i> <?php echo format_date($q['created_at']); ?></small>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo $q['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $q['id']; ?>" data-bs-parent="#accordionQuestionsAdmin">
                                    <div class="accordion-body bg-light p-4">
                                        <!-- Question Details -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark"><i class="fa fa-user me-2 text-primary"></i>Student Question:</h6>
                                            <p class="text-muted bg-white p-3 border rounded mb-0" style="white-space: pre-line;"><?php echo sanitize($q['question_text']); ?></p>
                                        </div>
                                        
                                        <!-- Replies history -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark mb-2"><i class="fa fa-comments me-2 text-success"></i>Replies History:</h6>
                                            <?php if (empty($replies)): ?>
                                                <p class="text-muted small mb-0"><i class="fa fa-info-circle me-1"></i> No replies posted yet.</p>
                                            <?php else: ?>
                                                <?php foreach ($replies as $rep): 
                                                    $replier_name = 'Academy Representative';
                                                    if ($rep['replier_type'] === 'Admin') {
                                                        $r_stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
                                                        $r_stmt->execute([$rep['replier_id']]);
                                                        $replier_name = $r_stmt->fetchColumn() . ' (Admin)';
                                                    } else {
                                                        $r_stmt = $pdo->prepare("SELECT name FROM teachers WHERE id = ?");
                                                        $r_stmt->execute([$rep['replier_id']]);
                                                        $replier_name = $r_stmt->fetchColumn() . ' (Teacher)';
                                                    }
                                                ?>
                                                    <div class="border rounded p-3 mb-2 bg-white shadow-xs">
                                                        <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                                            <small class="fw-bold text-success"><i class="fa fa-user-tie me-1"></i> <?php echo sanitize($replier_name); ?></small>
                                                            <small class="text-muted"><?php echo format_date($rep['created_at']); ?></small>
                                                        </div>
                                                        <p class="small text-muted mb-0" style="white-space: pre-line;"><?php echo sanitize($rep['reply_text']); ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Post Reply Form -->
                                        <div class="border-top pt-3">
                                            <h6 class="fw-bold text-dark mb-2"><i class="fa fa-reply me-2 text-primary"></i>Write Reply Response:</h6>
                                            <form action="" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                                                
                                                <div class="mb-3">
                                                    <textarea class="form-control" name="reply_text" rows="3" required placeholder="Type your instructional feedback here..."></textarea>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" name="submit_reply" class="btn btn-primary rounded-pill px-4 text-white">
                                                        <i class="fa fa-paper-plane me-1"></i> Submit Reply
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
