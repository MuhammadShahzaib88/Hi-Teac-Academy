<?php
// gallery.php
// Gallery with Category Filter

$page_title = "Gallery";
require_once 'config/config.php';
include 'includes/header.php';

// Fetch all gallery items
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
    $gallery_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $gallery_items = [];
}
?>

<!-- Page Banner -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, var(--navy-blue) 0%, var(--light-blue) 100%);">
    <div class="container text-center py-3">
        <h1 class="display-4 fw-bold">Campus Gallery</h1>
        <p class="lead mb-0">Explore our high-tech laboratories, classrooms, and event ceremonies</p>
    </div>
</section>

<!-- Gallery Filter Grid -->
<section class="py-5">
    <div class="container">
        
        <!-- Filter Controls -->
        <div class="row mb-5 text-center">
            <div class="col-12">
                <button class="btn btn-primary rounded-pill px-4 filter-btn me-2 mb-2 active" data-filter="all">All Photos</button>
                <button class="btn btn-outline-primary rounded-pill px-4 filter-btn me-2 mb-2" data-filter="Labs">Labs</button>
                <button class="btn btn-outline-primary rounded-pill px-4 filter-btn me-2 mb-2" data-filter="Classrooms">Classrooms</button>
                <button class="btn btn-outline-primary rounded-pill px-4 filter-btn me-2 mb-2" data-filter="Events">Events</button>
                <button class="btn btn-outline-primary rounded-pill px-4 filter-btn me-2 mb-2" data-filter="Campus">Campus</button>
            </div>
        </div>
        
        <!-- Gallery Items Row -->
        <div class="row g-4" id="gallery-grid">
            <?php if (empty($gallery_items)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fa fa-images text-muted fa-4x mb-3"></i>
                    <p class="text-muted">No images uploaded in the gallery yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($gallery_items as $item): 
                    $img_url = !empty($item['image_path']) && file_exists(ROOT_PATH . $item['image_path']) 
                        ? BASE_URL . $item['image_path'] 
                        : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=800&auto=format&fit=crop&q=60';
                ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 gallery-card" data-category="<?php echo sanitize($item['category']); ?>">
                        <div class="gallery-item">
                            <img src="<?php echo $img_url; ?>" alt="<?php echo sanitize($item['title']); ?>">
                            <div class="gallery-overlay">
                                <span class="badge bg-primary mb-2"><?php echo sanitize($item['category']); ?></span>
                                <h5 class="fw-bold mb-0 text-white"><?php echo sanitize($item['title']); ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>
</section>

<!-- jQuery Filtering Script -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.filter-btn').on('click', function() {
        // Toggle Active Button Class
        $('.filter-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
        
        var selectedFilter = $(this).data('filter');
        
        if (selectedFilter === 'all') {
            $('.gallery-card').fadeIn(300);
        } else {
            $('.gallery-card').each(function() {
                var itemCategory = $(this).data('category');
                if (itemCategory === selectedFilter) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
