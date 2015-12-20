/**
 * @package   TinyMCE
 * @category  plugins
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015, Nazar Mokrynskyi
 * @license   GNU Lesser General Public License 2.1, see license.txt
 */
Polymer(
	is				: 'cs-editor-inline'
	behaviors		: [Polymer.cs.behaviors.TinyMCE.editor]
	editor_config	: tinymce.editor_config_inline
)