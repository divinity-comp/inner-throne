<?php
/**
 * Discount Actions
 *
 * @package     restrict-content-pro
 * @subpackage  Admin/Discount Actions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.9
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add a new discount code
 *
 * @since 2.9
 * @return void
 */
function rcp_process_add_discount() {

	if ( ! wp_verify_nonce( $_POST['rcp_add_discount_nonce'], 'rcp_add_discount_nonce' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_discounts' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	$discounts = new RCP_Discounts();

	// Setup unsanitized data
	$data = array(
		'name'                 => $_POST['name'],
		'description'          => $_POST['description'],
		'amount'               => $_POST['amount'],
		'unit'                 => isset( $_POST['unit'] ) && $_POST['unit'] == '%' ? '%' : 'flat',
		'code'                 => $_POST['code'],
		'status'               => 'active',
		'expiration'           => $_POST['expiration'],
		'max_uses'             => $_POST['max'],
		'membership_level_ids' => isset( $_POST['membership_levels'] ) ? $_POST['membership_levels'] : array()
	);

	$add = $discounts->insert( $data );

	if ( is_wp_error( $add ) ) {
		rcp_log( sprintf( 'Error creating new discount code: %s', $add->get_error_message() ), true );
		wp_die( $add );
	}

	if ( $add ) {
		rcp_log( sprintf( 'Successfully added discount #%d.', $add ) );
		$url = admin_url( 'admin.php?page=rcp-discounts&rcp_message=discount_added' );
	} else {
		rcp_log( 'Error inserting new discount code into the database.', true );
		$url = admin_url( 'admin.php?page=rcp-discounts&rcp_message=discount_not_added' );
	}

	wp_safe_redirect( $url );
	exit;

}
add_action( 'rcp_action_add-discount', 'rcp_process_add_discount' );

/**
 * Edit an existing discount code
 *
 * @since 2.9
 * @return void
 */
function rcp_process_edit_discount() {

	if ( ! wp_verify_nonce( $_POST['rcp_edit_discount_nonce'], 'rcp_edit_discount_nonce' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_discounts' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	$discounts = new RCP_Discounts();

	// Setup unsanitized data
	$data = array(
		'name'                 => $_POST['name'],
		'description'          => $_POST['description'],
		'amount'               => $_POST['amount'],
		'unit'                 => isset( $_POST['unit'] ) && $_POST['unit'] == '%' ? '%' : 'flat',
		'code'                 => $_POST['code'],
		'status'               => $_POST['status'],
		'expiration'           => $_POST['expiration'],
		'max_uses'             => $_POST['max'],
		'membership_level_ids' => isset( $_POST['membership_levels'] ) ? $_POST['membership_levels'] : array()
	);

	$update = $discounts->update( $_POST['discount_id'], $data );

	if ( is_wp_error( $update ) ) {
		rcp_log( sprintf( 'Error editing discount code #%d: %s', $_POST['discount_id'], $update->get_error_message() ), true );

		wp_die( $update );
	}

	if ( $update ) {
		rcp_log( sprintf( 'Successfully edited discount #%d.', $_POST['discount_id'] ) );
		$url = admin_url( 'admin.php?page=rcp-discounts&discount-updated=1' );
	} else {
		rcp_log( sprintf( 'Error editing discount #%d.', $_POST['discount_id'] ), true );
		$url = admin_url( 'admin.php?page=rcp-discounts&discount-updated=0' );
	}

	wp_safe_redirect( $url );
	exit;

}
add_action( 'rcp_action_edit-discount', 'rcp_process_edit_discount' );

/**
 * Delete a discount code
 *
 * @since 2.9
 * @return void
 */
function rcp_process_delete_discount() {

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'rcp-delete-discount' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_discounts' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! isset( $_GET['discount_id'] ) ) {
		wp_die( __( 'Please select a discount.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 400 ) );
	}

	$discount_id = absint( $_GET['discount_id'] );
	$discounts   = new RCP_Discounts();
	$discounts->delete( $discount_id );

	rcp_log( sprintf( 'Deleted discount #%d.', $discount_id ) );

	wp_safe_redirect( add_query_arg( 'rcp_message', 'discount_deleted', 'admin.php?page=rcp-discounts' ) );
	exit;

}
add_action( 'rcp_action_delete_discount_code', 'rcp_process_delete_discount' );

/**
 * Activate a discount code
 *
 * @since 2.9
 * @return void
 */
function rcp_process_activate_discount() {

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'rcp-activate-discount' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_discounts' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! isset( $_GET['discount_id'] ) ) {
		wp_die( __( 'Please select a discount.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 400 ) );
	}

	$discounts = new RCP_Discounts();
	$discounts->update( absint( $_GET['discount_id'] ), array( 'status' => 'active' ) );

	rcp_log( sprintf( 'Successfully activated discount #%d.', $_GET['discount_id'] ) );

	wp_safe_redirect( add_query_arg( 'rcp_message', 'discount_activated', 'admin.php?page=rcp-discounts' ) );
	exit;

}
add_action( 'rcp_action_activate_discount', 'rcp_process_activate_discount' );

/**
 * Deactivate a discount code
 *
 * @since 2.9
 * @return void
 */
function rcp_process_deactivate_discount() {

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'rcp-deactivate-discount' ) ) {
		wp_die( __( 'Nonce verification failed.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! current_user_can( 'rcp_manage_discounts' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 403 ) );
	}

	if ( ! isset( $_GET['discount_id'] ) ) {
		wp_die( __( 'Please select a discount.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 400 ) );
	}

	$discounts = new RCP_Discounts();
	$discounts->update( absint( $_GET['discount_id'] ), array( 'status' => 'disabled' ) );

	rcp_log( sprintf( 'Successfully deactivated discount #%d.', $_GET['discount_id'] ) );

	wp_safe_redirect( add_query_arg( 'rcp_message', 'discount_deactivated', 'admin.php?page=rcp-discounts' ) );
	exit;

}
add_action( 'rcp_action_deactivate_discount', 'rcp_process_deactivate_discount' );