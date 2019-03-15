<?php

class Wpua_Media_Storage extends Wpua_Storage {

	public function wpua_avatar_upload( $file ) {

		$filetype = wp_check_filetype( $file->name );

		$media_upload = array();

		$media_upload['file'] = array(
	    'name' => $file->name,
	    'type' => $filetype['type'],
	    'tmp_name' => $file->path,
	    'error' => 0,
	    'size' => filesize( $file->path ),
		);

		$media_file = wp_handle_upload( $media_upload['file'], array(
			'test_form' => false,
			'test_upload' => false,
			'action' => 'custom_action',
		));

		if ( $media_file['file'] ) {

			$url = $media_file['url'];
			$filepath = $media_file['file'];
			if ( $image_meta = @wp_read_image_metadata( $filepath ) ) {
				if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
					$title = $image_meta['title']; }
			}

			$attachment = array(
			'guid'           => $url,
			'post_mime_type' => $filetype['type'],
			'post_title'     =>  preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->name),
			);
			$attachment_id = wp_insert_attachment( $attachment, $filepath );

			if ( ! is_wp_error( $attachment_id ) ) {
				//$this->delete_attachment_by_user( $this->user_id );
				wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $filepath ) );
				update_post_meta( $attachment_id, '_wp_attachment_wp_user_avatar', $this->user_id );
				$arr = wp_get_attachment_image_src( $attachment_id, 'full' );
				$this->avatar_url = $arr[0];
				$this->avatar_filename = basename( $filepath );
				$this->resource = $attachment_id;
				$saved = $this->save();
				if ( $saved === false ) {
					//$this->delete_attachment( $attachment_id );
					return $saved;
				}

				return $saved;
			}
		} else {

			return new WP_Error( 'file_upload_problem', __( "Media avatar could't uploading please check you have right permission for uploads folder.",'wp-user-avatar-pro' ) );

		}

	}

	public function delete_attachment_by_user( $user_id ) {

		if ( ! $user_id ) {
			return false; }

		$q = array(
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'posts_per_page' => '-1',
		'meta_query' => array(
		  array(
			'key' => '_wp_attachment_wp_user_avatar',
			'value' => $user_id,
			'compare' => '=',
		  ),
		),
		);
		$avatars_wp_query = new WP_Query( $q );
		if ( $avatars_wp_query->have_posts() ) {
			while ( $avatars_wp_query->have_posts() ) : $avatars_wp_query->the_post();
				$this->delete_attachment( get_the_ID() );
			endwhile;
			wp_reset_query();
		}

	}

	public function delete_attachment( $attachment_id, $force = true ) {

		if ( wp_attachment_is_image( $attachment_id ) ) {
			wp_delete_attachment( $attachment_id, $force );
		}

	}


}
