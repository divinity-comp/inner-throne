<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Subscribe and Unsubscribe API endpoints for Zapier.
 *
 * @since  1.0.0
 *
 * @param  array $query_modes Valid API endpoints.
 * @return array              Updated API endpoints.
 */
function edd_zapier_query_modes( $query_modes = array() ) {
	$new_modes = array(
		'zapier-test',
		'zapier-subscribe',
		'zapier-unsubscribe',
	);
	return array_merge( $query_modes, $new_modes );
}
add_filter( 'edd_api_valid_query_modes', 'edd_zapier_query_modes' );

/**
 * Modify API output based on API endpoint.
 *
 * @since  1.0.0
 *
 * @param  mixed  $data       Output data.
 * @param  string $query_mode Current API endpoint.
 * @return array              Response data.
 */
function edd_zapier_output_data( $data = '', $query_mode = '', $api ) {
	switch ( $query_mode ) {
		case 'zapier-test' :
			break;
		case 'zapier-subscribe' :
			$data = array( 'id' => EDD_Zapier_Subscription_Factory::maybe_add_subscription() );
			break;
		case 'zapier-unsubscribe' :
			$data = array( 'unsubscribed' => EDD_Zapier_Subscription_Factory::delete_subscription() );
			break;
		default :
			break;
	}
	return $data;
}
add_filter( 'edd_api_output_data', 'edd_zapier_output_data', 10, 3 );

/**
 * Ping all Zapier subscriptions when payment status changes.
 *
 * @since 1.0.0
 *
 * @param integer $payment_id Payment post ID.
 * @param string  $new_status New payment status.
 * @param string  $old_status Previous payment status.
 */
function edd_zapier_update_payment_status( $payment_id, $new_status, $old_status ) {
	$whitelist = array_keys( edd_get_payment_statuses() );
	if ( in_array( $new_status, $whitelist ) && $new_status !== $old_status ) {
		$order_data = edd_zapier_get_order_data( $payment_id );

		if ( 'publish' === $new_status ) {
			$new_status = 'new';
		}

		EDD_Zapier_Connection::push_and_scrub( "edd_{$new_status}_order", $order_data );
	}
}
add_action( 'edd_update_payment_status', 'edd_zapier_update_payment_status', 10, 3 );

/**
 * Ping all Zapier subscriptions when a payment is deleted.
 *
 * @since  1.0.0
 *
 * @param  integer $payment_id Payment post ID.
 */
function edd_zapier_payment_delete( $payment_id = 0 ) {
	$order_data = edd_zapier_get_order_data( $payment_id );
	EDD_Zapier_Connection::push_and_scrub( "edd_delete_order", $order_data );
}
add_action( 'edd_payment_delete', 'edd_zapier_payment_delete' );

/**
 * Ping all Zapier subscriptions with new customer data.
 *
 * @since  1.0.0
 *
 * @param  integer $customer_id   Customer ID.
 * @param  array   $args          Customer data.
 */
function edd_zapier_new_customer( $customer_id = 0, $args = array() ) {

	$customer = new EDD_Zapier_Customer( $customer_id );
	$customer->first_name = $customer->get_first_name();
	$customer->last_name  = $customer->get_last_name();

	EDD_Zapier_Connection::push_and_scrub( 'edd_new_customer', $customer );
}
add_action( 'edd_customer_post_create', 'edd_zapier_new_customer', 10, 2 );

/**
 * Ping all Zapier subscriptions with updated customer data.
 *
 * @since  1.2
 *
 * @param  bool    $updated       If the Customer has been updated.
 * @param  integer $customer_id   Customer ID.
 * @param  array   $args          Customer data.
 */
function edd_zapier_update_customer( $updated = false, $customer_id = 0, $args = array() ) {

	if( ! $updated ) {
		return;
	}

	$customer = new EDD_Zapier_Customer( $customer_id );
	$customer->first_name = $customer->get_first_name();
	$customer->last_name  = $customer->get_last_name();

	EDD_Zapier_Connection::push_and_scrub( 'edd_update_customer', $customer );
}
add_action( 'edd_customer_post_update', 'edd_zapier_update_customer', 10, 3 );

/**
 * Ping all Zapier subscriptions when a file is downloaded.
 *
 * @since  1.2
 *
 * @param  bool    $download_id  ID of the product being downloaded.
 * @param  string  $email        Email.
 * @param  integer $payment_id   Payment ID number.
 * @param  array   $args         Additional data array.
 */
function edd_zapier_file_downloaded( $download_id, $email, $payment_id, $args ) {

	$data  = array();
	$files = edd_get_download_files( $download_id );

	$data['file_name'] = $files[ $args['file_key'] ]['name'];
	$data['file']      = $files[ $args['file_key'] ]['file'];
	$data['email']     = $email;
	$data['product']   = get_the_title( $download_id );

	EDD_Zapier_Connection::push_and_scrub( 'edd_file_downloaded', $data );
}
add_action( 'edd_process_verified_download', 'edd_zapier_file_downloaded', 10, 4 );

/**
 * Ping all Zapier subscriptions when Recurring Payments subscription is created
 *
 * @since  1.2
 *
 * @param  integer $license_id   License ID.
 * @param  string  $new_status   Status
 */
function edd_zapier_create_subscription( $subscription_id = 0, $args = array() ) {

	if( ! class_exists( 'EDD_Subscription' ) ) {
		return;
	}

	$subscription = new EDD_Subscription( $subscription_id );

	EDD_Zapier_Connection::push_and_scrub( "edd_subscription_created", $subscription );
}
add_action( 'edd_subscription_post_create', 'edd_zapier_create_subscription', 10, 2 );

/**
 * Ping all Zapier subscriptions when Recurring Payments subscription is renewed
 *
 * @since  1.2
 *
 * @param  integer $sub_id       Subscription ID.
 * @param  object  $subscription EDD_Subscription object
 */
function edd_zapier_subscription_renewed( $sub_id = 0, $expiration = '', EDD_Subscription $subscription ) {

	if( ! class_exists( 'EDD_Subscription' ) ) {
		return;
	}

	EDD_Zapier_Connection::push_and_scrub( "edd_subscription_renewed", $subscription );
}
add_action( 'edd_subscription_post_renew', 'edd_zapier_subscription_renewed', 10, 3 );

/**
 * Ping all Zapier subscriptions when Recurring Payments subscription is completed
 *
 * @since  1.2
 *
 * @param  integer $sub_id       Subscription ID.
 * @param  object  $subscription EDD_Subscription object
 */
function edd_zapier_subscription_completed( $sub_id = 0, EDD_Subscription $subscription ) {

	if( ! class_exists( 'EDD_Subscription' ) ) {
		return;
	}

	EDD_Zapier_Connection::push_and_scrub( "edd_subscription_completed", $subscription );
}
add_action( 'edd_subscription_completed', 'edd_zapier_subscription_completed', 10, 2 );

/**
 * Ping all Zapier subscriptions when Recurring Payments subscription is completed
 *
 * @since  1.2
 *
 * @param  integer $sub_id       Subscription ID.
 * @param  object  $subscription EDD_Subscription object
 */
function edd_zapier_subscription_expired( $sub_id = 0, EDD_Subscription $subscription ) {

	if( ! class_exists( 'EDD_Subscription' ) ) {
		return;
	}

	EDD_Zapier_Connection::push_and_scrub( "edd_subscription_expired", $subscription );
}
add_action( 'edd_subscription_expired', 'edd_zapier_subscription_expired', 10, 2 );

/**
 * Ping all Zapier subscriptions when Recurring Payments subscription is failing
 *
 * @since  1.2
 *
 * @param  integer $sub_id       Subscription ID.
 * @param  object  $subscription EDD_Subscription object
 */
function edd_zapier_subscription_failing( $sub_id = 0, EDD_Subscription $subscription ) {

	if( ! class_exists( 'EDD_Subscription' ) ) {
		return;
	}

	EDD_Zapier_Connection::push_and_scrub( "edd_subscription_failing", $subscription );
}
add_action( 'edd_subscription_failing', 'edd_zapier_subscription_failing', 10, 2 );

/**
 * Ping all Zapier subscriptions when Recurring Payments subscription is cancelled
 *
 * @since  1.2
 *
 * @param  integer $sub_id       Subscription ID.
 * @param  object  $subscription EDD_Subscription object
 */
function edd_zapier_subscription_cancelled( $sub_id = 0, EDD_Subscription $subscription ) {

	if( ! class_exists( 'EDD_Subscription' ) ) {
		return;
	}

	EDD_Zapier_Connection::push_and_scrub( "edd_subscription_cancelled", $subscription );
}
add_action( 'edd_subscription_cancelled', 'edd_zapier_subscription_cancelled', 10, 2 );

/**
 * Ping all Zapier subscriptions when license key status changes
 *
 * @since  1.1
 *
 * @param  integer $license_id   License ID.
 * @param  string  $new_status   Status
 */
function edd_zapier_update_license_status( $license_id = 0, $new_status = '' ) {

	if( ! function_exists( 'edd_software_licensing' ) ) {
		return;
	}

	$license_data = edd_zapier_get_license_data( $license_id );

	EDD_Zapier_Connection::push_and_scrub( "edd_{$new_status}_license", $license_data );
}
add_action( 'edd_sl_post_set_status', 'edd_zapier_update_license_status', 10, 2 );

/**
 * Ping all Zapier subscriptions when license key is activated
 *
 * @since  1.1
 *
 * @param  integer $license_id   License ID.
 * @param  integer $download_id  Download ID.
 */
function edd_zapier_activate_license( $license_id = 0, $download_id = 0 ) {

	if( ! function_exists( 'edd_software_licensing' ) ) {
		return;
	}

	$license_data = edd_zapier_get_license_data( $license_id, $download_id );

	EDD_Zapier_Connection::push_and_scrub( "edd_license_activated", $license_data );
}
add_action( 'edd_sl_activate_license', 'edd_zapier_activate_license', 10, 2 );

/**
 * Ping all Zapier subscriptions when license key is deactivated
 *
 * @since  1.1
 *
 * @param  integer $license_id   License ID.
 * @param  integer $download_id  Download ID.
 */
function edd_zapier_deactivate_license( $license_id = 0, $download_id = 0 ) {

	if( ! function_exists( 'edd_software_licensing' ) ) {
		return;
	}

	$license_data = edd_zapier_get_license_data( $license_id, $download_id );

	EDD_Zapier_Connection::push_and_scrub( "edd_license_deactivated", $license_data );
}
add_action( 'edd_sl_deactivate_license', 'edd_zapier_deactivate_license', 10, 2 );

/**
 * Ping all Zapier subscriptions when license key is created
 *
 * @since  1.1
 *
 * @param  integer $license_id   License ID.
 * @param  integer $download_id  Download ID.
 * @param  integer $payment_id   Payment ID.
 * @param  integer $type         Product type (bundle or default).
 */
function edd_zapier_create_license( $license_id = 0, $download_id = 0, $payment_id = 0, $type = '' ) {

	if( ! function_exists( 'edd_software_licensing' ) ) {
		return;
	}

	$license_data = edd_zapier_get_license_data( $license_id, $download_id, $payment_id );

	EDD_Zapier_Connection::push_and_scrub( "edd_new_license", $license_data );
}
add_action( 'edd_sl_store_license', 'edd_zapier_create_license', 10, 4 );

/**
 * Get relevant data for a given complete order.
 *
 * @since  1.0.0
 *
 * @param  integer $payment_id Payment post ID.
 * @return array               Order data.
 */
function edd_zapier_get_order_data( $payment_id = 0 ) {

	$user_info                      = edd_get_payment_meta_user_info( $payment_id );
	$order_data                     = array();
	$order_data['ID']               = $payment_id;
	$order_data['key']              = edd_get_payment_key( $payment_id );
	$order_data['subtotal']         = edd_get_payment_subtotal( $payment_id );
	$order_data['tax']              = edd_get_payment_tax( $payment_id );
	$order_data['fees']             = edd_get_payment_fees( $payment_id );
	$order_data['total']            = edd_get_payment_amount( $payment_id );
	$order_data['gateway']          = edd_get_payment_gateway( $payment_id );
	$order_data['email']            = edd_get_payment_user_email( $payment_id );
	$order_data['date']             = get_the_time( 'Y-m-d H:i:s', $payment_id );
	$order_data['products']         = edd_zapier_get_order_products( $payment_id );
	$order_data['discount_codes']   = $user_info['discount'];
	$order_data['first_name']       = $user_info['first_name'];
	$order_data['last_name']        = $user_info['last_name'];
	$order_data['transaction_id']   = edd_get_payment_transaction_id( $payment_id );
	$order_data['billing_address']  = ! empty( $user_info['address'] ) ? $user_info['address'] : array( 'line1' => '', 'line2' => '', 'city' => '', 'country' => '', 'state' => '', 'zip' => '' );
	$order_data['shipping_address'] = ! empty( $user_info['shipping_info'] ) ? $user_info['shipping_info'] : array( 'address' => '', 'address2' => '', 'city' => '', 'country' => '', 'state' => '', 'zip' => '' );
	$order_data['metadata']         = edd_zapier_get_order_metadata( $payment_id );

	return $order_data;
}

/**
 * Retrieve an array of all custom metadata on a payment
 *
 * @since  1.3
 *
 * @param  integer $payment_id Payment post ID.
 * @return array               Metadata
 */
function edd_zapier_get_order_metadata( $payment_id = 0 ) {

	$ignore = array(
		'_edd_payment_gateway',
		'_edd_payment_mode',
		'_edd_payment_transaction_id',
		'_edd_payment_user_ip',
		'_edd_payment_customer_id',
		'_edd_payment_user_id',
		'_edd_payment_user_email',
		'_edd_payment_purchase_key',
		'_edd_payment_number',
		'_edd_completed_date',
		'_edd_payment_unlimited_downloads',
		'_edd_payment_total',
		'_edd_payment_tax',
		'_edd_payment_meta',
		'user_info',
		'cart_details',
		'downloads',
		'fees',
		'currency',
		'address'
	);

	$metadata = get_post_custom( $payment_id );
	foreach( $metadata as $key => $value ) {

		if( in_array( $key, $ignore ) ) {

			if( '_edd_payment_meta' == $key ) {

				// Look for custom values added to _edd_payment_meta
				foreach( $value as $inner_key => $inner_value ) {

					if( ! in_array( $inner_key, $ignore ) ) {

						$metadata[ $inner_key ] = $inner_value;

					}

				}

			}

			unset( $metadata[ $key ] );
		}

	}

	return $metadata;

}

/**
 * Get relevant data for a given license ID.
 *
 * @since  1.1
 *
 * @param  integer $license_id License post ID.
 * @return array               License data.
 */
function edd_zapier_get_license_data( $license_id = 0, $download_id = 0, $payment_id = 0 ) {

	$license = edd_software_licensing()->get_license( $license_id );

	// The license ID supplied didn't give us a valid license, no data to return.
	if ( false === $license ) {
		return array();
	}

	if ( empty( $download_id ) ) {

		$download_id = $license->download_id;

	}

	if ( empty( $payment_id ) ) {

		$payment_id = $license->payment_id;

	}

	$customer_id = edd_get_payment_customer_id( $payment_id );

	if( empty( $customer_id ) ) {

		$user_info       = edd_get_payment_meta_user_info( $payment_id );
		$customer        = new stdClass;
		$customer->email = edd_get_payment_user_email( $payment_id );
		$customer->name  = $user_info['first_name'];

	} else {

		$customer = new EDD_Customer( $customer_id );

	}

	if( $license->is_lifetime ) {
		$expiration = 'never';
	} else {
		$expiration = $license->expiration;
		$expiration = date( 'Y-n-d H:i:s', $expiration );
	}

	$download = method_exists( $license, 'get_download' ) ? $license->get_download() : new EDD_SL_Download( $download_id );


	$license_data = array(
		'ID'               => $license->ID,
		'key'              => $license->key,
		'customer_email'   => $customer->email,
		'customer_name'    => $customer->name,
		'product_id'       => $download_id,
		'product_name'     => $download->get_name(),
		'activation_limit' => $license->activation_limit,
		'activation_count' => $license->activation_count,
		'activated_urls'   => implode( ',', $license->sites ),
		'expiration'       => $expiration,
		'is_lifetime'      => $license->is_lifetime ? '1' : '0',
		'status'           => $license->status,
	);

	return $license_data;
}

/**
 * Get ordered products for a given order.
 *
 * @since  1.0.0
 *
 * @param  integer $payment_id Payment post ID.
 * @return array               Ordered products.
 */
function edd_zapier_get_order_products( $payment_id = 0 ) {

	$cart_items = edd_get_payment_meta_cart_details( $payment_id );
	$products   = array();

	foreach ( $cart_items as $key => $item ) {

		$price_name = '';
		if ( isset( $cart_items[ $key ]['item_number'] ) ) {
			$price_options  = $cart_items[ $key ]['item_number']['options'];
			if ( isset( $price_options['price_id'] ) ) {
				$price_name = edd_get_price_option_name( $item['id'], $price_options['price_id'], $payment_id );
			}
		}

		$products[ $key ]['Product']   = $item['name'];
		$products[ $key ]['Subtotal']  = $item['subtotal'];
		$products[ $key ]['Tax']       = $item['tax'];
		$products[ $key ]['Discount']  = $item['discount'];
		$products[ $key ]['Price']     = $item['price'];
		$products[ $key ]['PriceName'] = $price_name;
		$products[ $key ]['Quantity']  = $item['quantity'];
	}

	return $products;
}
