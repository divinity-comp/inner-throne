<?php

/**

 * Core user functions.

 *

 * @package Avatar

 * @version 4.0.0

 */



class WP_User_Avatar_Functions {

	

	var $settings = array();

	/**

	 * Constructor

	 * @since 1.8

	 * @uses add_filter()

	 * @uses register_activation_hook()

	 * @uses register_deactivation_hook()

	 */

	public function __construct() {

		global $wpua_avatar_default;

		$this->settings = WPUA_Avatar::get_setting('wp_user_avatar_settings');

		

		add_filter( 'get_avatar', array( $this, 'wpua_get_avatar_filter' ), 10, 5 );

		// Filter to display WP User Avatar at Buddypress

		add_filter( 'bp_core_fetch_avatar', array( $this, 'wpua_bp_core_fetch_avatar_filter' ), 10, 5 );

		// Filter to display WP User Avatar by URL at Buddypress

		add_filter( 'bp_core_fetch_avatar_url', array( $this, 'wpua_bp_core_fetch_avatar_url_filter' ), 10, 5 );

        

		if( isset( $this->settings['link_profile'] ) && !empty( $this->settings['link_profile'] ) )

		add_action( 'template_redirect', array( $this, 'fronted_avatar_filter'), 10 );

		

		if( WPUA_Avatar::get_setting('wp_user_avatar_upload_registration')){

		  

		  add_action( 'bp_before_registration_submit_buttons', array( 'WPUA_Avatar', 'wpua_avatar_form_new' ) );

          add_filter('bp_signup_usermeta', array('WPUA_Avatar', 'wpua_add_signup_meta_mu'));

          add_filter( 'bp_core_activate_account', array( $this, 'bp_core_activate_account')  );

		}

		

		add_action( 'bp_before_profile_avatar_upload_content', create_function( '', 'ob_start();') );

		add_action( 'bp_after_profile_avatar_upload_content', array( $this, 'bp_after_profile_avatar_upload_content'));

		$uid = isset($_GET['user_id']) ? $_GET['user_id'] : get_current_user_id();
		$this->set_default_avatar( $uid );		

	}

	/**
	 * Function to set default avatar based on user id
	 */
	function set_default_avatar( $uid ){
		global $wpua_avatar_default;
		
		$role_based_avatar = unserialize(get_option( 'role_based_avatar' ));		

		$users = new WP_User( $uid );

		$wpua_avatar_default = 

		isset($role_based_avatar[ $users->roles[0] ]) 

		&& ! empty($role_based_avatar[ $users->roles[0] ]) 

			? $role_based_avatar[ $users->roles[0] ] 

			: get_option( 'avatar_default_wp_user_avatar' );
	}

	public function bp_after_profile_avatar_upload_content(){

	  ob_end_clean();	

	  echo do_shortcode('[avatar_upload ]');

	}

	

	public function bp_core_activate_account( $signup_meta ){

	  $avatar = new WPUA_Avatar();

	  if(!empty($signup_meta['meta']['wpua_user_avatar']))

	  $avatar->wpua_avatar_upload( $signup_meta['meta']['wpua_user_avatar'], $signup_meta['user_id']);

	  return $signup_meta;	

	}

	

	public function fronted_avatar_filter(){

		

		if( has_filter( 'get_avatar', array( $this, 'wpua_get_avatar_filter') ) )

		remove_filter( 'get_avatar', array( $this, 'wpua_get_avatar_filter') );



		add_filter( 'get_avatar', array( $this, 'wpua_fronted_get_avatar' ), 10, 5 );

		add_action( 'wp_footer', array($this, 'wp_footer') );

		

	}



	public function wpua_fronted_get_avatar( $avatar, $id_or_email='', $size='', $default='', $alt='' ){



		$old_avatar = $this->wpua_get_avatar_filter( $avatar, $id_or_email, $size, $default, $alt );

		$user_url = $this->settings['link_url'];

		

		if( preg_match('~\{website_url}~', $user_url, $matches) ){

		   if ( is_object( $id_or_email ) ) {

			  if ( isset( $id_or_email->user_id ) && $id_or_email->user_id != 0 ) {

				 

			   $user = get_user_by( 'id', $id_or_email->user_id );

			  

			  } elseif ( ! empty( $id_or_email->comment_author_email ) ) {

					

			   $user = get_user_by( 'email', $id_or_email->comment_author_email );

			  }

		   } else {

			   

			   $user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );

		   }

		   if( is_object($user) )

		   $user_url = get_the_author_meta( 'user_url', $user->ID );

		   else if( is_numeric( $id_or_email ) )

		   $user_url = get_the_author_meta( 'user_url', $id_or_email );



		}

		

		

		if( ! empty($user_url ) )

		$new_avatar = apply_filters( 'wpua_avatar_with_link', '<a href="'.esc_url($user_url).'">'.$old_avatar.'</a>', $old_avatar, $user );

		

		if( empty($new_avatar))

		$new_avatar = $old_avatar;

		

		return $new_avatar;

		

	}

	

	public function wp_footer(){

	 

	  if( function_exists('is_admin_bar_showing') ){

		if( is_admin_bar_showing()  ){

		  remove_filter( 'get_avatar', array( $this, 'wpua_fronted_get_avatar') );

		  add_filter( 'get_avatar', array( $this, 'wpua_get_avatar_filter' ), 10, 5 );

	    }  

	  }else{

		 remove_filter( 'get_avatar', array( $this, 'wpua_fronted_get_avatar') );

		 add_filter( 'get_avatar', array( $this, 'wpua_get_avatar_filter' ), 10, 5 );

	  }



	}

	



	/**

	 * Returns WP User Avatar or Gravatar-hosted image if user doesn't have Buddypress-uploaded image

	 * @param string $avatar

	 * @param array  $params

	 * @param int    $item_id

	 * @param string $avatar_dir

	 * @param string $css_id

	 * @param int    $html_width

	 * @param int    $html_height

	 * @param string $avatar_folder_url

	 * @param string $avatar_folder_dir

	 * @uses object $wpua_functions

	 * @uses wpua_get_avatar_filter()

	 */

	public function wpua_bp_core_fetch_avatar_filter($gravatar,$params,$item_id='', $avatar_dir='', $css_id='', $html_width='', $html_height='', $avatar_folder_url='', $avatar_folder_dir='') {

		global $wpua_functions;



		if ( strpos( $gravatar,'gravatar.com',0 ) > -1 ) {

			$avatar = $wpua_functions->wpua_get_avatar_filter( $gravatar, ($params['object'] == 'user') ? $params['item_id'] : '', ($params['object'] == 'user') ? (($params['type'] == 'thumb') ? 50 :150) : 50, '', '' );

			return $avatar;

		} else { 		return $gravatar; }

	}



	/**

	 * Returns WP user default local avatar URL or Gravatar-hosted image URL if user doesn't have Buddypress-uploaded image

	 * @param string $avatar

	 * @param array  $params

	 * @uses object $wpua_functions

	 * @uses wpua_get_avatar_filter()

	 */

	public function wpua_bp_core_fetch_avatar_url_filter($gravatar,$params) {

		global $wpua_functions;

		if ( strpos( $gravatar,'gravatar.com',0 ) > -1 ) {

			$avatar = $wpua_functions->wpua_get_avatar_filter( $gravatar, ($params['object'] == 'user') ? $params['item_id'] : '', ($params['object'] == 'user') ? (($params['type'] == 'thumb') ? 50 :150) : 50, '', '' );

			return $avatar;

		} else { 		return $gravatar; }

	}



	/**

	 * Returns true if user has Gravatar-hosted image

	 * @since 1.4

	 * @param int|string $id_or_email

	 * @param bool       $has_gravatar

	 * @param int|string $user

	 * @param string     $email

	 * @uses get_user_by()

	 * @uses is_wp_error()

	 * @uses wp_cache_get()

	 * @uses wp_cache_set()

	 * @uses wp_remote_head()

	 * @return bool $has_gravatar

	 */

	public function wpua_has_gravatar($id_or_email='', $has_gravatar=0, $user='', $email='') {

		global $wpua_hash_gravatar,$avatar_default, $mustache_admin, $mustache_avatar, $mustache_medium, $mustache_original, $mustache_thumbnail, $post, $wpua_avatar_default, $wpua_disable_gravatar, $wpua_functions;	
		
		// User has WPUA

		// Decide if check gravatar required or not.

		if ( trim( $avatar_default ) != 'wp_user_avatar' and trim( $avatar_default ) != 'letter_based' ) {
			return true; 
		}



		if ( ! is_object( $id_or_email ) && ! empty( $id_or_email ) ) {

			// Find user by ID or e-mail address

			$user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );

			// Get registered user e-mail address

			$email = ! empty( $user ) ? $user->user_email : '';

		}



		if ( $email == '' ) {



			if ( ! is_numeric( $id_or_email ) and ! is_object( $id_or_email ) ) {

				$email = $id_or_email; } elseif ( ! is_numeric( $id_or_email ) and is_object( $id_or_email ))

			$email = $id_or_email->comment_author_email;

		}

		if ( $email != '' ) {

			$hash = md5( strtolower( trim( $email ) ) );

			// check if gravatar exists for hashtag using options

			if ( is_array( $wpua_hash_gravatar ) ) {





				if ( array_key_exists( $hash, $wpua_hash_gravatar ) and is_array( $wpua_hash_gravatar[$hash] ) and array_key_exists( date( 'm-d-Y' ), $wpua_hash_gravatar[$hash] ) )

				{

					return (bool) $wpua_hash_gravatar[$hash][date( 'm-d-Y' )];

				}



			}



			// end

			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { 
				$http='https';
			}else{
				$http='http';
			}
			$gravatar = $http.'://www.gravatar.com/avatar/'.$hash.'?d=404';



			$data = wp_cache_get( $hash );



			if ( false === $data ) {

				$response = wp_remote_head( $gravatar );

				$data = is_wp_error( $response ) ? 'not200' : $response['response']['code'];



				wp_cache_set( $hash, $data, $group = '', $expire = 60 * 5 );

				// here set if hashtag has avatar

				$has_gravatar = ($data == '200') ? true : false;

				if ( $wpua_hash_gravatar == false ) {

					$wpua_hash_gravatar[$hash][date( 'm-d-Y' )] = (bool) $has_gravatar;

					add_option( 'wpua_hash_gravatar',serialize( $wpua_hash_gravatar ) );

				} else {



					if ( array_key_exists( $hash, $wpua_hash_gravatar ) ) {



						unset( $wpua_hash_gravatar[$hash] );

						$wpua_hash_gravatar[$hash][date( 'm-d-Y' )] = (bool) $has_gravatar;

						update_option( 'wpua_hash_gravatar',serialize( $wpua_hash_gravatar ) );





					} else {

						$wpua_hash_gravatar[$hash][date( 'm-d-Y' )] = (bool) $has_gravatar;

						update_option( 'wpua_hash_gravatar',serialize( $wpua_hash_gravatar ) );



					}



				}

				// end

			}

			$has_gravatar = ($data == '200') ? true : false;



		} else { 	  $has_gravatar = false; }



		// Check if Gravatar image returns 200 (OK) or 404 (Not Found)

		return (bool) $has_gravatar;

	}



	/**

	 * Returns true if user has Gravatar-hosted image

	 * @param int $user_id

	 * @uses get_user_meta()

	 */

	public function get_wpua_has_gravatar($id_or_email) {

		if ( ! is_object( $id_or_email ) && ! empty( $id_or_email ) ) {

			// Find user by ID or e-mail address

			$user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );

			// Get registered user e-mail address

			$user_id = ! empty( $user ) ? $user->ID : '';

		}

		$wpua_has_gravatar = maybe_unserialize( get_option( 'wpua_has_gravatar' ) );

		$user_has_gravatar = '';

		if ( is_array( $wpua_has_gravatar ) ) {

			if ( array_key_exists( $user_id, $wpua_has_gravatar ) ) {

				$user_has_gravatar = $wpua_has_gravatar[$user_id]; }

		}

		return (bool) $user_has_gravatar;

	}





	/**

	 * Check if local image

	 * @since 1.9.2

	 * @param int $attachment_id

	 * @uses apply_filters()

	 * @uses wp_attachment_is_image()

	 * @return bool

	 */

	public function wpua_attachment_is_image($attachment_id) {

		$is_image = wp_attachment_is_image( $attachment_id );

		/**

	 * Filter local image check

	 * @since 1.9.2

	 * @param bool $is_image

	 * @param int $attachment_id

	 */

		$is_image = apply_filters( 'wpua_attachment_is_image', $is_image, $attachment_id );

		return (bool) $is_image;

	}



	/**

	 * Get local image tag

	 * @since 1.9.2

	 * @param int        $attachment_id

	 * @param int|string $size

	 * @param bool       $icon

	 * @param string     $attr

	 * @uses apply_filters()

	 * @uses wp_get_attachment_image()

	 * @return string

	 */

	public function wpua_get_attachment_image($attachment_id, $size='thumbnail', $icon=0, $attr='') {

		$image = wp_get_attachment_image( $attachment_id, $size, $icon, $attr );

		/**

	 * Filter local image tag

	 * @since 1.9.2

	 * @param string $image

	 * @param int $attachment_id

	 * @param int|string $size

	 * @param bool $icon

	 * @param string $attr

	 */

		return apply_filters( 'wpua_get_attachment_image', $image, $attachment_id, $size, $icon, $attr );

	}



	/**

	 * Get local image src

	 * @since 1.9.2

	 * @param int        $attachment_id

	 * @param int|string $size

	 * @param bool       $icon

	 * @uses apply_filters()

	 * @uses wp_get_attachment_image_src()

	 * @return array

	 */

	public function wpua_get_attachment_image_src($attachment_id, $size='thumbnail', $icon=0) {

		$image_src_array = wp_get_attachment_image_src( $attachment_id, $size, $icon );

		/**

	 * Filter local image src

	 * @since 1.9.2

	 * @param array $image_src_array

	 * @param int $attachment_id

	 * @param int|string $size

	 * @param bool $icon

	 */

		return apply_filters( 'wpua_get_attachment_image_src', $image_src_array, $attachment_id, $size, $icon );

	}



	/**

	 * Returns true if user has wp_user_avatar

	 * @since 1.1

	 * @param int|string $id_or_email

	 * @param bool       $has_wpua

	 * @param object     $user

	 * @param int        $user_id

	 * @uses int $blog_id

	 * @uses object $wpdb

	 * @uses int $wpua_avatar_default

	 * @uses object $wpua_functions

	 * @uses get_user_by()

	 * @uses get_user_meta()

	 * @uses get_blog_prefix()

	 * @uses wpua_attachment_is_image()

	 * @return bool

	 */



	public function has_wp_user_avatar($id_or_email='', $has_wpua=0, $user='', $user_id='') {

		global $blog_id, $wpdb, $wpua_avatar_default, $wpua_functions, $wpua_upload_dir;



		if ( ! is_object( $id_or_email ) && ! empty( $id_or_email ) ) {

			// Find user by ID or e-mail address

			$user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );

			// Get registered user ID

			$user_id = ! empty( $user ) ? $user->ID : '';

		}



		$wpua = get_user_meta( $user_id, $wpdb->get_blog_prefix( wpua_get_main_blog_id() ).'user_avatar', true );

		

		if ( $wpua_functions->wpua_attachment_is_image( $wpua ) ) {

			return true; }



		$arrs = maybe_unserialize( $wpua );

		$arr = isset($arrs[0]) ? $arrs[0] : array();

		

		if ( isset( $arr['avatar_url'] ) ) {

			if ( isset( $arr['type'] ) && in_array( $arr['type'], array( 'media', 'directory' ) ) ) {

				if ( $arr['type'] == 'media' &&  empty( $arr['resource'] )  ) {

					return false; }

				if ( $arr['type'] == 'directory' ) {

					$filepath = ABSPATH.trailingslashit( ltrim( $arr['resource'], '/' ) ).$arr['avatar_filename'];

					if ( ! file_exists( $filepath ) ) {

						return false; }

				}

			}



			return true;

		}



		return false;

	}

	/**

	Retrive default image url set by admin.

	 */

	public function wpua_default_image($size) {



		global $avatar_default, $mustache_admin, $mustache_avatar, $mustache_medium, $mustache_original, $mustache_thumbnail, $post, $wpua_avatar_default, $wpua_disable_gravatar, $wpua_functions;



		$default_image_details = array();

		// Show custom Default Avatar

		if ( ! empty( $wpua_avatar_default ) && $wpua_functions->wpua_attachment_is_image( $wpua_avatar_default ) ) {

			// Get image

			$wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src( $wpua_avatar_default, array( $size, $size ) );

			// Image src

			$default = $wpua_avatar_default_image[0];

			// Add dimensions if numeric size

			$default_image_details['dimensions'] = ' width="'.$wpua_avatar_default_image[1].'" height="'.$wpua_avatar_default_image[2].'"';



		} else {

			// Get mustache image based on numeric size comparison

			if ( $size > get_option( 'medium_size_w' ) ) {

				$default = $mustache_original;

			} elseif ( $size <= get_option( 'medium_size_w' ) && $size > get_option( 'thumbnail_size_w' ) ) {

				$default = $mustache_medium;

			} elseif ( $size <= get_option( 'thumbnail_size_w' ) && $size > 96 ) {

				$default = $mustache_thumbnail;

			} elseif ( $size <= 96 && $size > 32 ) {

				$default = $mustache_avatar;

			} elseif ( $size <= 32 ) {

				$default = $mustache_admin;

			}

			// Add dimensions if numeric size

			$default_image_details['dimensions'] = ' width="'.$size.'" height="'.$size.'"';

		}

		// Construct the img tag

		$default_image_details['size'] = $size;

		$default_image_details['src'] = $default;

		 return $default_image_details;



	}

	/**

	 * Replace get_avatar only in get_wp_user_avatar

	 * @since 1.4

	 * @param string     $avatar

	 * @param int|string $id_or_email

	 * @param int|string $size

	 * @param string     $default

	 * @param string     $alt

	 * @uses string $avatar_default

	 * @uses string $mustache_admin

	 * @uses string $mustache_avatar

	 * @uses string $mustache_medium

	 * @uses string $mustache_original

	 * @uses string $mustache_thumbnail

	 * @uses object $post

	 * @uses int $wpua_avatar_default

	 * @uses bool $wpua_disable_gravatar

	 * @uses object $wpua_functions

	 * @uses apply_filters()

	 * @uses get_wp_user_avatar()

	 * @uses has_wp_user_avatar()

	 * @uses wpua_has_gravatar()

	 * @uses wpua_attachment_is_image()

	 * @uses wpua_get_attachment_image_src()

	 * @uses get_option()

	 * @return string $avatar

	 */

	public function wpua_get_avatar_filter($avatar, $id_or_email='', $size='', $default='', $alt='') {

		global $avatar_default, $wpua_upload_url, $wpua_upload_dir,$wp_user_avatar_thumbnail_w,$wp_user_avatar_thumbnail_h, $mustache_admin, $mustache_avatar, $mustache_medium, $mustache_original, $mustache_thumbnail, $post, $wpua_avatar_default, $wpua_disable_gravatar, $wpua_functions;

		

		//Role Based Default Avatar	
		if ( ! is_object( $id_or_email ) && ! empty( $id_or_email ) ) {

			// Find user by ID or e-mail address

			$user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );

			// Get registered user e-mail address

			$uid = ! empty( $user ) ? $user->id : '';
			$this->set_default_avatar( $uid );

		}
				
		// User has WPUA

		$avatar = str_replace( 'gravatar_default','',$avatar );
		$avatar_defaults_options = array(
		'mystery',
		// 'blank',
		'gravatar_default',
		'identicon',
		'wavatar',
		'monsterid',
		'retro',
		// 'letter_based',
		);

		if ( is_object( $id_or_email ) ) {

			

			if ( ! empty( $id_or_email->comment_author_email ) ) {

				$avatar = get_wp_user_avatar( $id_or_email, $size, $default, $alt );

			} else {

				$avatar = get_wp_user_avatar( 'unknown@gravatar.com', $size, $default, $alt );

			}

			

		} else {



			if ( has_wp_user_avatar( $id_or_email ) ) {

				$avatar = get_wp_user_avatar( $id_or_email, $size, $default, $alt );

				// User has Gravatar and Gravatar is not disabled

			} elseif ( (bool) $wpua_disable_gravatar != 1 && $wpua_functions->wpua_has_gravatar( $id_or_email ) ) {

				$avatar = $avatar;

				// User doesn't have WPUA or Gravatar and Default Avatar is wp_user_avatar, show custom Default Avatar

			} elseif ( $avatar_default == 'wp_user_avatar' || $avatar_default == 'wp_user_avatar_custom_url' ) {

				$avatar_default = 'wp_user_avatar';

				if ( $avatar_default == 'wp_user_avatar'  ) {



					if ( ! empty( $wpua_avatar_default ) && $wpua_functions->wpua_attachment_is_image( $wpua_avatar_default ) ) {

						// Get image

						$wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src( $wpua_avatar_default, array( $size, $size ) );

						// Image src

						$default = $wpua_avatar_default_image[0];

						// Add dimensions if numeric size

						$dimensions = ' width="'.$wpua_avatar_default_image[1].'" height="'.$wpua_avatar_default_image[2].'"';

					} else if ( ! empty( $wpua_avatar_default ) && @file_exists( $wpua_upload_dir.$wpua_avatar_default ) ) {

						$default = $wpua_upload_url.$wpua_avatar_default;

						$dimensions = ' width="'.$size.'" height="'.$size.'"';

					} else {

						// Get mustache image based on numeric size comparison

						if ( $size > get_option( 'medium_size_w' ) ) {

							$default = $mustache_original;

						} elseif ( $size <= get_option( 'medium_size_w' ) && $size > get_option( 'thumbnail_size_w' ) ) {

							$default = $mustache_medium;

						} elseif ( $size <= get_option( 'thumbnail_size_w' ) && $size > 96 ) {

							$default = $mustache_thumbnail;

						} elseif ( $size <= 96 && $size > 32 ) {

							$default = $mustache_avatar;

						} elseif ( $size <= 32 ) {

							$default = $mustache_admin;

						}

						// Add dimensions if numeric size

						$dimensions = ' width="'.$size.'" height="'.$size.'"';

					}

				}

				//wp_print($default);

				// Construct the img tag

				$avatar = '<img src="'.$default.'"'.$dimensions.' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.' photo avatar-default" />';

				

			} elseif ( in_array($avatar_default, $avatar_defaults_options) ) {
				$avatar_obj = new WPUA_Avatar();
				$data = $avatar_obj->avatar_settings();								
				$user_role = $user->roles[0];
				if ( isset($data['role_based_avatar'][$user_role]) && ! empty($data['role_based_avatar'][$user_role])) {
					$data['default_avatar_url'] = $data['wpua_upload_url'].$data['role_based_avatar'][$user_role];
					$avatar = '<img src="'.$data['default_avatar_url'].'"'.$dimensions.' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.' photo avatar-default" />';
				}				
			} elseif ( $avatar_default == 'letter_based' ) {
										$dimensions = ' width="'.$size.'" height="'.$size.'"';

				$user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );
				$char = strtolower(substr($user->data->display_name,0,1));
				$avatar = '<img src="'.WPUAP_IMAGES.'/alphabets/'.$char.'-'.rand(1,4).'.png"'.$dimensions.' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.' photo avatar-default" />';
				
			}

		}

		/**

	 * Filter get_avatar filter

	 * @since 1.9

	 * @param string $avatar

	 * @param int|string $id_or_email

	 * @param int|string $size

	 * @param string $default

	 * @param string $alt

	 */

		return apply_filters( 'wpua_get_avatar_filter', $avatar, $id_or_email, $size, $default, $alt );

	}



	/**

	 * Find WPUA, show get_avatar if empty

	 * @since 1.0

	 * @param int|string $id_or_email

	 * @param int|string $size

	 * @param string     $align

	 * @param string     $alt

	 * @uses array $_wp_additional_image_sizes

	 * @uses array $all_sizes

	 * @uses string $avatar_default

	 * @uses int $blog_id

	 * @uses object $post

	 * @uses object $wpdb

	 * @uses int $wpua_avatar_default

	 * @uses object $wpua_functions

	 * @uses apply_filters()

	 * @uses get_the_author_meta()

	 * @uses get_blog_prefix()

	 * @uses get_user_by()

	 * @uses get_query_var()

	 * @uses is_author()

	 * @uses wpua_attachment_is_image()

	 * @uses wpua_get_attachment_image_src()

	 * @uses get_option()

	 * @uses get_avatar()

	 * @return string $avatar

	 */

	public function get_wp_user_avatar($id_or_email='', $size='96', $align='', $alt='') {

		global $all_sizes, $avatar_default, $blog_id, $post, $wpdb, $wpua_avatar_default, $wpua_functions, $_wp_additional_image_sizes, $wpua_upload_url, $wpua_upload_dir, $wp_user_avatar_thumbnail_w, $wp_user_avatar_thumbnail_h;

		$email = 'unknown@gravatar.com';

		$all_sizes = array_merge( get_intermediate_image_sizes(), array( 'original' ) );

		// Checks if comment

		if ( is_object( $id_or_email ) ) {

			// Checks if comment author is registered user by user ID

			if ( $id_or_email->user_id != 0 ) {

				$email = $id_or_email->user_id;

				// Checks that comment author isn't anonymous

			} elseif ( ! empty( $id_or_email->comment_author_email ) ) {

				// Checks if comment author is registered user by e-mail address

				$user = get_user_by( 'email', $id_or_email->comment_author_email );

				// Get registered user info from profile, otherwise e-mail address should be value

				$email = ! empty( $user ) ? $user->ID : $id_or_email->comment_author_email;

			}

			$alt = $id_or_email->comment_author;

		} else {

			if ( ! empty( $id_or_email ) ) {

				// Find user by ID or e-mail address

				$user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'email', $id_or_email );

			} else {

				// Find author's name if id_or_email is empty

				$author_name = get_query_var( 'author_name' );

				if ( is_author() ) {

					// On author page, get user by page slug

					$user = get_user_by( 'slug', $author_name );

				} else {

					// On post, get user by author meta

					$user_id = get_the_author_meta( 'ID' );

					$user = get_user_by( 'id', $user_id );

				}

			}

			// Set user's ID and name

			if ( ! empty( $user ) ) {

				$email = $user->ID;

				$alt = $user->display_name;

			}

		}

		// Checks if user has WPUA

		$wpua_meta_serialized = get_the_author_meta( $wpdb->get_blog_prefix( wpua_get_main_blog_id() ).'user_avatar', $email );

		$wpua_meta_unserialized = maybe_unserialize($wpua_meta_serialized);

		if(is_array($wpua_meta_unserialized)) {

			$wpua_meta = $wpua_meta_unserialized[rand(0,count($wpua_meta_unserialized)-1)];

		} else {

			$wpua_meta = $wpua_meta_unserialized;

		}

		

		// Add alignment class

		$alignclass = ! empty( $align ) && ($align == 'left' || $align == 'right' || $align == 'center') ? ' align'.$align : ' alignnone';

		// User has WPUA, check if on excluded list and bypass get_avatar

		if ( ! empty( $wpua_meta ) && $wpua_functions->wpua_attachment_is_image( $wpua_meta ) ) {

			// Numeric size use size array

			$get_size = is_numeric( $size ) ? array( $size, $size ) : $size;

			// Get image src

			$wpua_image = $wpua_functions->wpua_get_attachment_image_src( $wpua_meta, $get_size );

			// Add dimensions to img only if numeric size was specified

			$dimensions = is_numeric( $size ) ? ' width="'.$wpua_image[1].'" height="'.$wpua_image[2].'"' : '';

			// Construct the img tag

			$avatar = '<img src="'.$wpua_image[0].'"'.$dimensions.' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.$alignclass.' photo" />';

		} else {

			$wpua_meta = maybe_unserialize( $wpua_meta );

			if ( isset( $wpua_meta['avatar_url'] ) && ! empty( $wpua_meta['avatar_url'] ) ) {

				$dimensions = is_numeric( $size ) ? ' width="'.$size.'" height="'.$size.'"' : '';

				$src = $wpua_meta['avatar_url'];

				

				if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { 

					$src = str_replace('http://', '', $src);

					$src = str_replace('https://', '', $src);

					$src = 'https://'.$src;

				}

				$alt = apply_filters('wpua_img_alt',$alt,$id_or_email);

				$avatar = '<img src="'.$src.'"'.$dimensions.' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.$alignclass.' photo" />';

			} else {

				// Check for custom image sizes

				if ( in_array( $size, $all_sizes ) ) {

					if ( in_array( $size, array( 'original', 'large', 'medium', 'thumbnail' ) ) ) {

						$get_size = ($size == 'original') ? get_option( 'large_size_w' ) : get_option( $size.'_size_w' );

					} else {

						$get_size = $_wp_additional_image_sizes[$size]['width'];

					}

				} else {

					// Numeric sizes leave as-is

					$get_size = $size;

				}

				// User with no WPUA uses get_avatar

				$avatar = get_avatar( $email, $get_size, $default = '', $alt );

				if ( in_array( $size, array( 'original', 'large', 'medium', 'thumbnail' ) ) ) {

					$avatar = preg_replace( '/(width|height)=\"\d*\"\s/', '', $avatar );

					$avatar = preg_replace( "/(width|height)=\'\d*\'\s/", '', $avatar );

				}

				$replace = array( 'wp-user-avatar ', 'wp-user-avatar-'.$get_size.' ', 'wp-user-avatar-'.$size.' ', 'avatar-'.$get_size, 'photo' );

				$replacements = array( '', '', '', 'avatar-'.$size, 'wp-user-avatar wp-user-avatar-'.$size.$alignclass.' photo' );

				$avatar = str_replace( $replace, $replacements, $avatar );

			}

			// Remove width and height for non-numeric sizes

		}

		/**

	 * Filter get_wp_user_avatar

	 * @since 1.9

	 * @param string $avatar

	 * @param int|string $id_or_email

	 * @param int|string $size

	 * @param string $align

	 * @param string $alt

	 */

		return apply_filters( 'get_wp_user_avatar', $avatar, $id_or_email, $size, $align, $alt );

	}



	/**

	 * Return just the image src

	 * @since 1.1

	 * @param int|string $id_or_email

	 * @param int|string $size

	 * @param string     $align

	 * @uses get_wp_user_avatar()

	 * @return string

	 */

	public function get_wp_user_avatar_src($id_or_email='', $size='', $align='') {

		$wpua_image_src = '';

		// Gets the avatar img tag

		$wpua_image = get_wp_user_avatar( $id_or_email, $size, $align );

		// Takes the img tag, extracts the src

		if ( ! empty( $wpua_image ) ) {

			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $wpua_image, $matches, PREG_SET_ORDER );

			$wpua_image_src = ! empty( $matches ) ? $matches [0] [1] : '';

		}

		return $wpua_image_src;

	}



	/**

	 * Get default avatar

	 * @since 1.4

	 * @param int|string $id_or_email

	 * @param int|string $size

	 * @param string     $default

	 * @param string     $alt

	 * @uses string $avatar_default

	 * @uses string $mustache_avatar

	 * @uses int $wpua_avatar_default

	 * @uses object $wpua_functions

	 * @uses wpua_attachment_is_image()

	 * @uses wpua_get_attachment_image_src()

	 * @uses add_filter()

	 * @uses apply_filters()

	 * @uses get_avatar()

	 * @uses remove_filter()

	 * @return string $default

	 */

	public function get_wpua_default_avatar($id_or_email='', $size='', $default='', $alt='') {

		global $avatar_default,$wpua_upload_url, $mustache_avatar, $wpua_avatar_default, $wpua_disable_gravatar, $wpua_functions, $wpua_upload_dir;

		// Remove get_avatar filter

		remove_filter( 'get_avatar', array( $wpua_functions, 'wpua_get_avatar_filter' ) );

		if ( ! empty( $wpua_avatar_default ) && ( $avatar_default == 'wp_user_avatar' || $avatar_default == 'wp_user_avatar_custom_url') ) {

			if ( $wpua_functions->wpua_attachment_is_image( $wpua_avatar_default ) ) {

				$wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src( $wpua_avatar_default, array( $size, $size ) );

				$default = $wpua_avatar_default_image[0];

			}  else if ( is_file( $wpua_upload_dir.$wpua_avatar_default ) ) {

				$default = $wpua_upload_url.$wpua_avatar_default;

			} else {

				$default = $mustache_avatar;

			}

		} elseif ( (bool) $wpua_disable_gravatar != 1 ) {



			if ( empty( $avatar_default ) ) {

				$avatar_default = 'mystery';

			}



			$wpua_image = get_avatar( 'unknown@gravatar.com', 96, $avatar_default );



			// Takes the img tag, extracts the src

			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $wpua_image, $matches, PREG_SET_ORDER );

			$default = ! empty( $matches ) ? $matches [0] [1] : '';

		} else {

			$default = $mustache_avatar;

		}

		// Enable get_avatar filter

		add_filter( 'get_avatar', array( $wpua_functions, 'wpua_get_avatar_filter' ), 10, 5 );

		/**

	 * Filter original avatar src

	 * @since 1.9

	 * @param string $default

	 */

		return apply_filters( 'wpua_get_avatar_original', $default );

	}

}



/**

 * Initialize

 * @since 1.9.2

 */

function wpua_functions_init() {

	global $wpua_functions;

	$wpua_functions = new WP_User_Avatar_Functions();

}

add_action( 'plugins_loaded', 'wpua_functions_init' );

