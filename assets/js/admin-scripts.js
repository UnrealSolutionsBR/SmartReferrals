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
            url: ajaxurl,
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
                
                // Update the admin menu via AJAX
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'sr_update_admin_menu',
                        security: srAdminAjax.nonce
                    },
                    success: function(menuHTML) {
                        // Replace the menu with new HTML
                        $('#adminmenu').html(menuHTML);
                        
                        // Animate the submenu items
                        $('#adminmenu .wp-submenu-wrap').css('opacity', 0).animate({ opacity: 1 }, 500);
                    }
                });
            }
        });
    });
});
