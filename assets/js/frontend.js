(function($) {
  'use strict';

  $(function() {
    var frontapp = {
      scroll_position : parseInt(ucat_ns.scroll_position),
      scroll_unit     : ucat_ns.scroll_position_unit,
      init: function () {
        if( frontapp.scroll_position > 0 ){
          frontapp.showOnScroll();
          $(window).scroll(frontapp.showOnScroll);
        }
      },
      showOnScroll : function () {
        var position  = frontapp.scroll_position;
        var scrollTop = $(window).scrollTop();

        if( frontapp.scroll_unit === '%') {
          var documentHeight    = $(document).height();
          scrollTop = ( scrollTop / documentHeight ) * 100;
        }

        if( scrollTop > position) {
          $('nav.u_next_story').show();
        } else {
          $('nav.u_next_story').hide();
        }
      }
    };

    frontapp.init();

  });

}(jQuery));