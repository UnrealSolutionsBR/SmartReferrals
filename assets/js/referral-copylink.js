jQuery(document).ready(function($) {
    $('#sr-copy-button').on('click', function(e) {
        e.preventDefault();
        var copyText = $('#sr-referral-link').text(); // Obtener texto del span

        // Copiar el texto al portapapeles
        navigator.clipboard.writeText(copyText).then(function() {
            // Mostrar notificación
            if ($('.sr-copylink-notification').length === 0) {
                $('body').append('<div class="sr-copylink-notification">' + srCopyLink.copiedText + '<span class="sr-close-notification">×</span></div>');
            }
            var notification = $('.sr-copylink-notification');

            // Ajustar posición 20px debajo del header
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
        }).catch(function(err) {
            console.error('Error al copiar el texto: ', err);
        });
    });
});
