<?php
/**
 * Export Payments Class
 *
 * Export payment hsitory to a CSV
 *
 * @package     Restrict Content Pro
 * @subpackage  Export Class
 * @copyright   Copyright (c) 2017, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class RCP_Payments_Export extends RCP_Export {

	/**
	 * Our export type. Used for export-type specific filters / actions
	 *
	 * @access      public
	 * @var         string
	 * @since       1.5
	 */
	public $export_type = 'payments';

	/**
	 * Set the CSV columns
	 *
	 * @access      public
	 * @since       1.5
	 * @return      array
	 */
	public function csv_cols() {
		$cols = array(
			'id'               => __( 'ID',   'rcp' ),
			'status'           => __( 'Status', 'rcp' ),
			'object_type'      => __( 'Purchase Type', 'rcp' ),
			'object_id'        => __( 'Subscription ID', 'rcp' ),
			'subscription'     => __( 'Subscription Name', 'rcp' ),
			'amount'           => __( 'Amount', 'rcp' ),
			'subtotal'         => __( 'Subtotal', 'rcp' ),
			'credits'          => __( 'Credits', 'rcp' ),
			'fees'             => __( 'Fees', 'rcp' ),
			'discount_amount'  => __( 'Discount Amount', 'rcp' ),
			'discount_code'    => __( 'Discount Code', 'rcp' ),
			'user_id'          => __( 'User ID', 'rcp' ),
			'user_login'       => __( 'User Login', 'rcp' ),
			'payment_type'     => __( 'Payment Type', 'rcp' ),
			'gateway'          => __( 'Gateway', 'rcp' ),
			'subscription_key' => __( 'Subscription Key', 'rcp' ),
			'transaction_id'   => __( 'Transaction ID', 'rcp' ),
			'date'             => __( 'Date', 'rcp' )
		);
		return $cols;
	}

	/**
	 * Get the data being exported
	 *
	 * @access      public
	 * @since       1.5
	 * @return      array
	 */
	public function get_data() {
		global $wpdb;

		$data = array();
		$args = array();

		if( ! empty( $_POST['rcp-year'] ) ) {

			$args['date'] = array();
			$args['date']['year'] = absint( $_POST['rcp-year'] );

			if( ! empty( $_POST['rcp-month'] ) ) {

				$args['date']['month'] = absint( $_POST['rcp-month'] );

			}

		}

		$args['number'] = 999999;

		$rcp_db   = new RCP_Payments;
		$payments = $rcp_db->get_payments( $args );

		foreach ( $payments as $payment ) {

			$user   = get_userdata( $payment->user_id );

			$data[] = apply_filters( 'rcp_export_payments_get_data_row', array(
				'id'               => $payment->id,
				'status'           => $payment->status,
				'object_type'      => $payment->object_type,
				'object_id'        => $payment->object_id,
				'subscription'     => $payment->subscription,
				'amount'           => $payment->amount,
				'subtotal'         => $payment->subtotal,
				'credits'          => $payment->credits,
				'fees'             => $payment->fees,
				'discount_amount'  => $payment->discount_amount,
				'discount_code'    => $payment->discount_code,
				'user_id'          => $payment->user_id,
				'user_login'       => isset( $user->user_login ) ? $user->user_login : '',
				'payment_type'     => $payment->payment_type,
				'gateway'          => $payment->gateway,
				'subscription_key' => $payment->subscription_key,
				'transaction_id'   => $payment->transaction_id,
				'date'             => $payment->date
			), $payment );

		}

		$data = apply_filters( 'rcp_export_get_data', $data );
		$data = apply_filters( 'rcp_export_get_data_' . $this->export_type, $data );

		return $data;
	}
}