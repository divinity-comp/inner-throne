<?php

include_once 'class.dropbox.php';

class Wpua_Dropbox_Storage extends Wpua_Storage {

	private $dropbox;

	private $app_client = 'wp-user-avatar/1.0';

	private $folder_path = '/';

	public function load() {

		$options = $this->get_storage_option();

		if ( empty( $options['access_token'] ) ) {
			return new WP_Error( 'drobbox_token', __( 'Dropbox access token is required.','wp-user-avatar-pro' ) ); }

		try {
			$this->dropbox = new Dropbox_API(array( 'access_token' => $options['access_token'] ));
		
		} catch ( Exception $e ) {
			return new WP_Error( 'drobpox_api_invalid', $e->getMessage() );
		}

		if ( ! empty( $options['folder_path'] ) ) {
			$this->folder_path .= ltrim( $options['folder_path'], '/' ); }

	}

	public function getUploadPath($filename) {

		return trailingslashit( $this->folder_path ).$filename;

	}

	public function wpua_avatar_upload( $file ) {
		try {
			$uploaded_file = $this->dropbox->put_file( $file->path, $this->getUploadPath( $file->name ));
			if( is_array( $uploaded_file ) && isset($uploaded_file['path_display'])) {
				$sharable = $this->dropbox->create_sharable_link($uploaded_file['path_display']);
				if ( $sharable && isset($sharable->url)) {
					remove_query_arg( 'dl',  $sharable->url );
					$this->avatar_url = add_query_arg( 'dl', '1', $sharable->url );
					$this->avatar_filename = $file->name;
					$this->resource = trailingslashit( $this->folder_path );
					$output = $this->save();
					return $output;
				}
			}

		} catch ( Exception $e ) {
			return new WP_Error( 'upload_problem', $e->getMessage() );
		}
	}

}
