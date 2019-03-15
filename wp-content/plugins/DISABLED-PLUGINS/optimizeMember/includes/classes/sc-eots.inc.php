<?php
/**
 * Shortcode `[opmEot /]`.
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_sc_eots'))
{
	/**
	 * Shortcode `[s2Eot /]`.
	 */
	class c_ws_plugin__optimizemember_sc_eots
	{
		/**
		 * Handles the Shortcode for: `[opmEot /]`.
		 *
		 * @attaches-to ``add_shortcode('s2Eot');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Return-value of inner routine.
		 */
		public static function sc_eot_details($attr = array(), $content = '', $shortcode = '')
		{
			return c_ws_plugin__optimizemember_sc_eots_in::sc_eot_details($attr, $content, $shortcode);
		}
	}
}
