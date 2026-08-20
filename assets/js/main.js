/* assets/js/main.js */
/* Client-Side JavaScript Logic for Hi Teac Academy Management System */

$(document).ready(function() {
    
    // 1. Password Confirmation Check
    $('form').on('submit', function(e) {
        var password = $(this).find('input[name="password"]').val();
        var confirmPassword = $(this).find('input[name="confirm_password"]').val();
        
        if (password && confirmPassword && password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match! Please check and try again.');
            return false;
        }
    });

    // 2. Real-time Client-Side Table Search (Filtering rows)
    // Add data-search-table=".table-class" data-search-input="#search-box"
    $('[data-search-table]').on('keyup', function() {
        var query = $(this).val().toLowerCase();
        var targetTableSelector = $(this).data('search-table');
        
        $(targetTableSelector + ' tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // 3. Image Upload Preview
    // Class `.image-preview-input` and image element `#image-preview`
    $('.image-preview-input').on('change', function() {
        var input = this;
        var previewSelector = $(this).data('preview-target') || '#image-preview';
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(previewSelector).attr('src', e.target.result).removeClass('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    });

    // 4. Print Command Helper
    $('.btn-print-slip').on('click', function(e) {
        e.preventDefault();
        window.print();
    });

    // 5. Toast Notifications System
    window.showToast = function(type, message) {
        var toastClass = 'bg-primary';
        if (type === 'success') toastClass = 'bg-success';
        if (type === 'danger' || type === 'error') toastClass = 'bg-danger';
        if (type === 'warning') toastClass = 'bg-warning text-dark';
        if (type === 'info') toastClass = 'bg-info';

        var toastHTML = `
        <div class="toast align-items-center text-white ${toastClass} border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>`;

        var $toast = $(toastHTML);
        if ($('#toast-container').length === 0) {
            $('body').append('<div id="toast-container"></div>');
        }
        $('#toast-container').append($toast);
        var bootstrapToast = new bootstrap.Toast($toast[0], { delay: 5000 });
        bootstrapToast.show();
        
        $toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    };

    // 6. Tooltips Initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 7. Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $(".alert-dismissible").fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
});
