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

            // Ajustar posición según el dispositivo
            if ($(window).width() <= 768) {
                // Para móviles, 10px desde la parte superior
                notification.css('top', '10px');
            } else {
                // Para desktop, 20px debajo del header o admin bar
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
        }).catch(function(err) {
            console.error('Error al copiar el texto: ', err);
        });
    });
});
