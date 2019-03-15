<?php
/**
 * Class: WPUAP_Model_Settings
 * @author Flipper Code <hello@flippercode.com>
 * @version 4.0.0
 * @package Avatar
 */
if ( ! class_exists( 'WPUAP_Model_Settings' ) ) {
	/**
	 * Setting model for Plugin Options.
	 * @package Avatar
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPUAP_Model_Settings extends FlipperCode_Model_Base {
		/**
		 * Intialize Backup object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpuap_view_overview' => __( 'WP User Avatar', WPUAP_TEXT_DOMAIN ),
				'wpuap_howto_overview' => __( 'How It Works', WPUAP_TEXT_DOMAIN ),
				'wpuap_manage_settings' => __( 'Plugin Settings', WPUAP_TEXT_DOMAIN ),				
			);
		}
		/**
		 * Add or Edit Operation.
		 */
		function save() {
			$response['success'] = __( 'Setting(s) saved successfully.',WPUAP_TEXT_DOMAIN );
		    return $response;
		}
		function install(){
		  $defaults = array(
			'wp_user_avatar_hide_webcam' => 0,
			'wp_user_avatar_hide_mediamanager' => 0,
			'avatar_storage_option' => 'media',
			'wp_user_avatar_upload_size_limit' => 8388608,
			'wp_user_avatar_upload_registration' => 1
		  );
		  foreach( $defaults as $key => $value )
		   if( get_option( $key, false ) === false )
		   update_option( $key, $value );
		}
	}
}
