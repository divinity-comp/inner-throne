<?php
/**
* ################################################################################
* WPIMAGE AJAX FUNCTIONS
* ################################################################################
*/

add_action('wp_ajax_wpimages_get_images', 'wpimages_get_images');
add_action('wp_ajax_wpimages_resize_image', 'wpimages_resize_image');
add_action('wp_ajax_wpimages_optimise_all_images', 'wpimages_optimise_all_images_ajax');

/**
 * Verifies that the current user has administrator permission and, if not,
 * renders a json warning and dies
 */
function wpimages_verify_permission() {
	if ( ! current_user_can('administrator') ) {
		$results = array( 'success' => false, 'message' => 'Administrator permission is required' );
		echo json_encode( $results );
		die();
	}
}

/**
 * Searches for up to 250 images that are candidates for resize and renders them
 * to the browser as a json array, then dies
 */
function wpimages_get_images() {

	wpimages_verify_permission();

	global $wpdb;

	$query = $wpdb->prepare(
		"SELECT
			$wpdb->posts.ID AS ID,
			$wpdb->posts.guid AS guid,
			$wpdb->postmeta.meta_value AS file_meta
			FROM $wpdb->posts
			INNER JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id AMD $wpdb->postmeta.meta_key = %s
			WHERE $wpdb->posts.post_type = %s
			AND $wpdb->posts.post_mime_type LIKE %s
			AND $wpdb->posts.post_mime_type != %s",
		array( '_wp_attachment_metadata', 'attachment', 'image%', 'image/bmp' )
	);

	$images = $wpdb->get_results($query);
	$results = array();

	if ( $images ) {

		$maxW = wpimages_get_option( 'wpimages_max_width', WPIMAGE_DEFAULT_MAX_WIDTH );
		$maxH = wpimages_get_option( 'wpimages_max_height', WPIMAGE_DEFAULT_MAX_HEIGHT );
		$count = 0;

		foreach ( $images as $image ) {

			$meta = unserialize( $image->file_meta );

			if ( $meta['width'] > $maxW || $meta['height'] > $maxH ) {
				$count++;
				$results[] = array(
					'id'=>$image->ID,
					'width'=>$meta['width'],
					'height'=>$meta['height'],
					'file'=>$meta['file']
				);
			}

			// Make sure we only return a limited number of records so we don't overload the ajax features.
			if ( $count >= WPIMAGE_AJAX_MAX_RECORDS ){
				break;
			}
		}
	}

	print_r( json_encode( $results ) );
	die();
}

/**
 * Resizes the image with the given id according to the configured max width and height settings
 * renders a json response indicating success/failure and dies
 */
function wpimages_resize_image() {

	wpimages_verify_permission();

	global $wpdb;

	$id = intval( $_POST['id'] );

	if ( ! $id ) {
		$results = array( 'success' => false, 'message' => __( 'Missing ID Parameter', 'wpimage' ) );
		echo json_encode( $results );
		die();
	}

	// @TODO: probably doesn't need the join...?
	$query = $wpdb->prepare(
		"SELECT
		$wpdb->posts.ID AS ID,
		$wpdb->posts.guid AS guid,
		$wpdb->postmeta.meta_value AS file_meta
		FROM $wpdb->posts
		INNER JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = %s
		WHERE $wpdb->posts.ID = %d
		AND $wpdb->posts.post_type = %s
		AND $wpdb->posts.post_mime_type LIKE %s",
		array('_wp_attachment_metadata', $id, 'attachment', 'image%')
	);

	$images = $wpdb->get_results( $query );

	if ( $images ) {

		$image = $images[0];
		$meta = unserialize($image->file_meta);
		$uploads = wp_upload_dir();
		$oldPath = $uploads['basedir'] . "/" . $meta['file'];

		$maxW = wpimages_get_option( 'wpimages_max_width', WPIMAGE_DEFAULT_MAX_WIDTH );
		$maxH = wpimages_get_option( 'wpimages_max_height', WPIMAGE_DEFAULT_MAX_HEIGHT );

		// method one - slow but accurate, get file size from file itself
		// list($oldW, $oldH) = getimagesize( $oldPath );
		// method two - get file size from meta, fast but resize will fail if meta is out of sync
		$oldW = $meta['width'];
		$oldH = $meta['height'];


		if ( ( $oldW > $maxW && $maxW > 0) || ( $oldH > $maxH && $maxH > 0 ) ) {

			$quality = wpimages_get_option( 'wpimages_quality', WPIMAGE_DEFAULT_QUALITY );

			list( $newW, $newH ) = wp_constrain_dimensions( $oldW, $oldH, $maxW, $maxH );

			$resizeResult = wpimages_image_resize( $oldPath, $newW, $newH, false, null, null, $quality );

			// Uncommend to debug fail condition.
			/*$resizeResult = new WP_Error('invalid_image', __('Could not read image size'), $oldPath);*/

			if ( ! is_wp_error( $resizeResult ) ) {

				$newPath = $resizeResult;

				if ( $newPath !== $oldPath ) {
					// remove original and replace with re-sized image
					unlink( $oldPath );
					rename( $newPath, $oldPath );
				}

				$meta['width'] = $newW;
				$meta['height'] = $newH;

				// @TODO replace custom query with update_post_meta
				$update_query = $wpdb->prepare(
					"UPDATE $wpdb->postmeta
					SET $wpdb->postmeta.meta_value = %s
					WHERE  $wpdb->postmeta.post_id = %d
					AND $wpdb->postmeta.meta_key = %s",
					array( maybe_serialize( $meta ), $image->ID, '_wp_attachment_metadata' )
				);

				$wpdb->query( $update_query );

				$results = array(
					'success' => true,
					'id' => $id,
					'message' => sprintf( __('OK: %s', 'wpimage'), $oldPath )
				);
			}
			else {
				$results = array(
					'success' => false,
					'id'=> $id,
					'message' => sprintf( 
						__( 'ERROR: %s (%s)', 'wpimage' ),
						$oldPath,
						htmlentities( $resizeResult->get_error_message() ) 
					) 
				);
			}
		}
		else {
			$results = array(
				'success' => true,
				'id' => $id,
				'message' => sprintf( __( 'SKIPPED: %s (Resize not required)', 'wpimage' ), $oldPath ) 
			);
		}
	}
	else {
		$results = array(
			'success' => false,
			'id' => $id,
			'message' => sprintf( 
				__('ERROR: (Attachment with ID of %s not found)', 'wpimage' ),
				htmlentities( $id )
			)
		);
	}

	// If there is a quota we need to reset the directory size cache so it will re-calculate.
	delete_transient( 'dirsize_cache' );
	
	echo json_encode( $results );
	die();
}

function wpimages_optimise_all_images(){

	global $wpdb;

	$return = array(
		'error' => 0,
		'checked_num' => 0,
		'optimised_num' => 0,
	);
	
	$img_ids = array();

	$res = $wpdb->get_results( "SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type='attachment' AND post_mime_type IN ('image/jpeg','image/png')" );
	
	if( $res && ! empty( $res ) ){
		
		foreach ( $res as $key => $val ) {

			$img_meta = get_post_meta( $val->id, '_wpimage_size', true );

			if( ! $img_meta || ! isset( $img_meta['meta'] ) ) {	// Image is not already optimised.
				$img_ids[] = $val->id;
			}
		}
	}

	if( ! empty( $img_ids ) ){

		if( ! class_exists( 'Wpimage' ) ){
			require_once dirname( __FILE__ ) . '/lib/class-wp-image.php';
		}
		
		$wpimage_ins  = new Wpimage();

		$wpimage_options = unserialize( get_option('_wpimage_options') );
		$backup_before_compression = $wpimage_options['backup_before_compression'];

		foreach( $img_ids as $key => $image_id ) {

			$original_image_path = get_attached_file( $image_id );
			
			$optimised = wpimages_optimize_image( $original_image_path, $backup_before_compression );

			$return['checked_num']++;

            if ( $optimised && $optimised['url'] && wpimages_replace_media_image( $original_image_path, $optimised['url'] ) ) {

            	$savings_percentage = (int) $optimised['saved_bytes'] / (int) $optimised['original_size'] * 100;
                
                // Store compressed info to DB.
                update_post_meta( $image_id, '_wpimage_size', array(
                    'success' => true,
                    'meta' => wp_get_attachment_metadata( $image_id ),
                    'original_size' => wpimages_pretty_kb( $optimised['original_size'] ),
                    'compressed_size' => wpimages_pretty_kb( $optimised['bytes'] ),
                    'saved_bytes' => wpimages_pretty_kb( $optimised['saved_bytes'] ),
                    'savings_percent' => round( $savings_percentage, 2 ) . '%',
                    'backup_before_compression' => $optimised['backup_before_compression']
            	));

        		$wpimage_ins->deleteResource( $optimised['public_id'] );

        		$return['optimised_num']++;
            }
            else {

                // Error or no optimization.
                if ( file_exists( $original_image_path ) ) {

                    $kv = array(
                        'original_size' => wpimages_pretty_kb( filesize( $original_image_path ) ),
                        'error' => $optimised['error']
                    );

                    if( 'This image can not be optimized any further' === $kv['error'] ){
                        $kv['compressed_size'] = 'No savings found';
                        $kv['no_savings'] = true;
                    }

                    update_post_meta( $image_id, '_wpimage_size', $kv );
                }
            }
		}
	}

	return $return;
}

function wpimages_optimise_all_images_ajax(){
	$p = $_POST;
	if( isset( $p['nonce'] ) && wp_verify_nonce( $p['nonce'], 'optimisationio-cloudinary-api-settings' ) ){
		$return = wpimages_optimise_all_images();
	}
	else{
		$return = array( 'error' => 1, 'msg' => 'Non verified access' );
	}
	print_r( wp_json_encode( $return ) );
	die();
}
