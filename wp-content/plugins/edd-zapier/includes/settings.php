<?php

/**
 * Registers the settings subsection
 *
 * @since  1.3
 *
 * @param  array $sections Sections array.
 * @return array           Sections array.
 */
function edd_zapier_register_setting_section( $sections ) {
	$sections['zapier'] = __( 'Zapier', 'edd-zapier' );

	return $sections;
}
add_filter( 'edd_settings_sections_extensions', 'edd_zapier_register_setting_section', 10, 1 );

/**
 * Add custom extension settings to EDD.
 *
 * @since  1.0.0
 *
 * @param  array $settings Settings array.
 * @return array           Settings array.
 */
function edd_zapier_admin_options( $settings ) {

	$zapier_settings = array();

	$zapier_settings[] = array(
		'id'   => 'edd_zapier',
		'name' => '<strong id="edd_product_support">' . __( 'Zapier Settings', 'edd-zapier' ) . '</strong>',
		'type' => 'description',
		'desc' => __( 'Use the buttons below to trigger sample data to Zapier.', 'edd-zapier' )
	);

	$zapier_settings[] = array(
		'id'   => 'edd_new_customer',
		'type' => 'button',
		'std'  => __( 'Send New Customer Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_update_customer',
		'type' => 'button',
		'std'  => __( 'Send Updated Customer Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_new_order',
		'type' => 'button',
		'std'  => __( 'Send New Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_pending_order',
		'type' => 'button',
		'std'  => __( 'Send Pending Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_failed_order',
		'type' => 'button',
		'std'  => __( 'Send Failed Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_abandoned_order',
		'type' => 'button',
		'std'  => __( 'Send Abandoned Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_refunded_order',
		'type' => 'button',
		'std'  => __( 'Send Refunded Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_revoked_order',
		'type' => 'button',
		'std'  => __( 'Send Revoked Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_deleted_order',
		'type' => 'button',
		'std'  => __( 'Send Deleted Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_preapproval_order',
		'type' => 'button',
		'std'  => __( 'Send Pre-Approved Order Notification', 'edd-zapier' ),
	);

	$zapier_settings[] = array(
		'id'   => 'edd_file_downloaded',
		'type' => 'button',
		'std'  => __( 'Send File Downloaded Notification', 'edd-zapier' ),
	);

	if ( class_exists( 'EDD_Subscription' ) ) {

		$zapier_settings[] = array(
			'id'   => 'edd_subscription_created',
			'type' => 'button',
			'std'  => __( 'Send New Subscription Notification', 'edd-zapier' ),
		);

		$zapier_settings[] = array(
			'id'   => 'edd_subscription_payment',
			'type' => 'button',
			'std'  => __( 'Send New Subscription Payment Notification', 'edd-zapier' ),
		);

		$zapier_settings[] = array(
			'id'   => 'edd_subscription_renewed',
			'type' => 'button',
			'std'  => __( 'Send Subscription Renewed Notification', 'edd-zapier' ),
		);

		$zapier_settings[] = array(
			'id'   => 'edd_subscription_completed',
			'type' => 'button',
			'std'  => __( 'Send Subscription Completed Notification', 'edd-zapier' ),
		);

		$zapier_settings[] = array(
			'id'   => 'edd_subscription_expired',
			'type' => 'button',
			'std'  => __( 'Send Subscription Expired Notification', 'edd-zapier' ),
		);

		$zapier_settings[] = array(
			'id'   => 'edd_subscription_failing',
			'type' => 'button',
			'std'  => __( 'Send Subscription Failing Notification', 'edd-zapier' ),
		);

		$zapier_settings[] = array(
			'id'   => 'edd_subscription_cancelled',
			'type' => 'button',
			'std'  => __( 'Send Subscription Cancelled Notification', 'edd-zapier' ),
		);

	}

	if ( function_exists( 'edd_software_licensing' ) ) {
		$zapier_settings[] = array(
			'id'   => 'edd_new_license',
			'type' => 'button',
			'std'  => __( 'Send New License Key Notification', 'edd-zapier' ),
		);
		$zapier_settings[] = array(
			'id'   => 'edd_active_license',
			'type' => 'button',
			'std'  => __( 'Send Active License Notification', 'edd-zapier' ),
		);
		$zapier_settings[] = array(
			'id'   => 'edd_inactive_license',
			'type' => 'button',
			'std'  => __( 'Send Inactive License Notification', 'edd-zapier' ),
		);
		$zapier_settings[] = array(
			'id'   => 'edd_expired_license',
			'type' => 'button',
			'std'  => __( 'Send Expired License Notification', 'edd-zapier' ),
		);
		$zapier_settings[] = array(
			'id'   => 'edd_disabled_license',
			'type' => 'button',
			'std'  => __( 'Send Disabled License Notification', 'edd-zapier' ),
		);
		$zapier_settings[] = array(
			'id'   => 'edd_license_activated',
			'type' => 'button',
			'std'  => __( 'Send License Activated Notification', 'edd-zapier' ),
		);
		$zapier_settings[] = array(
			'id'   => 'edd_license_deactivated',
			'type' => 'button',
			'std'  => __( 'Send License Deactivated Notification', 'edd-zapier' ),
		);
	}

	if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
		$zapier_settings = array( 'zapier' => $zapier_settings );
	}

	return array_merge( $settings, $zapier_settings );

}
add_filter( 'edd_settings_extensions', 'edd_zapier_admin_options' );

/**
 * EDD Settings Description Callback.
 *
 * @since 1.0.0
*/
if ( ! function_exists( 'edd_description_callback' ) ) {
	function edd_description_callback( $args = array() ) {
		echo $args['desc'];
	}
}

/**
 * EDD Settings Button Callback.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'edd_button_callback' ) ) {
	function edd_button_callback( $args = array() ) {
		printf(
			'<a href="%1$s" class="button-secondary">%2$s</a><p class="desc">%3$s</p>',
			wp_nonce_url( add_query_arg( array( 'edd_zapier' => $args['id'] ) ), 'edd-zapier-test' ),
			$args['std'],
			$args['desc']
		);
	}
}

function edd_zapier_maybe_send_sample_data() {

	if ( ! isset( $_GET['edd_zapier'] ) || ! isset( $_GET['_wpnonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'edd-zapier-test' ) ) {
		return;
	}

	EDD_Zapier_Connection::sample_data_push( $_GET['edd_zapier'] );

}
add_action( 'admin_init', 'edd_zapier_maybe_send_sample_data' );
