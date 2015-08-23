// Generated by CoffeeScript 1.9.3

/**
 * @package   CleverStyle Widgets
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */

(function() {
  Polymer({
    'is': 'cs-section-switcher',
    'extends': 'section',
    properties: {
      selected: {
        observer: 'active_changed',
        type: Number
      }
    },
    ready: function() {
      return (function(_this) {
        return function() {
          var element, i, len, ref;
          ref = _this.children;
          for (i = 0, len = ref.length; i < len; i++) {
            element = ref[i];
            if (element.active) {
              return;
            }
          }
          _this.selected = 0;
        };
      })(this)();
    },
    active_changed: function() {
      var element, i, index, len, ref;
      ref = this.children;
      for (index = i = 0, len = ref.length; i < len; index = ++i) {
        element = ref[index];
        if (element.tagName === 'TEMPLATE') {
          continue;
        }
        element.active = index === this.selected;
        if (index === this.selected) {
          element.setAttribute('active', '');
        } else {
          element.removeAttribute('active');
        }
      }
    }
  });

}).call(this);
