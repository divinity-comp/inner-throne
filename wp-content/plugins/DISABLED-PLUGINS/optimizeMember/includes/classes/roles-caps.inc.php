<?php
/**
* Roles/Capabilities.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember\Roles_Caps
* @since 110524RC
*/
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**/
if(!class_exists("c_ws_plugin__optimizemember_roles_caps"))
	{
		/**
		* Roles/Capabilities.
		*
		* @package optimizeMember\Roles_Caps
		* @since 110524RC
		*/
		class c_ws_plugin__optimizemember_roles_caps
			{
				/**
				* Configures Roles/Capabilities.
				*
				* @package optimizeMember\Roles_Caps
				* @since 110524RC
				*
				* @return null
				*/
				public static function config_roles()
					{
						do_action("ws_plugin__optimizemember_before_config_roles", get_defined_vars());
						/**/
						if(!apply_filters("ws_plugin__optimizemember_lock_roles_caps", false))
							{
								c_ws_plugin__optimizemember_roles_caps::unlink_roles();
								/**/
								if(function_exists("bbp_get_dynamic_roles") /* bbPress v2.2+ integration. */)
									{
										foreach(bbp_get_caps_for_role(bbp_get_participant_role()) as $bbp_participant_cap => $bbp_participant_cap_is)
											if($bbp_participant_cap_is /* Is this capability enabled? */)
												$bbp_participant_caps[$bbp_participant_cap] = true;
									}
								else if(function_exists("bbp_get_caps_for_role") /* bbPress < v2.2 integration. */)
									{
										foreach(bbp_get_caps_for_role(bbp_get_participant_role()) as $bbp_participant_cap)
											$bbp_participant_caps[$bbp_participant_cap] = true;
									}
								/**/
								if(0 === 0) /* Subscriber Role is required by optimizeMember. */
									{
										$caps = array("read" => true, "level_0" => true);
										$caps = array_merge($caps, array("access_optimizemember_level0" => true));
										$caps = (!empty($bbp_participant_caps)) ? array_merge($caps, $bbp_participant_caps) : $caps;
										/**/
										if(!($role = get_role("subscriber")))
											{
												add_role("subscriber", "Subscriber");
												$role = get_role("subscriber");
											}
										/**/
										foreach(array_keys($caps) as $cap)
											$role->add_cap($cap);
									}
								/**/
								for($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
									{
										for($i = 0, $caps = array("read" => true, "level_0" => true); $i <= $n; $i++)
											$caps = array_merge($caps, array("access_optimizemember_level".$i => true));
										$caps = (!empty($bbp_participant_caps)) ? array_merge($caps, $bbp_participant_caps) : $caps;
										/**/
										if(!($role = get_role("optimizemember_level".$n)))
											{
												add_role("optimizemember_level".$n, "optimizeMember Level ".$n);
												$role = get_role("optimizemember_level".$n);
											}
										/**/
										foreach(array_keys($caps) as $cap)
											$role->add_cap($cap);
									}
								/**/
								$full_access_roles = array("administrator", "editor", "author", "contributor");
								/**/
								if(function_exists("bbp_get_caps_for_role") && !function_exists("bbp_get_dynamic_roles") /* bbPress < v2.2 integration. */)
									$full_access_roles = array_merge($full_access_roles, (array)bbp_get_moderator_role());
								/**/
								foreach($full_access_roles as $role)
									{
										if(($role = get_role($role)))
											for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
												$role->add_cap("access_optimizemember_level".$n);
									}
							}
						/**/
						do_action("ws_plugin__optimizemember_after_config_roles", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}

			/**
			 * Adds support for bbPress v2.2+ dynamic roles.
			 *
			 * @package optimizeMember\Roles_Caps
			 * @since 112512
			 *
			 * @attaches-to ``add_filter("bbp_get_caps_for_role");``
			 *
			 * @param array $caps
			 * @param bool $role
			 * @return array
			 */
				public static function bbp_dynamic_role_caps($caps = array(), $role = FALSE)
					{
						if(function_exists("bbp_get_dynamic_roles") && $role !== bbp_get_blocked_role())
							{
								$caps = array_merge($caps, array("read" => true, "level_0" => true));
								$caps = array_merge($caps, array("access_optimizemember_level0" => true));
								/**/
								if(in_array($role, array(bbp_get_keymaster_role(), bbp_get_moderator_role()), TRUE))
									{
										for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
											$caps = array_merge($caps, array("access_optimizemember_level".$n => true));
									}
							}
						return /* Dynamic capabilities. */ $caps;
					}
				/**
				* Unlinks Roles/Capabilities.
				*
				* @package optimizeMember\Roles_Caps
				* @since 110524RC
				*
				* @return null
				*/
				public static function unlink_roles()
					{
						do_action("ws_plugin__optimizemember_before_unlink_roles", get_defined_vars());
						/**/
						if(!apply_filters("ws_plugin__optimizemember_lock_roles_caps", false))
							{
								if(($role = get_role("subscriber")))
									$role->remove_cap("access_optimizemember_level0");
								/**/
								for($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["max_levels"]; $n++)
									remove_role("optimizemember_level".$n);
								/**/
								$full_access_roles = array("administrator", "editor", "author", "contributor");
								/**/
								if(function_exists("bbp_get_caps_for_role") && !function_exists("bbp_get_dynamic_roles") /* bbPress < v2.2 integration. */)
									$full_access_roles = array_merge($full_access_roles, (array)bbp_get_moderator_role());
								/**/
								foreach($full_access_roles as $role)
									{
										if(($role = get_role($role)))
											for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["max_levels"]; $n++)
												$role->remove_cap("access_optimizemember_level".$n);
									}
							}
						/**/
						do_action("ws_plugin__optimizemember_after_unlink_roles", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/**
				* Updates Roles/Capabilities via AJAX.
				*
				* @package optimizeMember\Roles_Caps
				* @since 110524RC
				*
				* @attaches-to ``add_action("wp_ajax_ws_plugin__optimizemember_update_roles_via_ajax");``
				*
				* @return null Exits script execution after output for AJAX caller.
				*/
				public static function update_roles_via_ajax()
					{
						do_action("ws_plugin__optimizemember_before_update_roles_via_ajax", get_defined_vars());
						/**/
						status_header(200); /* Send a 200 OK status header. */
						header("Content-Type: text/plain; charset=utf-8"); /* Content-Type with UTF-8. */
						eval('while (@ob_end_clean ());'); /* End/clean all output buffers that may exist. */
						/**/
						if(current_user_can("create_users")) /* Check priveledges. Ability to create Users? */
							/**/
							if(!empty($_POST["ws_plugin__optimizemember_update_roles_via_ajax"]))
								if(($nonce = $_POST["ws_plugin__optimizemember_update_roles_via_ajax"]))
									if(wp_verify_nonce($nonce, "ws-plugin--optimizemember-update-roles-via-ajax"))
										/**/
										if(!apply_filters("ws_plugin__optimizemember_lock_roles_caps", false))
											{
												c_ws_plugin__optimizemember_roles_caps::config_roles();
												$success = true; /* Roles updated. */
											}
										else /* Else flag as having been locked here. */
											$locked = true;
						/**/
						exit(apply_filters("ws_plugin__optimizemember_update_roles_via_ajax", /* Also handle ``$locked`` here. */
						((isset($success) && $success) ? "1" : ((isset($locked) && $locked) ? "l" : "0")), get_defined_vars()));
					}
			}
	}
?>