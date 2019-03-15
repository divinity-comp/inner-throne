<?php

class Wpua_Directory_Storage extends Wpua_Storage {

	public $upload_dir;

	public $default = false;

	public function load() {
		global $wpua_upload_dir;

		$setting = $this->get_storage_option();
		if ( ! empty( $setting ) ) {
			if ( is_string( $setting ) ) {
				$upload_dir = $setting; } else if ( is_array( $setting ) && isset( $setting['upload_dir'] ) ) {
				$upload_dir = $setting['upload_dir']; }
				if ( ! empty( $upload_dir ) ) {
					$upload_dir = ltrim( trim( $upload_dir ), '/' );
					$upload_dir = trailingslashit( trailingslashit( ABSPATH ). $upload_dir );
					if ( ! is_dir( $upload_dir ) ) {
						return new WP_Error( 'dirnotExists', $upload_dir.__( ' not exits.','wp-user-avatar-pro' ) );
					}
				} else {
					$upload_dir = $wpua_upload_dir;
				}
		} else {
			$upload_dir = $wpua_upload_dir;
		}

		if ( ! is_writable( $upload_dir ) ) {
			return new WP_Error( 'dirperm', $upload_dir.' not have write permisson.' ); }

		$this->upload_dir = $upload_dir;

	}

	public function get_upload_dir() {
		return $this->upload_dir;
	}

	public function get_upload_url() {
		$root_path = ltrim( str_replace( ABSPATH, '', $this->upload_dir ), '/' );
		return trailingslashit( site_url() ).$root_path;
	}

	public function wpua_avatar_upload( $file ) {

		if ( @rename( $file->path, $this->get_upload_dir().$file->name ) ) {
			$upload_basepath = trim( ltrim( str_replace( ABSPATH, '', $this->upload_dir ), '/' ) );
			$this->avatar_url = $this->get_upload_url().$file->name;
			$this->avatar_filename = $file->name;
			$this->resource = $upload_basepath;
			return $this->save();
		}
	}



}
