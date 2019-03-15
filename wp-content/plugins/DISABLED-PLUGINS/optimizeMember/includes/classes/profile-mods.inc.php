<?php
/**
* optimizeMember Profile modifications.
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
* @package optimizeMember\Profiles
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_profile_mods"))
	{
		/**
		* optimizeMember Profile modifications.
		*
		* @package optimizeMember\Profiles
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_profile_mods
			{
				/**
				* Handles Profile modifications.
				*
				* @package optimizeMember\Profiles
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function handle_profile_modifications ()
					{
						if (!empty ($_POST["ws_plugin__optimizemember_profile_save"])) /* Call inner routine? */
							{
								return c_ws_plugin__optimizemember_profile_mods_in::handle_profile_modifications ();
							}
					}
			}
	}
?>