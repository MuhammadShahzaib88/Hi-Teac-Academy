<?php
// courses.php
// Courses Overview page

$page_title = "Our Courses";
require_once 'config/config.php';
include 'includes/header.php';

// Fetch all courses
try {
    $stmt = $pdo->query("SELECT c.*, t.name as instructor_name FROM courses c LEFT JOIN teachers t ON c.instructor_id = t.id WHERE c.status = 'Active'");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
}
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">Academic Programs</h1>
        <p class="lead mb-0">Government-affiliated courses designed to build your career in IT</p>
    </div>
</section>

<!-- Courses List -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php foreach ($courses as $course): ?>
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="card border-0 shadow rounded-3 h-100 d-flex flex-column hover-lift">
                        <div class="course-header p-4 text-white" style="background: linear-gradient(135deg, var(--navy-blue), var(--light-blue)); border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill"><?php echo sanitize($course['code']); ?></span>
                                <h3 class="fw-bold mb-0"><?php echo format_currency($course['fee']); ?></h3>
                            </div>
                            <h4 class="fw-bold mb-0 text-white"><?php echo sanitize($course['name']); ?></h4>
                        </div>
                        
                        <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                            <p class="text-muted mb-4"><?php echo sanitize($course['description']); ?></p>
                            
                            <h5 class="fw-bold text-dark mb-3"><i class="fa fa-book-open text-primary me-2"></i>Course Modules & Syllabus</h5>
                            <div class="bg-light p-3 rounded mb-4 flex-grow-1">
                                <p class="mb-0 text-dark small" style="white-space: pre-line; line-height: 1.6;">
                                    <?php echo sanitize($course['modules']); ?>
                                </p>
                            </div>
                            
                            <div class="row mb-4 border-top border-bottom py-3 g-2">
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Duration</small>
                                    <span class="fw-semibold text-dark"><i class="fa fa-calendar-alt text-primary me-2"></i><?php echo sanitize($course['duration']); ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Course Instructor</small>
                                    <span class="fw-semibold text-dark"><i class="fa fa-user-tie text-primary me-2"></i><?php echo sanitize($course['instructor_name'] ?? 'Senior Faculty'); ?></span>
                                </div>
                            </div>
                            
                            <div class="row g-2 mt-auto">
                                <div class="col-sm-6">
                                    <a href="<?php echo BASE_URL; ?><?php echo ($course['id'] == 1) ? 'dit.php' : 'cit.php'; ?>" class="btn btn-outline-primary w-100 rounded-pill py-2">
                                        <i class="fa fa-info-circle me-1"></i> Full Syllabus
                                    </a>
                                </div>
                                <div class="col-sm-6">
                                    <a href="<?php echo BASE_URL; ?>student/admission.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary text-white w-100 rounded-pill py-2">
                                        <i class="fa fa-file-signature me-1"></i> Apply Admission
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-light text-center border-top">
    <div class="container py-3">
        <h3 class="fw-bold mb-3">Admission Criteria & Documents</h3>
        <p class="text-muted mx-auto mb-4" style="max-width: 700px;">To apply for either course, you must have completed matriculation or equivalent school exams. You will need to upload digital scans of your Matric Marksheet/Certificate and CNIC/B-Form inside the student profile portal.</p>
        <a href="<?php echo BASE_URL; ?>student/admission.php" class="btn btn-primary rounded-pill px-5 py-2 text-white"><i class="fa fa-file-invoice me-2"></i>Start Online Application</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
