<?php
/**
* Menu page for optimizeMember Pro ( ccBill Buttons page ).
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
* @package optimizeMember\Menu_Pages
* @since 1.5
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_pro_menu_page_ccbill_buttons"))
	{
		/**
		* Menu page for optimizeMember Pro ( ccBill Buttons page ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__optimizemember_pro_menu_page_ccbill_buttons
			{
				public function __construct ()
					{
						echo '<div class="wrap ws-menu-page op-bsw-wizard op-bsw-content">' . "\n";
						/**/
						echo '<div class="op-bsw-header">';
							echo '<div class="op-logo"><img src="' . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images/" . 'logo-optimizepress.png" alt="OptimizePress" height="50" class="animated flipInY"></div>';
						echo '</div>';
						echo '<div class="op-bsw-main-content inline-input-fields">';
						echo '<h2>ccBill Buttons</h2>' . "\n";
						/**/
						echo '<table class="ws-menu-page-table">' . "\n";
						echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
						echo '<tr class="ws-menu-page-table-tr">' . "\n";
						echo '<td class="ws-menu-page-table-l">' . "\n";
						/**/
						for ($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
							{
								$labelText = ws_plugin__optimizemember_getMembershipLabel($n);
								echo '<div class="ws-menu-page-group" title="ccBill Buttons For Level ' . $labelText . ' Access">' . "\n";
								/**/
								echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-level' . $n . '-buttons-section">' . "\n";
								echo '<h3>Button Code Generator For Level ' . $labelText . ' Access</h3>' . "\n";
								echo '<p>Very simple. All you do is customize the form fields provided, for each Membership Level that you plan to offer. Then press (Generate Button Code). These ccBill Buttons are customized to work with optimizeMember seamlessly. Member accounts will be activated instantly, in an automated fashion. When you, or a Member, cancels their Membership, or fails to make payments on time, optimizeMember will automatically terminate their Membership privileges. optimizeMember makes extensive use of the ccBill IPN/Postback and DataLink services. optimizeMember receives updates from ccBill behind-the-scene. <em>* Buttons are NOT saved here. This is only a Button Generator. Once you\'ve generated your Button, copy/paste it into your Membership Options Page. If you lose your Button Code, you\'ll need to re-generate a new one.</em></p>' . "\n";
								/**/
								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";
								/**/
								echo '<th class="ws-menu-page-th-side">' . "\n";
								echo '<label for="ws-plugin--optimizemember-pro-level' . $n . '-shortcode">' . "\n";
								echo 'Button Code<br />For Level ' . $labelText . ':<br /><br />' . "\n";
								echo '<div id="ws-plugin--optimizemember-pro-level' . $n . '-button-prev"></div>' . "\n";
								echo '</label>' . "\n";
								echo '</th>' . "\n";
								/**/
								echo '<td>' . "\n";
								echo '<form onsubmit="return false;">' . "\n";
								echo '<p id="ws-plugin--optimizemember-pro-level' . $n . '-trial-line">I\'ll offer the first <input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-level' . $n . '-trial-period" value="0" size="6" /> <select id="ws-plugin--optimizemember-pro-level' . $n . '-trial-term">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-membership-trial-terms.php"))) . '</select> @ $<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-level' . $n . '-trial-amount" value="0.00" size="4" /></p>' . "\n";
								echo '<p><span id="ws-plugin--optimizemember-pro-level' . $n . '-trial-then">Then, </span>I want to charge: $<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-level' . $n . '-amount" value="2.95" size="4" /> / <select id="ws-plugin--optimizemember-pro-level' . $n . '-term" class="ccBill-buttons-subscription-select">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-membership-regular-terms.php"))) . '</select></p>' . "\n";
								echo '<p>Description: <input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-level' . $n . '-desc" class="ccBill-buttons-description-input" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_label"]) . ' / description and pricing details here." size="73" /></p>' . "\n";
								//echo '<p' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '>Custom Capabilities ( comma-delimited ) <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: optimizeMember -> API Scripting -> Custom Capabilities.\\n\\n*ADVANCED TIP: You can specifiy a list of Custom Capabilities that will be (Added) with this purchase. Or, you could tell optimizeMember to (Remove All) Custom Capabilities that may or may not already exist for a particular Member, and (Add) only the new ones that you specify. To do this, just start your list of Custom Capabilities with `-all`.\\n\\nSo instead of just (Adding) Custom Capabilities:\\nmusic,videos,archives,gifts\\n\\nYou could (Remove All) that may already exist, and then (Add) new ones:\\n-all,calendar,forums,tools\\n\\nOr to just (Remove All) and (Add) nothing:\\n-all\'); return false;" tabindex="-1">[?]</a> <input type="text" maxlength="125" autocomplete="off" id="ws-plugin--optimizemember-pro-level' . $n . '-ccaps" size="40" /></p>' . "\n";
								echo 'Packages: ';
										if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
											foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
												echo '<input type="checkbox" onchange="showSelectedValues(\'ccpchk_'.$n.'\', \'ccaps\', '.$n.');" name="ws_plugin__optimizemember_ccpchk_'.$n.'" value="'.$val.'" /> '. $val . "\n";

											}
										} else {
											echo 'To use packages, enter some first in <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--optimizemember-gen-ops")).'">Define membership levels and packages section.</a>';
										}
										echo '<input type="hidden" autocomplete="off" name="ws_plugin__optimizemember_security_meta_box_ccaps" id="ws-plugin--optimizemember-pro-level' . $n . '-ccaps" value="" />'."\n";
								echo '<p>Currency: <select id="ws-plugin--optimizemember-pro-level' . $n . '-currency">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-currencies.php"))) . '</select> <input type="button" value="Generate Button Code" onclick="ws_plugin__optimizemember_pro_ccbillButtonGenerate(\'level' . $n . '\');" class="button-primary" /></p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";
								/**/
								echo '</tr>' . "\n";
								echo '<tr>' . "\n";
								/**/
								echo '<td colspan="2">' . "\n";
								echo '<form onsubmit="return false;">' . "\n";
								echo '<strong>WordPress Shortcode:</strong> ( recommended for both the WordPress Visual &amp; HTML Editors )<br />' . "\n";
								$ws_plugin__optimizemember_pro_temp_s = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/ccbill-checkout-button-shortcode.php")));
								$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/%%level%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($n)), $ws_plugin__optimizemember_pro_temp_s);
								$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/%%level_label%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_label"])), $ws_plugin__optimizemember_pro_temp_s);
								$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__optimizemember_pro_temp_s);
								echo '<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-level' . $n . '-shortcode" value="' . format_to_edit ($ws_plugin__optimizemember_pro_temp_s) . '" onclick="this.select ();" style="font-family:Consolas, monospace; width:99%;" />' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";
								/**/
								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";
								/**/
								echo '</div>' . "\n";
							}
						/**/
							?>
							<script type="text/javascript">
							window.showSelectedValues = function(param, elemId, n) {
									var text = jQuery("input[name=ws_plugin__optimizemember_"+param+"]:checked").map(
									     function () {return this.value;}).get().join(",");
									jQuery("#ws-plugin--optimizemember-pro-level"+n+"-" + elemId).val(text);


								}
							window.showSingleSelectedValues = function(param, elemId, n) {
								var text = jQuery("input[name=ws_plugin__optimizemember_"+param+"]:checked").map(
								     function () {return this.value;}).get().join(",");
								jQuery("#ws-plugin--optimizemember-pro-" + elemId + "-ccaps").val(text);


							}
								/**/
							</script>
							<?php
						echo '<div class="ws-menu-page-group" title="ccBill Modification/Cancellation Buttons">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-cancellation-buttons-section">' . "\n";
						echo '<h3>One Button Does It All For Modifications/Cancellations ( copy/paste )</h3>' . "\n";
						echo '<p>Every ccBill Recurring Subscription can be modified by the Customer, or even cancelled by the Customer through ccBill. It\'s very simple. A Member clicks a Modification/Cancellation Button. This brings the Customer to an "Order Lookup" screen at ccBill.com. Here they\'ll have easy access to make any changes they like. When important changes occur ( such as a cancellation ), information regarding this event will be relayed back to optimizeMember through ccBill\'s DataLink service. optimizeMember will react appropriately at that time.</p>' . "\n";
						echo '<p><em><strong>*Understanding Cancellations*</strong> It\'s important to realize that a Cancellation is not an EOT ( End Of Term ). All that happens during a Cancellation event, is that billing is stopped, and it\'s understood that the Customer is going to lose access, at some point in the future. This does NOT mean, that access will be revoked immediately. A separate EOT event will automatically handle a (demotion or deletion) later, at the appropriate time; which could be several days, or even a year after the Cancellation took place.</em></p>' . "\n";
						echo '<p><em><strong>*Some Hairy Details*</strong> There might be times whenever you notice that a Member\'s Subscription has been cancelled through ccBill... but, optimizeMember continues allowing the User access to your site as a paid Member. Please don\'t be confused by this... in 99.9% of these cases, the reason for this is legitimate. optimizeMember will only remove the User\'s Membership privileges when an EOT ( End Of Term ) is processed, a refund occurs, a chargeback occurs, or when a cancellation occurs - which would later result in a delayed Auto-EOT by optimizeMember.</em></p>' . "\n";
						echo '<p><em>optimizeMember will not process an EOT ( End Of Term ) until the User has completely used up the time they paid for. In other words, if a User signs up for a monthly Subscription on Jan 1st, and then cancels their Subscription on Jan 15th; technically, they should still be allowed to access the site for another 15 days, and then on Feb 1st, the time they paid for has completely elapsed. At that time, optimizeMember will remove their Membership privileges; by either demoting them to a Free Subscriber, or deleting their account from the system ( based on your configuration ). optimizeMember also calculates one extra day ( 24 hours ) into its equation, just to make sure access is not removed sooner than a Customer might expect.</em></p>' . "\n";
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<th class="ws-menu-page-th-side">' . "\n";
						echo '<label for="ws-plugin--optimizemember-pro-cancellation-shortcode">' . "\n";
						echo 'Button Code<br />For Cancellations:<br /><br />' . "\n";
						echo '<div id="ws-plugin--optimizemember-pro-cancellation-button-prev">' . "\n";
						$ws_plugin__optimizemember_pro_temp_s = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/ccbill-cancellation-button.php")));
						$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/%%images%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember_pro"]["c"]["dir_url"] . "/images")), $ws_plugin__optimizemember_pro_temp_s);
						$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/%%wpurl%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr (site_url ())), $ws_plugin__optimizemember_pro_temp_s);
						$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/&amp;/", "&", $ws_plugin__optimizemember_pro_temp_s); /* Match this with the JavaScript generator. */
						echo preg_replace ("/\<a/", '<a target="_blank"', $ws_plugin__optimizemember_pro_temp_s);
						echo '</div>' . "\n";
						echo '</label>' . "\n";
						echo '</th>' . "\n";
						/**/
						echo '<td class="align-top">' . "\n";
						echo '<form onsubmit="return false;">' . "\n";
						echo '<p>No configuration necessary.</p>' . "\n";
						echo '</form>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td colspan="2">' . "\n";
						echo '<form onsubmit="return false;">' . "\n";
						echo '<strong>WordPress Shortcode:</strong> ( recommended for both the WordPress Visual &amp; HTML Editors )<br />' . "\n";
						$ws_plugin__optimizemember_pro_temp_s = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/ccbill-cancellation-button-shortcode.php")));
						echo '<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-cancellation-shortcode" value="' . format_to_edit ($ws_plugin__optimizemember_pro_temp_s) . '" onclick="this.select ();" style="font-family:Consolas, monospace; width:99%;" />' . "\n";
						echo '</form>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						if (!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ())
							{
								echo '<div class="ws-menu-page-group" title="ccBill Capability (Buy Now) Buttons">' . "\n";
								/**/
								echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-ccap-buttons-section">' . "\n";
								echo '<h3>Button Code Generator For Independent Custom Capabilities</h3>' . "\n";
								echo '<p>This is VERY advanced. For further details, please check your Dashboard: <code>optimizeMember -> API Scripting -> Custom Capabiities</code>.</p>' . "\n";
								echo '<p>With optimizeMember, you can sell one or more Custom Capabilities using Buy Now functionality, to "existing" Users/Members, regardless of which Membership Level they have on your site <em>( i.e. you could even sell Independent Custom Capabilities to Users at Membership Level #0, normally referred to as Free Subscribers, if you like )</em>. So this is quite flexible. Independent Custom Capabilities do NOT rely on any specific Membership Level. That\'s why optimizeMember refers to these as `Independent` Custom Capabilities, because you can sell Capabilities this way, through Buy Now functionality, and the Customer\'s Membership Level Access, along with any existing paid Subscription they may already have with you, will remain completely unaffected. That being said, if you intend to charge a recurring fee for Custom Capabilities, please use a <code>Membership Level# Button</code> instead; because Independent Custom Capabilities can only be sold through Buy Now functionality.</p>' . "\n";
								echo '<p>Independent Custom Capabilities are added to a Customer\'s account immediately after checkout, and the Customer will have the Custom Capabilities for as long as their Membership lasts, based on their primary Subscription with your site, and/or forever, if they have a Lifetime account with you. In other words, Independent Custom Capabilities will exist on the Customer\'s account forever, or until an EOT <em>( End Of Term )</em> occurs on their primary Subscription with you; in which case optimizeMember would demote or delete the Customer\'s account <em>( based on your EOT configuration )</em>, and all Custom Capabilities are removed as well.</p>' . "\n";
								echo '<p>Very simple. All you do is customize the form fields provided, for each set of Custom Capabilities that you plan to sell. Then press (Generate Button Code). These special ccBill Buttons are customized to work with optimizeMember seamlessly. The Customer will be granted additional access to one or more Custom Capabilities that you specify; while the Customer\'s Membership Level Access and any existing paid Subscription they may already have with you, will remain completely unaffected.</p>' . "\n";
								echo '<p><em><strong>*Important Note*</strong> Independent Custom Capability Buttons should ONLY be displayed to existing Users/Members, and they MUST be logged-in, BEFORE clicking this Button. Otherwise, post-processing of their transaction will fail to recognize the Customer\'s existing account within WordPress. Please display this Button only to Users/Members that are already logged into their account ( perhaps in your Login Welcome Page for optimizeMember ), or in another location where you can be absolutely sure that a User/Member is logged in. optimizeMember\'s Simple Conditionals could also be used to ensure a User/Member is logged in, by wrapping your Shortcode within a Conditional test. For further details, please see: <code>optimizeMember -> API Scripting -> Simple Conditionals</code>.</em></p>' . "\n";
								echo '<p><em>* Buttons are NOT saved here. This is only a Button Generator. If you lose your Button Code, you\'ll need to come back &amp; re-generate a new one.</em></p>' . "\n";
								/**/
								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";
								/**/
								echo '<th class="ws-menu-page-th-side">' . "\n";
								echo '<label for="ws-plugin--optimizemember-pro-ccap-shortcode">' . "\n";
								echo 'Button Code<br />For Capabilities:<br /><br />' . "\n";
								echo '<div id="ws-plugin--optimizemember-pro-ccap-button-prev"></div>' . "\n";
								echo '</label>' . "\n";
								echo '</th>' . "\n";
								/**/
								echo '<td>' . "\n";
								echo '<form onsubmit="return false;">' . "\n";
								echo '<p>I want to charge: $<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-ccap-amount" value="2.95" size="4" /> / <select id="ws-plugin--optimizemember-pro-ccap-term">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-membership-ccap-terms.php"))) . '</select> <a href="#" onclick="alert(\'Note. ccBill does NOT allow a purchase to last beyond 1 year. So although optimizeMember will allow the Customer ongoing access to Independent Custom Capabilities ( details above ); the checkout form at ccBill will unfortunately show (1 year, or 365 days), due to ccBill limitations. Again, this does NOT affect Customer access. It only affects the presentation of the checkout form. Please read the section above for details regarding Independent Custom Capabilities.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";
								echo '<p>Description: <input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-ccap-desc" value="Description and pricing details here." size="73" /></p>' . "\n";
								//echo '<p>Custom Capabilities ( comma-delimited ) <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: optimizeMember -> API Scripting -> Custom Capabilities.\\n\\n*ADVANCED TIP: You can specifiy a list of Custom Capabilities that will be (Added) with this purchase. Or, you could tell optimizeMember to (Remove All) Custom Capabilities that may or may not already exist for a particular Member, and (Add) only the new ones that you specify. To do this, just start your list of Custom Capabilities with `-all`.\\n\\nSo instead of just (Adding) Custom Capabilities:\\nmusic,videos,archives,gifts\\n\\nYou could (Remove All) that may already exist, and then (Add) new ones:\\n-all,calendar,forums,tools\\n\\nOr to just (Remove All) and (Add) nothing:\\n-all\'); return false;" tabindex="-1">[?]</a> <input type="text" maxlength="125" autocomplete="off" id="ws-plugin--optimizemember-pro-ccap-ccaps" size="40" /></p>' . "\n";
								echo 'Packages: ';
										$n=1;
										if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
											foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
												echo '<input type="checkbox" onchange="showSingleSelectedValues(\'ccpchkccaps_'.$n.'\', \'ccap\', '.$n.');" name="ws_plugin__optimizemember_ccpchkccaps_'.$n.'" value="'.$val.'" /> '. $val . "\n";

											}
										} else {
											echo 'To use packages, enter some first in <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--optimizemember-gen-ops")).'">Define membership levels and packages section.</a>';
										}
										echo '<input type="hidden" autocomplete="off" name="ws_plugin__optimizemember_security_meta_box_ccaps" id="ws-plugin--optimizemember-pro-ccap-ccaps" value="" />'."\n";
								echo '<p>Currency: <select id="ws-plugin--optimizemember-pro-ccap-currency">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-currencies.php"))) . '</select> <input type="button" value="Generate Button Code" onclick="ws_plugin__optimizemember_pro_ccbillCcapButtonGenerate();" class="button-primary" /></p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";
								/**/
								echo '</tr>' . "\n";
								echo '<tr>' . "\n";
								/**/
								echo '<td colspan="2">' . "\n";
								echo '<form onsubmit="return false;">' . "\n";
								echo '<strong>WordPress Shortcode:</strong> ( recommended for both the WordPress Visual &amp; HTML Editors )<br />' . "\n";
								$ws_plugin__optimizemember_pro_temp_s = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/ccbill-ccaps-checkout-button-shortcode.php")));
								$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__optimizemember_pro_temp_s);
								echo '<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-ccap-shortcode" value="' . format_to_edit ($ws_plugin__optimizemember_pro_temp_s) . '" onclick="this.select ();" style="font-family:Consolas, monospace; width:99%;" />' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";
								/**/
								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";
								/**/
								echo '</div>' . "\n";
							}
						/**/
						echo '<div class="ws-menu-page-group" title="ccBill Member Registration Access Links">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-reg-links-section">' . "\n";
						echo '<h3>Registration Access Link Generator ( for Customer Service )</h3>' . "\n";
						echo '<p>optimizeMember automatically generates Registration Access Links for your Customers after checkout, and also sends them a link in a Confirmation Email. However, if you ever need to deal with a Customer Service issue that requires a new Registration Access Link to be created manually, you can use this tool for that. Alternatively, you can create their account yourself/manually by going to <code>optimizeMember -> Add A Member</code>. Either of these methods will work fine.</p>' . "\n";
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<form onsubmit="return false;">' . "\n";
						echo '<p>Paid Membership Level#: <select id="ws-plugin--optimizemember-pro-reg-link-level">' . "\n";
						for ($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++) {
							$labelText = ws_plugin__optimizemember_getMembershipLabel($n);
							echo '<option value="' . $n . '">optimizeMember Level ' . $labelText . '</option>' . "\n";
						}
						echo '</select></p>' . "\n";
						echo '<p>Paid Subscr. ID: <input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-reg-link-subscr-id" value="" size="50" /> <a href="#" onclick="alert(\'The Customer\\\'s Paid Subscr. ID ( aka: ccBill Subscription ID or Transaction ID ) must be unique. This value can be obtained from inside your ccBill transaction log. Each paying Customer MUST be associated with a unique Paid Subscr. ID. If the Customer is NOT associated with a Paid Subscr. ID, you will need to generate a unique value for this field on your own. But keep in mind, optimizeMember will be unable to maintain future communication with the ccBill DataLink service if this value does not reflect a real Paid Subscr. ID that exists in your ccBill transaction log.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";
						echo '<p>Custom String Value: <input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-reg-link-custom" value="' . esc_attr ($_SERVER["HTTP_HOST"]) . '" size="30" /> <a href="#" onclick="alert(\'A Paid Subscription is always associated with a Custom String that is passed through the custom=\\\'\\\'' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"]), 3) . '\\\'\\\' attribute of your Shortcode. This Custom Value, MUST always start with your domain name. However, you can also pipe delimit additional values after your domain, if you need to.\\n\\nFor example:\n' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"]), 3) . '|cv1|cv2|cv3\'); return false;" tabindex="-1">[?]</a> <input type="button" value="Generate Access Link" onclick="ws_plugin__optimizemember_pro_ccbillRegLinkGenerate();" class="button-primary" /> <img id="ws-plugin--optimizemember-pro-reg-link-loading" src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]) . '/images/ajax-loader.gif" alt="" style="display:none;" /></p>' . "\n";
						//echo '<p' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '>Custom Capabilities ( comma-delimited ) <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: optimizeMember -> API Scripting -> Custom Capabilities.\'); return false;" tabindex="-1">[?]</a> <input type="text" maxlength="125" autocomplete="off" id="ws-plugin--optimizemember-pro-reg-link-ccaps" size="40" onkeyup="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" /></p>' . "\n";
						echo 'Packages: ';
										$n=1;
										if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
											foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
												echo '<input type="checkbox" onchange="showSingleSelectedValues(\'ccpchkreg_'.$n.'\', \'reg-link\', '.$n.');" name="ws_plugin__optimizemember_ccpchkreg_'.$n.'" value="'.$val.'" /> '. $val . "\n";

											}
										} else {
											echo 'To use packages, enter some first in <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--optimizemember-gen-ops")).'">Define membership levels and packages section.</a>';
										}
										echo '<input type="hidden" autocomplete="off" name="ws_plugin__optimizemember_security_meta_box_ccaps" id="ws-plugin--optimizemember-pro-reg-link-ccaps" value="" />'."\n";
						echo '<p>Fixed Term Length ( for Non-Recurring Subscriptions ): <input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-reg-link-fixed-term" value="" size="10" /> <a href="#" onclick="alert(\'If the Customer purchased Membership through a non-recurring transaction ( i.e. there is no Initial/Trial Period and no recurring charges for ongoing access ), you may configure a Fixed Term Length in this field. This way the Customer\\\'s Membership Access is revoked by optimizeMember at the appropriate time. This will be a numeric value, followed by a space, then a single letter.\\n\\nHere are some examples:\\n\\n1 D ( this means 1 Day )\\n1 W ( this means 1 Week )\\n1 M ( this means 1 Month )\\n1 Y ( this means 1 Year )\\n1 L ( this means 1 Lifetime )\'); return false;">[?]</a></p>' . "\n";
						echo '<p id="ws-plugin--optimizemember-pro-reg-link" style="font-family:Consolas, monospace; display:none;"></p>' . "\n";
						echo '</form>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '<div class="ws-menu-page-group" title="ccBill Specific Post/Page (Buy Now) Buttons">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-sp-buttons-section">' . "\n";
						echo '<h3>Button Code Generator For Specific Post/Page Buttons</h3>' . "\n";
						echo '<p>optimizeMember now supports an additional layer of functionality ( very powerful ), which allows you to sell access to specific Posts/Pages that you\'ve created in WordPress. Specific Post/Page Access works independently from Member Level Access. That is, you can sell an unlimited number of Posts/Pages using "Buy Now / Non-Recurring" Buttons, and your Customers will NOT be required to have a Membership Account with your site in order to receive access. If they are already a Member, that\'s fine, but they won\'t need to be.</p>' . "\n";
						echo '<p>In other words, Customers will NOT need to login, just to receive access to the Specific Post/Page they purchased access to. optimizeMember will immediately redirect the Customer to the Specific Post/Page after checkout is completed successfully. An email is also sent to the Customer with a link ( see: <code>optimizeMember -> ccBill Options -> Specific Post/Page Email</code> ). Authentication is handled automatically through self-expiring links, good for 72 hours by default.</p>' . "\n";
						echo '<p>Specific Post/Page Access, is sort of like selling a product. Only, instead of shipping anything to the Customer, you just give them access to a specific Post/Page on your site; one that you created in WordPress. A Specific Post/Page that is protected by optimizeMember, might contain a download link for your eBook, access to file &amp; music downloads, access to additional support services, and the list goes on and on. The possibilities with this are endless; as long as your digital product can be delivered through access to a WordPress Post/Page that you\'ve created. To protect Specific Posts/Pages, please see: <code>optimizeMember -> Restriction Options -> Specific Post/Page Access</code>. Once you\'ve configured your Specific Post/Page Restrictions, those Posts/Pages will be available in the menus below.</p>' . "\n";
						echo '<p>Very simple. All you do is customize the form fields provided, for each Post/Page that you plan to sell. Then press (Generate Button Code). These ccBill Buttons are customized to work with optimizeMember seamlessly. You can even Package Additional Posts/Pages together into one transaction. <em>* Buttons are NOT saved here. This is only a Button Generator. Once you\'ve generated your Button, copy/paste it into your WordPress Editor, wherever you feel it would be most appropriate. If you lose your Button Code, you\'ll need to come back &amp; re-generate a new one.</em></p>' . "\n";
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<th class="ws-menu-page-th-side">' . "\n";
						echo '<label for="ws-plugin--optimizemember-pro-sp-shortcode">' . "\n";
						echo 'Button Code<br />Specific Posts/Pages:<br /><br />' . "\n";
						echo '<div id="ws-plugin--optimizemember-pro-sp-button-prev"></div>' . "\n";
						echo '</label>' . "\n";
						echo '</th>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<form onsubmit="return false;">' . "\n";
						/**/
						echo '<p><select id="ws-plugin--optimizemember-pro-sp-leading-id">' . "\n";
						echo '<option value="">&mdash; Select a Leading Post/Page that you\'ve protected &mdash;</option>' . "\n";
						/**/
						$ws_plugin__optimizemember_pro_temp_a_singulars = c_ws_plugin__optimizemember_utils_gets::get_all_singulars_with_sp ("exclude-conflicts");
						/**/
						foreach ($ws_plugin__optimizemember_pro_temp_a_singulars as $ws_plugin__optimizemember_pro_temp_o)
							echo '<option value="' . esc_attr ($ws_plugin__optimizemember_pro_temp_o->ID) . '">' . esc_html ($ws_plugin__optimizemember_pro_temp_o->post_title) . '</option>' . "\n";
						/**/
						echo '</select> <a href="#" onclick="alert(\'Required. The Leading Post/Page, is what your Customers will land on after checkout.\n\n*Tip* If there are no Posts/Pages in the menu, it\\\'s because you\\\'ve not configured optimizeMember for Specific Post/Page Access yet. See: optimizeMember -> Restriction Options -> Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";
						/**/
						echo '<p><select id="ws-plugin--optimizemember-pro-sp-additional-ids" multiple="multiple" style="height:100px;">' . "\n";
						echo '<optgroup label="&mdash; Package Additional Posts/Pages that you\'ve protected &mdash;">' . "\n";
						/**/
						foreach ($ws_plugin__optimizemember_pro_temp_a_singulars as $ws_plugin__optimizemember_pro_temp_o)
							echo '<option value="' . esc_attr ($ws_plugin__optimizemember_pro_temp_o->ID) . '">' . esc_html ($ws_plugin__optimizemember_pro_temp_o->post_title) . '</option>' . "\n";
						/**/
						echo '</optgroup></select> <a href="#" onclick="alert(\'Hold down your `Ctrl` key to select multiples.\\n\\nOptional. If you include Additional Posts/Pages, Customers will still land on your Leading Post/Page; BUT, they\\\'ll ALSO have access to some Additional Posts/Pages that you\\\'ve protected. This gives you the ability to create Post/Page Packages.\\n\\nIn other words, a Customer is sold a Specific Post/Page ( they\\\'ll land on your Leading Post/Page after checkout ), which might contain links to some other Posts/Pages that you\\\'ve packaged together under one transaction.\\n\\nBundling Additional Posts/Pages into one Package, authenticates the Customer for access to the Additional Posts/Pages automatically ( e.g. only one Access Link is needed, and optimizeMember generates this automatically ). However, you will STILL need to design your Leading Post/Page ( which is what a Customer will actually land on ), with links pointing to the other Posts/Pages. This way your Customers will have clickable links to everything they\\\'ve paid for.\\n\\n*Quick Summary* optimizeMember sends Customers to your Leading Post/Page, and also authenticates them for access to any Additional Posts/Pages automatically. You handle it from there.\\n\\n*Tip* If there are no Posts/Pages in this menu, it\\\'s because you\\\'ve not configured optimizeMember for Specific Post/Page Access yet. See: optimizeMember -> Restriction Options -> Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";
						/**/
						echo '<p>I want to charge: $<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-sp-amount" value="2.95" size="4" /> / <select id="ws-plugin--optimizemember-pro-sp-hours">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-sp-hours.php"))) . '</select></p>' . "\n";
						echo '<p>Description: <input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-sp-desc" value="Description and pricing details here." size="68" /></p>' . "\n";
						echo '<p>Currency: <select id="ws-plugin--optimizemember-pro-sp-currency">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-currencies.php"))) . '</select> <input type="button" value="Generate Button Code" onclick="ws_plugin__optimizemember_pro_ccbillSpButtonGenerate();" class="button-primary" /></p>' . "\n";
						echo '</form>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td colspan="2">' . "\n";
						echo '<form onsubmit="return false;">' . "\n";
						echo '<strong>WordPress Shortcode:</strong> ( recommended for both the WordPress Visual &amp; HTML Editors )<br />' . "\n";
						$ws_plugin__optimizemember_pro_temp_s = trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/ccbill-sp-checkout-button-shortcode.php")));
						$ws_plugin__optimizemember_pro_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__optimizemember_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__optimizemember_pro_temp_s);
						echo '<input type="text" autocomplete="off" id="ws-plugin--optimizemember-pro-sp-shortcode" value="' . format_to_edit ($ws_plugin__optimizemember_pro_temp_s) . '" onclick="this.select ();" style="font-family:Consolas, monospace; width:99%;" />' . "\n";
						echo '</form>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '<div class="ws-menu-page-group" title="ccBill Specific Post/Page Access Links">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-sp-links-section">' . "\n";
						echo '<h3>Specific Post/Page Link Generator ( for Customer Service )</h3>' . "\n";
						echo '<p>optimizeMember automatically generates Specific Post/Page Links for your Customers after checkout, and also sends them a link in a Confirmation Email. However, if you ever need to deal with a Customer Service issue that requires a new Specific Post/Page Link to be created manually, you can use this tool for that.</p>' . "\n";
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<form onsubmit="return false;">' . "\n";
						/**/
						echo '<p><select id="ws-plugin--optimizemember-pro-sp-link-leading-id">' . "\n";
						echo '<option value="">&mdash; Select a Leading Post/Page that you\'ve protected &mdash;</option>' . "\n";
						/**/
						$ws_plugin__optimizemember_pro_temp_a_singulars = c_ws_plugin__optimizemember_utils_gets::get_all_singulars_with_sp ("exclude-conflicts");
						/**/
						foreach ($ws_plugin__optimizemember_pro_temp_a_singulars as $ws_plugin__optimizemember_pro_temp_o)
							echo '<option value="' . esc_attr ($ws_plugin__optimizemember_pro_temp_o->ID) . '">' . esc_html ($ws_plugin__optimizemember_pro_temp_o->post_title) . '</option>' . "\n";
						/**/
						echo '</select> <a href="#" onclick="alert(\'Required. The Leading Post/Page, is what your Customers will land on after checkout.\n\n*Tip* If there are no Posts/Pages in the menu, it\\\'s because you\\\'ve not configured optimizeMember for Specific Post/Page Access yet. See: optimizeMember -> Restriction Options -> Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";
						/**/
						echo '<p><select id="ws-plugin--optimizemember-pro-sp-link-additional-ids" multiple="multiple" style="height:100px; min-width:450px;">' . "\n";
						echo '<optgroup label="&mdash; Package Additional Posts/Pages that you\'ve protected &mdash;">' . "\n";
						/**/
						foreach ($ws_plugin__optimizemember_pro_temp_a_singulars as $ws_plugin__optimizemember_pro_temp_o)
							echo '<option value="' . esc_attr ($ws_plugin__optimizemember_pro_temp_o->ID) . '">' . esc_html ($ws_plugin__optimizemember_pro_temp_o->post_title) . '</option>' . "\n";
						/**/
						echo '</optgroup></select> <a href="#" onclick="alert(\'Hold down your `Ctrl` key to select multiples.\\n\\nOptional. If you include Additional Posts/Pages, Customers will still land on your Leading Post/Page; BUT, they\\\'ll ALSO have access to some Additional Posts/Pages that you\\\'ve protected. This gives you the ability to create Post/Page Packages.\\n\\nIn other words, a Customer is sold a Specific Post/Page ( they\\\'ll land on your Leading Post/Page after checkout ), which might contain links to some other Posts/Pages that you\\\'ve packaged together under one transaction.\\n\\nBundling Additional Posts/Pages into one Package, authenticates the Customer for access to the Additional Posts/Pages automatically ( e.g. only one Access Link is needed, and optimizeMember generates this automatically ). However, you will STILL need to design your Leading Post/Page ( which is what a Customer will actually land on ), with links pointing to the other Posts/Pages. This way your Customers will have clickable links to everything they\\\'ve paid for.\\n\\n*Quick Summary* optimizeMember sends Customers to your Leading Post/Page, and also authenticates them for access to any Additional Posts/Pages automatically. You handle it from there.\\n\\n*Tip* If there are no Posts/Pages in this menu, it\\\'s because you\\\'ve not configured optimizeMember for Specific Post/Page Access yet. See: optimizeMember -> Restriction Options -> Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";
						/**/
						echo '<p><select id="ws-plugin--optimizemember-pro-sp-link-hours">' . trim (c_ws_plugin__optimizemember_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/ccbill-sp-hours.php"))) . '</select> <input type="button" value="Generate Access Link" onclick="ws_plugin__optimizemember_pro_ccbillSpLinkGenerate();" class="button-primary" /> <img id="ws-plugin--optimizemember-pro-sp-link-loading" src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]) . '/images/ajax-loader.gif" alt="" style="display:none;" /></p>' . "\n";
						echo '<p id="ws-plugin--optimizemember-pro-sp-link" style="font-family:Consolas, monospace; display:none;"></p>' . "\n";
						echo '</form>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '<div class="ws-menu-page-group" title="Shortcode Attributes ( Explained )">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-shortcode-attrs-section">' . "\n";
						echo '<h3>Shortcode Attributes ( Explained In Full Detail )</h3>' . "\n";
						echo '<p>When you generate a Button Code, optimizeMember will make a <a href="http://codex.wordpress.org/Shortcode_API#Overview" target="_blank" rel="external">Shortcode</a> available to you. Like most Shortcodes for WordPress, optimizeMember reads Attributes in your Shortcode. These Attributes will be pre-configured by one of optimizeMember\'s Button Generators automatically; so there really is nothing more you need to do. However, many site owners like to know exactly how these Shortcode Attributes work. Below, is a brief overview of each possible Shortcode Attribute.</p>' . "\n";
						/**/
						echo '<table class="form-table" style="margin-top:0;">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr style="padding-top:0;">' . "\n";
						/**/
						echo '<td style="padding-top:0;">' . "\n";
						echo '<ul>' . "\n";
						echo '<li><code>cancel="0"</code> Cancellation Button. Only valid w/ Membership Level Access. Possible values: <code>0</code> = this is NOT a Cancellation Button, <code>1</code> = this IS a Cancellation Button.</li>' . "\n";
						echo '<li><code>cc="USD"</code> 3 character Currency Code. Not valid when <code>modify|cancel="1"</code>.</li>' . "\n";
						echo (!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ()) ? '<li><code>ccaps="music,videos"</code> A comma-delimited list of Custom Capabilities. Only valid w/ Membership Level Access and/or Independent Custom Capabilities.</li>' . "\n" : '';
						echo '<li><code>custom="' . esc_html ($_SERVER["HTTP_HOST"]) . '"</code> must start with your domain. Additional values can be piped in ( ex: <code>custom="' . esc_html ($_SERVER["HTTP_HOST"]) . '|cv1|cv2|cv3|etc"</code> ). Not valid when <code>modify|cancel="1"</code>.</li>' . "\n";
						echo '<li><code>desc="Gold Membership"</code> A brief purchase Description. Not valid when <code>modify|cancel="1"</code>.</li>' . "\n";
						echo '<li><code>exp="72"</code> Access Expires ( in hours ). Only valid when <code>sp="1"</code> for Specific Post/Page Access.</li>' . "\n";
						echo '<li><code>form=""</code> Optional. This defaults to the ccBill dynamic pricing form, which you\'ve configured optimizeMember to use. However, if you are operating multiple dynamic pricing forms, this can be used to construct a payment button designed to integrate with a specific ccBill dynamic pricing form (e.g. making it possible to integrate optimizeMember with multiple ccBill payment forms, as they exist in your ccBill account). Please note that each ccBill form integrated with optimizeMember, must be created on the ccBill side first, and it MUST be created as a dynamic pricing form.</li>' . "\n";
						echo '<li><code>ids="14"</code> A Post/Page ID#, or a comma-delimited list of IDs. Only valid when <code>sp="1"</code> for Specific Post/Page Access.</li>' . "\n";
						echo '<li><code>image="default"</code> Button Image Location. Possible values: <code>default</code> = use the default ccBill Button, <code>http://...</code> = location of your custom Image.</li>' . "\n";
						echo '<li><code>level="1"</code> Membership Level [1-4] <em>( or, up to the number of configured Levels )</em>. Only valid for Buttons providing paid Membership Level Access.' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' Or, with Independent Custom Capabilities this MUST be set to <code>level="*"</code>, and <code>ccaps=""</code> must NOT be empty <em>( i.e. <code>level="*" ccaps="music,videos"</code> )</em>.') . '</li>' . "\n";
						echo '<li><code>modify="0"</code> Modification Button. Only valid w/ Membership Level Access. Possible values: <code>0</code> = this is NOT a Modification Button, <code>1</code> = this IS a Modification Button.</li>' . "\n";
						echo '<li><code>output="anchor"</code> Output Type. Possible values: <code>anchor</code> = ccBill Button (  &lt;a&gt; anchor tag ) URL w/ ?query string, <code>url</code> = raw URL w/ ?query string.</li>' . "\n";
						echo '<li><code>ra="2.95"</code> Regular, Buy Now, and/or Recurring Amount. Must be &gt;= <code>2.95</code> and &lt;= <code>100.00</code>. ccBill will NOT allow amounts larger than <code>100.00</code>. Not valid when <code>modify|cancel="1"</code>.</li>' . "\n";
						echo '<li><code>rp="1"</code> Regular Period. Only valid w/ Membership Level Access' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' and/or Independent Custom Capabilities') . '. Must be &gt;= <code>1</code> ( ex: <code>1</code> Week, <code>2</code> Months, <code>1</code> Month, <code>3</code> Days ). ccBill does NOT allow a Regular Billing Period to be &lt; <code>2</code> days, or &gt; <code>1</code> year. When <code>rr="1"</code> ( recurring ), ccBill ONLY allows monthly, bi-monthly, and quarterly billing. So, for Recurring Subscriptions, you must use one of these combinations: <code>rp="1" rt="M"</code> or <code>rp="2" rt="M"</code> or <code>rp="3" rt="M"</code>.</li>' . "\n";
						echo '<li><code>rt="M"</code> Regular Term. Only valid w/ Membership Level Access' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' and/or Independent Custom Capabilities') . '. Possible values: <code>D</code> = Days, <code>W</code> = Weeks, <code>M</code> = Months, <code>Y</code> = Years, <code>L</code> = Lifetime. ccBill does NOT allow a Regular Billing Period to be &lt; <code>2 days</code>, or &gt; <code>1 year</code>. When <code>rr="1"</code> ( recurring ), ccBill ONLY allows monthly, bi-monthly, and quarterly billing. So, for Recurring Subscriptions, you must use one of these combinations: <code>rp="1" rt="M"</code> or <code>rp="2" rt="M"</code> or <code>rp="3" rt="M"</code>.</li>' . "\n";
						echo '<li><code>rr="1"</code> Recurring directive. Only valid w/ Membership Level Access' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' and/or Independent Custom Capabilities') . '. Possible values: <code>0</code> = Non-Recurring / Buy Now ( no Initial/Trial Period possible ); <code>1</code> = Recurring Subscription, with possible Initial/Trial Period at a different Trial Amount. ccBill does NOT allow a 100% free Trial; you have to charge at least <code>2.95</code>. With ccBill, Initial/Trial Periods are NOT possible unless <code>rr="1"</code> ( recurring ).</li>' . "\n";
						echo '<li><code>sp="0"</code> Specific Post/Page Button. Possible values: <code>0</code> = this is NOT a Specific Post/Page Access Button, <code>1</code> = this IS a Specific Post/Page Access Button.</li>' . "\n";
						echo '<li><code>sub_account=""</code> Optional. This defaults to the ccBill sub-account which you\'ve configured optimizeMember to use. However, if you are operating multiple ccBill sub-accounts, this can be used to construct a payment button designed to integrate with a specific ccBill sub-account (e.g. making it possible to integrate optimizeMember with multiple ccBill sub-accounts, all from a single optimizeMember installation).</li>' . "\n";
						echo '<li><code>ta="0.00"</code> Trial Amount. Only valid w/ Membership Level Access; and only when <code>rr="1"</code> ( recurring ). When provided, must be &gt;= <code>2.95</code> and &lt;= <code>100.00</code>.</li>' . "\n";
						echo '<li><code>tp="0"</code> Trial Period. Only valid w/ Membership Level Access; and only when <code>rr="1"</code> ( recurring ). When provided, must &gt;= <code>1</code>. ccBill will NOT allow Initial/Trial Periods longer than <code>365</code> days.</li>' . "\n";
						echo '<li><code>tt="D"</code> Trial Term. Only valid w/ Membership Level Access; and only when <code>rr="1"</code> ( recurring ). Possible values: <code>D</code> = Days, <code>W</code> = Weeks, <code>M</code> = Months, <code>Y</code> = Years. My NOT exceed <code>365 days</code>.</li>' . "\n";
						echo '</ul>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '<div class="ws-menu-page-group" title="s2_ Vars ( Explained )">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-s2-vars-section">' . "\n";
						echo '<h3>s2_ Variables ( Explained In Full Detail )</h3>' . "\n";
						echo '<p>When you generate a Button Code, optimizeMember will make a Shortcode available to you. Ultimately, your Shortcode produces a clickable link that will send Customers over to ccBill where they\'ll complete the checkout process. You may notice that optimizeMember will attach several s2_ Variables to the link it generates. These s2_ Variables will be pre-configured by one of optimizeMember\'s Button Generators automatically; so there really is nothing more you need to do. However, many site owners like to know exactly how these s2_ Variables work. Below, is a brief overview of each possible s2_ Variable.</p>' . "\n";
						/**/
						echo '<table class="form-table" style="margin-top:0;">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr style="padding-top:0;">' . "\n";
						/**/
						echo '<td style="padding-top:0;">' . "\n";
						echo '<ul>' . "\n";
						echo '<li><code>&amp;s2_custom=' . esc_html ($_SERVER["HTTP_HOST"]) . '</code> The domain of your site, which is passed through the `custom` attribute in your Shortcode. You can pipe in additional values if you like ( ex: <code><em>' . esc_html ($_SERVER["HTTP_HOST"]) . '|cv1|cv2|cv3</em></code> ).</li>' . "\n";
						echo '<li><code>&amp;s2_customer_ip=' . esc_html (OPTIMIZEMEMBER_CURRENT_USER_IP) . '</code> This is the Customer\'s IP Address via <code>$_SERVER["REMOTE_ADDR"]</code>. optimizeMember will store the Customer\'s IP Address in case you need it for tracking and/or affiliate program integration <em>( optional )</em>.</li>' . "\n";
						echo '<li><code>&amp;s2_desc=Brief Description</code> Description optimizeMember uses for a ccBill purchase. This can/could be different from what you have on file at ccBill for a Dynamic Pricing Form.</li>' . "\n";
						echo '<li><code>&amp;s2_invoice=1:music,videos:2 D</code> optimizeMember\'s Item Number for Membership Level Access ( colon separated <code><em>level:custom_capabilities:fixed term</em></code> ) that the Subscription is for.' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' Or, with Independent Custom Capabilities this MUST have <code>level</code> set to <code>*</code> and the <code>custom_capabilities</code> section must NOT be empty <em>( i.e. <code>*:music,videos</code> )</em>.') . '</li>' . "\n";
						echo '<li><code>&amp;s2_invoice=sp:13,24,36:72</code> optimizeMember\'s Item Number for Specific Post/Page Access ( translates to: <code><em>sp:comma-delimited IDs:expiration hours</em></code> ).</li>' . "\n";
						echo '<li><code>&amp;s2_p1=0 D</code> For Subscriptions, this tells optimizeMember\'s DataLink/Background Post handlers how many Initial/Trial Days are in a Customer\'s ccBill Subscription configuration.</li>' . "\n";
						echo '<li><code>&amp;s2_p3=1 M</code> For Subscriptions, this tells optimizeMember\'s DataLink/Background Post handlers the Regular Billing Period of a Customer\'s ccBill Subscription configuration.</li>' . "\n";
						echo '<li><code>&amp;s2_referencing=54825</code> If a Member is logged-in when they click a Checkout Button, optimizeMember will tag this onto the end of the link so it can update a Customer\'s account instead of asking them to re-register after checkout. The value of this parameter will be set to the Member\'s current Paid Subscr. ID, as stored by optimizeMember in a previous purchase. If the Customer is NOT a paid Member ( i.e. they\'re still a Free Subscriber ), optimizeMember will grab the User\'s ID# in your WordPress database instead.</li>' . "\n";
						echo '</ul>' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						echo '</td>' . "\n";
						/**/
						echo '<td class="ws-menu-page-table-r">' . "\n";
						c_ws_plugin__optimizemember_menu_pages_rs::display ();
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						/**/
						echo '</div>' . "\n";
						echo '</div>' . "\n";
					}
			}
	}
/**/
new c_ws_plugin__optimizemember_pro_menu_page_ccbill_buttons ();
?>