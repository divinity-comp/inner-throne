<?php
/**
 * Filters
 *
 * @package     RCP\Custom_Redirects\Filters
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Filter registration return URLs
 *
 * @since       1.0.0
 * @param       string $redirect The current redirect URL
 * @param       int $user_id The ID for the logged in user
 * @return      string $redirect The redirect URL
 */
function rcp_custom_redirects_get_return_url( $redirect, $user_id ) {
	$level_id      = rcp_get_registration()->get_subscription();
	$redirect_urls = get_option( 'rcp_custom_redirects_subscription_urls' );

	// Level ID won't be available via the registration class in PayPal Express due to the confirmation page.
	if ( empty( $level_id ) ) {
		$member   = new RCP_Member( $user_id );
		$level_id = $member->get_pending_subscription_id();

		if ( empty( $level_id ) ) {
			$level_id = $member->get_subscription_id();
		}
	}

	if ( empty( $level_id ) ) {
		return $redirect;
	}

	if( is_array( $redirect_urls ) && array_key_exists( $level_id, $redirect_urls ) ) {
		if( $redirect_urls[$level_id] !== '' ) {
			$redirect = $redirect_urls[$level_id];
		}
	}

	return $redirect;
}
add_filter( 'rcp_return_url', 'rcp_custom_redirects_get_return_url', 10, 2 );

/**
 * Filter login redirect URL
 *
 * @param string  $redirect The current redirect URL.
 * @param WP_User $user     Object for the user logging in.
 *
 * @since 1.0.1
 * @return string $redirect The new redirect URL.
 */
function rcp_custom_redirects_get_login_redirect_url( $redirect, $user ) {

	$level_id      = rcp_get_subscription_id( $user->ID );
	$redirect_urls = get_option( 'rcp_custom_redirects_login_urls' );

	if( is_array( $redirect_urls ) && array_key_exists( $level_id, $redirect_urls ) ) {
		if( ! empty( $redirect_urls[ $level_id ] ) ) {
			$redirect = $redirect_urls[ $level_id ];
		}
	}

	return $redirect;

}
add_filter( 'rcp_login_redirect_url', 'rcp_custom_redirects_get_login_redirect_url', 10, 2 );