<?php
/**
* optimizeMember's self re-activation routines.
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
* @package optimizeMember\Installation
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_check_activation"))
	{
		/**
		* optimizeMember's self re-activation routines.
		*
		* @package optimizeMember\Installation
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_check_activation
			{
				/**
				* Checks for existing installs that are NOT yet re-activated.
				*
				* @package optimizeMember\Installation
				* @since 3.5
				*
				* @attaches-to ``add_action("admin_init");``
				*
				* @return null
				*/
				public static function check () /* Up-to-date? */
					{
						if (!($v = get_option ("ws_plugin__optimizemember_activated_version")) || !version_compare ($v, WS_PLUGIN__OPTIMIZEMEMBER_VERSION, ">="))
							{
								c_ws_plugin__optimizemember_installation::activate ("version");
							}
						else if (is_multisite () && is_main_site () && (!($mms_v = get_option ("ws_plugin__optimizemember_activated_mms_version")) || !version_compare ($mms_v, WS_PLUGIN__OPTIMIZEMEMBER_VERSION, ">=")))
							{
								c_ws_plugin__optimizemember_installation::activate ("mms_version");
							}
						else if (!($l = (int)get_option ("ws_plugin__optimizemember_activated_levels")) || $l !== $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"])
							{
								c_ws_plugin__optimizemember_installation::activate ("levels");
							}
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>