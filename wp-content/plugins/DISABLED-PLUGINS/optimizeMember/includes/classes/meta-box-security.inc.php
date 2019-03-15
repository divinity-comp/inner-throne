<?php
/**
* Security meta box.
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
* @package optimizeMember\Meta_Boxes
* @since 3.5
*/
if(!defined('WPINC'))
	exit("Do not access this file directly.");
/**/
if(!class_exists("c_ws_plugin__optimizemember_meta_box_security"))
	{
		/**
		* Security meta box.
		*
		* @package optimizeMember\Meta_Boxes
		* @since 3.5
		*/
		class c_ws_plugin__optimizemember_meta_box_security
			{
				/**
				* Adds security meta box to Post/Page editing stations.
				*
				* @package optimizeMember\Meta_Boxes
				* @since 3.5
				*
				* @param obj $post Post/Page object.
				* @return null
				*/
				public static function security_meta_box($post = FALSE)
			{
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action("ws_plugin__optimizemember_before_security_meta_box", get_defined_vars());
						unset($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if(is_object($post) && ($post_id = $post->ID) && (($post->post_type === "page" && current_user_can("edit_page", $post_id)) || current_user_can("edit_post", $post_id)))
							{
								if($post->post_type === "page" && ($page_id = $post_id)) /* OK. So we're dealing with a Page classification. */
									{
										if(!in_array($page_id, array_merge(array($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"], $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_welcome_page"], $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["file_download_limit_exceeded_page"]), preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"]))))
											{
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save" id="ws-plugin--optimizemember-security-meta-box-save" value="'.esc_attr(wp_create_nonce("ws-plugin--optimizemember-security-meta-box-save")).'" />'."\n";
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save_id" id="ws-plugin--optimizemember-security-meta-box-save-id" value="'.esc_attr($page_id).'" />'."\n";
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
													$pages[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level".$n."_pages"]));
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
													$posts[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level".$n."_posts"]));
												/**/
												echo '<p style="margin-left:2px;"><strong>Page Level Restriction?</strong></p>'."\n";
												//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-level">Add Level Restriction?</label>'."\n";
												echo '<select name="ws_plugin__optimizemember_security_meta_box_level" id="ws-plugin--optimizemember-security-meta-box-level" style="width:99%;">'."\n";
												echo '<option value=""></option>'."\n"; /* By default, we allow public access to any Post/Page. */
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++) {
													$levelText = ws_plugin__optimizemember_getMembershipLabel($n);
													echo ($pages[$n] !== array("all")) ? /* Protecting `all` Pages, of any kind? */
													((!in_array("all-pages", $posts[$n])) /* Protecting Posts of type: `page` ( i.e. `all-pages` )? */
													? '<option value="'.$n.'"'.((in_array($page_id, $pages[$n])) ? ' selected="selected"' : '').'>'.(($n === $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]) ? 'Require Highest '.$levelText : 'Require '.$levelText.' ( or higher )').'</option>'."\n"/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Posts of this type )</option>'."\n")/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Pages )</option>'."\n";
												}
												/**/
												echo '</select><br />'."\n";
												/**/
												if(!is_multisite() || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() || is_main_site())
													/* ^ Will change once Custom Capabilities are compatible with a Blog Farm. */
													{
														echo '<p style="margin-top:15px; margin-left:2px;"><strong>Require Product Packages?</strong></p>'."\n";
														//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-ccaps">Custom Capabilities?</label>'."\n";
														if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
														foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
															$ccaps = get_post_meta($page_id, "optimizemember_ccaps_req", true);
															$checked = '';
															if (is_array($ccaps) and in_array($val, $ccaps)) {
																$checked='checked="checked"';
															}
															echo '<label style="display:block;overflow:hidden;"><input type="checkbox" '.$checked.' onchange="showSelectedValues();" name="ws_plugin__optimizemember_ccpchk" value="'.$val.'" /> '.$val.'</label>' . "\n";
															//echo $val . '<br />' . "\n";
														}
														} else {
															echo 'To use packages, enter some first in <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--optimizemember-gen-ops")).'">Define membership levels and packages section.</a>';
														}
														echo '<input type="hidden" autocomplete="off" name="ws_plugin__optimizemember_security_meta_box_ccaps" id="ws-plugin--optimizemember-security-meta-box-ccaps" value="" />'."\n";
														//echo '<br /><small>* see: <code>API Scripting -> Custom Capabilities</code></small>'."\n";
														?>
														<p><?php _e('Delay content for ', OP_SN);?>
														<?php 
															global $post;
															$drip_days = get_post_meta($post->ID, "optimizemember_drip_days", true);
															$drip_redirect_url = get_post_meta($post->ID, "optimizemember_drip_redirect_url", true);
														?>
														<input style="display:inline; width:50px;" type="text" name="ws_plugin__optimizemember_drip_days" value="<?php echo $drip_days; ?>" />
														<?php _e('days', OP_SN);?>.</p>
                                                        <p><?php _e('Delay content works with Level 1 or above.', OP_SN); ?><br /><a target="_blank" href="https://optimizepress.zendesk.com/hc/en-us/articles/201187926"><?php _e('See our guide to delay content', OP_SN); ?></a></p>
														<label class="form-title" for="ws_plugin__optimizemember_drip_redirect"><?php _e('Redirect to:', OP_SN);?></label>
														 
														<select name="ws_plugin__optimizemember_drip_redirect_url">
														<option value="">---</option>
														<?php 
															$availablePages = get_pages();
															foreach ($availablePages as $page) {
																$selected = "";
																if ($page->ID != $post->ID) { // current page is excluded
																	if ($drip_redirect_url == get_page_link($page->ID)) {
																		$selected = 'selected="selected"';
																	}
																	$option = '<option '.$selected.' value="' . get_page_link( $page->ID ) . '">';
																	$option .= $page->post_title;
																	$option .= '</option>';
																	echo $option;
																}
															}
														?>
														</select>
														
														<script type="text/javascript">
														showSelectedValues();
														/**/
														function showSelectedValues()
														{
															var text = jQuery("input[name=ws_plugin__optimizemember_ccpchk]:checked").map(
															     function () {return this.value;}).get().join(",");

															jQuery("#ws-plugin--optimizemember-security-meta-box-ccaps").val(text);
														}
														/**/
														</script>
														<?php 
													}
												/**/
												echo '<p style="margin-left:2px;"><strong>Force SSL?</strong></p>'."\n";
												echo '<input type="checkbox"' . checked(get_post_meta($page_id, 'optimizemember_force_ssl', true), 'yes', false) . 'name="ws_plugin__optimizemember_force_ssl" value="yes" /> Yes' . "\n";
											}
										/**/
										else if($page_id == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"])
											echo 'This Page is your:<br /><strong>Membership Options Page</strong><br />( always publicly available )';
										/**/
										else if($page_id == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_welcome_page"])
											echo 'This Page is your:<br /><strong>Login Welcome Page</strong><br />( automatically guarded by optimizeMember )';
										/**/
										else if($page_id == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["file_download_limit_exceeded_page"])
											echo 'This Page is your:<br /><strong>Download Limit Exceeded Page</strong><br />( automatically guarded by optimizeMember )';
										/**/
										else if(in_array($page_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"])))
											echo 'This Page is a:<br /><strong>Specific Post/Page for sale</strong><br />( already guarded by optimizeMember )';
									}
								else /* Otherwise, we assume this is a Post, or possibly a Custom Post Type. It's NOT a Page. */
									{
										if(!in_array($post_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"])))
											{
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save" id="ws-plugin--optimizemember-security-meta-box-save" value="'.esc_attr(wp_create_nonce("ws-plugin--optimizemember-security-meta-box-save")).'" />'."\n";
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save_id" id="ws-plugin--optimizemember-security-meta-box-save-id" value="'.esc_attr($post_id).'" />'."\n";
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
													$posts[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level".$n."_posts"]));
												/**/
												echo '<p style="margin-left:2px;"><strong>Post Level Restriction?</strong></p>'."\n";
												//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-level">Add Level Restriction?</label>'."\n";
												echo '<select name="ws_plugin__optimizemember_security_meta_box_level" id="ws-plugin--optimizemember-security-meta-box-level" style="width:99%;">'."\n";
												echo '<option value=""></option>'."\n"; /* By default, we allow public access to any Post/Page. */
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++) {
													$levelText = ws_plugin__optimizemember_getMembershipLabel($n);
													echo ($posts[$n] !== array("all")) ? /* Protecting `all` Posts, of any kind? */
													((!in_array("all-".$post->post_type."s", $posts[$n])) /* Protecting Posts `all-[of-this-type]` ( don't forget plural `s` )? */
													? '<option value="'.$n.'"'.((in_array($post_id, $posts[$n])) ? ' selected="selected"' : '').'>'.(($n === $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]) ? 'Require Highest '.$levelText : 'Require '.$levelText.' ( or higher )').'</option>'."\n"/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Posts of this type )</option>'."\n")/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Posts )</option>'."\n";
												}
												/**/
												echo '</select><br />'."\n";
												/**/
												if(!is_multisite() || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() || is_main_site())
													/* ^ Will change once Custom Capabilities are compatible with a Blog Farm. */
													{
														echo '<p style="margin-top:15px; margin-left:2px;"><strong>Require Product Packages?</strong></p>'."\n";
														//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-ccaps">Custom Capabilities?</label>'."\n";
														if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
														foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
															$ccaps = get_post_meta($post->ID, "optimizemember_ccaps_req", true);
															$checked = '';
															if (is_array($ccaps) and in_array($val, $ccaps)) {
																$checked='checked="checked"';
															}
															echo '<input type="checkbox" '.$checked.' onchange="showSelectedValues();" name="ws_plugin__optimizemember_ccpchk" value="'.$val.'" /> '. $val . "\n";
															//echo $val . '<br />' . "\n";
														}
														} else {
															echo 'To use packages, enter some first in <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--optimizemember-gen-ops")).'">Define membership levels and packages section.</a>';
														}
														echo '<input type="hidden" autocomplete="off" name="ws_plugin__optimizemember_security_meta_box_ccaps" id="ws-plugin--optimizemember-security-meta-box-ccaps" value="" />'."\n";
														//echo '<br /><small>* see: <code>API Scripting -> Custom Capabilities</code></small>'."\n";
														?>

														<p><?php _e('Delay content for ', OP_SN);?>
														<?php 
															global $post;
															$drip_days = get_post_meta($post->ID, "optimizemember_drip_days", true);
															$drip_redirect_url = get_post_meta($post->ID, "optimizemember_drip_redirect_url", true);
														?>
														<input style="display:inline; width:50px;" type="text" name="ws_plugin__optimizemember_drip_days" value="<?php echo $drip_days; ?>" />
														<?php _e('days', OP_SN);?>.</p>
                                                        <p><?php _e('Delay content works with Level 1 or above.', OP_SN); ?><br /><a target="_blank" href="https://optimizepress.zendesk.com/hc/en-us/articles/201187926"><?php _e('See our guide to delay content', OP_SN); ?></a></p>
														<label class="form-title" for="ws_plugin__optimizemember_drip_redirect"><?php _e('Redirect to:', OP_SN);?></label>
														 
														<select name="ws_plugin__optimizemember_drip_redirect_url">
														<option value="">---</option>
														<?php 
															$availablePages = get_pages();
															foreach ($availablePages as $page) {
																$selected = "";
																if ($page->ID != $post->ID) { // current page is excluded
																	if ($drip_redirect_url == get_page_link($page->ID)) {
																		$selected = 'selected="selected"';
																	}
																	$option = '<option '.$selected.' value="' . get_page_link( $page->ID ) . '">';
																	$option .= $page->post_title;
																	$option .= '</option>';
																	echo $option;
																}
															}
														?>
														</select>
														<script type="text/javascript">
														/**/
														showSelectedValues();
														function showSelectedValues()
														{
															var text = jQuery("input[name=ws_plugin__optimizemember_ccpchk]:checked").map(
															     function () {return this.value;}).get().join(",");

															jQuery("#ws-plugin--optimizemember-security-meta-box-ccaps").val(text);
														}
														/**/
														</script>
														<?php
													}
												/**/
												echo '<p style="margin-left:2px;"><strong>Force SSL?</strong></p>'."\n";
												echo '<input type="checkbox"' . checked(get_post_meta($post_id, 'optimizemember_force_ssl', true), 'yes', false) . 'name="ws_plugin__optimizemember_force_ssl" value="yes" /> Yes' . "\n";
											}
										/**/
										else if(in_array($post_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"])))
											echo 'This Post is a:<br /><strong>Specific Post/Page for sale</strong><br />( already guarded by optimizeMember )';
									}
							}
						/**/
						do_action("ws_plugin__optimizemember_after_security_meta_box", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
					
			/**
				* Adds OptimizeMember options to OptimizePress Theme
				* @param obj $post Post/Page object.
				* @return null
				*/
				public static function optimizeMemberOptions($post = FALSE)
					{
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action("ws_plugin__optimizemember_before_security_meta_box", get_defined_vars());
						unset($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if(is_object($post) && ($post_id = $post->ID) && (($post->post_type === "page" && current_user_can("edit_page", $post_id)) || current_user_can("edit_post", $post_id)))
							{
								if($post->post_type === "page" && ($page_id = $post_id)) /* OK. So we're dealing with a Page classification. */
									{
										if(!in_array($page_id, array_merge(array($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"], $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_welcome_page"], $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["file_download_limit_exceeded_page"]), preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"]))))
											{
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save" id="ws-plugin--optimizemember-security-meta-box-save" value="'.esc_attr(wp_create_nonce("ws-plugin--optimizemember-security-meta-box-save")).'" />'."\n";
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save_id" id="ws-plugin--optimizemember-security-meta-box-save-id" value="'.esc_attr($page_id).'" />'."\n";
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
													$pages[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level".$n."_pages"]));
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
													$posts[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level".$n."_posts"]));
												/**/
												echo '<p style="margin-left:2px;"><strong>Page Level Restriction?</strong></p>'."\n";
												//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-level">Add Level Restriction?</label>'."\n";
												echo '<select name="ws_plugin__optimizemember_security_meta_box_level" id="ws-plugin--optimizemember-security-meta-box-level" style="width:99%;">'."\n";
												echo '<option value=""></option>'."\n"; /* By default, we allow public access to any Post/Page. */
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++) {
													$levelText = ws_plugin__optimizemember_getMembershipLabel($n);
													echo ($pages[$n] !== array("all")) ? /* Protecting `all` Pages, of any kind? */
													((!in_array("all-pages", $posts[$n])) /* Protecting Posts of type: `page` ( i.e. `all-pages` )? */
													? '<option value="'.$n.'"'.((in_array($page_id, $pages[$n])) ? ' selected="selected"' : '').'>'.(($n === $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]) ? 'Require Highest '.$levelText : 'Require '.$levelText.' ( or higher )').'</option>'."\n"/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Posts of this type )</option>'."\n")/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Pages )</option>'."\n";
												}
												/**/
												echo '</select><br />'."\n";
												/**/
												if(!is_multisite() || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() || is_main_site())
													/* ^ Will change once Custom Capabilities are compatible with a Blog Farm. */
													{
														echo '<p style="margin-top:15px; margin-left:2px;"><strong>Require Product Packages?</strong></p>'."\n";
														//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-ccaps">Custom Capabilities?</label>'."\n";
														if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
														foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
															$ccaps = get_post_meta($page_id, "optimizemember_ccaps_req", true);
															$checked = '';
															if (is_array($ccaps) and in_array($val, $ccaps)) {
																$checked='checked="checked"';
															}
                                                            echo '<label style="display:block;overflow:hidden;"><input type="checkbox" '.$checked.' onchange="showSelectedValues();" name="ws_plugin__optimizemember_ccpchk" value="'.$val.'" /> '.$val.'</label>' . "\n";
                                                            //echo $val . '<br />' . "\n";
														}
														} else {
															echo 'To use packages, enter some first in <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--optimizemember-gen-ops")).'">Define membership levels and packages section.</a>';
														}
														echo '<input type="hidden" autocomplete="off" name="ws_plugin__optimizemember_security_meta_box_ccaps" id="ws-plugin--optimizemember-security-meta-box-ccaps" value="" />'."\n";
														//echo '<br /><small>* see: <code>API Scripting -> Custom Capabilities</code></small>'."\n";
														?>
														<br /><br />
														<h2><?php _e('Content dripping', OP_SN); ?></h2>
														<p class="op-micro-copy"><?php _e('Content Dripping is the gradual, pre-scheduled release of premium website content to paying Members. Drip content requires content to be protected at Level 1 or above.', OP_SN);?><a target="_blank" href="https://optimizepress.zendesk.com/hc/en-us/articles/201187926"><?php _e('See our guide here', OP_SN);?></a></p>
														<p><?php _e('Delay content for ', OP_SN);?>
														<?php 
															global $post;
															$drip_days = get_post_meta($post->ID, "optimizemember_drip_days", true);
															$drip_redirect_url = get_post_meta($post->ID, "optimizemember_drip_redirect_url", true);
														?>
														<input style="display:inline; width:50px;" type="text" name="ws_plugin__optimizemember_drip_days" value="<?php echo $drip_days; ?>" />
														<?php _e('days', OP_SN);?>.</p>
                                                        <p><?php _e('Delay content works with Level 1 or above.', OP_SN); ?><br /><a target="_blank" href="https://optimizepress.zendesk.com/hc/en-us/articles/201187926"><?php _e('See our guide to delay content', OP_SN); ?></a></p>
														<label class="form-title" for="ws_plugin__optimizemember_drip_redirect"><?php _e('Redirect to if content not available', OP_SN);?></label>
														<p class="op-micro-copy"><?php _e('Select the page to which users will be redirected to if above entered number of days has not been reached', OP_SN);?></p>
														<select name="ws_plugin__optimizemember_drip_redirect_url">
														<option value="">---</option>
														<?php 
															$availablePages = get_pages(); 
															foreach ($availablePages as $page) {
																$selected = "";
																if ($page->ID != $post->ID) { // current page is excluded
																	if ($drip_redirect_url == get_page_link($page->ID)) {
																		$selected = 'selected="selected"';
																	}
																	$option = '<option '.$selected.' value="' . get_page_link( $page->ID ) . '">';
																	$option .= $page->post_title;
																	$option .= '</option>';
																	echo $option;
																}
															}
														?>
														</select>
														
														<script type="text/javascript">
														showSelectedValues();
														/**/
														function showSelectedValues()
														{
															var text = jQuery("input[name=ws_plugin__optimizemember_ccpchk]:checked").map(
															     function () {return this.value;}).get().join(",");

															jQuery("#ws-plugin--optimizemember-security-meta-box-ccaps").val(text);
														}
														/**/
														</script>
														<?php 
													}

												echo '<label class="form-title" for="ws_plugin__optimizemember_force_ssl">Force SSL?</label>'."\n";
												echo '<input type="checkbox"' . checked(get_post_meta($page_id, 'optimizemember_force_ssl', true), 'yes', false) . ' name="ws_plugin__optimizemember_force_ssl" value="yes" /> Yes' . "\n";
											}
										/**/
										else if($page_id == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["membership_options_page"])
											echo 'This Page is your:<br /><strong>Membership Options Page</strong><br />( always publicly available )';
										/**/
										else if($page_id == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["login_welcome_page"])
											echo 'This Page is your:<br /><strong>Login Welcome Page</strong><br />( automatically guarded by optimizeMember )';
										/**/
										else if($page_id == $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["file_download_limit_exceeded_page"])
											echo 'This Page is your:<br /><strong>Download Limit Exceeded Page</strong><br />( automatically guarded by optimizeMember )';
										/**/
										else if(in_array($page_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"])))
											echo 'This Page is a:<br /><strong>Specific Post/Page for sale</strong><br />( already guarded by optimizeMember )';
									}
								else /* Otherwise, we assume this is a Post, or possibly a Custom Post Type. It's NOT a Page. */
									{
										if(!in_array($post_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"])))
											{
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save" id="ws-plugin--optimizemember-security-meta-box-save" value="'.esc_attr(wp_create_nonce("ws-plugin--optimizemember-security-meta-box-save")).'" />'."\n";
												echo '<input type="hidden" name="ws_plugin__optimizemember_security_meta_box_save_id" id="ws-plugin--optimizemember-security-meta-box-save-id" value="'.esc_attr($post_id).'" />'."\n";
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++)
													$posts[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["level".$n."_posts"]));
												/**/
												echo '<p style="margin-left:2px;"><strong>Post Level Restriction?</strong></p>'."\n";
												//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-level">Add Level Restriction?</label>'."\n";
												echo '<select name="ws_plugin__optimizemember_security_meta_box_level" id="ws-plugin--optimizemember-security-meta-box-level" style="width:99%;">'."\n";
												echo '<option value=""></option>'."\n"; /* By default, we allow public access to any Post/Page. */
												/**/
												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]; $n++) {
													$levelText = ws_plugin__optimizemember_getMembershipLabel($n);
													echo ($posts[$n] !== array("all")) ? /* Protecting `all` Posts, of any kind? */
													((!in_array("all-".$post->post_type."s", $posts[$n])) /* Protecting Posts `all-[of-this-type]` ( don't forget plural `s` )? */
													? '<option value="'.$n.'"'.((in_array($post_id, $posts[$n])) ? ' selected="selected"' : '').'>'.(($n === $GLOBALS["WS_PLUGIN__"]["optimizemember"]["c"]["levels"]) ? 'Require Highest '.$levelText : 'Require '.$levelText.' ( or higher )').'</option>'."\n"/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Posts of this type )</option>'."\n")/**/
													: '<option value="" disabled="disabled">Level '.$levelText.' ( already protects "all" Posts )</option>'."\n";
												}
												/**/
												//echo '</select><br /><small>* see: <code>Restriction Options -> Posts</code></small>'."\n";
												/**/
												if(!is_multisite() || !c_ws_plugin__optimizemember_utils_conds::is_multisite_farm() || is_main_site())
													/* ^ Will change once Custom Capabilities are compatible with a Blog Farm. */
													{
														echo '<p style="margin-top:15px; margin-left:2px;"><strong>Require Product Packages?</strong></p>'."\n";
														//echo '<label class="screen-reader-text" for="ws-plugin--optimizemember-security-meta-box-ccaps">Custom Capabilities?</label>'."\n";
														if (count($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"]) > 0) {
														foreach($GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["ccp"] as $key => $val) {
															$ccaps = get_post_meta($post->ID, "optimizemember_ccaps_req", true);
															$checked = '';
															if (is_array($ccaps) and in_array($val, $ccaps)) {
																$checked='checked="checked"';
															}
															echo '<input type="checkbox" '.$checked.' onchange="showSelectedValues();" name="ws_plugin__optimizemember_ccpchk" value="'.$val.'" /> '. $val . "\n";
															//echo $val . '<br />' . "\n";
														}
														} else {
															echo 'To use packages, enter some first in <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--optimizemember-gen-ops")).'">Define membership levels and packages section.</a>';
														}
														echo '<input type="hidden" autocomplete="off" name="ws_plugin__optimizemember_security_meta_box_ccaps" id="ws-plugin--optimizemember-security-meta-box-ccaps" value="" />'."\n";
														//echo '<br /><small>* see: <code>API Scripting -> Custom Capabilities</code></small>'."\n";
														?>
														<br /><br />
														<h2><?php _e('Content dripping', OP_SN); ?></h2>
														<p class="op-micro-copy"><?php _e('Content Dripping is the gradual, pre-scheduled release of premium website content to paying Members.', OP_SN);?></p>
														<p><?php _e('Delay content for ', OP_SN);?>
														<?php 
															global $post;
															$drip_days = get_post_meta($post->ID, "optimizemember_drip_days", true);
															$drip_redirect_url = get_post_meta($post->ID, "optimizemember_drip_redirect_url", true);
														?>
														<input style="display:inline; width:50px;" type="text" name="ws_plugin__optimizemember_drip_days" value="<?php echo $drip_days; ?>" />
														<?php _e('days', OP_SN);?>.</p>
                                                        <p><?php _e('Delay content works with Level 1 or above.', OP_SN); ?><br /><a target="_blank" href="https://optimizepress.zendesk.com/hc/en-us/articles/201187926"><?php _e('See our guide to delay content', OP_SN); ?></a></p>
														<label class="form-title" for="ws_plugin__optimizemember_drip_redirect"><?php _e('Redirect to if content not available', OP_SN);?></label>
														<p class="op-micro-copy"><?php _e('Select the page to which users will be redirected to if above entered number of days has not been reached', OP_SN);?></p>
														<select name="ws_plugin__optimizemember_drip_redirect_url">
														<option value="">---</option>
														<?php 
															$availablePages = get_pages(); 
															foreach ($availablePages as $page) {
																$selected = "";
																if ($page->ID != $post->ID) { // current page is excluded
																	if ($drip_redirect_url == get_page_link($page->ID)) {
																		$selected = 'selected="selected"';
																	}
																	$option = '<option '.$selected.' value="' . get_page_link( $page->ID ) . '">';
																	$option .= $page->post_title;
																	$option .= '</option>';
																	echo $option;
																}
															}
														?>
														</select>
														<script type="text/javascript">
														/**/
														showSelectedValues();
														function showSelectedValues()
														{
															var text = jQuery("input[name=ws_plugin__optimizemember_ccpchk]:checked").map(
															     function () {return this.value;}).get().join(",");

															jQuery("#ws-plugin--optimizemember-security-meta-box-ccaps").val(text);
														}
														/**/
														</script>
														<?php
													}

												echo '<label class="form-title" for="ws_plugin__optimizemember_force_ssl">Force SSL?</label>'."\n";
												echo '<input type="checkbox"' . checked(get_post_meta($post_id, 'optimizemember_force_ssl', true), 'yes', false) . 'name="ws_plugin__optimizemember_force_ssl" value="yes" /> Yes' . "\n";
											}
										/**/
										else if(in_array($post_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["optimizemember"]["o"]["specific_ids"])))
											echo 'This Post is a:<br /><strong>Specific Post/Page for sale</strong><br />( already guarded by optimizeMember )';
									}
							}
						/**/
						do_action("ws_plugin__optimizemember_after_security_meta_box", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>