<?php
/**
* List Server integrations.
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
* @package optimizeMember\List_Servers
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_list_servers"))
	{
		/**
		* List Server integrations.
		*
		* @package optimizeMember\List_Servers
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_list_servers
			{
				/**
				* Determines whether or not any List Servers have been integrated.
				*
				* @package optimizeMember\List_Servers
				* @since 3.5
				*
				* @return bool True if List Servers have been integrated, else false.
				*/
				public static function list_servers_integrated ()
					{
						do_action ("ws_plugin__optimizemember_before_list_servers_integrated", get_defined_vars ());
						/**/
						for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++) /* Go through each Level; looking for a configured list. */
							if (!empty ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_provider"]))
								return apply_filters ("ws_plugin__optimizemember_list_servers_integrated", true, get_defined_vars ());
						/**/
						return apply_filters ("ws_plugin__optimizemember_list_servers_integrated", false, get_defined_vars ());
					}
				/**
				* Processes List Server integrations for optimizeMember.
				*
				* @package optimizeMember\List_Servers
				* @since 3.5
				*
				* @param str $role A WordPress Role ID/Name, such as `subscriber`, or `optimizemember_level1`.
				* @param int|str $level A numeric optimizeMember Access Level number.
				* @param str $login Username for the User.
				* @param str $pass Plain Text Password for the User.
				* @param str $email Email Address for the User.
				* @param str $fname First Name for the User.
				* @param str $lname Last Name for the User.
				* @param str $ip IP Address for the User.
				* @param bool $opt_in Defaults to false; must be set to true. Indicates the User IS opting in.
				* @param bool $double_opt_in Defaults to true. If false, no email confirmation is required. Use at your own risk.
				* @param int|str $user_id A WordPress User ID, numeric string or integer.
				* @return bool True if at least one List Server is processed successfully, else false.
				*
				* @todo Integrate {@link https://labs.aweber.com/docs/php-library-walkthrough AWeber's API}.
				* @todo Add a separate option for mail debugging; or consolidate?
				* @todo Integrate AWeber API ( much like the MailChimp API ).
				*/
				public static function process_list_servers ($role = FALSE, $level = FALSE, $login = FALSE, $pass = FALSE, $email = FALSE, $fname = FALSE, $lname = FALSE, $ip = FALSE, $opt_in = FALSE, $double_opt_in = TRUE, $user_id = FALSE)
					{
						global $current_site, $current_blog; /* For Multisite support. */
						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_process_list_servers", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						
						/**/
						if (c_ws_plugin__optimizemember_list_servers::list_servers_integrated () && ($args = func_get_args ()) && $role && is_string ($role) && is_numeric ($level) && $login && is_string ($login) && is_string ($pass = (string)$pass) && $email && is_string ($email) && is_email ($email) && is_string ($fname = (string)$fname) && is_string ($lname = (string)$lname) && is_string ($ip = (string)$ip) && $user_id && is_numeric ($user_id) && is_object ($user = new WP_User ($user_id)) && !empty ($user->ID))
						{
							$ccaps = implode (",", c_ws_plugin__optimizemember_user_access::user_access_ccaps ($user)); /* Get Custom Capabilities. */
							/**/
							$email_configs_were_on = c_ws_plugin__optimizemember_email_configs::email_config_status (); /* optimizeMember Filters enabled? */
							c_ws_plugin__optimizemember_email_configs::email_config_release (); /* Release optimizeMember Filters before we begin this routine. */

							$provider = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $level . "_provider"];
							$list = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $level . "_list"];

							/*
							 * It seems that this action is running before we load OP plugin so we need to include needed file manually 
							 *
							 * Afterwards we check if class exists (just to be on the safe side)
							 */
							if (defined('OP_PLUGIN_DIR')) {
								define('OP_SN','optimizepress');
								define('OP_DIR',OP_PLUGIN_DIR);
								define('OP_LIB',OP_DIR.'lib/');
								define('OP_MOD',OP_LIB.'modules/');
								define('OP_ASSETS',OP_DIR.'lib/assets/');
								require_once OP_PLUGIN_DIR . 'lib/functions/general.php';
								require_once OP_PLUGIN_DIR . 'lib/functions/options.php';
								require_once OP_PLUGIN_DIR . 'lib/functions/assets.php';
							}
							if (class_exists('OptimizePress_Assets_Core') && !empty($provider) && !empty($list) && op_assets_provider_enabled($provider)) {
								op_assets_provider_register($provider, $list, $email, $fname, $lname);
							}

							/**/
							eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
							do_action ("ws_plugin__optimizemember_during_process_list_servers", get_defined_vars ());
							unset ($__refs, $__v); /* Unset defined __refs, __v. */
							/**/
							if ($email_configs_were_on) /* Back on? */
								c_ws_plugin__optimizemember_email_configs::email_config ();
						}

						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_after_process_list_servers", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						return apply_filters ("ws_plugin__optimizemember_process_list_servers", (isset ($success) && $success), get_defined_vars ());
					}

            /**
             * Process list servers against current user.
             *
             * @since 141004
             * @package OptimizeMember\List_Servers
             *
             * See {@link process_list_servers()} for further details.
             *
             * @param bool $opt_in Defaults to `FALSE`; must be set to `TRUE`.
             * @param bool $double_opt_in Defaults to `TRUE`. Use at your own risk.
             * @param bool $clean_user_cache Defaults to `TRUE`; i.e. we start from a fresh copy of the current user.
             *
             * @return bool True if at least one list server is processed successfully.
             */
            public static function process_list_servers_against_current_user($opt_in = FALSE, $double_opt_in = TRUE, $clean_user_cache = TRUE)
            {
                if($clean_user_cache) // Start from a fresh user object here?
                {
                    clean_user_cache(get_current_user_id());
                    wp_cache_delete(get_current_user_id(), 'user_meta');
                    $user = new WP_User(get_current_user_id());
                }
                else $user = wp_get_current_user();

                return self::process_list_servers(
                    ($role = c_ws_plugin__optimizemember_user_access::user_access_role($user)),
                    ($level = c_ws_plugin__optimizemember_user_access::user_access_level($user)),
                    ($login = $user->user_login),
                    ($pass = $user->user_pass),
                    ($email = $user->user_email),
                    ($fname = $user->first_name),
                    ($lname = $user->last_name),
                    ($ip = @$_SERVER['REMOTE_ADDR']),
                    ($opt_in = $opt_in),
                    ($double_opt_in = $double_opt_in),
                    ($user_id = $user->ID)
                );
            }
				/**
				* Processes List Server removals for optimizeMember.
				*
				* @package optimizeMember\List_Servers
				* @since 3.5
				*
				* @param str $role A WordPress Role ID/Name, such as `subscriber`, or `optimizemember_level1`.
				* @param int|str $level A numeric optimizeMember Access Level number.
				* @param str $login Username for the User.
				* @param str $pass Plain Text Password for the User.
				* @param str $email Email address for the User.
				* @param str $fname First Name for the User.
				* @param str $lname Last Name for the User.
				* @param str $ip IP Address for the User.
				* @param bool $opt_out Defaults to false; must be set to true. Indicates the User IS opting out.
				* @param int|str $user_id A WordPress User ID, numeric string or integer.
				* @return bool True if at least one List Server is processed successfully, else false.
				*
				* @todo Integrate {@link https://labs.aweber.com/docs/php-library-walkthrough AWeber's API}.
				* @todo Add a separate option for mail debugging; or consolidate?
				* @todo Integrate AWeber API ( much like the MailChimp API ).
				*/
				public static function process_list_server_removals ($role = FALSE, $level = FALSE, $login = FALSE, $pass = FALSE, $email = FALSE, $fname = FALSE, $lname = FALSE, $ip = FALSE, $opt_out = FALSE, $user_id = FALSE)
					{
						global $current_site, $current_blog; /* For Multisite support. */
						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_process_list_server_removals", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if (c_ws_plugin__optimizemember_list_servers::list_servers_integrated () && ($args = func_get_args ()) && $role && is_string ($role) && is_numeric ($level) && $login && is_string ($login) && is_string ($pass = (string)$pass) && $email && is_string ($email) && is_email ($email) && is_string ($fname = (string)$fname) && is_string ($lname = (string)$lname) && is_string ($ip = (string)$ip) && is_bool ($opt_out = (bool)$opt_out) && $opt_out && $user_id && is_numeric ($user_id) && is_object ($user = new WP_User ($user_id)) && !empty ($user->ID))
							{
								$ccaps = implode (",", c_ws_plugin__optimizemember_user_access::user_access_ccaps ($user)); /* Get Custom Capabilities. */
								/**/
								$email_configs_were_on = c_ws_plugin__optimizemember_email_configs::email_config_status (); /* optimizeMember Filters enabled? */
								c_ws_plugin__optimizemember_email_configs::email_config_release (); /* Release optimizeMember Filters before we begin this routine. */
								/**/
								if (!empty ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["mailchimp_api_key"]) && !empty ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $level . "_mailchimp_list_ids"]))
									{
										if (!class_exists ("NC_MCAPI")) /* Include the MailChimp API Class here. */
											include_once dirname (dirname (__FILE__)) . "/externals/mailchimp/nc-mcapi.inc.php"; /* MailChimp API ( no-conflict version ). */
										/**/
										$mcapi = new NC_MCAPI ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["mailchimp_api_key"], true); /* MailChimp API ( no-conflict ). */
										/**/
										foreach (preg_split ("/[\r\n\t;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $level . "_mailchimp_list_ids"]) as $mailchimp_list)
											{
												$mailchimp = array ("function" => __FUNCTION__, "func_get_args" => $args, "api_removal_method" => "listUnsubscribe");
												/**/
												if (($mailchimp["list_id"] = trim (preg_replace ("/\:\:.*$/", "", $mailchimp_list)))) /* Trim & strip groups. */
													{
														if ($mailchimp["api_removal_response"] = $mcapi->{$mailchimp["api_removal_method"]}($mailchimp["list_id"], $email, /* See: `http://apidocs.mailchimp.com/`. */
														($mailchimp["api_removal_delete_member"] = apply_filters ("ws_plugin__optimizemember_mailchimp_removal_delete_member", false, get_defined_vars ())), /* Completely delete? */
														($mailchimp["api_removal_send_goodbye"] = apply_filters ("ws_plugin__optimizemember_mailchimp_removal_send_goodbye", false, get_defined_vars ())), /* Send goodbye letter? */
														($mailchimp["api_removal_send_notify"] = apply_filters ("ws_plugin__optimizemember_mailchimp_removal_send_notify", false, get_defined_vars ())))) /* Send notification? */
															$mailchimp["api_removal_success"] = $removal_success = true; /* Flag indicating that we DO have a successful removal; affects the function's overall return value. */
														$mailchimp["api_removal_properties"] = $mcapi; /* Include API instance too; as it contains some additional information after each method is processed ( need this in the logs ). */
														/**/
														$logv = c_ws_plugin__optimizemember_utilities::ver_details ();
														$logm = c_ws_plugin__optimizemember_utilities::mem_details ();
														$log4 = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "\nUser-Agent: " . $_SERVER["HTTP_USER_AGENT"];
														$log4 = (is_multisite () && !is_main_site ()) ? ($_log4 = $current_blog->domain . $current_blog->path) . "\n" . $log4 : $log4;
														$log2 = (is_multisite () && !is_main_site ()) ? "mailchimp-api-4-" . trim (preg_replace ("/[^a-z0-9]/i", "-", $_log4), "-") . ".log" : "mailchimp-api.log";
														/**/
														if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["gateway_debug_logs"])
															if (is_dir ($logs_dir = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["logs_dir"]))
																if (is_writable ($logs_dir) && c_ws_plugin__optimizemember_utils_logs::archive_oversize_log_files ())
																	file_put_contents ($logs_dir . "/" . $log2, $logv . "\n" . $logm . "\n" . $log4 . "\n" . var_export ($mailchimp, true) . "\n\n", FILE_APPEND);
													}
											}
									}
								/**/
								if (!empty ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $level . "_aweber_list_ids"]))
									{
										foreach (preg_split ("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $level . "_aweber_list_ids"]) as $aweber_list)
											{
												$aweber = array ("function" => __FUNCTION__, "func_get_args" => $args, "wp_mail_removal_method" => "listUnsubscribe");
												/**/
												if (($aweber["list_id"] = trim ($aweber_list))) /* Trim this up. NO trailing white space. */
													{
														$aweber["removal_bcc"] = apply_filters ("ws_plugin__optimizemember_aweber_removal_bcc", false, get_defined_vars ());
														/**/
														c_ws_plugin__optimizemember_email_configs::email_config (); /* Email configs MUST be ON for removal requests.
															The `From:` address MUST match AWeber account. See: <http://www.aweber.com/faq/questions/62/Can+I+Unsubscribe+People+Via+Email%3F>. */
														/**/
														if ($aweber["wp_mail_removal_response"] = wp_mail ($aweber["list_id"] . "@aweber.com", /* AWeber List ID converts to email address @aweber.com. */
														($aweber["wp_mail_removal_sbj"] = apply_filters ("ws_plugin__optimizemember_aweber_removal_sbj", "REMOVE#" . $email . "#optimizeMember#" . $aweber["list_id"], get_defined_vars ())), /* Bug fix. AWeber does not like dots ( possibly other chars ) in the Ad Tracking field. Now using just: `optimizeMember`. */
														($aweber["wp_mail_removal_msg"] = "REMOVE"), ($aweber["wp_mail_removal_headers"] = "From: \"" . preg_replace ('/"/', "'", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_name"]) . "\" <" . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_email"] . ">" . (($aweber["removal_bcc"]) ? "\r\nBcc: " . $aweber["removal_bcc"] : "") . "\r\nContent-Type: text/plain; charset=utf-8")))
															$aweber["wp_mail_removal_success"] = $removal_success = true; /* Flag indicating that we DO have a successful removal; affects the function's overall return value. */
														/**/
														c_ws_plugin__optimizemember_email_configs::email_config_release (); /* Release. */
														/**/
														$logv = c_ws_plugin__optimizemember_utilities::ver_details ();
														$logm = c_ws_plugin__optimizemember_utilities::mem_details ();
														$log4 = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "\nUser-Agent: " . $_SERVER["HTTP_USER_AGENT"];
														$log4 = (is_multisite () && !is_main_site ()) ? ($_log4 = $current_blog->domain . $current_blog->path) . "\n" . $log4 : $log4;
														$log2 = (is_multisite () && !is_main_site ()) ? "aweber-api-4-" . trim (preg_replace ("/[^a-z0-9]/i", "-", $_log4), "-") . ".log" : "aweber-api.log";
														/**/
														if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["gateway_debug_logs"])
															if (is_dir ($logs_dir = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["logs_dir"]))
																if (is_writable ($logs_dir) && c_ws_plugin__optimizemember_utils_logs::archive_oversize_log_files ())
																	file_put_contents ($logs_dir . "/" . $log2, $logv . "\n" . $logm . "\n" . $log4 . "\n" . var_export ($aweber, true) . "\n\n", FILE_APPEND);
													}
											}
									}
								/**/
								eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action ("ws_plugin__optimizemember_during_process_list_server_removals", get_defined_vars ());
								unset ($__refs, $__v); /* Unset defined __refs, __v. */
								/**/
								if ($email_configs_were_on) /* Back on? */
									c_ws_plugin__optimizemember_email_configs::email_config ();
							}
						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_after_process_list_server_removals", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						return apply_filters ("ws_plugin__optimizemember_process_list_server_removals", (isset ($removal_success) && $removal_success), get_defined_vars ());
					}

            /**
             * Process list server removals against current user.
             *
             * See {@link process_list_server_removals()} for further details.
             *
             * @since 141004
             * @package optimizeMember\List_Servers
             *
             * @param bool $opt_out Defaults to `FALSE`; must be set to `TRUE`.
             * @param bool $clean_user_cache Defaults to `TRUE`; i.e., we start from a fresh copy of the current user.
             *
             * @return bool True if at least one list server removal is processed successfully.
             */
            public static function process_list_server_removals_against_current_user($opt_out = FALSE, $clean_user_cache = TRUE)
            {
                if($clean_user_cache) // Start from a fresh user object here?
                {
                    clean_user_cache(get_current_user_id());
                    wp_cache_delete(get_current_user_id(), 'user_meta');
                    $user = new WP_User(get_current_user_id());
                }
                else $user = wp_get_current_user();

                return self::process_list_server_removals(
                    ($role = c_ws_plugin__optimizemember_user_access::user_access_role($user)),
                    ($level = c_ws_plugin__optimizemember_user_access::user_access_level($user)),
                    ($login = $user->user_login),
                    ($pass = $user->user_pass),
                    ($email = $user->user_email),
                    ($fname = $user->first_name),
                    ($lname = $user->last_name),
                    ($ip = @$_SERVER['REMOTE_ADDR']),
                    ($opt_out = $opt_out),
                    ($user_id = $user->ID)
                );
            }
				/**
				* Listens to Collective EOT/MOD Events processed internally by optimizeMember.
				*
				* This is only applicable when ``["custom_reg_auto_opt_outs"]`` contains related Event(s).
				*
				* @package optimizeMember\List_Servers
				* @since 3.5
				*
				* @attaches-to ``add_action("ws_plugin__optimizemember_during_collective_mods");``
				* @attaches-to ``add_action("ws_plugin__optimizemember_during_collective_eots");``
				*
				* @param int|str $user_id Required. A WordPress User ID, numeric string or integer.
				* @param array $vars Required. An array of defined variables passed by the calling Hook.
				* @param str $event Required. A specific event that triggered this call from the Action Hook.
				* @param str $event_spec Required. A specific event specification *( a broader classification )*.
				* @param str $mod_new_role Required if ``$event_spec === "modification"`` ( but can be empty ). Role the User is being modified to.
				* @param str $mod_new_user Optional. If ``$event_spec === "modification"``, the new User object with current details.
				* @param str $mod_old_user Optional. If ``$event_spec === "modification"``, the old/previous User obj with old details.
				* @return null This function does not have a return value.
				*/
				public static function auto_process_list_server_removals ($user_id = FALSE, $vars = FALSE, $event = FALSE, $event_spec = FALSE, $mod_new_role = FALSE, $mod_new_user = FALSE, $mod_old_user = FALSE)
					{
						global $current_site, $current_blog; /* For Multisite support. */
						static $auto_processed = array (); /* Process ONE time for each User. */
						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_auto_process_list_server_removals", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						$custom_reg_auto_op_outs = c_ws_plugin__optimizemember_utils_strings::wrap_deep ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_auto_opt_outs"], "/^", "$/i");
						/**/
						if (c_ws_plugin__optimizemember_list_servers::list_servers_integrated () && $user_id && is_numeric ($user_id) && !in_array ($user_id, $auto_processed) && is_array ($vars) && is_string ($event = (string)$event) && is_string ($event_spec = (string)$event_spec) && (c_ws_plugin__optimizemember_utils_arrays::in_regex_array ($event, $custom_reg_auto_op_outs) || c_ws_plugin__optimizemember_utils_arrays::in_regex_array ($event_spec, $custom_reg_auto_op_outs)) && is_object ($user = $_user = new WP_User ($user_id)) && !empty ($user->ID))
							{
								$mod_new_role = ($event_spec === "modification" && $mod_new_role && is_string ($mod_new_role)) ? $mod_new_role : /* Might be empty ( i.e. they now have NO Role ). */ false;
								$mod_new_user = ($event_spec === "modification" && $mod_new_user && is_object ($mod_new_user) && !empty ($mod_new_user->ID) && $mod_new_user->ID === $_user->ID) ? $mod_new_user : false;
								$mod_old_user = ($event_spec === "modification" && $mod_old_user && is_object ($mod_old_user) && !empty ($mod_old_user->ID) && $mod_old_user->ID === $_user->ID) ? $mod_old_user : false;
								/**/
								$user = ($event_spec === "modification" && $mod_old_user) ? $mod_old_user : $_user; /* Now, should we switch over to the old/previous User object ``$mod_old_user`` here? Or, should we use the one pulled by this routine with the User's ID? */
								/**/
								if (($event_spec !== "modification" || ($event_spec === "modification" && /* Might be empty ( i.e. they now have NO Role ). */ (string)$mod_new_role !== c_ws_plugin__optimizemember_user_access::user_access_role ($user) && strtotime ($user->user_registered) < strtotime ("-10 seconds") && ($event !== "user-role-change" || ($event === "user-role-change" && !empty ($vars["_p"]["ws_plugin__optimizemember_custom_reg_auto_opt_out_transitions"]))))) && ($auto_processed[$user->ID] = true))
									{
										$removed = c_ws_plugin__optimizemember_list_servers::process_list_server_removals (c_ws_plugin__optimizemember_user_access::user_access_role ($user), c_ws_plugin__optimizemember_user_access::user_access_level ($user), $user->user_login, false, $user->user_email, $user->first_name, $user->last_name, false, true, $user->ID);
										/**/
										if ($event_spec === "modification" && $mod_new_role && ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_auto_opt_out_transitions"] === "2" || ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_auto_opt_out_transitions"] === "1" && $removed)) /* Transitoning User/Member to different list(s)? */)
											{
												$user = ($event_spec === "modification" && $mod_new_user) ? $mod_new_user : $_user; /* Now, should we switch over to a new/current User object ``$mod_new_user`` here? ( which may contain newly updated details ). Or, should we simply use the User object pulled by this routine with the User's ID? */
												/**/
												$transitioned = c_ws_plugin__optimizemember_list_servers::process_list_servers ($mod_new_role, c_ws_plugin__optimizemember_user_access::user_access_role_to_level ($mod_new_role), $user->user_login, false, $user->user_email, $user->first_name, $user->last_name, false, true, (($removed) ? false : true), $user->ID);
												/**/
												eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
												do_action ("ws_plugin__optimizemember_during_auto_process_list_server_removal_transitions", get_defined_vars ());
												unset ($__refs, $__v); /* Unset defined __refs, __v. */
											}
										/**/
										eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
										do_action ("ws_plugin__optimizemember_during_auto_process_list_server_removals", get_defined_vars ());
										unset ($__refs, $__v); /* Unset defined __refs, __v. */
									}
							}
						/**/
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_after_auto_process_list_server_removals", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>