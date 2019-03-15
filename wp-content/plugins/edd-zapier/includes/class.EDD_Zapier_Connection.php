<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Zapier_Connection {

	/**
	 * Push event data to Zapier subscriptions and remove invalid subscriptions.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event Event trigger.
	 * @param array  $data  Event data.
	 */
	public static function push_and_scrub( $event = '', $data = array() ) {
		$subscriptions = self::event_push( $event, $data );
		self::delete_invalid_subscriptions( $subscriptions );
	} /* push_and_scrub() */

	/**
	 * Push new event data to Zapier subscriptions.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $event Event trigger.
	 * @param  array  $data  Event data.
	 * @return array         Remote response codes.
	 */
	public static function event_push( $event = '', $data = array() ) {
		$data = apply_filters( 'edd_zapier_' . $event . '_data', $data );
		$subscriptions = EDD_Zapier_Subscription_Factory::get_subscriptions( $event );
		foreach ( $subscriptions as $key => $subscription ) {
			$response_code = self::api_post( $subscription->url, $data );
			$subscriptions[ $key ]->is_valid = ( 410 !== $response_code ) ? true : false;
		}
		return $subscriptions;
	} /* event_push() */

	/**
	 * Send data to a Zapier subscription URL.
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $url  Zapier URL.
	 * @param  array   $data Event data.
	 * @return integer       Remote HTTP response code.
	 */
	public static function api_post( $url = '', $data = array() ) {
		$response = wp_remote_post(
			esc_url( $url ),
			array(
				'headers' => array( 'content-type' => 'application/json' ),
				'body'    => json_encode( $data ),
			)
		);

		return absint( wp_remote_retrieve_response_code( $response ) );
	} /* api_post() */

	/**
	 * Delete all invalid subscriptions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $subscriptions Subscription objects.
	 */
	public static function delete_invalid_subscriptions( $subscriptions ) {
		foreach ( $subscriptions as $subscription ) {
			if ( ! $subscription->is_valid ) {
				EDD_Zapier_Subscription_Factory::delete_subscription( $subscription->ID );
			}
		}
	} /* delete_invalid_subscriptions() */

	/**
	 * Push sample data to all Zapier subscriptions.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $trigger Trigger name.
	 */
	public static function sample_data_push( $trigger = '' ) {
		$trigger_data = self::get_trigger_data( $trigger );
		self::push_and_scrub( $trigger, $trigger_data );
	}

	/**
	 * Get sample data for a given trigger.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $trigger Trigger name.
	 * @return array           Trigger data.
	 */
	public static function get_trigger_data( $trigger = '' ) {

		switch( $trigger ) {

			case 'edd_new_customer' :
			case 'edd_update_customer' :

				$trigger_data = array(
					'user_id'        => 1234,
					'name'           => 'John Doe',
					'first_name'     => 'John',
					'last_name'      => 'Doe',
					'email'          => 'johndoe123@test.com',
					'payment_ids'    => 2345,
					'purchase_value' => '23.5',
					'date_created'   => date( 'Y-m-d h:i:s' ),
					'purchase_count' => 1,
					'notes'          => null,
				);

				break;


			case 'edd_new_license' :
			case 'edd_active_license' :
			case 'edd_inactive_license' :
			case 'edd_expired_license' :
			case 'edd_disabled_license' :
			case 'edd_license_activated' :
			case 'edd_license_deactivated' :

				$trigger_data = array(
					'ID'               => 1234,
					'key'              => '736b31fec1ecb01c28b51a577bb9c2b3',
					'customer_name'    => 'Jane Doe',
					'customer_email'   => 'jane@test.com',
					'product_id'       => 4321,
					'product_name'     => 'Sample Product',
					'activation_limit' => 1,
					'activation_count' => 1,
					'activated_urls'   => 'sample.com',
					'expiration'       => date( 'Y-n-d H:i:s', current_time( 'timestamp' ) ),
					'is_lifetime'      => 0,
					'status'           => 'active',
				);

				break;

			case 'edd_file_downloaded' :

				$trigger_data = array(
					'file_name' => 'Sample File Name',
					'file'      => home_url( 'sample/file/url/file.zip' ),
					'email'     => 'jane@test.com',
					'product'   => 'Sample Product',
				);

				break;

			case 'edd_subscription_created' :
			case 'edd_subscription_renewed' :
			case 'edd_subscription_completed' :
			case 'edd_subscription_expired' :
			case 'edd_subscription_failing' :
			case 'edd_subscription_cancelled' :

				$trigger_data = array(
					'id'                => '183',
					'customer_id'       => '36',
					'period'            => 'month',
					'initial_amount'    => '16.47',
					'recurring_amount'  => '10.98',
					'bill_times'        => '0',
					'transaction_id'    => '',
					'parent_payment_id' => '845',
					'product_id'        => '8',
					'created'           => '2016-06-13 13:47:24',
					'expiration'        => '2016-07-13 23:59:59',
					'status'            => 'pending',
					'profile_id'        => 'ppe-4e3ca7d1c017e0ea8b24ff72d1d23022-8',
					'gateway'           => 'paypalexpress',
					'customer'          => array(
						'id'             => '36',
						'purchase_count' => '2',
						'purchase_value' => '32.93',
						'email'          => 'jane@test.com',
						'emails'         => array(
							'jane@test.com',
						),
						'name'           => 'Jane Doe',
						'date_created'   => '2016-06-13 13:19:50',
						'payment_ids'    => '842,845,846',
						'user_id'        => '1',
						'notes'          => array(
					  		'These are notes about the customer',
						),
					),
					'user_id' => '24',
				);

				break;

			case 'edd_delete_order' :
			case 'edd_publish_order' :
			case 'edd_cancelled_order' :
			case 'edd_refunded_order' :
			case 'edd_revoked_order' :
			case 'edd_pending_order' :
			case 'edd_failed_order' :
			case 'edd_abandoned_order' :
			case 'edd_delete_order' :
			case 'edd_edd_subscription_order' :
			case 'edd_subscription_payment' :
			default :

				$trigger_data = array(
					'ID'               => 2345,
					'products'         => array(
						array(
							'Product'  => 'Sample Product Name',
							'PriceName'=> 'Standard',
							'Price'    => '20'
						),
					),
					'date'             => date( 'Y-m-d h:i:s' ),
					'key'              => 'ca2aaaa2a9e9e5369b8280403431b6fd',
					'gateway'          => 'manual',
					'subtotal'         => '20',
					'tax'              => '1.2',
					'fees'             => '2.3',
					'total'            => '23.5',
					'discount_codes'   => 'none',
					'transaction_id'   => 'test12345',
					'email'            => 'johndoe123@test.com',
					'first_name'       => 'John',
					'last_name'        => 'Doe',
					'billing_address'  => array( 'line1' => 'Street 1', 'line2' => 'Line 2', 'city' => 'My Fair City', 'country' => 'US', 'state' => 'MD', 'zip' => '55555' ),
					'shipping_address' => array( 'address' => 'Street 1', 'Address2' => 'Line 2', 'city' => 'My Fair City', 'country' => 'US', 'state' => 'MD', 'zip' => '55555' ),
					'metadata'         => array( 'field_id' => 'Field value', 'field_id_2' => 'Second field value' )
				);

				break;
		}

		return $trigger_data;
	}

} /* EDD_Zapier_Connection */
