<?php
/**
* Shortcode `[optimizeMember-Pro-Google-Button /]` ( inner processing routines ).
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
* @package optimizeMember\Google
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_google_button_in"))
	{
		/**
		* Shortcode `[optimizeMember-Pro-Google-Button /]` ( inner processing routines ).
		*
		* @package optimizeMember\Google
		* @since 1.5
		*/
		class c_ws_plugin__optimizemember_pro_google_button_in
			{
				/**
				* Shortcode `[optimizeMember-Pro-Google-Button /]`.
				*
				* @package optimizeMember\Google
				* @since 1.5
				*
				* @attaches-to ``add_shortcode("optimizeMember-Pro-Google-Button");``
				*
				* @param array $attr An array of Attributes.
				* @param str $content Content inside the Shortcode.
				* @param str $shortcode The actual Shortcode name itself.
				* @return str The resulting Google Button Code, HTML markup.
				*/
				public static function sc_google_button ($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						c_ws_plugin__optimizemember_no_cache::no_cache_constants /* No caching on pages that contain this Payment Button. */ (true);
						/**/
						$attr = /* Force array. Trim quote entities. */ c_ws_plugin__optimizemember_utils_strings::trim_qts_deep ((array)$attr);
						/**/
						$attr = shortcode_atts (array ("ids" => "0", "exp" => "72", "level" => "1", "ccaps" => "", "desc" => "", "cc" => "USD", "custom" => $_SERVER["HTTP_HOST"], "ta" => "0", "tp" => "0", "tt" => "D", "ra" => "0.01", "rp" => "1", "rt" => "M", "rr" => "1", "modify" => "0", "cancel" => "0", "sp" => "0", "image" => "default", "output" => "anchor"), $attr);
						/**/
						$attr["tt"] = /* Term lengths absolutely must be provided in upper-case format. Only after running shortcode_atts(). */ strtoupper ($attr["tt"]);
						$attr["rt"] = /* Term lengths absolutely must be provided in upper-case format. Only after running shortcode_atts(). */ strtoupper ($attr["rt"]);
						$attr["rr"] = /* Must be provided in upper-case format. Numerical, or BN value. Only after running shortcode_atts(). */ strtoupper ($attr["rr"]);
						$attr["ccaps"] = /* Custom Capabilities must be typed in lower-case format. Only after running shortcode_atts(). */ strtolower ($attr["ccaps"]);
						$attr["rr"] = /* Lifetime Subscriptions require Buy Now. Only after running shortcode_atts(). */ ($attr["rt"] === "L") ? "BN" : $attr["rr"];
						$attr["rr"] = /* Independent Ccaps do NOT recur. Only after running shortcode_atts(). */ ($attr["level"] === "*") ? "BN" : $attr["rr"];
						$attr["rr"] = /* No Trial / non-recurring. Only after running shortcode_atts(). */ (!$attr["tp"] && !$attr["rr"]) ? "BN" : $attr["rr"];
						/**/
						if /* Modifications/Cancellations. */ ($attr["modify"] || $attr["cancel"])
							{
								$default_image = $GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images/google-edit-button.png";
								/**/
								$code = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (dirname (dirname (__FILE__)))) . "/templates/buttons/google-cancellation-button.php")));
								$code = preg_replace ("/%%images%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (site_url ())), $code);
								/**/
								$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);
								/**/
								$code = ($attr["output"] === "anchor") ? /* Buttons already in anchor format. */ $code : $code;
								if ($attr["output"] === "url" && preg_match ('/ href\="(.*?)"/', $code, $m) && ($href = $m[1]))
									$code = ($url = c_ws_plugin__optimizemember_utils_urls::n_amps ($href));
								/**/
								unset /* Just a little housekeeping */ ($href, $url, $m);
							}
						else if /* Specific Post/Page Buttons. */ ($attr["sp"])
							{
								$default_image = "https://checkout.google.com/buttons/checkout.gif?merchant_id=" . urlencode ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_google_merchant_id"]) . "&amp;w=180&amp;h=46&amp;style=trans&amp;variant=text&amp;loc=" . urlencode (_x ("en_US", "s2member-front google-button-lang-code", "s2member"));
								/**/
								$code = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (dirname (dirname (__FILE__)))) . "/templates/buttons/google-sp-checkout-button.php")));
								$code = preg_replace ("/%%images%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (site_url ())), $code);
								/**/
								foreach /* Google Buttons are simply a reflection of these attributes. */ ($attr as $key => $val)
									$code = preg_replace ("/%%" . preg_quote ($key, "/") . "%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (urlencode ($val)), $code);
								/**/
								if (preg_match ('/ href\="(.*?)"/', $code, $m) && ($url = c_ws_plugin__optimizemember_utils_urls::n_amps ($m[1])))
									$code = preg_replace ('/ href\=".*?"/', ' href="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (c_ws_plugin__optimizemember_utils_urls::add_optimizemember_sig ($url))) . '"', $code);
								/**/
								$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);
								/**/
								$code = ($attr["output"] === "anchor") ? /* Buttons already anchor format. */ $code : $code;
								if ($attr["output"] === "url" && preg_match ('/ href\="(.*?)"/', $code, $m) && ($href = $m[1]))
									$code = ($url = c_ws_plugin__optimizemember_utils_urls::n_amps ($href));
								/**/
								unset /* Just a little housekeeping */ ($href, $url, $m);
							}
						else if /* Independent Custom Capabilities. */ ($attr["level"] === "*")
							{
								$default_image = "https://checkout.google.com/buttons/checkout.gif?merchant_id=" . urlencode ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_google_merchant_id"]) . "&amp;w=180&amp;h=46&amp;style=trans&amp;variant=text&amp;loc=" . urlencode (_x ("en_US", "s2member-front google-button-lang-code", "s2member"));
								/**/
								$code = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (dirname (dirname (__FILE__)))) . "/templates/buttons/google-ccaps-checkout-button.php")));
								$code = preg_replace ("/%%images%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (site_url ())), $code);
								/**/
								foreach /* Google Buttons are simply a reflection of these attributes. */ ($attr as $key => $val)
									$code = preg_replace ("/%%" . preg_quote ($key, "/") . "%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (urlencode ($val)), $code);
								/**/
								if (preg_match ('/ href\="(.*?)"/', $code, $m) && ($url = c_ws_plugin__optimizemember_utils_urls::n_amps ($m[1])))
									$code = preg_replace ('/ href\=".*?"/', ' href="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (c_ws_plugin__optimizemember_utils_urls::add_optimizemember_sig ($url))) . '"', $code);
								/**/
								$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);
								/**/
								$code = ($attr["output"] === "anchor") ? /* Buttons already anchor format. */ $code : $code;
								if ($attr["output"] === "url" && preg_match ('/ href\="(.*?)"/', $code, $m) && ($href = $m[1]))
									$code = ($url = c_ws_plugin__optimizemember_utils_urls::n_amps ($href));
								/**/
								unset /* Just a little housekeeping */ ($href, $url, $m);
							}
						else /* Otherwise, we'll process this Button normally, using Membership routines. */
							{
								$default_image = "https://checkout.google.com/buttons/checkout.gif?merchant_id=" . urlencode ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_google_merchant_id"]) . "&amp;w=180&amp;h=46&amp;style=trans&amp;variant=text&amp;loc=" . urlencode (_x ("en_US", "s2member-front google-button-lang-code", "s2member"));
								/**/
								$code = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (dirname (dirname (__FILE__)))) . "/templates/buttons/google-checkout-button.php")));
								/*
								 * Custom button
								 */
								if (!empty($content)) {	
									$code = preg_replace('/%%button_markup%%/', $content, $code);
									$code = preg_replace('/%%url%%/', '%%wpurl%%/?optimizemember_pro_google_co=1&amp;co[level]=%%level%%&amp;co[ccaps]=%%ccaps%%&amp;co[desc]=%%desc%%&amp;co[cc]=%%cc%%&amp;co[custom]=%%custom%%&amp;co[ta]=%%ta%%&amp;co[tp]=%%tp%%&amp;co[tt]=%%tt%%&amp;co[ra]=%%ra%%&amp;co[rp]=%%rp%%&amp;co[rt]=%%rt%%&amp;co[rr]=%%rr%%&amp;co[image]=%%image%%&amp;co[output]=%%output%%', $code);
								} else {
									$code = preg_replace ("/%%button_markup%%/", '<a href="%%wpurl%%/?optimizemember_pro_google_co=1&amp;co[level]=%%level%%&amp;co[ccaps]=%%ccaps%%&amp;co[desc]=%%desc%%&amp;co[cc]=%%cc%%&amp;co[custom]=%%custom%%&amp;co[ta]=%%ta%%&amp;co[tp]=%%tp%%&amp;co[tt]=%%tt%%&amp;co[ra]=%%ra%%&amp;co[rp]=%%rp%%&amp;co[rt]=%%rt%%&amp;co[rr]=%%rr%%&amp;co[image]=%%image%%&amp;co[output]=%%output%%"><img src="https://checkout.google.com/buttons/checkout.gif?w=180&amp;h=46&amp;style=trans&amp;variant=text&amp;loc=' . urlencode (_x ("en_US", "s2member-front google-button-lang-code", "s2member")) . '" style="width:auto; height:auto; border:0;" alt="Google" /></a>', $code);
								}

								$code = preg_replace ("/%%images%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (site_url ())), $code);
								/**/
								foreach /* Google Buttons are simply a reflection of these attributes. */ ($attr as $key => $val)
									$code = preg_replace ("/%%" . preg_quote ($key, "/") . "%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (urlencode ($val)), $code);
								/**/
								if (preg_match ('/ href\="(.*?)"/', $code, $m) && ($url = c_ws_plugin__optimizemember_utils_urls::n_amps ($m[1])))
									$code = preg_replace ('/ href\=".*?"/', ' href="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (c_ws_plugin__optimizemember_utils_urls::add_optimizemember_sig ($url))) . '"', $code);
								/*
								 * Only when there is no custom button
								 */
								if (empty($content)) {
									$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);
								}
								/**/
								$code = ($attr["output"] === "anchor") ? /* Buttons already anchor format. */ $code : $code;
								if ($attr["output"] === "url" && preg_match ('/ href\="(.*?)"/', $code, $m) && ($href = $m[1]))
									$code = ($url = c_ws_plugin__optimizemember_utils_urls::n_amps ($href));
								/**/
								unset /* Just a little housekeeping */ ($href, $url, $m);
							}
						/**/
						$code = preg_replace('/^\s+|\n|\r|\s+$/m', '', $code);
						return /* Button. */ $code;
					}
			}
	}
?>