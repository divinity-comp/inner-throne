<?php
/**
 * Payment Actions
 *
 * @package     restrict-content-pro
 * @subpackage  Admin/Payment Actions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add a new manual payment
 *
 * @since 2.9
 * @return void
 */
function rcp_process_add_payment() {

	if ( ! wp_verify_nonce( $_POST['rcp_add_payment_nonce'], 'rcp_add_payment_nonce' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_payments' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	$current_user = wp_get_current_user();

	rcp_log( sprintf( '%s manually inserting new payment record.', $current_user->user_login ) );

	$payments = new RCP_Payments();
	$user     = get_user_by( 'login', $_POST['user'] );

	if ( $user ) {

		$data = array(
			'amount'           => empty( $_POST['amount'] ) ? 0.00 : sanitize_text_field( $_POST['amount'] ),
			'user_id'          => $user->ID,
			'date'             => empty( $_POST['date'] ) ? date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) : date( 'Y-m-d', strtotime( $_POST['date'], current_time( 'timestamp' ) ) ) . ' ' . date( 'H:i:s', current_time( 'timestamp' ) ),
			'payment_type'     => 'manual',
			'subscription'     => rcp_get_subscription( $user->ID ),
			'subscription_key' => rcp_get_subscription_key( $user->ID ),
			'transaction_id'   => sanitize_text_field( $_POST['transaction-id'] ),
			'status'           => sanitize_text_field( $_POST['status'] ),
		);

		$add = $payments->insert( $data );

	}

	if ( ! empty( $add ) ) {
		$cache_args = array( 'earnings' => 1, 'subscription' => 0, 'user_id' => 0, 'date' => '' );
		$cache_key  = md5( implode( ',', $cache_args ) );
		delete_transient( $cache_key );

		$url = admin_url( 'admin.php?page=rcp-payments&rcp_message=payment_added' );
	} else {
		rcp_log( sprintf( 'Failed adding new manual payment by %s: supplied user login doesn\'t exist.', $current_user->user_login ), true );
		$url = admin_url( 'admin.php?page=rcp-payments&rcp_message=payment_not_added' );
	}

	wp_safe_redirect( $url );
	exit;

}
add_action( 'rcp_action_add-payment', 'rcp_process_add_payment' );

/**
 * Edit an existing payment
 *
 * @since 2.9
 * @return void
 */
function rcp_process_edit_payment() {

	if ( ! wp_verify_nonce( $_POST['rcp_edit_payment_nonce'], 'rcp_edit_payment_nonce' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_payments' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	$payments     = new RCP_Payments();
	$payment_id   = absint( $_POST['payment-id'] );
	$user         = get_user_by( 'login', $_POST['user'] );
	$member       = new RCP_Member( $user->ID );
	$current_user = wp_get_current_user();

	rcp_log( sprintf( '%s manually updating payment #%d.', $current_user->user_login, $payment_id ) );

	if ( $user && $payment_id ) {

		$pending_subscription     = $member->get_pending_subscription_name();
		$pending_subscription_key = $member->get_pending_subscription_key();

		$data = array(
			'amount'           => empty( $_POST['amount'] ) ? 0.00 : sanitize_text_field( $_POST['amount'] ),
			'user_id'          => $user->ID,
			'date'             => empty( $_POST['date'] ) ? date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) : date( 'Y-m-d H:i:s', strtotime( $_POST['date'], current_time( 'timestamp' ) ) ),
			'subscription'     => ! empty( $pending_subscription ) ? $pending_subscription : $member->get_subscription_name(),
			'subscription_key' => ! empty( $pending_subscription_key ) ? $pending_subscription_key : $member->get_subscription_key(),
			'transaction_id'   => sanitize_text_field( $_POST['transaction-id'] ),
			'status'           => sanitize_text_field( $_POST['status'] ),
		);

		$update = $payments->update( $payment_id, $data );

	}

	if ( ! empty( $update ) ) {
		$cache_args = array( 'earnings' => 1, 'subscription' => 0, 'user_id' => 0, 'date' => '' );
		$cache_key  = md5( implode( ',', $cache_args ) );
		delete_transient( $cache_key );

		$url = admin_url( 'admin.php?page=rcp-payments&rcp_message=payment_updated' );
	} else {
		$url = admin_url( 'admin.php?page=rcp-payments&rcp_message=payment_not_updated' );
	}

	wp_safe_redirect( $url );
	exit;

}
add_action( 'rcp_action_edit-payment', 'rcp_process_edit_payment' );

/**
 * Delete a payment
 *
 * @since 2.9
 * @return void
 */
function rcp_process_delete_payment() {

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'rcp_delete_payment_nonce' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_payments' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	$current_user = wp_get_current_user();

	rcp_log( sprintf( '%s deleting payment #%d.', $current_user->user_login, absint( $_GET['payment_id'] ) ) );

	$payments = new RCP_Payments();
	$payments->delete( absint( $_GET['payment_id'] ) );
	wp_safe_redirect( admin_url( add_query_arg( 'rcp_message', 'payment_deleted', 'admin.php?page=rcp-payments' ) ) );
	exit;

}
add_action( 'rcp_action_delete_payment', 'rcp_process_delete_payment' );