jQuery(document).ready(function($) {
    $('#sr-copy-button').on('click', function(e) {
        e.preventDefault();
        var copyText = $('#sr-referral-link').val();

        // Copy the text to clipboard without selecting the input
        navigator.clipboard.writeText(copyText).then(function() {
            // Show notification
            if ( $('.sr-copylink-notification').length === 0 ) {
                $('body').append('<div class="sr-copylink-notification">' + srCopyLink.copiedText + '<span class="sr-close-notification">×</span></div>');
            }
            var notification = $('.sr-copylink-notification');

            // Adjust position 20px below the header
            var headerHeight = $('header').outerHeight() || $('#wpadminbar').outerHeight() || 0;
            notification.css('top', (headerHeight + 40) + 'px');
            notification.addClass('show');

            // Close notification on click
            notification.find('.sr-close-notification').on('click', function() {
                notification.removeClass('show');
            });

            // Automatically hide after 5 seconds
            setTimeout(function() {
                notification.removeClass('show');
            }, 5000);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    });
});
