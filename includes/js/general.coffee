###*
 * @package		CleverStyle CMS
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright	Copyright (c) 2011-2015, Nazar Mokrynskyi
 * @license		MIT License, see license.txt
###
$ ->
	L	= cs.Language
	$.ajaxSetup(
		type	: 'post'
		error	: (xhr) ->
			cs.ui.notify(
				if xhr.responseText
					JSON.parse(xhr.responseText).error_description
				else
					L.connection_error.toString()
				'warning'
				5
			)
	)
	$('.cs-header-sign-in-slide').click ->
		$('.cs-header-guest-form').removeClass('active')
		$('.cs-header-sign-in-form').addClass('active')
		$('.cs-header-sign-in-email').focus()
	$('.cs-header-registration-slide').click ->
		$('.cs-header-guest-form').removeClass('active')
		$('.cs-header-registration-form').addClass('active')
		$('.cs-header-registration-email').focus()
	$('.cs-header-restore-password-slide').click ->
		$('.cs-header-sign-in-form, .cs-header-registration-form').removeClass('active')
		$('.cs-header-restore-password-form').addClass('active')
		$('.cs-header-restore-password-email').focus()
	$('.cs-header-registration-email').keyup (event) ->
		if event.which == 13
			$('.cs-header-registration-process').click()
	$('.cs-header-sign-in-form').submit ->
		cs.sign_in($('.cs-header-sign-in-email').val(), $('.cs-header-user-password').val())
		return false
	$('.cs-header-sign-out-process').click ->
		cs.sign_out()
	$('#current_password').click ->
		password	= $('.cs-profile-current-password')
		if password.prop('type') == 'password'
			password.prop('type', 'text')
			this.icon = 'unlock'
		else
			password.prop('type', 'password')
			this.icon = 'lock'
	$('#new_password').click ->
		password	= $('.cs-profile-new-password')
		if password.prop('type') == 'password'
			password.prop('type', 'text')
			this.icon = 'unlock'
		else
			password.prop('type', 'password')
			this.icon = 'lock'
	$('.cs-header-registration-process').click ->
		if !cs.rules_text
			cs.registration $('.cs-header-registration-email').val()
			return
		$modal = $(cs.ui.simple_modal("""
			<h2>#{L.rules_agree}</h2>
			<p>#{cs.rules_text}</p>
			<p class="cs-text-right">
				<button is="cs-button" primary class="cs-registration-continue">#{L.yes}</button>
			</p>
		"""))
		$modal
			.find('.cs-registration-continue')
			.click ->
				$modal[0].close()
				cs.registration $('.cs-header-registration-email').val()
	$('.cs-header-restore-password-process').click ->
		cs.restore_password $('.cs-header-restore-password-email').val()
	$('.cs-profile-change-password').click ->
		cs.change_password $('.cs-profile-current-password').val(), $('.cs-profile-new-password').val()
	$('.cs-header-back').click ->
		$('.cs-header-guest-form').addClass('active')
		$('.cs-header-registration-form, .cs-header-sign-in-form, .cs-header-restore-password-form').removeClass('active')
	return
