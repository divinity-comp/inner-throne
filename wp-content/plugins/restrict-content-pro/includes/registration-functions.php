<?php
/**
 * Registration Functions
 *
 * Processes the registration form
 *
 * @package     Restrict Content Pro
 * @subpackage  Registration Functions
 * @copyright   Copyright (c) 2017, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Register a new user
 *
 * @access public
 * @since  1.0
 * @return void
 */
function rcp_process_registration() {

	// check nonce
	if ( ! ( isset( $_POST["rcp_register_nonce"] ) && wp_verify_nonce( $_POST['rcp_register_nonce'], 'rcp-register-nonce' ) ) ) {
		return;
	}

	global $rcp_options, $rcp_levels_db;

	$membership_level_id = rcp_get_registration()->get_membership_level_id();
	$membership_level    = $rcp_levels_db->get_level( $membership_level_id );
	$discount            = isset( $_POST['rcp_discount'] ) ? sanitize_text_field( strtolower( $_POST['rcp_discount'] ) ) : '';
	$price               = number_format( (float) $membership_level->price, 2 );
	$price               = str_replace( ',', '', $price );
	$auto_renew          = rcp_registration_is_recurring();
	$trial_duration      = $rcp_levels_db->trial_duration( $membership_level_id );
	$trial_duration_unit = $rcp_levels_db->trial_duration_unit( $membership_level_id );
	// if both today's total and the recurring total are 0, the there is a full discount
	// if this is not a recurring membership only check today's total
	$full_discount     = ( $auto_renew ) ? ( rcp_get_registration()->get_total() == 0 && rcp_get_registration()->get_recurring_total() == 0 ) : ( rcp_get_registration()->get_total() == 0 );
	$customer          = rcp_get_customer_by_user_id();
	$has_trialed       = ! empty( $customer ) ? $customer->has_trialed() : false;
	$registration_type = 'new'; // Whether this is a `new` membership, `renewal`, or `upgrade`.

	// get the selected payment method/gateway
	if ( ! isset( $_POST['rcp_gateway'] ) ) {
		$gateway = 'paypal';
	} else {
		$gateway = sanitize_text_field( $_POST['rcp_gateway'] );
	}

	// Change gateway to "free" if this membership doesn't require payment.
	if ( $full_discount || empty( $price ) ) {
		$gateway = 'free';
	}

	rcp_log( sprintf( 'Started new registration for membership level #%d via %s.', $membership_level_id, $gateway ) );

	/***********************
	 * validate the form
	 ***********************/

	do_action( 'rcp_before_form_errors', $_POST, $customer );

	$is_ajax = isset( $_POST['rcp_ajax'] );

	$user_data = rcp_validate_user_data();

	if ( ! rcp_is_registration() ) {
		// no membership level was chosen
		rcp_errors()->add( 'no_level', __( 'Please choose a membership level', 'rcp' ), 'register' );
	}

	if ( $membership_level_id && $price == 0 && $membership_level->duration > 0 && $has_trialed ) {
		// this ensures that users only sign up for a free trial once
		rcp_errors()->add( 'free_trial_used', __( 'You may only sign up for a free trial once', 'rcp' ), 'register' );
	}

	if ( ! empty( $discount ) ) {

		// make sure we have a valid discount
		if ( rcp_validate_discount( $discount, $membership_level_id ) ) {

			// check if the user has already used this discount
			if ( $price > 0 && ! $user_data['need_new'] && rcp_user_has_used_discount( $user_data['id'], $discount ) && apply_filters( 'rcp_discounts_once_per_user', false, $discount, $membership_level_id ) ) {
				rcp_errors()->add( 'discount_already_used', __( 'You can only use the discount code once', 'rcp' ), 'register' );
			}

		} else {
			// the entered discount code is incorrect
			rcp_errors()->add( 'invalid_discount', __( 'The discount you entered is invalid', 'rcp' ), 'register' );
		}

	}

	// Validate extra fields in gateways with the 2.1+ gateway API
	if ( ! has_action( 'rcp_gateway_' . $gateway ) && $price > 0 && ! $full_discount ) {

		$gateways    = new RCP_Payment_Gateways;
		$gateway_var = $gateways->get_gateway( $gateway );
		$gateway_obj = new $gateway_var['class'];
		$gateway_obj->validate_fields();
	}

	// If enabled, terms agreement must be checked.
	if ( ! empty( $rcp_options['enable_terms'] ) && ! isset( $_POST['rcp_agree_to_terms'] ) ) {
		rcp_errors()->add( 'terms_not_agreed', __( 'You must agree to the terms and conditions', 'rcp' ), 'register' );
	}

	// If enabled, privacy policy agreement must be checked.
	if ( ! empty( $rcp_options['enable_privacy_policy'] ) && ! isset( $_POST['rcp_agree_to_privacy_policy'] ) ) {
		rcp_errors()->add( 'privacy_policy_not_agreed', __( 'You must agree to the privacy policy', 'rcp' ), 'register' );
	}

	do_action( 'rcp_form_errors', $_POST, $customer );

	// retrieve all error messages, if any
	$errors = rcp_errors()->get_error_messages();

	if ( ! empty( $errors ) && $is_ajax ) {
		rcp_log( sprintf( 'Registration cancelled with the following errors: %s.', implode( ', ', $errors ) ) );

		wp_send_json_error( array(
			'success' => false,
			'errors'  => rcp_get_error_messages_html( 'register' ),
			'nonce'   => wp_create_nonce( 'rcp-register-nonce' ),
			'gateway' => array(
				'slug'     => $gateway,
				'supports' => ! empty( $gateway_obj->supports ) ? $gateway_obj->supports : false
			)
		) );

	} elseif ( $is_ajax ) {
		wp_send_json_success( array(
			'success' => true,
			'total'   => rcp_get_registration()->get_total(),
			'gateway' => array(
				'slug'     => $gateway,
				'supports' => ! empty( $gateway_obj->supports ) ? $gateway_obj->supports : false
			),
			'level'   => array(
				'trial' => ! empty( $trial_duration )
			)
		) );

	}

	// only create the user if there are no errors
	if ( ! empty( $errors ) ) {
		return;
	}

	if ( $user_data['need_new'] ) {

		$display_name = trim( $user_data['first_name'] . ' ' . $user_data['last_name'] );

		$user_data['id'] = wp_insert_user( array(
				'user_login'      => $user_data['login'],
				'user_pass'       => $user_data['password'],
				'user_email'      => $user_data['email'],
				'first_name'      => $user_data['first_name'],
				'last_name'       => $user_data['last_name'],
				'display_name'    => ! empty( $display_name ) ? $display_name : $user_data['login'],
				'user_registered' => date( 'Y-m-d H:i:s' )
			)
		);

	}

	if ( empty( $user_data['id'] ) ) {
		return;
	}

	// Create the customer record if it doesn't already exist.
	if ( empty( $customer ) ) {
		$customer_id = rcp_add_customer( array( 'user_id' => absint( $user_data['id'] ) ) );
		$customer    = rcp_get_customer( $customer_id );
	}

	$user_id = $user_data['id'];

	// Save agreement to terms and privacy policy.
	if ( ! empty( $_POST['rcp_agree_to_terms'] ) ) {
		$terms_agreed = get_user_meta( $user_id, 'rcp_terms_agreed', true );

		if ( ! is_array( $terms_agreed ) ) {
			$terms_agreed = array();
		}

		$terms_agreed[] = current_time( 'timestamp' );

		update_user_meta( $user_id, 'rcp_terms_agreed', $terms_agreed );
	}
	if ( ! empty( $_POST['rcp_agree_to_privacy_policy'] ) ) {
		$privacy_policy_agreed = get_user_meta( $user_id, 'rcp_privacy_policy_agreed', true );

		if ( ! is_array( $privacy_policy_agreed ) ) {
			$privacy_policy_agreed = array();
		}

		$privacy_policy_agreed[] = current_time( 'timestamp' );

		update_user_meta( $user_id, 'rcp_privacy_policy_agreed', $privacy_policy_agreed );
	}

	update_user_meta( $user_id, '_rcp_new_subscription', '1' );

	$subscription_key = rcp_generate_subscription_key();

	$previous_membership       = rcp_get_customer_single_membership( $customer->get_id() );
	$previous_membership_level = false;

	if ( $previous_membership ) {
		// Backwards compat.
		update_user_meta( $user_id, '_rcp_old_subscription_id', $previous_membership->get_object_id() );

		$previous_membership_level = rcp_get_subscription_details( $previous_membership->get_object_id() );

		if ( $previous_membership->get_object_id() == $membership_level_id ) {
			$registration_type = 'renewal';
		} elseif ( $previous_membership->is_active() ) {
			$registration_type = 'upgrade';
		}
	}

	rcp_log( sprintf( 'Registration type: %s.', $registration_type ) );

	// Delete pending payment ID. A new one may be created for paid membership.
	delete_user_meta( $user_id, 'rcp_pending_payment_id' );

	// Delete old pending data that may have been added in previous versions.
	delete_user_meta( $user_id, 'rcp_pending_expiration_date' );
	delete_user_meta( $user_id, 'rcp_pending_subscription_key' );

	// Backwards compatibility pre-2.9: set pending subscription key.
	update_user_meta( $user_id, 'rcp_pending_subscription_key', $subscription_key );

	// Backwards compatibility: we still need to set this for Hard-set Expiration dates.
	update_user_meta( $user_id, 'rcp_pending_subscription_level', $membership_level->id );

	$amount = ( ! empty( $trial_duration ) && ! $has_trialed ) ? 0.00 : rcp_get_registration()->get_total();

	// Create a pending membership if this is not a manual renewal.
	if ( 'renewal' != $registration_type ) {
		$membership_data = array(
			'customer_id'      => $customer->get_id(),
			'object_id'        => $membership_level->id,
			'object_type'      => 'membership',
			'initial_amount'   => $amount,
			'recurring_amount' => empty( $membership_level->duration ) ? 0.00 : rcp_get_registration()->get_recurring_total( true, false ),
			'auto_renew'       => $auto_renew,
			'maximum_renewals' => ! empty( $membership_level->maximum_renewals ) ? absint( $membership_level->maximum_renewals ) : 0,
			'status'           => 'pending',
			'gateway'          => $gateway,
			'subscription_key' => $subscription_key
		);

		if ( 'upgrade' == $registration_type && ! empty( $previous_membership ) ) {
			$membership_data['upgraded_from'] = $previous_membership->get_id();
		}

		$membership_id = $customer->add_membership( $membership_data );

		if ( 'upgrade' == $registration_type ) {
			$membership = rcp_get_membership( $membership_id );
			$membership->add_note( sprintf( __( 'Upgraded from %s (membership #%d).', 'rcp' ), $previous_membership_level->name, $previous_membership->get_id() ) );
		}
	} elseif ( ! empty( $previous_membership ) ) {
		// If they're renewing an existing membership then we use the existing membership record.
		$membership_id = $previous_membership->get_id();

		$new_membership_data = array(
			'auto_renew'       => $auto_renew,
			'gateway'          => $gateway,
			'recurring_amount' => empty( $membership_level->duration ) ? 0.00 : rcp_get_registration()->get_recurring_total( true, false )
		);

		if ( ! $previous_membership->is_active() ) {
			$new_membership_data['status']          = 'pending';
			$new_membership_data['expiration_date'] = $previous_membership->calculate_expiration( true );
		}

		rcp_update_membership( $membership_id, $new_membership_data );

		rcp_log( sprintf( 'Using existing membership #%d for payment.', $membership_id ) );
	}

	// Create a pending payment
	$credits = ! empty( $previous_membership ) ? $previous_membership->get_prorate_credit_amount() : 0;

	$payment_data = array(
		'date'             => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
		'subscription'     => $membership_level->name,
		'object_id'        => $membership_level->id,
		'object_type'      => 'subscription',
		'gateway'          => $gateway,
		'subscription_key' => $subscription_key,
		'amount'           => $amount,
		'user_id'          => $user_id,
		'customer_id'      => $customer->get_id(),
		'membership_id'    => $membership_id,
		'status'           => 'pending',
		'subtotal'         => $membership_level->price,
		'credits'          => $credits,
		'fees'             => rcp_get_registration()->get_total_fees() + $credits,
		'discount_amount'  => rcp_get_registration()->get_total_discounts(),
		'discount_code'    => $discount,
	);

	$rcp_payments = new RCP_Payments();
	$payment_id   = $rcp_payments->insert( $payment_data );
	update_user_meta( $user_data['id'], 'rcp_pending_payment_id', $payment_id );

	if ( 'upgrade' == $registration_type ) {
		// Flag the member as having just upgraded
		update_user_meta( $user_id, '_rcp_just_upgraded', current_time( 'timestamp' ) );
	}

	/**
	 * Triggers after all the form data has been processed, but before the user is sent to the payment gateway.
	 * The user's membership is pending at this point.
	 *
	 * @param array                $_POST               Posted data.
	 * @param int                  $user_id             ID of the user registering.
	 * @param float                $price               Price of the membership.
	 * @param int                  $payment_id          ID of the pending payment associated with this registration.
	 * @param RCP_Customer         $customer            Customer object.
	 * @param int                  $membership_id       ID of the new pending membership.
	 * @param RCP_Membership|false $previous_membership Previous membership object, or false if none.
	 * @param string               $registration_type   Type of registration: 'new', 'renewal', or 'upgrade'.
	 */
	do_action( 'rcp_form_processing', $_POST, $user_id, $price, $payment_id, $customer, $membership_id, $previous_membership, $registration_type );

	// process a paid membership
	if ( $price > '0' || $trial_duration ) {

		if ( ! empty( $discount ) && $full_discount ) {

			// Full discount with auto renew should never expire.
			if ( '2' != rcp_get_auto_renew_behavior() ) {
				rcp_update_membership( $membership_id, array( 'expiration_date' => null ) );
			}

			// Complete payment. This also activates the membership.
			$rcp_payments->update( $payment_id, array( 'status' => 'complete' ) );

			rcp_log( sprintf( 'Completed registration to membership level #%d with full discount for customer #%d.', $membership_level_id, $customer->get_id() ) );

			// @todo this could be better
			if ( $user_data['need_new'] ) {
				wp_signon( array(
					'user_login'    => $user_data['login'],
					'user_password' => $user_data['password']
				) );
			}

			wp_redirect( rcp_get_return_url( $user_id ) );
			exit;

		}

		// log the new user in
		if ( $user_data['need_new'] ) {
			wp_signon( array(
				'user_login'    => $user_data['login'],
				'user_password' => $user_data['password']
			) );
		}

		$redirect = rcp_get_return_url( $user_id );

		$subscription_data = array(
			'price'               => rcp_get_registration()->get_total( true, false ), // get total without the fee
			'recurring_price'     => rcp_get_registration()->get_recurring_total( true, false ), // get recurring total without the fee
			'discount'            => rcp_get_registration()->get_total_discounts(),
			'discount_code'       => $discount,
			'fee'                 => rcp_get_registration()->get_total_fees(),
			'length'              => $membership_level->duration,
			'length_unit'         => strtolower( $membership_level->duration_unit ),
			'subscription_id'     => $membership_level->id,
			'subscription_name'   => $membership_level->name,
			'key'                 => $subscription_key,
			'user_id'             => $user_data['id'],
			'user_name'           => $user_data['login'],
			'user_email'          => $user_data['email'],
			'currency'            => rcp_get_currency(),
			'auto_renew'          => $auto_renew,
			'return_url'          => $redirect,
			'new_user'            => $user_data['need_new'],
			'trial_duration'      => $trial_duration,
			'trial_duration_unit' => $trial_duration_unit,
			'trial_eligible'      => ! $has_trialed,
			'post_data'           => $_POST,
			'payment_id'          => $payment_id,
			'membership_id'       => $membership_id,
			'customer'            => $customer
		);

		// if giving the user a credit, make sure the credit does not exceed the first payment
		if ( $subscription_data['fee'] < 0 && abs( $subscription_data['fee'] ) > $subscription_data['price'] ) {
			$subscription_data['fee'] = -1 * $subscription_data['price'];
		}

		update_user_meta( $user_data['id'], 'rcp_pending_subscription_amount', round( $subscription_data['price'] + $subscription_data['fee'], 2 ) );

		// send all of the membership data off for processing by the gateway
		rcp_send_to_gateway( $gateway, apply_filters( 'rcp_subscription_data', $subscription_data ) );

		// process a free or trial membership
	} else {

		// Complete payment. This also activates the membership.
		$rcp_payments->update( $payment_id, array( 'status' => 'complete' ) );

		if ( $user_data['need_new'] ) {

			if ( ! isset( $rcp_options['disable_new_user_notices'] ) ) {

				// send an email to the admin alerting them of the registration
				wp_new_user_notification( $user_id );

			}

			// @todo this could be better
			wp_signon( array(
				'user_login'    => $user_data['login'],
				'user_password' => $user_data['password']
			) );

		}

		rcp_log( sprintf( 'Completed free registration to membership level #%d for customer #%d.', $membership_level_id, $customer->get_id() ) );

		// send the newly created user to the redirect page after logging them in
		wp_redirect( rcp_get_return_url( $user_id ) );
		exit;

	} // end price check

}

add_action( 'init', 'rcp_process_registration', 100 );
add_action( 'wp_ajax_rcp_process_register_form', 'rcp_process_registration', 100 );
add_action( 'wp_ajax_nopriv_rcp_process_register_form', 'rcp_process_registration', 100 );

/**
 * Provide the default registration values when checking out with Stripe Checkout.
 *
 * @return void
 */
function rcp_handle_stripe_checkout() {

	if ( isset( $_POST['rcp_ajax'] ) || empty( $_POST['rcp_gateway'] ) || empty( $_POST['stripeEmail'] ) || 'stripe_checkout' !== $_POST['rcp_gateway'] ) {
		return;
	}

	if ( empty( $_POST['rcp_user_email'] ) ) {
		$_POST['rcp_user_email'] = $_POST['stripeEmail'];
	}

	if ( empty( $_POST['rcp_user_login'] ) ) {
		$_POST['rcp_user_login'] = $_POST['stripeEmail'];
	}

	if ( empty( $_POST['rcp_user_first'] ) ) {
		$user_email              = explode( '@', $_POST['rcp_user_email'] );
		$_POST['rcp_user_first'] = $user_email[0];
	}

	if ( empty( $_POST['rcp_user_last'] ) ) {
		$_POST['rcp_user_last'] = '';
	}

	if ( empty( $_POST['rcp_user_pass'] ) ) {
		$_POST['rcp_user_pass'] = wp_generate_password();
	}

	if ( empty( $_POST['rcp_user_pass_confirm'] ) ) {
		$_POST['rcp_user_pass_confirm'] = $_POST['rcp_user_pass'];
	}

}

add_action( 'rcp_before_form_errors', 'rcp_handle_stripe_checkout' );


/**
 * Validate and setup the user data for registration
 *
 * @access      public
 * @since       1.5
 * @return      array
 */
function rcp_validate_user_data() {

	$user = array();

	if ( ! is_user_logged_in() ) {
		$user['id']               = 0;
		$user['login']            = sanitize_text_field( $_POST['rcp_user_login'] );
		$user['email']            = sanitize_text_field( $_POST['rcp_user_email'] );
		$user['first_name']       = sanitize_text_field( $_POST['rcp_user_first'] );
		$user['last_name']        = sanitize_text_field( $_POST['rcp_user_last'] );
		$user['password']         = sanitize_text_field( $_POST['rcp_user_pass'] );
		$user['password_confirm'] = sanitize_text_field( $_POST['rcp_user_pass_confirm'] );
		$user['need_new']         = true;
	} else {
		$userdata         = get_userdata( get_current_user_id() );
		$user['id']       = $userdata->ID;
		$user['login']    = $userdata->user_login;
		$user['email']    = $userdata->user_email;
		$user['need_new'] = false;
	}


	if ( $user['need_new'] ) {
		if ( username_exists( $user['login'] ) ) {
			// Username already registered
			rcp_errors()->add( 'username_unavailable', __( 'Username already taken', 'rcp' ), 'register' );
		}
		if ( ! rcp_validate_username( $user['login'] ) ) {
			// invalid username
			rcp_errors()->add( 'username_invalid', __( 'Invalid username', 'rcp' ), 'register' );
		}
		if ( empty( $user['login'] ) ) {
			// empty username
			rcp_errors()->add( 'username_empty', __( 'Please enter a username', 'rcp' ), 'register' );
		}
		if ( ! is_email( $user['email'] ) ) {
			//invalid email
			rcp_errors()->add( 'email_invalid', __( 'Invalid email', 'rcp' ), 'register' );
		}
		if ( email_exists( $user['email'] ) ) {
			//Email address already registered
			rcp_errors()->add( 'email_used', sprintf(
				__( 'This email address is already in use. If this is your email address, please <a href="%s">log in</a> and try again.', 'rcp' ),
				esc_url( rcp_get_login_url() )
			), 'register' );
		}
		if ( empty( $user['password'] ) ) {
			// passwords do not match
			rcp_errors()->add( 'password_empty', __( 'Please enter a password', 'rcp' ), 'register' );
		}
		if ( $user['password'] !== $user['password_confirm'] ) {
			// passwords do not match
			rcp_errors()->add( 'password_mismatch', __( 'Passwords do not match', 'rcp' ), 'register' );
		}
	}

	return apply_filters( 'rcp_user_registration_data', $user );
}


/**
 * Get the registration success/return URL
 *
 * @param       $user_id int The user ID we have just registered
 *
 * @access      public
 * @since       1.5
 * @return      string
 */
function rcp_get_return_url( $user_id = 0 ) {

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	global $rcp_options;

	if ( isset( $rcp_options['redirect'] ) ) {
		$redirect = get_permalink( $rcp_options['redirect'] );
	} else {
		$redirect = home_url();
	}
	return apply_filters( 'rcp_return_url', $redirect, $user_id );
}

/**
 * Determine if the current page is a registration page
 *
 * @access      public
 * @since       2.0
 * @return      bool
 */
function rcp_is_registration_page() {

	global $rcp_options, $post;

	$ret = false;

	if ( isset( $rcp_options['registration_page'] ) ) {
		$ret = is_page( $rcp_options['registration_page'] );
	}

	if ( ! empty( $post ) && has_shortcode( $post->post_content, 'register_form' ) ) {
		$ret = true;
	}

	return apply_filters( 'rcp_is_registration_page', $ret );
}

/**
 * Get the auto renew behavior
 *
 * 1 == All memberships auto renew
 * 2 == No memberships auto renew
 * 3 == Customer chooses whether to auto renew
 *
 * @access      public
 * @since       2.0
 * @return      int
 */
function rcp_get_auto_renew_behavior() {

	global $rcp_options, $rcp_level;


	// Check for old disable auto renew option
	if ( isset( $rcp_options['disable_auto_renew'] ) ) {
		$rcp_options['auto_renew'] = '2';
		unset( $rcp_options['disable_auto_renew'] );
		update_option( 'rcp_settings', $rcp_options );
	}

	$behavior = isset( $rcp_options['auto_renew'] ) ? $rcp_options['auto_renew'] : '3';

	if ( $rcp_level ) {
		$level = rcp_get_subscription_details( $rcp_level );
		if ( $level->price == '0' ) {
			$behavior = '2';
		}
	}

	return apply_filters( 'rcp_auto_renew_behavior', $behavior );
}

/**
 * When new memberships are registered, a flag is set
 *
 * This removes the flag as late as possible so other systems can hook into
 * rcp_set_status and perform actions on new memberships
 *
 * @deprecated 3.0 Still here for backwards compatibility.
 *
 * @param string $status  User's membership status.
 * @param int    $user_id ID of the member.
 *
 * @access      public
 * @since       2.3.6
 * @return      void
 */
function rcp_remove_new_subscription_flag( $status, $user_id ) {

	if ( ! in_array( $status, array( 'active', 'free' ) ) ) {
		return;
	}

	delete_user_meta( $user_id, '_rcp_old_subscription_id' );
	delete_user_meta( $user_id, '_rcp_new_subscription' );
}

add_action( 'rcp_set_status', 'rcp_remove_new_subscription_flag', 9999999, 2 );

/**
 * When upgrading memberships, the new level / key are stored as pending. Once payment is received, the pending
 * values are set as the permanent values.
 *
 * See https://github.com/restrictcontentpro/restrict-content-pro/issues/294
 *
 * @deprecated 3.0 Still here for backwards compatibility.
 *
 * @param string     $status     User's membership status.
 * @param int        $user_id    ID of the user.
 * @param string     $old_status Previous membership status.
 * @param RCP_Member $member     Member object.
 *
 * @access      public
 * @since       2.4.3
 * @return      void
 */
function rcp_set_pending_subscription_on_upgrade( $status, $user_id, $old_status, $member ) {

	if ( 'active' !== $status ) {
		return;
	}

	$subscription_id  = get_user_meta( $user_id, 'rcp_pending_subscription_level', true );
	$subscription_key = get_user_meta( $user_id, 'rcp_pending_subscription_key', true );

	if ( ! empty( $subscription_id ) && ! empty( $subscription_key ) ) {

		//$member->set_subscription_id( $subscription_id );
		//$member->set_subscription_key( $subscription_key );

		delete_user_meta( $user_id, 'rcp_pending_subscription_level' );
		delete_user_meta( $user_id, 'rcp_pending_subscription_key' );

	}
}

add_action( 'rcp_set_status', 'rcp_set_pending_subscription_on_upgrade', 10, 4 );

/**
 * Determine if this registration is recurring
 *
 * @since 2.5
 * @return bool
 */
function rcp_registration_is_recurring() {

	$auto_renew = false;

	if ( '3' == rcp_get_auto_renew_behavior() ) {
		$auto_renew = isset( $_POST['rcp_auto_renew'] );
	}

	if ( '1' == rcp_get_auto_renew_behavior() ) {
		$auto_renew = true;
	}

	// make sure this gateway supports recurring payments
	if ( $auto_renew && ! empty( $_POST['rcp_gateway'] ) ) {
		$auto_renew = rcp_gateway_supports( sanitize_text_field( $_POST['rcp_gateway'] ), 'recurring' );
	}

	if ( $auto_renew && ! empty( $_POST['rcp_level'] ) ) {
		$details = rcp_get_subscription_details( $_POST['rcp_level'] );

		// check if this is an unlimited or free membership
		if ( empty( $details->duration ) || empty( $details->price ) ) {
			$auto_renew = false;
		}
	}

	if ( ! rcp_get_registration_recurring_total() > 0 ) {
		$auto_renew = false;
	}

	return apply_filters( 'rcp_registration_is_recurring', $auto_renew );

}

/**
 * Add the registration total before the gateway fields
 *
 * @since 2.5
 * @return void
 */
function rcp_registration_total_field() {
	?>
	<div class="rcp_registration_total"></div>
	<?php
}

add_action( 'rcp_after_register_form_fields', 'rcp_registration_total_field' );

/**
 * Get formatted total for this registration
 *
 * @param bool $echo Whether or not to echo the value.
 *
 * @since      2.5
 * @return string|bool
 */
function rcp_registration_total( $echo = true ) {
	$total = rcp_get_registration_total();

	// the registration has not been setup yet
	if ( false === $total ) {
		return false;
	}

	if ( 0 < $total ) {
		$total = rcp_currency_filter( $total );
	} else {
		$total = __( 'free', 'rcp' );
	}

	global $rcp_levels_db;

	$level               = $rcp_levels_db->get_level( rcp_get_registration()->get_membership_level_id() );
	$trial_duration      = $rcp_levels_db->trial_duration( $level->id );
	$trial_duration_unit = $rcp_levels_db->trial_duration_unit( $level->id );

	if ( ! empty( $trial_duration ) && ! rcp_customer_has_trialed() ) {
		$total = sprintf( __( 'Free trial - %s', 'rcp' ), $trial_duration . ' ' . rcp_filter_duration_unit( $trial_duration_unit, $trial_duration ) );
	}

	$total = apply_filters( 'rcp_registration_total', $total );

	if ( $echo ) {
		echo $total;
	}

	return $total;
}

/**
 * Get the total for this registration
 *
 * @since  2.5
 * @return float|false
 */
function rcp_get_registration_total() {

	if ( ! rcp_is_registration() ) {
		return false;
	}

	return rcp_get_registration()->get_total();
}

/**
 * Get formatted recurring total for this registration
 *
 * @param bool $echo Whether or not to echo the value.
 *
 * @since  2.5
 * @return string|bool
 */
function rcp_registration_recurring_total( $echo = true ) {
	$total = rcp_get_registration_recurring_total();

	// the registration has not been setup yet
	if ( false === $total ) {
		return false;
	}

	if ( 0 < $total ) {
		$total        = rcp_currency_filter( $total );
		$subscription = rcp_get_subscription_details( rcp_get_registration()->get_membership_level_id() );

		if ( $subscription->duration == 1 ) {
			$total .= '/' . rcp_filter_duration_unit( $subscription->duration_unit, 1 );
		} else {
			$total .= sprintf( __( ' every %s %s', 'rcp' ), $subscription->duration, rcp_filter_duration_unit( $subscription->duration_unit, $subscription->duration ) );
		}
	} else {
		$total = __( 'free', 'rcp' );;
	}

	$total = apply_filters( 'rcp_registration_recurring_total', $total );

	if ( $echo ) {
		echo $total;
	}

	return $total;
}

/**
 * Get the recurring total payment
 *
 * @since 2.5
 * @return bool|int
 */
function rcp_get_registration_recurring_total() {

	if ( ! rcp_is_registration() ) {
		return false;
	}

	return rcp_get_registration()->get_recurring_total();
}

/**
 * Is the registration object setup?
 *
 * @since 2.5
 * @return bool
 */
function rcp_is_registration() {
	return (bool) rcp_get_registration()->get_membership_level_id();
}

/**
 * Get the registration object. If it hasn't been setup, setup an empty
 * registration object.
 *
 * @return RCP_Registration
 */
function rcp_get_registration() {
	global $rcp_registration;

	// setup empty registration object if one doesn't exist
	if ( ! is_a( $rcp_registration, 'RCP_Registration' ) ) {
		rcp_setup_registration();
	}

	return $rcp_registration;
}

/**
 * Setup the registration object
 *
 * Auto setup cart on page load if $_POST parameters are found
 *
 * @param int|null    $level_id ID of the membership level for this registration.
 * @param string|null $discount Discount code to apply to this registration.
 *
 * @since  2.5
 * @return void
 */
function rcp_setup_registration( $level_id = null, $discount = null ) {
	global $rcp_registration;

	$rcp_registration = new RCP_Registration( $level_id, $discount );
	do_action( 'rcp_setup_registration', $level_id, $discount );
}

/**
 * Automatically setup the registration object
 *
 * @uses rcp_setup_registration()
 *
 * @return void
 */
function rcp_setup_registration_init() {

	if ( empty( $_POST['rcp_level'] ) ) {
		return;
	}

	$level_id = abs( $_POST['rcp_level'] );
	$discount = ! empty( $_REQUEST['discount'] ) ? sanitize_text_field( $_REQUEST['discount'] ) : null;
	$discount = ! empty( $_POST['rcp_discount'] ) ? sanitize_text_field( $_POST['rcp_discount'] ) : $discount;

	rcp_setup_registration( $level_id, $discount );
}

add_action( 'init', 'rcp_setup_registration_init' );


/**
 * Filter levels to only show valid upgrade levels
 *
 * @since 2.5
 * @return array Array of membership levels.
 */
function rcp_filter_registration_upgrade_levels() {

	remove_filter( 'rcp_get_levels', 'rcp_filter_registration_upgrade_levels' );

	$levels = rcp_get_upgrade_paths();

	add_filter( 'rcp_get_levels', 'rcp_filter_registration_upgrade_levels' );

	return $levels;

}

/**
 * Hook into registration page and filter upgrade path
 */
add_action( 'rcp_before_subscription_form_fields', 'rcp_filter_registration_upgrade_levels' );

/**
 * Add prorate credit to member registration
 *
 * @param RCP_Registration $registration
 *
 * @since 2.5
 * @return void
 */
function rcp_add_prorate_fee( $registration ) {
	if ( ! $amount = rcp_get_member_prorate_credit() ) {
		return;
	}

	// If renewing their current membership, no proration.
	if ( is_user_logged_in() && rcp_get_subscription_id() == $registration->get_membership_level_id() ) {
		return;
	}

	$registration->add_fee( -1 * $amount, __( 'Proration Credit', 'rcp' ), false, true );

	rcp_log( sprintf( 'Adding %.2f proration credits to registration for user #%d.', $amount, get_current_user_id() ) );
}

add_action( 'rcp_registration_init', 'rcp_add_prorate_fee' );

/**
 * Add message to checkout specifying proration credit
 *
 * @since 2.5
 * @return void
 */
function rcp_add_prorate_message() {
	$upgrade_paths = rcp_get_upgrade_paths( get_current_user_id() );
	$has_upgrade   = false;

	/*
	 * The proration message should only be shown if the user has at least one upgrade
	 * option available where the price is greater than $0.
	 */
	if ( ! empty( $upgrade_paths ) ) {
		foreach ( $upgrade_paths as $subscription_level ) {
			if ( $subscription_level->id != rcp_get_subscription_id() && ( $subscription_level->price > 0 || $subscription_level->fee > 0 ) ) {
				$has_upgrade = true;
				break;
			}
		}
	}

	if ( ( ! $amount = rcp_get_member_prorate_credit() ) || ( ! $has_upgrade ) ) {
		return;
	}

	$prorate_message = sprintf( '<p>%s</p>', __( 'If you upgrade or downgrade your account, the new membership will be prorated up to %s for the first payment. Prorated prices are shown below.', 'rcp' ) );

	printf( apply_filters( 'rcp_registration_prorate_message', $prorate_message ), esc_html( rcp_currency_filter( $amount ) ) );
}

add_action( 'rcp_before_subscription_form_fields', 'rcp_add_prorate_message' );

/**
 * Removes the reminder sent flags when the member's status is set to active.
 * This allows the reminders to be re-sent for the next membership period.
 *
 * @deprecated 3.0 In favour of `rcp_remove_membership_reminder_flags()`.
 * @see rcp_remove_membership_reminder_flags()
 *
 * @param string     $status     User's membership status.
 * @param int        $user_id    ID of the user.
 * @param string     $old_status Old status from before the update.
 * @param RCP_Member $member     Member object.
 *
 * @since 2.5.5
 * @return void
 */
function rcp_remove_expiring_soon_email_sent_flag( $status, $user_id, $old_status, $member ) {

	if ( 'active' !== $status ) {
		return;
	}

	global $wpdb;

	$query = $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE %s", $user_id, '_rcp_reminder_sent_' . absint( $member->get_subscription_id() ) . '_%' );
	$wpdb->query( $query );

}
//add_action( 'rcp_set_status', 'rcp_remove_expiring_soon_email_sent_flag', 10, 4 );

/**
 * Removes the expiration/renewal reminder flags when a membership is renewed. This allows the reminders to be re-sent
 * for the next membership period.
 *
 * @param string         $expiration       New expiration date.
 * @param int            $membership_id    ID of the membership.
 * @param RCP_Membership $membership       Membership object.
 *
 * @since 3.0
 * @return void
 */
function rcp_remove_membership_reminder_flags( $expiration, $membership_id, $membership ) {

	global $wpdb;

	$user_id = $membership->get_customer()->get_user_id();

	$query = $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE %s", $user_id, '_rcp_reminder_sent_' . absint( $membership->get_id() ) . '_%' );
	$wpdb->query( $query );


}
add_action( 'rcp_membership_post_renew', 'rcp_remove_membership_reminder_flags', 10, 3 );

/**
 * Trigger email verification during registration.
 *
 * @uses rcp_send_email_verification()
 *
 * @param array $posted  Posted form data.
 * @param int   $user_id ID of the user making this registration.
 * @param float $price   Price of the membership level.
 *
 * @return void
 */
function rcp_set_email_verification_flag( $posted, $user_id, $price ) {

	global $rcp_options;

	$require_verification = isset( $rcp_options['email_verification'] ) ? $rcp_options['email_verification'] : 'off';
	$required             = in_array( $require_verification, array( 'free', 'all' ) );

	// Not required if this is a paid registration and email verification is required for free only.
	if ( $price > 0 && 'free' == $require_verification ) {
		$required = false;
	}

	// Not required if they've already had a membership level.
	// This prevents email verification from popping up for old users on upgrades/downgrades/renewals.
	if ( get_user_meta( $user_id, '_rcp_old_subscription_id', true ) ) {
		$required = false;
	}

	// Bail if verification not required.
	if ( ! apply_filters( 'rcp_require_email_verification', $required, $posted, $user_id, $price ) ) {
		return;
	}

	// Email verification already completed.
	if ( get_user_meta( $user_id, 'rcp_email_verified', true ) ) {
		return;
	}

	// Add meta flag to indicate they're pending email verification.
	update_user_meta( $user_id, 'rcp_pending_email_verification', strtolower( md5( uniqid() ) ) );

	// Send email.
	rcp_send_email_verification( $user_id );

}

add_action( 'rcp_form_processing', 'rcp_set_email_verification_flag', 10, 3 );

/**
 * Remove membership data if registration payment fails. Includes:
 *
 *  - Update pending payment status to "Failed"
 *  - If this is a brand new registration, disable the associated membership. @todo Maybe this could be improved.
 *
 * @param RCP_Payment_Gateway $gateway
 *
 * @since  2.8
 * @return void
 */
function rcp_remove_subscription_data_on_failure( $gateway ) {

	// Mark the pending payment as failed.
	if ( ! empty( $gateway->user_id ) && is_object( $gateway->payment ) ) {

		/**
		 * @var RCP_Payments $rcp_payments_db
		 */
		global $rcp_payments_db;

		$rcp_payments_db->update( $gateway->payment->id, array( 'status' => 'failed' ) );

		if ( is_object( $gateway->membership ) && 0 == $gateway->membership->get_times_billed() ) {
			$gateway->membership->disable();
		}

	}

	// Log error.
	rcp_log( sprintf( '%s registration failed for user #%d. Error message: %s', rcp_get_gateway_name_from_object( $gateway ), $gateway->user_id, $gateway->error_message ), true );

}

add_action( 'rcp_registration_failed', 'rcp_remove_subscription_data_on_failure' );

/**
 * Complete a registration when a payment is completed by updating the following:
 *
 *      - Add discount code to member's profile (if applicable).
 *      - Increase discount code usage (if applicable).
 *      - Mark as trialing (if applicable).
 *      - Remove the role granted by the previous membership level and apply new one.
 *
 * @param int $payment_id ID of the payment being completed.
 *
 * @since 2.9
 * @return void
 */
function rcp_complete_registration( $payment_id ) {

	/**
	 * @var RCP_Payments $rcp_payments_db
	 */
	global $rcp_payments_db;

	$payment = $rcp_payments_db->get_payment( $payment_id );

	if ( ! empty( $payment->customer_id ) ) {
		$customer = rcp_get_customer( $payment->customer_id );
	} else {
		$customer = false;
	}

	// No customer - bail.
	if ( empty( $customer ) ) {
		return;
	}

	$pending_payment_id = $customer->get_pending_payment_id();

	// This doesn't correspond to the most recent registration - bail.
	if ( empty( $pending_payment_id ) || $pending_payment_id != $payment_id ) {
		return;
	}

	rcp_log( sprintf( 'Completing registration for customer #%d via payment #%d.', $customer->get_id(), $pending_payment_id ) );

	$membership_id = $payment->membership_id;
	$membership    = rcp_get_membership( $membership_id );

	if ( 0 == $membership->get_times_billed() ) {
		// If this is a brand new membership, activate it.
		$membership->activate();
	} else {
		// Otherwise this is a manual renewal, so let's renew it.
		$expiration = '';

		if ( 'pending' == $membership->get_status() ) {
			// If membership is pending, use existing expiration date. This would have been set during registration.
			$expiration = $membership->get_expiration_date( false );
		}

		$membership->renew( $membership->is_recurring(), 'active', $expiration );
	}

	// Increase the number of uses for this discount code.
	if ( ! empty( $payment->discount_code ) ) {
		$discounts    = new RCP_Discounts();
		$discount_obj = $discounts->get_by( 'code', $payment->discount_code );

		rcp_log( sprintf( 'Recording usage of discount code #%d.', $discount_obj->id ) );

		// Record the usage of this discount code
		$discounts->add_to_user( $customer->get_user_id(), $payment->discount_code );

		// Increase the usage count for the code
		$discounts->increase_uses( $discount_obj->id );
	}

	// Delete the pending payment record.
	delete_user_meta( $customer->get_user_id(), 'rcp_pending_payment_id' );

	$member = new RCP_Member( $customer->get_user_id() ); // for backwards compatibility in the below action

	/**
	 * Registration successful! Hook into this action if you need to execute code
	 * after a successful registration, but not during an automatic renewal.
	 *
	 * @var RCP_Member     $member
	 * @var RCP_Customer   $customer
	 * @var RCP_Membership $membership
	 * @since 2.9
	 */
	do_action( 'rcp_successful_registration', $member, $customer, $membership );

}

add_action( 'rcp_update_payment_status_complete', 'rcp_complete_registration' );

/**
 * Register a user account as an RCP member, assign a membership level,
 * calculate the expiration date, etc.
 *
 * @deprecated 3.0 Use `RCP_Customer::add_membership()` instead.
 * @see        RCP_Customer::add_membership()
 *
 * @param int     $user_id             ID of the user to add the membership to.
 * @param array   $args                {
 *                                     Array of membership arguments. Only `subscription_id` is required.
 *
 * @type string   $status              Optional.    Status to set: free, active, cancelled, or expired. If omitted, set
 *       to free or active.
 * @type int      $subscription_id     Required. ID number of the membership level to give the user.
 * @type string   $expiration          Optional. Expiration date to give the user in MySQL format. If omitted,
 *       calculated automatically.
 * @type string   $discount_code       Optional. Name of a discount code to add to the user's profile and increment
 *       usage count.
 * @type string   $subscription_key    Optional. Subscription key to add to the user's profile.
 * @type int|bool $trial_duration      Optional. Only supply this to give the user a free trial.
 * @type string   $trial_duration_unit Optional. `day`, `month`, or `year`.
 * @type bool     $recurring           Optional. Whether or not the membership is automatically recurring. Default is
 *       `false`.
 * @type string   $payment_profile_id  Optional. Payment profile ID to add to the user's profile.
 * }
 *
 * @since      2.9
 * @return bool
 */
function rcp_add_user_to_subscription( $user_id, $args = array() ) {

	$defaults = array(
		'status'              => '',
		'subscription_id'     => 0,
		'expiration'          => '',    // Calculated automatically if not provided.
		'discount_code'       => '',    // To add to their profile and increase usage.
		'subscription_key'    => '',
		'trial_duration'      => false, // To set as trialing.
		'trial_duration_unit' => 'day',
		'recurring'           => false,
		'payment_profile_id'  => ''
	);

	$args = wp_parse_args( $args, $defaults );

	// Membership level ID is required.
	if ( empty( $args['subscription_id'] ) ) {
		return false;
	}

	// Check to see if a customer exists, and if not, create one.
	$customer = rcp_get_customer_by_user_id( $user_id );

	if ( empty( $customer ) ) {
		$customer_id = rcp_add_customer( array(
			'user_id' => $user_id
		) );

		if ( empty( $customer_id ) ) {
			return false;
		}

		$customer = rcp_get_customer( $customer_id );
	}

	$member              = new RCP_Member( $user_id );
	$rcp_levels_db       = new RCP_Levels();
	$previous_membership = rcp_get_customer_single_membership( $customer->get_id() );
	$old_membership_id   = ! empty( $previous_membership ) ? $previous_membership->get_object_id() : false;
	$membership_level    = $rcp_levels_db->get_level( $args['subscription_id'] );

	// Invalid membership level - bail.
	if ( empty( $membership_level ) ) {
		return false;
	}

	if ( ! empty( $previous_membership ) && $previous_membership->get_object_id() == $args['subscription_id'] ) {

		/**
		 * If the previous membership is the same as this new one, renew it instead of adding a new one.
		 */

		$expiration = ! empty( $args['expiration'] ) ? $args['expiration'] : '';
		$previous_membership->renew( false, 'active', $expiration );

		if ( ! empty( $args['subscription_key'] ) ) {
			$previous_membership->set_subscription_key( $args['subscription_key'] );
		}

		if ( ! empty( $args['payment_profile_id'] ) ) {
			$previous_membership->set_gateway_customer_id( $args['payment_profile_id'] );
		}

	} else {

		/**
		 * Gather all the membership data for a brand new membership.
		 */

		$membership_data = array(
			'object_id' => $membership_level->id
		);

		if ( ! empty( $args['subscription_key'] ) ) {
			$membership_data['subscription_key'] = $args['subscription_key'];
		}

		/*
		 * Initial and recurring amounts.
		 */
		$membership_data['initial_amount']   = $membership_level->price + $membership_level->fee;
		$membership_data['recurring_amount'] = $membership_level->price;

		/*
		 * Expiration date
		 * Calculate it if not provided.
		 */
		$expiration = $args['expiration'];
		if ( empty( $expiration ) ) {
			$expiration = rcp_calculate_subscription_expiration( $membership_level->id, ! $member->has_trialed() );
		}
		$membership_data['expiration_date'] = $expiration;

		/*
		 * Discount code
		 * Increase the number of uses.
		 */
		if ( ! empty( $args['discount_code'] ) ) {
			$discounts    = new RCP_Discounts();
			$discount_obj = $discounts->get_by( 'code', $args['discount_code'] );

			// Record the usage of this discount code
			$discounts->add_to_user( $user_id, $args['discount_code'] );

			// Increase the usage count for the code
			$discounts->increase_uses( $discount_obj->id );
		}

		/*
		 * Set the status
		 */
		$status = $args['status'];
		if ( empty( $status ) || 'free' == $status ) {
			$status = 'active';
		}
		$membership_data['status'] = $status;

		// Recurring
		$membership_data['auto_renew'] = ! empty( $args['recurring'] ) ? 1 : 0;

		// Payment profile ID
		if ( ! empty( $args['payment_profile_id'] ) ) {
			$membership_data['gateway_subscription_id'] = $args['payment_profile_id'];
		}

		/**
		 * Now actually insert or update the membership.
		 */
		$membership_id = $customer->add_membership( $membership_data );

		if ( empty( $membership_id ) ) {
			return false;
		}

	}

	/**
	 * Registration successful! Hook into this action if you need to execute code
	 * after a successful registration, but not during an automatic renewal.
	 *
	 * @var RCP_Member $member
	 * @since 2.9
	 */
	do_action( 'rcp_successful_registration', $member );

	return true;

}

/**
 * Automatically add new users to a membership level if enabled
 *
 * @param int $user_id ID of the newly created user.
 *
 * @since 2.9
 * @return void
 */
function rcp_user_register_add_subscription_level( $user_id ) {

	global $rcp_options;

	if ( empty( $rcp_options['auto_add_users'] ) ) {
		return;
	}

	$level_id = absint( $rcp_options['auto_add_users_level'] );

	if ( empty( $level_id ) ) {
		return;
	}

	// Don't run if we're on the registration form.
	if ( did_action( 'rcp_form_errors' ) ) {
		return;
	}

	rcp_log( sprintf( 'Auto adding membership level to user #%d.', $user_id ) );

	$customer = rcp_get_customer_by_user_id( $user_id );

	if ( empty( $customer ) ) {
		$customer_id = rcp_add_customer( array( 'user_id' => $user_id ) );

		if ( empty( $customer_id ) ) {
			rcp_log( sprintf( 'Error creating customer record for user #%d.', $user_id ) );

			return;
		}

		$customer = rcp_get_customer( $customer_id );
	}

	if ( empty( $customer ) ) {
		rcp_log( sprintf( 'Error retrieving customer record for user #%d.', $user_id ) );

		return;
	}

	$customer->add_membership( array(
		'object_id'     => $level_id,
		'status'        => 'active', // Note: this bypasses RCP_Membership::activate(). Not sure if desired.
		'signup_method' => 'manual'
	) );

}

add_action( 'user_register', 'rcp_user_register_add_subscription_level' );
