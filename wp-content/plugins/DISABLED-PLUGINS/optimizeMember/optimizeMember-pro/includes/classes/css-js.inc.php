<?php
/**
* CSS/JS integrations with theme.
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
* @package optimizeMember\CSS_JS
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_css_js"))
	{
		/**
		* CSS/JS integrations with theme.
		*
		* @package optimizeMember\CSS_JS
		* @since 1.5
		*/
		class c_ws_plugin__optimizemember_pro_css_js
			{
				/**
				* Adds Pro Module CSS.
				*
				* @package optimizeMember\CSS_JS
				* @since 1.5
				*
				* @attaches-to ``add_action("ws_plugin__optimizemember_during_css");``
				*
				* @param array $vars Expects array of defined variables, passed in by the Action Hook.
				* @return null
				*/
				public static function css ($vars = FALSE)
					{
						$u = $GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"];
						$i = $GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images";
						/**/
						echo "\n"; /* Add a line break before inclusion. */
						/**/
						include_once dirname (dirname (__FILE__)) . "/optimizemember-pro.css";
						/**/
						return; /* Return unformity. */
					}
				/**
				* Adds Pro Module JavaScript.
				*
				* @package optimizeMember\CSS_JS
				* @since 1.5
				*
				* @attaches-to ``add_action("ws_plugin__optimizemember_during_js_w_globals");``
				*
				* @param array $vars Expects array of defined variables, passed in by the Action Hook.
				* @return null
				*/
				public static function js_w_globals ($vars = FALSE)
					{
						$g = "var OPTIMIZEMEMBER_PRO_VERSION = '" . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (OPTIMIZEMEMBER_PRO_VERSION) . "',";
						/**/
						$g = trim ($g, " ,") . ";"; /* Trim & add semicolon. */
						/**/
						$u = $GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"];
						$i = $GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images";
						/**/
						echo "\n" . $g . "\n"; /* Add a line break before inclusion. */
						/**/
						include_once dirname (dirname (__FILE__)) . "/optimizemember-pro-min.js";
						/**/
						return; /* Return unformity. */
					}
			}
	}
?>