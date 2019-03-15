<?php
/**
 * Card Update Form
 *
 * This form is displayed with the [rcp_update_card] shortcode.
 * @link http://docs.restrictcontentpro.com/article/1608-rcpupdatecard
 *
 * For modifying this template, please see: http://docs.restrictcontentpro.com/article/1738-template-files
 *
 * @package     Restrict Content Pro
 * @subpackage  Templates/Card Update Form
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * @var RCP_Membership $rcp_membership
 */
global $rcp_membership;
?>
<form id="rcp_update_card_form" class="rcp_form" action="" method="POST">

	<?php $cards = $rcp_membership->get_card_details(); ?>

	<?php if( ! empty( $cards ) ) : ?>
		<h3><?php _e( 'Your Cards', 'rcp' ); ?></h3>
		<?php foreach( $cards as $card ) : ?>
			<fieldset class="rcp_current_cards_fieldset">
				<p>
					<span class="rcp_card_details_name"><?php _e( 'Name:', 'rcp' ); ?> <?php echo $card['name']; ?></span>
					<span class="rcp_card_details_type"><?php _e( 'Type:', 'rcp' ); ?> <?php echo $card['type']; ?></span>
					<span class="rcp_card_details_last4"><?php _e( 'Last 4:', 'rcp' ); ?> <?php echo $card['last4']; ?></span>
					<span class="rcp_card_details_exp"><?php _e( 'Exp:', 'rcp' ); ?> <?php echo $card['exp_month'] . ' / ' . $card['exp_year']; ?></span>
				</p>
			</fieldset>
		<?php endforeach; ?>
	<?php endif; ?>

	<fieldset class="rcp_card_fieldset">
		<p id="rcp_card_number_wrap">
			<label><?php _e( 'Card Number', 'rcp' ); ?></label>
			<input type="text" size="20" maxlength="20" name="rcp_card_number" class="rcp_card_number card-number" />
		</p>
		<p id="rcp_card_cvc_wrap">
			<label><?php _e( 'Card CVC', 'rcp' ); ?></label>
			<input type="text" size="4" maxlength="4" name="rcp_card_cvc" class="rcp_card_cvc card-cvc" />
		</p>
		<p id="rcp_card_zip_wrap">
			<label><?php _e( 'Card ZIP or Postal Code', 'rcp' ); ?></label>
			<input type="text" size="10" name="rcp_card_zip" class="rcp_card_zip card-zip" />
		</p>
		<p id="rcp_card_name_wrap">
			<label><?php _e( 'Name on Card', 'rcp' ); ?></label>
			<input type="text" size="20" name="rcp_card_name" class="rcp_card_name card-name" />
		</p>
		<p id="rcp_card_exp_wrap">
			<label><?php _e( 'Expiration (MM/YYYY)', 'rcp' ); ?></label>
			<select name="rcp_card_exp_month" class="rcp_card_exp_month card-expiry-month">
				<?php for( $i = 1; $i <= 12; $i++ ) : ?>
					<option value="<?php echo $i; ?>"><?php echo $i . ' - ' . rcp_get_month_name( $i ); ?></option>
				<?php endfor; ?>
			</select>
			<span class="rcp_expiry_separator"> / </span>
			<select name="rcp_card_exp_year" class="rcp_card_exp_year card-expiry-year">
				<?php
				$year = date( 'Y' );
				for( $i = $year; $i <= $year + 10; $i++ ) : ?>
					<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php endfor; ?>
			</select>
		</p>
	</fieldset>
	<div class="rcp_message error">
	</div>
	<p id="rcp_submit_wrap">
		<input type="hidden" name="rcp_membership_id" value="<?php echo esc_attr( $rcp_membership->get_id() ); ?>"/>
		<input type="hidden" name="rcp_update_card_nonce" value="<?php echo wp_create_nonce( 'rcp-update-card-nonce' ); ?>"/>
		<input type="submit" name="rcp_submit_card_update" id="rcp_submit" value="<?php esc_attr_e( 'Update Card', 'rcp' ); ?>"/>
	</p>
</form>
