<?php
/**
 * Stripe Cancellation Form handler.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_cancellation'))
{
	/**
	 * Stripe Cancellation Form handler.
	 *
	 * @package s2Member\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_cancellation
	{
		/**
		 * Handles processing of Pro Form cancellations.
		 *
		 * @package optimizeMember\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function stripe_cancellation()
		{
			if(!empty($_POST['optimizemember_pro_stripe_cancellation']))
				c_ws_plugin__optimizemember_pro_stripe_cancellation_in::stripe_cancellation();
		}
	}
}