(function ($) {
    'use strict';

    $(function () {
        var frontapp = {
            scroll_position: parseInt(ucat_ns.scroll_position),
            scroll_unit: ucat_ns.scroll_position_unit,
            window_height: $(window).height(),
            init: function () {
                if (frontapp.scroll_position > 0) {
                    frontapp.showOnScroll();
                    $(window).on('scroll', frontapp.showOnScroll);
                    $(window).on('resize', frontapp.onResize);
                }
            },
            onResize: function (){
                frontapp.window_height = $(window).height();
            },
            showOnScroll: function () {
                let scrollTop = $(window).scrollTop();

                if (frontapp.scroll_unit === '%') {
                    const documentHeight = $(document).height();
                    const halfWindowHeight = frontapp.window_height/2;
                    scrollTop = ( (scrollTop + halfWindowHeight) / documentHeight) * 100;
                }

                if (scrollTop > frontapp.scroll_position) {
                    $('nav.u_next_story').show();
                } else {
                    $('nav.u_next_story').hide();
                }
            }
        };

        frontapp.init();

    });

}(jQuery));
