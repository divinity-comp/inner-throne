<?php
/**
 * Shortcode `[optimizeMember-Pro-Stripe-Form /]`.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_form'))
{
	/**
	 * Shortcode `[optimizeMember-Pro-Stripe-Form /]`.
	 *
	 * @package optimizeMember\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_form
	{
		/**
		 * Shortcode `[optimizeMember-Pro-Stripe-Form /]`.
		 *
		 * @package optimizeMember\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_shortcode('optimizeMember-Pro-Stripe-Form');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Return-value of inner routine.
		 */
		public static function sc_stripe_form($attr, $content = '', $shortcode = '')
		{
			return c_ws_plugin__optimizemember_pro_stripe_form_in::sc_stripe_form($attr, $content, $shortcode);
		}

		/**
		 * Shortcode `[optimizeMember-Pro-Stripe-Form-Option /]`.
		 *
		 * @package optimizeMember\PayPal
		 * @since 140617
		 *
		 * @attaches-to ``add_shortcode('optimizeMember-Pro-Stripe-Form-Option');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Return-value of inner routine.
		 */
		public static function sc_stripe_form_option($attr, $content = '', $shortcode = '')
		{
			return c_ws_plugin__optimizemember_pro_stripe_form_in::sc_stripe_form_option($attr, $content, $shortcode);
		}
	}
}