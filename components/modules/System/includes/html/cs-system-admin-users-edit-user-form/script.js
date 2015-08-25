// Generated by CoffeeScript 1.9.3

/**
 * @package    CleverStyle CMS
 * @subpackage System module
 * @category   modules
 * @author     Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright  Copyright (c) 2015, Nazar Mokrynskyi
 * @license    MIT License, see license.txt
 */

(function() {
  var L;

  L = cs.Language;

  Polymer({
    'is': 'cs-system-admin-users-edit-user-form',
    behaviors: [cs.Polymer.behaviors.Language],
    properties: {
      user_id: -1,
      user_data: {
        type: Object,
        value: {}
      },
      languages: [],
      timezones: [],
      block_until: {
        observer: '_block_until',
        type: String
      }
    },
    ready: function() {
      $.when($.getJSON('api/System/admin/languages'), $.getJSON('api/System/admin/timezones'), $.getJSON('api/System/admin/users/' + this.user_id)).done((function(_this) {
        return function(languages, timezones, data) {
          var block_until, description, i, language, languages_list, len, ref, ref1, timezone, timezones_list;
          languages_list = [];
          languages_list.push({
            clanguage: '',
            description: L.system_default
          });
          ref = languages[0];
          for (i = 0, len = ref.length; i < len; i++) {
            language = ref[i];
            languages_list.push({
              clanguage: language,
              description: language
            });
          }
          timezones_list = [];
          timezones_list.push({
            timezone: '',
            description: L.system_default
          });
          ref1 = timezones[0];
          for (description in ref1) {
            timezone = ref1[description];
            timezones_list.push({
              timezone: timezone,
              description: description
            });
          }
          _this.set('languages', languages_list);
          _this.set('timezones', timezones_list);
          block_until = (function() {
            var date, z;
            block_until = data[0].block_until;
            date = new Date;
            if (parseInt(block_until)) {
              date.setTime(parseInt(block_until) * 1000);
            }
            z = function(number) {
              return ('0' + number).substr(-2);
            };
            return date.getFullYear() + '-' + z(date.getMonth() + 1) + '-' + z(date.getDate()) + 'T' + z(date.getHours()) + ':' + z(date.getMinutes());
          })();
          _this.set('block_until', block_until);
          return _this.set('user_data', data[0]);
        };
      })(this));
      this.workarounds(this.shadowRoot);
      return cs.observe_inserts_on(this.shadowRoot, this.workarounds);
    },
    workarounds: function(target) {
      return $(target).cs().radio_buttons_inside();
    },
    status_change: function(e) {
      return this.set('user_data.status', $(e.currentTarget).children('input').val());
    },
    show_password: function(e) {
      var $lock, password;
      $lock = $(e.currentTarget);
      password = $lock.next()[0];
      if (password.type === 'password') {
        password.type = 'text';
        return $lock.removeClass('uk-icon-lock').addClass('uk-icon-unlock');
      } else {
        password.type = 'password';
        return $lock.removeClass('uk-icon-unlock').addClass('uk-icon-lock');
      }
    },
    _block_until: function() {
      var block_until, date;
      block_until = this.block_until;
      date = new Date;
      date.setFullYear(block_until.substr(0, 4));
      date.setMonth(block_until.substr(5, 2) - 1);
      date.setDate(block_until.substr(8, 2));
      date.setHours(block_until.substr(11, 2));
      date.setMinutes(block_until.substr(14, 2));
      date.setSeconds(0);
      date.setMilliseconds(0);
      return this.set('user_data.block_until', date.getTime() / 1000);
    },
    status_state: function(expected) {
      var status;
      status = this.user_data.status;
      return status == expected;
    },
    status_class: function(expected) {
      return 'uk-button' + (this.status_state(expected) ? ' uk-active' : '');
    },
    save: function() {
      return $.ajax({
        url: 'api/System/admin/users/' + this.user_id,
        type: 'patch',
        data: {
          user: this.user_data
        },
        success: function() {
          return UIkit.notify(L.changes_saved.toString(), 'success');
        }
      });
    }
  });

}).call(this);
