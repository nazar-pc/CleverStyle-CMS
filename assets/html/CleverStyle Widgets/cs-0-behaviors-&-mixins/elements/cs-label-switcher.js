// Generated by LiveScript 1.5.0
/**
 * @package   CleverStyle Widgets
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015-2017, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
(function(){
  Polymer.cs.behaviors.csLabelSwitcher = [
    Polymer.cs.behaviors.label, Polymer.cs.behaviors.tooltip, Polymer.cs.behaviors.injectLightStyles, {
      _styles_dom_module: 'cs-label-switcher-styles',
      attached: function(){
        if (this.querySelector('cs-icon')) {
          return;
        }
        this.querySelector('input').insertAdjacentHTML('afterend', '<cs-icon icon="check" mono></cs-icon>');
        if (this.querySelector('input').disabled) {
          this.querySelector('label').setAttribute('disabled', '');
        }
      }
    }
  ];
}).call(this);
