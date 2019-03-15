<?php
/**
* Administrative CSS/JS for menu pages.
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
* @package optimizeMember\Admin_CSS_JS
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_admin_css_js"))
	{
		/**
		* Administrative CSS/JS for menu pages.
		*
		* @package optimizeMember\Admin_CSS_JS
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_admin_css_js
			{
				/**
				* Outputs the CSS for administrative menu pages.
				*
				* @package optimizeMember\Admin_CSS_JS
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function menu_pages_css ()
					{
						if (!empty ($_GET["ws_plugin__optimizemember_menu_pages_css"])) /* Call inner routine? */
							{
								return c_ws_plugin__optimizemember_admin_css_js_in::menu_pages_css ();
							}
					}
				/**
				* Outputs the JS for administrative menu pages.
				*
				* @package optimizeMember\Admin_CSS_JS
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function menu_pages_js ()
					{
						if (!empty ($_GET["ws_plugin__optimizemember_menu_pages_js"])) /* Call inner routine? */
							{
								return c_ws_plugin__optimizemember_admin_css_js_in::menu_pages_js ();
							}
					}
			}
	}
?>