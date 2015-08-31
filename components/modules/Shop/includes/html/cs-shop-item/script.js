// Generated by CoffeeScript 1.9.3

/**
 * @package   Shop
 * @category  modules
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2014-2015, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */

(function() {
  Polymer({
    'is': 'cs-shop-item',
    'extends': 'section',
    behaviors: [cs.Polymer.behaviors.Language],
    properties: {
      header_title: '',
      item_id: Number,
      price: String,
      in_stock: Number
    },
    ready: function() {
      var attributes;
      this.set('header_title', this.querySelector('h1').textContent);
      this.set('price', sprintf(cs.shop.settings.price_formatting, this.price));
      attributes = $(this.querySelector('#attributes'));
      if (attributes.length) {
        this.show_attributes = true;
        attributes.find('table').addClass('cs-table').attr('hover', '').find('td:first-of-type').addClass('cs-text-bold');
      }
      return $(this.$.images).append($(this.querySelectorAll('#videos > a')).each(function() {
        var $this;
        $this = $(this);
        if ($this.children('img')) {
          return $this.attr('data-video', 'true');
        }
      })).append(this.querySelectorAll('#images > img')).fotorama({
        allowfullscreen: 'native',
        controlsonstart: false,
        fit: 'contain',
        keyboard: true,
        nav: 'thumbs',
        ratio: 4 / 3,
        trackpad: true,
        width: '100%'
      });
    }
  });

}).call(this);
