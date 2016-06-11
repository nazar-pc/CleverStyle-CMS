// Generated by LiveScript 1.4.0
/**
 * @package   Shop
 * @category  modules
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2014-2016, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
(function(){
  $(function(){
    var L, make_modal;
    L = cs.Language('shop_');
    make_modal = function(types, title, action){
      var res$, index, type, modal;
      res$ = [];
      for (index in types) {
        type = types[index];
        res$.push("<option value=\"" + index + "\">" + type + "</option>");
      }
      types = res$;
      types = types.join('');
      modal = $(cs.ui.simple_modal("<form is=\"cs-form\">\n	<h3 class=\"cs-text-center\">" + title + "</h3>\n	<label>" + L.title + "</label>\n	<input is=\"cs-input-text\" name=\"title\" required>\n	<label>" + L.color + "</label>\n	<input is=\"cs-input-text\" name=\"color\"><br>\n	<input is=\"cs-input-text\" type=\"color\">\n	<label>" + L.order_status_type + "</label>\n	<select is=\"cs-select\" name=\"type\" required>" + types + "</select>\n	<label>" + L.send_update_status_email + "</label>\n	<div>\n		<label is=\"cs-label-button\"><input type=\"radio\" name=\"send_update_status_email\" value=\"1\" checked> " + L.yes + "</label>\n		<label is=\"cs-label-button\"><input type=\"radio\" name=\"send_update_status_email\" value=\"0\"> " + L.no + "</label>\n	</div>\n	<label>" + L.comment_used_in_email + "</label>\n	<textarea is=\"cs-textarea\" autosize name=\"comment\"></textarea>\n	<br>\n	<button is=\"cs-button\" primary type=\"submit\">" + action + "</button>\n</form>"));
      modal.find('[type=color]').change(function(){
        modal.find('[name=color]').val($(this).val());
      });
      modal.find('[name=color]').change(function(){
        modal.find('[type=color]').val($(this).val());
      });
      return modal;
    };
    $('html').on('mousedown', '.cs-shop-order-status-add', function(){
      cs.api('get api/Shop/admin/order_statuses/types').then(function(types){
        var modal;
        modal = make_modal(types, L.order_status_addition, L.add);
        modal.find('form').submit(function(){
          cs.api('post api/Shop/admin/order_statuses', this).then(function(){
            alert(L.added_successfully);
            location.reload();
          });
          return false;
        });
      });
    }).on('mousedown', '.cs-shop-order-status-edit', function(){
      var id;
      id = $(this).data('id');
      cs.api(['get api/Shop/admin/order_statuses/types', "get api/Shop/admin/order_statuses/" + id]).then(function(arg$){
        var types, type, modal;
        types = arg$[0], type = arg$[1];
        modal = make_modal(types, L.order_status_edition, L.edit);
        modal.find('form').submit(function(){
          cs.api("put api/Shop/admin/order_statuses/" + id, this).then(function(){
            alert(L.edited_successfully);
            location.reload();
          });
          return false;
        });
        modal.find('[name=title]').val(type.title);
        modal.find('[name=color]').val(type.color);
        modal.find('[type=color]').val(type.color);
        modal.find('[name=type]').val(type.type);
        modal.find("[name=send_update_status_email][value=" + type.send_update_status_email + "]").prop('checked', true);
        modal.find('[name=comment]').val(type.comment);
      });
    }).on('mousedown', '.cs-shop-order-status-delete', function(){
      var id;
      id = $(this).data('id');
      if (confirm(L.sure_want_to_delete)) {
        cs.api("delete api/Shop/admin/order_statuses/" + id).then(function(){
          alert(L.deleted_successfully);
          location.reload();
        });
      }
    });
  });
}).call(this);
