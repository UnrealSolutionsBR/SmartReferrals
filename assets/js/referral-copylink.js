jQuery(document).ready(function($) {
    $('#sr-copy-button').on('click', function(e) {
        e.preventDefault();

        // Seleccionar el contenido del textarea
        var $textarea = $('.sr-referral-input');
        $textarea.focus();
        $textarea.select();

        try {
            // Intentar copiar al portapapeles
            var successful = document.execCommand('copy');
            if (successful) {
                // Mostrar notificación
                if ($('.sr-copylink-notification').length === 0) {
                    $('body').append('<div class="sr-copylink-notification">' + srCopyLink.copiedText + '<span class="sr-close-notification">×</span></div>');
                }
                var notification = $('.sr-copylink-notification');

                // Ajustar posición de la notificación
                var headerHeight = $('header').outerHeight() || $('#wpadminbar').outerHeight() || 0;
                notification.css('top', (headerHeight + 40) + 'px');
                notification.addClass('show');

                // Cerrar notificación al hacer clic
                notification.find('.sr-close-notification').on('click', function() {
                    notification.removeClass('show');
                });

                // Ocultar automáticamente después de 5 segundos
                setTimeout(function() {
                    notification.removeClass('show');
                }, 5000);
            } else {
                console.error('Failed to copy text.');
            }
        } catch (err) {
            console.error('Error copying text: ', err);
        }
    });
});
