<?php
/**
 * WPUA_Storage Class
 * @package Avatar
 * @author Flipper Code <hello@flippercode.com>
 */

/**
 * Abstract Class for Storage.
 */
abstract class Wpua_Storage {

	/**
	 * Storage settings
	 * @var array
	 */
	protected $settings;
	/**
	 * Avatar url to be saved.
	 * @var string
	 */
	protected $avatar_url;
	/**
	 * Avatar File name.
	 * @var string
	 */
	protected $avatar_filename;
	/**
	 * Resource for avatar
	 * @var string
	 */
	protected $resource;
	/**
	 * User id of uploader.
	 * @var integer
	 */
	public $user_id = 0;
	/**
	 * Storage Type
	 * @var [type]
	 */
	public $type;
	/**
	 * Upload to storage.
	 * @param  glob $file file data.
	 */
	abstract function wpua_avatar_upload( $file );
	/**
	 * Check if file is valid.
	 * @param  glob $file File data.
	 * @return boolean       True or False.
	 */
	public function is_validate_upload( $file ) {

		if ( ! is_object( $file ) ) {
			return false; }

		if ( ! isset( $file->name ) || empty( $file->name ) ) {
			return false; }

		if ( ! file_exists( $file->path ) ) {
			return false; }

		return true;
	}
	/**
	 * User's meta name to save avatar name.
	 * @return string Avatar meta name.
	 */
	public function user_meta_name() {

		global $wpdb,$blog_id;
		return $wpdb->get_blog_prefix( $blog_id ).'user_avatar';
	}
	/**
	 * Save avatar in user's meta.
	 * @return string Output of update_user_meta.
	 */
	protected function save() {

		extract( get_object_vars( $this ), EXTR_SKIP );
		$user_metadata = compact( 'avatar_url', 'avatar_filename', 'resource', 'type' );

		$user_metadata = array_filter( $user_metadata );
		if ( count( $user_metadata ) < 4 ) {
			return new WP_Error( 'save_problem', __( 'Please set your storage meta properly','wp-user-avatar-pro' ) ); }

		$user_id = $this->user_id ? $this->user_id : get_current_user_id();
		if ( ! $user_id ) {
			return new WP_Error( 'save_problem', __( 'User not found.','wp-user-avatar-pro' ) ); }
		return $user_metadata;
		//return update_user_meta( $user_id, $this->user_meta_name(), $user_metadata );

	}

	/**
	 * Set Storage option for avatar.
	 * @param array $options Storage options.
	 */
	public function set_storage_option( $options ) {
		$this->options = $options;
	}
	/**
	 * Get storage option for avatar.
	 * @return array Storage options.
	 */
	public function get_storage_option() {

		if ( $this->options ) {
			return $this->options; }

	}
}
