<?php
/**
 * Administrative CSS/JS.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_admin_css_js'))
{
	/**
	 * Administrative CSS/JS.
	 *
	 * @package s2Member\Admin_CSS_JS
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_admin_css_js
	{
		/**
		 * Adds JavaScript for this Payment Gateway.
		 *
		 * @package s2Member\Admin_CSS_JS
		 * @since 140617
		 *
		 * @attaches-to ``add_action('ws_plugin__s2member_during_menu_pages_js');``
		 *
		 * @param array $vars Expects an array of defined variables to be passed in by the Action Hook.
		 */
		public static function stripe_menu_pages_js($vars)
		{
			echo "\n"; // Add a line break before inclusion of this file.
			include_once dirname(dirname(dirname(dirname(__FILE__)))).'/menu-pages/stripe-menu-pages-s-min.js';
		}
	}
}