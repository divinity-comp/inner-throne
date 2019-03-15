<?php
/**
 * Class WPUA_Avatar File
 * @package Avatar
 * @author Flipper Code <hello@flippercode.com>
 */

if ( ! class_exists( 'WPUA_Avatar' ) ) {

	/**
	 * Display Avatar, Edit Avatar and Save Avatar Class.
	 * @package Avatar
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPUA_Avatar {
		/**
		 * Initialize Class
		 */
		public function __construct() {

		}
		/**
		 * Retrive Avatar Settings.
		 * @return array Avatar Settings.
		 */
		public static function avatar_settings() {
			$data = array();
			$data['avatar_default'] = get_option( 'avatar_default' );
			// Attachment ID of default avatar.
			$data['role_based_avatar'] = unserialize(get_option( 'role_based_avatar' ));
			$uid = isset($_GET['user_id']) ? $_GET['user_id'] : get_current_user_id();
			$users = new WP_User( $uid );
			$current_page = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
			$data['avatar_default_wp_user_avatar'] = 
				isset($data['role_based_avatar'][ $users->roles[0] ]) 
				&& ! empty($data['role_based_avatar'][ $users->roles[0] ]) 
				&& ($_GET['page'] != 'wpuap_manage_settings') 
				&& $current_page != 'user-new.php'
				? $data['role_based_avatar'][ $users->roles[0] ] 
				: get_option( 'avatar_default_wp_user_avatar' );

			// Default avatar 512x512.
			$mustache_original = WPUAP_IMAGES.'wpua.png';
			// Default avatar 300x300.
			$mustache_medium = WPUAP_IMAGES.'wpua-300x300.png';
			// Default avatar 150x150.
			$mustache_thumbnail = WPUAP_IMAGES.'wpua-150x150.png';
			// Default avatar 96x96.
			$mustache_avatar = WPUAP_IMAGES.'wpua-96x96.png';
			// Default avatar 32x32.
			$mustache_admin = WPUAP_IMAGES.'wpua-32x32.png';
			// Avatar Upload Directory.
			$upload_dir = wp_upload_dir();

			if ( is_multisite() ) {
				$pos = strpos( $upload_dir['basedir'],'uploads' );
				$wpua_upload_dir = substr( $upload_dir['basedir'],0,($pos + 7) ).'/wp-user-avatar/';
				$pos_url = strpos( $upload_dir['baseurl'],'uploads' );
				$wpua_upload_url = substr( $upload_dir['baseurl'],0,($pos_url + 8) ).'/wp-user-avatar/';
			} else {
				$wpua_upload_dir = $upload_dir['basedir'].'/wp-user-avatar/';
				$wpua_upload_url = $upload_dir['baseurl'].'/wp-user-avatar/';
			}
			$data['wpua_upload_dir'] = $wpua_upload_dir;
			$data['wpua_upload_url'] = $wpua_upload_url;

			if ( ! empty( $data['avatar_default_wp_user_avatar'] ) ) {
				if ( wp_attachment_is_image( $data['avatar_default_wp_user_avatar'] ) ) {
					$avatar_thumb_src = wp_get_attachment_image_src( $data['avatar_default_wp_user_avatar'], array( 32, 32 ) );
					$avatar_thumb = $avatar_thumb_src[0];
				} else if ( file_exists( $wpua_upload_dir.$data['avatar_default_wp_user_avatar'] ) ) {
					$avatar_thumb = $wpua_upload_url.$data['avatar_default_wp_user_avatar'];
				} else {
					$avatar_thumb = $mustache_admin;
				}
			} else {
				$avatar_thumb = $mustache_thumbnail;
			}
			$data['default_avatar_url'] = $avatar_thumb;
			// Avatar media uploader.
			$data['avatar_storage_option'] = get_option( 'avatar_storage_option','media' );
			$data['wp_user_avatar_storage'] = get_option( 'wp_user_avatar_storage' );
			// Booleans.
			$data['show_avatars'] = get_option( 'show_avatars' );
			$data['wp_user_avatar_upload_registration'] = get_option( 'wp_user_avatar_upload_registration' );

			// Avatar dimensions.
			$data['wp_user_avatar_thumbnail_w'] = get_option( 'wp_user_avatar_thumbnail_w' );
			$data['wp_user_avatar_thumbnail_h'] = get_option( 'wp_user_avatar_thumbnail_h' );

			$data['wp_user_avatar_disable_gravatar'] = get_option( 'wp_user_avatar_disable_gravatar' );

			// Avatar dimensions.
			$data['wp_user_avatar_hide_mediamanager'] = get_option( 'wp_user_avatar_hide_mediamanager' );
			$data['wp_user_avatar_hide_webcam'] = get_option( 'wp_user_avatar_hide_webcam' );

			// Check for updates.
			$data['wp_user_avatar_default_avatar_updated'] = get_option( 'wp_user_avatar_default_avatar_updated' );

			$data['wp_user_avatar_resize_upload'] = get_option( 'wp_user_avatar_resize_upload' );

			$data['wp_user_avatar_upload_size_limit'] = get_option( 'wp_user_avatar_upload_size_limit' );
			$data['wp_user_avatar_upload_size_width'] = (int)get_option( 'wp_user_avatar_upload_size_width' );
			$data['wp_user_avatar_upload_size_height'] = (int)get_option( 'wp_user_avatar_upload_size_height' );

			$data['avatar_default'] = get_option( 'avatar_default' );
			$data['avatar_rating'] = get_option( 'avatar_rating' );
			$data['wp_user_avatar_settings'] = get_option( 'wp_user_avatar_settings' );
			$data['role_based_avatar'] = unserialize(get_option( 'role_based_avatar' ));
			$data['wp_user_avatar_url'] = $data['default_avatar_url'];
			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { 
				$http='https';
				$data['default_avatar_url'] = str_replace('http://', 'https://', $data['default_avatar_url']);
				if(! empty($data['role_based_avatar'])){
					foreach($data['role_based_avatar'] as $role=>$role_avatar){
						$data['role_based_avatar'][$role] = str_replace('http://', 'https://', $data['role_based_avatar'][$role]);
					}
				}
			}else{
				$http='http';
			}
			$data['mystery_url'] = $http.'://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32&d=mm&r=g&forcedefault=1';
			$data['gravatar_default_url'] = $http.'://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32&r=g&forcedefault=1';
			$data['identicon_url'] = $http.'://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32&d=identicon&r=g&forcedefault=1';
			$data['wavatar_url'] = $http.'://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32&d=wavatar&r=g&forcedefault=1';
			$data['monsterid_url'] = $http.'://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32&d=monsterid&r=g&forcedefault=1';
			$data['retro_url'] = $http.'://1.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=32&d=retro&r=g&forcedefault=1';
			
			return $data;
		}

		/**
		 *
		 */
		public static function wpua_theme_setting() {
			global $pagenow;
			$avatar_obj = new WPUA_Avatar();
			$data = $avatar_obj->avatar_settings();
			//echo '<pre>'; print_r($data);
			if ( isset( $data['wp_user_avatar_settings'] ) ) {
				$setting = $data['wp_user_avatar_settings'];
				
				if(isset($setting['apply_predefined_design']) and $setting['apply_predefined_design'] == 'true'){
					$color = $setting['color_schema'];
				}else{
					$color = isset($setting['theme_color']) ? $setting['theme_color'] : '';
					
				}
				
				$tooltip_bg = isset($setting['tooltip_background']) ? $setting['tooltip_background'] : '';
				$overlay_opacity = isset($setting['overlay_opacity'])? $setting['overlay_opacity'] : '';
				$overlay_color = isset($setting['overlay_color']) ? $setting['overlay_color'] : '';
				$overlay_bgcolor = isset($setting['overlay_background']) ? $setting['overlay_background'] : '';
				$tooltip_color = isset($setting['tooltip_color']) ? $setting['tooltip_color'] : '' ;
				$tooltip_border = isset($setting['tooltip_border']) ? $setting['tooltip_border'] : '';
				$hex = str_replace( '#', '', $overlay_bgcolor );
				if ( strlen( $hex ) == 3 ) {
					$r = hexdec( substr( $hex,0,1 ).substr( $hex,0,1 ) );
					$g = hexdec( substr( $hex,1,1 ).substr( $hex,1,1 ) );
					$b = hexdec( substr( $hex,2,1 ).substr( $hex,2,1 ) );
				} else {
					$r = hexdec( substr( $hex,0,2 ) );
					$g = hexdec( substr( $hex,2,2 ) );
					$b = hexdec( substr( $hex,4,2 ) );
				}
					$rgb = array( $r, $g, $b );
			} else {
				$color = '#0073AA';
			}
			?>
			<style>
			.avatar_overlays p  {
 				background: rgba(<?php echo $r; ?>, <?php echo $g; ?>, <?php echo $b; ?>, <?php echo $overlay_opacity; ?>);
 				color: <?php echo $overlay_color; ?>;
				}
			.wpuap_tooltip:hover .wpuap_tooltip_content {
		    display: inline;
		    position: absolute;
		    color: <?php echo $tooltip_border; ?>;
		    border: 1px solid <?php echo $tooltip_bg; ?>;
		    background: <?php echo $tooltip_bg; ?>
			}

			.avatar_container [class^=icon-],
			.avatar_container [class*=" icon-"] {
			    color: <?php echo $color; ?> !important;
			}

			#ci-modal,.ci_controls {
			    background-color: <?php echo $color; ?> !important;
			}
			<?php if( $pagenow == 'wp-login.php'): ?>
			 .wpua_extra_avatars{
				 text-align:center;
			 }
			 .avatar_container{
				 margin-bottom:20px;
			 }
			 .avatar_container img{
				width:100%;
			 }
			<?php endif; ?>
			</style>
		<?php
		}
		/**
		 * Retrive User's avatar
		 * @param  int $user_id User ID.
		 * @return string          Avatar URL.
		 */
		public function wpua_get_avatar_thumb($user_id) {
			global $wpdb, $blog_id;
			
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id(); }
			// delete_user_meta($user_id,$wpdb->get_blog_prefix( $blog_id ).'user_avatar');
			$avatar_meta = get_user_meta( $user_id, $wpdb->get_blog_prefix( $blog_id ).'user_avatar', true );
			$all_avatars = $avatar_meta;
			if ( is_array( $all_avatars ) ) {
				return $all_avatars;
			}
		}
		/**
		 * Display Avatar with Edit/Delete Icons.
		 * @param  object $user User Object.
		 */
		public static function wpua_avatar_html($user) {

			global $wpdb, $blog_id, $wpua_functions;
			$avatar_obj = new WPUA_Avatar();
			$data = $avatar_obj->avatar_settings();
			$all_avatar_thumbnails = $avatar_obj->wpua_get_avatar_thumb( $user->ID );
			$is_admin = is_admin() ? '_admin' : '';
			$user_role = $user->roles[0];
			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { 
				$http='https';
			}else{
				$http='http';
			}
			if( empty($data['wp_user_avatar_disable_gravatar']) && $wpua_functions->wpua_has_gravatar($user->ID) && ! has_wp_user_avatar($user->ID) ){
			  if( $default_gravatar = get_wp_user_avatar_src($user->ID, '150'))	
			  $data['default_avatar_url'] = $default_gravatar;
			} else if ( $data['avatar_default'] == 'wp_user_avatar' and isset($data['role_based_avatar'][$user_role]) && ! empty($data['role_based_avatar'][$user_role])) {
					$data['default_avatar_url'] = $data['wpua_upload_url'].$data['role_based_avatar'][$user_role];
			} else if ( $data['avatar_default'] == 'letter_based' ) {
		
				$char = strtolower(substr($user->data->display_name,0,1));
				$data['default_avatar_url'] = WPUAP_IMAGES.'/alphabets/'.$char.'-'.rand(1,4).'.png';
				
			} else{											
				$gravatar_base_url = $http.'://1.gravatar.com/avatar/'.md5( strtolower( trim( $user->data->user_email ) ) ).'?';
				$data['wp_user_avatar_url'] = $data['default_avatar_url'];
				$data['mystery_url'] = $gravatar_base_url.'s=150&d=mm&r=g';
				$data['gravatar_default_url'] = $gravatar_base_url.'s=150&r=g';
				$data['identicon_url'] = $gravatar_base_url.'s=150&d=identicon&r=g';
				$data['wavatar_url'] = $gravatar_base_url.'s=150&d=wavatar&r=g';
				$data['monsterid_url'] = $gravatar_base_url.'s=150&d=monsterid&r=g';
				$data['retro_url'] = $gravatar_base_url.'s=150&d=retro&r=g';
				$data['default_avatar_url'] = isset($data[ $data['avatar_default'].'_url' ]) ?  $data[ $data['avatar_default'].'_url' ] : '';
			}
			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { 
					$data['default_avatar_url'] = str_replace('http://', '', $data['default_avatar_url']);
					$data['default_avatar_url'] = str_replace('https://', '', $data['default_avatar_url']);
					$data['default_avatar_url'] = 'https://'.$data['default_avatar_url'];
				}
		?>
		<?php do_action( 'wpua_before_avatar'.$is_admin ); ?>
	    <?php wp_nonce_field( 'no_action', 'wpua_avatar' ); ?>
        <input type="hidden" name="default_avatar" id="default_avatar" value="<?php echo $data['default_avatar_url'];?>" />
        <input type="hidden" name="wp-user-avatar-url" id="wp-user-avatar-url" value="" />
        <div id="wpua-add-button" class="avatar-container">
        <?php
		if ( is_array( $all_avatar_thumbnails ) && count($all_avatar_thumbnails) > 1 )
		$all_avatar_thumbnails = array($all_avatar_thumbnails[0]);

        if ( is_array( $all_avatar_thumbnails ) ) {
            foreach ( $all_avatar_thumbnails as $avatar ) {  
			?>
              <img src="<?php echo ( '' != $avatar['avatar_url'] ) ? $avatar['avatar_url'] : $data['default_avatar_url'] ; ?>" alt="" id="wp-user-avatar-img" class="ci_choose_image" />
            <?php
            }
        }?>
        </div>
        <div class='wpua_extra_avatars' >
        <?php 
        
        $total_thumbnail =  1;
        for ( $count = 0; $count < $total_thumbnail - count( $all_avatar_thumbnails ); $count++ ) { ?>
            <img src="<?php echo $data['default_avatar_url'] ; ?>" alt="" id="wp-user-avatar-img-2" class="ci_choose_image" />
        <?php }  ?>
        </div>
        <br>
        <?php do_action( 'wpua_after_avatar'.$is_admin ); ?>

	<?php
		}

		/**
		 * Display Avatar Upload for new User Register.
		 */
		public function wpua_avatar_form_new() {
			global $wpdb, $blog_id;
			$avatar_obj = new WPUA_Avatar();
			$data = $avatar_obj->avatar_settings();
			$is_admin = is_admin() ? '_admin' : '';
		?>
		<?php do_action( 'wpua_before_avatar'.$is_admin ); ?>
	    <?php wp_nonce_field( 'no_action', 'wpua_avatar' ); ?>
        <input type="hidden" name="default_avatar" id="default_avatar" value="<?php echo $data['default_avatar_url'];?>" />
        <input type="hidden" name="wp-user-avatar-url" id="wp-user-avatar-url" value="" />
        <div class='wpua_extra_avatars' >
        <?php
	        $total_thumbnail = 1;
	        for ( $count = 0; $count < $total_thumbnail; $count++ ) { ?>
	            <img src="<?php echo $data['default_avatar_url'] ; ?>" alt="" id="wp-user-avatar-img-2" class="ci_choose_image" />
	        <?php }  ?>
        </div>
        <br>
        <?php do_action( 'wpua_after_avatar'.$is_admin ); ?>

	<?php
		}
		/**
		 * Output of [avatar_upload]. Display Avatar with Edit/Delete Icons on frontend.
		 * @param  array $atts Shortcode Options.
		 */
		public static function wpua_avatar_edit_html($atts) {
			
			if ( ! is_user_logged_in() ) return; 
			$avatar_obj = new WPUA_Avatar();
			global $current_user, $errors;
			extract( shortcode_atts( array( 'user' => '' ), $atts ) );
			$valid_user = $current_user;
			// Find user by ID, login, slug, or e-mail address.
			if ( ! empty( $user ) ) {
				$get_user = is_numeric( $user ) ? get_user_by( 'id', $user ) : get_user_by( 'login', $user );
				$get_user = empty( $get_user ) ? get_user_by( 'slug', $user ) : $get_user;
				$get_user = empty( $get_user ) ? get_user_by( 'email', $user ) : $get_user;
				// Check if current user can edit this user.
				$valid_user = current_user_can( 'edit_user', $get_user ) ? $get_user : null;
			}
			// Show form only for valid user.
			if ( $valid_user ) {
				// Save.
				if ( isset( $_POST['avatar_submit'] ) && $_POST['avatar_submit'] && 'update' == sanitize_text_field( wp_unslash( $_POST['action'] ) ) ) {
					if (  wp_verify_nonce( $_POST['_wpnonce'], 'update-user_'.$valid_user->ID ) ) {
						do_action( 'wpua_update', $valid_user->ID );
						// Check for errors.
						$errors = $avatar_obj->wpua_edit_user( $valid_user->ID );
					}
				}
				// Errors.
				if ( isset( $errors ) && is_wp_error( $errors ) ) {
					echo '<div class="error"><p>'.implode( "</p>\n<p>", $errors->get_error_messages() ).'</p></div>';
				} elseif ( isset( $_GET['updated'] ) && '1' == $_GET['updated'] ) {
					$profile_updated_msg = apply_filters('wpua_profile_update_msg',__( 'Profile updated.','wp-user-avatar-pro' ) );
					echo '<div class="updated"><p><strong>'.$profile_updated_msg.'</strong></p></div>';
				}
				
				ob_start();
			?>
		<form id="wpua-edit-<?php echo $valid_user->ID; ?>" class="wpua-edit" action="" method="post" enctype="multipart/form-data">
		<?php do_action( 'wpua_show_user_profile', $valid_user); ?>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $valid_user->ID ); ?>" />
		<?php wp_nonce_field( 'update-user_'.$valid_user->ID ); ?>
		<?php $update_btn_label = apply_filters('wpua_update_btn_text','Update Avatar'); ?>
		<input type="submit" name="avatar_submit"  class="" value="<?php echo $update_btn_label; ?>">
		</form>
		<?php
		return ob_get_clean();
			}
		}
		/**
		 * Check if Errors or Redirect to Update Page.
		 * @param  integer $user_id User ID.
		 * @return array           Errors.
		 */
		private function wpua_edit_user( $user_id = 0 ) {
			$update = $user_id ? true : false;
			$user = new stdClass;
			$errors = new WP_Error();
			do_action_ref_array( 'wpua_update_errors', array( &$errors, $update, &$user ) );
			if ( $errors->get_error_codes() ) {
				// Return with errors.
				return $errors;
			}
			if ( $update ) {
				// Redirect with updated variable.
				$redirect_url = esc_url_raw( add_query_arg( array( 'updated' => '1' ), wp_get_referer() ) );
				/**
				* Filter redirect URL
				* @since 1.9.12
				* @param string $redirect_url
				*/
				$redirect_url = apply_filters( 'wpua_edit_user_redirect_url', $redirect_url );
				/**
				* Filter wp_safe_redirect or wp_redirect
				* @since 1.9.12
				* @param bool $safe_redirect
				*/
				$safe_redirect = apply_filters( 'wpua_edit_user_safe_redirect', true );
				$safe_redirect ? wp_safe_redirect( $redirect_url ) : wp_redirect( $redirect_url );
				exit;
			}
		}
		/**
		 * Setup User's meta to save avatar.
		 * @param  int $user_id User ID.
		 */
		public function wpua_avatar_save($user_id) {
			global $wpua_upload_dir, $wpua_admin, $wpdb;
			$avatar_obj = new WPUA_Avatar();

			if( empty( $_POST['wp-user-avatar-url'] ) && empty( $_POST['wp-user-avatar-deleted-url'] ) )
			return false;
			
			if( (isset($_POST['wp-user-avatar-url'][0]) and empty( $_POST['wp-user-avatar-url'][0] )) && (isset($_POST['wp-user-avatar-deleted-url'][0]) and empty( $_POST['wp-user-avatar-deleted-url'][0] )) )
			return false;

			if ( isset( $_POST['wpua_avatar'] ) && ! empty( $_POST['wpua_avatar'] ) ) {
				
				if (  wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpua_avatar'] ) ), 'wpua_avatar_delete' ) ) {
					delete_user_meta( $user_id, $wpdb->get_blog_prefix( wpua_get_main_blog_id() ).'user_avatar' );

				}
			}
			if ( isset( $_POST['wp-user-avatar-url'] ) && ! empty( $_POST['wp-user-avatar-url'] ) ) {
				$avatar_obj->wpua_avatar_upload( $_POST['wp-user-avatar-url'], $user_id );
			}
			// Check whether User as Gravatar-hosted image and update usermeta
			// $wpua_admin->set_wpua_has_gravatar( $user_id );
		}
		/**
		 * Upload Avatar into selected storage.
		 * @param  string  $file_name Source File Name.
		 * @param  string  $filepath  Source File Path.
		 * @param  integer $user_id   User ID.
		 */
		function wpua_avatar_upload( $files , $user_id = 0 ) {
			global $wpdb, $blog_id;
			$avatar_obj = new WPUA_Avatar();
			$all_avatar_thumbnails = $avatar_obj->wpua_get_avatar_thumb( $user_id );
			$data = $avatar_obj->avatar_settings();
			$wpua_upload_dir = $data['wpua_upload_dir'];
			$avatar_storage = new Wpua_Avatar_Storage();
			$avatar_storage->storage_type = $data['avatar_storage_option'];
			$avatar_storage->storage_options = $data['wp_user_avatar_storage'];
 			$storage_load = $avatar_storage->Factory();

			if ( 0 == $user_id || false == $user_id ) {
				$user_id = get_current_user_id(); }
            
			if ( ! is_wp_error( $storage_load ) ) {
				$storage_load->user_id = $user_id;
				$final_save_meta = array();

				if( $files ){
					foreach ( $files as $key => $file_data ) {
						$file_name = sanitize_file_name( $file_data );
						if ( $file_name == '' ) {
							continue; }
						$filepath = $wpua_upload_dir.'cache/'.$file_name;
						$file = array( 'name' => $file_name, 'path' => $filepath );
						$final_save_meta[] = $avatar_storage->wpua_avatar_upload( (object) $file );
					}
				}else{
				 if ( is_array( $all_avatar_thumbnails ) && $all_avatar_thumbnails && ! empty($_POST['wp-user-avatar-deleted-url'])) {
					foreach ( $all_avatar_thumbnails as $old_avatar ) {
						if ( ! in_array( $old_avatar['avatar_url'], $_POST['wp-user-avatar-deleted-url'] ) ) {
							$final_save_meta[] = $old_avatar;
						}
					}
				 }
				}
				
				update_user_meta( $user_id, $wpdb->get_blog_prefix( $blog_id ).'user_avatar', $final_save_meta  );
			}
			$avatar_storage->delete_temp_file( $filepath );
		}
		public function user_placeholders($user,$content) {
			$placeholders = array( 'user_nicename','user_email', 'user_url', 'display_name', 'first_name', 'last_name', 'nickname' );
			foreach ( $placeholders as $placeholder ) {
				$content = str_replace( '{'.$placeholder.'}', $user->{$placeholder}, $content );
			}
			$content = str_replace( '{author_url}', get_author_posts_url( $user->ID ), $content );
			$content = str_replace( '{user_bio}', get_the_author_meta( 'description',$user->ID ), $content );
			return stripcslashes( $content );
		}
		/**
		 * Display avatar using [avatar] shortcode.
		 */
		public static function wpua_shortcode($atts, $content=null) {

			global $all_sizes, $blog_id, $post, $wpdb;
			$avatar_obj = new WPUA_Avatar();
			$data = $avatar_obj->avatar_settings();
			// Set shortcode attributes
			extract( shortcode_atts( array( 'user' => '', 'size' => '96', 'align' => '', 'link' => '', 'target' => '' ), $atts ) );
			// Find user by ID, login, slug, or e-mail address
			if ( ! empty( $user ) ) {

				if( $user == 'current' ) {
					$user = wp_get_current_user();
				} else {
					$user = is_numeric( $user ) ? get_user_by( 'id', $user ) : get_user_by( 'login', $user );
					$user = empty( $user ) ? get_user_by( 'slug', $user ) : $user;
					$user = empty( $user ) ? get_user_by( 'email', $user ) : $user;
				}

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
			
			// Numeric sizes leave as-is
			$get_size = $size;
			// Check for custom image sizes if there are captions
			if ( ! empty( $content ) ) {
				if ( in_array( $size, $all_sizes ) ) {
					if ( in_array( $size, array( 'original', 'large', 'medium', 'thumbnail' ) ) ) {
						$get_size = ($size == 'original') ? get_option( 'large_size_w' ) : get_option( $size.'_size_w' );
					} else {
						$get_size = $_wp_additional_image_sizes[$size]['width'];
					}
				}
			}
			// Get user ID
			$id_or_email = ! empty( $user ) ? $user->ID : 'unknown@gravatar.com';
			// Check if link is set
			if ( ! empty( $link ) ) {
				// CSS class is same as link type, except for URL
				$link_class = $link;
				if ( $link == 'file' ) {
					// Get image src
					$link = get_wp_user_avatar_src( $id_or_email, 'original' );
				} elseif ( $link == 'attachment' ) {
					// Get attachment URL
					$link = get_attachment_link( get_the_author_meta( $wpdb->get_blog_prefix( $blog_id ).'user_avatar', $id_or_email ) );
				} else {
					// URL
					$link_class = 'custom';
				}
				// Open in new window
				$target_link = ! empty( $target ) ? ' target="'.$target.'"' : '';
				// Wrap the avatar inside the link
				$html = '<a href="'.$link.'" class="wp-user-avatar-link wp-user-avatar-'.$link_class.'"'.$target_link.'>'.get_wp_user_avatar( $id_or_email, $get_size, $align ).'</a>';
			} else {
				$html = get_wp_user_avatar( $id_or_email, $get_size, $align );
			}
			
			$html = $avatar_obj->wpuap_apply_effects($html,$id_or_email);
			$html = apply_filters('wpua_avatar_markup',$html,$id_or_email);
			// Check if caption is set
			if ( ! empty( $content ) ) {
				// Get attachment ID
				$wpua = get_user_meta( $id_or_email, $wpdb->get_blog_prefix( $blog_id ).'user_avatar', true );
				// Clean up caption
				$content = trim( $content );
				$content = preg_replace( '/\r|\n/', '', $content );
				$content = preg_replace( '/<\/p><p>/', '', $content, 1 );
				$content = preg_replace( '/<\/p><p>$/', '', $content );
				$content = str_replace( '</p><p>', '<br /><br />', $content );
				$avatar = do_shortcode( image_add_caption( $html, $wpua, $content, $title = '', $align, $link, $get_size, $alt = '' ) );
			} else {
				$avatar = $html;
			}
			return $avatar;
		}

		/**
		 * Display avatar listing using [avatar_listing] shortcode.
		 */
		public static function wpua_shortcode_listing( $atts, $content = null ) {
			
			$avatar_obj = new WPUA_Avatar();
			$data = $avatar_obj->avatar_settings();
			$avatar_listing = '';
			if( isset($atts['display_type']) and $atts['display_type'] != '' ) {
				switch($atts['display_type']) {
					case 'current_user' : $args['include'] = array(get_current_user_id()); break;
					case 'latest_users' : $args['orderby'] = 'registered'; $args['order'] ='DESC'; break;
					default : 	$args['role'] = $atts['display_type'];
				}
				
			}

			$args['number'] = (isset($atts['how_many'])) ? $atts['how_many'] : 1;
			$args = apply_filters('wpua_get_users',$args);
			$all_users = get_users($args);
			if(is_array($all_users)) {
				foreach($all_users as $user) {
					$user_info = get_userdata($user->ID);

					$avatar_html = "<div class='wpua_listing_avatar'>";
					if( isset($atts['show_link']) && 'true' == $atts['show_link'] ) {
					$avatar_html.= "<div class='section_image'><a href='".get_author_posts_url($user->ID)."'>".get_wp_user_avatar($user->ID).'</a></div>';
					} else {
					$avatar_html.= "<div class='section_image'>".get_wp_user_avatar($user->ID).'</div>';
					}
					if( isset($atts['show_name']) && 'true' == $atts['show_name'] and ''!=$user_info->display_name) {
						$avatar_html.= "<div class='section_title'>".$user_info->display_name."</div>";
					}
					if( isset($atts['show_bio']) && 'true' == $atts['show_bio'] and ''!=$user_info->description) {
						$avatar_html.= "<div class='section_content'>".$user_info->description."</div>";
					}
					$avatar_html.= "</div>";

					$avatar_listing .= $avatar_html;
				}
			}
			
			$avatar_listing = apply_filters('wpua_avatar_listing_markup',$avatar_listing,$user->ID);
			$avatar_listing = "<div class='wpua_avatar_listing'>".$avatar_listing."</div>";
			return $avatar_listing;
		}

		public function wpuap_apply_effects($html,$id_or_email) {
			if ( ! empty( $id_or_email ) ) {
				$user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : get_user_by( 'login', $id_or_email );
				$user = empty( $user ) ? get_user_by( 'slug', $id_or_email ) : $user;
				$user = empty( $user ) ? get_user_by( 'email', $id_or_email ) : $user;
			}  else {
				return $html;
			}
			$avatar_obj = new WPUA_Avatar();
			$data = $avatar_obj->avatar_settings();
			if ( isset( $data['wp_user_avatar_settings'] ) and isset( $data['wp_user_avatar_settings']['display_overlays'] ) and $data['wp_user_avatar_settings']['display_overlays'] == true ) {
				$html = "<div class='avatar_overlays'>".$html.' <p>'.$avatar_obj->user_placeholders( $user,$data['wp_user_avatar_settings']['overlays_content'] ).'</p></div>';
			}
			if ( isset( $data['wp_user_avatar_settings'] ) and isset( $data['wp_user_avatar_settings']['display_tooltip'] ) and $data['wp_user_avatar_settings']['display_tooltip'] == true ) {
				$html = "<div class='wpuap_tooltip'>".$html." <div class='wpuap_tooltip_content'><img class='callout' src='".WPUAP_IMAGES."callout.gif' />".$avatar_obj->user_placeholders( $user,$data['wp_user_avatar_settings']['tooltip_content'] ).'</div></div>';
			}
			return $html;
		}
		
		public static function get_setting( $key ){
		   $all_settings = self::avatar_settings();
		   return ( is_array($all_settings) && isset($all_settings[$key])) ? $all_settings[$key] : false;	
		}

		public  function wpua_add_signup_meta_mu($meta){
		   $wpua_upload_dir = self::get_setting('wpua_upload_dir');

           if( isset($_POST['wp-user-avatar-url']) && !empty($_POST['wp-user-avatar-url']) ){
	         $avatars = array();
	         if( is_array($_POST['wp-user-avatar-url']) ){
		         $user_avatars = array_filter($_POST['wp-user-avatar-url']);
		         foreach( $user_avatars as $file ){
			       if( file_exists($wpua_upload_dir.'cache/'.$file) )
				   $avatars[] = $file;
		         }

	         }else{
		         if( file_exists($wpua_upload_dir.'cache/'.$_POST['wp-user-avatar-url']) ){
			       $avatars[] = $_POST['wp-user-avatar-url'];
		         }
	         }

	         $meta = array_merge( array('wpua_user_avatar' => $avatars), $meta );


           }

		   return $meta;
		}

		public function wpua_action_process_option_update_mu( $user_id, $password, $meta ){
            $avatar = new WPUA_Avatar();
			if(!empty($meta['wpua_user_avatar'])){
				$avatar->wpua_avatar_upload( $meta['wpua_user_avatar'], $user_id);
			}
		}


	}

}

