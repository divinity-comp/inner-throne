<?php
/**
* CSS/JS loading handlers for optimizeMember ( inner processing routines ).
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
* @package optimizeMember\CSS_JS
* @since 3.5
*/
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**/
if(!class_exists("c_ws_plugin__optimizemember_css_js_in"))
	{
		/**
		* CSS/JS loading handlers for optimizeMember ( inner processing routines ).
		*
		* @package optimizeMember\CSS_JS
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_css_js_in
			{
				/**
				* Outputs CSS for theme integration.
				*
				* @package optimizeMember\CSS_JS
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after loading CSS.
				*/
				public static function css()
					{
						do_action("ws_plugin__optimizemember_before_css", get_defined_vars());
						/**/
						if(!empty($_GET["ws_plugin__optimizemember_css"]))
							{
								status_header(200); /* 200 OK status. */
								/**/
								header("Content-Type: text/css; charset=utf-8");
								header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("+1 week"))." GMT");
								header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
								header("Cache-Control: max-age=604800");
								header("Pragma: public");
								/**/
								eval('while (@ob_end_clean ());');
								/**/
								$u = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"];
								$i = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images";
								/**/
								ob_start("c_ws_plugin__optimizemember_utils_css::compress_css");
								/**/
								include_once dirname(dirname(__FILE__))."/optimizemember.css";
								/**/
								do_action("ws_plugin__optimizemember_during_css", get_defined_vars());
								/**/
								exit(); /* Clean exit. */
							}
						/**/
						do_action("ws_plugin__optimizemember_after_css", get_defined_vars());
					}
				/**
				* Outputs JS for theme integration.
				*
				* Be sure optimizeMember's API Constants are already defined before firing this.
				*
				* @package optimizeMember\CSS_JS
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after loading JS w/Globals.
				*/
				public static function js_w_globals()
					{
						do_action("ws_plugin__optimizemember_before_js_w_globals", get_defined_vars());
						/**/
						if(!empty($_GET["ws_plugin__optimizemember_js_w_globals"]))
							{
								status_header(200); /* 200 OK status header. */
								/**/
								header("Content-Type: application/x-javascript; charset=utf-8");
								header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("+1 week"))." GMT");
								header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
								header("Cache-Control: max-age=604800");
								header("Pragma: public");
								/**/
								eval('while (@ob_end_clean ());');
								/**/
								include_once dirname(dirname(__FILE__))."/jquery/jquery.sprintf/jquery.sprintf-min.js";
								/**/
								echo "\n"; /* Add a line break before writing JavaScript Globals to file. */
								/**/
								echo "var OPTIMIZEMEMBER_VERSION = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_VERSION)."',";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_LOGIN_COUNTER = ".OPTIMIZEMEMBER_CURRENT_USER_LOGIN_COUNTER.",";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN = ".((OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN) ? "true" : "false").",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER = ".((OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER) ? "true" : "false").",";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_ACCESS_LEVEL = ".OPTIMIZEMEMBER_CURRENT_USER_ACCESS_LEVEL.",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_ACCESS_LABEL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_ACCESS_LABEL)."',";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_SUBSCR_ID = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_SUBSCR_ID)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_SUBSCR_OR_WP_ID = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_SUBSCR_OR_WP_ID)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_SUBSCR_GATEWAY = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_SUBSCR_GATEWAY)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_CUSTOM = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_CUSTOM)."',";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_REGISTRATION_TIME = ".OPTIMIZEMEMBER_CURRENT_USER_REGISTRATION_TIME.",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_PAID_REGISTRATION_TIME = ".OPTIMIZEMEMBER_CURRENT_USER_PAID_REGISTRATION_TIME.",";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS = ".OPTIMIZEMEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS.",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_REGISTRATION_DAYS = ".OPTIMIZEMEMBER_CURRENT_USER_REGISTRATION_DAYS.",";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_DISPLAY_NAME = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_DISPLAY_NAME)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_FIRST_NAME = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_FIRST_NAME)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_LAST_NAME = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_LAST_NAME)."',";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_LOGIN = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_LOGIN)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_EMAIL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_EMAIL)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_IP = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_IP)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_REGISTRATION_IP = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_REGISTRATION_IP)."',";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_ID = ".OPTIMIZEMEMBER_CURRENT_USER_ID.",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_FIELDS = ".OPTIMIZEMEMBER_CURRENT_USER_FIELDS.",";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_ALLOWED = ".OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_ALLOWED.",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED = ".((OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED) ? "true" : "false").",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY = ".OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY.",";
								echo "OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS = ".OPTIMIZEMEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS.",";
								/**/
								echo "OPTIMIZEMEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID = ".OPTIMIZEMEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID.",";
								echo "OPTIMIZEMEMBER_MEMBERSHIP_OPTIONS_PAGE_ID = ".OPTIMIZEMEMBER_MEMBERSHIP_OPTIONS_PAGE_ID.",";
								echo "OPTIMIZEMEMBER_LOGIN_WELCOME_PAGE_ID = ".OPTIMIZEMEMBER_LOGIN_WELCOME_PAGE_ID.",";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL)."',";
								echo "OPTIMIZEMEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL)."',";
								echo "OPTIMIZEMEMBER_MEMBERSHIP_OPTIONS_PAGE_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_MEMBERSHIP_OPTIONS_PAGE_URL)."',";
								echo "OPTIMIZEMEMBER_LOGIN_WELCOME_PAGE_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_LOGIN_WELCOME_PAGE_URL)."',";
								echo "OPTIMIZEMEMBER_LOGOUT_PAGE_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_LOGOUT_PAGE_URL)."',";
								echo "OPTIMIZEMEMBER_LOGIN_PAGE_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_LOGIN_PAGE_URL)."',";
								/**/
								for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
									{
										if(defined(($OPTIMIZEMEMBER_LEVELn_LABEL = "OPTIMIZEMEMBER_LEVEL".$n."_LABEL")))
											echo $OPTIMIZEMEMBER_LEVELn_LABEL." = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(constant($OPTIMIZEMEMBER_LEVELn_LABEL))."',";
									}
								/**/
								for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
									{
										if(defined(($OPTIMIZEMEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED = "OPTIMIZEMEMBER_LEVEL".$n."_FILE_DOWNLOADS_ALLOWED")))
											echo $OPTIMIZEMEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED." = ".constant($OPTIMIZEMEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED).",";
									}
								/**/
								for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
									{
										if(defined(($OPTIMIZEMEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS = "OPTIMIZEMEMBER_LEVEL".$n."_FILE_DOWNLOADS_ALLOWED_DAYS")))
											echo $OPTIMIZEMEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS." = ".constant($OPTIMIZEMEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS).",";
									}
								/**/
								echo "OPTIMIZEMEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS)."',";
								/**/
								echo "OPTIMIZEMEMBER_REG_EMAIL_FROM_NAME = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_REG_EMAIL_FROM_NAME)."',";
								echo "OPTIMIZEMEMBER_REG_EMAIL_FROM_EMAIL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_REG_EMAIL_FROM_EMAIL)."',";
								/**/
								echo "OPTIMIZEMEMBER_PAYPAL_NOTIFY_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_PAYPAL_NOTIFY_URL)."',";
								echo "OPTIMIZEMEMBER_PAYPAL_RETURN_URL = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_PAYPAL_RETURN_URL)."',";
								/**/
								echo "OPTIMIZEMEMBER_PAYPAL_BUSINESS = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_PAYPAL_BUSINESS)."',";
								echo "OPTIMIZEMEMBER_PAYPAL_ENDPOINT = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_PAYPAL_ENDPOINT)."',";
								echo "OPTIMIZEMEMBER_PAYPAL_API_ENDPOINT = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_PAYPAL_API_ENDPOINT)."',";
								/**/
								echo "OPTIMIZEMEMBER_VALUE_FOR_PP_INV = Math.round (new Date ().getTime ()) + '~".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_IP)."',";
								echo "OPTIMIZEMEMBER_VALUE_FOR_PP_INV_GEN = optimizemember_value_for_pp_inv_gen = function(){ var invoice = '', formatSeed = function(seed, reqWidth) { seed = parseInt(seed, 10).toString (16); if (reqWidth < seed.length) return seed.slice (seed.length - reqWidth); else if (reqWidth > seed.length) return Array(1 + (reqWidth - seed.length)).join ('0') + seed; return seed; }; if (typeof OPTIMIZEMEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED === 'undefined') OPTIMIZEMEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED = Math.floor (Math.random () * 0x75bcd15); OPTIMIZEMEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED++; invoice = formatSeed(parseInt(new Date ().getTime () / 1000, 10), 8); invoice += formatSeed(OPTIMIZEMEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED, 5); invoice += '~' + OPTIMIZEMEMBER_CURRENT_USER_IP; return invoice; },";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_ON0 = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_ON0)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_OS0 = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_OS0)."',";
								/**/
								echo "OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_ON1 = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_ON1)."',";
								echo "OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_OS1 = '".c_ws_plugin__optimizemember_utils_strings::esc_js_sq(OPTIMIZEMEMBER_CURRENT_USER_VALUE_FOR_PP_OS1)."';";
								/**/
								$u = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"];
								$i = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images";
								/**/
								echo "\n"; /* Add a line break before inclusion. */
								/**/
								include_once dirname(dirname(__FILE__))."/optimizemember-min.js";
								/**/
								do_action("ws_plugin__optimizemember_during_js_w_globals", get_defined_vars());
								/**/
								exit(); /* Clean exit. */
							}
						/**/
						do_action("ws_plugin__optimizemember_after_js_w_globals", get_defined_vars());
					}
			}
	}
?>