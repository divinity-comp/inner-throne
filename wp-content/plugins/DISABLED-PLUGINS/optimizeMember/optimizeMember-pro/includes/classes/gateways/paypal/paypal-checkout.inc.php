<?php
/**
* PayPal Checkout Form processing.
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* This WordPress plugin ( optimizeMember Pro ) is comprised of two parts:
*
* o (1) Its PHP code is licensed under the GPL license, as is WordPress.
* 	You should have received a copy of the GNU General Public License,
* 	along with this software. In the main directory, see: /licensing/
* 	If not, see: {@link http://www.gnu.org/licenses/}.
*
* o (2) All other parts of ( optimizeMember Pro ); including, but not limited to:
* 	the CSS code, some JavaScript code, images, and design;
* 	are licensed according to the license purchased.
* 	See: {@link http://www.optimizepress.com/prices/}
*
* Unless you have our prior written consent, you must NOT directly or indirectly license,
* sub-license, sell, resell, or provide for free; part (2) of the optimizeMember Pro Module;
* or make an offer to do any of these things. All of these things are strictly
* prohibited with part (2) of the optimizeMember Pro Module.
*
* Your purchase of optimizeMember Pro includes free lifetime upgrades via optimizeMember.com
* ( i.e. new features, bug fixes, updates, improvements ); along with full access
* to our video tutorial library: {@link http://www.optimizepress.com/videos/}
*
* @package optimizeMember\PayPal
* @since 1.5
*/
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**/
if(!class_exists("c_ws_plugin__optimizemember_pro_paypal_checkout"))
	{
		/**
		* PayPal Checkout Form processing.
		*
		* @package optimizeMember\PayPal
		* @since 1.5
		*/
		class c_ws_plugin__optimizemember_pro_paypal_checkout
			{
				/**
				* Handles processing of Pro Form checkouts.
				*
				* @package optimizeMember\PayPal
				* @since 1.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function paypal_checkout()
					{
						if(!empty($_POST["optimizemember_pro_paypal_checkout"]) || (!empty($_GET["optimizemember_paypal_xco"]) && $_GET["optimizemember_paypal_xco"] === "optimizemember_pro_paypal_checkout_return"))
							{
								if($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["paypal_payflow_api_username"])
									return c_ws_plugin__optimizemember_pro_paypal_checkout_pf_in::paypal_checkout();
								/**/
								if($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_paypal_checkout_rdp"])
									return c_ws_plugin__optimizemember_pro_paypal_checkout_rdp_in::paypal_checkout();
								/**/
								return c_ws_plugin__optimizemember_pro_paypal_checkout_in::paypal_checkout();
							}
					}
			}
	}
?>