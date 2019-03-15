<?php
/**
 * Discount Codes Page
 *
 * @package     Restrict Content Pro
 * @subpackage  Admin/Discount Codes
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Render the discounts table
 *
 * @return void
 */
function rcp_discounts_page() {
	/**
	 * @var RCP_Discounts $rcp_discounts_db
	 */
	global $rcp_discounts_db;
	$page   = admin_url( '/admin.php?page=rcp-discounts' );
	$status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';

	// Query counts.
	$all_count      = $rcp_discounts_db->count();
	$active_count   = $rcp_discounts_db->count( array( 'status' => 'active' ) );
	$inactive_count = $rcp_discounts_db->count( array( 'status' => 'disabled' ) );
	?>
	<div class="wrap">
		<?php if( isset( $_GET['edit_discount'] ) ) :
			include('edit-discount.php');
		else : ?>
			<h1><?php _e( 'Discount Codes', 'rcp' ); ?></h1>

			<ul class="subsubsub">
				<li>
					<a href="<?php echo esc_url( remove_query_arg( 'status', $page ) ); ?>" title="<?php esc_attr_e( 'View all discount codes', 'rcp' ); ?>"<?php echo 'all' == $status ? ' class="current"' : ''; ?>>
						<?php _e( 'All', 'rcp' ); ?>
						<span class="count">(<?php echo $all_count; ?>)</span>
					</a>
				</li>
				<?php if ( $active_count > 0 ) : ?>
					<li>
						|<a href="<?php echo esc_url( add_query_arg( 'status', 'active', $page ) ); ?>" title="<?php esc_attr_e( 'View active discount codes', 'rcp' ); ?>"<?php echo 'active' == $status ? ' class="current"' : ''; ?>>
							<?php _e( 'Active', 'rcp' ); ?>
							<span class="count">(<?php echo $active_count; ?>)</span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $inactive_count > 0 ) : ?>
					<li>
						|<a href="<?php echo esc_url( add_query_arg( 'status', 'disabled', $page ) ); ?>" title="<?php esc_attr_e( 'View inactive discount codes', 'rcp' ); ?>"<?php echo 'disabled' == $status ? ' class="current"' : ''; ?>>
							<?php _e( 'Inactive', 'rcp' ); ?>
							<span class="count">(<?php echo $inactive_count; ?>)</span>
						</a>
					</li>
				<?php endif; ?>
			</ul>

			<table class="wp-list-table widefat posts">
				<thead>
					<tr>
						<th scope="col" class="rcp-discounts-name-col column-primary" ><?php _e( 'Name', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-desc-col"><?php _e( 'Description', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-code-col" ><?php _e( 'Code', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-subscription-col" ><?php _e( 'Membership Levels', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-amount-col"><?php _e( 'Amount', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-type-col"><?php _e( 'Type', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-status-col"><?php _e( 'Status', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-col"><?php _e( 'Uses', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-left-col"><?php _e( 'Uses Left', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-expir-col" ><?php _e( 'Expiration', 'rcp' ); ?></th>
						<?php do_action( 'rcp_discounts_page_table_header' ); ?>
					</tr>
				</thead>
				<tbody>
				<?php $codes = rcp_get_discounts( array( 'status' => $status ) ); ?>
				<?php
				if($codes) :
					$i = 1;
					foreach( $codes as $key => $code) : ?>
						<tr class="rcp_row <?php if( rcp_is_odd( $i ) ) { echo 'alternate'; } ?>">
							<td class="column-primary has-row-actions" data-colname="<?php esc_attr_e( 'Name', 'rcp' ); ?>">
								<strong><a href="<?php echo esc_url( add_query_arg( 'edit_discount', $code->id, $page ) ); ?>"><?php echo stripslashes( $code->name ); ?></a></strong>
								<div class="row-actions">
									<?php if( current_user_can( 'rcp_manage_discounts' ) ) : ?>
										<a href="<?php echo esc_url( add_query_arg( 'edit_discount', $code->id, $page ) ); ?>"><?php _e( 'Edit', 'rcp' ); ?></a> |
										<?php if( $code->status === 'active' ) { ?>
											<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'rcp-action' => 'deactivate_discount', 'discount_id' => $code->id ), $page ), 'rcp-deactivate-discount' ) ); ?>"><?php _e( 'Deactivate', 'rcp' ); ?></a> |
										<?php } else { ?>
											<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'rcp-action' => 'activate_discount', 'discount_id' => $code->id ), $page ), 'rcp-activate-discount' ) ); ?>"><?php _e( 'Activate', 'rcp' ); ?></a> |
										<?php } ?>
										<span class="trash"><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'rcp-action' => 'delete_discount_code', 'discount_id' => $code->id ), $page ), 'rcp-delete-discount' ) ); ?>" class="rcp_delete_discount"><?php _e( 'Delete', 'rcp' ); ?></a></span> |
										<span class="id rcp-id-col"><?php echo __( 'ID:', 'rcp' ) . ' ' . $code->id; ?></span>
									<?php endif; ?>
								</div>
								<button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e( 'Show more details', 'rcp' ); ?></span></button>
							</td>
							<td data-colname="<?php esc_attr_e( 'Description', 'rcp' ); ?>"><?php echo stripslashes( $code->description ); ?></td>
							<td data-colname="<?php esc_attr_e( 'Code', 'rcp' ); ?>"><?php echo $code->code; ?></td>
							<td data-colname="<?php esc_attr_e( 'Membership Levels', 'rcp' ); ?>">
								<?php
								$membership_levels = maybe_unserialize( $code->membership_level_ids );
								if ( ! empty( $membership_levels ) && count( $membership_levels ) > 1 ) {
									_e( 'Multiple Levels', 'rcp' );
								} elseif( ! empty( $membership_levels ) && 1 === count( $membership_levels ) ) {
									echo rcp_get_subscription_name( $membership_levels[0] );
								} else {
									echo __( 'All Levels', 'rcp' );
								}
								?>
							</td>
							<td data-colname="<?php esc_attr_e( 'Amount', 'rcp' ); ?>"><?php echo rcp_discount_sign_filter( $code->amount, $code->unit ); ?></td>
							<td data-colname="<?php esc_attr_e( 'Type', 'rcp' ); ?>"><?php echo $code->unit == '%' ? __( 'Percentage', 'rcp' ) : __( 'Flat', 'rcp' ); ?></td>
							<td data-colname="<?php esc_attr_e( 'Status', 'rcp' ); ?>">
								<?php
									if(rcp_is_discount_not_expired( $code->id ) ) {
										echo $code->status === 'active' ? __( 'active', 'rcp' ) : __( 'disabled', 'rcp' );
									} else {
										_e( 'expired', 'rcp' );
									}
								?>
							</td>
							<td data-colname="<?php esc_attr_e( 'Uses', 'rcp' ); ?>"><?php if( $code->max_uses > 0 ) { echo rcp_count_discount_code_uses( $code->code ) . '/' . $code->max_uses; } else { echo rcp_count_discount_code_uses( $code->code ); }?></td>
							<td data-colname="<?php esc_attr_e( 'Uses Left', 'rcp' ); ?>"><?php echo rcp_discount_has_uses_left( $code->id ) ? 'yes' : 'no'; ?></td>
							<td data-colname="<?php esc_attr_e( 'Expiration', 'rcp' ); ?>"><?php echo $code->expiration == '' ? __( 'none', 'rcp' ) : date_i18n( 'Y-m-d H:i:s', strtotime( $code->expiration, current_time( 'timestamp' ) ) ); ?></td>
							<?php do_action('rcp_discounts_page_table_column', $code->id); ?>
						</tr>
					<?php
					$i++;
					endforeach;
				else : ?>
					<tr><td colspan="11"><?php _e( 'No discount codes added yet.', 'rcp' ); ?></td></tr>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="col" class="rcp-discounts-name-col column-primary" ><?php _e( 'Name', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-desc-col"><?php _e( 'Description', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-code-col" ><?php _e( 'Code', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-subscription-col" ><?php _e( 'Membership Levels', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-amount-col"><?php _e( 'Amount', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-type-col"><?php _e( 'Type', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-status-col"><?php _e( 'Status', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-col"><?php _e( 'Uses', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-uses-left-col"><?php _e( 'Uses Left', 'rcp' ); ?></th>
						<th scope="col" class="rcp-discounts-expir-col" ><?php _e( 'Expiration', 'rcp' ); ?></th>
						<?php do_action( 'rcp_discounts_page_table_header' ); ?>
					</tr>
				</tfoot>
			</table>
			<?php do_action( 'rcp_discounts_below_table' ); ?>
			<?php if( current_user_can( 'rcp_manage_discounts' ) ) : ?>
				<h2><?php _e( 'Add New Discount', 'rcp' ); ?></h2>
				<form id="rcp-discounts" action="" method="POST">
					<table class="form-table">
						<tbody>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-name"><?php _e( 'Name', 'rcp' ); ?></label>
								</th>
								<td>
									<input name="name" id="rcp-name" type="text" value=""/>
									<p class="description"><?php _e( 'The name of this discount', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-description"><?php _e( 'Description', 'rcp' ); ?></label>
								</th>
								<td>
									<textarea name="description" id="rcp-description"></textarea>
									<p class="description"><?php _e( 'The description of this discount code', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-code"><?php _e( 'Code', 'rcp' ); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-code" name="code" value=""/>
									<p class="description"><?php _e( 'Enter a code for this discount, such as 10PERCENT', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-unit"><?php _e( 'Type', 'rcp' ); ?></label>
								</th>
								<td>
									<select name="unit" id="rcp-duration-unit">
										<option value="%"><?php _e( 'Percentage', 'rcp' ); ?></option>
										<option value="flat"><?php _e( 'Flat amount', 'rcp' ); ?></option>
									</select>
									<p class="description"><?php _e( 'The kind of discount to apply for this discount.', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-amount"><?php _e( 'Amount', 'rcp' ); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-amount" name="amount" value=""/>
									<p class="description"><?php _e( 'The amount of this discount code.', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-subscription"><?php _e( 'Membership Levels', 'rcp' ); ?></label>
								</th>
								<td>
									<?php
									$levels = rcp_get_subscription_levels( 'all' );
									if( $levels ) {
										foreach ( $levels as $level ) : ?>
											<input type="checkbox" id="rcp-membership-levels-<?php echo esc_attr( $level->id ); ?>" name="membership_levels[]" value="<?php echo esc_attr( $level->id ) ?>">
											<label for="rcp-membership-levels-<?php echo esc_attr( $level->id ); ?>"><?php echo esc_html( $level->name ); ?></label>
											<br>
										<?php
										endforeach;
									}
									?>
									<p class="description"><?php _e( 'The membership levels this discount code can be used for.', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-expiration"><?php _e( 'Expiration date', 'rcp' ); ?></label>
								</th>
								<td>
									<input name="expiration" id="rcp-expiration" type="text" class="rcp-datetimepicker"/>
									<p class="description"><?php _e( 'Enter the expiration date for this discount code in the format of yyyy-mm-dd hh:mm:ss. For no expiration, leave blank', 'rcp' ); ?></p>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" valign="top">
									<label for="rcp-max-uses"><?php _e( 'Max Uses', 'rcp' ); ?></label>
								</th>
								<td>
									<input type="text" id="rcp-max-uses" name="max" value=""/>
									<p class="description"><?php _e( 'The maximum number of times this discount can be used. Leave blank for unlimited.', 'rcp' ); ?></p>
								</td>
							</tr>
							<?php do_action( 'rcp_add_discount_form' ); ?>
						</tbody>
					</table>
					<p class="submit">
						<input type="hidden" name="rcp-action" value="add-discount"/>
						<input type="submit" value="<?php _e( 'Add Discount Code', 'rcp' ); ?>" class="button-primary"/>
					</p>
					<?php wp_nonce_field( 'rcp_add_discount_nonce', 'rcp_add_discount_nonce' ); ?>
				</form>
			<?php endif; ?>
		<?php endif; ?>
	</div><!--end wrap-->

	<?php
}