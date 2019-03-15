<?php
/**
* Menu page for optimizeMember Pro ( PayPal option detail rows ).
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
if (!class_exists ("c_ws_plugin__optimizemember_pro_menu_page_paypal_ops_detail_rows"))
	{
		/**
		* Menu page for optimizeMember Pro ( PayPal option detail rows ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__optimizemember_pro_menu_page_paypal_ops_detail_rows
			{
				public function __construct ()
					{
						echo '<tr>' . "\n";
						/**/
						echo '<th>' . "\n";
						echo '<label for="ws-plugin--optimizemember-pro-paypal-checkout-rdp">' . "\n";
						echo 'PayPal Pro Forms / Recurring Profile Behavior:<br />' . "\n";
						echo '( only affects first payment of Recurring Profiles )<br />' . "\n";
						echo '</label>' . "\n";
						echo '</th>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<select name="ws_plugin__optimizemember_pro_paypal_checkout_rdp" id="ws-plugin--optimizemember-pro-paypal-checkout-rdp">' . "\n";
						echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_paypal_checkout_rdp"]) ? ' selected="selected"' : '') . '>Consolidate w/ Recurring Profile ( 1st payment charged immediately )</option>' . "\n";
						echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["pro_paypal_checkout_rdp"]) ? ' selected="selected"' : '') . '>Real-Time / Direct Pay ( 1st payment charged immediately, in real-time )</option>' . "\n";
						echo '</select><br />' . "\n";
						echo 'Applies only to "Subscriptions" ( aka: Recurring Profiles ). [ <a href="#" onclick="alert(\'This setting is ignored when/if you offer a Free Trial Period. This setting only affects PayPal Pro Forms that use a `Subscription` configuration with an immediate charge. In other words, if your PayPal Pro Form is configured to bill on a recurring basis ( starting the day of signup ), this setting controls the way in which optimizeMember handles the first payment of the Customer\\\'s Paid Subscription ( aka: Recurring Profile ).\\n\\nWe recommend: `Consolidate w/ Recurring Profile`, because this keeps all charges associated with a particular Customer organized in your PayPal account. No matter which option you choose, a first Initial Payment ( when applicable ), will always be charged immediately. However, in cases where it is critical that a Customer NOT gain access until their first payment has been fully captured, choose: `Real-Time / Direct Pay`, which tells optimizeMember to authorize/capture the first payment in real-time during checkout, instead of consolidating it into the Recurring Profile.\\n\\nHere Is A Breakdown For You\\n\\n— Consolidate w/ Recurring Profile —\\noptimizeMember creates a Recurring Profile with an Initial Payment amount, to be charged immediately. PayPal generates the Recurring Profile, returns a successful response to optimizeMember and the Customer gains access. Moments later ( usually within 30 seconds ), PayPal will authorize/capture the first payment. If the first payment is declined, optimizeMember will revoke the Customer\\\'s access immediately.\\n\\n— Real-Time / Direct Pay ( mission critical ) —\\noptimizeMember charges the first payment separately ( in real-time during checkout ), leaving no possibility for the Customer to gain access until the first charge is fully captured. A Recurring Profile is also generated, which handles any future billing. You will have two billing records in your PayPal account. One for the Initial Payment, and another for the Recurring Profile.\'); return false;">full details</a> ]' . "\n";
						echo '</td>' . "\n";
						/**/
						echo '</tr>' . "\n";
					}
			}
	}
/**/
new c_ws_plugin__optimizemember_pro_menu_page_paypal_ops_detail_rows ();
?>