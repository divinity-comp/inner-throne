<?php
/**
 * Membership Levels Page
 *
 * @package     Restrict Content Pro
 * @subpackage  Admin/Membership Levels
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Render membership levels page
 *
 * @return void
 */
function rcp_member_levels_page() {
	global $rcp_options, $rcp_db_name, $wpdb, $rcp_levels_db;
	$page   = admin_url( '/admin.php?page=rcp-member-levels' );
	$status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';

	// Query counts.
	$all_count      = $rcp_levels_db->count();
	$active_count   = $rcp_levels_db->count( array( 'status' => 'active' ) );
	$inactive_count = $rcp_levels_db->count( array( 'status' => 'inactive' ) );
	?>
	<div class="wrap">
		<?php if(isset($_GET['edit_subscription'])) :
			include('edit-subscription.php');
		else : ?>
			<h1><?php _e('Membership Levels', 'rcp'); ?></h1>

			<ul class="subsubsub">
				<li>
					<a href="<?php echo esc_url( remove_query_arg( 'status', $page ) ); ?>" title="<?php esc_attr_e( 'View all membership levels', 'rcp' ); ?>"<?php echo 'all' == $status ? ' class="current"' : ''; ?>>
						<?php _e( 'All', 'rcp' ); ?>
						<span class="count">(<?php echo $all_count; ?>)</span>
					</a>
				</li>
				<?php if ( $active_count > 0 ) : ?>
					<li>
						|<a href="<?php echo esc_url( add_query_arg( 'status', 'active', $page ) ); ?>" title="<?php esc_attr_e( 'View active membership levels', 'rcp' ); ?>"<?php echo 'active' == $status ? ' class="current"' : ''; ?>>
							<?php _e( 'Active', 'rcp' ); ?>
							<span class="count">(<?php echo $active_count; ?>)</span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $inactive_count > 0 ) : ?>
					<li>
						|<a href="<?php echo esc_url( add_query_arg( 'status', 'inactive', $page ) ); ?>" title="<?php esc_attr_e( 'View inactive membership levels', 'rcp' ); ?>"<?php echo 'inactive' == $status ? ' class="current"' : ''; ?>>
							<?php _e( 'Inactive', 'rcp' ); ?>
							<span class="count">(<?php echo $inactive_count; ?>)</span>
						</a>
					</li>
				<?php endif; ?>
			</ul>

			<table class="wp-list-table widefat fixed posts rcp-subscriptions">
				<thead>
					<tr>
						<th scope="col" class="rcp-sub-name-col column-primary"><?php _e( 'Name', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-desc-col"><?php _e( 'Description', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-status-col"><?php _e( 'Status', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-level-col"><?php _e( 'Access Level', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-duration-col"><?php _e( 'Duration', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-price-col"><?php _e( 'Price', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-subs-col"><?php _e( 'Memberships', 'rcp' ); ?></th>
						<?php do_action('rcp_levels_page_table_header'); ?>
						<th scope="col" class="rcp-sub-order-col"><?php _e( 'Order', 'rcp' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
				<?php $levels = rcp_get_subscription_levels( $status ); ?>
				<?php
				if($levels) :
					$i = 1;
					foreach( $levels as $key => $level) : ?>
						<tr id="recordsArray_<?php echo $level->id; ?>" class="rcp-subscription rcp_row <?php if(rcp_is_odd($i)) { echo 'alternate'; } ?>">
							<td class="rcp-sub-name-col column-primary has-row-actions" data-colname="<?php esc_attr_e( 'Name', 'rcp' ); ?>">
								<strong><a href="<?php echo esc_url( add_query_arg( 'edit_subscription', $level->id, $page ) ); ?>"><?php echo stripslashes( $level->name ); ?></a></strong>
								<?php if( current_user_can( 'rcp_manage_levels' ) ) : ?>
									<div class="row-actions">
										<a href="<?php echo esc_url( add_query_arg('edit_subscription', $level->id, $page) ); ?>"><?php _e('Edit', 'rcp'); ?></a> |
										<?php if($level->status != 'inactive') { ?>
											<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'rcp-action' => 'deactivate_subscription', 'level_id' => $level->id ), $page ), 'rcp-deactivate-subscription-level' ) ); ?>"><?php _e('Deactivate', 'rcp'); ?></a> |
										<?php } else { ?>
											<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'rcp-action' => 'activate_subscription', 'level_id' => $level->id ), $page ), 'rcp-activate-subscription-level' ) ); ?>"><?php _e('Activate', 'rcp'); ?></a> |
										<?php } ?>
										<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'rcp-action' => 'delete_subscription', 'level_id' => $level->id ), $page ), 'rcp-delete-subscription-level' ) ); ?>" class="rcp_delete_subscription"><?php _e('Delete', 'rcp'); ?></a></span> |
										<span class="rcp-sub-id-col rcp-id-col" data-colname="<?php esc_attr_e( 'ID:', 'rcp' ); ?>"> <?php echo __( 'ID:', 'rcp' ) . ' ' . $level->id; ?></span>
										<?php do_action( 'rcp_membership_level_row_actions', $level ); ?>
									</div>
								<?php endif; ?>
								<button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e( 'Show more details', 'rcp' ); ?></span></button>
							</td>
							<td class="rcp-sub-desc-col" data-colname="<?php esc_attr_e( 'Description', 'rcp' ); ?>"><?php echo stripslashes( $level->description ); ?></td>
							<td class="rcp-sub-status-col" data-colname="<?php esc_attr_e( 'Status', 'rcp' ); ?>"><?php echo ucwords( $level->status ); ?></td>
							<td class="rcp-sub-level-col" data-colname="<?php esc_attr_e( 'Access Level', 'rcp' ); ?>"><?php echo $level->level != '' ? $level->level : __('none', 'rcp'); ?></td>
							<td class="rcp-sub-duration-col" data-colname="<?php esc_attr_e( 'Duration', 'rcp' ); ?>">
								<?php
									if($level->duration > 0) {
										echo $level->duration . ' ' . rcp_filter_duration_unit($level->duration_unit, $level->duration);
									} else {
										echo __('unlimited', 'rcp');
									}
								?>
							</td>
							<td class="rcp-sub-price-col" data-colname="<?php esc_attr_e( 'Price', 'rcp' ); ?>">
								<?php
								$price = rcp_get_subscription_price( $level->id );
								if( ! $price ) {
									echo __( 'Free', 'rcp' );
								} else {
									echo rcp_currency_filter( $price );
								}
								?>
							</td>
							<td class="rcp-sub-subs-col" data-colname="<?php esc_attr_e( 'Memberships', 'rcp' ); ?>">
								<?php
								echo rcp_count_memberships( array(
									'status__in' => array( 'active', 'cancelled' ),
									'object_id'  => $level->id
								) );
								?>
							</td>
							<?php do_action('rcp_levels_page_table_column', $level->id); ?>
							<td class="rcp-sub-order-col"><a href="#" class="rcp-drag-handle"></a></td>
						</tr>
					<?php $i++;
					endforeach;
				else : ?>
					<tr><td colspan="9"><?php _e('No membership levels added yet.', 'rcp'); ?></td></tr>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="col" class="rcp-sub-name-col column-primary"><?php _e( 'Name', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-desc-col"><?php _e( 'Description', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-status-col"><?php _e( 'Status', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-level-col"><?php _e( 'Access Level', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-duration-col"><?php _e( 'Duration', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-price-col"><?php _e( 'Price', 'rcp' ); ?></th>
						<th scope="col" class="rcp-sub-subs-col"><?php _e( 'Memberships', 'rcp' ); ?></th>
						<?php do_action('rcp_levels_page_table_footer'); ?>
						<th scope="col" class="rcp-sub-order-col"><?php _e( 'Order', 'rcp' ); ?></th>
					</tr>
				</tfoot>
			</table>
			<?php do_action('rcp_levels_below_table'); ?>
			<?php if( current_user_can( 'rcp_manage_levels' ) ) : ?>
				<h2><?php _e('Add New Level', 'rcp'); ?></h2>
				<form id="rcp-member-levels" action="" method="post">
					<table class="form-table">
						<tbody>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-name"><?php _e('Name', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-name" name="name" value=""/>
									<p class="description"><?php _e('The name of the membership level.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-description"><?php _e('Description', 'rcp'); ?></label>
								</th>
								<td>
									<textarea id="rcp-description" name="description"></textarea>
									<p class="description"><?php _e('Membership level description. This is shown on the registration form.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-level"><?php _e('Access Level', 'rcp'); ?></label>
								</th>
								<td>
									<select id="rcp-level" name="level">
										<?php
										$access_levels = rcp_get_access_levels();
										foreach( $access_levels as $access ) {
											echo '<option value="' . $access . '">' . $access . '</option>';
										}
										?>
									</select>
									<p class="description">
										<?php _e('Level of access this membership gives. Leave None for default or you are unsure what this is.', 'rcp'); ?>
										<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php _e( '<strong>Access Level</strong>: refers to a tiered system where a member\'s ability to view content is determined by the access level assigned to their account. A member with an access level of 5 can view content assigned to access levels of 5 and lower, whereas a member with an access level of 4 can only view content assigned to levels of 4 and lower.', 'rcp' ); ?>"></span>
									</p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-duration"><?php _e('Duration', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-duration" name="duration" value="0"/>
									<select name="duration_unit" id="rcp-duration-unit">
										<option value="day"><?php _e('Day(s)', 'rcp'); ?></option>
										<option value="month"><?php _e('Month(s)', 'rcp'); ?></option>
										<option value="year"><?php _e('Year(s)', 'rcp'); ?></option>
									</select>
									<p class="description">
										<?php _e('Length of time for this membership level. Enter 0 for unlimited.', 'rcp'); ?>
										<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php _e( '<strong>Example</strong>: setting this to 1 month would make memberships last 1 month, after which they will renew automatically or be marked as expired.', 'rcp' ); ?>"></span>
									</p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-maximum-renewals-setting"><?php _e( 'Maximum Renewals', 'rcp' ); ?></label>
								</th>
								<td>
									<select name="maximum_renewals_setting" id="rcp-maximum-renewals-setting">
										<option value="forever"><?php _e( 'Until Cancelled', 'rcp' ); ?></option>
										<option value="specific"><?php _e( 'Specific Number', 'rcp' ); ?></option>
									</select>
									<label for="rcp-maximum-renewals" class="screen-reader-text"><?php _e( 'Enter the maximum number of renewals', 'rcp' ); ?></label>
									<input type="number" id="rcp-maximum-renewals" name="maximum_renewals" value="0" style="display:none;"/>
									<p class="description">
										<?php _e( 'Number of renewals to process after the first payment.', 'rcp' ); ?>
										<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( '<strong>Until Cancelled</strong>: will continue billing the member indefinitely, or until they cancel their membership. <br/><br/><strong>Specific Number</strong> will allow you to enter the number of additional times you wish to bill the customer after their first payment. If you enter "3", the member will be billed once immediately when they sign up, then 3 more times after that. Then billing will stop automatically.', 'rcp' ); ?>"></span>
									</p>
								</td>
							</tr>
							<tr class="form-field" style="display: none;">
								<th scope="row" valign="top">
									<label for="rcp-after-final-payment"><?php _e( 'After Final Payment', 'rcp' ); ?></label>
								</th>
								<td>
									<select name="after_final_payment" id="rcp-after-final-payment">
										<option value="lifetime"><?php _e( 'Grant Lifetime Access', 'rcp' ); ?></option>
										<option value="expire_immediately"><?php _e( 'End Membership Immediately', 'rcp' ); ?></option>
										<option value="expire_term_end"><?php _e( 'End Membership at End of Billing Period', 'rcp' ); ?></option>
									</select>
									<p class="description">
										<?php _e( 'Action to take after the final payment has been received.', 'rcp'); ?>
										<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( '<strong>Grant Lifetime Access</strong>: will update the member\'s expiration date to "none" to give them lifetime access to restricted content. <br/><br/><strong>End Membership Immediately</strong>: will make the user\'s membership expire immediately after the final payment is received and they will lose access to restricted content. <br/><br/><strong>End Membership at End of Billing Period</strong>: will allow the user to complete one more period after the final payment, after which their membership will expire. For example, if the membership duration is set to 1 month, the user will make their final payment then have access for 1 more month after that before expiring.', 'rcp' ); ?>"></span>
									</p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="trial_duration"><?php _e('Free Trial Duration', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="trial_duration" name="trial_duration" value="0"/>
									<select name="trial_duration_unit" id="trial_duration_unit">
										<option value="day"><?php _e('Day(s)', 'rcp'); ?></option>
										<option value="month"><?php _e('Month(s)', 'rcp'); ?></option>
										<option value="year"><?php _e('Year(s)', 'rcp'); ?></option>
									</select>
									<p class="description">
										<?php _e('Length of time the free trial should last. Enter 0 for no free trial.', 'rcp'); ?>
										<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php _e( '<strong>Example</strong>: setting this to 7 days would give the member a 7-day free trial. The member would be billed at the end of the trial.<p><strong>Note:</strong> If you enable a free trial, the regular membership duration and price must be greater than 0.</p>', 'rcp' ); ?>"></span>
									</p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-price"><?php _e('Price', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-price" name="price" value="0" pattern="^(\d+\.\d{1,2})|(\d+)$"/>
									<p class="description">
										<?php _e('The price of this membership level. Enter 0 for free.', 'rcp'); ?>
										<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php _e( 'This price refers to the amount paid per duration period. For example, if duration period is set to 1 month, this would be the amount charged each month.', 'rcp' ); ?>"></span>
									</p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-fee"><?php _e('Signup Fee', 'rcp'); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-fee" name="fee" value="0"/>
									<p class="description"><?php _e('Optional signup fee to charge subscribers for the first billing cycle. Enter a negative number to give a discount on the first payment.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-status"><?php _e('Status', 'rcp'); ?></label>
								</th>
								<td>
									<select name="status" id="rcp-status">
										<option value="active"><?php _e('Active', 'rcp'); ?></option>
										<option value="inactive"><?php _e('Inactive', 'rcp'); ?></option>
									</select>
									<p class="description"><?php _e('Members may only sign up for active membership levels.', 'rcp'); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-role"><?php _e( 'User Role', 'rcp' ); ?></label>
								</th>
								<td>
									<select name="role" id="rcp-role">
										<?php wp_dropdown_roles( 'subscriber' ); ?>
									</select>
									<p class="description"><?php _e( 'The user role given to the member after signing up.', 'rcp' ); ?></p>
								</td>
							</tr>
							<?php do_action( 'rcp_add_subscription_form' ); ?>
						</tbody>
					</table>
					<p class="submit">
						<input type="hidden" name="rcp-action" value="add-level"/>
						<input type="submit" value="<?php _e('Add Membership Level', 'rcp'); ?>" class="button-primary"/>
					</p>
					<?php wp_nonce_field( 'rcp_add_level_nonce', 'rcp_add_level_nonce' ); ?>
				</form>
			<?php endif; ?>
		<?php endif; ?>
	</div><!--end wrap-->

	<?php
}
