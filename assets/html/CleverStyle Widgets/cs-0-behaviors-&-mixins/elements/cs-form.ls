/**
 * @package   CleverStyle Widgets
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015-2017, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
styles	= ''
Polymer.cs.behaviors.cs-form = [
	attached : !->
		# Hack: The only way to achieve proper styling inside of cs-form, otherwise new slots system doesn't give us enough flexibility
		if @_styles_added
			return
		@_styles_added	= true
		if !styles
			head	= document.querySelector('head')
			head.insertAdjacentHTML(
				'beforeend',
				"""<style is="custom-style" include="cs-form-styles"></style>"""
			)
			Polymer.updateStyles()
			setTimeout (!~>
				styles	:= head.lastElementChild.textContent.split(':not([style-scope]):not(.style-scope)').join('')
				head.removeChild(head.lastElementChild)
				@insertAdjacentHTML('beforeend', "<style>#styles</style>")
			), 0
		else
			@insertAdjacentHTML('beforeend', "<style>#styles</style>")
]
