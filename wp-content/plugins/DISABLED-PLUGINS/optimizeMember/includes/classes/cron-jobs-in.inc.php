<?php
/**
* Cron routines handled by optimizeMember ( inner processing routines ).
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
if (!class_exists ("c_ws_plugin__optimizemember_cron_jobs_in"))
	{
		/**
		* Cron routines handled by optimizeMember ( inner processing routines ).
		*
		* @package optimizeMember\Cron_Jobs
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_cron_jobs_in
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
				* @return array Array of WP_Cron schedules after having added a 10 minute cycle.
				*/
				public static function extend_cron_schedules ($schedules = array ())
					{
						eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_extend_cron_schedules", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						$array = array ("every10m" => array ("interval" => 600, "display" => "Every 10 Minutes"));
						/**/
						return apply_filters ("ws_plugin__optimizemember_extend_cron_schedules", array_merge ($array, $schedules), get_defined_vars ());
					}
				/**
				* Allows the Auto-EOT Sytem to be processed through a server-side Cron Job.
				*
				* @package optimizeMember\Cron_Jobs
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after task completed.
				*/
				public static function auto_eot_system_via_cron ()
					{
						do_action ("ws_plugin__optimizemember_before_auto_eot_system_via_cron", get_defined_vars ());
						/**/
						if (!empty ($_GET["optimizemember_auto_eot_system_via_cron"])) /* Called through HTTP? */
							{
								if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["auto_eot_system_enabled"])
									{
										c_ws_plugin__optimizemember_auto_eots::auto_eot_system (); /* Process. */
										/**/
										do_action ("ws_plugin__optimizemember_during_auto_eot_system_via_cron", get_defined_vars ());
									}
								exit (); /* Clean exit. */
							}
						/**/
						do_action ("ws_plugin__optimizemember_after_auto_eot_system_via_cron", get_defined_vars ());
					}
			}
	}
?>