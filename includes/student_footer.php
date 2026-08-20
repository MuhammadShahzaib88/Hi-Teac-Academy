<?php
// includes/student_footer.php
// Footer and JS scripts for Student Dashboard
?>
    </div> <!-- End of .dashboard-content -->
</div> <!-- End of .dashboard-wrapper -->

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

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
