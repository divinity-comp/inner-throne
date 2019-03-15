<?php
/**
* Shortcode `[optimizeMember-PayPal-Button]` ( encryption sub-routines ).
*
* Copyright: © 2009-2011
* {@link http://www.optimizepress.com/ optimizePress, Inc.}
* ( coded in the USA )
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package optimizeMember\PayPal
* @since 3.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_sc_paypal_button_e"))
	{
		/**
		* Shortcode `[optimizeMember-PayPal-Button]` ( encryption sub-routines ).
		*
		* @package optimizeMember\PayPal
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_sc_paypal_button_e
			{
				/**
				* Handles PayPal Button encryption.
				*
				* This uses the PayPal API. optimizeMember will NOT attempt to encrypt Buttons until there is at least a Business Email Address and API Username configured.
				* optimizeMember also maintains a log of communication with the PayPal API. If logging is enabled, check: `/wp-content/plugins/optimizemember-logs/paypal-api.log`.
				*
				* @package optimizeMember\PayPal
				* @since 3.5
				*
				* @param str $code The PayPal Button Code before encryption.
				* @param array $vars An array of defined variables in the scope of the calling Filter.
				* @return str The Resulting PayPal Button Code *( possibly encrypted, depending on configuration )*.
				*/
				public static function sc_paypal_button_encryption ($code = FALSE, $vars = FALSE)
					{
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__optimizemember_before_sc_paypal_button_encryption", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["paypal_btn_encryption"] && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["paypal_business"] && $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["paypal_api_username"])
							{
								$cache = /* Are we caching? */ apply_filters ("ws_plugin__optimizemember_sc_paypal_button_encryption_cache", true, get_defined_vars ());
								/**/
								$_code = $vars["code"]; $attr = $vars["attr"]; /* Let's unpack ( i.e. use shorter references ) to these two important vars. */
								/**/
								if ($cache && ($transient = "s2m_btn_" . md5 ($code . c_ws_plugin__optimizemember_utilities::ver_checksum ())) && ($cache = get_transient ($transient)))
									$code = /* Great, so we can use the cached version here to save processing time. The MD5 hash uses ``$code`` and NOT ``$_code``. */ $cache;
								/**/
								else if /* Are we able to parse hidden input variables? */ (is_array ($inputs = c_ws_plugin__optimizemember_utils_forms::form_whips_2_array ($_code)) && !empty ($inputs))
									{
										$paypal = array ("METHOD" => "BMCreateButton", "BUTTONCODE" => "ENCRYPTED", "BUTTONTYPE" => (($attr["sp"] || $attr["rr"] === "BN") ? "BUYNOW" : "SUBSCRIBE"));
										/**/
										$i = 0; /* Initialize incremental variable counter. PayPal wants these numbered using L_BUTTONVAR{n}; where {n} starts at zero. */
										foreach ($inputs as $input => $value) /* Now run through each of the input variables that we parsed from the Full Button Code */
											if (!preg_match ("/^cmd$/i", $input)) /* Don't include the `cmd` var; it will produce major errors in the API response. */
												{
													/* The PayPal API method `BMCreateButton` expects (amount|a1|a3) to include 2 decimal places. */
													if (preg_match ("/^(amount|a1|a3)$/i", $input))
														$value = number_format ($value, 2, ".", "");
													/**/
													$paypal["L_BUTTONVAR" . $i] = $input . "=" . $value;
													$i++; /* Increment variable counter. */
												}
										/**/
										if (($paypal = c_ws_plugin__optimizemember_paypal_utilities::paypal_api_response ($paypal)) && empty ($paypal["__error"]) && !empty ($paypal["WEBSITECODE"]) && ($code = $paypal["WEBSITECODE"]))
											/* Only proceed if we DID get a valid response from the PayPal API. This works as a nice fallback; just in case the API connection fails. */
											{
												$default_image = "https://www.paypal.com/" . _x ("en_US", "s2member-front paypal-button-lang-code", "s2member") . "/i/btn/btn_xpressCheckout.gif";
												/**/
												$code = preg_replace ("/\<img[^\>]+\>/i", "", $code); /* Remove the 1x1 pixel tracking image that PayPal sticks in there. */
												// PHP 7 compat
												$code = preg_replace_callback ("/(\<input)([^\>]+)(\>)/i", "c_ws_plugin__optimizemember_sc_paypal_button_e::_sc_paypal_button_encryption_preg_replace_xhtml_callback", $code);
												/**/
												$code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);
												/**/
												$code = ($attr["output"] === "anchor") ? '<a href="' . esc_attr (c_ws_plugin__optimizemember_utils_forms::form_whips_2_url ($code)) . '"><img src="' . esc_attr (($attr["image"] && $attr["image"] !== "default") ? $attr["image"] : $default_image) . '" style="width:auto; height:auto; border:0;" alt="PayPal" /></a>' : $code;
												$code = ($attr["output"] === "url") ? c_ws_plugin__optimizemember_utils_forms::form_whips_2_url ($code) : $code;

												// button output in OptimizePress --- DO NOT REMOVE!
												$code = ($attr["output"] === "button" && !empty($vars['content'])) ? preg_replace ("/<input\stype=\"image\"([^\>]+)(\>)/i", $vars['content'], $code) : $code;
												
												($cache && $transient) ? set_transient ($transient, $code, apply_filters ("ws_plugin__optimizemember_sc_paypal_button_encryption_cache_exp_time", 3600, get_defined_vars ())) : null; /* Caching? */
											}
									}
							}
						/* No WordPress Filters apply here. */
						/* Instead, use: `ws_plugin__optimizemember_sc_paypal_button`. */
						return $code; /* Button Code. Possibly w/ API encryption now. */
					}


			/**
			 * Preg replace callback method.
			 * 
			 * @param $m
			 * @return string
             */
			public static function _sc_paypal_button_encryption_preg_replace_xhtml_callback($m)
					{
						return $m[1].rtrim($m[2], " /\t\n\r\0\x0B").' /'.$m[3];
					}
			}
	}