<?php
// includes/footer.php
// Global Footer for Public Pages
?>
<footer class="no-print">
    <div class="container">
        <div class="row g-4">
            <!-- Column 1: Brand & About -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="600">
                <h5 class="text-white mb-4"><i class="fa fa-graduation-cap text-primary me-2"></i>Hi Teac Academy</h5>
                <p>Hi Teac Academy is a leading educational institution offering premium technical diplomas and certificates (DIT and CIT) to equip students with future-ready skills in software engineering, networking, graphics, and office administration.</p>
                <div class="mt-4">
                    <a href="<?php echo get_setting('facebook_url', '#'); ?>" class="btn btn-outline-light btn-sm rounded-circle me-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo get_setting('twitter_url', '#'); ?>" class="btn btn-outline-light btn-sm rounded-circle me-2" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo get_setting('instagram_url', '#'); ?>" class="btn btn-outline-light btn-sm rounded-circle me-2" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo get_setting('youtube_url', '#'); ?>" class="btn btn-outline-light btn-sm rounded-circle" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-duration="800">
                <h5 class="text-white mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>index.php"><i class="fa fa-angle-right me-2 text-primary"></i>Home</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>about.php"><i class="fa fa-angle-right me-2 text-primary"></i>About Us</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>courses.php"><i class="fa fa-angle-right me-2 text-primary"></i>Our Courses</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>gallery.php"><i class="fa fa-angle-right me-2 text-primary"></i>Gallery</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>faq.php"><i class="fa fa-angle-right me-2 text-primary"></i>FAQs</a></li>
                    <li class="mb-2"><a href="<?php echo BASE_URL; ?>contact.php"><i class="fa fa-angle-right me-2 text-primary"></i>Contact Us</a></li>
                </ul>
            </div>

            <!-- Column 3: Courses -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="text-white mb-4">Our Programs</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo BASE_URL; ?>dit.php" class="d-block mb-1">Diploma in Information Technology (DIT)</a>
                        <small class="text-muted"><i class="far fa-clock me-1 text-primary"></i> 1 Year Diploma</small>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo BASE_URL; ?>cit.php" class="d-block mb-1">Certificate in Information Technology (CIT)</a>
                        <small class="text-muted"><i class="far fa-clock me-1 text-primary"></i> 6 Months Course</small>
                    </li>
                </ul>
            </div>

            <!-- Column 4: Contact info -->
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-duration="1200">
                <h5 class="text-white mb-4">Contact Academy</h5>
                <p><i class="fa fa-map-marker-alt text-primary me-2"></i><?php echo get_setting('address', 'Kohat, KPK, Pakistan'); ?></p>
                <p><i class="fa fa-phone text-primary me-2"></i><?php echo get_setting('contact_phone', '03304347547'); ?></p>
                <p><i class="fa fa-envelope text-primary me-2"></i><?php echo get_setting('contact_email', 'shahzaibbangash24@gmail.com'); ?></p>
                <span class="badge bg-success p-2"><i class="fa fa-check-circle me-1"></i> Admissions Status: <?php echo get_setting('admission_status', 'Open'); ?></span>
            </div>
        </div>
        
        <hr class="my-4 border-secondary">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Hi Teac Academy. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="<?php echo BASE_URL; ?>privacy.php" class="me-3">Privacy Policy</a>
                <a href="<?php echo BASE_URL; ?>terms.php">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

<script>
    // Initialize AOS animations
    AOS.init({
        once: true,
        duration: 800
    });
</script>

<?php
// Trigger toast if a flash message exists in session
if (isset($_SESSION['flash_msg'])):
    $msg = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']);
?>
<script>
    $(document).ready(function() {
        showToast("<?php echo $msg['type']; ?>", "<?php echo $msg['message']; ?>");
    });
</script>
<?php endif; ?>

</body>
</html>
