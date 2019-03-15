<?php
/**
 * Menu page for (Stripe API Constants).
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_menu_page_stripe_s_api_c'))
{
	/**
	 * Menu page for s2Member Pro (Stripe API Constants).
	 *
	 * @package s2Member\Menu_Pages
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_menu_page_stripe_s_api_c
	{
		public function __construct()
		{
			if(is_multisite() && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() && !is_main_site())
			{
			}
			else // Otherwise, we can display the standardized version of this information.
			{
			}
		}
	}
}

new c_ws_plugin__optimizemember_pro_menu_page_stripe_s_api_c ();