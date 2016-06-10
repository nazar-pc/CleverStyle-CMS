/**
 * @package    CleverStyle Framework
 * @subpackage System module
 * @category   modules
 * @author     Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright  Copyright (c) 2015-2016, Nazar Mokrynskyi
 * @license    MIT License, see license.txt
 */
L										= cs.Language('system_admin_')
cs.{}Polymer.{}behaviors.{}admin.System	=
	components	:
		# Module/plugin enabling
		_enable_component : (component, component_type, meta) !->
			category		= component_type + 's'
			cs.api([
				"get			api/System/admin/#category/#component/dependencies"
				'get_settings	api/System/admin/system'
			]).then ([dependencies, settings]) !~>
				# During enabling we don't care about those since module should be already installed
				delete dependencies.db_support
				delete dependencies.storage_support
				translation_key	= if component_type == 'module' then 'modules_enabling_of_module' else 'plugins_enabling_of_plugin'
				title			= "<h3>#{L[translation_key](component)}</h3>"
				message			= ''
				message_more	= ''
				if Object.keys(dependencies).length
					message	= @_compose_dependencies_message(component, dependencies)
					if settings.simple_admin_mode
						cs.ui.notify(message, 'error', 5)
						return
				if meta && meta.optional
					message_more	+= '<p class="cs-text-success cs-block-success">' + L.for_complete_feature_set(meta.optional.join(', ')) + '</p>'
				modal	= cs.ui.confirm(
					"#title#message#message_more"
					!~>
						cs.Event.fire(
							"admin/System/components/#category/enable/before"
							name	: component
						)
							.then -> cs.api("enable api/System/admin/#category/#component")
							.then !~>
								@reload()
								cs.ui.notify(L.changes_saved, 'success', 5)
								cs.Event.fire(
									"admin/System/components/#category/enable/after"
									name	: component
								)
				)
				modal.ok.innerHTML		= L[if !message then 'enable' else 'force_enable_not_recommended']
				modal.ok.primary		= !message
				modal.cancel.primary	= !modal.ok.primary
				$(modal).find('p:not([class])').addClass('cs-text-error cs-block-error')
		# Module/plugin disabling
		_disable_component : (component, component_type) !->
			category			= component_type + 's'
			cs.api([
				"get			api/System/admin/#category/#component/dependent_packages"
				'get_settings	api/System/admin/system'
			]).then ([dependent_packages, settings]) !~>
				translation_key		= if component_type == 'module' then 'modules_disabling_of_module' else 'plugins_disabling_of_plugin'
				title				= "<h3>#{L[translation_key](component)}</h3>"
				message				= ''
				if Object.keys(dependent_packages).length
					for type, packages of dependent_packages
						translation_key = if type == 'modules' then 'this_package_is_used_by_module' else 'this_package_is_used_by_plugin'
						for _package in packages
							message += "<p>#{L[translation_key](_package)}</p>"
					message += "<p>#{L.dependencies_not_satisfied}</p>"
					if settings.simple_admin_mode
						cs.ui.notify(message, 'error', 5)
						return
				modal	= cs.ui.confirm(
					"#title#message"
					!~>
						cs.Event.fire(
							"admin/System/components/#category/disable/before"
							name	: component
						)
							.then -> cs.api("disable api/System/admin/#category/#component")
							.then !~>
								@reload()
								cs.ui.notify(L.changes_saved, 'success', 5)
								cs.Event.fire(
									"admin/System/components/#category/disable/after"
									name	: component
								)
				)
				modal.ok.innerHTML		= L[if !message then 'disable' else 'force_disable_not_recommended']
				modal.ok.primary		= !message
				modal.cancel.primary	= !modal.ok.primary
				$(modal).find('p').addClass('cs-text-error cs-block-error')
		# Module/plugin/theme update
		_update_component : (existing_meta, new_meta) !->
			component		= new_meta.package
			category		= new_meta.category
			cs.api([
				"get			api/System/admin/#category/#component/update_dependencies"
				'get_settings	api/System/admin/system'
			]).then ([dependencies, settings]) !~>
				# During update we don't care about those since module should be already installed
				delete dependencies.db_support
				delete dependencies.storage_support
				translation_key	=
					switch category
					| 'modules'	=> (if component == 'System' then 'modules_updating_of_system' else 'modules_updating_of_module')
					| 'plugins'	=> 'plugins_updating_of_plugin'
					| 'themes'	=> 'appearance_updating_theme'
				title			= "<h3>#{L[translation_key](component)}</h3>"
				message			= ''
				if component == 'System'
					message_more	= '<p class>' + L.modules_update_system(existing_meta.version, new_meta.version) + '</p>'
				else
					translation_key	=
						switch category
						| 'modules'	=> 'modules_update_module'
						| 'plugins'	=> 'plugins_update_plugin'
						| 'themes'	=> 'appearance_update_theme'
					message_more	= '<p class>' + L[translation_key](component, existing_meta.version, new_meta.version) + '</p>'
				if Object.keys(dependencies).length
					message	= @_compose_dependencies_message(component, dependencies)
					if settings.simple_admin_mode
						cs.ui.notify(message, 'error', 5)
						return
				if new_meta.optional
					message_more	+= '<p class="cs-text-success cs-block-success">' + L.for_complete_feature_set(new_meta.optional.join(', ')) + '</p>'
				modal	= cs.ui.confirm(
					"#title#message#message_more"
					!~>
						(
							if component == 'System'
								cs.Event.fire('admin/System/components/modules/update_system/before')
							else
								cs.Event.fire(
									"admin/System/components/#category/update/before"
									name	: component
								)
						)
							.then -> cs.api("update api/System/admin/#category/#component")
							.then ->
								cs.ui.notify(L.changes_saved, 'success', 5)
								if component == 'System'
									cs.Event.fire('admin/System/components/modules/update_system/after')
								else
									cs.Event.fire(
										"admin/System/components/#category/update/after"
										name	: component
									)
							.then !-> location.reload()
				)
				modal.ok.innerHTML		= L[if !message then 'yes' else 'force_update_not_recommended']
				modal.ok.primary		= !message
				modal.cancel.primary	= !modal.ok.primary
				$(modal).find('p:not([class])').addClass('cs-text-error cs-block-error')
		# Module/plugin/theme complete removal
		_remove_completely_component : (component, category) !->
			translation_key		=
				switch category
				| 'modules' => 'modules_completely_remove_module'
				| 'plugins'	=> 'plugins_completely_remove_plugin'
				| 'themes'	=> 'appearance_completely_remove_theme'
			<~! cs.ui.confirm(L[translation_key](component), _)
			cs.api("delete api/System/admin/#category/#component").then !~>
				@reload()
				cs.ui.notify(L.changes_saved, 'success', 5)
		# Compose HTML representation of dependencies details
		_compose_dependencies_message : (component, dependencies) ->
			message = ''
			for what, categories of dependencies
				if categories instanceof Array
					categories = {categories : [categories]}
				for category, details of categories
					for detail in details
						message	+=
							"""<p class="cs-block-error cs-text-error">""" +
							(switch what
								case 'update_from'
									if component == 'System'
										L.modules_update_system_impossible_from_version_to(detail.from, detail.to, detail.can_update_from)
									else
										L.modules_module_cant_be_updated_from_version_to(component, detail.from, detail.to, detail.can_update_from)
								case 'update_older'
									translation_key =
										switch category
										| 'modules'	=> (if component == 'System' then 'modules_update_system_impossible_older_version' else 'modules_update_module_impossible_older_version')
										| 'plugins'	=> 'plugins_update_plugin_impossible_older_version'
										| 'themes'	=> 'appearance_update_theme_impossible_older_version'
									L[translation_key](component, detail.from, detail.to)
								case 'update_same'
									translation_key =
										switch category
										| 'modules'	=> (if component == 'System' then 'modules_update_system_impossible_same_version' else 'modules_update_module_impossible_same_version')
										| 'plugins'	=> 'plugins_update_plugin_impossible_same_version'
										| 'themes'	=> 'appearance_update_theme_impossible_same_version'
									L[translation_key](component, detail.version)
								case 'provide'
									translation_key =
										if category == 'modules'
											'module_already_provides_functionality'
										else
											'plugin_already_provides_functionality'
									L[translation_key](detail.name, detail.features.join('", "'))
								case 'require'
									for required in detail.required
										required	= if required[1] && required[1] !~= '0' then required.join(' ') else ''
										if category == 'unknown'
											L.package_or_functionality_not_found(detail.name + required)
										else
											translation_key =
												if category == 'modules'
													'modules_unsatisfactory_version_of_the_module'
												else
													'plugins_unsatisfactory_version_of_the_plugin'
											L[translation_key](detail.name, required, detail.existing)
								case 'conflict'
									for conflict in detail.conflicts
										L.package_is_incompatible_with(conflict.package, conflict.conflicts_with, conflict.of_versions.filter(-> it !~= '0').join(' '))
								case 'db_support'
									L.modules_compatible_databases_not_found(detail.join('", "'))
								case 'storage_support'
									L.modules_compatible_storages_not_found(detail.join('", "'))
							) +
							"</p>"
			"""#message<p class="cs-block-error cs-text-error">#{L.dependencies_not_satisfied}</p>"""
	upload :
		# Generic package uploading, jqXHR object will be returned
		_upload_package : (file_input) ->
			if !file_input.files.length
				throw new Error('file should be selected')
			form_data	= new FormData
			form_data.append('file', file_input.files[0])
			cs.api('post api/System/admin/upload', form_data)
	settings :
		properties	:
			settings_api_url	:
				observer	: '_reload_settings'
				type		: String
			settings			: Object
			simple_admin_mode	: Boolean
		_reload_settings : !->
			cs.api([
				'get_settings ' + @settings_api_url
				'get_settings api/System/admin/system'
			]).then ([settings, system_settings]) !~>
				@simple_admin_mode	= system_settings.simple_admin_mode == 1
				@set('settings', settings)
		_apply : !->
			cs.api('apply_settings ' + @settings_api_url, @settings).then !~>
				@_reload_settings()
				cs.ui.notify(L.changes_applied, 'warning', 5)
		_save : !->
			cs.api('save_settings ' + @settings_api_url, @settings).then  !~>
				@_reload_settings()
				cs.ui.notify(L.changes_saved, 'success', 5)
		_cancel : !->
			cs.api('cancel_settings ' + @settings_api_url).then !~>
				@_reload_settings()
				cs.ui.notify(L.changes_canceled, 'success', 5)
