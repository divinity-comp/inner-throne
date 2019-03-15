<?php
if ( ! class_exists( 'WP_Error' ) ) {
	include WPINC.'/class-wp-error.php'; }

class Wpua_Avatar_Storage {

	public static $instance;

	private $storage;

	public $storage_type;

	public $storage_options;

	private $storage_dir;

	public $authenticate = true;

	private static $exists = array();

	public function __construct() {

		global $wp_user_avatar_storage, $avatar_storage_option;

		$this->storage_dir = WPUAP_PLUGIN_CLASSES.'storage/';

	}

	public static function getInstance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self; }
		return self::$instance;
	}

	public function Factory( $storage_type = '',  $validate = false ) {

		if ( ! empty( $storage_type ) ) {
			$this->storage_type = $storage_type; }

		if ( isset( self::$exists[$this->storage_type] ) ) {
			return self::$exists[$this->storage_type]; }

		switch ( $this->storage_type ) {

			case 'directory' :
				include_once($this->storage_dir.'class-wpua-directory-storage.php');
				$storage = new Wpua_Directory_Storage();
				$options = array( 'upload_dir' => $this->storage_options['directory'] );
				$storage->set_storage_option( $options );
			break;

			case 'aws' :
				include_once($this->storage_dir.'class-wpua-aws-storage.php');
				$storage = new Wpua_Aws_Storage();
				$options = array(
					'bucket' => $this->storage_options['aws'],
					'settings' => $this->storage_options['setting']['aws'],
				   );
				$storage->set_storage_option( $options );
			break;

			case 'dropbox' :
				include_once($this->storage_dir.'class-wpua-dropbox-storage.php');
				$storage = new Wpua_Dropbox_Storage();
				$storage->set_storage_option( array(
					'access_token' => $this->storage_options['dropbox']['access_token'],
					'folder_path' => $this->storage_options['dropbox']['upload_path'],
		        ));
			break;

			case 'media' :
			default :
				require_once($this->storage_dir.'class-wpua-media-storage.php');
				$storage = new Wpua_Media_Storage();
			break;

		}

		$storage->type = $this->storage_type;

		if ( method_exists( $storage, 'load' ) ) {
			$return = $storage->load();
			if ( is_wp_error( $return ) ) {

				if ( $validate == true ) {
					return $return; }

				if ( $storage->type !== 'media' ) {
					return $this->Factory( 'media' ); }
			}
		}

		$this->storage = $storage;
		self::$exists[$this->storage_type] = $storage;

		return $storage;
	}

	public function wpua_avatar_upload( $file ) {

		if ( $this->storage->is_validate_upload( $file ) ) {
			return  $this->storage->wpua_avatar_upload( $file ); }

	}

	public function delete_temp_file( $file ) {

		if ( is_object( $file ) ) {
			$filepath = $file->path; } else {
			$filepath = $file; }

			@unlink( $filepath );

	}

}



