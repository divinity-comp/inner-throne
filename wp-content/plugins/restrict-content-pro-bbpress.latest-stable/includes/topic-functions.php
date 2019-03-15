<?php

// checks if a topic is premium
function rcp_topic_is_premium( $topic_id = null ) {

	if ( is_null( $topic_id ) ) {

		$topic_id = bbp_get_topic_id();

	}

	$ret = (bool) get_post_meta( $topic_id, '_is_topic_paid', true );

	if( ! $ret ) {

		$ret = rcp_forum_is_premium( bbp_get_forum_id() );

	}

	return $ret;
}

function rcp_bbp_can_access_topic( $topic_id = 0 ) {

	if( ! function_exists( 'rcp_is_active' ) ) {
		return true;
	}

	$ret           = true;
	$user_id       = get_current_user_id();
	$paid_only     = rcp_topic_is_premium( $topic_id );
	$access_level  = get_post_meta( $topic_id, 'rcp_access_level', true );
	$subscriptions = get_post_meta( $topic_id, 'rcp_subscription_level', true );

	if( $paid_only && ! rcp_is_active( $user_id ) ) {

		$ret = false; // User does not have a paid subscription

	}

	if ( $access_level > 0 && ! rcp_user_has_access( $user_id, $access_level ) ) {

		$ret = false; // User does not have the necessary access level

	}

	if( ! empty( $subscriptions ) && ! in_array( rcp_get_subscription_id( $user_id ), $subscriptions ) ) {

		$ret = false; // User does not have the appropriate subscription level

	}

	if( ! rcp_bbp_can_access_forum( bbp_get_forum_id() ) ) {

		$ret = false; // If the user can't view the topic, they can't view the forum
	}

	if( current_user_can( 'moderate' ) ) {

		$ret = true; // Moderators can always access forums

	}

	return apply_filters( 'rcp_bbp_can_access_topic', $ret, $topic_id, $user_id );
}

// hides all topics in a restricted forum for non active users
function rcp_filter_topics_list( $query ) {

	global $user_ID;

	if ( bbp_is_single_forum() && ! rcp_bbp_can_access_forum( bbp_get_forum_id() ) ) {

		$query = array(); // return an empty query

	}

	return $query;
}
add_filter( 'bbp_has_topics_query', 'rcp_filter_topics_list' );

// retrieves array of all premium topics for the specified forum ID (if given, all otherwise)
function rcp_get_premium_topics( $forum_id = null ) {

	if ( is_null( $forum_id ) ) {
		$forum_id = bbp_get_forum_id();
	}

	$paid_ids = array();

	if ( $forum_id ) {

		$paid_posts = get_posts( 'meta_key=_is_topic_paid&meta_value=1&post_status=publish&post_type=topic' );

	} else {

		$paid_posts = get_posts( 'meta_key=_is_topic_paid&meta_value=1&post_status=publish&post_type=topic&post_parent=' . $forum_id );

	}

	if ( $paid_posts ) {

		foreach ( $paid_posts as $p ) {
			$paid_ids[] = $p->ID;

		}

	}

	if ( sizeof( $paid_ids ) >= 1 ) {
		return $paid_ids;
	}

	return false;
}

// hides the new reply form
function rcp_hide_new_topic_form( $can_access ) {
	global $user_ID;

	if ( ! rcp_bbp_can_access_forum( bbp_get_forum_id() ) ) {
		return false;
	}
	return $can_access;
}
add_filter( 'bbp_current_user_can_access_create_topic_form', 'rcp_hide_new_topic_form' );

// Disable single topic views
function rcp_hide_single_topic() {

	$topic_id  = get_the_ID();
	$post_type = get_post_type( $topic_id );

	if( ! function_exists( 'bbp_get_topic_post_type' ) ) {

		return;

	}

	if( bbp_get_topic_post_type() !== $post_type ) {
		
		return;
	
	}

	if ( rcp_bbp_can_access_forum( bbp_get_forum_id() ) ) {

		return;

	}

	if( is_user_logged_in() ) {
		
		$redirect = home_url();
	
	} else {
	
		$redirect = bbp_get_topic_permalink( bbp_get_topic_id( $topic_id ) );
	
	}
	wp_redirect( home_url( 'wp-login.php?redirect_to=' . urlencode( $redirect ) ) ); exit;
}
add_action( 'template_redirect', 'rcp_hide_single_topic' );