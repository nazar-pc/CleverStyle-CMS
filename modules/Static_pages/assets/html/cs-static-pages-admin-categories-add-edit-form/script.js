// Generated by LiveScript 1.5.0
/**
 * @package   Static pages
 * @category  modules
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2016-2017, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
(function(){
  Polymer({
    is: 'cs-static-pages-admin-categories-add-edit-form',
    behaviors: [cs.Polymer.behaviors.computed_bindings, cs.Polymer.behaviors.Language('static_pages_')],
    properties: {
      category: Object,
      original_title: String,
      categories: Array
    },
    ready: function(){
      var this$ = this;
      Promise.all([
        this.id
          ? cs.api('get api/Static_pages/admin/categories/' + this.id)
          : {
            title: '',
            path: '',
            parent: 0
          }, cs.api('get api/Static_pages/admin/categories')
      ]).then(function(arg$){
        var categories;
        this$.category = arg$[0], categories = arg$[1];
        this$.original_title = this$.category.title;
        this$.categories = categories;
      });
    },
    _save: function(){
      var method, suffix, this$ = this;
      method = this.id ? 'put' : 'post';
      suffix = this.id ? '/' + this.id : '';
      cs.api(method + " api/Static_pages/admin/categories" + suffix, this.category).then(function(){
        cs.ui.notify(this$.L.changes_saved, 'success', 5);
      });
    }
  });
}).call(this);
