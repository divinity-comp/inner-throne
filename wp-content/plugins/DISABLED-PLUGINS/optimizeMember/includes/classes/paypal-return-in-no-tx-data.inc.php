<?php
/**
* optimizeMember's PayPal Auto-Return/PDT handler (inner processing routine).
*
* Copyright: © 2009-2011
* {@link http://www.websharks-inc.com/ WebSharks, Inc.}
* (coded in the USA)
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember\PayPal
* @since 110720
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__optimizemember_paypal_return_in_no_tx_data"))
	{
		/**
		* optimizeMember's PayPal Auto-Return/PDT handler (inner processing routine).
		*
		* @package optimizeMember\PayPal
		* @since 110720
		*/
		class c_ws_plugin__optimizemember_paypal_return_in_no_tx_data
			{
				/**
				* optimizeMember's PayPal Auto-Return/PDT handler (inner processing routine).
				*
				* @package optimizeMember\PayPal
				* @since 110720
				*
				* @param array $vars Required. An array of defined variables passed by {@link optimizeMember\PayPal\c_ws_plugin__optimizemember_paypal_return_in::paypal_return()}.
				* @return array|bool The original ``$paypal`` array passed in (extracted) from ``$vars``, or false when conditions do NOT apply.
				*/
				public static function /* Conditional phase for ``c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()``. */ cp ($vars = array())
					{
						extract  /* Extract all vars passed in from: ``c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()``. */($vars, EXTR_OVERWRITE | EXTR_REFS);

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__optimizemember_during_paypal_return_before_no_return_data", get_defined_vars ());
						unset($__refs, $__v);

						$paypal["optimizemember_log"][] = "No Return-Data. Customer MUST wait for Email Confirmation.";
						$paypal["optimizemember_log"][] = "Note. This can sometimes happen when/if you are offering an Initial/Trial Period. There are times when a Payment Gateway will NOT supply optimizeMember with any data immediately after checkout. When/if this happens, optimizeMember must process the transaction via IPN only (i.e., behind-the-scene), and the Customer must wait for Email Confirmation in these cases.";
						$paypal["optimizemember_log"][] = /* Recording _POST + _GET vars for analysis and debugging. */ var_export ($_REQUEST, true);

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__optimizemember_during_paypal_return_during_no_return_data", get_defined_vars ());
						unset($__refs, $__v);

						if /* Using a custom success redirection URL? */ ($custom_success_redirection)
						{
							$paypal["optimizemember_log"][] = "Redirecting Customer to a custom URL: " . $custom_success_redirection . ".";

							wp_redirect($custom_success_redirection);
						}
						else // Else we use the default redirection URL for this scenario, which is the Home Page.
						{
							$paypal["optimizemember_log"][] = "Redirecting Customer to the Home Page (after asking Customer to check their email).";

							echo c_ws_plugin__optimizemember_return_templates::return_template ($paypal["subscr_gateway"],
								_x ('<strong>Thank you! (you MUST check your email before proceeding).</strong><br /><br />* Note: It can take <em>(up to 15 minutes)</em> for Email Confirmation with important details. If you don\'t receive email confirmation in the next 15 minutes, please contact Support.', "s2member-front", "s2member") . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["paypal_sandbox"] || (c_ws_plugin__optimizemember_utils_conds::pro_is_installed () && !empty($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_" . $paypal["subscr_gateway"] . "_sandbox"]))) ? '<br /><br />' . _x ('<strong>* Sandbox Mode *:</strong> You may NOT receive this Email in Sandbox Mode. Sandbox addresses are usually bogus (for testing).', "s2member-front", "s2member") : ''),
							   _x ("Back To Home Page", "s2member-front", "s2member"), home_url ("/"));
						}
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__optimizemember_during_paypal_return_after_no_return_data", get_defined_vars ());
						unset($__refs, $__v);

						return apply_filters("c_ws_plugin__optimizemember_paypal_return_in_no_tx_data", $paypal, get_defined_vars ());
					}
			}
	}