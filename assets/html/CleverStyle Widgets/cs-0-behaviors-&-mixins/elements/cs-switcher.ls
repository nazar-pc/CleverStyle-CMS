/**
 * @package   CleverStyle Widgets
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015-2017, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
csw.behaviors.cs-switcher = [
	properties	:
		selected	:
			notify		: true
			observer	: '_selected_changed'
			type		: Number
	ready : !->
		for element in @children
			if element.active
				return
		if !@selected
			@selected = 0
	_selected_changed : (selected) !->
		if selected == undefined
			return
		for element, index in @children
			if element.matches('template')
				continue
			element.active = index == selected
			if index == selected
				element.setAttribute('active', '')
			else
				element.removeAttribute('active')
]