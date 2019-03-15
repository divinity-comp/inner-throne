<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Zapier_Subscription_Factory {

	/**
	 * Get all subscription URLs for a given event.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $event Event trigger.
	 * @return array         Subscription objects.
	 */
	public static function get_subscriptions( $event = '' ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare(
			"
			SELECT ID, post_title as url, post_content as event
			FROM   $wpdb->posts
			WHERE  post_type = 'edd-zapier-sub'
			       AND post_content = %s
			",
			$event
		) );
	} /* get_subscriptions() */

	/**
	 * Find a Zapier subscription by URL.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $url Subscription URL.
	 * @return bool        True if subscription exists, otherwise false.
	 */
	public static function subscription_exists( $url = '' ) {
		global $wpdb;

		$subscription_id = $wpdb->get_var( $wpdb->prepare(
			"
			SELECT ID
			FROM   $wpdb->posts
			WHERE  post_type = 'edd-zapier-sub'
			       AND post_title = %s
			",
			esc_url( $url )
		) );

		return ! empty( $subscription_id );
	} /* subscription_exists() */

	/**
	 * Add new Zapier subscription if it does not already exist.
	 *
	 * @since  1.0.0
	 *
	 * @return integer Subscription ID on success, otherwise 0.
	 */
	public static function maybe_add_subscription() {
		$request = self::get_request_or_abort();
		if ( self::subscription_exists( $request->target_url ) ) {
			EDD()->api->output( 409 );
		}
		return self::add_subscription( $request->target_url, $request->event );
	} /* maybe_add_subscription() */

	/**
	 * Add new Zapier subscription.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $url   Subscription URL.
	 * @param  string $event Event trigger.
	 * @return mixed         Subscription object on success, otherwise false.
	 */
	public static function add_subscription( $url = '', $event = '' ) {
		$subscription_id = 0;

		if ( ! empty( $url ) && ! empty( $event ) ) {
			$subscription_id = wp_insert_post( array(
				'post_type'    => 'edd-zapier-sub',
				'post_status'  => 'publish',
				'post_content' => esc_attr( $event ),
				'post_title'   => esc_url( $url ),
			) );
		}

		return $subscription_id;
	} /* add_subscription() */

	/**
	 * Delete a Zapier subscription if request is valid.
	 *
	 * @since  1.0.0
	 *
	 * @return bool True on success, otherwise false.
	 */
	public static function maybe_delete_subscription() {
		$request = self::get_request_or_abort();
		self::delete_subscription( $request->id );
	} /* maybe_delete_subscription() */

	/**
	 * Delete a Zapier subscription.
	 *
	 * @since  1.0.0
	 *
	 * @param  integer $subscription_id Subscription ID.
	 * @return bool                     True if deleted, otherwise false.
	 */
	public static function delete_subscription( $subscription_id = 0 ) {
		$deleted = wp_delete_post( absint( $subscription_id ), true );
		return ( ! empty( $deleted ) );
	} /* delete_subscription() */

	/**
	 * Get Zapier request object.
	 *
	 * @since  1.0.0
	 *
	 * @return object Request from Zapier if valid, otherwise false.
	 */
	public static function get_request_or_abort() {
		$request = json_decode( file_get_contents('php://input') );
		if ( is_object( $request ) && ! empty( $request ) ) {
			return $request;
		} else {
			EDD()->api->output( 417 );
		}
	} /* get_request_or_abort() */

}
/* EDD_Zapier_Subscription_Factory */
