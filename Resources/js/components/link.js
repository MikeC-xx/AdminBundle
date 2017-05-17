'use strict';

(function ($) {
  var methods = {
    init: function () {

      return this.each(function () {
        var $this = $(this);

        $this.addClass('link');

        $this.on('click', function () {
          window.location = $this.data('href');
        });

      });

    }

  };

  $.fn.link = function (methodOrOptions) {
    if (methods[methodOrOptions]) {
      return methods[methodOrOptions].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof methodOrOptions === 'object' || !methodOrOptions) {
      return methods.init.apply(this, arguments);
    } else {
      $.error('Method ' + methodOrOptions + ' does not exists on jQuery.link');
    }

  };

})(jQuery);
