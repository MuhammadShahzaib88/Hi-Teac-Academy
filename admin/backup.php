<?php
// admin/backup.php
// Administrator Database Backup Manager
// Uses pure PHP PDO approach — no exec() or shell commands required.

$page_title = "Database Backup";
require_once dirname(__DIR__) . '/config/config.php';
require_admin_login();

// ============================================================
// DOWNLOAD HANDLER: triggered when "Download Backup" is clicked
// ============================================================
if (isset($_GET['download']) && $_GET['download'] === '1') {
    // Verify CSRF from GET param (basic nonce check)
    if (!isset($_GET['token']) || !verify_csrf_token($_GET['token'])) {
        die("CSRF Token validation failed.");
    }

    $filename = 'hi_teac_backup_' . date('Y-m-d_His') . '.sql';

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // ── Helper: escape SQL string values ──────────────────────
    function sql_escape_string(PDO $pdo, $value): string {
        if (is_null($value)) return 'NULL';
        $quoted = $pdo->quote($value);
        return $quoted;
    }

    // ── SQL Dump Header ───────────────────────────────────────
    $output  = "-- =========================================================\n";
    $output .= "-- Hi Teac Academy — Database Backup\n";
    $output .= "-- Generated : " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Database  : " . DB_NAME . "\n";
    $output .= "-- =========================================================\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    try {
        // Get all table names in this database
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $table_safe = "`$table`";

            // DROP + CREATE statement
            $create_result = $pdo->query("SHOW CREATE TABLE $table_safe")->fetch(PDO::FETCH_NUM);
            $output .= "\n-- -------------------------------------------------------\n";
            $output .= "-- Table: $table\n";
            $output .= "-- -------------------------------------------------------\n";
            $output .= "DROP TABLE IF EXISTS $table_safe;\n";
            $output .= $create_result[1] . ";\n\n";

            // INSERT statements for each row
            $rows = $pdo->query("SELECT * FROM $table_safe")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $columns = '`' . implode('`, `', array_keys($rows[0])) . '`';
                $output .= "-- Data for table: $table\n";

                $row_chunks = array_chunk($rows, 100); // batch of 100 rows per INSERT
                foreach ($row_chunks as $chunk) {
                    $values_list = [];
                    foreach ($chunk as $row) {
                        $row_values = array_map(fn($val) => sql_escape_string($pdo, $val), array_values($row));
                        $values_list[] = '(' . implode(', ', $row_values) . ')';
                    }
                    $output .= "INSERT INTO $table_safe ($columns) VALUES\n";
                    $output .= implode(",\n", $values_list) . ";\n";
                }
                $output .= "\n";
            }
        }
    } catch (PDOException $e) {
        $output .= "\n-- ERROR during backup: " . $e->getMessage() . "\n";
    }

    $output .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
    $output .= "\n-- =========================================================\n";
    $output .= "-- Backup completed successfully.\n";
    $output .= "-- =========================================================\n";

    echo $output;
    exit;
}

// ============================================================
// NORMAL PAGE LOAD: show backup information and controls
// ============================================================
// Collect DB stats for display
try {
    $tables       = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $table_count  = count($tables);

    $db_size_stmt = $pdo->prepare("
        SELECT ROUND(SUM(data_length + index_length) / 1024, 2) AS size_kb
        FROM information_schema.TABLES
        WHERE table_schema = ?
    ");
    $db_size_stmt->execute([DB_NAME]);
    $db_size = $db_size_stmt->fetchColumn() ?? 'N/A';

    // Row counts per table
    $table_stats = [];
    foreach ($tables as $table) {
        $cnt = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $table_stats[$table] = $cnt;
    }
} catch (PDOException $e) {
    $tables      = [];
    $table_count = 0;
    $db_size     = 'N/A';
    $table_stats = [];
}

$csrf_token = generate_csrf_token();

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<div class="row g-4">

    <!-- Backup Action Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-database text-primary me-2"></i>Database Backup</h5>
            </div>
            <div class="card-body p-4 text-center">

                <div class="py-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:90px;height:90px;background:linear-gradient(135deg,#0b3c5d,#328cc1);">
                        <i class="fa fa-download fa-2x text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Generate SQL Backup</h5>
                    <p class="text-muted small mb-4">
                        Download a complete SQL dump of the <strong><?php echo DB_NAME; ?></strong> database,
                        including all table structures and data records. Backup is generated in real-time using PHP.
                    </p>

                    <!-- Stats Summary -->
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <div class="border rounded-3 p-3">
                                <h4 class="fw-bold text-primary mb-0"><?php echo $table_count; ?></h4>
                                <small class="text-muted">Tables</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3">
                                <h4 class="fw-bold text-success mb-0"><?php echo array_sum($table_stats); ?></h4>
                                <small class="text-muted">Total Rows</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3">
                                <h4 class="fw-bold text-info mb-0"><?php echo $db_size; ?></h4>
                                <small class="text-muted">Size (KB)</small>
                            </div>
                        </div>
                    </div>

                    <a href="?download=1&token=<?php echo urlencode($csrf_token); ?>"
                       class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm">
                        <i class="fa fa-download me-2"></i> Download Backup (.sql)
                    </a>
                </div>

                <hr>
                <div class="text-start">
                    <p class="small text-muted mb-1"><i class="fa fa-info-circle text-info me-1"></i> The backup contains:</p>
                    <ul class="small text-muted ps-4 mb-0">
                        <li>DROP and CREATE statements for each table</li>
                        <li>All data rows as INSERT statements</li>
                        <li>UTF-8 / utf8mb4 encoding</li>
                        <li>Compatible with MySQL / MariaDB / phpMyAdmin</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Overview Card -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-table text-primary me-2"></i>Database Tables Overview</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($table_stats)): ?>
                    <p class="text-muted text-center py-5">Could not fetch table information.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Table Name</th>
                                    <th class="text-center">Row Count</th>
                                    <th class="text-end">Backup Included</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach ($table_stats as $tbl => $count): ?>
                                    <tr>
                                        <td class="text-muted small"><?php echo $i++; ?></td>
                                        <td><code class="text-dark"><?php echo htmlspecialchars($tbl); ?></code></td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border"><?php echo number_format($count); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-success text-white px-2 py-1 rounded-pill">
                                                <i class="fa fa-check me-1"></i> Yes
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold text-dark">TOTAL</td>
                                    <td class="text-center fw-bold"><?php echo number_format(array_sum($table_stats)); ?></td>
                                    <td class="text-end text-muted small"><?php echo $table_count; ?> tables</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Restore Instructions -->
        <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-upload text-warning me-2"></i>How to Restore Backup</h5>
            </div>
            <div class="card-body p-4">
                <ol class="text-muted small mb-0 ps-3">
                    <li class="mb-2">Open <strong>phpMyAdmin</strong> in your browser.</li>
                    <li class="mb-2">Select (or create) the target database: <code><?php echo DB_NAME; ?></code></li>
                    <li class="mb-2">Click the <strong>Import</strong> tab at the top.</li>
                    <li class="mb-2">Click <strong>Choose File</strong> and select the downloaded <code>.sql</code> backup file.</li>
                    <li class="mb-0">Click <strong>Go / Import</strong> to restore all data.</li>
                </ol>
                <div class="alert alert-warning alert-sm mt-3 mb-0 small py-2">
                    <i class="fa fa-exclamation-triangle me-1"></i>
                    <strong>Warning:</strong> Restoring will DROP and recreate existing tables. Always backup current data before importing.
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/admin_footer.php'; ?>
