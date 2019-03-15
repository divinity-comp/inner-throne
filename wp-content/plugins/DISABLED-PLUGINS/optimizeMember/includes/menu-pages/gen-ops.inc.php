<?php
/**
* Menu page for the optimizeMember plugin ( General Options page ).
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
* @package optimizeMember\Menu_Pages
* @since 3.0
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__optimizemember_menu_page_gen_ops"))
	{
		/**
		* Menu page for the optimizeMember plugin ( General Options page ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__optimizemember_menu_page_gen_ops
		{
			public function __construct ()
			{
				echo '<div class="wrap ws-menu-page op-bsw-wizard op-bsw-content">' . "\n";
				/**/
				echo '<div class="op-bsw-header">';
					echo '<div class="op-logo"><img src="' . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images/" . 'logo-optimizepress.png" alt="OptimizePress" height="50" class="animated flipInY"></div>';
				echo '</div>';
				echo '<div class="op-bsw-main-content">';
				echo '<h2>General Options</h2>' . "\n";
				/**/
				echo '<table class="ws-menu-page-table">' . "\n";
				echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
				echo '<tr class="ws-menu-page-table-tr">' . "\n";
				echo '<td class="ws-menu-page-table-l">' . "\n";
				/**/
				echo '<form method="post" name="ws_plugin__optimizemember_options_form" id="ws-plugin--optimizemember-options-form">' . "\n";
				echo '<input type="hidden" name="ws_plugin__optimizemember_options_save" id="ws-plugin--optimizemember-options-save" value="' . esc_attr (wp_create_nonce ("ws-plugin--optimizemember-options-save")) . '" />' . "\n";
				echo '<input type="hidden" name="ws_plugin__optimizemember_configured" id="ws-plugin--optimizemember-configured" value="1" />' . "\n";
				/**/
				do_action ("ws_plugin__optimizemember_during_gen_ops_page_before_left_sections", get_defined_vars ());

				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_membership_levels", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_membership_levels", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Define Membership Levels And Packages">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-membership-levels-section">' . "\n";
					echo '<h3>Membership Levels and Packages ( required, please customize these )</h3>' . "\n";
					echo '<p>The default Membership Levels are labeled generically; feel free to modify them as needed. optimizeMember supports Free Subscribers <em>( at Level #0 )</em>, along with several Primary Roles for paid Membership <em>( i.e. Levels 1-10 )</em>, created by the OptimizeMember plugin. OptimizeMember also supports unlimited Custom Capability Packages <em>( see <code>optimizeMember -> API Scripting -> Custom Capabilities</code> )</em>. That being said, you don\'t have to use all of the Membership Levels if you don\'t want to. To use only 1 or 2 of these Levels, just design your Membership Options / Redirect Page, so it only includes Payment Buttons for the Levels being used.</p>' . "\n";
					/**do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_membership_levels", get_defined_vars ());**/
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					/**/
					for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
						{
							$labelText = ws_plugin__optimizemember_getMembershipLabel($n);
							echo '<tr>' . "\n";
							/**/
							echo '<th>' . "\n";
							echo '<label for="ws-plugin--optimizemember-level' . $n . '-label">' . "\n";
							echo ($n === 0) ? 'Level #' . $n . ' <em>( Free Subscribers )</em>:' . "\n" : 'Level #' . $n . ' Members:' . "\n";
							echo '</label>' . "\n";
							echo '</th>' . "\n";
							/**/
							echo '</tr>' . "\n";
							echo '<tr>' . "\n";
							/**/
							echo '<td>' . "\n";
							echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_level' . $n . '_label" id="ws-plugin--optimizemember-level' . $n . '-label" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_label"]) . '" /><br />' . "\n";
							echo 'This is the Label for Level #' . $n . (($n === 0) ? ' ( Free Subscribers )' : ' Members') . '.<br />' . "\n";
							echo '</td>' . "\n";
							/**/
							echo '</tr>' . "\n";
						}
					/**/
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					/**/
					echo '<div class="ws-menu-page-hr"></div>' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-membership-levels-section">' . "\n";
					echo '<h3>Packages</h3>' . "\n";
					echo '<p>Packages are a great way to protect individual products or content.  You can protect any content to a particular package by using the admin options on the page and this will ensure only users with that package added to their account will be given access</p>' . "\n";
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th style="padding-top:0;">' . "\n";
					echo '<label for="ws-plugin--optimizemember-ccp">' . "\n";
					echo 'Add new package:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<input type="hidden" name="ws_plugin__optimizemember_ccp[]" value="update-signal" id="optimizemember_ccp_signal" />'."\n";
					echo '<input type="hidden" name="ws_plugin__optimizemember_ccp[]" value="" id="optimizemember_ccp_signal_value" />'."\n";
					echo '<td>' . "\n";
					echo '<input type="text" onblur="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" name="ws_plugin__optimizemember_ccp[]" id="ws-plugin--optimizemember-ccp" value="" />';
					if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
						echo '<h3>Available packages</h3>' . "\n";
                        echo '<table>';
						foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
							if (!empty($val)) {
                                echo '<tr>';
								echo '<input type="hidden" name="ws_plugin__optimizemember_ccp[]" value="'.$val.'" />' . "\n";
								echo '<td>' . $val . '</td>';
                                echo '<td><a style="background: none repeat scroll 0 0 #b20000 !important; color: #fff !important;box-shadow:none;text-shadow:none;border-color:#790000;margin-top:5px;" class="button" onClick="jQuery(\'#optimizemember_ccp_signal\').val(\'remove-signal\');jQuery(\'#optimizemember_ccp_signal_value\').val(\''.$val.'\'); jQuery( \'#ws-plugin--optimizemember-options-form\' ).submit();"> Delete this package</a></td>'."\n";
                                echo '</tr>';
							}
						}
                        echo '</table>';
					}

					//echo 'This affects your administrative Dashboard only <em>( i.e. your list of Users )</em>.<br />optimizeMember can force WordPress to use your Labels instead of referencing Roles by `optimizeMember Level #`. If this is your first installation of optimizeMember, we suggest leaving this set to <code>no</code> until you\'ve had a chance to get acclimated with optimizeMember\'s functionality. In fact, many site owners choose to leave this off, because they find it less confusing when Roles are referred to by their optimizeMember Level #.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '<table class="form-table" style="margin-top:0;display:none;">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";

					/**/
					echo '<th style="padding-top:0;">' . "\n";
					echo '<label for="ws-plugin--optimizemember-apply-label-translations">' . "\n";
					echo 'Force WordPress to use your Labels?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="radio" name="ws_plugin__optimizemember_apply_label_translations" id="ws-plugin--optimizemember-apply-label-translations-" value="1" checked="checked" />';
					//echo '<input type="radio" name="ws_plugin__optimizemember_apply_label_translations" id="ws-plugin--optimizemember-apply-label-translations-0" value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["apply_label_translations"]) ? ' checked="checked"' : '') . ' /> <label for="ws-plugin--optimizemember-apply-label-translations-0">No</label> &nbsp;&nbsp;&nbsp; <input type="radio" name="ws_plugin__optimizemember_apply_label_translations" id="ws-plugin--optimizemember-apply-label-translations-1" value="1"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["apply_label_translations"]) ? ' checked="checked"' : '') . ' /> <label for="ws-plugin--optimizemember-apply-label-translations-1">Yes, force WordPress to use my Labels.</label><br />' . "\n";
					echo 'This affects your administrative Dashboard only <em>( i.e. your list of Users )</em>.<br />optimizeMember can force WordPress to use your Labels instead of referencing Roles by `optimizeMember Level #`. If this is your first installation of optimizeMember, we suggest leaving this set to <code>no</code> until you\'ve had a chance to get acclimated with optimizeMember\'s functionality. In fact, many site owners choose to leave this off, because they find it less confusing when Roles are referred to by their optimizeMember Level #.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '<div class="ws-menu-page-hr"></div>' . "\n";
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-filter-wp-query">' . "\n";
					echo 'Hide links?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					//echo '<div class="ws-menu-page-scrollbox" style="height:105px;">' . "\n";
					echo '<input type="hidden" name="ws_plugin__optimizemember_filter_wp_query[]" value="update-signal" />' . "\n";
					foreach (array ("all" => "<strong>Hide links to protected content for non-members</strong>") as $ws_plugin__optimizemember_temp_s_value => $ws_plugin__optimizemember_temp_s_label) {
						echo '<input type="checkbox" name="ws_plugin__optimizemember_filter_wp_query[]" id="ws-plugin--optimizemember-filter-wp-query-' . esc_attr (preg_replace ("/[^a-z0-9_\-]/", "-", $ws_plugin__optimizemember_temp_s_value)) . '" value="' . esc_attr ($ws_plugin__optimizemember_temp_s_value) . '"' . ((in_array ($ws_plugin__optimizemember_temp_s_value, $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["filter_wp_query"])) ? ' checked="checked"' : '') . ' /> <label for="ws-plugin--optimizemember-filter-wp-query-' . esc_attr (preg_replace ("/[^a-z0-9_\-]/", "-", $ws_plugin__optimizemember_temp_s_value)) . '">' . $ws_plugin__optimizemember_temp_s_label . '</label><br />' . "\n";
						break;
					}
					echo 'This option will hide links to any protected content for members who do not have access to that content<br />' . "\n";

					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					/**/
					echo '<div class="ws-menu-page-hr"></div>' . "\n";
					/**/
					echo '<input type="button" value="Reset Roles/Capabilities" class="ws-menu-page-right ws-plugin--optimizemember-reset-roles-button button" style="min-width:175px;" />' . "\n";
					echo '<p>The button to the right, is a nifty tool, which allows you to reset optimizeMember\'s internal Roles and Capabilities that integrate with WordPress. If you, or a developer working with you, has made attempts to alter the default <em>internal</em> Role/Capability sets that come with optimizeMember, and you need to reset them back to the way optimizeMember expects them to be, please use this tool. </p>' . "\n";
					/**/
					// echo '<p class="submit"><input id="opmNewSave" type="submit" class="op-pb-button green" value="Save Changes" /></p>' . "\n";
					// echo '<p id="msg"><p>';
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_membership_levels", get_defined_vars ());
				}

				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_list_server_integration", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_list_server_integration", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Define Mailing List/Autoresponder Configurations">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-list-server-integration-section">' . "\n";
					echo '<h3>Mailing List/Autoresponder Configurations</h3>' . "\n";
					echo '<p>To enable and configure email marketing service go to <a href="' . admin_url() . 'admin.php?page=optimizepress-dashboard#email_marketing_services">OP theme/plugin Dashboard settings</a></p>' . "\n";
					echo '<p>Note: Lists/follow upsequences can take up to 1 minute to be retrieved from your provider.</p>' . "\n";
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_list_server_integration", get_defined_vars ());
					/**/
					$providers = op_assets_provider_list();
					$providersDetails = array();
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					/**/
					for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
						{
							$labelText = ws_plugin__optimizemember_getMembershipLabel($n);
							$provider = format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_provider"]);
							echo '<tr>' . "\n";
							/**/
							echo '<th>' . "\n";
							echo '<h4>' . (($n === 0) ? 'Level #' . $n . ' <em>(Free Subscribers)</em>' . "\n" : 'Level #' . $n . ' Members') . '</h4>' . "\n";
							echo '</th>' . "\n";
							echo '</tr>' . "\n";
							/**/
							echo '<tr>' . "\n";
							/**/
							echo '<th>' . "\n";
							echo '<label for="ws_plugin__optimizemember_level' . $n . '_provider" id="ws_plugin__optimizemember_level' . $n . '_provider_label">' . "\n";
							echo 'List Provider:' . "\n";
							echo '</label>' . "\n";
							echo '</th>' . "\n";
							echo '<th>' . "\n";
							echo '<label for="ws_plugin__optimizemember_level' . $n . '_list" id="ws_plugin__optimizemember_level' . $n . '_list_label">' . "\n";
							if ($provider === 'infusionsoft') {
								echo 'Follow up sequence:' . "\n";
							} else if ($provider === 'arpreach') {
								echo 'Autoresponder:' . "\n";
							} else {
								echo 'List:' . "\n";
							}
							echo '</label>' . "\n";
							echo '</th>' . "\n";
							/**/
							echo '</tr>' . "\n";
							echo '<tr>' . "\n";
							/**/
							echo '<td>' . "\n";
							echo '<select data-level="' . $n . '" name="ws_plugin__optimizemember_level' . $n . '_provider" id="ws_plugin__optimizemember_level' . $n . '_provider" class="ws_plugin__optimizemember_level_provider">' . "\n";
							echo '<option value="0">Select provider</option>' . "\n";
							if (count($providers) > 0) {
								foreach ($providers as $providerId => $providerName) {
									/*
									 * Subscribing is happening on their URL and can't be done in background
									 */
									if ($providerId === 'oneshoppingcart') {
										continue;
									}
									echo '<option value="' . $providerId .'"' . selected($providerId, $provider, false) . '>' . $providerName . '</option>' . "\n";
								}
							}
							echo '</select><br />' . "\n";

							echo '</td>' . "\n";
							/**/
							echo '<td>' . "\n";

							if ($provider === 'arpreach') {
								echo '<input type="text" data-level="' . $n . '" value="' . format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_list"]) . '" name="ws_plugin__optimizemember_level' . $n . '_list" id="ws_plugin__optimizemember_level' . $n . '_list" class="ws_plugin__optimizemember_level_list">' . "\n";
							} else {
								echo '<select data-level="' . $n . '" name="ws_plugin__optimizemember_level' . $n . '_list" id="ws_plugin__optimizemember_level' . $n . '_list" class="ws_plugin__optimizemember_level_list">' . "\n";
								$provider = format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_provider"]);
								if (!empty($provider)) {
									$list = format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level" . $n . "_list"]);
									if (isset($providersDetails[$provider])) {
										$details = $providersDetails[$provider];
									} else {
										$details = op_assets_provider_items($provider, true);
										if (count($details) > 0) {
											$details = $details['lists'];
											$providersDetails[$provider] = $details;
										}
									}
									foreach ($details as $listId => $item) {
										echo '<option value="' . $listId . '"' . selected($list, $listId, false) . '>' . $item['name'] . '</option>' . "\n";
									}

								} else {
									echo '<option value="0">Select provider first</option>' . "\n";
								}
								echo '</select><br />' . "\n";
							}
							echo '</td>' . "\n";
							/**/
							echo '</tr>' . "\n";
						}
					/**/
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					/**/
					// echo '<p class="submit"><input id="opmNewSave" type="submit" class="op-pb-button green" value="Save Changes" /></p>' . "\n";
					// echo '<p class="save_info"><p>';
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_list_server_integration", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_membership_options_page", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_membership_options_page", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Membership Options / Redirect Page">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-membership-options-page-section">' . "\n";
					echo '<h3>Membership Options / Redirect Page ( required, please customize this )</h3>' . "\n";
					echo '<p>This is where visitors who do not have access to your restricted membership content will be redirected to.  We recommend using this page either as a login page, or as a page where you offer promote your membership or product.</p>' . "\n";
					echo '<p>If you want to add a login form on your page, use the "Membership Login Form" element in the Liveeditor to add a login form for your visitors to login</p>' . "\n";
					//echo '<p><em><strong>*Tip*</strong> If you allow Open Registration ( i.e. Free Subscribers ), you might want to place a link on your Membership Options / Redirect Page, which points directly to your Registration Form, instead of routing a Customer through your Payment Gateway first. For further details, please check the section above: <code>optimizeMember -> General Options -> Open Registration</code>.</em></p>' . "\n";
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<p><em><strong>BuddyPress:</strong> Even with BuddyPress, optimizeMember still needs a Membership Options / Redirect Page. This is where your Payment Button(s) will go, giving people the ability to pay you. And again, this is also the Page that anyone could be redirected to <em>( by optimizeMember )</em>, should they attempt to access an area of your site, which may require access to something they are currenty NOT allowed to view.' . ((!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ()) ? ' For more on this advanced topic, please check your Dashboard here: <code>optimizeMember -> API Scripting -> Membership Options / Redirect Page / Variables</code>.' : '') . '</em></p>' . "\n" : '';
					echo '<p><em><strong>*Tip*</strong> optimizeMember will NEVER allow this Page to be protected from public access.</em></p>' . "\n";
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_membership_options_page", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-membership-options-page">' . "\n";
					echo 'Membership Options / Redirect Page:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_membership_options_page" id="ws-plugin--optimizemember-membership-options-page">' . "\n";
					echo '<option value="">&mdash; Select &mdash;</option>' . "\n";
					foreach (($ws_plugin__optimizemember_temp_a = array_merge ((array)get_pages ())) as $ws_plugin__optimizemember_temp_o)
						echo '<option value="' . esc_attr ($ws_plugin__optimizemember_temp_o->ID) . '"' . (($ws_plugin__optimizemember_temp_o->ID == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"]) ? ' selected="selected"' : '') . '>' . esc_html ($ws_plugin__optimizemember_temp_o->post_title) . '</option>' . "\n";
					echo '</select><br />' . "\n";
					echo 'Select a page which your users will be redirected to when they try to access content they\'re not currently allowed to view.  We recommend having a login form on this page or details of your membership or product' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_membership_options_page", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_login_welcome_page", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_login_welcome_page", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Members Home Page / Login Welcome Page">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-login-welcome-page-section">' . "\n";
					echo '<h3>Members Home Page / Login Welcome Page ( required, please customize this )</h3>' . "\n";
					echo '<p>Please create and/or choose an existing Page to use as the first page Members will see after logging in.</p>' . "\n";
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<p><em><strong>BuddyPress:</strong> optimizeMember integrates with BuddyPress. Your Login Welcome Page affects BuddyPress too.</em></p>' . "\n" : '';
					echo '<p><em><strong>*Tip*</strong> This special Page will be protected from public access ( automatically ) by optimizeMember.</em></p>' . "\n";
					//echo '<p><strong>See also:</strong> This KB article: <a href="http://help.optimizepress.com/customer/portal/articles/1281577-customizing-your-login-welcome-page" target="_blank" rel="external">Customizing Your Login Welcome Page</a>.</p>'."\n";
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_login_welcome_page", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-welcome-page">' . "\n";
					echo 'Members Home Page / Login Welcome Page:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_login_welcome_page" id="ws-plugin--optimizemember-login-welcome-page">' . "\n";
					echo '<option value="">&mdash; Select &mdash;</option>' . "\n";
					foreach (($ws_plugin__optimizemember_temp_a = array_merge ((array)get_pages ())) as $ws_plugin__optimizemember_temp_o)
						echo '<option value="' . esc_attr ($ws_plugin__optimizemember_temp_o->ID) . '"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_redirection_override"] && $ws_plugin__optimizemember_temp_o->ID == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_welcome_page"]) ? ' selected="selected"' : '') . '>' . esc_html ($ws_plugin__optimizemember_temp_o->post_title) . '</option>' . "\n";
					echo '</select><br />' . "\n";
					echo 'Please choose a Page to be used as the first page Members will see after logging in. This Page can contain anything you like. We recommend the following title: <code>Welcome To Our Members Area</code>.<br /><br />' . "\n";
					echo '&darr; Or, you may configure a Special Redirection URL, if you prefer. You\'ll need to type in the full URL, starting with: <code>http://</code>. <em>A few <a href="#" onclick="alert(\'Replacement Codes:\\n\\n%%current_user_login%% = The current User\\\'s Username, lowercase.\\n%%current_user_id%% = The current User\\\'s ID.\\n%%current_user_level%% = The current User\\\'s optimizeMember Level.\\n%%current_user_role%% = The current User\\\'s WordPress Role.' . ((!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ()) ? '\\n%%current_user_ccaps%% = The current User\\\'s Custom Capabilities.' : '') . '\\n%%current_user_logins%% = Number of times the current User has logged in.\\n\\nFor example, if you\\\'re using BuddyPress, and you want to redirect Members to their BuddyPress Profile page after logging in, you would setup a Special Redirection URL, like this: ' . site_url ("/members/%%current_user_login%%/profile/") . '\\n\\nOr ... using %%current_user_level%%, you could have a separate Login Welcome Page for each Membership Level that you plan to offer. BuddyPress not required.\'); return false;">Replacement Codes</a> are also supported here.</em>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_redirection_override" id="ws-plugin--optimizemember-login-redirection-override" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_redirection_override"]) . '" /><br />' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_login_welcome_page", get_defined_vars ());
				}
				if(apply_filters("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_email_config", TRUE, get_defined_vars()))
				{
					do_action("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_email_config", get_defined_vars());

					echo '<div class="ws-menu-page-group" title="Email Configuration">'."\n";

					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-email-section">'."\n";
					echo '<h3 style="margin:0;">Email From: '.esc_html('"Name" <address>').'</h3>'."\n";
					echo '<p style="margin:0;">This is the name/address that will appear in outgoing email notifications sent by the OptimizeMember plugin.</p>'."\n";
					do_action("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_email_from_name_config", get_defined_vars());

					echo '<table class="form-table">'."\n";
					echo '<tbody>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-reg-email-from-name">'."\n";
					echo 'Email From Name:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_reg_email_from_name" id="ws-plugin--optimizemember-reg-email-from-name" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_name"]).'" /><br />'."\n";
					echo 'We recommend that you use the name of your site here.'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-reg-email-from-email">'."\n";
					echo 'Email From Address:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_reg_email_from_email" id="ws-plugin--optimizemember-reg-email-from-email" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_from_email"]).'" /><br />'."\n";
					echo 'Example: support@your-domain.com. <em>Please read <a href="#" onclick="alert(\'Running WordPress with an SMTP mail plugin?\\n\\nPlease be advised. If you run an SMTP mail plugin with WordPress, be sure to configure OptimizeMember with a valid `From:` address (i.e., one matching your SMTP configuration perhaps). Most free SMTP servers, such as Gmail and Yahoo, require that your `From:` header match the email address associated with your account. Please check with your SMTP service provider before attempting to configure plugins like OptimizeMember to use a different `From:` address when sending email messages.\'); return false;">this important note</a></em>.'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-reg-email-support-link">'."\n";
					echo 'Email Support/Contact Link:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_reg_email_support_link" id="ws-plugin--optimizemember-reg-email-support-link" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["reg_email_support_link"]).'" /><br />'."\n";
					echo 'Ex: <code>mailto:support@your-domain.com</code> (<em>mailto link</em>)<br />'."\n";
					echo 'Or: <code>'.esc_html(home_url("/contact-us/")).'</code>'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '</tbody>'."\n";
					echo '</table>'."\n";

					echo '<div class="ws-menu-page-hr"></div>'."\n";

					echo '<h3 style="margin:0;">New User Email Configuration</h3>'."\n";
					echo '<input type="hidden" id="ws-plugin--optimizemember-pluggables-wp-new-user-notification" value="'.esc_attr((empty($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["pluggables"]["wp_new_user_notification"])) ? '0' : '1').'" />'."\n";
					echo (empty($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["pluggables"]["wp_new_user_notification"])) ? '<p class="ws-menu-page-error" style="margin:0;"><em><strong>Conflict warning:</strong> You have another theme or plugin installed that is preventing OptimizeMember from controlling this aspect of your installation. When the pluggable function <code><a href="http://codex.wordpress.org/Function_Reference/wp_new_user_notification" target="_blank" rel="external">wp_new_user_notification()</a></code> is handled by another plugin, it\'s not possible for OptimizeMember to allow customization of New User Emails. This is NOT a major issue. In fact, in some cases, it might be desirable. That being said, if you DO want to use OptimizeMember\'s customization of New User Emails, you will need to deactivate one plugin at a time until this conflict warning goes away.</em></p>'."\n" : '';
					do_action("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_new_user_emails", get_defined_vars());

					echo '<table class="form-table">'."\n";
					echo '<tbody>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<select name="ws_plugin__optimizemember_new_user_emails_enabled" id="ws-plugin--optimizemember-new-user-emails-enabled">'."\n";
					echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["new_user_emails_enabled"]) ? ' selected="selected"' : '').'>No (default, use WordPress defaults)</option>'."\n";
					echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["new_user_emails_enabled"]) ? ' selected="selected"' : '').'>Yes (customize New User Emails with OptimizeMember)</option>'."\n";
					echo '</select>'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '</tbody>'."\n";
					echo '</table>'."\n";

					echo '<div id="ws-plugin--optimizemember-new-user-emails">'."\n";

					echo '<div class="ws-menu-page-hr"></div>'."\n";

					echo '<h3 style="margin:0;">New User Email Message (<a href="#" onclick="jQuery(\'div#ws-plugin--optimizemember-new-user-email-details\').toggle(); return false;" class="ws-dotted-link">click to customize</a>)</h3>'."\n";
					echo '<p style="margin:0;">This email is sent to new Users/Members who did <em>not</em> set a Custom Password during registration.</p>'."\n";
					do_action("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_new_user_email", get_defined_vars());

					echo '<div id="ws-plugin--optimizemember-new-user-email-details" style="display:none;">'."\n";
					echo c_ws_plugin__optimizemember_utils_conds::bp_is_installed() ? '<p><em><strong>BuddyPress:</strong> please note that BuddyPress does NOT send this email to Users that register through the BuddyPress registration system. This is because BuddyPress sends each User an activation link; eliminating the need for this email altogether. However, you CAN still customize OptimizeMember\'s separate email to paying Members. See: <strong>OptimizeMember → PayPal Options → Signup Confirmation Email</strong>.</em></p>'."\n" : '';
					echo '<table class="form-table">'."\n";
					echo '<tbody>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-new-user-email-subject">'."\n";
					echo 'New User Email Subject:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_new_user_email_subject" id="ws-plugin--optimizemember-new-user-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["new_user_email_subject"]).'" /><br />'."\n";
					echo 'Subject Line used in the email sent to new Users/Members.'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-new-user-email-message">'."\n";
					echo 'New User Email Message:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<p><em>(The purpose of this email is to send the user a password-setup link; i.e., <code>%%wp_set_pass_url%%</code>)</em></small><p>'."\n";
					echo '<textarea style="width:100%" name="ws_plugin__optimizemember_new_user_email_message" id="ws-plugin--optimizemember-new-user-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["new_user_email_message"]).'</textarea><br />'."\n";
					echo 'Message Body used in the email sent to new Users/Members.<br /><br />'."\n";
					echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
					echo '<ul>'."\n";
					echo '<li style="margin-bottom:1em;"><code>%%wp_set_pass_url%%</code> = The full URL where Users can set their password for the first time. Note that <code>%%wp_set_pass_url%%</code> should be used instead of the older/deprecated <code style="text-decoration:line-through;">%%user_pass%%</code> Replacement Code. Starting w/ WordPress v4.3+, a user can visit this link (i.e., <code>%%wp_set_pass_url%%</code>) to set a password of their own, and this is accomplished without sending a plain-text password via email; which improves security. It is suggested that you prefix this as follows: <em>To set your password, visit: <code>%%wp_set_pass_url%%</code></em></li>'."\n";
					echo '<li><code>%%role%%</code> = The Role ID <code>(subscriber, optimizemember_level[0-9]+, administrator, editor, author, contributor)</code>.</li>'."\n";
					echo '<li><code>%%label%%</code> = The Role ID Label <code>(Subscriber, OptimizeMember Level 1, OptimizeMember Level 2; or your own custom Labels—if configured)</code>.</li>'."\n";
					echo '<li><code>%%level%%</code> = The Level number <code>(0, 1, 2, 3, 4)</code>. (<em>deprecated, no longer recommended; use <code>%%role%%</code></em>)</li>'."\n";
					echo '<li><code>%%ccaps%%</code> = Custom Capabilities. Ex: <code>music,videos,free_gift</code> (<em>in comma-delimited format</em>).</li>'."\n";
					echo '<li><code>%%user_first_name%%</code> = The First Name of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_last_name%%</code> = The Last Name of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_full_name%%</code> = The Full Name (First &amp; Last) of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_email%%</code> = The Email Address of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_login%%</code> = The Username the Member selected during registration.</li>'."\n";
					echo '<li><code>%%user_ip%%</code> = The User\'s IP Address, detected via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
					echo '<li><code>%%user_id%%</code> = A unique WordPress User ID generated during registration.</li>'."\n";
					echo '<li><code>%%wp_login_url%%</code> = The full URL where Users can get logged into your site.</li>'."\n";
					echo '<li style="margin-top:1em;"><code style="text-decoration:line-through;">%%user_pass%%</code> = <em>Deprecated, please stop using this. Starting w/ WordPress v4.3, this email is only sent to users who did NOT set a Custom Password during registration; i.e., the New User Notification email is only sent to the user whenever they need it to set their password. In that scenario, you should be sending the user the <code>%%wp_set_pass_url%%</code> instead of sending a plain-text password via email; which is a security no-no.</em></li>'."\n";
					echo '</ul>'."\n";

					echo '<strong>Custom Registration/Profile Fields are also supported in this email:</strong>'."\n";
					echo '<ul>'."\n";
					echo '<li><code>%%date_of_birth%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>date_of_birth</code>.</li>'."\n";
					echo '<li><code>%%street_address%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>street_address</code>.</li>'."\n";
					echo '<li><code>%%country%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>country</code>.</li>'."\n";
					echo '<li><em><code>%%etc, etc...%%</code> <strong>see:</strong> OptimizeMember → General Options → Registration/Profile Fields</em>.</li>'."\n";
					echo '</ul>'."\n";

					echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
					echo '<ul>'."\n";
					echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
					echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
					echo '</ul>'."\n";
					echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
					echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
					echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

					echo (!is_multisite() || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() || is_main_site()) ?
						'<div class="ws-menu-page-hr"></div>'."\n".
						'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$user</code> variable is the most important one. It\'s an instance of the <a href="http://codex.wordpress.org/Class_Reference/WP_User" target="_blank" rel="external"><code>WP_User</code></a> class (e.g., <code>$user->ID</code>, <code>$user->has_cap()</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
						: '';
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '</tbody>'."\n";
					echo '</table>'."\n";
					echo '</div>'."\n";

					echo '<div class="ws-menu-page-hr"></div>'."\n";

					echo '<h3 style="margin:0;">Administrative: New User Notification (<a href="#" onclick="jQuery(\'div#ws-plugin--optimizemember-new-user-admin-email-details\').toggle(); return false;" class="ws-dotted-link">click to customize</a>)</h3>'."\n";
					echo '<p style="margin:0;">This email notification is sent to you, each time a new User/Member registers.</p>'."\n";
					do_action("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_new_user_admin_email", get_defined_vars());

					echo '<div id="ws-plugin--optimizemember-new-user-admin-email-details" style="display:none;">'."\n";
					echo '<table class="form-table">'."\n";
					echo '<tbody>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-new-user-admin-email-recipients">'."\n";
					echo 'New User Notification Recipients:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_new_user_admin_email_recipients" id="ws-plugin--optimizemember-new-user-admin-email-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["new_user_admin_email_recipients"]).'" /><br />'."\n";
					echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
					echo '<code>"Name" &lt;user@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-new-user-admin-email-subject">'."\n";
					echo 'New User Notification Subject:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_new_user_admin_email_subject" id="ws-plugin--optimizemember-new-user-admin-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["new_user_admin_email_subject"]).'" /><br />'."\n";
					echo 'Subject Line used in the email notification sent to Administrator.'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--optimizemember-new-user-admin-email-message">'."\n";
					echo 'New User Notification Message:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<textarea name="ws_plugin__optimizemember_new_user_admin_email_message" id="ws-plugin--optimizemember-new-user-admin-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["new_user_admin_email_message"]).'</textarea><br />'."\n";
					echo 'Message Body used in the email notification sent to Administrator.<br /><br />'."\n";
					echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
					echo '<ul>'."\n";
					echo '<li><code>%%role%%</code> = The Role ID <code>(subscriber, optimizemember_level[0-9]+, administrator, editor, author, contributor)</code>.</li>'."\n";
					echo '<li><code>%%label%%</code> = The Role ID Label <code>(Subscriber, OptimizeMember Level 1, OptimizeMember Level 2; or your own custom Labels—if configured)</code>.</li>'."\n";
					echo '<li><code>%%level%%</code> = The Level number <code>(0, 1, 2, 3, 4)</code>. (<em>deprecated, no longer recommended; use <code>%%role%%</code></em>)</li>'."\n";
					echo '<li><code>%%ccaps%%</code> = Custom Capabilities. Ex: <code>music,videos,free_gift</code> (<em>in comma-delimited format</em>).</li>'."\n";
					echo '<li><code>%%user_first_name%%</code> = The First Name of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_last_name%%</code> = The Last Name of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_full_name%%</code> = The Full Name (First &amp; Last) of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_email%%</code> = The Email Address of the Member who registered their Username.</li>'."\n";
					echo '<li><code>%%user_login%%</code> = The Username the Member selected during registration.</li>'."\n";
					echo '<li><code>%%user_ip%%</code> = The User\'s IP Address, detected via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
					echo '<li><code>%%user_id%%</code> = A unique WordPress User ID generated during registration.</li>'."\n";
					echo '<li><code>%%wp_login_url%%</code> = The full URL where Users can get logged into your site.</li>'."\n";
					echo '<li style="margin-top:1em;"><code style="text-decoration:line-through;">%%user_pass%%</code> = <em>Deprecated. Starting w/ WordPress v4.3, this is no longer applicable (or advised).</em></li>'."\n";
					echo '</ul>'."\n";

					echo '<strong>Custom Registration/Profile Fields are also supported in this email:</strong>'."\n";
					echo '<ul>'."\n";
					echo '<li><code>%%date_of_birth%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>date_of_birth</code>.</li>'."\n";
					echo '<li><code>%%street_address%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>street_address</code>.</li>'."\n";
					echo '<li><code>%%country%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>country</code>.</li>'."\n";
					echo '<li><em><code>%%etc, etc...%%</code> <strong>see:</strong> OptimizeMember → General Options → Registration/Profile Fields</em>.</li>'."\n";
					echo '</ul>'."\n";

					echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
					echo '<ul>'."\n";
					echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
					echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
					echo '</ul>'."\n";
					echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
					echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
					echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

					echo (!is_multisite() || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() || is_main_site()) ?
						'<div class="ws-menu-page-hr"></div>'."\n".
						'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$user</code> variable is the most important one. It\'s an instance of the <a href="http://codex.wordpress.org/Class_Reference/WP_User" target="_blank" rel="external"><code>WP_User</code></a> class (e.g., <code>$user->ID</code>, <code>$user->has_cap()</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
						: '';
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '</tbody>'."\n";
					echo '</table>'."\n";
					echo '</div>'."\n";
					echo '</div>'."\n";

					echo '</div>'."\n";

					echo '</div>'."\n";

					do_action("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_email_config", get_defined_vars());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_open_registration", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_open_registration", get_defined_vars ());
					/**/
					if (is_multisite () && is_main_site ()) /* A Multisite Network, and we're on the Main Site? */
					{
						echo '<div class="ws-menu-page-group" title="Open Registration">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-open-registration-section">' . "\n";
						echo '<h3>Open Registration / Free Subscribers ( optional )</h3>' . "\n";
						echo '<p>On the Main Site of a Multisite Network, the settings for Open Registration are consolidated into the <code>optimizeMember -> Multisite (Config)</code> panel.</p>' . "\n";
						do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_open_registration", get_defined_vars ());
						echo '</div>' . "\n";
						/**/
						echo '</div>' . "\n";
					}
					else /* Else we display this section normally. No special considerations are required in this case. */
					{
						echo '<div class="ws-menu-page-group" title="Open Registration">' . "\n";
						/**/
						echo '<div class="ws-menu-page-section ws-plugin--optimizemember-open-registration-section">' . "\n";
						echo '<h3>Open Registration / Free Subscribers ( optional )</h3>' . "\n";
						echo '<p>optimizeMember supports Free Subscribers ( at Level #0 ), along with four Primary Levels [1-10] of paid Membership. If you want your visitors to be capable of registering absolutely free, you will want to "allow" Open Registration. Whenever a visitor registers without paying, they\'ll automatically become a Free Subscriber, at Level #0.</p>' . "\n";
						do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_open_registration", get_defined_vars ());
						/**/
						echo '<table class="form-table">' . "\n";
						echo '<tbody>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<th>' . "\n";
						echo '<label for="ws-plugin--optimizemember-allow-subscribers-in">' . "\n";
						echo 'Allow Open Registration? ( Free Subscribers )' . "\n";
						echo '</label>' . "\n";
						echo '</th>' . "\n";
						/**/
						echo '</tr>' . "\n";
						echo '<tr>' . "\n";
						/**/
						echo '<td>' . "\n";
						echo '<select name="ws_plugin__optimizemember_allow_subscribers_in" id="ws-plugin--optimizemember-allow-subscribers-in">' . "\n";
						echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["allow_subscribers_in"]) ? ' selected="selected"' : '') . '>No ( do NOT allow Open Registration )</option>' . "\n";
						echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["allow_subscribers_in"]) ? ' selected="selected"' : '') . '>Yes ( allow Open Registration; Free Subscribers at Level #0 )</option>' . "\n";
						echo '</select><br />' . "\n";
						echo 'If you set this to <code>Yes</code>, you\'re unlocking <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::wp_register_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your Standard Registration Form.\\n* optimizeMember makes this form available to logged-in Administrators, at all times ( for testing purposes ), regardless of configuration.' . ((c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '') . '\');">/wp-login.php?action=register</a>. When a visitor registers without paying, they\'ll automatically become a Free Subscriber, at Level #0. The optimizeMember software reserves Level #0; to be used ONLY for Free Subscribers. All other Membership Levels [1-4] require payment.' . "\n";
						echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<br /><br /><em><strong>BuddyPress:</strong> BuddyPress will use its own Registration Form <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your BuddyPress Registration Form.\\n* However, you will probably be redirected away from this BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.\');">here</a>.<br />optimizeMember integrates with BuddyPress, and the above setting will control Open Regisration for BuddyPress too.</em>' . "\n" : '';
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
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_open_registration", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_login_registration", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_login_registration", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Login Page & Registration Form Design">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-login-registration-section">' . "\n";
					echo '<h3>Login/Registration Page Customization ( required )</h3>' . "\n";
					echo '<p>These settings customize your Standard Login/Registration Pages:<br />( <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::wp_register_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your Standard Registration Form.\\n* optimizeMember makes this form available to logged-in Administrators, at all times ( for testing purposes ), regardless of configuration.' . ((c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '') . '\');">' . esc_html (c_ws_plugin__optimizemember_utils_urls::wp_register_url ()) . '</a> )</p>' . "\n";
					echo (is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && is_main_site ()) ? '<p><em>The Main Site of a Multisite Blog Farm uses this Form instead, powered by your theme.<br />( <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::wp_signup_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your Multisite Registration Form.\\n* optimizeMember makes this form available to logged-in Super Administrators, at all times ( for testing purposes ), regardless of configuration.' . ((c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '') . '\');">' . esc_html (c_ws_plugin__optimizemember_utils_urls::wp_signup_url ()) . '</a> )</em></p>' . "\n" : '';
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<p><em><strong>BuddyPress:</strong> BuddyPress will use its own Registration Form, powered by your theme.<br />( <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your BuddyPress Registration Form.\\n* However, you will probably be redirected away from this BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.\');">' . esc_html (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . '</a> )</em></p>' . "\n" : '';
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_login_registration", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<h3 style="margin:0;">Overall Font/Size Configuration</h3>' . "\n";
					echo '<p style="margin:0;">These settings are all focused on your Login/Registration Fonts.</p>' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-font-size">' . "\n";
					echo 'Overall Font Size:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_font_size" id="ws-plugin--optimizemember-login-reg-font-size" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_font_size"]) . '" /><br />' . "\n";
					echo 'Set this to a numeric value, calculated in pixels.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-font-family">' . "\n";
					echo 'Overall Font Family:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_font_family" id="ws-plugin--optimizemember-login-reg-font-family" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_font_family"]) . '" /><br />' . "\n";
					echo 'Set this to a web-safe font family.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-font-field-size">' . "\n";
					echo 'Form Field Font Size:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_font_field_size" id="ws-plugin--optimizemember-login-reg-font-field-size" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_font_field_size"]) . '" /><br />' . "\n";
					echo 'Set this to a numeric value, calculated in pixels.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					/**/
					echo '<div class="ws-menu-page-hr"></div>' . "\n";
					/**/
					echo '<table class="form-table" style="margin-top:0;">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<h3 style="margin:0;">Background Configuration</h3>' . "\n";
					echo '<p style="margin:0;">These settings are all focused on your Login/Registration Background.</p>' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-background-color">' . "\n";
					echo 'Background Color:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_background_color" id="ws-plugin--optimizemember-login-reg-background-color" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_color"]) . '" /><br />' . "\n";
					echo 'Set this to a 6-digit hex color code.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-background-image">' . "\n";
					echo 'Background Image:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_background_image" id="ws-plugin--optimizemember-login-reg-background-image" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_image"]) . '" /><br />' . "\n";
					echo '<input type="button" id="ws-plugin--optimizemember-login-reg-background-image-media-btn" value="Open Media Library" class="ws-menu-page-media-btn button" rel="ws-plugin--optimizemember-login-reg-background-image" />' . "\n";
					echo 'Set this to the URL of your Background Image. ( this is optional )<br />';
					echo 'If supplied, your Background Image will be tiled.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-background-image-repeat">' . "\n";
					echo 'Background Image Tile:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_login_reg_background_image_repeat" id="ws-plugin--optimizemember-login-reg-background-image-repeat">' . "\n";
					echo '<option value="repeat"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_image_repeat"] === "repeat") ? ' selected="selected"' : '') . '>Seamless Tile ( background-repeat: repeat; )</option>' . "\n";
					echo '<option value="repeat-x"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_image_repeat"] === "repeat-x") ? ' selected="selected"' : '') . '>Tile Horizontally ( background-repeat: repeat-x; )</option>' . "\n";
					echo '<option value="repeat-y"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_image_repeat"] === "repeat-y") ? ' selected="selected"' : '') . '>Tile Vertically ( background-repeat: repeat-y; )</option>' . "\n";
					echo '<option value="no-repeat"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_image_repeat"] === "no-repeat") ? ' selected="selected"' : '') . '>No Tiles ( background-repeat: no-repeat; )</option>' . "\n";
					echo '</select><br />' . "\n";
					echo 'This controls the way your Background Image is styled with CSS. [ <a href="http://www.w3schools.com/css/pr_background-repeat.asp" target="_blank" rel="external">learn more</a> ]' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-background-text-color">' . "\n";
					echo 'Color of Text on top of your Background:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_background_text_color" id="ws-plugin--optimizemember-login-reg-background-text-color" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_text_color"]) . '" /><br />' . "\n";
					echo 'Set this to a 6-digit hex color code.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-background-text-shadow-color">' . "\n";
					echo 'Shadow Color for Text on top of your Background:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_background_text_shadow_color" id="ws-plugin--optimizemember-login-reg-background-text-shadow-color" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_text_shadow_color"]) . '" /><br />' . "\n";
					echo 'Set this to a 6-digit hex color code.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-background-box-shadow-color">' . "\n";
					echo 'Shadow Color for Boxes on top of your Background:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_background_box_shadow_color" id="ws-plugin--optimizemember-login-reg-background-box-shadow-color" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_background_box_shadow_color"]) . '" /><br />' . "\n";
					echo 'Set this to a 6-digit hex color code.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					/**/
					echo '<div class="ws-menu-page-hr"></div>' . "\n";
					/**/
					echo '<table class="form-table" style="margin-top:0;">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<h3 style="margin:0;">Logo Image Configuration</h3>' . "\n";
					echo '<p style="margin:0;">These settings are all focused on your Login/Registration Logo.</p>' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-logo-src">' . "\n";
					echo 'Logo Image Location:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_logo_src" id="ws-plugin--optimizemember-login-reg-logo-src" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_logo_src"]) . '" /><br />' . "\n";
					echo '<input type="button" id="ws-plugin--optimizemember-login-reg-logo-src-media-btn" value="Open Media Library" class="ws-menu-page-media-btn button" rel="ws-plugin--optimizemember-login-reg-logo-src" />' . "\n";
					echo 'Set this to the URL of your Logo Image.<br />' . "\n";
					echo 'Suggested size is around 500 x 100.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-logo-src-width">' . "\n";
					echo 'Logo Image Width:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_logo_src_width" id="ws-plugin--optimizemember-login-reg-logo-src-width" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_logo_src_width"]) . '" /><br />' . "\n";
					echo 'The pixel Width of your Logo Image. <em>* This ALSO affects the overall width of your Login/Registration forms. If you want wider form fields, use a wider Logo.</em>' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-logo-src-height">' . "\n";
					echo 'Logo Image Height:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_logo_src_height" id="ws-plugin--optimizemember-login-reg-logo-src-height" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_logo_src_height"]) . '" /><br />' . "\n";
					echo 'The pixel Height of your Logo Image.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-logo-url">' . "\n";
					echo 'Logo Image Click URL:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_logo_url" id="ws-plugin--optimizemember-login-reg-logo-url" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_logo_url"]) . '" /><br />' . "\n";
					echo 'Set this to the Click URL for your Logo Image.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-logo-title">' . "\n";
					echo 'Logo Image Title Attribute:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_login_reg_logo_title" id="ws-plugin--optimizemember-login-reg-logo-title" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_logo_title"]) . '" /><br />' . "\n";
					echo 'Used as the <code>title=""</code> attribute for your Logo Image.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					/**/
					echo '<div class="ws-menu-page-hr"></div>' . "\n";
					/**/
					echo '<table class="form-table" style="margin-top:0;">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<h3 style="margin:0;">Footer Design ( i.e. Bottom )</h3>' . "\n";
					echo '<p style="margin:0;">This field accepts raw HTML' . ((!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ()) ? ' ( and/or PHP )' : '') . ' code.</p>' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-login-reg-footer-design">' . "\n";
					echo 'Login/Registration Footer Design ( optional ):' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<textarea name="ws_plugin__optimizemember_login_reg_footer_design" id="ws-plugin--optimizemember-login-reg-footer-design" rows="3" wrap="off" spellcheck="false">' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_reg_footer_design"]) . '</textarea><br />' . "\n";
					echo 'This optional HTML' . ((!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ()) ? ' ( and/or PHP )' : '') . ' code will appear at the very bottom of your Login/Registration Forms.' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_login_registration", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_custom_reg_fields", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_custom_reg_fields", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Registration/Profile Fields &amp; Options">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-custom-reg-fields-section">' . "\n";
					echo '<h3>Custom Registration/Profile Fields ( optional, for further customization )</h3>' . "\n";
					echo '<p>Some fields are already built-in by default. The defaults are: <code>*Username*, *Email*, *First Name*, *Last Name*</code>.</p>' . "\n";
					/**/
					echo '<p>Custom Fields will appear in your Standard Registration Form, and in User/Member Profiles:<br />( <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::wp_register_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your Standard Registration Form.\\n* optimizeMember makes this form available to logged-in Administrators, at all times ( for testing purposes ), regardless of configuration.' . ((c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '') . '\');">' . esc_html (c_ws_plugin__optimizemember_utils_urls::wp_register_url ()) . '</a> )</p>' . "\n";
					echo (is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && is_main_site ()) ? '<p><em>The Main Site of a Multisite Blog Farm uses this Form. optimizeMember supports Custom Fields here too.<br />( <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::wp_signup_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your Multisite Registration Form.\\n* optimizeMember makes this form available to logged-in Super Administrators, at all times ( for testing purposes ), regardless of configuration.' . ((c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '') . '\');">' . esc_html (c_ws_plugin__optimizemember_utils_urls::wp_signup_url ()) . '</a> )</em></p>' . "\n" : '';
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<p><em><strong>BuddyPress:</strong> BuddyPress will use its own Registration Form <a href="' . esc_attr (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . '" target="_blank" rel="external" onclick="alert(\'optimizeMember will now open your BuddyPress Registration Form.\\n* However, you will probably be redirected away from this BuddyPress Registration Form ( ' . c_ws_plugin__optimizemember_utils_strings::esc_js_sq (c_ws_plugin__optimizemember_utils_urls::bp_register_url ()) . ' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.\');">here</a>.<br />optimizeMember can integrate your Custom Fields with BuddyPress too, please see options below.</em></p>' . "\n" : '';
					echo '<p><strong>Regarding registration...</strong> Custom Fields do NOT appear during repeat registration and/or checkout attempts (e.g. they do NOT appear for any user that is currently logged into the site). Please make sure that you test registration and/or checkout forms while NOT logged in (e.g. please test as a first-time customer). Existing users/members/customers may update Custom Fields by editing their Profile.</p>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_custom_reg_fields", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label>' . "\n";
					echo 'Custom Registration/Profile Fields:' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="hidden" name="ws_plugin__optimizemember_custom_reg_fields" id="ws-plugin--optimizemember-custom-reg-fields" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields"]) . '" />' . "\n";
					echo '<div id="ws-plugin--optimizemember-custom-reg-field-configuration"></div>' . "\n"; /* This is filled by JavaScript routines. */
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-custom-reg-names">' . "\n";
					echo 'Collect First/Last Names During Registration?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_custom_reg_names" id="ws-plugin--optimizemember-custom-reg-names">' . "\n";
					echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_password"]) ? ' selected="selected"' : '') . '>Yes ( always collect First/Last Names during registration )</option>' . "\n";
					echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_names"]) ? ' selected="selected"' : '') . '>No ( do NOT collect First/Last Names during registration )</option>' . "\n";
					echo '</select><br />' . "\n";
					echo 'Recommended setting ( <code>Yes</code> ). It\'s usually a good idea to leave this on.' . "\n";
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<br /><em>* Has NO affect on BuddyPress registration form (BuddyPress always collects a full <code>Name</code> field).</em>' . "\n" : '';
					echo (c_ws_plugin__optimizemember_utils_conds::pro_is_installed ()) ? '<br /><em>* optimizeMember Pro (Checkout) Forms always require a First/Last Name for billing.</em>' . "\n" : '';
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-custom-reg-display-name">' . "\n";
					echo 'Set "Display Name" During Registration?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_custom_reg_display_name" id="ws-plugin--optimizemember-custom-reg-display-name">' . "\n";
					echo '<option value="full"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_display_name"] === "full") ? ' selected="selected"' : '') . '>Yes ( set Display Name to User\'s Full Name )</option>' . "\n";
					echo '<option value="first"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_display_name"] === "first") ? ' selected="selected"' : '') . '>Yes ( set Display Name to User\'s First Name )</option>' . "\n";
					echo '<option value="last"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_display_name"] === "last") ? ' selected="selected"' : '') . '>Yes ( set Display Name to User\'s Last Name )</option>' . "\n";
					echo '<option value="login"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_display_name"] === "login") ? ' selected="selected"' : '') . '>Yes ( set Display Name to User\'s Username )</option>' . "\n";
					echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_display_name"]) ? ' selected="selected"' : '') . '>No ( leave Display Name at default WordPress value )</option>' . "\n";
					echo '</select>' . "\n";
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<br /><em>* Has NO affect on BuddyPress registration form (BuddyPress always uses its full <code>Name</code> field).</em>' . "\n" : '';
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-email-reg-link">' . "\n";
					echo 'Send registration link over email to customer' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_email_reg_link" id="ws-plugin--optimizemember-email-reg-link">' . "\n";
					echo '<option value="no"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["email_reg_link"] === "no") ? ' selected="selected"' : '') . '>No</option>' . "\n";
					echo '<option value="yes"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["email_reg_link"] === "yes") ? ' selected="selected"' : '') . '>Yes</option>' . "\n";
					echo '</select>' . "\n";
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<br /><em>* Has NO affect on BuddyPress registration form (BuddyPress always uses its full <code>Name</code> field).</em>' . "\n" : '';
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-custom-reg-password">' . "\n";
					echo 'Allow Custom Passwords During Registration?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<p style="margin-bottom:4px;"><em><strong>Note:</strong> Custom Passwords are easier for users. However, this has an impact on site security. Enabling Custom Passwords disables the New User Notification email that contains a password-setup link for each user. In other words, enabling Custom Passwords effectively disables any sort of email verification procedure, because the user is allowed to set their password during registration, instead of doing that via email confirmation—which is the default WordPress behavior.</em></p>'."\n";
					echo (is_multisite() && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() && is_main_site()) ? '<p style="margin-bottom:4px;"><em>* For security purposes, Custom Passwords are not possible on the Main Site of a Blog Farm. <a href="#" onclick="alert(\'For security purposes, Custom Passwords are not possible on the Main Site of a Blog Farm. A User must wait for the activation/confirmation email; where a randomly generated Password will be assigned. Please note, this limitation only affects your Main Site, via `/wp-signup.php`. In other words, your Customers (i.e., other Blog Owners) will still have the ability to allow Custom Passwords with OptimizeMember. YOU are affected by this limitation, NOT them.\\n\'); return false;" tabindex="-1">[?]</a></em></p>'."\n" : '';
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed()) ? '<p style="margin-bottom:4px;"><em>* Does not affect BuddyPress registration form (always <code>yes</code> with BuddyPress registration).</em></p>'."\n" : '';
					echo '<select name="ws_plugin__optimizemember_custom_reg_password" id="ws-plugin--optimizemember-custom-reg-password"' . ((is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && is_main_site () && !c_ws_plugin__optimizemember_utils_conds::pro_is_installed ()) ? ' disabled="disabled"' : '') . '>' . "\n";
					echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_password"]) ? ' selected="selected"' : '') . '>No (send each user a password-setup link after registration; recommended for best security)</option>' . "\n";
					echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_password"]) ? ' selected="selected"' : '') . '>Yes ( allow members to create their own password during registration )</option>' . "\n";
					echo '</select><br />' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-custom-reg-force-personal-emails">' . "\n";
					echo 'Force Personal Emails During Registration?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_custom_reg_force_personal_emails" id="ws-plugin--optimizemember-custom-reg-force-personal-emails" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_force_personal_emails"]) . '" /><br />' . "\n";
					echo 'To force personal email addresses, provide a comma-delimited list of email users to reject. <a href="#" onclick="alert(\'optimizeMember will reject [user]@ ( based on your configuration here ). A JavaScript alert message will be issued, asking the User to, `please use a personal email address`.\'); return false;" tabindex="-1">[?]</a><br />' . "\n";
					echo 'Ex: <code>info,help,admin,webmaster,hostmaster,sales,support,spam</code><br />' . "\n";
					echo 'See: <a href="http://kb.mailchimp.com/article/what-role-addresses-does-mailchimp-specifically-block-from-bulk-importing/" target="_blank" rel="external">this article</a> for a more complete list.' . "\n";
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<br /><em>* Affects BuddyPress registration form too.</em>' . "\n" : '';
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-custom-reg-fields-4bp">' . "\n";
					echo 'Integrate Custom Registration/Profile Fields with BuddyPress?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<div class="ws-menu-page-scrollbox" style="height:65px;">' . "\n";
					echo '<input type="hidden" name="ws_plugin__optimizemember_custom_reg_fields_4bp[]" value="update-signal"' . ((!c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? ' disabled="disabled"' : '') . ' />' . "\n";
					foreach (array ("profile-view" => "Yes, integrate with BuddyPress Public Profiles.", "registration" => "Yes, integrate with BuddyPress Registration Form.", "profile" => "Yes, integrate with BuddyPress Profile Editing Panel.") as $ws_plugin__optimizemember_temp_s_value => $ws_plugin__optimizemember_temp_s_label)
						echo '<input type="checkbox" name="ws_plugin__optimizemember_custom_reg_fields_4bp[]" id="ws-plugin--optimizemember-custom-reg-fields-4bp-' . esc_attr (preg_replace ("/[^a-z0-9_\-]/", "-", $ws_plugin__optimizemember_temp_s_value)) . '" value="' . esc_attr ($ws_plugin__optimizemember_temp_s_value) . '"' . ((in_array ($ws_plugin__optimizemember_temp_s_value, $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["custom_reg_fields_4bp"])) ? ' checked="checked"' : '') . ((!c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? ' disabled="disabled"' : '') . ' /> <label for="ws-plugin--optimizemember-custom-reg-fields-4bp-' . esc_attr (preg_replace ("/[^a-z0-9_\-]/", "-", $ws_plugin__optimizemember_temp_s_value)) . '">' . $ws_plugin__optimizemember_temp_s_label . '</label><br />' . "\n";
					echo '</div>' . "\n";
					echo (!c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<em>* BuddyPress is NOT installed; which is perfectly OK. BuddyPress is NOT a requirement.</em>' . "\n" : '<em>* The options above, make it possible to integrate Custom Registration/Profile Fields ( i.e. those configured with optimizeMember ) into BuddyPress as well. However, if you configure Profile Fields with BuddyPress, those will NOT be integrated with optimizeMember. Therefore, if you need Custom Registration/Profile Fields to work with both optimizeMember and with BuddyPress, please configure them with optimizeMember.</em>';
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_custom_reg_fields", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_profile_modifications", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_profile_modifications", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Member Profile Modifications">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-profile-modifications-section">' . "\n";
					echo '<h3>Giving Members The Ability To Modify Their Profile</h3>' . "\n";
					echo '<p>optimizeMember can be configured to redirect Members away from the <a href="' . esc_attr (admin_url ("/profile.php")) . '" target="_blank" rel="external">default Profile Editing Panel</a> that is built into WordPress. When/if a Member attempts to access the default Profile Editing Panel, they\'ll instead, be redirected to the Login Welcome Page that you\'ve configured through optimizeMember. <strong>Why would I redirect?</strong> Unless you\'ve made some drastic modifications to your WordPress installation, the default Profile Editing Panel that ships with WordPress, is NOT really suited for public access, even by a Member.</p>' . "\n";
					echo '<p>So instead of using this default Profile Editing Panel; optimizeMember creates an added layer of functionality, on top of WordPress. It does this by providing you <em>( as the site owner )</em>, with a special Shortcode: <code>[optimizeMember-Profile /]</code> that you can place into your Login Welcome Page, or any Post/Page for that matter <em>( even into a Text Widget )</em>. This Shortcode produces an Inline Profile Editing Form that supports all aspects of optimizeMember, including Password changes; and any Custom Registration/Profile Fields that you\'ve configured with optimizeMember.</p>' . "\n";
					echo '<p>Alternatively, optimizeMember also gives you the ability to send your Members to a <a href="' . esc_attr (site_url ("/?optimizemember_profile=1")) . '" target="_blank" rel="external">special Stand-Alone version</a>. This Stand-Alone version has been designed <em>( with a bare-bones format )</em>, intentionally. This makes it possible for you to <a href="#" onclick="if(!window.open(\'' . site_url ("/?optimizemember_profile=1") . '\', \'_popup\', \'width=600,height=400,left=100,screenX=100,top=100,screenY=100,location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1\')) alert(\'Please disable popup blockers and try again!\'); return false;" rel="external">open it up in a popup window</a>, or embed it into your Login Welcome Page using an IFRAME. Code samples are provided below.</p>' . "\n";
					echo (c_ws_plugin__optimizemember_utils_conds::bp_is_installed ()) ? '<p><em><strong>BuddyPress:</strong> BuddyPress already provides Users/Members with a Profile Editing Panel, powered by your theme. If you\'ve configured Custom Registration/Profile Fields with optimizeMember, you can also enable optimizeMember\'s Profile Field integration with BuddyPress ( recommended ). For further details, see: <code>optimizeMember -> General Options -> Registration/Profile Fields</code>.</em></p>' . "\n" : '';
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_profile_modifications", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-force-admin-lockouts">' . "\n";
					echo 'Redirect Members away from the Default Profile Panel?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_force_admin_lockouts" id="ws-plugin--optimizemember-force-admin-lockouts">' . "\n";
					echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["force_admin_lockouts"]) ? ' selected="selected"' : '') . '>No ( I want to use the WordPress default methodologies )</option>' . "\n";
					echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["force_admin_lockouts"]) ? ' selected="selected"' : '') . '>Yes ( redirect to Login Welcome Page; locking all /wp-admin/ areas )</option>' . "\n";
					echo '</select><br />' . "\n";
					echo 'Recommended setting ( <code>Yes</code> ). <em><strong>*Note*</strong> When this is set to ( <code>Yes</code> ), optimizeMember will take an initiative to further safeguard ALL <code>/wp-admin/</code> areas of your installation; not just the Default Profile Panel.</em>' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					/**/
					echo '<div class="ws-menu-page-hr"></div>' . "\n";
					/**/
					echo '<p><strong>Shortcode ( copy/paste )</strong>, for an Inline Profile Modification Form:<br />' . "\n";
					echo '<p><input type="text" autocomplete="off" value="' . format_to_edit ('[optimizeMember-Profile /]') . '" style="font-size:90%; font-family:Consolas, monospace; width:99%;" onclick="this.select ();" /></p>' . "\n";
					/**/
					echo '<p style="margin-top:20px;"><strong>Stand-Alone ( copy/paste )</strong>, for popup window:</p>' . "\n";
					echo '<p><input type="text" autocomplete="off" value="' . format_to_edit (preg_replace ("/\<\?php echo OPTIMIZEMEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL; \?\>/", c_ws_plugin__optimizemember_utils_strings::esc_refs (site_url ("/?optimizemember_profile=1")), file_get_contents (dirname (__FILE__) . "/code-samples/current-user-profile-modification-page-url-2-ops.x-php"))) . '" style="font-size:90%; font-family:Consolas, monospace; width:99%;" onclick="this.select ();" /></p>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_profile_modifications", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_trk_ops_page_during_left_sections_display_other_membership_options_header", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_trk_ops_page_during_left_sections_before_other_membership_options_header", get_defined_vars ());
					/**/
					echo '<div style="border-bottom:1px solid #DFDFDF; margin:-20px 0 9px 0; padding:0;">&nbsp;</div>' . "\n";
					// echo '<div id="icon-tools" class="icon32" style="margin-top:0; margin-bottom:0; padding-top:0; padding-bottom:0;"><br /></div>' . "\n";
					echo '<h2 style="margin-top:30px;">Other Membership Options</h2>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_trk_ops_page_during_left_sections_after_other_membership_options_header", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_url_shortening", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_url_shortening", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="URL Shortening Service Preference">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-url-shortening-section">' . "\n";
					echo '<h3>URL Shortening Service API ( Preference )</h3>' . "\n";
					echo '<p>In a few special cases, long URLs generated by optimizeMember, containing encrypted authentication details, will be shortened; using one of the URL Shortening APIs <em>( listed below )</em>. A shortened URL prevents issues with VERY long links becoming corrupted by a Customer\'s email application. For instance, the Signup Confirmation Email that optimizeMember sends out to a new paying Customer, may contain a link which is shortened to prevent corruption by email applications. By default, optimizeMember uses the tinyURL API, which has proven itself to be the most reliable. However, in cases where an API service call fails, optimizeMember will automatically use one or more of its other APIs as a backup. The option below, allows you to configure which URL Shortening API optimizeMember should try first <em>( i.e. the one you prefer )</em>.</p>' . "\n";
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_url_shortening", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-default-url-shortener">' . "\n";
					echo 'URL Shortening Service API ( Preference ):' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_default_url_shortener" id="ws-plugin--optimizemember-default-url-shortener">' . "\n";
					echo '<option value="tiny_url"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["default_url_shortener"] === "tiny_url") ? ' selected="selected"' : '') . '>tinyurl.com ( free tinyURL™ API service )</option>' . "\n";
					echo '<option value="goo_gl"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["default_url_shortener"] === "goo_gl") ? ' selected="selected"' : '') . '>goo.gl ( free Google URL Shortening API service )</option>' . "\n";
					echo '<option value="none"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["default_url_shortener"] === "none") ? ' selected="selected"' : '') . '>none</option>' . "\n";
					echo '</select>' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-default-custom-str-url-shortener">' . "\n";
					echo 'Custom URL Shortening Service API ( Optional/Advanced ):' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__optimizemember_default_custom_str_url_shortener" id="ws-plugin--optimizemember-default-custom-str-url-shortener" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["default_custom_str_url_shortener"]) . '" /><br />' . "\n";
					echo 'Your own custom URL <code>( i.e. GET request )</code>, with <code>%%s2_long_url%%</code> Replacement Code. [ <a href="#" onclick="alert(\'optimizeMember makes it possible for advanced site owners to use a custom URL shortening service they prefer, over the ones currently pre-integrated with optimizeMember. In order for this to work, your URL shortening service MUST support basic GET requests through its API ( sometimes referred to as a REST or NVP API ). In addition, your URL shortening service MUST be capable of returning a simple URL in the response that optimizeMember receives, as a result of optimizeMember processing the GET request you formulate. See example below.\\n\\nBitly example GET request with format=txt:\nhttp://api.bitly.com/v3/shorten?login=demo&apiKey=2d71bf07&format=txt&longUrl=%%s2_long_url%%\\n( optimizeMember expects a shortened URL in the response from Bitly )\\n\\n* If you configure optimizeMember to use your own custom URL shortening service, optimizeMember will try your configuration first, and if anything fails, it will fall back on its own pre-integrated backups. When configuring your URL for the GET request, optimizeMember makes two Replacement Codes available:\\n\\n%%s2_long_url%% = The full URL that needs to be shortened ( raw URL-encoded ).\\n%%s2_long_url_md5%% = An MD5 hash of the full URL ( might be useful in some APIs ).\\n\\n* If you have any trouble getting your URL shortening service integrated with optimizeMember in this way, you might take a look at this WordPress Filter ( `ws_plugin__optimizemember_url_shorten` ), which optimizeMember makes available for advanced circumstances. Search optimizeMember\\\'s source code for `ws_plugin__optimizemember_url_shorten`.\'); return false;" tabindex="-1">click for details</a> ]<br />' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_url_shortening", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_deactivation", (!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site () || is_super_admin ()), get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_deactivation", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Deactivation Safeguards"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["run_deactivation_routines"]) ? ' default-state="open"' : '') . '>' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-deactivation-section">' . "\n";
					echo '<h3>Deactivation Safeguards ( highly recommended )</h3>' . "\n";
					echo (is_multisite () && c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () && !is_main_site () && is_super_admin ()) ? '<p><em class="ws-menu-page-hilite">On a Multisite Blog Farm, this panel is ONLY visible to YOU, as a Super Administrator. optimizeMember automatically Safeguards everything on a Multisite Blog Farm. However, as the Super Administrator, you may turn this off; on a per-Blog basis. For example, if you\'re going to de-activate optimizeMember on this particular Blog, you can turn OFF the Safeguards below, so that optimizeMember will completely erase itself.</em></p>' . "\n" : '<p>By default, optimizeMember will retain all of it\'s Roles, Capabilities, and your Configuration Options when/if you deactivate optimizeMember from the Plugins Menu in WordPress. However, if you would like for optimizeMember to erase itself completely, please choose: <code>No ( upon deactivation, erase all data/options )</code>.</p>' . "\n";
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_deactivation", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-run-deactivation-routines">' . "\n";
					echo 'Safeguard optimizeMember Data/Options?' . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<select name="ws_plugin__optimizemember_run_deactivation_routines" id="ws-plugin--optimizemember-run-deactivation-routines">' . "\n";
					echo '<option value="0"' . ((!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["run_deactivation_routines"]) ? ' selected="selected"' : '') . '>Yes ( safeguard all data/options )</option>' . "\n";
					echo '<option value="1"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["run_deactivation_routines"]) ? ' selected="selected"' : '') . '>No ( upon deactivation, erase all data/options )</option>' . "\n";
					echo '</select><br />' . "\n";
					echo 'Recommended setting: ( <code>Yes, safeguard all data/options</code> )' . "\n";
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_deactivation", get_defined_vars ());
				}
				/**/
				if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_security", true, get_defined_vars ()))
				{
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_security", get_defined_vars ());
					/**/
					echo '<div class="ws-menu-page-group" title="Security Encryption Key">' . "\n";
					/**/
					echo '<div class="ws-menu-page-section ws-plugin--optimizemember-security-section">' . "\n";
					echo '<img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]) . '/images/large-icon.png" title="optimizeMember ( a Membership management system for WordPress )" alt="" style="float:right; margin:0 0 0 25px; border:0;" />' . "\n";
					echo '<h3>Security Encryption Key ( optional, for tighter security )</h3>' . "\n";
					echo '<p>Just like WordPress, optimizeMember is open-source software. Which is wonderful. However, this also makes it possible for anyone to grab a copy of the software, and try to learn their way around its security measures. In order to keep your installation of optimizeMember unique/secure, you should configure a Security Encryption Key. optimizeMember will use your Security Encryption Key to protect itself against hackers. It does this by encrypting all sensitive information with your Key. A Security Encryption Key is unique to your installation.</p>' . "\n";
					echo '<p>Once you configure this, you do NOT want to change it; not ever. In fact, it is a VERY good idea to keep this backed up in a safe place, just in case you need to move your site, or re-install optimizeMember in the future. Some of the sensitive data that optimizeMember stores, will be encrypted with this Key. If you change it, that data can no longer be read, even by optimizeMember itself. In other words, don\'t use optimizeMember for six months, then decide to change your Key. That would break your installation.</p>' . "\n";
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_security", get_defined_vars ());
					/**/
					echo '<table class="form-table">' . "\n";
					echo '<tbody>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<th>' . "\n";
					echo '<label for="ws-plugin--optimizemember-sec-encryption-key">' . "\n";
					echo 'Security Encryption Key ( at least 60 chars )' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["sec_encryption_key"]) ? ' <a href="#" onclick="ws_plugin__optimizemember_enableSecurityKey(); return false;" title="( not recommended )">edit key</a>' : ' <a href="#" onclick="ws_plugin__optimizemember_generateSecurityKey(); return false;" title="Insert an auto-generated Key. ( recommended )">auto-generate</a>') . "\n";
					echo '</label>' . "\n";
					echo '</th>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '<tr>' . "\n";
					/**/
					echo '<td>' . "\n";
					echo '<input type="text" maxlength="256" autocomplete="off" name="ws_plugin__optimizemember_sec_encryption_key" id="ws-plugin--optimizemember-sec-encryption-key" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["sec_encryption_key"]) . '"' . (($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["sec_encryption_key"]) ? ' disabled="disabled"' : '') . ' />' . "\n";
					echo (!$GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["sec_encryption_key"]) ? '<br />This may contain letters, numbers, spaces; even punctuation. Up to 256 characters.<br /><em>Ex: <code>' . esc_html (strtoupper (c_ws_plugin__optimizemember_utils_strings::random_str_gen (64))) . '</code></em>' . "\n" : '';
					echo (count ($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["sec_encryption_key_history"]) > 1) ? '<br /><a href="#" onclick="ws_plugin__optimizemember_securityKeyHistory(); return false;">Click here</a> for a history of your last 10 Encryption Keys.<div id="ws-plugin--optimizemember-sec-encryption-key-history" style="display:none;"><code>' . implode ('</code><br /><code>', $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["sec_encryption_key_history"]) . '</code></div>' . "\n" : '';
					echo '</td>' . "\n";
					/**/
					echo '</tr>' . "\n";
					echo '</tbody>' . "\n";
					echo '</table>' . "\n";

                    echo '<div class="ws-menu-page-hr"></div>'."\n";

                    echo '<h3>Additional Details Regarding this Key:</h3>'."\n";
                    echo '<p>Your Security Encryption Key is used throughout OptimizeMember\'s source code for many different things. However, MOST (not all, but most) uses of this Key are related to transactional processing within a particular session; so changing the Key won\'t really impact these scenarios in any significant way. Your Security Encryption Key is simply there to enhance security of data that is being transmitted in these cases.</p>'."\n";
                    echo '<p>That said, there are a few scenarios where use of your Security Encryption Key is more long-term. These include: Specific Post/Page Access Links, Registration Access Links, and it can also have a long-term impact on IPN communication because some data analyzed by OptimizeMember includes a checksum that depends on your Key. If the Key changes, it could cause future IPN data (i.e. data from your payment gateway) to fail validation.</p>'."\n";
					echo '</div>' . "\n";
					/**/
					echo '</div>' . "\n";
					/**/
					do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_security", get_defined_vars ());
				}
				/**/

				do_action ("ws_plugin__optimizemember_during_gen_ops_page_after_left_sections", get_defined_vars ());
				/**/
				//echo '<div class="ws-menu-page-hr"></div>' . "\n";
				/**/
				echo '<p class="submit"><input type="submit" class="op-pb-button green" value="Save All Changes" /></p>' . "\n";
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
new c_ws_plugin__optimizemember_menu_page_gen_ops ();
?>