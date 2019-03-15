<?php
/**
* Authorize.Net Silent Post *( aka: IPN )* ( inner processing routines ).
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
* @package optimizeMember\AuthNet
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_authnet_notify_in"))
	{
		/**
		* Authorize.Net Silent Post *( aka: IPN )* ( inner processing routines ).
		*
		* @package optimizeMember\AuthNet
		* @since 1.5
		*/
		class c_ws_plugin__optimizemember_pro_authnet_notify_in
			{
				/**
				* Handles Authorize.Net IPN URL processing.
				*
				* @package optimizeMember\AuthNet
				* @since 1.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after handling IPN processing.
				*/
				public static function authnet_notify ()
					{
						global $current_site, $current_blog; /* For Multisite support. */
						/**/
						if (!empty ($_GET["optimizemember_pro_authnet_notify"]) && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_authnet_api_login_id"])
							{
								@ignore_user_abort (true); /* Continue processing even if/when connection is broken by the sender. */
								/**/
								if (is_array ($authnet = c_ws_plugin__optimizemember_pro_authnet_utilities::authnet_postvars ()) && ($_authnet = $authnet))
									{
										$authnet["optimizemember_log"][] = "IPN received on: " . date ("D M j, Y g:i:s a T");
										$authnet["optimizemember_log"][] = "optimizeMember POST vars verified with Authorize.Net.";
										/**/
										if ($authnet["x_subscription_id"] && $authnet["x_subscription_paynum"] && $authnet["x_response_code"] === "1")
											{
												if (($_authnet = c_ws_plugin__optimizemember_pro_authnet_utilities::authnet_parse_arb_desc ($authnet)) && ($authnet = $_authnet))
													{
														$authnet["optimizemember_log"][] = "Authorize.Net transaction identified as ( `ARB / PAYMENT #" . $authnet["x_subscription_paynum"] . "` ).";
														$authnet["optimizemember_log"][] = "IPN reformulated. Piping through optimizeMember's core/standard PayPal processor as `txn_type` ( `subscr_payment` ).";
														$authnet["optimizemember_log"][] = "Please check PayPal IPN logs for further processing details.";
														/**/
														$processing = $processed = true;
														$ipn = array (); /* Reset. */
														/**/
														$ipn["txn_type"] = "subscr_payment";
														$ipn["subscr_id"] = $authnet["x_subscription_id"];
														$ipn["txn_id"] = $authnet["x_trans_id"];
														/**/
														$ipn["custom"] = $authnet["s2_custom"];
														/**/
														$ipn["mc_gross"] = number_format ($authnet["x_amount"], 2, ".", "");
														$ipn["mc_currency"] = strtoupper ("USD"); /* Auth.Net uses USD. */
														$ipn["tax"] = number_format ($authnet["x_tax"], 2, ".", "");
														/**/
														$ipn["payer_email"] = $authnet["x_email"];
														$ipn["first_name"] = $authnet["x_first_name"];
														$ipn["last_name"] = $authnet["x_last_name"];
														/**/
														$ipn["option_name1"] = "Referencing Customer ID";
														$ipn["option_selection1"] = $authnet["x_subscription_id"];
														/**/
														$ipn["option_name2"] = "Customer IP Address";
														$ipn["option_selection2"] = null;
														/**/
														$ipn["item_number"] = $authnet["s2_invoice"];
														$ipn["item_name"] = $authnet["x_description"];
														/**/
														$ipn_q = "&optimizemember_paypal_proxy=authnet&optimizemember_paypal_proxy_use=pro-emails";
														$ipn_q .= "&optimizemember_paypal_proxy_verification=" . urlencode (c_ws_plugin__optimizemember_paypal_utilities::paypal_proxy_key_gen ());
														/**/
														c_ws_plugin__optimizemember_utils_urls::remote (site_url ("/?optimizemember_paypal_notify=1" . $ipn_q), $ipn, array ("timeout" => 20));
													}
												else /* Otherwise, we don't have enough information to reforumalte this IPN response. An error must be generated. */
													{
														$authnet["optimizemember_log"][] = "Authorize.Net transaction identified as ( `ARB / PAYMENT #" . $authnet["x_subscription_paynum"] . "` ).";
														$authnet["optimizemember_log"][] = "Ignoring this IPN. The transaction does NOT contain a valid reference value/desc.";
													}
											}
										/**/
										else if ($authnet["x_subscription_id"] && $authnet["x_subscription_paynum"] && preg_match ("/^(2|3)$/", $authnet["x_response_code"]))
											{
												if (($_authnet = c_ws_plugin__optimizemember_pro_authnet_utilities::authnet_parse_arb_desc ($authnet)) && ($authnet = $_authnet))
													{
														$authnet["optimizemember_log"][] = "Authorize.Net transaction identified as ( `ARB / FAILED PAYMENT` ).";
														$authnet["optimizemember_log"][] = "optimizeMember does NOT respond to individual failed payment notifications.";
														$authnet["optimizemember_log"][] = "When multiple consecutive payments fail, optimizeMember is notified via ARB services.";
														$authnet["optimizemember_log"][] = "This does not require any action ( at the moment ) on the part of optimizeMember.";
													}
												else /* Otherwise, we don't have enough information to reforumalte this IPN response. An error must be generated. */
													{
														$authnet["optimizemember_log"][] = "Authorize.Net transaction identified as ( `ARB / FAILED PAYMENT` ).";
														$authnet["optimizemember_log"][] = "Ignoring this IPN. The transaction does NOT contain a valid reference value/desc.";
													}
											}
										/**/
										else if (!$processed) /* If nothing was processed, here we add a message to the logs indicating the IPN was ignored. */
											$authnet["optimizemember_log"][] = "Ignoring this IPN. The transaction does NOT require any action on the part of optimizeMember.";
									}
								else /* Extensive log reporting here. This is an area where many site owners find trouble. Depending on server configuration; remote HTTPS connections may fail. */
									{
										$authnet["optimizemember_log"][] = "Unable to verify POST vars. This is most likely related to an invalid Authorize.Net configuration. Please check: optimizeMember -> Authorize.Net Options.";
										$authnet["optimizemember_log"][] = "If you're absolutely SURE that your Authorize.Net configuration is valid, you may want to run some tests on your server, just to be sure \$_POST variables are populated, and that your server is able to connect to Authorize.Net over an HTTPS connection.";
										$authnet["optimizemember_log"][] = "optimizeMember uses the WP_Http class for remote connections; which will try to use cURL first, and then fall back on the FOPEN method when cURL is not available. On a Windows server, you may have to disable your cURL extension. Instead, set allow_url_fopen = yes in your php.ini file. The cURL extension (usually) does NOT support SSL connections on a Windows server.";
										$authnet["optimizemember_log"][] = var_export ($_REQUEST, true); /* Recording _POST + _GET vars for analysis and debugging. */
									}
								/*
								If debugging/logging is enabled; we need to append $authnet to the log file.
									Logging now supports Multisite Networking as well.
								*/
								$logv = c_ws_plugin__optimizemember_utilities::ver_details ();
								$logm = c_ws_plugin__optimizemember_utilities::mem_details ();
								$log4 = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "\nUser-Agent: " . $_SERVER["HTTP_USER_AGENT"];
								$log4 = (is_multisite () && !is_main_site ()) ? ($_log4 = $current_blog->domain . $current_blog->path) . "\n" . $log4 : $log4;
								$log2 = (is_multisite () && !is_main_site ()) ? "authnet-ipn-4-" . trim (preg_replace ("/[^a-z0-9]/i", "-", $_log4), "-") . ".log" : "authnet-ipn.log";
								/**/
								if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["gateway_debug_logs"])
									if (is_dir ($logs_dir = $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["logs_dir"]))
										if (is_writable ($logs_dir) && c_ws_plugin__optimizemember_utils_logs::archive_oversize_log_files ())
											file_put_contents ($logs_dir . "/" . $log2, $logv . "\n" . $logm . "\n" . $log4 . "\n" . var_export ($authnet, true) . "\n\n", FILE_APPEND);
								/**/
								status_header (200); /* Send a 200 OK status header. */
								header ("Content-Type: text/plain; charset=utf-8"); /* Content-Type text/plain with UTF-8. */
								eval ('while (@ob_end_clean ());'); /* End/clean all output buffers that may or may not exist. */
								/**/
								exit (); /* Exit now. */
							}
					}
			}
	}
?>