<?php
// index.php
// Homepage for Hi Teac Academy

$page_title = "Home - Academy Management System";
require_once 'config/config.php';
include 'includes/header.php';

// Fetch Active Courses for homepage cards
try {
    $courses_stmt = $pdo->query("SELECT c.*, t.name as instructor_name FROM courses c LEFT JOIN teachers t ON c.instructor_id = t.id WHERE c.status = 'Active' LIMIT 3");
    $courses = $courses_stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
}

// Fetch Teachers
try {
    $teachers_stmt = $pdo->query("SELECT * FROM teachers LIMIT 4");
    $teachers = $teachers_stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
}

// Fetch Latest Announcements
try {
    $announcements_stmt = $pdo->query("SELECT * FROM announcements WHERE status = 'Active' ORDER BY created_at DESC LIMIT 3");
    $announcements = $announcements_stmt->fetchAll();
} catch (PDOException $e) {
    $announcements = [];
}

// Fetch Gallery Preview
try {
    $gallery_stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC LIMIT 4");
    $gallery_items = $gallery_stmt->fetchAll();
} catch (PDOException $e) {
    $gallery_items = [];
}
?>

<!-- Hero Banner -->
<section class="hero-banner d-flex align-items-center">
    <div class="container text-center text-md-start py-5">
        <div class="row align-items-center">
            <div class="col-md-7 mb-4 mb-md-0" data-aos="fade-right">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 text-uppercase fw-semibold tracking-wider">Admission Open Batches</span>
                <h1 class="display-3 fw-bold mb-3 lh-sm text-white">Unlock Your Future in <span class="text-warning">IT Education</span></h1>
                <p class="lead mb-4 text-white-50">Join the region's elite IT academy. Enroll in the Board of Technical Education registered DIT (1 Year) and CIT (6 Months) courses to transform your professional path.</p>
                <div class="d-grid d-md-flex gap-3">
                    <a href="<?php echo BASE_URL; ?>admission.php" class="btn btn-warning btn-lg rounded-pill px-4 py-3 fw-bold text-dark shadow-sm hover-lift">
                        <i class="fa fa-paper-plane me-2"></i> Apply Online Now
                    </a>
                    <a href="<?php echo BASE_URL; ?>courses.php" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3 fw-semibold hover-lift">
                        Explore Courses
                    </a>
                </div>
            </div>
            <div class="col-md-5 text-center" data-aos="fade-left">
                <i class="fa fa-laptop-code text-warning opacity-75" style="font-size: 15rem;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <h3 class="fw-bold text-primary mb-0">15+</h3>
                <small class="text-muted text-uppercase fw-semibold">Years Experience</small>
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <h3 class="fw-bold text-primary mb-0">10,000+</h3>
                <small class="text-muted text-uppercase fw-semibold">Graduates</small>
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <h3 class="fw-bold text-primary mb-0">98%</h3>
                <small class="text-muted text-uppercase fw-semibold">Success Ratio</small>
            </div>
            <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="400">
                <h3 class="fw-bold text-primary mb-0">100%</h3>
                <small class="text-muted text-uppercase fw-semibold">Govt Certified</small>
            </div>
        </div>
    </div>
</section>

<!-- About Academy Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-up">
                <span class="text-primary fw-semibold text-uppercase">Who We Are</span>
                <h2 class="fw-bold mb-4">Empowering Generations with Modern Tech Expertise</h2>
                <p class="text-muted mb-4">Hi Teac Academy is dedicated to providing superior professional education in Pakistan. Specialized in DIT and CIT programs, our curricula are customized to fit direct demands of the IT employment market, under the supervision of highly certified professors.</p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check-circle text-success fs-4 me-2"></i>
                            <span class="fw-medium">State-of-the-Art Computer Labs</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check-circle text-success fs-4 me-2"></i>
                            <span class="fw-medium">Govt Registered Diploma</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check-circle text-success fs-4 me-2"></i>
                            <span class="fw-medium">Flexible Batch Timings</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check-circle text-success fs-4 me-2"></i>
                            <span class="fw-medium">Affordable Fee Installments</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <div class="p-4 bg-light rounded-4 shadow-sm border">
                    <i class="fa fa-university fa-5x text-primary mb-3"></i>
                    <h4 class="fw-bold">Hi Teac Academy Campus</h4>
                    <p class="text-muted mb-0">Our campus provides structured lecture halls and interactive programming labs equipped with high-speed internet and professional software development tools.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Cards Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-semibold text-uppercase">Featured Courses</span>
            <h2 class="fw-bold">Our Technical Training Programs</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Choose the ideal professional certification to advance your software coding, system setup, or office administration capabilities.</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <?php foreach ($courses as $course): ?>
                <div class="col-lg-5 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="course-card hover-lift h-100 d-flex flex-column">
                        <div class="course-header">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-warning text-dark fw-bold"><?php echo sanitize($course['code']); ?></span>
                                <span class="fw-semibold text-white"><?php echo format_currency($course['fee']); ?></span>
                            </div>
                            <h4 class="fw-bold mb-0 text-white"><?php echo sanitize($course['name']); ?></h4>
                        </div>
                        <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                            <p class="text-muted mb-3 flex-grow-1"><?php echo sanitize(substr($course['description'], 0, 180)); ?>...</p>
                            <div class="mb-4">
                                <div class="small text-muted mb-1"><i class="fa fa-clock me-2 text-primary"></i><strong>Duration:</strong> <?php echo sanitize($course['duration']); ?></div>
                                <div class="small text-muted"><i class="fa fa-user-tie me-2 text-primary"></i><strong>Instructor:</strong> <?php echo sanitize($course['instructor_name'] ?? 'Senior Academy Faculty'); ?></div>
                            </div>
                            <div class="mt-auto">
                                <a href="<?php echo BASE_URL; ?><?php echo ($course['id'] == 1) ? 'dit.php' : 'cit.php'; ?>" class="btn btn-outline-primary rounded-pill w-100 mb-2">View Modules Detail</a>
                                <a href="<?php echo BASE_URL; ?>student/admission.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary rounded-pill text-white w-100">Apply Admission Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-semibold text-uppercase">Core Strengths</span>
            <h2 class="fw-bold">Why Students Choose Hi Teac</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center" data-aos="zoom-in" data-aos-delay="100">
                <div class="p-4 rounded-3 border h-100 hover-lift bg-white">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fa fa-award fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Government Certified</h5>
                    <p class="text-muted mb-0">Our diplomas and courses are fully recognized and registered with official boards of technical education.</p>
                </div>
            </div>
            <div class="col-md-4 text-center" data-aos="zoom-in" data-aos-delay="200">
                <div class="p-4 rounded-3 border h-100 hover-lift bg-white">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fa fa-user-graduate fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Job Oriented Labs</h5>
                    <p class="text-muted mb-0">We emphasize hands-on programming, networking setup, and administrative software assignments.</p>
                </div>
            </div>
            <div class="col-md-4 text-center" data-aos="zoom-in" data-aos-delay="300">
                <div class="p-4 rounded-3 border h-100 hover-lift bg-white">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fa fa-handshake fs-3"></i>
                    </div>
                    <h5 class="fw-bold">Alumni Network</h5>
                    <p class="text-muted mb-0">Join an active student community with alumni placed in premier software houses and government departments.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Teachers Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-semibold text-uppercase">Instructors</span>
            <h2 class="fw-bold">Meet Our Senior Faculty</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($teachers as $teacher): 
                $photo_url = !empty($teacher['photo']) && file_exists(ROOT_PATH . $teacher['photo']) 
                    ? BASE_URL . $teacher['photo'] 
                    : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
            ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm rounded-3 text-center p-4 h-100 hover-lift">
                        <img src="<?php echo $photo_url; ?>" alt="<?php echo sanitize($teacher['name']); ?>" class="rounded-circle mx-auto mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--primary-color);">
                        <h5 class="fw-bold mb-1"><?php echo sanitize($teacher['name']); ?></h5>
                        <p class="text-primary small mb-2"><?php echo sanitize($teacher['designation']); ?></p>
                        <p class="text-muted small mb-0"><strong>Specialization:</strong> <?php echo sanitize($teacher['specialization']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-semibold text-uppercase">Feedback</span>
            <h2 class="fw-bold">What Our Students Say</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm bg-light p-4 h-100">
                    <i class="fa fa-quote-left fa-2x text-primary opacity-25 mb-3"></i>
                    <p class="lead-sm text-muted">"Completing the DIT program at Hi Teac Academy was the turning point in my career. The practical SQL database labs and coding tasks gave me the confidence to secure a software engineer placement right after graduating."</p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="ms-1">
                            <h6 class="fw-bold mb-0">Bilal Siddiqui</h6>
                            <small class="text-primary">DIT Graduate (2025)</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm bg-light p-4 h-100">
                    <i class="fa fa-quote-left fa-2x text-primary opacity-25 mb-3"></i>
                    <p class="lead-sm text-muted">"The CIT batch timings were extremely flexible, which allowed me to upskill while doing my day job. The trainers are patient, knowledgeable, and push you to learn real-world execution. Best computer institute."</p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="ms-1">
                            <h6 class="fw-bold mb-0">Ayesha Fatima</h6>
                            <small class="text-primary">CIT Graduate (2025)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Announcements and Gallery Row -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5">
            <!-- Latest Announcements -->
            <div class="col-lg-6" data-aos="fade-right">
                <h3 class="fw-bold mb-4"><i class="fa fa-bullhorn text-primary me-2"></i>Latest Announcements</h3>
                <?php if (empty($announcements)): ?>
                    <p class="text-muted">No recent announcements at the moment.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush shadow-sm rounded-3">
                        <?php foreach ($announcements as $announce): ?>
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-primary text-uppercase"><?php echo sanitize($announce['type']); ?></span>
                                    <small class="text-muted"><?php echo format_date($announce['created_at'], 'd M Y'); ?></small>
                                </div>
                                <h6 class="fw-bold text-dark"><?php echo sanitize($announce['title']); ?></h6>
                                <p class="text-muted mb-0 small"><?php echo sanitize($announce['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Gallery Preview -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0"><i class="fa fa-image text-primary me-2"></i>Campus Gallery</h3>
                    <a href="<?php echo BASE_URL; ?>gallery.php" class="btn btn-outline-primary btn-sm rounded-pill">View All</a>
                </div>
                <div class="row g-2">
                    <?php if (empty($gallery_items)): ?>
                        <p class="text-muted col-12">Images loading soon.</p>
                    <?php else: ?>
                        <?php foreach ($gallery_items as $item): 
                            $img_url = !empty($item['image_path']) && file_exists(ROOT_PATH . $item['image_path']) 
                                ? BASE_URL . $item['image_path'] 
                                : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                        ?>
                            <div class="col-6">
                                <div class="position-relative overflow-hidden rounded-3" style="height: 120px;">
                                    <img src="<?php echo $img_url; ?>" alt="<?php echo sanitize($item['title']); ?>" class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 text-white p-1 text-center" style="font-size: 0.75rem;">
                                        <?php echo sanitize($item['category']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Summary Widget -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-semibold text-uppercase">FAQs</span>
            <h2 class="fw-bold">Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="accordion shadow-sm" id="homepageFaq">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Are the DIT and CIT diplomas government certified?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#homepageFaq">
                            <div class="accordion-body text-muted">
                                Yes, our DIT (Diploma in Information Technology) and CIT (Certificate in Information Technology) programs are fully registered and affiliated with the government Board of Technical Education.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                What is the fee structure for DIT and CIT courses?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#homepageFaq">
                            <div class="accordion-body text-muted">
                                The total DIT program fee is Rs. 24,000 (1 Year, split into convenient monthly or semester installments). The CIT program fee is Rs. 12,000 (6 Months). Secure payment options are available upon admission approval.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                How can I apply for admission?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#homepageFaq">
                            <div class="accordion-body text-muted">
                                You need to register as a student on our website, log in to your Student Dashboard, and fill out the online admission form with scan uploads of your CNIC/B-Form and Matriculation Certificate.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo BASE_URL; ?>faq.php" class="text-decoration-none fw-semibold">View More FAQs <i class="fa fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Admission Banner -->
<section class="py-5 text-white text-center" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--dark-accent) 100%);">
    <div class="container py-3" data-aos="zoom-in">
        <h2 class="fw-bold mb-3">Ready to Start Your Professional Journey?</h2>
        <p class="lead mb-4 text-white-50">Applications are currently being accepted online. Don't wait, secure your seat in our next batch!</p>
        <a href="<?php echo BASE_URL; ?>student/admission.php" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold text-dark hover-lift">Apply Online Admission Now</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
