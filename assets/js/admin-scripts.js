jQuery(document).ready(function($) {
    $('.sr-module-card').on('change', '#sr-module-toggle', function() {
        var isChecked = $(this).is(':checked') ? 'yes' : 'no';
        var $toggle = $(this);
        var $switchLabel = $toggle.closest('.sr-switch');
        var $slider = $switchLabel.find('.sr-slider');
        var $loadingCircle = $switchLabel.find('.sr-loading-circle');

        // Disable the toggle to prevent rapid clicks
        $toggle.prop('disabled', true);

        // Show loading circle and hide slider
        $slider.hide();
        $loadingCircle.show();

        // AJAX request to update the option and menu
        $.ajax({
            url: srAdminAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'sr_toggle_module',
                enabled: isChecked,
                security: srAdminAjax.nonce
            },
            success: function(response) {
                // Hide loading circle and show slider again
                $loadingCircle.hide();
                $slider.show();
                $toggle.prop('disabled', false);

                // Update the submenu item visibility
                if (response.success && response.data) {
                    if (response.data.enabled === 'yes') {
                        // Show the submenu item with animation
                        $('li.toplevel_page_sr-dashboard ul.wp-submenu li a[href="admin.php?page=sr-referrals"]').parent().fadeIn();
                    } else {
                        // Hide the submenu item with animation
                        $('li.toplevel_page_sr-dashboard ul.wp-submenu li a[href="admin.php?page=sr-referrals"]').parent().fadeOut();
                    }
                }
            }
        });
    });
});
