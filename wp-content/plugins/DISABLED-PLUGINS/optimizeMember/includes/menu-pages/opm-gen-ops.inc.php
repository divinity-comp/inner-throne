<?php
/**
* Menu page for the optimizeMember plugin ( NEW General Options page ).
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
		* Menu page for the optimizeMember plugin ( NEW General Options page ).
		*
		* @package optimizeMember\Menu_Pages
		* @since 130214
		*/
		class c_ws_plugin__optimizemember_menu_page_opm_gen_ops
			{
				public function __construct ()
					{
						echo '<div class="wrap ws-menu-page op-bsw-wizard op-bsw-content">' . "\n";
						/**/
						echo '<div class="op-bsw-header">';
							echo '<div class="op-logo"><img src="' . $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["dir_url"]."/images/" . 'logo-optimizepress.png" alt="OptimizePress" height="50" class="animated flipInY"></div>';
						echo '</div>';
						echo '<div class="op-bsw-main-content">';
						echo '<h2>NEW General Options</h2>' . "\n";
						echo '<p>' . _('Use these options to customize styling and functionality of your pages. Ensure you also create and assign menus to your blog Menus within the <a href="nav-menus.php">Wordpress Menus admin panel</a> if you want to use navigation menus on your pages.') . '</p>';
						/**/
						echo '<table class="ws-menu-page-table">' . "\n";
						echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
						echo '<tr class="ws-menu-page-table-tr">' . "\n";
						echo '<td class="ws-menu-page-table-l">' . "\n";
						/**/
						echo '<form method="post" name="ws_plugin__optimizemember_new_options_form" id="ws-plugin--optimizemember-new-options-form">' . "\n";
						echo '<input type="hidden" name="ws_plugin__optimizemember_options_save" id="ws-plugin--optimizemember-options-save" value="' . esc_attr (wp_create_nonce ("ws-plugin--optimizemember-options-save")) . '" />' . "\n";
						echo '<input type="hidden" name="ws_plugin__optimizemember_configured" id="ws-plugin--optimizemember-configured" value="1" />' . "\n";
						echo '<input type="hidden" name="action" value="optimizemember-new-options" />' . "\n";
						/**/
						do_action ("ws_plugin__optimizemember_during_gen_ops_page_before_left_sections", get_defined_vars ());
						/**/

						if (apply_filters ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_display_membership_levels", true, get_defined_vars ()))
						{
							do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_before_membership_levels", get_defined_vars ());
							/**/
							echo '<div class="ws-menu-page-group" title="Define Membership Levels And Packages">' . "\n";
							/**/
							echo '<div class="ws-menu-page-section ws-plugin--optimizemember-membership-levels-section">' . "\n";
							echo '<h3>Membership Levels and Packages ( required, please customize these )</h3>' . "\n";
							echo '<p>The default Membership Levels are labeled generically; feel free to modify them as needed. optimizeMember supports Free Subscribers <em>( at Level #0 )</em>, along with several Primary Roles for paid Membership <em>( i.e. Levels 1-4 )</em>, created by the optimizeMember plugin.' . ((!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ()) ? ' optimizeMember also supports unlimited Custom Capability Packages <em>( see <code>optimizeMember -> API Scripting -> Custom Capabilities</code> )</em>' : '') . '. That being said, you don\'t have to use all of the Membership Levels if you don\'t want to. To use only 1 or 2 of these Levels, just design your Membership Options Page, so it only includes Payment Buttons for the Levels being used.</p>' . "\n";
							echo (!is_multisite () || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm () || is_main_site ()) ? '<p><em><strong>TIP:</strong> <strong>Unlimited Membership Levels</strong> are only possible with <a href="' . esc_attr (c_ws_plugin__optimizemember_readmes::parse_readme_value ("Pro Module / Prices")) . '" target="_blank" rel="external">optimizeMember Pro</a>. However, Custom Capabilities are possible in all versions of optimizeMember, including the free version. Custom Capabilities are a great way to extend optimizeMember in creative ways. If you\'re an advanced site owner, a theme designer, or a web developer integrating optimizeMember for a client, please check your Dashboard, under: <code>optimizeMember -> API Scripting -> Custom Capabilities</code>. </em></p>' . "\n" : '';
							do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_during_membership_levels", get_defined_vars ());
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
							echo '<input type="hidden" name="ws_plugin__optimizemember_ccp[]" value="update-signal" />'."\n";
							echo '<td>' . "\n";
							echo '<input type="text" onblur="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" name="ws_plugin__optimizemember_ccp[]" id="ws-plugin--optimizemember-ccp" value="" />';
							if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
								echo '<h3>Available packages</h3>' . "\n";
								foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
									if (!empty($val)) {
										echo '<input type="hidden" name="ws_plugin__optimizemember_ccp[]" value="'.$val.'" />' . "\n";
										echo $val . '<br />' . "\n";
									}
								}
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
							echo '<p>The button to the right, is a nifty tool, which allows you to reset optimizeMember\'s internal Roles and Capabilities that integrate with WordPress. If you, or a developer working with you, has made attempts to alter the default <em>internal</em> Role/Capability sets that come with optimizeMember, and you need to reset them back to the way optimizeMember expects them to be, please use this tool. <em>Attn Developers: it is also possible lock-in your modified Roles/Capabilities with an optimizeMember Filter. </em></p>' . "\n";
							/**/
							echo '<p class="submit"><input id="opmNewSave" type="submit" class="op-pb-button green" value="Save Changes" /></p>' . "\n";
							echo '<p id="msg"><p>';
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
							echo '<p>To enable and configure email marketing service go to <a href="' . admin_url() . 'admin.php?page=optimizepress#email_marketing_services">OP theme/plugin Dashboard settings</a></p>' . "\n";
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
									echo '</td>' . "\n";
									/**/
									echo '</tr>' . "\n";
								}
							/**/
							echo '</tbody>' . "\n";
							echo '</table>' . "\n";
							/**/
							echo '<p class="submit"><input id="opmNewSave" type="submit" class="op-pb-button green" value="Save Changes" /></p>' . "\n";
							echo '<p class="save_info"><p>';
							echo '</div>' . "\n";
							/**/
							echo '</div>' . "\n";
							/**/
							do_action ("ws_plugin__optimizemember_during_gen_ops_page_during_left_sections_after_list_server_integration", get_defined_vars ());
						}

						/**/

						/**/
						do_action ("ws_plugin__optimizemember_during_gen_ops_page_after_left_sections", get_defined_vars ());
						/**/
						//echo '<div class="ws-menu-page-hr"></div>' . "\n";
						/**/
						//echo '<p class="submit"><input type="submit" class="button-primary" value="Save All Changes" /></p>' . "\n";
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
new c_ws_plugin__optimizemember_menu_page_opm_gen_ops();
?>