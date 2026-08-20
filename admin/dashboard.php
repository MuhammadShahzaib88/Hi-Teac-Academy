<?php
// admin/dashboard.php
// Administrator Dashboard Home

$page_title = "Admin Dashboard";
require_once dirname(__DIR__) . '/includes/admin_header.php';

// 1. Fetch count statistics
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_admissions = $pdo->query("SELECT COUNT(*) FROM admissions")->fetchColumn();
$pending_admissions = $pdo->query("SELECT COUNT(*) FROM admissions WHERE status = 'Pending'")->fetchColumn();
$total_questions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$answered_questions = $pdo->query("SELECT COUNT(*) FROM questions WHERE status = 'Answered'")->fetchColumn();

// 2. Fetch Recent Students
$recent_students = $pdo->query("SELECT * FROM students ORDER BY id DESC LIMIT 5")->fetchAll();

// 3. Fetch Recent Admissions
$recent_admissions = $pdo->query("
    SELECT a.*, s.first_name, s.last_name, c.name as course_name
    FROM admissions a
    JOIN students s ON a.student_id = s.id
    JOIN courses c ON a.course_id = c.id
    ORDER BY a.id DESC LIMIT 5
")->fetchAll();

// 4. Fetch Recent Questions
$recent_questions = $pdo->query("
    SELECT q.*, s.first_name, s.last_name
    FROM questions q
    JOIN students s ON q.student_id = s.id
    ORDER BY q.id DESC LIMIT 5
")->fetchAll();

// 5. Data for ChartJS course enrollment statistics
$dit_count = $pdo->query("SELECT COUNT(*) FROM admissions WHERE course_id = 1")->fetchColumn();
$cit_count = $pdo->query("SELECT COUNT(*) FROM admissions WHERE course_id = 2")->fetchColumn();
?>

<!-- Statistics Counter Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-2">
        <div class="stat-card stat-card-primary text-center">
            <h3 class="fw-bold mb-1"><?php echo $total_students; ?></h3>
            <span class="small text-uppercase">Total Students</span>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card stat-card-info text-center">
            <h3 class="fw-bold mb-1"><?php echo $total_courses; ?></h3>
            <span class="small text-uppercase">Total Courses</span>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card stat-card-success text-center">
            <h3 class="fw-bold mb-1"><?php echo $total_admissions; ?></h3>
            <span class="small text-uppercase">Admissions</span>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card stat-card-warning text-center">
            <h3 class="fw-bold mb-1"><?php echo $pending_admissions; ?></h3>
            <span class="small text-uppercase">Pending Adm.</span>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card stat-card-danger text-center">
            <h3 class="fw-bold mb-1"><?php echo $total_questions; ?></h3>
            <span class="small text-uppercase">Questions</span>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card stat-card-success text-center">
            <h3 class="fw-bold mb-1"><?php echo $answered_questions; ?></h3>
            <span class="small text-uppercase">Answered Qs</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart Column -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-chart-pie text-primary me-2"></i>Course Enrollments Ratio</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                <div style="width: 250px; height: 250px;">
                    <canvas id="courseRatioChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Students List -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-user-graduate text-primary me-2"></i>Recent Student Signups</h5>
                <a href="<?php echo BASE_URL; ?>admin/students.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Signup Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_students as $student): ?>
                                <tr>
                                    <td><span class="fw-semibold text-dark"><?php echo sanitize($student['first_name'] . ' ' . $student['last_name']); ?></span></td>
                                    <td><?php echo sanitize($student['email']); ?></td>
                                    <td><?php echo format_date($student['created_at'], 'd M Y'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Admissions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-file-invoice text-primary me-2"></i>Recent Course Applications</h5>
                <a href="<?php echo BASE_URL; ?>admin/admissions.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_admissions as $adm): 
                                $badge = 'badge-pending';
                                if ($adm['status'] === 'Approved') $badge = 'badge-approved';
                                if ($adm['status'] === 'Rejected') $badge = 'badge-rejected';
                            ?>
                                <tr>
                                    <td><span class="fw-semibold text-dark"><?php echo sanitize($adm['first_name'] . ' ' . $adm['last_name']); ?></span></td>
                                    <td><?php echo sanitize($adm['course_name']); ?></td>
                                    <td><span class="badge <?php echo $badge; ?> px-2 py-1 rounded-pill small"><?php echo $adm['status']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Questions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-question-circle text-primary me-2"></i>Recent Questions Asked</h5>
                <a href="<?php echo BASE_URL; ?>admin/questions.php" class="btn btn-sm btn-outline-primary rounded-pill">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_questions as $q): 
                                $badge = ($q['status'] === 'Answered') ? 'bg-success' : 'bg-warning text-dark';
                            ?>
                                <tr>
                                    <td><span class="fw-semibold text-dark"><?php echo sanitize($q['first_name'] . ' ' . $q['last_name']); ?></span></td>
                                    <td><?php echo sanitize(substr($q['subject'], 0, 30)); ?>...</td>
                                    <td><span class="badge <?php echo $badge; ?> px-2 py-1 rounded-pill small"><?php echo $q['status']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script to generate Chart.js charts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    var ctx = document.getElementById('courseRatioChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['DIT Course', 'CIT Course'],
            datasets: [{
                data: [<?php echo $dit_count; ?>, <?php echo $cit_count; ?>],
                backgroundColor: [
                    '#0b3c5d',
                    '#328cc1'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
