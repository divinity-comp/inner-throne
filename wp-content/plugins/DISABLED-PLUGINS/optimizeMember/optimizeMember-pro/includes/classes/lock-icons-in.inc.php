<?php
/**
* Pro Lock Icons ( inner processing routines ).
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
* @package optimizeMember\Lock_Icons
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_lock_icons_in"))
	{
		/**
		* Pro Lock Icons ( inner processing routines ).
		*
		* @package optimizeMember\Lock_Icons
		* @since 1.5
		*/
		class c_ws_plugin__optimizemember_pro_lock_icons_in
			{
				/**
				* The "(s2)" column header, attaching an ID to it as well.
				*
				* @package optimizeMember\Lock_Icons
				* @since 1.5
				*
				* @param array $cols Array of columns passed through by a Filter.
				* @return array Merged array of columns, including the one for `(s2)`.
				*/
				public static function _lock_icons_return_column ($cols = FALSE)
					{
						return array_merge ($cols, array ("ws_plugin__optimizemember_pro_lock_icons" => "(opm)"));
					}
				/**
				* Status for Tags.
				*
				* @package optimizeMember\Lock_Icons
				* @since 1.5
				*
				* @param str $value Existing value for this data column.
				* @param str $column_name Column ID/Name. We need to look at this to fill the `(s2)` column.
				* @param int|str $id Expecting a numeric Tag ID to be passed through by the Filter.
				* @return str If `(s2)` column, return status. Else, existing ``$value``.
				*/
				public static function _lock_icons_return_value_tags ($value = FALSE, $column_name = FALSE, $id = FALSE)
					{
						return ($column_name === "ws_plugin__optimizemember_pro_lock_icons") ? c_ws_plugin__optimizemember_pro_lock_icons_in::_return_lock_icons_description (c_ws_plugin__optimizemember_ptags_sp::check_specific_ptag_level_access ($id, false)) : $value;
					}
				/**
				* Status for Categories.
				*
				* @package optimizeMember\Lock_Icons
				* @since 1.5
				*
				* @param str $value Existing value for this data column.
				* @param str $column_name Column ID/Name. We need to look at this to fill the `(s2)` column.
				* @param int|str $id Expecting a numeric Category ID to be passed through by the Filter.
				* @return str If `(s2)` column, return status. Else, existing ``$value``.
				*/
				public static function _lock_icons_return_value_categories ($value = FALSE, $column_name = FALSE, $id = FALSE)
					{
						return ($column_name === "ws_plugin__optimizemember_pro_lock_icons") ? c_ws_plugin__optimizemember_pro_lock_icons_in::_return_lock_icons_description (c_ws_plugin__optimizemember_catgs_sp::check_specific_catg_level_access ($id, false)) : $value;
					}
				/**
				* Status for Pages, these are handled separately.
				*
				* @package optimizeMember\Lock_Icons
				* @since 1.5
				*
				* @param str $column_name Column ID/Name. We need to look at this to fill the `(s2)` column.
				* @param int|str $id Expecting a numeric Page ID to be passed by the Action Hook.
				* @return null
				*/
				public static function _lock_icons_echo_value_pages ($column_name = FALSE, $id = FALSE)
					{
						echo ($column_name === "ws_plugin__optimizemember_pro_lock_icons") ? c_ws_plugin__optimizemember_pro_lock_icons_in::_return_lock_icons_description (c_ws_plugin__optimizemember_pages_sp::check_specific_page_level_access ($id, false)) : "";
					}
				/**
				* Status for all other Post Types, including Custom Post Types; but excluding Pages.
				*
				* @package optimizeMember\Lock_Icons
				* @since 1.5
				*
				* @param str $column_name Column ID/Name. We need to look at this to fill the `(s2)` column.
				* @param int|str $id Expecting a numeric Post ID to be passed by the Action Hook.
				* @return null
				*/
				public static function _lock_icons_echo_value_post_types ($column_name = FALSE, $id = FALSE)
					{
						echo ($column_name === "ws_plugin__optimizemember_pro_lock_icons") ? c_ws_plugin__optimizemember_pro_lock_icons_in::_return_lock_icons_description (c_ws_plugin__optimizemember_posts_sp::check_specific_post_level_access ($id, false)) : "";
					}
				/**
				* CSS styles for Lock Icon columns.
				*
				* @package optimizeMember\Lock_Icons
				* @since 1.5
				*
				* @return null
				*/
				public static function _lock_icons_echo_css ()
					{
						$css = '<style type="text/css">';
						$css .= 'th.column-ws_plugin__optimizemember_pro_lock_icons, td.column-ws_plugin__optimizemember_pro_lock_icons { width: 45px; text-align:center; }';
						$css .= '</style>';
						/**/
						echo apply_filters ("_ws_plugin__optimizemember_pro_lock_icons_echo_css", $css, get_defined_vars ());
					}
				/**
				* Translates a return array into a string description.
				*
				* @package optimizeMember\Lock_Icons
				* @since 1.5
				*
				* @param array $array Expects an array returned by one of optimizeMember's security routines.
				* @return str A verbose string representation of the return array details.
				*/
				public static function _return_lock_icons_description ($array = FALSE)
					{
						$dir_url = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"];
						/**/
						if (isset ($array["optimizemember_level_req"]))
							$req = 'Requires Membership Level #' . $array["optimizemember_level_req"];
						/**/
						else if (isset ($array["optimizemember_ccap_req"]))
							$req = 'Requires Custom Capabilities';
						/**/
						else if (isset ($array["optimizemember_sp_req"]))
							$req = 'Requires Specific Post/Page Access';
						/**/
						$desc = isset($req) ? '<img src="' . esc_attr ($dir_url) . '/images/lock-icon.png" style="cursor:help; width:16px; border:0;" title="' . esc_attr ($req) . '" />' : '<span style="cursor:help;" title="Publicly Available">&mdash;</span>';
						/**/
						return apply_filters ("_ws_plugin__optimizemember_pro_return_lock_icons_description", $desc, get_defined_vars ());
					}
			}
	}
?>