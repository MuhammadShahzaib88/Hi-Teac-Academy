<?php
// faq.php
// Frequently Asked Questions Detailed Page

$page_title = "Frequently Asked Questions";
require_once 'config/config.php';
include 'includes/header.php';
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">FAQ Center</h1>
        <p class="lead mb-0">Find answers to commonly asked questions about courses, certifications, and policies</p>
    </div>
</section>

<!-- FAQs Accordions -->
<section class="py-5">
    <div class="container" style="max-width: 900px;">
        
        <!-- Category 1: Admission & Eligibility -->
        <div class="mb-5" data-aos="fade-up">
            <h4 class="fw-bold mb-3 text-primary"><i class="fa fa-user-graduate me-2"></i>Admission & Eligibility</h4>
            <div class="accordion shadow-sm" id="accordionAdmissions">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="adm-h1">
                        <button class="accordion-button fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#adm-c1" aria-expanded="true" aria-controls="adm-c1">
                            Who can apply for the DIT and CIT programs?
                        </button>
                    </h2>
                    <div id="adm-c1" class="accordion-collapse collapse show" aria-labelledby="adm-h1" data-bs-parent="#accordionAdmissions">
                        <div class="accordion-body text-muted">
                            Any student who has completed their matriculation (Science or Arts, Matric certificate or equivalent marksheet) is eligible to apply. There is no strict age restriction.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="adm-h2">
                        <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#adm-c2" aria-expanded="false" aria-controls="adm-c2">
                            What documents do I need to scan and upload?
                        </button>
                    </h2>
                    <div id="adm-c2" class="accordion-collapse collapse" aria-labelledby="adm-h2" data-bs-parent="#accordionAdmissions">
                        <div class="accordion-body text-muted">
                            You must scan and upload:
                            <ul class="mb-0 mt-2">
                                <li>Matric Marksheet or Matric Certificate.</li>
                                <li>Your CNIC or B-Form (if under 18 years of age).</li>
                                <li>A recent passport size photograph for profile/student card purposes.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 2: Fees & Payments -->
        <div class="mb-5" data-aos="fade-up">
            <h4 class="fw-bold mb-3 text-primary"><i class="fa fa-credit-card me-2"></i>Fees & Payments</h4>
            <div class="accordion shadow-sm" id="accordionFees">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="fee-h1">
                        <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#fee-c1" aria-expanded="false" aria-controls="fee-c1">
                            Do I have to pay the entire course fee upfront?
                        </button>
                    </h2>
                    <div id="fee-c1" class="accordion-collapse collapse" aria-labelledby="fee-h1" data-bs-parent="#accordionFees">
                        <div class="accordion-body text-muted">
                            No, the academy supports flexible, interest-free payment models. For the 1-year DIT course (total fee Rs. 24,000), you can pay in two semester blocks of Rs. 12,000 or set up convenient monthly installments after admission approval.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="fee-h2">
                        <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#fee-c2" aria-expanded="false" aria-controls="fee-c2">
                            Are there any hidden costs (exam fees, lab charges)?
                        </button>
                    </h2>
                    <div id="fee-c2" class="accordion-collapse collapse" aria-labelledby="fee-h2" data-bs-parent="#accordionFees">
                        <div class="accordion-body text-muted">
                            The fee includes lab usage, teacher guidelines, and internal tests. External Board Examination fees are payable directly to the Board of Technical Education at the time of exam registration (typically separate and regulated by the board).
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category 3: Class Schedule & Batches -->
        <div class="mb-5" data-aos="fade-up">
            <h4 class="fw-bold mb-3 text-primary"><i class="fa fa-clock me-2"></i>Class Schedule & Batches</h4>
            <div class="accordion shadow-sm" id="accordionSchedules">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="sch-h1">
                        <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sch-c1" aria-expanded="false" aria-controls="sch-c1">
                            What are the class timings?
                        </button>
                    </h2>
                    <div id="sch-c1" class="accordion-collapse collapse" aria-labelledby="sch-h1" data-bs-parent="#accordionSchedules">
                        <div class="accordion-body text-muted">
                            We run multiple batches daily from Monday to Friday:
                            <ul class="mb-0 mt-2">
                                <li>Morning Batch: 9:00 AM - 11:00 AM</li>
                                <li>Noon Batch: 11:30 AM - 1:30 PM</li>
                                <li>Afternoon Batch: 3:00 PM - 5:00 PM</li>
                                <li>Evening Batch: 6:00 PM - 8:00 PM</li>
                            </ul>
                            Batch choices are chosen upon admission confirmation.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="sch-h2">
                        <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#sch-c2" aria-expanded="false" aria-controls="sch-c2">
                            Can I switch my batch choice later?
                        </button>
                    </h2>
                    <div id="sch-c2" class="accordion-collapse collapse" aria-labelledby="sch-h2" data-bs-parent="#accordionSchedules">
                        <div class="accordion-body text-muted">
                            Yes, students can request batch switches by filing a request with the academy administration. Switches are approved based on batch seat availability.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
