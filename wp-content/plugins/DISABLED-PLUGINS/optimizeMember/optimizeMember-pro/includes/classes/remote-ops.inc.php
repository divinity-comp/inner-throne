<?php
/**
* optimizeMember Pro Remote Operations API.
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
if (!class_exists ("c_ws_plugin__optimizemember_pro_remote_ops"))
	{
		/**
		* optimizeMember Pro Remote Operations API.
		*
		* @package optimizeMember\API_Remote_Ops
		* @since 110713
		*/
		class c_ws_plugin__optimizemember_pro_remote_ops
			{
				/**
				* Handles Remote Operation communications.
				*
				* @package optimizeMember\API_Remote_Ops
				* @since 110713
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution with a serialized array on success, or a string beginning with `Error:` on failure.
				*/
				public static function remote_ops ()
					{
						if (!empty ($_GET["optimizemember_pro_remote_op"]) && !empty ($_POST["optimizemember_pro_remote_op"])) {
							c_ws_plugin__optimizemember_no_cache::no_cache_constants (true);
							/**/
							status_header(200);
							header("Content-Type: text/plain; charset=utf-8");
							eval('while (@ob_end_clean ());');
							/**/
							if (is_array ($op = maybe_unserialize (c_ws_plugin__optimizemember_utils_strings::trim_deep (stripslashes_deep ($_POST["optimizemember_pro_remote_op"])))))
								{
									if (is_array ($op =  /* Now trim again, in case of serialized array. */c_ws_plugin__optimizemember_utils_strings::trim_deep ($op)))
										{
											if (!empty ($op["api_key"]) && $op["api_key"] === c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen ())
												{
													if (!empty ($op["op"]) && is_callable ("c_ws_plugin__optimizemember_pro_remote_ops_in::" . $op["op"]))
															exit(call_user_func ("c_ws_plugin__optimizemember_pro_remote_ops_in::" . $op["op"], $op));

													exit('Error: $_POST["optimizemember_pro_remote_op"]["op"] is empty or invalid.');
												}
											exit('Error: $_POST["optimizemember_pro_remote_op"]["api_key"] is empty or invalid.');
										}
									exit('Error: $_POST["optimizemember_pro_remote_op"] is NOT a serialized array.');
								}
							exit('Error: $_POST["optimizemember_pro_remote_op"] is NOT a serialized array.');

						} elseif (!empty($_GET['optimizemember_pro_remote_op_integration'])) {
							c_ws_plugin__optimizemember_no_cache::no_cache_constants (true);
							/**/
							status_header(200);
							header("Content-Type: text/plain; charset=utf-8");
							eval('while (@ob_end_clean ());');

							if ((!isset($_GET['method']) || $_GET['method'] !== 'get') && is_array ($_POST =  /* Now trim again, in case of serialized array. */c_ws_plugin__optimizemember_utils_strings::trim_deep ($_POST)) && count($_POST) > 0) {
								if (!empty ($_POST["api_key"]) && $_POST["api_key"] === c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen ()) {
									if (!empty ($_POST["op"]) && is_callable ("c_ws_plugin__optimizemember_pro_remote_ops_in::" . $_POST["op"])) {
										exit(call_user_func ("c_ws_plugin__optimizemember_pro_remote_ops_in::" . $_POST["op"], $_POST));
									}
									exit('Error: $_POST["op"] is empty or invalid.');
								}
								exit('Error: $_POST["api_key"] is empty or invalid.');
							} else if (is_array ($_GET =  /* Now trim again, in case of serialized array. */c_ws_plugin__optimizemember_utils_strings::trim_deep ($_GET)) && $_GET > 1) {
								if (!empty ($_GET["api_key"]) && $_GET["api_key"] === c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen ()) {
									if (!empty ($_GET["op"]) && is_callable ("c_ws_plugin__optimizemember_pro_remote_ops_in::" . $_GET["op"])) {
										exit(call_user_func ("c_ws_plugin__optimizemember_pro_remote_ops_in::" . $_GET["op"], $_GET));
									}
									exit('Error: $_GET["op"] is empty or invalid.');
								}
								exit('Error: $_GET["api_key"] is empty or invalid.');
							}
							exit('Error: $_POST or $_GET is NOT a serialized array.');
						} elseif (!empty($_GET['optimizemember_pro_jvzoo_op_integration'])) {
							c_ws_plugin__optimizemember_no_cache::no_cache_constants (true);
							/**/
							status_header(200);
							header("Content-Type: text/plain; charset=utf-8");
							eval('while (@ob_end_clean ());');

							if (is_array ($_GET =  /* Now trim again, in case of serialized array. */c_ws_plugin__optimizemember_utils_strings::trim_deep ($_GET)) && $_GET > 1) {
								if (!empty ($_GET["api_key"]) && $_GET["api_key"] === c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen ()) {
									if (!empty ($_GET["op"]) && is_callable ("c_ws_plugin__optimizemember_pro_jvzoo_ops_in::" . $_GET["op"])) {
										exit(call_user_func("c_ws_plugin__optimizemember_pro_jvzoo_ops_in::" . $_GET["op"], c_ws_plugin__optimizemember_utils_strings::trim_deep($_POST)));
									}
									exit('Error: $_GET["op"] is empty or invalid.');
								}
								exit('Error: $_GET["api_key"] is empty or invalid.');
							}
							exit('Error: $_POST or $_GET is NOT a serialized array.');
						}
					}
				/**
				* Test if this WordPress instance is a specific Remote Operation.
				*
				* @package optimizeMember\API_Remote_Ops
				* @since 110713
				*
				* @param str $_op The Remote Operation to test this instance against.
				* @return bool True if instance is the specified Operation, else false.
				*/
				public static function is_remote_op ($_op = FALSE)
					{
						if (!empty ($_GET["optimizemember_pro_remote_op"]) && !empty ($_POST["optimizemember_pro_remote_op"]))
							if (is_array ($op = maybe_unserialize (c_ws_plugin__optimizemember_utils_strings::trim_deep (stripslashes_deep ($_POST["optimizemember_pro_remote_op"])))))
								if (is_array ($op =  /* Now trim again, in case of serialized array. */c_ws_plugin__optimizemember_utils_strings::trim_deep ($op)))
									if (!empty ($op["api_key"]) && $op["api_key"] === c_ws_plugin__optimizemember_pro_remote_ops::remote_ops_key_gen ())
										if (!empty ($op["op"]) && $op["op"] === $_op)
											return true;
						return false;
					}
				/**
				* Generates an API Key, for Remote Operations.
				*
				* @package optimizeMember\API_Remote_Ops
				* @since 110713
				*
				* @return str An API Key. It's an MD5 Hash, 32 chars, URL-safe.
				*/
				public static function remote_ops_key_gen ()
					{
						global /* Multisite Networking. */ $current_site, $current_blog;
						/**/
						if (is_multisite () && !is_main_site ())
							$key = md5 (c_ws_plugin__optimizemember_utils_encryption::xencrypt ($current_blog->domain . $current_blog->path, false, false));
						/**/
						else /* Else it's a standard API Key; not on a Multisite Network, or not on the Main Site anyway. */
							$key = md5 (c_ws_plugin__optimizemember_utils_encryption::xencrypt (preg_replace ("/\:[0-9]+$/", "", $_SERVER["HTTP_HOST"]), false, false));
						/**/
						return apply_filters("ws_plugin__optimizemember_pro_remote_ops_key", (!empty ($key)) ? $key : "");
					}
			}
	}
?>