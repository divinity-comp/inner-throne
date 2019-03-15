<?php
/**
 * Filters
 *
 * @package     RCP\Custom_Redirects\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Override the RCP login form processing
 *
 * @since       1.0.0
 * @return      void
 */
function rcp_custom_redirects_process_login_form() {
	if( ! isset( $_POST['rcp_action'] ) || 'login' != $_POST['rcp_action'] ) {
		return;
	}

	if( ! isset( $_POST['rcp_login_nonce'] ) || ! wp_verify_nonce( $_POST['rcp_login_nonce'], 'rcp-login-nonce' ) ) {
		return;
	}

	if( is_email( $_POST['rcp_user_login'] ) && ! username_exists( $_POST['rcp_user_login'] ) ) {
		$user = get_user_by( 'email', $_POST['rcp_user_login'] );
	} else {
		// This returns the user ID and other info from the user name
		$user = get_user_by( 'login', $_POST['rcp_user_login'] );
	}

	do_action( 'rcp_before_form_errors', $_POST );

	if( ! $user ) {
		// If the user name doesn't exist
		rcp_errors()->add( 'empty_username', __( 'Invalid username or email', 'rcp' ), 'login' );
	}

	if( ! isset( $_POST['rcp_user_pass'] ) || $_POST['rcp_user_pass'] == '') {
		// If no password was entered
		rcp_errors()->add( 'empty_password', __( 'Please enter a password', 'rcp' ), 'login' );
	}

	if( $user ) {
		// Check the user's login with their password
		if( ! wp_check_password( $_POST['rcp_user_pass'], $user->user_pass, $user->ID ) ) {
			// If the password is incorrect for the specified user
			rcp_errors()->add( 'empty_password', __( 'Incorrect password', 'rcp' ), 'login' );
		}
	}

	if( function_exists( 'is_limit_login_ok' ) && ! is_limit_login_ok() ) {
		rcp_errors()->add( 'limit_login_failed', limit_login_error_msg(), 'login' );
	}

	do_action( 'rcp_login_form_errors', $_POST );

	// Retrieve all error messages
	$errors = rcp_errors()->get_error_messages();

	// Only log the user in if there are no errors
	if( empty( $errors ) ) {
		$remember = isset( $_POST['rcp_user_remember'] );

		$redirect = ! empty( $_POST['rcp_redirect'] ) ? $_POST['rcp_redirect'] : home_url();

		$level_id      = rcp_get_subscription_id( $user->ID );
		$redirect_urls = get_option( 'rcp_custom_redirects_login_urls' );

		if( is_array( $redirect_urls ) && array_key_exists( $level_id, $redirect_urls ) ) {
			if( $redirect_urls[$level_id] !== '' ) {
				$redirect = $redirect_urls[$level_id];
			}
		}

		rcp_login_user_in( $user->ID, $_POST['rcp_user_login'], $remember );

		// Redirect the user back to the appropriate page
		wp_redirect( $redirect );
		exit;
	} else {
		if( function_exists( 'limit_login_failed' ) ) {
			limit_login_failed( $_POST['rcp_user_login'] );
		}
	}
}

// Only use our login override if not on version 2.8.2+.
if ( ! defined( 'RCP_PLUGIN_VERSION' ) || version_compare( RCP_PLUGIN_VERSION, '2.8.2', '<' ) ) {
	remove_action( 'init', 'rcp_process_login_form' );
	add_action( 'init', 'rcp_custom_redirects_process_login_form' );
}