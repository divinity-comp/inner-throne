<?php
/**
 * ClickBank IPN Handler (inner processing routines).
 * VERSION 6 of API
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__optimizemember_pro_clickbank_notify_in'))
{
	/**
	 * ClickBank IPN Handler (inner processing routines).
	 *
	 * @package OptimizeMember\ClickBank
	 * @since 140806
	 */
	class c_ws_plugin__optimizemember_pro_clickbank_notify_in
	{
		/**
		 * Handles ClickBank IPN URL processing.
		 *
		 * @package OptimizeMember\ClickBank
		 * @since 140806
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function clickbank_notify()
		{
			global $current_site, $current_blog; // For Multisite support.

			if(!empty($_GET['optimizemember_pro_clickbank_notify']) && $GLOBALS['WS_PLUGIN__']['optimizemember']['o']['pro_clickbank_username'])
			{
				@ignore_user_abort(TRUE); // Continue processing even if/when connection is broken by the sender.

				if(is_array($clickbank = c_ws_plugin__optimizemember_pro_clickbank_utilities::clickbank_postvars()) && strcasecmp($clickbank['role'], 'VENDOR') === 0 && ($_clickbank = $clickbank))
				{
					$clickbank['optimizemember_log'][] = 'IPN received on: '.date('D M j, Y g:i:s a T');
					$clickbank['optimizemember_log'][] = 'OptimizeMember POST vars verified with ClickBank.';

					$s2vars = c_ws_plugin__optimizemember_pro_clickbank_utilities::clickbank_parse_s2vars($clickbank['lineItems'][0]->downloadUrl, $clickbank['transactionType']);

					if (is_multisite()){
						$linkArray = parse_url($clickbank['lineItems'][0]->downloadUrl);
						$linkTest = $linkArray['scheme'] . '://' . $linkArray['host'] . $linkArray['path'];
						$linkTest = substr($linkTest,0,-1);

						if (home_url() != $linkTest){
							$clickbank['optimizemember_log'][] = "Clickbank IPN doesn't match site URL";
							return;
						}
					}

					if(isset ($s2vars['s2_p1'], $s2vars['s2_p3']) && $s2vars['s2_p1'] === '0 D') // No Trial defaults to Regular Period.
						$s2vars['s2_p1'] = $s2vars['s2_p3']; // Initial Period. No Trial defaults to Regular Period.

					$clickbank['s2vars'] = $s2vars; // So they appear in the log entry for this Notification.

					if(strcasecmp($clickbank['customer']->billing->firstName.' '.$clickbank['customer']->billing->lastName, $clickbank['customer']->billing->fullName) !== 0 && preg_match('/(?:[^ ]+)(?: +)(?:[^ ]+)/', $clickbank['customer']->billing->fullName))
						list ($clickbank['customer']->billing->firstName, $clickbank['customer']->billing->lastName) = preg_split('/ +/', $clickbank['customer']->billing->fullName, 2);

					if(preg_match('/^(?:TEST_)?SALE$/i', $clickbank['transactionType']) && !$clickbank['lineItems'][0]->recurring)
					{
						$clickbank['optimizemember_log'][] = 'ClickBank transaction identified as ( `SALE/STANDARD` ).';
						$clickbank['optimizemember_log'][] = 'IPN reformulated. Piping through OptimizeMember\'s core/standard PayPal processor as `txn_type` ( `web_accept` ).';
						$clickbank['optimizemember_log'][] = 'Please check PayPal IPN logs for further processing details.';

						$processing = $processed = TRUE;
						$ipn        = array(); // Reset.

						$ipn['txn_type'] = 'web_accept';

						$ipn['txn_id'] = $clickbank['receipt'];

						$ipn['custom'] = $s2vars['s2_custom'];

						$ipn['mc_gross']    = number_format($clickbank['totalOrderAmount'], 2, '.', '');
						$ipn['mc_currency'] = strtoupper($clickbank['currency']);
						$ipn['tax']         = number_format('0.00', 2, '.', '');

						$ipn['payer_email'] = $clickbank['customer']->billing->email;
						$ipn['first_name']  = ucwords(strtolower($clickbank['customer']->billing->firstName));
						$ipn['last_name']   = ucwords(strtolower($clickbank['customer']->billing->lastName));

						$ipn['option_name1']      = ($s2vars['s2_referencing']) ? 'Referencing Customer ID' : 'Originating Domain';
						$ipn['option_selection1'] = ($s2vars['s2_referencing']) ? $s2vars['s2_referencing'] : $_SERVER['HTTP_HOST'];

						$ipn['option_name2']      = 'Customer IP Address';
						$ipn['option_selection2'] = $s2vars['s2_customer_ip'];

						$ipn['item_number'] = $s2vars['s2_invoice'];
						$ipn['item_name']   = $s2vars['s2_desc'];

						$ipn['optimizemember_paypal_proxy']              = 'clickbank';
						$ipn['optimizemember_paypal_proxy_use']          = 'standard-emails';
						$ipn['optimizemember_paypal_proxy_verification'] = c_ws_plugin__optimizemember_paypal_utilities::paypal_proxy_key_gen();

						c_ws_plugin__optimizemember_utils_urls::remote(home_url('/?optimizemember_paypal_notify=1'), $ipn, array('timeout' => 20));
					}
					else if(preg_match('/^(?:TEST_)?SALE$/i', $clickbank['transactionType']) && $clickbank['lineItems'][0]->recurring)
					{
						$clickbank['optimizemember_log'][] = 'ClickBank transaction identified as ( `SALE/RECURRING` ).';
						$clickbank['optimizemember_log'][] = 'IPN reformulated. Piping through OptimizeMember\'s core/standard PayPal processor as `txn_type` ( `subscr_signup` ).';
						$clickbank['optimizemember_log'][] = 'Please check PayPal IPN logs for further processing details.';

						$processing = $processed = TRUE;
						$ipn        = array(); // Reset.

						$ipn['txn_type']  = 'subscr_signup';
						$ipn['subscr_id'] = $s2vars['s2_subscr_id'];
						$ipn['recurring'] = $clickbank['lineItems'][0]->paymentPlan->paymentsRemaining > 0 ? '1' : '0';

						$ipn['txn_id'] = $clickbank['receipt'];

						$ipn['custom'] = $s2vars['s2_custom'];

						$ipn['period1'] = $s2vars['s2_p1'];
						$ipn['period3'] = $s2vars['s2_p3'];

						$ipn['mc_amount1'] = number_format($clickbank['totalOrderAmount'], 2, '.', '');
						$ipn['mc_amount3'] = number_format($clickbank['lineItems'][0]->paymentPlan->rebillAmount, 2, '.', '');

						$ipn['mc_gross'] = (preg_match('/^[1-9]/', $ipn['period1'])) ? $ipn['mc_amount1'] : $ipn['mc_amount3'];

						$ipn['mc_currency'] = strtoupper($clickbank['currency']);
						$ipn['tax']         = number_format('0.00', 2, '.', '');

						$ipn['payer_email'] = $clickbank['customer']->billing->email;
						$ipn['first_name']  = ucwords(strtolower($clickbank['customer']->billing->firstName));
						$ipn['last_name']   = ucwords(strtolower($clickbank['customer']->billing->lastName));

						$ipn['option_name1']      = ($s2vars['s2_referencing']) ? 'Referencing Customer ID' : 'Originating Domain';
						$ipn['option_selection1'] = ($s2vars['s2_referencing']) ? $s2vars['s2_referencing'] : $_SERVER['HTTP_HOST'];

						$ipn['option_name2']      = 'Customer IP Address';
						$ipn['option_selection2'] = $s2vars['s2_customer_ip'];

						$ipn['item_number'] = $s2vars['s2_invoice'];
						$ipn['item_name']   = $s2vars['s2_desc'];

						$ipn['optimizemember_paypal_proxy']     = 'clickbank';
						$ipn['optimizemember_paypal_proxy_use'] = 'standard-emails';
						$ipn['optimizemember_paypal_proxy_use'] .= ($ipn['mc_gross'] > 0) ? ',subscr-signup-as-subscr-payment' : '';
						$ipn['optimizemember_paypal_proxy_verification'] = c_ws_plugin__optimizemember_paypal_utilities::paypal_proxy_key_gen();

						c_ws_plugin__optimizemember_utils_urls::remote(home_url('/?optimizemember_paypal_notify=1'), $ipn, array('timeout' => 20));
					}
					else if(preg_match('/^(?:TEST_)?BILL$/i', $clickbank['transactionType']) && $clickbank['lineItems'][0]->recurring)
					{
						$clickbank['optimizemember_log'][] = 'ClickBank transaction identified as ( `BILL/RECURRING` ).';
						$clickbank['optimizemember_log'][] = 'IPN reformulated. Piping through OptimizeMember\'s core/standard PayPal processor as `txn_type` ( `subscr_payment` ).';
						$clickbank['optimizemember_log'][] = 'Please check PayPal IPN logs for further processing details.';

						$processing = $processed = TRUE;
						$ipn        = array(); // Reset.

						$ipn['txn_type']  = 'subscr_payment';
						$ipn['subscr_id'] = $s2vars['s2_subscr_id'];

						$ipn['txn_id'] = $clickbank['receipt'];

						$ipn['custom'] = $s2vars['s2_custom'];

						$ipn['mc_gross']    = number_format($clickbank['totalOrderAmount'], 2, '.', '');
						$ipn['mc_currency'] = strtoupper($clickbank['currency']);
						$ipn['tax']         = number_format('0.00', 2, '.', '');

						$ipn['payer_email'] = $clickbank['customer']->billing->email;
						$ipn['first_name']  = ucwords(strtolower($clickbank['customer']->billing->firstName));
						$ipn['last_name']   = ucwords(strtolower($clickbank['customer']->billing->lastName));

						$ipn['option_name1']      = ($s2vars['s2_referencing']) ? 'Referencing Customer ID' : 'Originating Domain';
						$ipn['option_selection1'] = ($s2vars['s2_referencing']) ? $s2vars['s2_referencing'] : $_SERVER['HTTP_HOST'];

						$ipn['option_name2']      = 'Customer IP Address';
						$ipn['option_selection2'] = $s2vars['s2_customer_ip'];

						$ipn['item_number'] = $s2vars['s2_invoice'];
						$ipn['item_name']   = $s2vars['s2_desc'];

						$ipn['optimizemember_paypal_proxy']              = 'clickbank';
						$ipn['optimizemember_paypal_proxy_use']          = 'standard-emails';
						$ipn['optimizemember_paypal_proxy_verification'] = c_ws_plugin__optimizemember_paypal_utilities::paypal_proxy_key_gen();

						c_ws_plugin__optimizemember_utils_urls::remote(home_url('/?optimizemember_paypal_notify=1'), $ipn, array('timeout' => 20));
					}
					else if(preg_match('/^(?:TEST_)?(?:RFND|CGBK|INSF)$/i', $clickbank['transactionType'])) // Product Type irrelevant here; checked below.
					{
						$clickbank['optimizemember_log'][] = 'ClickBank transaction identified as ( `RFND|CGBK|INSF` ).';
						$clickbank['optimizemember_log'][] = 'IPN reformulated. Piping through OptimizeMember\'s core/standard PayPal processor as `payment_status` ( `refunded|reversed` ).';
						$clickbank['optimizemember_log'][] = 'Please check PayPal IPN logs for further processing details.';

						$processing = $processed = TRUE;
						$ipn        = array(); // Reset.

						$ipn['payment_status'] = (preg_match('/^(?:TEST_)?RFND$/', $clickbank['transactionType'])) ? 'refunded' : 'reversed';

						$ipn['parent_txn_id'] = ($clickbank['lineItems'][0]->recurring && $s2vars['s2_subscr_id']) ? $s2vars['s2_subscr_id'] : $clickbank['receipt'];

						$ipn['custom'] = $s2vars['s2_custom'];

						$ipn['mc_fee']      = '-'.number_format('0.00', 2, '.', '');
						$ipn['mc_gross']    = '-'.number_format(abs($clickbank['totalOrderAmount']), 2, '.', '');
						$ipn['mc_currency'] = strtoupper($clickbank['currency']);
						$ipn['tax']         = '-'.number_format('0.00', 2, '.', '');

						$ipn['payer_email'] = $clickbank['customer']->billing->email;
						$ipn['first_name']  = ucwords(strtolower($clickbank['customer']->billing->firstName));
						$ipn['last_name']   = ucwords(strtolower($clickbank['customer']->billing->lastName));

						$ipn['option_name1']      = ($s2vars['s2_referencing']) ? 'Referencing Customer ID' : 'Originating Domain';
						$ipn['option_selection1'] = ($s2vars['s2_referencing']) ? $s2vars['s2_referencing'] : $_SERVER['HTTP_HOST'];

						$ipn['option_name2']      = 'Customer IP Address';
						$ipn['option_selection2'] = $s2vars['s2_customer_ip'];

						$ipn['item_number'] = $s2vars['s2_invoice'];
						$ipn['item_name']   = $s2vars['s2_desc'];

						$ipn['optimizemember_paypal_proxy']              = 'clickbank';
						$ipn['optimizemember_paypal_proxy_use']          = 'standard-emails';
						$ipn['optimizemember_paypal_proxy_verification'] = c_ws_plugin__optimizemember_paypal_utilities::paypal_proxy_key_gen();

						c_ws_plugin__optimizemember_utils_urls::remote(home_url('/?optimizemember_paypal_notify=1'), $ipn, array('timeout' => 20));
					}
					if( // Here we handle Recurring cancellations, and/or EOT (End Of Term) through $clickbank['lineItems'][0]->paymentPlan->rebillStatus.
						(preg_match('/^(?:TEST_)?(?:SALE|BILL)$/i', $clickbank['transactionType']) && $clickbank['lineItems'][0]->recurring && (preg_match('/^COMPLET(?:ED)?$/i', $clickbank['lineItems'][0]->paymentPlan->rebillStatus) || $clickbank['lineItems'][0]->paymentPlan->paymentsRemaining <= 0) && apply_filters('c_ws_plugin__optimizemember_pro_clickbank_notify_handles_completions', TRUE, get_defined_vars()))
						|| (preg_match('/^(?:TEST_)?CANCEL-(TEST\-)?REBILL$/i', $clickbank['transactionType']) && $clickbank['lineItems'][0]->recurring)
					)
					{
						$clickbank['optimizemember_log'][] = 'ClickBank transaction identified as ( `RECURRING/COMPLETED` or `CANCEL-REBILL` ).';
						$clickbank['optimizemember_log'][] = 'IPN reformulated. Piping through OptimizeMember\'s core/standard PayPal processor as `txn_type` ( `subscr_cancel` ).';
						$clickbank['optimizemember_log'][] = 'Please check PayPal IPN logs for further processing details.';

						$processing = $processed = TRUE;
						$ipn        = array(); // Reset.

						$ipn['txn_type']  = 'subscr_cancel';
						$ipn['subscr_id'] = $s2vars['s2_subscr_id'];

						$ipn['custom'] = $s2vars['s2_custom'];

						$ipn['period1'] = $s2vars['s2_p1'];
						$ipn['period3'] = $s2vars['s2_p3'];

						$ipn['payer_email'] = $clickbank['customer']->billing->email;
						$ipn['first_name']  = ucwords(strtolower($clickbank['customer']->billing->firstName));
						$ipn['last_name']   = ucwords(strtolower($clickbank['customer']->billing->lastName));

						$ipn['option_name1']      = ($s2vars['s2_referencing']) ? 'Referencing Customer ID' : 'Originating Domain';
						$ipn['option_selection1'] = ($s2vars['s2_referencing']) ? $s2vars['s2_referencing'] : $_SERVER['HTTP_HOST'];

						$ipn['option_name2']      = 'Customer IP Address';
						$ipn['option_selection2'] = $s2vars['s2_customer_ip'];

						$ipn['item_number'] = $s2vars['s2_invoice'];
						$ipn['item_name']   = $s2vars['s2_desc'];

						$ipn['optimizemember_paypal_proxy']              = 'clickbank';
						$ipn['optimizemember_paypal_proxy_use']          = 'standard-emails';
						$ipn['optimizemember_paypal_proxy_verification'] = c_ws_plugin__optimizemember_paypal_utilities::paypal_proxy_key_gen();

						c_ws_plugin__optimizemember_utils_urls::remote(home_url('/?optimizemember_paypal_notify=1'), $ipn, array('timeout' => 20));
					}
					if(empty($processed)) // If nothing was processed, here we add a message to the logs indicating the IPN was ignored.
						$clickbank['optimizemember_log'][] = 'Ignoring this IPN request. The transaction does NOT require any action on the part of OptimizeMember.';
				}
				else // Extensive log reporting here. This is an area where many site owners find trouble. Depending on server configuration; remote HTTPS connections may fail.
				{
					$clickbank['optimizemember_log'][] = 'Unable to verify POST vars. This is most likely related to an invalid ClickBank configuration. Please check: OptimizeMember ⥱ ClickBank Options.';
					$clickbank['optimizemember_log'][] = 'If you\'re absolutely SURE that your ClickBank configuration is valid, you may want to run some tests on your server, just to be sure $_POST variables are populated, and that your server is able to connect to ClickBank over an HTTPS connection.';
					$clickbank['optimizemember_log'][] = 'OptimizeMember uses the WP_Http class for remote connections; which will try to use cURL first, and then fall back on the FOPEN method when cURL is not available. On a Windows server, you may have to disable your cURL extension. Instead, set allow_url_fopen = yes in your php.ini file. The cURL extension (usually) does NOT support SSL connections on a Windows server.';
					$clickbank['optimizemember_log'][] = print_r($_REQUEST, TRUE)."\n\n".print_r(json_decode(file_get_contents('php://input')), TRUE); // Recording data for analysis and debugging.
				}
				/*
				If debugging/logging is enabled; we need to append $clickbank to the log file.
					Logging now supports Multisite Networking as well.
				*/
				$logt = c_ws_plugin__optimizemember_utilities::time_details();
				$logv = c_ws_plugin__optimizemember_utilities::ver_details();
				$logm = c_ws_plugin__optimizemember_utilities::mem_details();
				$log4 = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\n".'User-Agent: '.@$_SERVER['HTTP_USER_AGENT'];
				$log4 = (is_multisite() && !is_main_site()) ? ($_log4 = $current_blog->domain.$current_blog->path)."\n".$log4 : $log4;
				$log2 = (is_multisite() && !is_main_site()) ? 'clickbank-ipn-4-'.trim(preg_replace('/[^a-z0-9]/i', '-', !empty($_log4) ? $_log4 : ''), '-').'.log' : 'clickbank-ipn.log';

				if($GLOBALS['WS_PLUGIN__']['optimizemember']['o']['gateway_debug_logs'])
					if(is_dir($logs_dir = $GLOBALS['WS_PLUGIN__']['optimizemember']['c']['logs_dir']))
						if(is_writable($logs_dir) && c_ws_plugin__optimizemember_utils_logs::archive_oversize_log_files())
							file_put_contents($logs_dir.'/'.$log2,
							                  'LOG ENTRY: '.$logt."\n".$logv."\n".$logm."\n".$log4."\n".
							                  c_ws_plugin__optimizemember_utils_logs::conceal_private_info(var_export($clickbank, TRUE))."\n\n",
							                  FILE_APPEND);

				status_header(200); // Send a 200 OK status header.
				header('Content-Type: text/plain; charset=UTF-8'); // Content-Type text/plain with UTF-8.
				while(@ob_end_clean()) ; // Clean any existing output buffers.

				exit (); // Exit now.
			}
		}
	}
}