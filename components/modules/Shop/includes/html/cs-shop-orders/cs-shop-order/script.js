// Generated by CoffeeScript 1.4.0

/**
 * @package       Shop
 * @order_status  modules
 * @author        Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright     Copyright (c) 2014, Nazar Mokrynskyi
 * @license       MIT License, see license.txt
*/


(function() {
  var L;

  L = cs.Language;

  Polymer({
    shipping_type_text: L.shop_shipping_type,
    total_price_text: L.shop_total_price,
    discount_text: L.shop_discount,
    shipping_cost_text: L.shop_shipping_cost,
    for_payment_text: L.shop_for_payment,
    ready: function() {
      var $this, discount, for_payment, shipping_type, total_price;
      $this = $(this);
      this.order_number = sprintf(L.shop_order_number, $this.data('id'));
      this.order_date = $this.data('date-formatted');
      this.order_status = $this.children('#order_status').text();
      shipping_type = $this.children('#shipping_type');
      this.shipping_type = shipping_type.text();
      this.shipping_cost = shipping_type.data('price');
      this.shipping_cost_formatted = sprintf(cs.shop.settings.price_formatting, this.shipping_cost);
      total_price = 0;
      discount = 0;
      $this.find('cs-shop-order-item').each(function() {
        var price, unit_price, units;
        $this = $(this);
        units = $this.data('units');
        unit_price = $this.data('unit-price');
        price = $this.data('price');
        total_price += units * unit_price;
        return discount += (units * unit_price) - price;
      });
      for_payment = total_price - discount + this.shipping_cost;
      this.total_price_formatted = sprintf(cs.shop.settings.price_formatting, total_price);
      this.discount_formatted = discount ? sprintf(cs.shop.settings.price_formatting, discount) : '';
      return this.for_payment_formatted = sprintf(cs.shop.settings.price_formatting, for_payment);
    }
  });

}).call(this);
