<?php
/**
 * Stripe SSL.
 */
if(realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME']))
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_stripe_ssl'))
{
	/**
	 * Stripe SSL.
	 *
	 * @package optimizeMember\Stripe
	 * @since 140617
	 */
	class c_ws_plugin__optimizemember_pro_stripe_ssl
	{
		/**
		 * Auto-detects Pro Forms when Auto SSL is enabled.
		 *
		 * @package optimizeMember\Stripe
		 * @since 140617
		 *
		 * @attaches-to ``add_filter('ws_plugin__optimizemember_check_force_ssl');``
		 *
		 * @param bool  $force Expects a boolean value passed through by the Filter.
		 * @param array $vars Expects an array of defined variables, passed through by the Filter.
		 *
		 * @return bool True if SSL is not currently being forced, but this routine detected that it should be; else the existing value of ``$force``.
		 */
		public static function sc_stripe_form_auto_force_ssl($force, $vars)
		{
			global $post; // Need this global object reference.

			if(!$force && defined('optimizeMEMBER_PRO_AUTO_FORCE_SSL') && optimizeMEMBER_PRO_AUTO_FORCE_SSL)
				if(is_object($post) && strpos($post->post_content, '[optimizeMember-Pro-Stripe-Form') !== FALSE)
					return ($force = TRUE);

			return $force; // Keep current value.
		}
	}
}