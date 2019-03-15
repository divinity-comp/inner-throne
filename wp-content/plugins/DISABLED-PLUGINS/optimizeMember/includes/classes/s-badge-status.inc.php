<?php
/**
* Security Badge Status API.
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
* @package optimizeMember\Security_Badges
* @since 110524RC
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_s_badge_status"))
	{
		/**
		* Security Badge Status API.
		*
		* @package optimizeMember\Security_Badges
		* @since 110524RC
		*/
		class c_ws_plugin__optimizemember_s_badge_status
			{
				/**
				* Handles Security Badge Status API.
				*
				* @package optimizeMember\Security_Badges
				* @since 110524RC
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function s_badge_status ()
					{
						if (!empty ($_GET["optimizemember_s_badge_status"])) /* Call inner routine? */
							{
								c_ws_plugin__optimizemember_s_badge_status_in::s_badge_status ();
							}
					}
			}
	}
?>