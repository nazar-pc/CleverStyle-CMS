###*
 * @package   CleverStyle Widgets
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
###
Polymer.cs					= Polymer.cs || {}
Polymer.cs.behaviors		= Polymer.cs.behaviors || {}
Polymer.cs.behaviors.button	=
	properties	:
		active	:
			reflectToAttribute	: true
			type				: Boolean
		empty	:
			reflectToAttribute	: true
			type				: Boolean
		icon	:
			reflectToAttribute	: true
			type				: String
	ready : ->
		if !@childNodes.length
			@empty = true