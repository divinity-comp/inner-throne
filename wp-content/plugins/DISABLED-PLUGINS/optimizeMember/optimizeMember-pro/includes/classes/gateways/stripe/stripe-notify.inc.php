<?php
/**
 * Stripe Silent Post *(aka: IPN)* handler.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_notify'))
{
	/**
	 * Stripe Silent Post *(aka: IPN)* handler.
	 *
	 * @package optimizeMember\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_notify
	{
		/**
		 * Handles Stripe IPN URL processing.
		 *
		 * @package optimizeMember\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function stripe_notify()
		{
			if(!empty($_GET['optimizemember_pro_stripe_notify']))
				c_ws_plugin__optimizemember_pro_stripe_notify_in::stripe_notify();
		}
	}
}