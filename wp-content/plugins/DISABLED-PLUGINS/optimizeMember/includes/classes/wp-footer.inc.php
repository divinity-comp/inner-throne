<?php
/**
* WordPress footer code.
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
* @package optimizeMember\WP_Footer
* @since 110524RC
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_wp_footer"))
	{
		/**
		* WordPress footer code.
		*
		* @package optimizeMember\WP_Footer
		* @since 110524RC
		*/
		class c_ws_plugin__optimizemember_wp_footer
			{
				/**
				* Generates footer code, when/if configured.
				*
				* @package optimizeMember\WP_Footer
				* @since 110524RC
				*
				* @return null
				*/
				public static function wp_footer_code ()
					{
						do_action ("ws_plugin__optimizemember_before_wp_footer_code", get_defined_vars ());
						/**/
						if (($code = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["wp_footer_code"]))
							{
								if (is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ())
									{
										echo do_shortcode ($code) . "\n"; /* No PHP here. */
									}
								else /* Otherwise, safe to allow PHP code. */
									{
										echo do_shortcode (c_ws_plugin__optimizemember_utilities::evl ($code));
									}
							}
						/**/
						do_action ("ws_plugin__optimizemember_after_wp_footer_code", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>