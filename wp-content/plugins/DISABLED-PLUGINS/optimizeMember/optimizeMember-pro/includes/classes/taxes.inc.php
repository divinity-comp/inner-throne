<?php
/**
 * Taxes.
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_taxes'))
{
	/**
	 * Taxes.
	 *
	 * @package optimizeMember\Taxes
	 * @since 150122
	 */
	class c_ws_plugin__optimizemember_pro_taxes
	{
		/**
		 * Determines whether or not tax may apply.
		 *
		 * @since 150122 Enhancing coupon codes and gift codes.
		 *
		 * @return boolean `TRUE` if tax may apply.
		 */
		public static function may_apply()
		{
			if((float)$GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_default_tax'] > 0)
				return TRUE;

			if($GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_tax_rates'])
				return TRUE;

			return FALSE;
		}
	}
}