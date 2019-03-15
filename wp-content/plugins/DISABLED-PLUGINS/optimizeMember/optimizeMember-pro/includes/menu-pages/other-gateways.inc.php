<?php
/**
* Menu page for optimizeMember Pro ( Other Gateways page ).
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
if (!class_exists ("c_ws_plugin__optimizemember_pro_menu_page_other_gateways"))
	{
		/**
		* Menu page for optimizeMember Pro ( Other Gateways page ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__optimizemember_pro_menu_page_other_gateways
			{
				public function __construct ()
					{
						echo '<div class="wrap ws-menu-page op-bsw-wizard op-bsw-content">' . "\n";
						/**/
						echo '<div class="op-bsw-header">';
							echo '<div class="op-logo"><img src="' . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images/" . 'logo-optimizepress.png" alt="OptimizePress" height="50" class="animated flipInY"></div>';
						echo '</div>';
						echo '<div class="op-bsw-main-content">';
						echo '<h2>Other Payment Gateways</h2>' . "\n";
						/**/
						echo '<table class="ws-menu-page-table">' . "\n";
						echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
						echo '<tr class="ws-menu-page-table-tr">' . "\n";
						echo '<td class="ws-menu-page-table-l">' . "\n";
						/**/
						echo '<form method="post" name="ws_plugin__optimizemember_pro_options_form" id="ws-plugin--optimizemember-pro-options-form">' . "\n";
						echo '<input type="hidden" name="ws_plugin__optimizemember_options_save" id="ws-plugin--optimizemember-options-save" value="' . esc_attr (wp_create_nonce ("ws-plugin--optimizemember-options-save")) . '" />' . "\n";
						/**/
						echo '<div class="ws-menu-page-group" title="Other Payment Gateways ( optional )" default-state="open">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-pro-other-gateways-section">' . "\n";
						echo '<h3>Other Payment Gateways ( enable / disable )</h3>' . "\n";
						echo '<p>optimizeMember recommends PayPal Pro as the best available option for payment processing. However, optimizeMember has also been integrated with the additional Payment Gateways listed below. If you wish to take advantage of these additional Gateway integrations, you must enable them explicitly from this page. Once enabled, options will become available in your optimizeMember Menu on the left-hand side. optimizeMember has the ability to operate with as many Gateways integrations as you like. If you\'d like to use them all, you can! Just remember, for each Payment Gateway that you integrate, you must configure the options for that Gateway, and you must use optimizeMember\'s Button Generator to create WordPress Shortcodes that go into your Membership Options Page <em>( aka: your Signup Page )</em>.</p>' . "\n";
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<div class="ws-menu-page-scrollbox" style="height:250px;">' . "\n";
						echo '<input type="hidden" name="ws_plugin__optimizemember_pro_gateways_enabled[]" value="update-signal" />' . "\n";
						foreach (c_ws_plugin__optimizemember_pro_gateways::available_gateways () as $ws_plugin__optimizemember_temp_s_key => $ws_plugin__optimizemember_temp_s_val)
							echo '<input type="checkbox" name="ws_plugin__optimizemember_pro_gateways_enabled[]" id="ws-plugin--optimizemember-pro-gateways-enabled-' . esc_attr ($ws_plugin__optimizemember_temp_s_key) . '" value="' . esc_attr ($ws_plugin__optimizemember_temp_s_key) . '"' . ((in_array ($ws_plugin__optimizemember_temp_s_key, $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_gateways_enabled"])) ? ' checked="checked"' : '') . ' /> <label for="ws-plugin--optimizemember-pro-gateways-enabled-' . esc_attr ($ws_plugin__optimizemember_temp_s_key) . '">' . $ws_plugin__optimizemember_temp_s_val . '</label><br /><br />' . "\n";
						echo '<input type="checkbox" checked="checked" disabled="disabled" /> <label><strong>PayPal Website Payments Standard</strong> <em>( w/ Buttons )</em><br />&uarr; supports Buy Now &amp; Recurring. ( core / always on )</label>' . "\n";
						echo '</div>' . "\n";
						echo 'Enable/disable Payment Gateways integrated with optimizeMember Pro.' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
						/**/
						// echo '<div class="ws-menu-page-hr"></div>' . "\n";
						/**/
						echo '<p class="submit"><input type="submit" class="op-pb-button green" value="Save Changes, ( then refresh )" /></p>' . "\n";
						/**/
						echo '</form>' . "\n";
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
new c_ws_plugin__optimizemember_pro_menu_page_other_gateways ();
?>