<?php
/**
 * Stripe Specific Post/Page Check form processing.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_sp_checkout'))
{
	/**
	 * Stripe Specific Post/Page Check form processing.
	 *
	 * @package optimizeMember\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_sp_checkout
	{
		/**
		 * Handles processing of Pro Forms for Specific Post/Page checkout.
		 *
		 * @package optimizeMember\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function stripe_sp_checkout()
		{
			if(!empty($_POST['optimizemember_pro_stripe_sp_checkout']))
				c_ws_plugin__optimizemember_pro_stripe_sp_checkout_in::stripe_sp_checkout();
		}
	}
}