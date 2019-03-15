<?php
/**
 * OptimizeMember's PayPal IPN handler.
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_paypal_notify'))
{
	/**
	 * optimizeMember's PayPal IPN handler.
	 *
	 * @package optimizeMember\PayPal
	 * @since 3.5
	 */
	class c_ws_plugin__optimizemember_paypal_notify
	{
		/**
		 * Handles PayPal IPN processing.
		 *
		 * @package optimizeMember\PayPal
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function paypal_notify()
		{
			if(!empty($_GET['optimizemember_paypal_notify']))
				c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify();
		}
	}
}