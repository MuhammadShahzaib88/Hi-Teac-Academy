<?php
// student/ask_question.php
// Student Question Desk & Historical Responses

$page_title = "Ask Questions";
require_once dirname(__DIR__) . '/includes/student_header.php';

$student_id = $_SESSION['student_id'];
$errors = [];
$success = false;

// Process new question post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_question'])) {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token validation failed.");
    }
    
    $subject = sanitize($_POST['subject'] ?? '');
    $question_text = sanitize($_POST['question_text'] ?? '');
    
    if (empty($subject)) $errors[] = "Subject is required.";
    if (empty($question_text)) $errors[] = "Question content is required.";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO questions (student_id, subject, question_text, status) VALUES (?, ?, ?, 'Pending')");
            $stmt->execute([$student_id, $subject, $question_text]);
            
            // Set notification for admin (Admin ID = 1)
            $student_name = $_SESSION['student_first_name'] . ' ' . $_SESSION['student_last_name'];
            create_notification('Admin', 1, 'New Student Question', "Student $student_name posted a new question: '$subject'");
            
            set_flash_message('success', 'Your question has been posted successfully! A teacher will reply shortly.');
            header('Location: ' . BASE_URL . 'student/ask_question.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Failed to submit question due to database error.";
        }
    }
}

// Fetch questions asked by this student
try {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE student_id = ? ORDER BY id DESC");
    $stmt->execute([$student_id]);
    $questions = $stmt->fetchAll();
} catch (PDOException $e) {
    $questions = [];
}
?>

<div class="row g-4">
    <!-- Submit Form Column -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-paper-plane text-primary me-2"></i>Ask a New Question</h5>
            </div>
            
            <div class="card-body p-4">
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

                <form action="<?php echo BASE_URL; ?>student/ask_question.php" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">Subject / Topic</label>
                        <input type="text" class="form-control" id="subject" name="subject" required placeholder="e.g. Graphic design software options">
                    </div>
                    
                    <div class="mb-4">
                        <label for="question_text" class="form-label fw-semibold">Your Question Details</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="5" required placeholder="Describe your doubt or technical issue clearly..."></textarea>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" name="submit_question" class="btn btn-primary rounded-pill py-2 text-white">
                            <i class="fa fa-question-circle me-1"></i> Submit Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Question Tickets History List -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-history text-primary me-2"></i>My Questions & Replies</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (empty($questions)): ?>
                    <p class="text-muted text-center py-5">You haven't asked any questions yet.</p>
                <?php else: ?>
                    <div class="accordion" id="accordionQuestions">
                        <?php foreach ($questions as $index => $q): 
                            $badge = ($q['status'] === 'Answered') ? 'bg-success' : 'bg-warning text-dark';
                            
                            // Fetch replies for this question
                            $reply_stmt = $pdo->prepare("SELECT * FROM replies WHERE question_id = ? ORDER BY id ASC");
                            $reply_stmt->execute([$q['id']]);
                            $replies = $reply_stmt->fetchAll();
                        ?>
                            <div class="accordion-item border mb-3 rounded shadow-sm overflow-hidden">
                                <h2 class="accordion-header" id="heading-<?php echo $q['id']; ?>">
                                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $q['id']; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $q['id']; ?>">
                                        <div class="d-flex flex-wrap align-items-center w-100 me-3">
                                            <span class="fw-bold text-dark me-2"><?php echo sanitize($q['subject']); ?></span>
                                            <span class="badge <?php echo $badge; ?> me-auto my-1"><?php echo $q['status']; ?></span>
                                            <small class="text-muted"><i class="fa fa-calendar me-1"></i> <?php echo format_date($q['created_at'], 'd M Y'); ?></small>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo $q['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $q['id']; ?>" data-bs-parent="#accordionQuestions">
                                    <div class="accordion-body bg-light p-4">
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark"><i class="fa fa-user me-2 text-primary"></i>My Question:</h6>
                                            <p class="text-muted bg-white p-3 border rounded mb-0" style="white-space: pre-line;"><?php echo sanitize($q['question_text']); ?></p>
                                        </div>
                                        
                                        <!-- Replies Section -->
                                        <div>
                                            <h6 class="fw-bold text-dark mb-3"><i class="fa fa-comments me-2 text-success"></i>Replies / Answers:</h6>
                                            <?php if (empty($replies)): ?>
                                                <p class="text-muted small mb-0"><i class="fa fa-info-circle me-1"></i> No replies from instructors yet. Awaiting response.</p>
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

<?php require_once dirname(__DIR__) . '/includes/student_footer.php'; ?>
