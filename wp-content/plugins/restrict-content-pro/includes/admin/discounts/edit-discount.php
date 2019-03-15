<?php
/**
 * Edit Discount Code
 *
 * @package     Restrict Content Pro
 * @subpackage  Admin/Edit Discount
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

$code = rcp_get_discount_details( urldecode( $_GET['edit_discount'] ) );
?>
<h1>
	<?php _e( 'Edit Discount Code:', 'rcp' ); echo ' ' . $code->name; ?>
	<a href="<?php echo admin_url( '/admin.php?page=rcp-discounts' ); ?>" class="add-new-h2">
		<?php _e( 'Cancel', 'rcp' ); ?>
	</a>
</h1>
<form id="rcp-edit-discount" action="" method="post">
	<table class="form-table">
		<tbody>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-name"><?php _e(' Name', 'rcp' ); ?></label>
				</th>
				<td>
					<input name="name" id="rcp-name" type="text" value="<?php echo esc_html( stripslashes( $code->name ) ); ?>"/>
					<p class="description"><?php _e(' The name of this discount', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-description"><?php _e(' Description', 'rcp' ); ?></label>
				</th>
				<td>
					<textarea name="description" id="rcp-description"><?php echo esc_html( stripslashes( $code->description ) ); ?></textarea>
					<p class="description"><?php _e(' The description of this discount code', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-code"><?php _e(' Code', 'rcp' ); ?></label>
				</th>
				<td>
					<input type="text" id="rcp-code" name="code" value="<?php echo esc_attr( $code->code ); ?>"/>
					<p class="description"><?php _e(' Enter a code for this discount, such as 10PERCENT', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-unit"><?php _e(' Type', 'rcp' ); ?></label>
				</th>
				<td>
					<select name="unit" id="rcp-unit">
						<option value="%" <?php selected( $code->unit, '%' ); ?>><?php _e(' Percentage', 'rcp' ); ?></option>
						<option value="flat" <?php selected( $code->unit, 'flat' ); ?>><?php _e(' Flat amount', 'rcp' ); ?></option>
					</select>
					<p class="description"><?php _e(' The kind of discount to apply for this discount.', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-amount"><?php _e(' Amount', 'rcp' ); ?></label>
				</th>
				<td>
					<input type="text" id="rcp-amount" name="amount" value="<?php echo esc_attr( $code->amount ); ?>"/>
					<p class="description"><?php _e(' The amount of this discount code.', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-subscription"><?php _e( 'Membership Levels', 'rcp' ); ?></label>
				</th>
				<td>
					<?php
					$levels = rcp_get_subscription_levels('all' );
					if( $levels ) {
						$current = maybe_unserialize( $code->membership_level_ids );
						if ( empty ( $current ) || ! is_array( $current ) ) {
							$current = array();
						}
						foreach ( $levels as $level ) : ?>
							<input type="checkbox" id="rcp-membership-levels-<?php echo esc_attr( $level->id ); ?>" name="membership_levels[]" value="<?php echo esc_attr( $level->id ) ?>" <?php checked( true, in_array( $level->id, $current ) ); ?>>
							<label for="rcp-membership-levels-<?php echo esc_attr( $level->id ); ?>"><?php echo esc_html( $level->name ); ?></label>
							<br>
						<?php
						endforeach;
					}
					?>
					<p class="description"><?php _e( 'The membership levels this discount code can be used for. Leave blank for all levels.', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" valign="top">
					<label for="rcp-expiration"><?php _e(' Expiration date', 'rcp' ); ?></label>
				</th>
				<td>
					<input name="expiration" id="rcp-expiration" type="text" class="rcp-datetimepicker" value="<?php echo $code->expiration == '' ? '' : esc_attr( date( 'Y-m-d H:i:s', strtotime( $code->expiration, current_time( 'timestamp' ) ) ) ); ?>"/>
					<p class="description"><?php _e(' Enter the expiration date for this discount code in the format of yyyy-mm-dd hh:mm:ss. Leave blank for no expiration', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-status"><?php _e(' Status', 'rcp' ); ?></label>
				</th>
				<td>
					<select name="status" id="rcp-status">
						<option value="active" <?php selected( $code->status, '%' ); ?>><?php _e(' Active', 'rcp' ); ?></option>
						<option value="disabled" <?php selected( $code->status, 'disabled' ); ?>><?php _e(' Disabled', 'rcp' ); ?></option>
					</select>
					<p class="description"><?php _e(' The status of this discount code.', 'rcp' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="rcp-max-uses"><?php _e(' Max Uses', 'rcp' ); ?></label>
				</th>
				<td>
					<input type="text" id="rcp-max-uses" name="max" value="<?php echo esc_attr( absint( $code->max_uses ) ); ?>"/>
					<p class="description"><?php _e(' The maximum number of times this discount can be used. Leave blank for unlimited.', 'rcp' ); ?></p>
				</td>
			</tr>
			<?php do_action( 'rcp_edit_discount_form', $code->id ); ?>
		</tbody>
	</table>
	<p class="submit">
		<input type="hidden" name="rcp-action" value="edit-discount"/>
		<input type="hidden" name="discount_id" value="<?php echo absint( urldecode( $_GET['edit_discount'] ) ); ?>"/>
		<input type="submit" value="<?php _e(' Update Discount', 'rcp' ); ?>" class="button-primary"/>
	</p>
	<?php wp_nonce_field( 'rcp_edit_discount_nonce', 'rcp_edit_discount_nonce' ); ?>
</form>