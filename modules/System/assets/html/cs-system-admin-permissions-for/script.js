// Generated by LiveScript 1.5.0
/**
 * @package    CleverStyle Framework
 * @subpackage System module
 * @category   modules
 * @author     Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright  Copyright (c) 2015-2017, Nazar Mokrynskyi
 * @license    MIT License, see license.txt
 */
(function(){
  Polymer({
    is: 'cs-system-admin-permissions-for',
    behaviors: [cs.Polymer.behaviors.computed_bindings, cs.Polymer.behaviors.Language('system_admin_permissions_')],
    properties: {
      'for': {
        type: String,
        value: ''
      },
      group: '',
      user: '',
      all_permissions: Array,
      permissions: Object
    },
    ready: function(){
      var this$ = this;
      cs.api(['get api/System/admin/blocks', 'get api/System/admin/permissions', "get api/System/admin/" + this['for'] + "s/" + this[this['for']] + "/permissions"]).then(function(arg$){
        var blocks, all_permissions, permissions, block_index_to_title, res$, group, labels, label, id;
        blocks = arg$[0], all_permissions = arg$[1], permissions = arg$[2];
        block_index_to_title = {};
        blocks.forEach(function(block){
          return block_index_to_title[block.index] = block.title;
        });
        res$ = [];
        for (group in all_permissions) {
          labels = all_permissions[group];
          res$.push({
            group: group,
            labels: (fn$())
          });
        }
        this$.all_permissions = res$;
        this$.permissions = permissions;
        function fn$(){
          var ref$, results$ = [];
          for (label in ref$ = labels) {
            id = ref$[label];
            results$.push({
              name: label,
              id: id,
              description: group === 'Block' ? block_index_to_title[label] : ''
            });
          }
          return results$;
        }
      });
    },
    save: function(){
      var this$ = this;
      cs.api("put api/System/admin/" + this['for'] + "s/" + this[this['for']] + "/permissions", this.$.form).then(function(){
        cs.ui.notify(this$.L.changes_saved, 'success', 5);
      });
    },
    invert: function(e){
      var div, radios, i$, len$, radio;
      div = e.currentTarget;
      while (!div.matches('div')) {
        div = div.parentElement;
      }
      radios = Array.prototype.filter.call(div.querySelectorAll("[type=radio]:not([value='-1'])"), function(it){
        return !it.checked;
      });
      for (i$ = 0, len$ = radios.length; i$ < len$; ++i$) {
        radio = radios[i$];
        radio.parentElement.click();
      }
    },
    allow_all: function(e){
      var div, i$, ref$, len$, radio;
      div = e.currentTarget;
      while (!div.matches('div')) {
        div = div.parentElement;
      }
      for (i$ = 0, len$ = (ref$ = div.querySelectorAll("[type=radio][value='1']")).length; i$ < len$; ++i$) {
        radio = ref$[i$];
        radio.parentElement.click();
      }
    },
    deny_all: function(e){
      var div, i$, ref$, len$, radio;
      div = e.currentTarget;
      while (!div.matches('div')) {
        div = div.parentElement;
      }
      for (i$ = 0, len$ = (ref$ = div.querySelectorAll("[type=radio][value='0']")).length; i$ < len$; ++i$) {
        radio = ref$[i$];
        radio.parentElement.click();
      }
    },
    permission_state: function(id, expected){
      var permission;
      permission = this.permissions[id];
      return permission == expected || (expected == '-1' && permission === undefined);
    }
  });
}).call(this);
