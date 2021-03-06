<?php
/**
* OptimizeMember's PayPal IPN handler (inner processing routine).
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__optimizemember_paypal_notify_in_billing_agreement_signup"))
	{
		/**
		* optimizeMember's PayPal IPN handler (inner processing routine).
		*
		* @package optimizeMember\PayPal
		* @since 140326
		*/
		class c_ws_plugin__optimizemember_paypal_notify_in_billing_agreement_signup
			{
				/**
				* optimizeMember's PayPal IPN handler (inner processing routine).
				*
				* @package optimizeMember\PayPal
				* @since 140326
				*
				* @param array $vars Required. An array of defined variables passed by {@link optimizeMember\PayPal\c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()}.
				* @return array|bool The original ``$paypal`` array passed in (extracted) from ``$vars``, or false when conditions do NOT apply.
				*/
				public static function cp ($vars = array()) // Conditional phase for ``c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()``.
					{
						extract($vars, EXTR_OVERWRITE | EXTR_REFS); // Extract all vars passed in from: ``c_ws_plugin__optimizemember_paypal_notify_in::paypal_notify()``.

						if (!empty($paypal["txn_type"]) && preg_match ("/^mp_signup$/i", $paypal["txn_type"]))
							{
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__optimizemember_during_paypal_notify_before_billing_agreement_signup", get_defined_vars ());
								unset($__refs, $__v);

								if (!get_transient ($transient_ipn = "s2m_ipn_" . md5 ("optimizemember_transient_" . $_paypal_s)) && set_transient ($transient_ipn, time (), 31556926 * 10))
									{
										$paypal["optimizemember_log"][] = "optimizeMember `txn_type` identified as ( `mp_signup` ).";

										$processing = $during = true; // Yes, we ARE processing this.

										$paypal["optimizemember_log"][] = "The `txn_type` does not require any action on the part of optimizeMember.";
										$paypal["optimizemember_log"][] = "optimizeMember Pro handles Billing Agreement signups on-site, with an IPN proxy.";

										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("ws_plugin__optimizemember_during_paypal_notify_during_billing_agreement_signup", get_defined_vars ());
										unset($__refs, $__v);
									}
								else // Else, this is a duplicate IPN. Must stop here.
									{
										$paypal["optimizemember_log"][] = "Not processing. Duplicate IPN.";
										$paypal["optimizemember_log"][] = "optimizeMember `txn_type` identified as ( `mp_signup` ).";
										$paypal["optimizemember_log"][] = "Duplicate IPN. Already processed. This IPN will be ignored.";
									}
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__optimizemember_during_paypal_notify_after_billing_agreement_signup", get_defined_vars ());
								unset($__refs, $__v);

								return apply_filters("c_ws_plugin__optimizemember_paypal_notify_in_billing_agreement_signup", $paypal, get_defined_vars ());
							}
						else return apply_filters("c_ws_plugin__optimizemember_paypal_notify_in_billing_agreement_signup", false, get_defined_vars ());
					}
			}
	}