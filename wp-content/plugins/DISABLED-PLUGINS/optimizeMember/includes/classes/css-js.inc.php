<?php
/**
* CSS/JS loading handlers for optimizeMember.
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
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_css_js"))
	{
		/**
		* CSS/JS loading handlers for optimizeMember.
		*
		* @package optimizeMember\CSS_JS
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_css_js
			{
				/**
				* Outputs CSS for theme integration.
				*
				* @package optimizeMember\CSS_JS
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function css ()
					{
						if (!empty ($_GET["ws_plugin__optimizemember_css"]))
							{
								return c_ws_plugin__optimizemember_css_js_in::css ();
							}
					}
				/**
				* Outputs JS for theme integration.
				*
				* @package optimizeMember\CSS_JS
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function js_w_globals ()
					{
						if (!empty ($_GET["ws_plugin__optimizemember_js_w_globals"]))
							{
								return c_ws_plugin__optimizemember_css_js_in::js_w_globals ();
							}
					}
			}
	}
?>