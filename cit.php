<?php
// cit.php
// Certificate in Information Technology (CIT) Detailed Page

$page_title = "Certificate in Information Technology (CIT)";
require_once 'config/config.php';
include 'includes/header.php';
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--dark-accent) 0%, var(--navy-blue) 100%);">
    <div class="container text-center py-4">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-2 fw-bold">6-MONTHS CERTIFICATE</span>
        <h1 class="display-4 fw-bold">Certificate in Information Technology</h1>
        <p class="lead mb-0 text-white-50">Affiliated with Sindh/KPK Board of Technical Education</p>
    </div>
</section>

<!-- Course Details -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left Side Syllabus & Modules -->
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="fw-bold mb-4">Course Structure & Modules</h2>
                <p class="text-muted">The CIT course is a six-month foundational program designed to provide students with computing literacy and office administration capabilities required for regular business operations.</p>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="fw-bold mb-0"><i class="fa fa-folder-open me-2"></i>Technical Modules & Laboratories</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">Module 1: Computer Fundamentals & Operating Systems</div>
                                    <span class="text-muted small">Introduction to desktop hardware, operating system architectures, file directory organization, system software setups, control panel tools, and device installations.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">Module 2: MS Office Automation Suite</div>
                                    <span class="text-muted small">Comprehensive hands-on training in MS Word (formatting documents, reports creation), MS Excel (mathematical equations, custom formulas, graphs & tables), MS PowerPoint (slide designs, transition parameters), and MS Access.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">Module 3: InPage Urdu & Vernacular Desktop Publishing</div>
                                    <span class="text-muted small">Learning phonetic Urdu keyboards layout, typing speeds, configuring and printing Urdu scripts, creating newspaper layouts and book formats in InPage.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-3">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold text-dark">Module 4: Internet, Web Browsing, & Professional Communications</div>
                                    <span class="text-muted small">Understanding web search optimization, email management (creating email accounts, custom folders, attaching files), cloud storage setups (Google Drive, OneDrive), and digital safety protocols.</span>
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
                                <span class="fw-bold text-primary">CIT-02</span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Duration:</span>
                                <span class="fw-bold text-dark">6 Months</span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <span class="text-muted">Total Fee:</span>
                                <span class="fw-bold text-dark"><?php echo format_currency(12000); ?></span>
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
                            <a href="<?php echo BASE_URL; ?>student/admission.php?course_id=2" class="btn btn-primary btn-lg rounded-pill text-white py-2 shadow-sm hover-lift">
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
                    <p class="text-muted small">CIT certification qualifies you for several administration and computing positions:</p>
                    <ul class="list-unstyled mb-0 text-muted small">
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>Computer Operator</li>
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>Office Assistant</li>
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>Data Entry Operator</li>
                        <li class="mb-2"><i class="fa fa-angle-right me-2 text-primary"></i>Urdu Desktop Composer</li>
                        <li><i class="fa fa-angle-right me-2 text-primary"></i>Freelance Virtual Assistant</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
