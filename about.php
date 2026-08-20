<?php
// about.php
// About Page for Hi Teac Academy

$page_title = "About Us";
require_once 'config/config.php';
include 'includes/header.php';

// Fetch Faculty members
try {
    $teachers_stmt = $pdo->query("SELECT * FROM teachers");
    $teachers = $teachers_stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
}
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">About Our Academy</h1>
        <p class="lead mb-0">Learn about our history, values, facilities, and the team behind Hi Teac Academy</p>
    </div>
</section>

<!-- Vision & Mission -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="fw-bold text-dark mb-3">Our Core Mission</h2>
                <p class="text-muted lead">At Hi Teac Academy, we aim to bridge the gap between theoretical knowledge and practical IT industry application.</p>
                <p class="text-muted">Established with the goal of serving students seeking career pathways in technical streams, we provide structured, Board-affiliated academic diplomas that enable graduates to build viable careers in coding, web development, graphics design, and hardware/software administration.</p>
                <div class="row g-3 mt-2">
                    <div class="col-sm-6">
                        <div class="p-3 border rounded bg-light">
                            <h5 class="fw-bold text-primary"><i class="fa fa-eye me-2"></i>Our Vision</h5>
                            <small class="text-muted">To be the leading training hub in Pakistan for technical information technologies education.</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border rounded bg-light">
                            <h5 class="fw-bold text-primary"><i class="fa fa-bullseye me-2"></i>Our Goal</h5>
                            <small class="text-muted">Empower 100% of our registered students with hands-on labs and certifications.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="bg-light p-5 rounded-4 shadow-sm border">
                    <h3 class="fw-bold mb-4"><i class="fa fa-history text-primary me-2"></i>Academy Milestone</h3>
                    <div class="mb-3 border-start border-primary border-3 ps-3">
                        <h6 class="fw-bold mb-1">Founded in 2011</h6>
                        <p class="small text-muted mb-0">Started as a small training institute in Karachi with a single computer lab and a handful of students.</p>
                    </div>
                    <div class="mb-3 border-start border-primary border-3 ps-3">
                        <h6 class="fw-bold mb-1">Board Affiliation (2014)</h6>
                        <p class="small text-muted mb-0">Received official registration and affiliation with the State Board of Technical Education to award official DIT diplomas.</p>
                    </div>
                    <div class="border-start border-primary border-3 ps-3">
                        <h6 class="fw-bold mb-1">Digital Evolution (2026)</h6>
                        <p class="small text-muted mb-0">Launched the online Academy Management System (Hi Teac Portal) allowing remote admissions, question tickets, and status tracking.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Facilities Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-semibold text-uppercase">Campus Features</span>
            <h2 class="fw-bold">Our Learning Environment</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                    <i class="fa fa-desktop text-primary fa-3x mb-3"></i>
                    <h5 class="fw-bold">Modern Computer Labs</h5>
                    <p class="text-muted small">Equipped with Intel Core i7 stations, dual displays, and high-speed fiber internet connection to facilitate coding and graphic design workflows.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                    <i class="fa fa-book-reader text-primary fa-3x mb-3"></i>
                    <h5 class="fw-bold">Technical Library</h5>
                    <p class="text-muted small">A collection of programming references, hardware guides, office management manuals, and past Board examination files for reference.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                    <i class="fa fa-server text-primary fa-3x mb-3"></i>
                    <h5 class="fw-bold">In-house Hosting Server</h5>
                    <p class="text-muted small">Allows students to deploy and test web applications, database connections, and custom scripts locally on the academy intranet.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Faculty Grid Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-primary fw-semibold text-uppercase">Faculty Members</span>
            <h2 class="fw-bold">Meet Our Senior Instructors</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Our trainers are industry professionals with years of development and system administration experience.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($teachers as $teacher): 
                $photo_url = !empty($teacher['photo']) && file_exists(ROOT_PATH . $teacher['photo']) 
                    ? BASE_URL . $teacher['photo'] 
                    : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
            ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm rounded-3 text-center p-4 h-100 hover-lift bg-light">
                        <img src="<?php echo $photo_url; ?>" alt="<?php echo sanitize($teacher['name']); ?>" class="rounded-circle mx-auto mb-3 shadow-sm" style="width: 130px; height: 130px; object-fit: cover; border: 3px solid var(--primary-color);">
                        <h5 class="fw-bold mb-1"><?php echo sanitize($teacher['name']); ?></h5>
                        <p class="text-primary small mb-3"><?php echo sanitize($teacher['designation']); ?></p>
                        <hr class="my-2">
                        <p class="text-muted small mb-2"><i class="fa fa-envelope text-primary me-2"></i><?php echo sanitize($teacher['email'] ?? 'N/A'); ?></p>
                        <p class="text-muted small mb-2"><i class="fa fa-phone text-primary me-2"></i><?php echo sanitize($teacher['phone'] ?? 'N/A'); ?></p>
                        <p class="text-muted small mb-0"><strong>Specialization:</strong> <?php echo sanitize($teacher['specialization']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
