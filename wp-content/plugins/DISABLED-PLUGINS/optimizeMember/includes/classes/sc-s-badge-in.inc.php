<?php
/**
* Shortcode for `[optimizeMember-Security-Badge /]` ( inner processing routines ).
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
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_sc_s_badge_in"))
	{
		/**
		* Shortcode for `[optimizeMember-Security-Badge /]` ( inner processing routines ).
		*
		* @package optimizeMember\Security_Badges
		* @since 110524RC
		*/
		class c_ws_plugin__optimizemember_sc_s_badge_in
			{
				/**
				* Handles the Shortcode for: `[optimizeMember-Security-Badge /]`.
				*
				* @package optimizeMember\Security_Badges
				* @since 110524RC
				*
				* @attaches-to ``add_shortcode("optimizeMember-Security-Badge");``
				*
				* @param array $attr An array of Attributes.
				* @param str $content Content inside the Shortcode.
				* @param str $shortcode The actual Shortcode name itself.
				* @return str Resulting Security Badge code; HTML markup.
				*/
				public static function sc_s_badge ($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_sc_s_badge", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						$attr = c_ws_plugin__optimizemember_utils_strings::trim_qts_deep ((array)$attr);
						/**/
						$attr = shortcode_atts (array ("v" => "1"), $attr); /* One attribute. */
						/**/
						$code = c_ws_plugin__optimizemember_utilities::s_badge_gen ($attr["v"], false, false);
						/**/
						return apply_filters ("ws_plugin__optimizemember_sc_s_badge", $code, get_defined_vars ());
					}
			}
	}
?>