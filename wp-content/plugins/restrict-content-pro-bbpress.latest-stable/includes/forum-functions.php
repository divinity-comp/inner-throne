<?php

function rcp_forum_is_premium( $forum_id = null ) {

	if( ! function_exists( 'bbp_get_forum_id' ) ) {

		return false;

	}

	if ( is_null( $forum_id ) ) {
		$forum_id = bbp_get_forum_id();
	}
	return get_post_meta( $forum_id, '_is_forum_paid', true ) ? true : false;
}

function rcp_bbp_can_access_forum( $forum_id = 0 ) {

	if( ! function_exists( 'rcp_is_active' ) ) {
		return true;
	}

	$ret           = true;
	$user_id       = get_current_user_id();
	$paid_only     = rcp_forum_is_premium( $forum_id );
	$access_level  = get_post_meta( $forum_id, 'rcp_access_level', true );
	$subscriptions = get_post_meta( $forum_id, 'rcp_subscription_level', true );

	if( $paid_only && ! rcp_is_active( $user_id ) ) {

		$ret = false; // User does not have a paid subscription

	}

	if ( $access_level > 0 && ! rcp_user_has_access( $user_id, $access_level ) ) {

		$ret = false; // User does not have the necessary access level

	}

	if( ! empty( $subscriptions ) && ! in_array( rcp_get_subscription_id( $user_id ), $subscriptions ) ) {

		$ret = false; // User does not have the appropriate subscription level

	}

	if( current_user_can( 'moderate' ) ) {

		$ret = true; // Moderators can always access forums

	}

	return apply_filters( 'rcp_bbp_can_access_forum', $ret, $forum_id, $user_id );
}

function rcp_bbp_remove_core_user_checks() {
	if( function_exists( 'is_bbpress' ) && is_bbpress() ) {
		remove_action( 'loop_start', 'rcp_user_level_checks', 10 );
	}
}
add_action( 'wp_head', 'rcp_bbp_remove_core_user_checks' );