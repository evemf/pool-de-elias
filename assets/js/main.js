(function ($) {
    'use strict';

    $(document).ready(function () {
        $('.pde-header-cart').on('click', function (event) {
            event.preventDefault();
            const $miniCart = $(this).next('.pde-mini-cart');
            $miniCart.attr('aria-hidden', function (_, attr) {
                return attr === 'true' ? 'false' : 'true';
            }).toggleClass('is-open');
        });

        $('.pde-dashboard__sidebar a').on('click', function (event) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({ scrollTop: target.offset().top - 80 }, 400);
            }
        });
    });
})(jQuery);
