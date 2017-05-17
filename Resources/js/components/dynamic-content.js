'use strict';

(function ($) {
  var methods = {
    init: function () {
      this.each(function () {
        var $this = $(this);
        var $formFilter = $this.find('[data-dynamic-content-filter]');

        $formFilter.on('submit', function (e) {
          e.preventDefault();
          $formFilter.find('[name="page"]').val(1);
          return methods.reload.apply($this);
        });

        $this.data('form-filter', $formFilter);
      });

      return methods.reload.apply(this);
    },

    replaceContent: function (data) {
      var _this = this;

      _this.find('tbody').remove();
      _this.find('tfoot').remove();
      _this.append(data);
      _this.find('[data-href]').link();
      _this.find('[data-page]').on('click', function (e) {
        e.preventDefault();
        _this.data('form-filter').find('[name="page"]').val($(this).data('page'));
        methods.reload.apply(_this);
      });

    },

    reload: function () {
      return this.each(function () {
        var $this = $(this);
        var params = {};

        methods.replaceContent.apply($this, [$this.data('dynamic-content-placeholder')]);

        $this.data('form-filter').serializeArray().forEach(function (param) {
          params[param.name] = param.value;
        });

        $.post(
          $this.data('dynamic-content'),
          Object.assign({}, params, {
            columnArray: $this.data('column-array')
          }),
          function (data) {
            methods.replaceContent.apply($this, [data]);
          }
        );

      });

    }

  };

  $.fn.dynamicContent = function (methodOrOptions) {
    if (methods[methodOrOptions]) {
      return methods[methodOrOptions].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof methodOrOptions === 'object' || !methodOrOptions) {
      return methods.init.apply(this, arguments);
    } else {
      $.error('Method ' + methodOrOptions + ' does not exists on jQuery.dynamicContent');
    }

  };

})(jQuery);
