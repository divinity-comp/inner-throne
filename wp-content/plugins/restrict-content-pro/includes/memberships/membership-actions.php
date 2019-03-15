<?php
/**
 * Membership Actions
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2018, Restrict Content Pro team
 * @license   GPL2+
 * @since     3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Process membership cancellation via "Cancel" link.
 *
 * @see   rcp_get_membership_cancel_url()
 *
 * @since 3.0
 * @return void
 */
function rcp_process_membership_cancellation() {

	if ( ! isset( $_GET['rcp-action'] ) || $_GET['rcp-action'] !== 'cancel' ) {
		return;
	}

	if ( ! is_user_logged_in() ) {
		return;
	}

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'rcp-cancel-nonce' ) ) {
		return;
	}

	$membership = false;

	if ( ! empty( $_GET['membership-id'] ) ) {
		$membership = rcp_get_membership( absint( $_GET['membership-id'] ) );
	} elseif ( ! empty( $_GET['member-id'] ) ) {
		// Support for old method of cancellation by user ID.
		$customer   = rcp_get_customer_by_user_id( get_current_user_id() );
		$membership = rcp_get_customer_single_membership( $customer->get_user_id() );
	}

	// Bail if not successful in locating a membership.
	if ( empty( $membership ) ) {
		return;
	}

	$success  = $membership->cancel_payment_profile();
	$redirect = remove_query_arg( array( 'rcp-action', '_wpnonce', 'membership-id', 'member-id' ), rcp_get_current_url() );

	if ( is_wp_error( $success ) && false !== strpos( 'paypal', $membership->get_gateway() ) ) {
		// No profile ID stored, so redirect to PayPal to cancel manually
		$redirect = 'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_manage-paylist';
	}

	if ( true === $success ) {

		do_action( 'rcp_process_member_cancellation', get_current_user_id() );

		$redirect = add_query_arg( array(
			'profile'       => 'cancelled',
			'membership_id' => $membership->get_id()
		), $redirect );

	} elseif ( is_wp_error( $success ) ) {

		$redirect = add_query_arg( 'cancellation_failure', urlencode( $success->get_error_message() ), $redirect );

	}

	wp_redirect( $redirect );
	exit;

}

add_action( 'template_redirect', 'rcp_process_membership_cancellation' );

/**
 * When a payment is completed, increment the `times_billed` for the corresponding membership.
 *
 * @param int $payment_id ID of the payment.
 *
 * @since 3.0
 * @return void
 */
function rcp_increment_membership_times_billed( $payment_id ) {

	/**
	 * @var RCP_Payments $rcp_payments_db
	 */
	global $rcp_payments_db;

	$payment = $rcp_payments_db->get_payment( $payment_id );

	if ( empty( $payment->membership_id ) ) {
		return;
	}

	$membership = rcp_get_membership( $payment->membership_id );

	if ( ! empty( $membership ) ) {
		$membership->increment_times_billed();
	}

}

add_action( 'rcp_update_payment_status_complete', 'rcp_increment_membership_times_billed', 999 );

/**
 * Run a second membership status transition hook that includes the new status in the hook name. For example:
 *        rcp_transition_membership_status_active
 *        rcp_transition_membership_status_expired
 *
 * @param string $old_status    Previous status, before the update.
 * @param string $new_status    New status being set.
 * @param int    $membership_id ID of the membership.
 *
 * @since 3.0
 * @return void
 */
function rcp_transition_membership_status_hook( $old_status, $new_status, $membership_id ) {
	/**
	 * @param string $old_status
	 * @param int    $membership_id
	 */
	do_action( 'rcp_transition_membership_status_' . sanitize_key( $new_status ), $old_status, $membership_id );

	if ( has_action( 'rcp_set_status' ) || has_action( 'rcp_set_status_' . $new_status ) ) {
		$membership = rcp_get_membership( $membership_id );
		$member     = new RCP_Member( $membership->get_customer()->get_user_id() );

		/**
		 * @deprecated 3.0 Use `rcp_transition_membership_status` instead.
		 */
		do_action( 'rcp_set_status', $new_status, $member->ID, $old_status, $member );

		/**
		 * @deprecated 3.0 Use `rcp_transition_membership_status_{$new_status}` instead.
		 */
		do_action( "rcp_set_status_{$new_status}", $member->ID, $old_status, $member );
	}
}

add_action( 'rcp_transition_membership_status', 'rcp_transition_membership_status_hook', 10, 3 );

/**
 * Removes the membership level assigned role from a customer when their membership expires.
 *
 * @param string $old_status    Previous membership status.
 * @param int    $membership_id ID of the membership.
 *
 * @since 3.0
 * @return void
 */
function rcp_update_expired_membership_role( $old_status, $membership_id ) {

	$membership       = rcp_get_membership( $membership_id );
	$membership_level = rcp_get_subscription_details( $membership->get_object_id() );
	$default_role     = get_option( 'default_role', 'subscriber' );

	if ( ! empty( $membership_level ) && is_object( $membership_level ) && $membership_level->role !== $default_role ) {
		$user = get_userdata( $membership->get_customer()->get_user_id() );
		$user->remove_role( $membership_level->role );
	}

}

add_action( 'rcp_transition_membership_status_expired', 'rcp_update_expired_membership_role', 10, 2 );

/**
 * When a membership's object ID changes, remove the user role from the old level, and add the user role from the
 * new level. This isn't actually used, but is just here in case people do weird things.
 *
 * @param int $old_object_id Old membership level ID.
 * @param int $new_object_id New membership level ID.
 * @param int $membership_id ID of the membership.
 *
 * @since 3.0
 * @return void
 */
function rcp_update_user_role_on_membership_object_id_transition( $old_object_id, $new_object_id, $membership_id ) {

	if ( $old_object_id == $new_object_id ) {
		return;
	}

	$old_membership_level = rcp_get_subscription_details( $old_object_id );
	$new_membership_level = rcp_get_subscription_details( $new_object_id );
	$membership           = rcp_get_membership( $membership_id );

	if ( empty( $membership ) || empty( $old_membership_level ) || empty( $new_membership_level ) ) {
		return;
	}

	rcp_log( sprintf( 'Detected object ID change for membership #%d. Editing user role.', $membership_id ) );

	$user = get_userdata( $membership->get_customer()->get_user_id() );

	if ( empty( $user ) ) {
		rcp_log( 'Invalid user. Exiting.' );

		return;
	}

	/*
	 * Remove the old role.
	 */
	$old_role = get_option( 'default_role', 'subscriber' );
	$old_role = ! empty( $old_membership_level->role ) ? $old_membership_level->role : $old_role;

	rcp_log( sprintf( 'Removing role %s from user #%d.', $old_role, $user->ID ) );

	$user->remove_role( $old_role );

	/*
	 * Add the new role.
	 */
	$new_role = ! empty( $new_membership_level->role ) ? $new_membership_level->role : get_option( 'default_role', 'subscriber' );

	rcp_log( sprintf( 'Adding role %s to user #%d.', $new_role, $user->ID ) );

	$user->add_role( apply_filters( 'rcp_default_user_level', $new_role, $new_membership_level->id ) );

}

add_action( 'rcp_transition_membership_object_id', 'rcp_update_user_role_on_membership_object_id_transition', 10, 3 );

/**
 * Run activation sequence if new membership is being added with status "active". This adds the user role to
 * the customer and sends the activation email.
 *
 * @param int   $membership_id
 * @param array $data
 *
 * @since 3.0
 */
function rcp_activate_membership_on_insert( $membership_id, $data ) {

	$membership = rcp_get_membership( $membership_id );

	if ( 'active' == $membership->get_status() ) {
		$membership->activate();
	}

}

add_action( 'rcp_new_membership_added', 'rcp_activate_membership_on_insert', 10, 2 );

/**
 * If multiple memberships is not enabled:
 * When a membership is activated, disable all others.
 *
 * @param int            $membership_id
 * @param RCP_Membership $membership
 *
 * @since 3.0
 * @return void
 */
function rcp_disable_memberships_on_activate( $membership_id, $membership ) {

	if ( ! rcp_multiple_memberships_enabled() ) {
		$membership->get_customer()->disable_memberships( $membership_id );
	}

}

add_action( 'rcp_membership_pre_activate', 'rcp_disable_memberships_on_activate', 10, 2 );

/**
 * Disable memberships when a user account is deleted. This cancels recurring billing and disables the
 * memberships, as they're irrelevant without a user account.
 *
 * @param int  $user_id  ID of the user being deleted.
 * @param bool $reassign ID of the user to reassign posts and links to.
 *
 * @since 3.0
 * @return void
 */
function rcp_disable_memberships_on_user_delete( $user_id, $reassign ) {

	$customer = rcp_get_customer_by_user_id( $user_id );

	// No customer - bail.
	if ( empty( $customer ) ) {
		return;
	}

	rcp_log( sprintf( 'Disabling memberships for customer #%d, as associated user account #%d was deleted.', $customer->get_id(), $user_id ) );

	// Disable memberships.
	$customer->disable_memberships();

	// Delete customer.
	rcp_delete_customer( $customer->get_id() );

}

add_action( 'delete_user', 'rcp_disable_memberships_on_user_delete', 10, 2 );