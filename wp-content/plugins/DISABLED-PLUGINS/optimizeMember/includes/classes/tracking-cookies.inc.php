<?php
/**
* Tracking Cookies.
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
if (!class_exists ("c_ws_plugin__optimizemember_tracking_cookies"))
	{
		/**
		* Tracking Cookies.
		*
		* @package optimizeMember\Tracking
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_tracking_cookies
			{
				/**
				* Deletes optimizeMember's temporary tracking cookie.
				*
				* @package optimizeMember\Tracking
				* @since 110815
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function delete_tracking_cookie ()
					{
						if (!empty ($_GET["optimizemember_delete_tracking_cookie"])) /* Call inner routine? */
							{
								return c_ws_plugin__optimizemember_tracking_cookies_in::delete_tracking_cookie ();
							}
					}
				/**
				* Deletes optimizeMember's temporary tracking cookie.
				*
				* @package optimizeMember\Tracking
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function delete_sp_tracking_cookie ()
					{
						if (!empty ($_GET["optimizemember_delete_sp_tracking_cookie"])) /* Call inner routine? */
							{
								return c_ws_plugin__optimizemember_tracking_cookies_in::delete_sp_tracking_cookie ();
							}
					}
			}
	}
?>