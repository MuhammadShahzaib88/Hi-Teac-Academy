<?php
// dit.php
// Diploma in Information Technology (DIT) Detailed Page

$page_title = "Diploma in Information Technology (DIT)";
require_once 'config/config.php';
include 'includes/header.php';
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--dark-accent) 0%, var(--navy-blue) 100%);">
    <div class="container text-center py-4">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-2 fw-bold">1-YEAR DIPLOMA</span>
        <h1 class="display-4 fw-bold">Diploma in Information Technology</h1>
        <p class="lead mb-0 text-white-50">Affiliated with Sindh/KPK Board of Technical Education</p>
    </div>
</section>

<!-- Course Details -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Side Syllabus & Modules -->
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="fw-bold mb-4">Course Structure & Semester Modules</h2>
                <p class="text-muted">The DIT program is a comprehensive one-year course divided into two structured semesters. Each semester covers both fundamental concepts and advanced engineering laboratories under certified trainers.</p>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="fw-bold mb-0"><i class="fa fa-folder-open me-2"></i>Semester 1 (6 Months)</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">1. Information Technology Fundamentals</div>
                                    <span class="text-muted small">Computer architectures, hardware configurations, input/output peripherals, data representations, and introductory OS commands.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">2. Office Automation Suite</div>
                                    <span class="text-muted small">Advanced MS Word document styling, MS Excel spreadsheet equations, MS PowerPoint business presentation slide decks, and MS Access tables.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">3. Web Designing & Development</div>
                                    <span class="text-muted small">HTML5 layouts, CSS3 stylesheets, Responsive Flexbox grids, JavaScript syntax, DOM controls, and jQuery modules.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">4. C/C++ Programming Logic</div>
                                    <span class="text-muted small">Variables, control flow, loops, arrays, functions, pointers, structural programming, and basic Object Oriented Programming concepts.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="fw-bold mb-0"><i class="fa fa-folder-open me-2"></i>Semester 2 (6 Months)</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">1. Graphic Designing & Vector Assets</div>
                                    <span class="text-muted small">Photo editing tools, vector icons styling, branding banners, Adobe Photoshop canvas edits, CorelDraw vector diagrams, and Urdu InPage scripts.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">2. Database Management Systems (SQL)</div>
                                    <span class="text-muted small">Relational database schemas, primary/foreign keys, normalization rules, SQL queries, joins, views, and index optimizations.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">3. Operating Systems & Servers</div>
                                    <span class="text-muted small">Process scheduling, memory management, Windows Server administration, Linux shell scripting, user permissions, and security policies.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">4. Computer Networking & Security</div>
                                    <span class="text-muted small">OSI model protocols, TCP/IP addresses, subnet masks, routing switches, router configs, WAN setups, and system firewall security.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Side Meta Info Box -->
            <div class="col-lg-4" data-aos="fade-left">
                <div class="card border-0 shadow-lg rounded-3 mb-4 bg-light">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3 text-dark">Course Roster Details</h4>
                        <hr class="my-3">
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Code:</span>
                                <span class="fw-bold text-primary">DIT-01</span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Duration:</span>
                                <span class="fw-bold text-dark">1 Year (2 Semesters)</span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Total Fee:</span>
                                <span class="fw-bold text-dark"><?php echo format_currency(24000); ?></span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Certification:</span>
                                <span class="fw-bold text-success">Board of Technical Ed.</span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Eligibility:</span>
                                <span class="fw-bold text-dark">Matric Pass (Science/Arts)</span>
                            </li>
                        </ul>
                        <div class="d-grid gap-2">
                            <a href="<?php echo BASE_URL; ?>student/admission.php?course_id=1" class="btn btn-primary btn-lg rounded-pill text-white py-2 shadow-sm hover-lift">
                                <i class="fa fa-file-signature me-2"></i> Enroll in Course
                            </a>
                            <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline-secondary btn-lg rounded-pill py-2">
                                <i class="fa fa-question-circle me-2"></i> Request Call
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa fa-user-graduate text-primary me-2"></i>Career Prospects</h5>
                    <p class="text-muted small">Graduating with a DIT diploma qualifies you for numerous technician roles in public and private sectors:</p>
                    <ul class="list-unstyled mb-0 text-muted small">
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>Junior Web Developer</li>
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>Graphic Designer</li>
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>IT Support Assistant</li>
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>Network Administrator Assistant</li>
                        <li><i class="fa fa-angle-right me-2 text-primary"></i>Data Entry Operator (Govt Scale 11-14)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
