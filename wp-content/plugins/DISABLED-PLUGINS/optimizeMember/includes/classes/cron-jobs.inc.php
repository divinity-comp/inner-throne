<?php
/**
* Cron routines handled by optimizeMember.
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
* @package optimizeMember\Cron_Jobs
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_cron_jobs"))
	{
		/**
		* Cron routines handled by optimizeMember.
		*
		* @package optimizeMember\Cron_Jobs
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_cron_jobs
			{
				/**
				* Extends WP-Cron schedules to support 10 minute intervals.
				*
				* @package optimizeMember\Cron_Jobs
				* @since 3.5
				*
				* @attaches-to ``add_filter("cron_schedules");``
				*
				* @param array $schedules Expects an array of WP_Cron schedules passed in by the Filter.
				* @return inner Return-value of inner routine.
				*/
				public static function extend_cron_schedules ($schedules = array ()) /* Call inner function? */
					{
						return c_ws_plugin__optimizemember_cron_jobs_in::extend_cron_schedules ($schedules);
					}
				/**
				* Allows the Auto-EOT Sytem to be processed through a server-side Cron Job.
				*
				* @package optimizeMember\Cron_Jobs
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function auto_eot_system_via_cron ()
					{
						if (!empty ($_GET["optimizemember_auto_eot_system_via_cron"])) /* Call inner routine? */
							{
								return c_ws_plugin__optimizemember_cron_jobs_in::auto_eot_system_via_cron ();
							}
					}
			}
	}
?>