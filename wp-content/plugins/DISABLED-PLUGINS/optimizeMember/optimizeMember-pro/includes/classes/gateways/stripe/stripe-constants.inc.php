<?php
/**
 * Stripe API Constants *(for site owners)*.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_constants'))
{
	/**
	 * Stripe API Constants *(for site owners)*.
	 */
	class c_ws_plugin__optimizemember_pro_stripe_constants
	{
		/**
		 * Stripe API Constants *(for site owners)*.
		 *
		 *
		 * @attaches-to ``add_filter('ws_plugin__optimizemember_during_constants_c');``
		 *
		 * @param array $c Checksum array should be passed through by the Filter.
		 * @param array $vars Array of defined variables, passed through by the Filter.
		 *
		 * @return array Checksum array with new indexes for Constant values.
		 */
		public static function stripe_constants($c, $vars)
		{
			/**
			 * Flag indicating the Stripe Gateway is active.
			 *
			 * @var bool
			 */
			if(!defined('OPTIMIZEMEMBER_PRO_STRIPE_GATEWAY'))
				define('OPTIMIZEMEMBER_PRO_STRIPE_GATEWAY', ($c[] = TRUE));

			return $c; // Return $c calculation values.
		}
	}
}