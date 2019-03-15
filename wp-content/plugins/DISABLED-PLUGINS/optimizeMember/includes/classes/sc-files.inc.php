<?php
/**
* Shortcode `[opmFile /]`.
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
* @package optimizeMember\opmFile
* @since 110926
*/
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**/
if(!class_exists("c_ws_plugin__optimizemember_sc_files"))
	{
		/**
		* Shortcode `[opmFile /]`.
		*
		* @package optimizeMember\opmFile
		* @since 110926
		*/
		class c_ws_plugin__optimizemember_sc_files
			{
				/**
				* Handles the Shortcode for: `[opmFile /]`.
				*
				* @package optimizeMember\opmFile
				* @since 110926
				*
				* @attaches-to ``add_shortcode("opmFile");``
				*
				* @param array $attr An array of Attributes.
				* @param str $content Content inside the Shortcode.
				* @param str $shortcode The actual Shortcode name itself.
				* @return str Value of the requested File Download URL, or null on failure.
				*/
				public static function sc_get_file($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						return c_ws_plugin__optimizemember_sc_files_in::sc_get_file($attr, $content, $shortcode);
					}
				/**
				* Handles the Shortcode for: `[opmStream /]`.
				*
				* @package optimizeMember\opmFile
				* @since 130119
				*
				* @attaches-to ``add_shortcode("opmStream");``
				*
				* @param array $attr An array of Attributes.
				* @param str $content Content inside the Shortcode.
				* @param str $shortcode The actual Shortcode name itself.
				* @return str HTML markup that produces an audio/video stream for a specific player.
				*/
				public static function sc_get_stream($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						return c_ws_plugin__optimizemember_sc_files_in::sc_get_stream($attr, $content, $shortcode);
					}
			}
	}
?>