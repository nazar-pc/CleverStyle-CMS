// Generated by LiveScript 1.4.0
/**
 * @package    CleverStyle Framework
 * @subpackage System module
 * @category   modules
 * @author     Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright  Copyright (c) 2015-2016, Nazar Mokrynskyi
 * @license    MIT License, see license.txt
 */
(function(){
  Polymer({
    'is': 'cs-system-admin-users-groups-form',
    behaviors: [cs.Polymer.behaviors.Language('system_admin_users_')],
    properties: {
      user: '',
      user_groups: Array,
      other_groups: Array
    },
    ready: function(){
      this._reload();
    },
    _reload: function(){
      var this$ = this;
      cs.api(['get api/System/admin/groups', "get api/System/admin/users/" + this.user + "/groups"]).then(function(arg$){
        var groups, user_groups_ids, user_groups, other_groups, group;
        groups = arg$[0], user_groups_ids = arg$[1];
        user_groups = [];
        other_groups = [];
        for (group in groups) {
          group = groups[group];
          if (user_groups_ids.indexOf(group.id) !== -1) {
            user_groups.push(group);
          } else {
            other_groups.push(group);
          }
        }
        this$.user_groups = user_groups;
        this$.other_groups = other_groups;
        this$._init_sortable();
      });
    },
    _init_sortable: function(){
      var $shadowRoot, $group;
      $shadowRoot = $(this.shadowRoot);
      if ($shadowRoot.find('#user-groups > div:not(:first)').length < this.user_groups.length || $shadowRoot.find('#other-groups > div:not(:first)').length < this.other_groups.length) {
        setTimeout(this._init_sortable.bind(this), 100);
        return;
      }
      $group = $shadowRoot.find('#user-groups, #other-groups');
      require(['html5sortable'], function(){
        var this$ = this;
        $group.sortable({
          connectWith: 'user-groups-list',
          items: 'div:not(:first)',
          placeholder: '<div class="cs-block-primary">'
        }).on('sortupdate', function(){
          $(this$.$['user-groups']).children('div:not(:first)').removeClass('cs-block-warning cs-text-warning').addClass('cs-block-success cs-text-success');
          $(this$.$['other-groups']).children('div:not(:first)').removeClass('cs-block-success cs-text-success').addClass('cs-block-warning cs-text-warning');
        });
      });
    },
    save: function(){
      var groups, this$ = this;
      groups = $(this.$['user-groups']).children('div:not(:first)').map(function(){
        return this.group;
      }).get();
      cs.api("put api/System/admin/users/" + this.user + "/groups", {
        groups: groups
      }).then(function(){
        cs.ui.notify(this$.L.changes_saved, 'success', 5);
      });
    }
  });
}).call(this);
