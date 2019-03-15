<?php
/**
 * Edit Membership
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2018, Restrict Content Pro team
 * @license   GPL2+
 * @since     3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $_GET['membership_id'] ) || ! is_numeric( $_GET['membership_id'] ) ) {
	wp_die( __( 'Something went wrong.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 400 ) );
}

$membership_id = $_GET['membership_id'];
$membership    = rcp_get_membership( $membership_id );

if ( empty( $membership ) ) {
	wp_die( __( 'Something went wrong.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 400 ) );
}

// Prevent editing disabled memberships.
if ( $membership->is_disabled() ) {
	wp_die( __( 'Invalid membership.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
}

$user             = get_userdata( $membership->get_customer()->get_user_id() );
$membership_level = rcp_get_subscription_details( $membership->get_object_id() );
$created_date     = date( 'Y-m-d', strtotime( $membership->get_created_date( false ), current_time( 'timestamp' ) ) );
$expiration_date  = date( 'Y-m-d', strtotime( $membership->get_expiration_date( false ), current_time( 'timestamp' ) ) );

// Action URLs.
$cancel_url = wp_nonce_url( rcp_get_memberships_admin_page( array(
	'membership_id' => $membership->get_id(),
	'rcp-action'    => 'cancel_membership'
) ), 'cancel_membership' );
$expire_url = wp_nonce_url( rcp_get_memberships_admin_page( array(
	'membership_id' => $membership->get_id(),
	'rcp-action'    => 'expire_membership'
) ), 'expire_membership' );

// If this is a payment plan then override the cancellation URL to take them to a second confirmation screen.
if ( $membership->has_payment_plan() ) {
	$cancel_url = rcp_get_memberships_admin_page( array(
		'membership_id' => $membership->get_id(),
		'view'          => 'cancel-confirmation',
	) );
}

// Payments
$payments = $membership->get_payments( array( 'number' => 5 ) );
?>
<div class="wrap">
	<h1><?php _e( 'Membership Details', 'rcp' ); ?></h1>

	<div id="rcp-item-card-wrapper">
		<div class="rcp-info-wrapper rcp-item-section rcp-membership-card-wrapper">
			<form id="rcp-edit-membership-info" method="POST">
				<div class="rcp-item-info">
					<table class="widefat striped">
						<tbody>
						<tr>
							<th scope="row" class="row-title">
								<label for="tablecell"><?php _e( 'ID:', 'rcp' ); ?></label>
							</th>
							<td>
								<?php echo $membership->get_id(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row" class="row-title">
								<label for="tablecell"><?php _e( 'Customer:', 'rcp' ); ?></label>
							</th>
							<td>
								<a href="<?php echo esc_url( rcp_get_customers_admin_page( array( 'customer_id' => $membership->get_customer()->get_id(), 'view' => 'edit' ) ) ); ?>"><?php echo esc_html( $user->display_name ); ?></a>
							</td>
						</tr>
						<tr>
							<th scope="row" class="row-title">
								<label for="rcp-membership-level"><?php _e( 'Membership Level:', 'rcp' ); ?></label>
							</th>
							<td>
								<?php echo esc_html( $membership->get_membership_level_name() ); ?>
								<!-- @todo add "Change" button -->
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="rcp-status"><?php _e( 'Membership Status:', 'rcp' ); ?></label>
							</th>
							<td>
								<select name="status" id="rcp-status">
									<?php
									$statuses = array( 'active', 'expired', 'cancelled', 'pending' );
									foreach ( $statuses as $status ) :
										echo '<option value="' . esc_attr( $status ) . '"' . selected( $status, $membership->get_status(), false ) . '>' . rcp_get_status_label( $status ) . '</option>';
									endforeach;
									?>
								</select>
								<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Active memberships have access to restricted content. Members with a status of Cancelled may continue to access restricted content until the expiration date on their account is reached. When a user reaches his or her expiration date, their status is updated to Expired.', 'rcp' ); ?>"></span>
								<span id="rcp-membership-status-action-buttons">
									<?php if ( 'cancelled' != $membership->get_status() ) :
										$title_text = $membership->can_cancel() ? __( 'Cancel membership and recurring billing. The customer will retain access until they reach the expiration date.', 'rcp' ) : __( 'Membership cannot be cancelled as it does not have a recurring subscription.', 'rcp' );
										?>
										<a class="button rcp_cancel" id="rcp-cancel-membership-button" href="<?php echo esc_url( $cancel_url ) ?>" title="<?php echo esc_attr( $title_text ); ?>" <?php echo ! $membership->can_cancel() ? ' disabled="disabled"' : ''; ?>><?php _e( 'Cancel', 'rcp' ); ?></a>
									<?php endif; ?>
									<?php if ( $membership->is_active() ) : ?>
										<a class="button" id="rcp-expire-membership-button" href="<?php echo esc_url( $expire_url ); ?>" title="<?php esc_attr_e( 'Revoke the customer\'s access immediately', 'rcp' ); ?>"><?php _e( 'Expire', 'rcp' ); ?></a>
									<?php endif; ?>
								</span>
							</td>
						</tr>
						<tr>
							<th scope="row" class="row-title">
								<label><?php _e( 'Billing Cycle:', 'rcp' ); ?></label>
							</th>
							<td>
								<?php echo $membership->get_formatted_billing_cycle(); ?>
							</td>
						</tr>
						<tr>
							<th scope="row" class="row-title">
								<label><?php _e( 'Times Billed:', 'rcp' ); ?></label>
							</th>
							<td>
								<?php
								if ( 0 == $membership->get_maximum_renewals() && $membership_level->duration > 0 && $membership_level->price > 0 ) {
									printf( __( '%d / Until Cancelled', 'rcp' ), $membership->get_times_billed() );
								} else {
									$renewals = ( 0 == $membership_level->price ) ? 1 : $membership->get_maximum_renewals() + 1;

									printf( __( '%d / %d', 'rcp' ), $membership->get_times_billed(), $renewals );
								}
								?>
							</td>
						</tr>
						<tr>
							<th scope="row" class="row-title">
								<label for="rcp-membership-created"><?php _e( 'Date Created:', 'rcp' ); ?></label>
							</th>
							<td>
								<span class="rcp-membership-created"><?php echo $membership->get_created_date(); ?></span>
								<input type="text" id="rcp-membership-created" name="created_date" class="rcp-datepicker rcp-membership-created hidden" value="<?php echo esc_attr( $created_date ); ?>"/>
								<span>&nbsp;&ndash;&nbsp;</span>
								<a href="#" id="rcp-edit-membership-created"><?php _e( 'Edit', 'rcp' ); ?></a>
							</td>
						</tr>
						<tr>
							<th scope="row" class="row-title">
								<label for="rcp-membership-expiration">
									<?php echo $membership->is_trialing() ? __( 'Trialling Until:', 'rcp' ) : __( 'Expiration Date:', 'rcp' ); ?>
								</label>
							</th>
							<td>
								<span class="rcp-membership-expiration"><?php echo ( 'none' == $membership->get_expiration_date() ) ? __( 'Never Expires', 'rcp' ) : $membership->get_expiration_date(); ?></span>
								<input type="text" id="rcp-membership-expiration" name="expiration_date" class="rcp-datepicker rcp-membership-expiration hidden" value="<?php echo esc_attr( $expiration_date ); ?>"/>
								<span class="rcp-membership-expiration-none-wrap hidden">
									<input type="checkbox" id="rcp-membership-expiration-none" name="expiration_date_none" value="1" <?php checked( 'none' == $membership->get_expiration_date() ); ?> />
									<label for="rcp-membership-expiration-none"><?php _e( 'Never expires', 'rcp' ); ?></label>
								</span>
								<span>&nbsp;&ndash;&nbsp;</span>
								<a href="#" id="rcp-edit-membership-expiration"><?php _e( 'Edit', 'rcp' ); ?></a>
							</td>
						</tr>
						<?php if ( 'cancelled' == $membership->get_status() ) : ?>
							<tr>
								<th scope="row" class="row-title">
									<label for="rcp-membership-cancellation-date"><?php _e( 'Cancellation Date:', 'rcp' ); ?></label>
								</th>
								<td>
									<?php
									$cancellation_date = $membership->get_cancellation_date();

									if ( empty( $cancellation_date ) ) {
										_e( 'Unknown', 'rcp' );
									} else {
										echo $cancellation_date;
									}
									?>
								</td>
							</tr>
						<?php endif; ?>
						<tr>
							<th scope="row" class="row-title">
								<label for="rcp-recurring"><?php _e( 'Auto Renew:', 'rcp' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="auto_renew" id="rcp-recurring" value="1" <?php checked( $membership->is_recurring() ); ?>/>
								<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php _e( 'If checked, this member has a recurring subscription. Only customers with recurring memberships will be given the option to cancel their membership on their subscription details page.', 'rcp' ); ?>"></span>
							</td>
						</tr>
						<?php if ( 'free' != $membership->get_gateway() ) : ?>
							<tr>
								<th scope="row" class="row-title">
									<label><?php _e( 'Payment Method:', 'rcp' ); ?></label>
								</th>
								<td>
									<?php
									$gateways     = rcp_get_payment_gateways();
									$gateway_used = $membership->get_gateway();

									if ( array_key_exists( $gateway_used, $gateways ) && ! empty( $gateways[$gateway_used]['admin_label'] ) ) {
										echo $gateways[$gateway_used]['admin_label'];
									} else {
										echo esc_html( $gateway_used );
									}
									?>
								</td>
							</tr>
							<tr>
								<th scope="row" class="row-title">
									<label for="rcp-membership-gateway-customer-id"><?php _e( 'Gateway Customer ID:', 'rcp' ); ?></label>
								</th>
								<td>
									<span class="rcp-membership-gateway-customer-id">
										<?php echo $membership->get_gateway_customer_id(); ?>
									</span>
									<input type="text" id="rcp-membership-gateway-customer-id" name="gateway_customer_id" class="hidden" value="<?php echo esc_attr( $membership->get_gateway_customer_id() ); ?>"/>
									<span>&nbsp;&ndash;&nbsp;</span>
									<a href="#" id="rcp-edit-membership-gateway-customer-id"><?php _e( 'Edit', 'rcp' ); ?></a>
								</td>
							</tr>
							<tr>
								<th scope="row" class="row-title">
									<label for="rcp-membership-gateway-subscription-id"><?php _e( 'Gateway Subscription ID:', 'rcp' ); ?></label>
								</th>
								<td>
									<span class="rcp-membership-gateway-subscription-id">
										<?php echo $membership->get_gateway_subscription_id(); ?>
									</span>
									<input type="text" id="rcp-membership-gateway-subscription-id" name="gateway_subscription_id" class="hidden" value="<?php echo esc_attr( $membership->get_gateway_subscription_id() ); ?>"/>
									<span>&nbsp;&ndash;&nbsp;</span>
									<a href="#" id="rcp-edit-membership-gateway-subscription-id"><?php _e( 'Edit', 'rcp' ); ?></a>
								</td>
							</tr>
						<?php endif; ?>
						<?php if ( $membership->was_upgrade() ) : ?>
							<tr>
								<th scope="row" class="row-title">
									<label><?php _e( 'Upgraded From:', 'rcp' ); ?></label>
								</th>
								<td>
									<?php
									$previous_membership = rcp_get_membership( $membership->get_upgraded_from() );

									if ( ! empty( $previous_membership ) ) {
										$previous_membership_level = rcp_get_subscription_details( $previous_membership->get_object_id() );

										echo $previous_membership_level->name;
									}
									?>
								</td>
							</tr>
						<?php endif; ?>
						<?php
						/**
						 * Used for adding additional content to the end of the membership table.
						 *
						 * @param RCP_Membership $membership
						 *
						 * @since 3.0
						 */
						do_action( 'rcp_edit_membership_after', $membership );
						?>
						</tbody>
					</table>
				</div>

				<div id="rcp-membership-notices">
					<div class="notice notice-info inline hidden" id="rcp-membership-level-update-notice">
						<p><?php _e( 'Changing the membership level assigned will not automatically adjust any pricing.', 'rcp' ); ?></p>
					</div>
					<div class="notice notice-info inline hidden" id="rcp-membership-expiration-update-notice">
						<p><?php _e( 'Changing the expiration date will not affect when renewal payments are processed.', 'rcp' ); ?></p>
					</div>
					<div class="notice notice-info inline hidden" id="rcp-membership-recurring-update-notice">
						<p><?php _e( 'Changing the recurring indicator will not set up or remove a subscription with the gateway. This checkbox is for updating RCP records only.', 'rcp' ); ?></p>
					</div>
					<div class="notice notice-warning inline hidden" id="rcp-membership-gateway-subscription-id-update-notice">
						<p><?php _e( 'Changing the gateway subscription ID can result in renewals not being processed. Do this with caution.', 'rcp' ); ?></p>
					</div>
				</div>
				<div id="rcp-item-edit-actions" class="edit-item">
					<input type="hidden" name="rcp-action" value="edit_membership"/>
					<input type="hidden" name="membership_id" value="<?php echo esc_attr( $membership->get_id() ); ?>"/>
					<?php wp_nonce_field( 'rcp_edit_membership', 'rcp_edit_membership_nonce' ); ?>
					<input type="submit" name="rcp_update_membership" id="rcp_update_membership" class="button button-primary" value="<?php _e( 'Update Membership', 'rcp' ); ?>"/>
					&nbsp;<input type="submit" name="rcp_delete_membership" class="rcp-delete-membership button" value="<?php _e( 'Delete Membership', 'rcp' ); ?>"/>
				</div>
			</form>
		</div>

		<div id="rcp-membership-payments-wrapper" class="rcp-item-section">
			<h3><?php _e( 'Payments:', 'rcp' ); ?></h3>
			<table class="wp-list-table widefat striped payments">
				<thead>
				<tr>
					<th class="column-primary"><?php _e( 'ID', 'rcp' ); ?></th>
					<th><?php _e( 'Date', 'rcp' ); ?></th>
					<th><?php _e( 'Amount', 'rcp' ); ?></th>
					<th><?php _e( 'Status', 'rcp' ); ?></th>
					<th><?php _e( 'Transaction ID', 'rcp' ); ?></th>
					<th><?php _e( 'Invoice', 'rcp' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $payments ) ) : ?>
					<?php foreach ( $payments as $payment ) : ?>
						<tr>
							<td class="column-primary" data-colname="<?php esc_attr_e( 'ID', 'rcp' ); ?>">
								<a href="<?php echo esc_url( add_query_arg( 'payment_id', urlencode( $payment->id ), admin_url( 'admin.php?page=rcp-payments&view=edit-payment' ) ) ); ?>"><?php echo $payment->id; ?></a>
								<button type="button" class="toggle-row">
									<span class="screen-reader-text"><?php _e( 'Show more details', 'rcp' ); ?></span>
								</button>
							</td>
							<td data-colname="<?php esc_attr_e( 'Date', 'rcp' ); ?>"><?php echo $payment->date; ?></td>
							<td data-colname="<?php esc_attr_e( 'Amount', 'rcp' ); ?>"><?php echo rcp_currency_filter( $payment->amount ); ?></td>
							<td data-colname="<?php esc_attr_e( 'Status', 'rcp' ); ?>"><?php echo rcp_get_status_label( $payment->status ); ?></td>
							<td data-colname="<?php esc_attr_e( 'Transaction ID', 'rcp' ); ?>"><?php echo rcp_get_merchant_transaction_id_link( $payment ); ?></td>
							<td data-colname="<?php esc_attr_e( 'Invoice', 'rcp' ); ?>">
								<a href="<?php echo esc_url( rcp_get_invoice_url( $payment->id ) ); ?>"><?php _e( 'View Invoice', 'rcp' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( 5 === count( $payments ) ) : ?>
						<tr>
							<td colspan="6">
								<a href="<?php echo esc_url( add_query_arg( 'membership_id', urlencode( $membership->get_id() ), admin_url( 'admin.php?page=rcp-payments' ) ) ); ?>"><?php _e( 'View all payments', 'rcp' ); ?></a>
							</td>
						</tr>
					<?php endif; ?>
				<?php else : ?>
					<tr>
						<td colspan="6"><?php _e( 'No payments found.', 'rcp' ); ?></td>
					</tr>
				<?php endif; ?>
				</tbody>
				<?php if ( current_user_can( 'rcp_manage_payments' ) ) : ?>
					<tfoot>
					<tr class="alternate">
						<td colspan="6">
							<form id="rcp-membership-add-renewal" method="POST">
								<p>
									<?php _e( 'Use this form to manually record a renewal payment.', 'rcp' ); ?>
									<span alt="f223" class="rcp-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Note: this does not initiate a charge in your merchant processor. This should only be used for recording a missed payment or one that was manually collected.', 'rcp' ); ?>"></span>
								</p>
								<p>
									<label>
										<span style="display: inline-block; width: 150px; padding: 3px;"><?php _e( 'Amount:', 'rcp' ); ?></span>
										<input type="text" class="regular-text" style="width: 100px; padding: 3px;" name="amount" value="<?php echo esc_attr( $membership->get_recurring_amount() ); ?>" placeholder="0.00">
									</label>
								</p>
								<p>
									<label>
										<span style="display: inline-block; width: 150px; padding: 3px;"><?php _e( 'Transaction ID:', 'rcp' ); ?></span>
										<input type="text" class="regular-text" style="width: 100px; padding: 3px;" name="transaction_id" value="" placeholder="">
									</label>
								</p>

								<input type="hidden" name="rcp-action" value="add_membership_payment"/>
								<input type="hidden" name="membership_id" value="<?php echo esc_attr( $membership->get_id() ); ?>"/>
								<?php wp_nonce_field( 'rcp_add_membership_payment', 'rcp_add_membership_payment_nonce' ); ?>
								<?php if ( $membership->can_renew() ) : ?>
									<input type="submit" name="renew_and_add_payment" class="button alignright" style="margin-left: 8px;" value="<?php esc_attr_e( 'Record Payment and Renew Membership', 'rcp' ); ?>"/>
								<?php endif; ?>
								<input type="submit" name="add_payment_only" class="button alignright" value="<?php esc_attr_e( 'Record Payment Only', 'rcp' ); ?>"/>
							</form>
						</td>
					</tr>
					</tfoot>
				<?php endif; ?>
			</table>
		</div>

		<div id="rcp-membership-notes-wrapper" class="rcp-item-section">
			<h3><?php _e( 'Notes:', 'rcp' ); ?></h3>
			<div id="rcp-membership-notes" class="rcp-item-notes">
				<?php echo wpautop( $membership->get_notes() ); ?>
			</div>
			<form id="rcp-edit-membership-notes" method="POST">
				<label for="rcp-add-membership-note" class="screen-reader-text"><?php _e( 'Add Note', 'rcp' ); ?></label>
				<textarea id="rcp-add-membership-note" class="rcp-add-item-note" name="new_note" placeholder="<?php esc_attr_e( 'Add a note...', 'rcp' ); ?>"></textarea>
				<div class="edit-item">
					<input type="hidden" name="rcp-action" value="add_membership_note"/>
					<input type="hidden" name="membership_id" value="<?php echo esc_attr( $membership->get_id() ); ?>"/>
					<?php wp_nonce_field( 'rcp_add_membership_note', 'rcp_add_membership_note_nonce' ); ?>
					<input type="submit" class="button" value="<?php esc_attr_e( 'Add Note', 'rcp' ); ?>"/>
				</div>
			</form>
		</div>
	</div>
</div>
