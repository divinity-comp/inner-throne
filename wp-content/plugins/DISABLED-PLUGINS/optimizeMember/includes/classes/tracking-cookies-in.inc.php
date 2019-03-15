<?php
/**
* Tracking Cookies ( inner processing routines ).
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
* @package optimizeMember\Tracking
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_tracking_cookies_in"))
	{
		/**
		* Tracking Cookies ( inner processing routines ).
		*
		* @package optimizeMember\Tracking
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_tracking_cookies_in
			{
				/**
				* Deletes optimizeMember's temporary tracking cookie.
				*
				* @package optimizeMember\Tracking
				* @since 110815
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after deleting Cookie.
				*/
				public static function delete_tracking_cookie ()
					{
						do_action ("ws_plugin__optimizemember_before_delete_tracking_cookie", get_defined_vars ());
						/**/
						if (!empty ($_GET["optimizemember_delete_tracking_cookie"])) /* Deletes cookie. */
							{
								setcookie ("optimizemember_tracking", "", time () + 31556926, COOKIEPATH, COOKIE_DOMAIN);
								setcookie ("optimizemember_tracking", "", time () + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
								/**/
								do_action ("ws_plugin__optimizemember_during_delete_tracking_cookie", get_defined_vars ());
								/**/
								@ini_set ("zlib.output_compression", 0); /* Turn off. */
								/**/
								status_header (200); /* Send a 200 OK status header. */
								header ("Content-Type: image/png"); /* Content-Type image/png for 1px transparency. */
								eval ('while (@ob_end_clean ());'); /* End/clean all output buffers that may or may not exist. */
								/**/
								exit (file_get_contents (dirname (dirname (dirname (__FILE__))) . "/images/trans-1px.png"));
							}
						/**/
						do_action ("ws_plugin__optimizemember_after_delete_tracking_cookie", get_defined_vars ());
					}
				/**
				* Deletes optimizeMember's temporary tracking cookie.
				*
				* @package optimizeMember\Tracking
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after deleting Cookie.
				*/
				public static function delete_sp_tracking_cookie ()
					{
						do_action ("ws_plugin__optimizemember_before_delete_sp_tracking_cookie", get_defined_vars ());
						/**/
						if (!empty ($_GET["optimizemember_delete_sp_tracking_cookie"])) /* Deletes cookie. */
							{
								setcookie ("optimizemember_sp_tracking", "", time () + 31556926, COOKIEPATH, COOKIE_DOMAIN);
								setcookie ("optimizemember_sp_tracking", "", time () + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
								/**/
								do_action ("ws_plugin__optimizemember_during_delete_sp_tracking_cookie", get_defined_vars ());
								/**/
								@ini_set ("zlib.output_compression", 0); /* Turn off. */
								/**/
								status_header (200); /* Send a 200 OK status header. */
								header ("Content-Type: image/png"); /* Content-Type image/png for 1px transparency. */
								eval ('while (@ob_end_clean ());'); /* End/clean all output buffers that may or may not exist. */
								/**/
								exit (file_get_contents (dirname (dirname (dirname (__FILE__))) . "/images/trans-1px.png"));
							}
						/**/
						do_action ("ws_plugin__optimizemember_after_delete_sp_tracking_cookie", get_defined_vars ());
					}
			}
	}
?>