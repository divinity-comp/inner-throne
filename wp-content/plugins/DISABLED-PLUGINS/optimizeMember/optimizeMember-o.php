<?php
/**
* WordPress with optimizeMember only.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the UK )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember
* @since 110912
*/
include_once dirname (__FILE__) . "/includes/classes/utils-s2o.inc.php";
/**/
if (($ws_plugin__optimizemember_o["wp_dir"] = c_ws_plugin__optimizemember_utils_s2o::wp_dir (dirname (__FILE__), dirname ($_SERVER["SCRIPT_FILENAME"]))))
	{
		if (($ws_plugin__optimizemember_o["wp_settings_as"] = c_ws_plugin__optimizemember_utils_s2o::wp_settings_as ($ws_plugin__optimizemember_o["wp_dir"], __FILE__)))
			{
				/**
				* Short initialization mode for WordPress.
				*
				* @package optimizeMember
				* @since 110912
				*
				* @var bool
				*/
				define ("SHORTINIT", true);
				/**
				* Flag indicating only optimizeMember is being loaded.
				*
				* @package optimizeMember
				* @since 110912
				*
				* @var bool
				*/
				define ("WS_PLUGIN__OPTIMIZEMEMBER_ONLY", true);
				/*
				Load WordPress.
				*/
				require($ws_plugin__optimizemember_o["wp_dir"] . "/wp-load.php");
				eval ("?>" . /* Settings after ``SHORTINIT``. */ $ws_plugin__optimizemember_o["wp_settings_as"]);
			}
		else /* Else fallback on full WordPress. */
			require($ws_plugin__optimizemember_o["wp_dir"] . "/wp-load.php");
	}
unset ($ws_plugin__optimizemember_o);
?>