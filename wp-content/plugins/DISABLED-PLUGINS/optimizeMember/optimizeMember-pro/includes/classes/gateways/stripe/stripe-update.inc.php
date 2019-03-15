<?php
/**
 * Stripe Update Form processing.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_update'))
{
	/**
	 * Stripe Update Form processing.
	 *
	 * @package optimizeMember\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_update
	{
		/**
		 * Handles processing of Pro Form billing updates.
		 *
		 * @package optimizeMember\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function stripe_update()
		{
			if(!empty($_POST['optimizemember_pro_stripe_update']))
				c_ws_plugin__optimizemember_pro_stripe_update_in::stripe_update();
		}
	}
}