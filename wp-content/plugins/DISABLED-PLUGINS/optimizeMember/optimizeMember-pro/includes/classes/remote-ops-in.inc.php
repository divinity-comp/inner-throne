<?php
/**
* optimizeMember Pro Remote Operations API ( inner processing routines ).
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* This WordPress plugin ( optimizeMember Pro ) is comprised of two parts:
*
* o (1) Its PHP code is licensed under the GPL license, as is WordPress.
* 	You should have received a copy of the GNU General Public License,
* 	along with this software. In the main directory, see: /licensing/
* 	If not, see: {@link http://www.gnu.org/licenses/}.
*
* o (2) All other parts of ( optimizeMember Pro ); including, but not limited to:
* 	the CSS code, some JavaScript code, images, and design;
* 	are licensed according to the license purchased.
* 	See: {@link http://www.optimizepress.com/prices/}
*
* Unless you have our prior written consent, you must NOT directly or indirectly license,
* sub-license, sell, resell, or provide for free; part (2) of the optimizeMember Pro Module;
* or make an offer to do any of these things. All of these things are strictly
* prohibited with part (2) of the optimizeMember Pro Module.
*
* Your purchase of optimizeMember Pro includes free lifetime upgrades via optimizeMember.com
* ( i.e. new features, bug fixes, updates, improvements ); along with full access
* to our video tutorial library: {@link http://www.optimizepress.com/videos/}
*
* @package optimizeMember\API_Remote_Ops
* @since 110713
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_remote_ops_in"))
	{
		/**
		* optimizeMember Pro Remote Operations API ( inner processing routines ).
		*
		* @package optimizeMember\API_Remote_Ops
		* @since 110713
		*/
		class c_ws_plugin__optimizemember_pro_remote_ops_in
			{
				/**
				* Creates a new User.
				*
				* @package optimizeMember\API_Remote_Ops
				* @since 110713
				*
				* @param array An input array of Remote Operation parameters.
				* @return str Returns a serialized array with an `ID` element object on success,
				* 	else returns a string beginning with `Error:` on failure; which will include details regarding the error.
				*/
				public static function create_user ($op = NULL)
					{
						if (!empty ($op["op"]) && $op["op"] === "create_user" && !empty ($op["data"]) && is_array ($op["data"]))
							{
								if(!empty($op["data"]["modify_if_login_exists"])) {
									if(!empty($op["data"]["user_login"]) && ($_user = new WP_User((string)$op["data"]["user_login"])) && !empty($_user->ID)) {
										return c_ws_plugin__optimizemember_pro_remote_ops_in::modify_user(array_merge($op, array("op" => "modify_user")));
									} else if (!empty($op["data"]["user_email"]) && ($_user = get_user_by('email', (string)$op["data"]["user_email"])) && !empty($_user->ID)) {
										return c_ws_plugin__optimizemember_pro_remote_ops_in::modify_user(array_merge($op, array("op" => "modify_user")));
									}
								}
								/**/
								$GLOBALS["ws_plugin__optimizemember_registration_vars"] = array ();
								$v = &$GLOBALS["ws_plugin__optimizemember_registration_vars"];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_user_login"] = (string)@$op["data"]["user_login"];
								$v["ws_plugin__optimizemember_custom_reg_field_user_email"] = (string)@$op["data"]["user_email"];
								/**/
								if (empty ($op["data"]["user_pass"]) || !is_string ($op["data"]["user_pass"]))
									$op["data"]["user_pass"] = c_ws_plugin__optimizemember_registrations::maybe_custom_pass($op["data"]["user_pass"]);
								$GLOBALS["ws_plugin__optimizemember_generate_password_return"] = $op["data"]["user_pass"];
								$v['ws_plugin__optimizemember_custom_reg_field_user_pass1'] = $op['data']['user_pass'];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_first_name"] = (string)@$op["data"]["first_name"];
								$v["ws_plugin__optimizemember_custom_reg_field_last_name"] = (string)@$op["data"]["last_name"];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_level"] = (string)@$op["data"]["optimizemember_level"];
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_ccaps"] = (string)@$op["data"]["optimizemember_ccaps"];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_registration_ip"] = (string)@$op["data"]["optimizemember_registration_ip"];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_subscr_gateway"] = (string)@$op["data"]["optimizemember_subscr_gateway"];
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_subscr_id"] = (string)@$op["data"]["optimizemember_subscr_id"];
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_custom"] = (string)@$op["data"]["optimizemember_custom"];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_auto_eot_time"] = (string)@$op["data"]["optimizemember_auto_eot_time"];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_optimizemember_notes"] = (string)@$op["data"]["optimizemember_notes"];
								/**/
								$v["ws_plugin__optimizemember_custom_reg_field_opt_in"] = (string)@$op["data"]["opt_in"];
								/**/
								if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"])
									foreach (json_decode ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"], true) as $field)
										{
											$field_var = preg_replace ("/[^a-z0-9]/i", "_", strtolower ($field["id"]));
											$field_id_class = preg_replace ("/_/", "-", $field_var);
											/**/
											if (isset($op["data"]["custom_fields"][$field_var]))
												$v["ws_plugin__optimizemember_custom_reg_field_" . $field_var] = $op["data"]["custom_fields"][$field_var];

                                            if (isset($op["data"][$field_var])){
                                                $v["ws_plugin__optimizemember_custom_reg_field_" . $field_var] = $op["data"][$field_var];
                                            }
										}
								$create = array ("user_login" => (string)@$op["data"]["user_login"], "user_pass" => (string)@$op["data"]["user_pass"], "user_email" => (string)@$op["data"]["user_email"]);
								/**/
								if (((is_multisite () && ($new = $user_id = c_ws_plugin__optimizemember_registrations::ms_create_existing_user ($create["user_login"], $create["user_email"], $create["user_pass"]))) || ($new = $user_id = wp_create_user ($create["user_login"], $create["user_pass"], $create["user_email"]))) && !is_wp_error ($new))
									{
										if (is_object ($user = new WP_User ($user_id)) && !empty ($user->ID) && ($user_id = $user->ID))
											{
												if (!empty ($op["data"]["notification"])) {
													if (version_compare(get_bloginfo("version"), "4.3.1", ">="))
														wp_new_user_notification($user_id, null, "both", $op['data']['user_pass']);
													else if (version_compare(get_bloginfo("version"), "4.3", ">="))
														wp_new_user_notification($user_id, "both", $op['data']['user_pass']);
													else wp_new_user_notification($user_id, $op['data']['user_pass']);
												}
												/**/
												return serialize (array ("ID" => $user_id));
											}
										return "Error: Creation may have failed. Unable to obtain WP_User ID.";
									}
								else if (is_wp_error ($new) && $new->get_error_code ())
									return "Error: " . $new->get_error_message ();
								/**/
								return "Error: User creation failed for an unknown reason. Please try again.";
							}
						return "Error: Empty or invalid request ( `create_user` ). Please try again.";
					}
				/**
				* Modifies an existing User.
				*
				* @package optimizeMember\API_Remote_Ops
				* @since 110713
				*
				* @param array An input array of Remote Operation parameters.
				* @return str Returns a serialized array with an `ID` element object on success,
				* 	else returns a string beginning with `Error:` on failure; which will include details regarding the error.
				*/
				public static function modify_user ($op = NULL)
					{
						if (!empty ($op["op"]) && $op["op"] === "modify_user" && !empty ($op["data"]) && is_array ($op["data"]))
							{
								if(!empty($op["data"]["user_id"]) && ($_user = new WP_User((integer)$op["data"]["user_id"])) && !empty($_user->ID))
									$user = $_user;
								/**/
								else if(!empty($op["data"]["user_login"]) && ($_user = new WP_User((string)$op["data"]["user_login"])) && !empty($_user->ID))
									$user = $_user;
								/**/
								else if(!empty($op["data"]["user_email"]) && ($_user = get_user_by('email', (string)$op["data"]["user_email"])) && !empty($_user->ID))
									$user = $_user;
								/**/
								else return "Error: Modification failed. Unable to obtain WP_User object instance with data supplied (i.e. ID/Username not found).";
								/**/
								if (is_multisite () && !is_user_member_of_blog ($user->ID))
									return "Error: Modification failed. Unable to obtain WP_User object instance with data supplied (i.e. ID/Username not a part of this Blog).";
								/**/
								if(is_super_admin($user->ID) || $user->has_cap("administrator"))
									return "Error: Modification failed. This API will not modify Administrators.";
								/**/
								$userdata["ID"] = /* Needed for database update. */ $user->ID;
								/**/
								if (!empty ($op["data"]["user_email"]))
									if (is_email ((string)$op["data"]["user_email"]) && !email_exists ((string)$op["data"]["user_email"]))
											$userdata["user_email"] = (string)$op["data"]["user_email"];
								/**/
								if (!empty ($op["data"]["user_pass"]))
									if /* No pass change on demo! */ ($user->user_login !== "demo")
										$userdata["user_pass"] = (string)$op["data"]["user_pass"];
								/**/
								if (!empty ($op["data"]["first_name"]))
									$userdata["first_name"] = (string)$op["data"]["first_name"];
								/**/
								if (!empty ($op["data"]["display_name"]))
									$userdata["display_name"] = (string)$op["data"]["display_name"];
								/**/
								if (!empty ($op["data"]["last_name"]))
									$userdata["last_name"] = (string)$op["data"]["last_name"];
								/**/
								if (isset ($op["data"]["optimizemember_level"]) && (integer)$op["data"]["optimizemember_level"] === 0)
								{
									if /* Not the same? */ (c_ws_plugin__optimizemember_user_access::user_access_role ($user) !== get_option("default_role"))
										$userdata["role"] = get_option ("default_role");
								}
								else if (!empty ($op["data"]["optimizemember_level"]) && (integer)$op["data"]["optimizemember_level"] > 0)
								{
									if /* Not the same? */ (c_ws_plugin__optimizemember_user_access::user_access_role ($user) !== "optimizemember_level".(integer)$op["data"]["optimizemember_level"])
										$userdata["role"] = "optimizemember_level".(integer)$op["data"]["optimizemember_level"];
								}
								wp_update_user /* OK. Now send this array for an update. */($userdata);
								/**/
								$old_user = /* Copy existing User obj. */ unserialize(serialize($user));
								$user = /* Update our object instance. */ new WP_User($user->ID);
								/**/
								$role = c_ws_plugin__optimizemember_user_access::user_access_role ($user);
								$level = c_ws_plugin__optimizemember_user_access::user_access_role_to_level($role);
								/**/
								if(!empty($op["data"]["auto_opt_out_transition"]))
									$_p["ws_plugin__optimizemember_custom_reg_auto_opt_out_transitions"] = TRUE;
								/**/
								if /* In this case, we need to fire Hook: `ws_plugin__optimizemember_during_collective_mods`. */(!empty($userdata["role"]))
									do_action("ws_plugin__optimizemember_during_collective_mods", $user->ID, get_defined_vars(), "user-role-change", "modification", $role, $user, $old_user);
								/**/
								if (!empty($op["data"]["optimizemember_ccaps"]) && preg_match ("/^-all/", str_replace ("+", "", (string)$op["data"]["optimizemember_ccaps"])))
									foreach ($user->allcaps as $cap => $cap_enabled)
										if (preg_match ("/^access_optimizemember_ccap_/", $cap))
											$user->remove_cap ($ccap = $cap);
								/**/
								if (!empty($op["data"]["optimizemember_ccaps"]) && preg_replace ("/^-all[\r\n\t\s;,]*/", "", str_replace ("+", "", (string)$op["data"]["optimizemember_ccaps"])))
									foreach (preg_split ("/[\r\n\t\s;,]+/", preg_replace ("/^-all[\r\n\t\s;,]*/", "", str_replace ("+", "", (string)$op["data"]["optimizemember_ccaps"]))) as $ccap)
										if (strlen ($ccap = trim (strtolower (preg_replace ("/[^a-z_0-9]/i", "", $ccap)))))
											$user->add_cap ("access_optimizemember_ccap_" . $ccap);
								/**/
								if(isset($op["data"]["optimizemember_originating_blog"]) && is_multisite())
									update_user_meta($user->ID, "optimizemember_originating_blog", (integer)$op["data"]["optimizemember_originating_blog"]);
								/**/
								if(isset($op["data"]["optimizemember_subscr_gateway"]))
									update_user_option($user->ID, "optimizemember_subscr_gateway", (string)$op["data"]["optimizemember_subscr_gateway"]);
								/**/
								if(isset($op["data"]["optimizemember_subscr_id"]))
									update_user_option($user->ID, "optimizemember_subscr_id", (string)$op["data"]["optimizemember_subscr_id"]);
								/**/
								if(isset($op["data"]["optimizemember_custom"]))
									update_user_option($user->ID, "optimizemember_custom", (string)$op["data"]["optimizemember_custom"]);
								/**/
								if(isset($op["data"]["optimizemember_registration_ip"]))
									update_user_option($user->ID, "optimizemember_registration_ip", (string)$op["data"]["optimizemember_registration_ip"]);
								/**/
								if(isset($op["data"]["optimizemember_notes"]))
									update_user_option($user->ID, "optimizemember_notes", trim(get_user_option("optimizemember_notes", $user->ID)."\n\n".(string)$op["data"]["optimizemember_notes"]));
								/**/
								if(isset($op["data"]["optimizemember_auto_eot_time"]))
									update_user_option($user->ID, "optimizemember_auto_eot_time", ((!empty($op["data"]["optimizemember_auto_eot_time"])) ? strtotime((string)$op["data"]["optimizemember_auto_eot_time"]) : ""));
								/**/
								if /* Custom Registration/Profile Fields configured? */ ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"])
									{
										$_existing_fields = get_user_option("optimizemember_custom_fields", $user->ID);
										/**/
										foreach(json_decode($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"], true) as $field)
										{
											$field_var = preg_replace("/[^a-z0-9]/i", "_", strtolower($field["id"]));
											$field_id_class = preg_replace("/_/", "-", $field_var);
											/**/
											if(!isset($op["data"]["custom_fields"][$field_var]))
											{
												if(isset($_existing_fields[$field_var]) && ((is_array($_existing_fields[$field_var]) && !empty($_existing_fields[$field_var])) || (is_string($_existing_fields[$field_var]) && strlen($_existing_fields[$field_var]))))
													$fields[$field_var] = $_existing_fields[$field_var];
												else unset($fields[$field_var]);
											}
											else // Default case handler.
											{
												if((is_array($op["data"]["custom_fields"][$field_var]) && !empty($op["data"]["custom_fields"][$field_var])) || (is_string($op["data"]["custom_fields"][$field_var]) && strlen($op["data"]["custom_fields"][$field_var])))
													$fields[$field_var] = $op["data"]["custom_fields"][$field_var];
												else unset($fields[$field_var]);
											}
										}
										if(!empty($fields))
											update_user_option($user->ID, "optimizemember_custom_fields", $fields);
										else delete_user_option($user->ID, "optimizemember_custom_fields");
									}
								if /* We ONLY process this if they are higher than Level #0. */($level > 0)
								{
									$pr_times = get_user_option("optimizemember_paid_registration_times", $user->ID);
									$pr_times["level"] = (empty($pr_times["level"])) ? time() : $pr_times["level"];
									$pr_times["level".$level] = (empty($pr_times["level".$level])) ? time() : $pr_times["level".$level];
									update_user_option /* Update now. */($user->ID, "optimizemember_paid_registration_times", $pr_times);
								}
								if /* Should we process List Servers? */(!empty($op["data"]["opt_in"]) && !empty($role) && $level >= 0)
									c_ws_plugin__optimizemember_list_servers::process_list_servers($role, $level, $user->user_login, ((!empty($op["data"]["user_pass"])) ? (string)$op["data"]["user_pass"] : ""), $user->user_email, $user->first_name, $user->last_name, false, true, true, $user->ID);
								/**/
								if /* Delete/reset IP Restrictions? */(!empty($op["data"]["reset_ip_restrictions"]))
									c_ws_plugin__optimizemember_ip_restrictions::delete_reset_specific_ip_restrictions(strtolower($user->user_login));
								/**/
								if /* Delete/reset File Downloads log? */ (!empty($op["data"]["reset_file_download_access_log"]))
									delete_user_option ($user->ID, "optimizemember_file_download_access_log");
								/**/
								return serialize (array ("ID" => $user->ID));
							}
						return "Error: Empty or invalid request ( `modify_user` ). Please try again.";
					}
				/**
				* Deletes an existing User.
				*
				* @package optimizeMember\API_Remote_Ops
				* @since 110713
				*
				* @param array An input array of Remote Operation parameters.
				* @return str Returns a serialized array with an `ID` element object on success,
				* 	else returns a string beginning with `Error:` on failure; which will include details regarding the error.
				*/
				public static function delete_user ($op = NULL)
					{
						if (!empty ($op["op"]) && $op["op"] === "delete_user" && !empty ($op["data"]) && is_array ($op["data"]))
							{
								if(!empty($op["data"]["user_id"]) && ($_user = new WP_User((integer)$op["data"]["user_id"])) && !empty($_user->ID))
									$user = $_user;
								/**/
								else if(!empty($op["data"]["user_login"]) && ($_user = new WP_User((string)$op["data"]["user_login"])) && !empty($_user->ID))
									$user = $_user;
								/**/
								else return "Error: Deletion failed. Unable to obtain WP_User object instance.";
								/**/
								if(is_super_admin($user->ID) || $user->has_cap("administrator"))
									return "Error: Deletion failed. This API will not delete Administrators.";
								/**/
								include_once ABSPATH . "wp-admin/includes/admin.php";
								wp_delete_user($user->ID);
								/**/
								return serialize (array ("ID" => $user->ID));
							}
						return "Error: Empty or invalid request (`delete_user`). Please try again.";
					}

				/**
				 * Returns membership levels
				 * @param  array $op
				 * @return str Returns a serialized array with array of levels on success, else returns a string wth error.
				 */
				public static function get_levels ($op = NULL)
				{
					if (!empty($op["op"]) && $op["op"] === "get_levels") {
						$levels = array();
						for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++) {
							$levels[$n] = ws_plugin__optimizemember_getMembershipLabel($n);
						}

						return serialize($levels);
					}

					return "Error: Empty or invalid request (`get_levels`). Please try again.";
				}

				/**
				 * Returns membership packages
				 * @param  array $op
				 * @return str Returns a serialized array with array of packages on success, else returns a string wth error.
				 */
				public static function get_packages ($op = null)
				{
					if (!empty($op["op"]) && $op["op"] === "get_packages") {
						$packages = array();
						if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
							foreach ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $package) {
								$packages[] = $package;
							}
						}

						return serialize($packages);
					}

					return "Error: Empty or invalid request (`get_packages`). Please try again.";
				}

				/**
				 * Change membership level.
				 * @param  array $op
				 * @return string Returns status message starting with "Success" or "Error" depending of outcome
				 */
				public static function change_level($op = null)
				{
					// Check if we are in correct method and that give user exists
					if ( ! empty($op["op"]) && $op["op"] === "change_level") {
						if ( ! isset($op["data"]["user_email"]) || empty($op["data"]["user_email"])) {
							return "Error: Missing data[user_email] param.";
						}

						$user = get_user_by('email', $op["data"]["user_email"]);

						if (empty($user->ID)) {
							return "Error: User not found (using data[user_email] param).";
						}
					}

					// Check if level info exists
					if ( ! isset($op["data"]["optimizemember_level"])) {
						return "Error: Mising level information (data[optimizemember_level]).";
					}

					// Process level info
					$data = array('ID' => $user->ID);
					if (isset($op["data"]["optimizemember_level"]) && (int) $op["data"]["optimizemember_level"] === 0) {
						if (c_ws_plugin__optimizemember_user_access::user_access_role($user) !== get_option("default_role")) {
							$data["role"] = get_option("default_role");
						}
					} else if ( ! empty ($op["data"]["optimizemember_level"]) && (int) $op["data"]["optimizemember_level"] > 0) {
						$roleKey = "optimizemember_level" . (int) $op["data"]["optimizemember_level"];
						if (c_ws_plugin__optimizemember_user_access::user_access_role ($user) !== $roleKey) {
							$data["role"] = $roleKey;
						}
					}

					// We are only changing the level if it is different from before
					if (count($data) > 1) {
						// Updating level data
						wp_update_user($data);

						$user = new WP_User($user->ID);

						$role = c_ws_plugin__optimizemember_user_access::user_access_role($user);
						$level = c_ws_plugin__optimizemember_user_access::user_access_role_to_level($role);

						// Triggering EMS providers
						if ( ! empty($op["data"]["opt_in"]) && ! empty($role) && $level >= 0) {
							c_ws_plugin__optimizemember_list_servers::process_list_servers($role, $level, $user->user_login, "", $user->user_email, $user->first_name, $user->last_name, false, true, true, $user->ID);
						}

						return "Success: level changed.";
					}

					return "Success: level is the same as before.";
				}

				/**
				 * Add additional given package to given user (identified by user_email).
				 * @param array $op
				 * @return string
				 */
				public static function add_package($op = null)
				{
					if ( ! empty($op["op"]) && $op["op"] === "add_package") {
						if ( ! isset($op["data"]["user_email"]) || empty($op["data"]["user_email"])) {
							return "Error: Missing data[user_email] param.";
						}

						$user = get_user_by('email', $op["data"]["user_email"]);

						if (empty($user->ID)) {
							return "Error: User not found (using data[user_email] param).";
						}
					}

					if ( ! empty($op["data"]["optimizemember_ccaps"]) && preg_replace("/^-all[\r\n\t\s;,]*/", "", str_replace("+", "", $op["data"]["optimizemember_ccaps"]))) {
						// Fetching all packages
						$packages = array();
						if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
							foreach ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $package) {
								$packages[] = $package;
							}
						}

						foreach (preg_split("/[\r\n\t\s;,]+/", preg_replace("/^-all[\r\n\t\s;,]*/", "", str_replace("+", "", $op["data"]["optimizemember_ccaps"]))) as $ccap) {
							if (strlen($ccap = trim(strtolower(preg_replace("/[^a-z_0-9]/i", "", $ccap)))) && in_array("access_optimizemember_ccap_" . $ccap, $packages)) {
								$user->add_cap("access_optimizemember_ccap_" . $ccap);
							}

							return "Error: Package name in wrong format or package with that name does not exist.";
						}

						return "Success: Package(s) added.";
					} else {
						return "Error: New packages not found in the request.";
					}

					return "Error: Empty or invalid request (`add_package`). Please try again.";
				}

				/**
				 * Dečete given package from given user (identified by user_email).
				 * @param array $op
				 * @return string
				 */
				public static function delete_package($op = null)
				{
					if ( ! empty($op["op"]) && $op["op"] === "delete_package") {
						if ( ! isset($op["data"]["user_email"]) || empty($op["data"]["user_email"])) {
							return "Error: Missing data[user_email] param.";
						}

						$user = get_user_by('email', $op["data"]["user_email"]);

						if (empty($user->ID)) {
							return "Error: User not found (using data[user_email] param).";
						}
					}

					if ( ! empty($op["data"]["optimizemember_ccaps"]) && preg_replace("/^-all[\r\n\t\s;,]*/", "", str_replace("+", "", $op["data"]["optimizemember_ccaps"]))) {
						foreach (preg_split("/[\r\n\t\s;,]+/", preg_replace("/^-all[\r\n\t\s;,]*/", "", str_replace("+", "", $op["data"]["optimizemember_ccaps"]))) as $ccap) {
							if (strlen($ccap = trim(strtolower(preg_replace("/[^a-z_0-9]/i", "", $ccap))))) {
								$user->remove_cap("access_optimizemember_ccap_" . $ccap);
							}
						}

						return "Success: Package(s) deleted.";
					} else {
						return "Error: New packages not found in the request.";
					}

					return "Error: Empty or invalid request (`delete_package`). Please try again.";
				}
			}
	}
?>