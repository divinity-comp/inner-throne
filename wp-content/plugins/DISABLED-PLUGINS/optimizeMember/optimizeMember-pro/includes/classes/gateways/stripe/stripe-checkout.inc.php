<?php
/**
 * Stripe Checkout Form handler.
*/
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_checkout'))
{
	/**
	 * Stripe Checkout Form handler.
	 *
	 * @package s2Member\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_checkout
	{
		/**
		 * Handles processing of Pro Form checkouts.
		 *
		 * @package s2Member\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function stripe_checkout()
		{
			if(!empty($_POST['optimizemember_pro_stripe_checkout']))
				c_ws_plugin__optimizemember_pro_stripe_checkout_in::stripe_checkout();
		}
	}
}