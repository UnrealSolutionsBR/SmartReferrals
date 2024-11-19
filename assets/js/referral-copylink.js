jQuery(document).ready(function($) {
    $('#sr-copy-button').on('click', function(e) {
        e.preventDefault();
        var copyText = $('#sr-referral-link');
        copyText.select();
        document.execCommand('copy');

        // Show notification
        if ( $('.sr-copylink-notification').length === 0 ) {
            $('body').append('<div class="sr-copylink-notification">' + srCopyLink.copiedText + '<span class="sr-close-notification">×</span></div>');
        }
        var notification = $('.sr-copylink-notification');
        notification.addClass('show');

        // Close notification on click
        notification.find('.sr-close-notification').on('click', function() {
            notification.removeClass('show');
        });

        // Automatically hide after 5 seconds
        setTimeout(function() {
            notification.removeClass('show');
        }, 5000);
    });
});
