<?php
/**
 * Helper functions
 *
 * @package     RCP\Custom_Redirects\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Retrieve the URL for a given level
 *
 * @since       1.0.0
 * @param       string $url_type Whether to retrieve a subscription or login URL
 * @param       int $level_id The ID of the level to retrieve the URL for
 * @return      string $url The URL for the given level
 */
function rcp_custom_redirects_get_url( $url_type = 'subscription', $level_id = false ) {
	$url = '';

	if( $level_id ) {
		switch( $url_type ) {
			case 'subscription' :
				$redirect_urls = get_option( 'rcp_custom_redirects_subscription_urls' );
				break;
			case 'login' :
				$redirect_urls = get_option( 'rcp_custom_redirects_login_urls' );
				break;
			default :
				$redirect_urls = apply_filters( 'rcp_custom_redirects_urls', array(), $url_type, $level_id );
				break;
		}

		if( is_array( $redirect_urls ) && array_key_exists( $level_id, $redirect_urls ) ) {
			$url = $redirect_urls[$level_id];
		}
	}

	return $url;
}