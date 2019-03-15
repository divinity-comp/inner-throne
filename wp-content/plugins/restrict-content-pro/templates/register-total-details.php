<?php
/**
 * Registration Form - Total Details
 *
 * This template is loaded into register.php and register-single.php to display the total
 * membership cost, fees, and any recurring costs.
 * @link http://docs.restrictcontentpro.com/article/1597-registerform
 *
 * For modifying this template, please see: http://docs.restrictcontentpro.com/article/1738-template-files
 *
 * @package     Restrict Content Pro
 * @subpackage  Templates/Register/Total Details
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! rcp_is_registration() ) {
	return;
}

?>

<table class="rcp_registration_total_details rcp-table">

	<thead>

		<tr>
			<th scope="col"><?php _e( 'Membership', 'rcp' ); ?></th>
			<th scope="col"><?php _e( 'Amount', 'rcp' ); ?></th>
		</tr>

	</thead>

	<tbody style="vertical-align: top;">

		<tr>
			<td data-th="<?php esc_attr_e( 'Membership', 'rcp' ); ?>"><?php echo rcp_get_subscription_name( rcp_get_registration()->get_membership_level_id() ); ?></td>
			<td data-th="<?php esc_attr_e( 'Amount', 'rcp' ); ?>"><?php echo ( rcp_get_subscription_price( rcp_get_registration()->get_membership_level_id() ) > 0 ) ? rcp_currency_filter( rcp_get_subscription_price( rcp_get_registration()->get_membership_level_id() ) ) : __( 'free', 'rcp' ); ?></td>
		</tr>

		<?php if ( rcp_get_subscription_price( rcp_get_registration()->get_membership_level_id() ) ) : ?>
			<?php if ( rcp_get_registration()->get_fees() || rcp_get_registration()->get_discounts() ) : ?>
				<tr>
					<th colspan="2"><?php _e( 'Discounts and Fees', 'rcp' ); ?></th>
				</tr>

				<?php // Discounts ?>
				<?php if ( rcp_get_registration()->get_discounts() ) : foreach( rcp_get_registration()->get_discounts() as $code => $recuring ) : if ( ! $discount = rcp_get_discount_details_by_code( $code ) ) continue; ?>
					<tr class="rcp-discount">
						<td data-th="<?php esc_attr_e( 'Discount', 'rcp' ); ?>"><?php echo esc_html( $discount->name ); ?></td>
						<td data-th="<?php esc_attr_e( 'Discount Amount', 'rcp' ); ?>"><?php echo esc_html( rcp_discount_sign_filter( $discount->amount, $discount->unit ) ); ?></td>
					</tr>
				<?php endforeach; endif; ?>

				<?php // Fees ?>
				<?php if ( rcp_get_registration()->get_fees() ) : foreach( rcp_get_registration()->get_fees() as $fee ) :

					$sign          = ( $fee['amount'] < 0 ) ? '-' : '';
					$fee['amount'] = abs( $fee['amount'] );
				?>
					<tr class="rcp-fee">
						<td data-th="<?php esc_attr_e( 'Fee', 'rcp' ); ?>"><?php echo esc_html( $fee['description'] ); ?></td>
						<td data-th="<?php esc_attr_e( 'Fee Amount', 'rcp' ); ?>"><?php echo esc_html( $sign . rcp_currency_filter( $fee['amount'] ) ); ?></td>
					</tr>
				<?php endforeach; endif; ?>

			<?php endif; ?>
		<?php endif; ?>

	</tbody>

	<tfoot>

		<tr class="rcp-total">
			<th scope="row"><?php _e( 'Total Today', 'rcp' ); ?></th>
			<td data-th="<?php esc_attr_e( 'Total Today', 'rcp' ); ?>"><?php rcp_registration_total(); ?></td>
		</tr>

		<?php if ( rcp_registration_is_recurring() ) : ?>
			<?php
			$subscription = rcp_get_subscription_details( rcp_get_registration()->get_membership_level_id() );

			if ( $subscription->duration == 1 ) {
				$label = sprintf( __( 'Total Recurring Per %s', 'rcp' ), rcp_filter_duration_unit( $subscription->duration_unit, 1 ) );
			} else {
				$label = sprintf( __( 'Total Recurring Every %s %s', 'rcp' ), $subscription->duration, rcp_filter_duration_unit( $subscription->duration_unit, $subscription->duration ) );
			}

			if ( ! empty( $subscription->maximum_renewals ) ) {
				$label = sprintf( __( '%d Additional Payments Every %s %s', 'rcp' ), $subscription->maximum_renewals, $subscription->duration, rcp_filter_duration_unit( $subscription->duration_unit, $subscription->duration ) );
			}
			?>
			<tr class="rcp-recurring-total">
				<th scope="row"><?php echo $label; ?></th>
				<td data-th="<?php echo esc_attr( $label ); ?>"><?php rcp_registration_recurring_total(); ?></td>
			</tr>
		<?php endif; ?>

	</tfoot>
</table>
