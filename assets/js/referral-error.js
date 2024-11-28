jQuery(document).ready(function($) {
    if (srReferralError.errorMessage) {
        // Usar el mismo sistema de notificación que el de copiar enlace
        if ($('.sr-copylink-notification').length === 0) {
            $('body').append('<div class="sr-copylink-notification">' + srReferralError.errorMessage + '<span class="sr-close-notification">×</span></div>');
        }
        var notification = $('.sr-copylink-notification');

        // Ajustar posición según el dispositivo
        if ($(window).width() <= 768) {
            // Para dispositivos móviles, 10px desde la parte superior
            notification.css('top', '10px');
        } else {
            // Para escritorio, 20px debajo del encabezado o barra de administración
            var headerHeight = $('header').outerHeight() || $('#wpadminbar').outerHeight() || 0;
            notification.css('top', (headerHeight + 40) + 'px');
        }

        notification.addClass('show');

        // Cerrar notificación al hacer clic
        notification.find('.sr-close-notification').on('click', function() {
            notification.removeClass('show');
        });

        // Ocultar automáticamente después de 5 segundos
        setTimeout(function() {
            notification.removeClass('show');
        }, 5000);
    }
});