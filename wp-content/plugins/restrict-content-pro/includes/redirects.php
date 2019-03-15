<?php
/**
 * Redirects
 *
 * @package     Restrict Content Pro
 * @subpackage  Redirects
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Redirect non-subscribed users away from a restricted post
 * If the redirect page is premium, users are sent to the home page
 *
 * @return void
 */
function rcp_redirect_from_premium_post() {
	global $rcp_options, $user_ID, $post, $wp_query;
	if( empty( $rcp_options['hide_premium'] ) ) {
		return;
	}

	$member   = new RCP_Member( $user_ID ); // for backwards compatibility
	$customer = rcp_get_customer(); // currently logged in customer

	if( is_singular() && ! rcp_user_can_access( get_current_user_id(), $post->ID ) ) {
		$redirect_page_id = $rcp_options['redirect_from_premium'];
		if( ! empty( $redirect_page_id ) ) {
			// Bail without redirecting if we're already on this page.
			if ( $redirect_page_id == $post->ID ) {
				return;
			}

			$redirect = get_permalink( $redirect_page_id );
		} else {
			$redirect = home_url();
		}

		$redirect = apply_filters( 'rcp_restricted_post_redirect_url', $redirect, $member, $post, $customer );

		wp_redirect( esc_url_raw( $redirect ) ); exit;
	} elseif( is_post_type_archive() && $wp_query->have_posts() && rcp_is_restricted_post_type( get_post_type() ) && ! rcp_user_can_access( get_current_user_id(), get_the_ID() ) ) {
		if( isset( $rcp_options['redirect_from_premium'] ) ) {
			$redirect = get_permalink( $rcp_options['redirect_from_premium'] );
		} else {
			// Avoid a crazy redirect loop.
			$redirect = ! is_front_page() ? home_url() : false;
		}

		$redirect = apply_filters( 'rcp_restricted_post_redirect_url', $redirect, $member, $post, $customer );

		if ( $redirect ) {
			wp_redirect( esc_url_raw( $redirect ) ); exit;
		}
	}
}
add_action( 'template_redirect', 'rcp_redirect_from_premium_post', 999 );

/**
 * Hijack the default WP login URL and redirect users to custom login page
 *
 * @param string $login_url
 *
 * @return string
 */
function rcp_hijack_login_url( $login_url ) {
	global $rcp_options;
	if( isset( $rcp_options['hijack_login_url'] ) && isset( $rcp_options['login_redirect'] ) ) {
		$login_url = get_permalink( $rcp_options['login_redirect'] );
	}
	return $login_url;
}
add_filter( 'login_url', 'rcp_hijack_login_url' );


/**
 * Redirects users to the custom login page when access wp-login.php
 *
 * @return void
 */
function rcp_redirect_from_wp_login() {
	global $rcp_options;

	if( isset( $rcp_options['hijack_login_url'] ) && isset( $rcp_options['login_redirect'] ) ) {

		if ( ! empty( $_GET['redirect_to'] ) ) {
			$login_url = add_query_arg( 'redirect', urlencode( $_GET['redirect_to'] ), get_permalink( $rcp_options['login_redirect'] ) );
		} else {
			$login_url = get_permalink( $rcp_options['login_redirect'] );
		}

		wp_redirect( esc_url_raw( $login_url ) ); exit;
	}
}
add_action( 'login_form_login', 'rcp_redirect_from_wp_login' );