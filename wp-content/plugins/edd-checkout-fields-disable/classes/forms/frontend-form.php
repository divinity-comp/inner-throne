<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class CFM_Frontend_Form extends CFM_Render_Form {
	private static $_instance;
	
	function __construct() {
		add_action( 'edd_insert_payment', array($this,'submit_post'),10,2);
		add_filter( 'edd_purchase_form_required_fields', array($this, 'req_fields'), 10);
		remove_action( 'edd_register_fields_before', 'edd_user_info_fields' );
		add_action( 'edd_register_fields_before', array($this, 'add_fields'));
		remove_action( 'edd_purchase_form_after_user_info', 'edd_user_info_fields' );
		add_action('edd_purchase_form_after_user_info', array($this, 'add_fields'));

	}
	
	public static function init() {
		if ( !self::$_instance ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function add_fields() {
		ob_start();
		$this->render_form( get_option( 'edd_cfm_id' ) );		
		$content = ob_get_contents();
		ob_end_clean();
		echo $content;
	}
	
	public function submit_post( $payment, $payment_data ) {
		require_once EDD_PLUGIN_DIR . 'includes/admin/upload-functions.php';
		if ( function_exists( 'edd_set_upload_dir' ) ) {
			add_filter( 'upload_dir', 'edd_set_upload_dir' );
		}
		$form_id       = get_option( 'edd_cfm_id' );
		$form_vars     = $this->get_input_fields( $form_id );
		$form_settings = get_post_meta( $form_id, 'edd-checkout-fields_settings', true );
		list( $post_vars, $tax_vars, $meta_vars) = $form_vars;
		$post_id = $payment;
		self::update_post_meta( $meta_vars, $post_id, $form_vars );
		// set the post form_id for later usage
		update_post_meta( $post_id, self::$config_id, $form_id );
		// send the response (these are options in 2.1, so let's set this array up for that)
		if ( function_exists( 'edd_set_upload_dir' ) ) {
			remove_filter( 'upload_dir', 'edd_set_upload_dir' );
		}
	}
	
	public static function update_post_meta( $meta_vars, $post_id, $form_vars) {
		// prepare the meta vars
		list( $meta_key_value, $multi_repeated, $files ) = self::prepare_meta_fields( $meta_vars );
		// save custom fields
		foreach ($form_vars[2] as $key => $value){
			if ( isset( $_POST[$value['name']] ) ){
				update_post_meta( $post_id, $value['name'],$_POST[$value['name']]);
			}
		}
		// save all custom fields
		foreach ( $meta_key_value as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
		// save any multicolumn repeatable fields
		foreach ( $multi_repeated as $repeat_key => $repeat_value ) {
			// first, delete any previous repeatable fields
			delete_post_meta( $post_id, $repeat_key );
			// now add them
			foreach ( $repeat_value as $repeat_field ) {
				add_post_meta( $post_id, $repeat_key, $repeat_field );
			}
		}
		// save any files attached
		foreach ( $files as $file_input ) {
			// delete any previous value
			delete_post_meta( $post_id, $file_input[ 'name' ] );
			foreach ( $file_input[ 'value' ] as $attachment_id ) {
				cfm_associate_attachment( $attachment_id, $post_id );
				add_post_meta( $post_id, $file_input[ 'name' ], $attachment_id );
			}
		}
	}
	
	public static function req_fields( $fields = array() ){

		if( isset( $fields['edd_first'] ) ) {
			unset( $fields['edd_first'] );
		}

		if( isset( $fields['edd_last'] ) ) {
			unset( $fields['edd_last'] );
		}

		$form_id   = get_option( 'edd_cfm_id' );
		$form_vars = CFM_Render_Form::get_input_fields( $form_id );
		$new_req   = array();

		foreach ( $form_vars[0] as $key => $value ) {
			if ( isset ( $value['required'] ) && $value['required'] == 'yes' ) {
				$new_req[ $value['name'] ] = array(
					'error_id' => 'invalid_' . $value['name'],
					'error_message' => sprintf( __( 'Please fill out %s', 'edd_cfm' ), $value['label'] )
				);
			}
		}

		$fields = array_merge( $fields, $new_req );

		return $fields;
	}

}
