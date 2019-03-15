<?php
/**
 * Stripe CSS/JS for theme integration.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_css_js'))
{
	/**
	 * Stripe CSS for theme integration.
	 *
	 * @package optimizeMember\CSS_JS
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_css_js
	{
		/**
		 * Adds the CSS for this Payment Gateway.
		 *
		 * @package optimizeMember\CSS_JS
		 * @since 140617
		 *
		 * @attaches-to ``add_action('ws_plugin__optimizemember_during_css');``
		 *
		 * @param array $vars Expects an array of defined vars to be passed in by the Action Hook.
		 */
		public static function stripe_css($vars)
		{
			$u = $GLOBALS['WS_PLUGIN__']['optimizemember_pro']['c']['dir_url'];
			$i = $GLOBALS['WS_PLUGIN__']['optimizemember_pro']['c']['dir_url'].'/images';

			if(!apply_filters('ws_plugin__optimizemember_pro_css_affects_gateways', TRUE)
			   || has_action('ws_plugin__optimizemember_during_css', 'c_ws_plugin__optimizemember_pro_css_js::css')
			) // This check allows a site owner to disable all CSS by removing the main CSS Hook in one shot.
			{
				echo "\n"; // Add a line break before inclusion.
				include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/separates/gateways/stripe/stripe.css';
			}
		}

		/**
		 * Adds the JavaScript for this Payment Gateway.
		 *
		 * @package optimizeMember\CSS_JS
		 * @since 140617
		 *
		 * @attaches-to ``add_action('ws_plugin__optimizemember_during_js_w_globals');``
		 *
		 * @param array $vars Expects an array of defined vars to be passed in by the Action Hook.
		 */
		public static function stripe_js_w_globals($vars)
		{
			$g = 'var optimizeMEMBER_PRO_STRIPE_GATEWAY = true,';

			$g = trim($g, ' ,').';'; // Trim & add semicolon.

			$u = $GLOBALS['WS_PLUGIN__']['optimizemember_pro']['c']['dir_url'];
			$i = $GLOBALS['WS_PLUGIN__']['optimizemember_pro']['c']['dir_url'].'/images';

			echo "\n".$g."\n"; // Add a line break before inclusion.

			include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/separates/gateways/stripe/stripe-min.js';
		}
	}
}