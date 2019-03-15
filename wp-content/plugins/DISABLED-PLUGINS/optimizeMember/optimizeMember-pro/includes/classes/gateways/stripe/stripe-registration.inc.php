<?php
/**
 * Stripe Registration Form processing.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_registration'))
{
	/**
	 * Stripe Registration Form processing.
	 *
	 * @package optimizeMember\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_registration
	{
		/**
		 * Handles processing of Pro Form registrations.
		 *
		 * @package optimizeMember\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function stripe_registration()
		{
			if(!empty($_POST['optimizemember_pro_stripe_registration']))
				c_ws_plugin__optimizemember_pro_stripe_registration_in::stripe_registration();
		}
	}
}