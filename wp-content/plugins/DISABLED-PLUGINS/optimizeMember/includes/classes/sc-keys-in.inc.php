<?php
/**
* Shortcode `[opmKey /]` ( inner processing routines ).
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
* @package optimizeMember\opmKey
* @since 110912
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_sc_keys_in"))
	{
		/**
		* Shortcode `[opmKey /]` ( inner processing routines ).
		*
		* @package optimizeMember\opmKey
		* @since 110912
		*/
		class c_ws_plugin__optimizemember_sc_keys_in
			{
				/**
				* Handles the Shortcode for: `[opmKey /]`.
				*
				* @package optimizeMember\opmKey
				* @since 110912
				*
				* @attaches-to ``add_shortcode("opmKey");``
				*
				* @param array $attr An array of Attributes.
				* @param str $content Content inside the Shortcode.
				* @param str $shortcode The actual Shortcode name itself.
				* @return str Value of the requested key, or null on failure.
				*/
				public static function sc_get_key ($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_sc_get_key", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						$attr = c_ws_plugin__optimizemember_utils_strings::trim_qts_deep ((array)$attr);
						/**/
						$attr = shortcode_atts (array ("file_download" => "", "directive" => ""), $attr);
						/**/
						eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_sc_get_key_after_shortcode_atts", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if ($attr["file_download"]) /* Requesting a File Download Key? */
							$get = c_ws_plugin__optimizemember_files::file_download_key ($attr["file_download"], $attr["directive"]);
						/**/
						return apply_filters ("ws_plugin__optimizemember_sc_get_key", ((isset ($get)) ? $get : null), get_defined_vars ());
					}
			}
	}
?>