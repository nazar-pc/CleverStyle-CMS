// Generated by CoffeeScript 1.4.0

/**
 * @package   Shop
 * @category  modules
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2014, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
*/


(function() {

  $(function() {
    var L, set_attribute_types;
    L = cs.Language;
    set_attribute_types = [1, 2, 6, 9];
    return $('html').on('click', '.cs-shop-attribute-add', function() {
      return $.ajax({
        url: 'api/Shop/admin/attributes/types',
        type: 'get',
        success: function(types) {
          var index, modal, type;
          types = (function() {
            var _results;
            _results = [];
            for (index in types) {
              type = types[index];
              _results.push("<option value=\"" + index + "\">" + type + "</option>");
            }
            return _results;
          })();
          types = types.join('');
          modal = $.cs.simple_modal("<h3 class=\"cs-center\">" + L.shop_attribute_addition + "</h3>\n<p>\n	" + L.shop_attribute_type + ": <select name=\"type\">" + types + "</select>\n</p>\n<p>\n	" + L.shop_possible_values + ": <textarea name=\"value\"></textarea>\n</p>\n<p>\n	" + L.shop_title + ": <input name=\"title\">\n</p>\n<p>\n	" + L.shop_internal_title + ": <input name=\"internal_title\">\n</p>\n<p>\n	<button class=\"uk-button\">" + L.shop_add + "</button>\n</p>");
          return modal.on('click', 'button', function() {
            var value;
            type = modal.find('[name=type]').val();
            value = set_attribute_types.indexOf(parseInt(type)) !== -1 ? modal.find('[name=value]').val().split('\n') : '';
            return $.ajax({
              url: 'api/Shop/admin/attributes',
              type: 'post',
              data: {
                type: type,
                title: modal.find('[name=title]').val(),
                internal_title: modal.find('[name=internal_title]').val(),
                value: value
              },
              success: function() {
                alert(L.shop_added_successfully);
                return location.reload();
              }
            });
          }).on('change', '[name=type]', function() {
            var value_container;
            value_container = $(this).parent().next();
            type = $(this).val();
            if (set_attribute_types.indexOf(parseInt(type)) !== -1) {
              return value_container.show();
            } else {
              return value_container.hide();
            }
          });
        }
      });
    }).on('click', '.cs-shop-attribute-edit', function() {
      var id;
      id = $(this).data('id');
      return $.ajax({
        url: 'api/Shop/admin/attributes/types',
        type: 'get',
        success: function(types) {
          return $.ajax({
            url: "api/Shop/admin/attributes/" + id,
            type: 'get',
            success: function(attribute) {
              var index, modal, type;
              types = (function() {
                var _results;
                _results = [];
                for (index in types) {
                  type = types[index];
                  _results.push("<option value=\"" + index + "\">" + type + "</option>");
                }
                return _results;
              })();
              types = types.join('');
              modal = $.cs.simple_modal("<h3 class=\"cs-center\">" + L.shop_attribute_addition + "</h3>\n<p>\n	" + L.shop_attribute_type + ": <select name=\"type\">" + types + "</select>\n</p>\n<p>\n	" + L.shop_possible_values + ": <textarea name=\"value\"></textarea>\n</p>\n<p>\n	" + L.shop_title + ": <input name=\"title\">\n</p>\n<p>\n	" + L.shop_internal_title + ": <input name=\"internal_title\">\n</p>\n<p>\n	<button class=\"uk-button\">" + L.shop_edit + "</button>\n</p>");
              modal.on('click', 'button', function() {
                var value;
                type = modal.find('[name=type]').val();
                value = set_attribute_types.indexOf(parseInt(type)) !== -1 ? modal.find('[name=value]').val().split('\n') : '';
                return $.ajax({
                  url: "api/Shop/admin/attributes/" + id,
                  type: 'put',
                  data: {
                    type: type,
                    title: modal.find('[name=title]').val(),
                    internal_title: modal.find('[name=internal_title]').val(),
                    value: value
                  },
                  success: function() {
                    alert(L.shop_edited_successfully);
                    return location.reload();
                  }
                });
              }).on('change', '[name=type]', function() {
                var value_container;
                value_container = $(this).parent().next();
                type = $(this).val();
                if (set_attribute_types.indexOf(parseInt(type)) !== -1) {
                  return value_container.show();
                } else {
                  return value_container.hide();
                }
              });
              modal.find('[name=type]').val(attribute.type).change();
              modal.find('[name=value]').val(attribute.value ? attribute.value.join('\n') : '');
              modal.find('[name=title]').val(attribute.title);
              return modal.find('[name=internal_title]').val(attribute.internal_title);
            }
          });
        }
      });
    }).on('click', '.cs-shop-attribute-delete', function() {
      var id;
      id = $(this).data('id');
      if (confirm(L.shop_sure_want_to_delete)) {
        return $.ajax({
          url: "api/Shop/admin/attributes/" + id,
          type: 'delete',
          success: function() {
            alert(L.shop_deleted_successfully);
            return location.reload();
          }
        });
      }
    });
  });

}).call(this);
