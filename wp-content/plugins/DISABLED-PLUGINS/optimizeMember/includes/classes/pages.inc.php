<?php
/**
* optimizeMember's Page protection routines *( for current Page )*.
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
* @package optimizeMember\Pages
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pages"))
	{
		/**
		* optimizeMember's Page protection routines *( for current Page )*.
		*
		* @package optimizeMember\Pages
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_pages
			{
				/**
				* Handles Page Level Access permissions *( for current Page )*.
				*
				* @package optimizeMember\Pages
				* @since 3.5
				*
				* @return null Or exits script execution after redirection.
				*/
				public static function check_page_level_access ()
					{
						global $post; /* ``get_the_ID()`` unavailable outside The Loop. */
						/**/
						do_action ("ws_plugin__optimizemember_before_check_page_level_access", get_defined_vars ());
						/**/
						$excluded = apply_filters ("ws_plugin__optimizemember_check_page_level_access_excluded", false, get_defined_vars ());
						/**/
						if (!$excluded && is_page () && is_object ($post) && !empty ($post->ID) && ($page_id = (int)$post->ID) && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"])
							{
								if (!c_ws_plugin__optimizemember_systematics::is_wp_systematic_use_page ()) /* Do NOT touch WordPress Systematics. This excludes all WordPress Systematics. */
									{
										$user = (is_user_logged_in () && is_object ($user = wp_get_current_user ()) && !empty ($user->ID)) ? $user : false; /* Current User's object. */
										/**/
										if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_welcome_page"] && $page_id === (int)$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_welcome_page"] && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level0")) && $page_id !== (int)$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"])
											c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", 0, $_SERVER["REQUEST_URI"], "sys") . exit ();
										/**/
										else if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_redirection_override"] && ($login_redirection_uri = c_ws_plugin__optimizemember_login_redirects::login_redirection_uri ($user, "root-returns-false")) && preg_match ("/^" . preg_quote ($login_redirection_uri, "/") . "$/", $_SERVER["REQUEST_URI"]) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level0")) && $page_id !== (int)$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"])
											c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", 0, $_SERVER["REQUEST_URI"], "sys") . exit ();
										/**/
										else if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["file_download_limit_exceeded_page"] && $page_id === (int)$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["file_download_limit_exceeded_page"] && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level0")) && $page_id !== (int)$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"])
											c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", 0, $_SERVER["REQUEST_URI"], "sys") . exit ();
										/**/
										else if (!c_ws_plugin__optimizemember_systematics::is_systematic_use_page ()) /* Never restrict Systematics. However, there are 3 exceptions above. */
											{
												for ($n = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n >= 0; $n--) /* Page Level restrictions. Go through each Level. */
													{
														if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_pages"] === "all" && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level" . $n)))
															c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", $n, $_SERVER["REQUEST_URI"]) . exit ();
														/**/
														else if (strpos ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_posts"], "all-") && in_array ("all-pages", preg_split ("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_posts"])) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level" . $n)))
															c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", $n, $_SERVER["REQUEST_URI"], "post") . exit ();
														/**/
														else if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_pages"] && in_array ($page_id, preg_split ("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_pages"])) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level" . $n)))
															c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", $n, $_SERVER["REQUEST_URI"]) . exit ();
													}
												/**/
												if (has_tag ()) /* Here we take a look to see if this Page has any Tags. If so, we need to run the full set of routines against Tags also. */
													{
														for ($n = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n >= 0; $n--) /* Tag Level restrictions ( possibly through Page Tagger ). Go through each Level. */
															{
																if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_ptags"] === "all" && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level" . $n)))
																	c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", $n, $_SERVER["REQUEST_URI"], "ptag") . exit ();
																/**/
																else if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_ptags"] && has_tag (preg_split ("/[\r\n\t;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_ptags"])) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level" . $n)))
																	c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", $n, $_SERVER["REQUEST_URI"], "ptag") . exit ();
															}
													}
												/**/
												for ($n = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n >= 0; $n--) /* URIs. Go through each Level. */
													{
														if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_ruris"]) /* URIs configured at this Level? */
															/**/
															foreach (preg_split ("/[\r\n\t]+/", c_ws_plugin__optimizemember_ruris::fill_ruri_level_access_rc_vars ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_ruris"], $user)) as $str)
																if ($str && preg_match ("/" . preg_quote ($str, "/") . "/", $_SERVER["REQUEST_URI"]) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && (!$user || !$user->has_cap ("access_optimizemember_level" . $n)))
																	c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "level", $n, $_SERVER["REQUEST_URI"], "ruri") . exit ();
													}
												/**/
												if (is_array ($ccaps_req = get_post_meta ($page_id, "optimizemember_ccaps_req", true)) && !empty ($ccaps_req) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true))
													{
														foreach ($ccaps_req as $ccap) /* The ``$user`` MUST satisfy ALL Custom Capability requirements. Stored as an array of Custom Capabilities. */
															if (strlen ($ccap) && (!$user || !$user->has_cap ("access_optimizemember_ccap_" . $ccap)) /* Does this ``$user``, have this Custom Capability? */)
																c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "ccap", $ccap, $_SERVER["REQUEST_URI"], "ccap") . exit ();
													}
												/**/
													$dripDays = get_post_meta($page_id, "optimizemember_drip_days", true);
												if (!empty ($dripDays) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true))
													{
														$dripUrl = get_post_meta($page_id, "optimizemember_drip_redirect_url", true);
														$time = optimizemember_paid_registration_time('level' . OPTIMIZEMEMBER_CURRENT_USER_ACCESS_LEVEL);
														if ($time && $time >= ($_days_ago = strtotime("-$dripDays days")) && !current_user_can('manage_options')) {
															if (!empty($dripUrl)) {
																wp_redirect($dripUrl); exit;
															} else {
																c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "drip_days", $dripDays, $_SERVER["REQUEST_URI"], "drip_days") . exit ();
															}
														} 
													}
												/**/
												if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"] && in_array ($page_id, preg_split ("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"])) && c_ws_plugin__optimizemember_no_cache::no_cache_constants (true) && !c_ws_plugin__optimizemember_sp_access::sp_access ($page_id))
													c_ws_plugin__optimizemember_mo_page::wp_redirect_w_mop_vars /* Configure MOP Vars here. */ ("page", $page_id, "sp", $page_id, $_SERVER["REQUEST_URI"], "sp") . exit ();
											}
										/**/
										do_action ("ws_plugin__optimizemember_during_check_page_level_access", get_defined_vars ());
									}
							}
						/**/
						do_action ("ws_plugin__optimizemember_after_check_page_level_access", get_defined_vars ());
						/**/
						return; /* For uniformity. */
					}
			}
	}
?>