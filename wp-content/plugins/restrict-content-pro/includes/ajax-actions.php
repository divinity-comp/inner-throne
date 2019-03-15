<?php
/**
 * Ajax Actions
 *
 * Process the front-end ajax actions.
 *
 * @package     Restrict Content Pro
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/license/gpl-2.1.php GNU Public License
 */

/**
 * Check whether a discount code is valid. Used during registration to validate a discount code on the fly.
 *
 * @return void
 */
function rcp_validate_discount_with_ajax() {
	if( isset( $_POST['code'] ) ) {

		$return          = array();
		$return['valid'] = false;
		$return['full']  = false;
		$subscription_id = isset( $_POST['subscription_id'] ) ? absint( $_POST['subscription_id'] ) : 0;

		rcp_setup_registration( $subscription_id, $_POST['code'] );

		if( rcp_validate_discount( $_POST['code'], $subscription_id ) ) {

			$code_details = rcp_get_discount_details_by_code( sanitize_text_field( $_POST['code'] ) );

			if( ( ! rcp_registration_is_recurring() && rcp_get_registration()->get_recurring_total() == 0.00 ) && rcp_get_registration()->get_total() == 0.00 ) {

				// this is a 100% discount
				$return['full']   = true;

			}

			$return['valid']  = true;
			$return['amount'] = rcp_discount_sign_filter( $code_details->amount, $code_details->unit );

		}

		wp_send_json( $return );
	}
	die();
}
add_action( 'wp_ajax_validate_discount', 'rcp_validate_discount_with_ajax' );
add_action( 'wp_ajax_nopriv_validate_discount', 'rcp_validate_discount_with_ajax' );

/**
 * Calls the load_fields() method for gateways when a gateway selection is made
 *
 * @since  2.1
 * @return void
 */
function rcp_load_gateway_fields() {

	$gateways = new RCP_Payment_Gateways;
	$gateways->load_fields();
	die();
}
add_action( 'wp_ajax_rcp_load_gateway_fields', 'rcp_load_gateway_fields' );
add_action( 'wp_ajax_nopriv_rcp_load_gateway_fields', 'rcp_load_gateway_fields' );

/**
 * Setup the registration details
 *
 * @since  2.5
 * @return void
 */
function rcp_calc_total_ajax() {
	$return = array(
		'valid' => false,
		'total' => __( 'No available membership levels for your account.', 'rcp' ),
	);

	if ( ! rcp_is_registration() ) {
		wp_send_json( $return );
	}

	ob_start();

	rcp_get_template_part( 'register-total-details' );

	$return['total'] = ob_get_clean();

	wp_send_json( $return );
}
add_action( 'wp_ajax_rcp_calc_discount', 'rcp_calc_total_ajax' );
add_action( 'wp_ajax_nopriv_rcp_calc_discount', 'rcp_calc_total_ajax' );