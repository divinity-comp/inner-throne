<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class CFM_Install {
	public function init() {
		$db_version = get_option( 'edd_cfm_version' );
		EDD_CFM()->setup->register_post_type();
		if ( !$db_version ) {
			$this->create_post();
			update_option( 'edd_cfm_version', '1.0' );
			set_transient( '_edd_cfm_activation_redirect', true, 30 );
		} else {
			return;
		}
	}
	
	public function create_post() {
		global $wpdb, $wp_version;
		$page_id = get_option( 'edd_cfm_id' );
		if ( $page_id != false ) {
			return;
		}
		$page_data = array(
			'post_status' => 'publish',
			'post_type' => 'edd-checkout-fields',
			'post_author' => 1,
			'post_title' => 'Checkout Fields' 
		);
		$page_id   = wp_insert_post( $page_data );
		$data      = array(
			 1 => array(
				'input_type' => 'email',
				'template' => 'edd_email',
				'required' => 'yes',
				'label' => 'Email',
				'name' => 'edd_email',
				'is_meta' => 'no',
				'help' => 'We will send the purchase receipt to this address.',
				'css' => '',
				'placeholder' => '',
				'default' => '',
				'size' => '40' 
			),
			2 => array(
				'input_type' => 'text',
				'template' => 'edd_first',
				'required' => 'yes',
				'label' => 'First Name',
				'name' => 'edd_first',
				'is_meta' => 'no',
				'help' => 'We will use this to personalize your account experience.',
				'css' => '',
				'placeholder' => '',
				'default' => '',
				'size' => '40' 
			),
			3 => array(
				'input_type' => 'text',
				'template' => 'edd_last',
				'required' => 'yes',
				'label' => 'Last Name',
				'name' => 'edd_last',
				'is_meta' => 'no',
				'help' => 'We will use this as well to personalize your account experience.',
				'css' => '',
				'placeholder' => '',
				'default' => '',
				'size' => '40' 
			) 
		);
		update_post_meta( $page_id, 'edd-checkout-fields', $data );
		update_option( 'edd_cfm_id', $page_id );
		return;
	}
	
	public function update_to() {
		$version = get_option( 'edd_cfm_version', '1.0' );
		switch ( $version ) {
			case '1.0':
				break;
			default:
				// clean
				break;
		}
	}
}