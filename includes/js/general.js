// Generated by CoffeeScript 1.9.3

/**
 * @package		CleverStyle CMS
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2011-2015, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
 */

(function() {
  $(function() {
    var L;
    L = cs.Language;
    $.ajaxSetup({
      type: 'post',
      data: {
        session: cs.getcookie('session')
      },
      error: function(xhr) {
        return cs.ui.notify(xhr.responseText ? JSON.parse(xhr.responseText).error_description : L.connection_error.toString(), 'warning', 5);
      }
    });
    $('.cs-header-sign-in-slide').click(function() {
      $('.cs-header-guest-form').removeClass('active');
      $('.cs-header-sign-in-form').addClass('active');
      return $('.cs-header-sign-in-email').focus();
    });
    $('.cs-header-registration-slide').click(function() {
      $('.cs-header-guest-form').removeClass('active');
      $('.cs-header-registration-form').addClass('active');
      return $('.cs-header-registration-email').focus();
    });
    $('.cs-header-restore-password-slide').click(function() {
      $('.cs-header-sign-in-form, .cs-header-registration-form').removeClass('active');
      $('.cs-header-restore-password-form').addClass('active');
      return $('.cs-header-restore-password-email').focus();
    });
    $('.cs-header-registration-email').keyup(function(event) {
      if (event.which === 13) {
        return $('.cs-header-registration-process').click();
      }
    });
    $('.cs-header-sign-in-form').submit(function() {
      cs.sign_in($('.cs-header-sign-in-email').val(), $('.cs-header-user-password').val());
      return false;
    });
    $('.cs-header-sign-out-process').click(function() {
      return cs.sign_out();
    });
    $('#current_password').click(function() {
      var password;
      password = $('.cs-profile-current-password');
      if (password.prop('type') === 'password') {
        password.prop('type', 'text');
        return this.icon = 'unlock';
      } else {
        password.prop('type', 'password');
        return this.icon = 'lock';
      }
    });
    $('#new_password').click(function() {
      var password;
      password = $('.cs-profile-new-password');
      if (password.prop('type') === 'password') {
        password.prop('type', 'text');
        return this.icon = 'unlock';
      } else {
        password.prop('type', 'password');
        return this.icon = 'lock';
      }
    });
    $('.cs-header-registration-process').click(function() {
      var $modal;
      if (!cs.rules_text) {
        cs.registration($('.cs-header-registration-email').val());
        return;
      }
      $modal = $(cs.ui.simple_modal("<h2>" + L.rules_agree + "</h2>\n<p>" + cs.rules_text + "</p>\n<p class=\"cs-text-right\">\n	<button is=\"cs-button\" primary class=\"cs-registration-continue\">" + L.yes + "</button>\n</p>"));
      return $modal.find('.cs-registration-continue').click(function() {
        $modal[0].close();
        return cs.registration($('.cs-header-registration-email').val());
      });
    });
    $('.cs-header-restore-password-process').click(function() {
      return cs.restore_password($('.cs-header-restore-password-email').val());
    });
    $('.cs-profile-change-password').click(function() {
      return cs.change_password($('.cs-profile-current-password').val(), $('.cs-profile-new-password').val());
    });
    $('.cs-header-back').click(function() {
      $('.cs-header-guest-form').addClass('active');
      return $('.cs-header-registration-form, .cs-header-sign-in-form, .cs-header-restore-password-form').removeClass('active');
    });
  });

}).call(this);
