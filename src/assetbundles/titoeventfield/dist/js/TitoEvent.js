/**
 * Tito plugin for Craft CMS
 *
 * TitoEvent Field JS
 *
 * @author    Eli Van Zoeren
 * @copyright Copyright (c) 2018 Eli Van Zoeren
 * @link      https://elivz.com
 * @package   Tito
 * @since     1.0.0TitoTitoEvent
 */

(function($, window, document, undefined) {
  var pluginName = "TitoEvent",
    defaults = {};

  // Plugin constructor
  function Plugin(element, options) {
    this.element = element;

    this.options = $.extend({}, defaults, options);

    this._defaults = defaults;
    this._name = pluginName;

    this.init();
  }

  Plugin.prototype = {
    init: function(id) {
      $(".tito-event-field", this.element).selectize({});
    }
  };

  // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations
  $.fn[pluginName] = function(options) {
    return this.each(function() {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(this, "plugin_" + pluginName, new Plugin(this, options));
      }
    });
  };
})(jQuery, window, document);
