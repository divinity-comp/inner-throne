<?php

/**

 * Global variables used in plugin.

 *

 * @package Avatar

 * @version 4.0.0

 */



/**

 * @since 1.8

 * @uses get_intermediate_image_sizes()

 * @uses get_option()

 * @uses wp_max_upload_size()

 */



// Define global variables
// add_action("admin_init", 'wpua_global_variables');
// function wpua_global_variables(){


// 	global $avatar_default,

// 		   $show_avatars,

// 		   $wpua_avatar_default,

// 		   $wpua_disable_gravatar,

// 		   $wpua_upload_registration,

// 		   $wp_user_avatar_thumbnail_w,

// 		   $wp_user_avatar_thumbnail_h,

// 		   $wp_user_avatar_upload_max_w,

// 		   $wp_user_avatar_upload_max_h,

// 		   $wpua_tinymce,

// 		   $mustache_original,

// 		   $mustache_medium,

// 		   $mustache_thumbnail,

// 		   $mustache_avatar,

// 		   $mustache_admin,

// 		   $wpua_default_avatar_updated,

// 		   $upload_size_limit,

// 		   $upload_size_limit_with_units,

// 		   $wpua_user_upload_size_limit,

// 		   $wpua_upload_size_limit,

// 		   $wpua_upload_size_limit_with_units,

// 		   $all_sizes,

// 		   $wpua_imgloader,

// 		   $wpua_upload_dir,

// 		   $wpua_upload_url,

// 		 $avatar_storage_option,

// 		 $wp_user_avatar_hide_webcam,

// 		 $wp_user_avatar_hide_mediamanager,

// 		 $wp_user_avatar_storage;



// 	// Default avatar name

// 	$avatar_default = get_option( 'avatar_default' );

// 	// Attachment ID of default avatar

// 	$role_based_avatar = unserialize(get_option( 'role_based_avatar' ));

// 	$uid = isset($_GET['user_id']) ? $_GET['user_id'] : get_current_user_id();

// 	$users = new WP_User( $uid );

// 	$wpua_avatar_default = 

// 		isset($role_based_avatar[ $users->roles[0] ]) 

// 		&& ! empty($role_based_avatar[ $users->roles[0] ]) 

// 			? $role_based_avatar[ $users->roles[0] ] 

// 			: get_option( 'avatar_default_wp_user_avatar' );



// 	// Avatar media uploader

// 		$avatar_storage_option = get_option( 'avatar_storage_option','media' );

// 		$wp_user_avatar_storage = get_option( 'wp_user_avatar_storage' );



// 	// Booleans

// 	$show_avatars = get_option( 'show_avatars' );

// 	$wpua_disable_gravatar = get_option( 'wp_user_avatar_disable_gravatar' );

// 	$wpua_upload_registration = get_option( 'wp_user_avatar_upload_registration' );



// 	// Avatar dimensions

// 	$wp_user_avatar_thumbnail_w = get_option( 'wp_user_avatar_thumbnail_w' );

// 	$wp_user_avatar_thumbnail_h = get_option( 'wp_user_avatar_thumbnail_h' );



// 	// Avatar dimensions

// 	$wp_user_avatar_hide_mediamanager = get_option( 'wp_user_avatar_hide_mediamanager' );

// 	$wp_user_avatar_hide_webcam = get_option( 'wp_user_avatar_hide_webcam' );



// 	// Default avatar 512x512

// 	$mustache_original = WPUAP_URL.'assets/images/wpua.png';

// 	// Default avatar 300x300

// 	$mustache_medium = WPUAP_URL.'assets/images/wpua-300x300.png';

// 	// Default avatar 150x150

// 	$mustache_thumbnail = WPUAP_URL.'assets/images/wpua-150x150.png';

// 	// Default avatar 96x96

// 	$mustache_avatar = WPUAP_URL.'assets/images/wpua-96x96.png';

// 	// Default avatar 32x32

// 	$mustache_admin = WPUAP_URL.'assets/images/wpua-32x32.png';



// 	// Check for updates

// 	$wpua_default_avatar_updated = get_option( 'wp_user_avatar_default_avatar_updated' );



// 	// Server upload size limit

// 	$upload_size_limit = wp_max_upload_size();

// 	// Convert to KB

// 	if ( $upload_size_limit > 1024 ) {

// 		$upload_size_limit /= 1024;

// 	}

// 	$upload_size_limit_with_units = (int) $upload_size_limit.'KB';



// 	// User upload size limit

// 	$wpua_user_upload_size_limit = get_option( 'wp_user_avatar_upload_size_limit' );

// 	if ( $wpua_user_upload_size_limit == 0 || $wpua_user_upload_size_limit > wp_max_upload_size() ) {

// 		$wpua_user_upload_size_limit = wp_max_upload_size();

// 	}

// 	// Value in bytes

// 	$wpua_upload_size_limit = $wpua_user_upload_size_limit;

// 	// Convert to KB

// 	if ( $wpua_user_upload_size_limit > 1024 ) {

// 		$wpua_user_upload_size_limit /= 1024;

// 	}

// 	$wpua_upload_size_limit_with_units = (int) $wpua_user_upload_size_limit.'KB';



// 	// Check for custom image sizes

// 	$all_sizes = array_merge( get_intermediate_image_sizes(), array( 'original' ) );





// 	// Avatar Upload Directory;

// 	$upload_dir = wp_upload_dir();



// 	if ( is_multisite() ) {

// 		$pos = strpos( $upload_dir['basedir'],'uploads' );

// 		$wpua_upload_dir = substr( $upload_dir['basedir'],0,($pos + 7) ).'/wp-user-avatar/';

// 		$pos_url = strpos( $upload_dir['baseurl'],'uploads' );

// 		$wpua_upload_url = substr( $upload_dir['baseurl'],0,($pos_url + 8) ).'/wp-user-avatar/';

// 	} else {

// 		$wpua_upload_dir = $upload_dir['basedir'].'/wp-user-avatar/';

// 		$wpua_upload_url = $upload_dir['baseurl'].'/wp-user-avatar/';

// 	}

// }
