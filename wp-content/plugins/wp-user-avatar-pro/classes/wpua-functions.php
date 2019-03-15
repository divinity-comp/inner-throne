<?php
/**
 * Public user functions.
 *
 * @package Avatar
 * @version 4.0.0
 */

/**
 * Returns true if user has wp_user_avatar
 * @since 1.8
 * @param int|string $id_or_email
 * @param bool       $has_wpua
 * @param object     $user
 * @param int        $user_id
 * @uses object $wpua_functions
 * @return object has_wp_user_avatar()
 */
function has_wp_user_avatar($id_or_email='', $has_wpua='', $user='', $user_id='') {
	global $wpua_functions;
	return $wpua_functions->has_wp_user_avatar( $id_or_email, $has_wpua, $user, $user_id );
}

/**
 * Find WPUA, show get_avatar if empty
 * @since 1.8
 * @param int|string $id_or_email
 * @param int|string $size
 * @param string     $align
 * @param string     $alt
 * @uses object $wpua_functions
 * @return object get_wp_user_avatar()
 */
function get_wp_user_avatar($id_or_email='', $size='', $align='', $alt='') {
	global $wpua_functions;
	return $wpua_functions->get_wp_user_avatar( $id_or_email, $size, $align, $alt );
}

/**
 * Return just the image src
 * @since 1.8
 * @param int|string $id_or_email
 * @param int|string $size
 * @param string     $align
 * @uses object $wpua_functions
 * @return object get_wp_user_avatar_src()
 */
function get_wp_user_avatar_src($id_or_email='', $size='', $align='') {
	global $wpua_functions;
	return $wpua_functions->get_wp_user_avatar_src( $id_or_email, $size, $align );
}

/**
 * Before wrapper for profile
 * @since 1.6
 * @uses do_action()
 */
function wpua_before_avatar() {
	do_action( 'wpua_before_avatar' );
}

/**
 * After wrapper for profile
 * @since 1.6
 * @uses do_action()
 */
function wpua_after_avatar() {
	do_action( 'wpua_after_avatar' );
}

/**
 * Before avatar container
 * @since 1.6
 * @uses apply_filters()
 * @uses bbp_is_edit()
 * @uses wpuf_has_shortcode()
 */
function wpua_do_before_avatar() {
	$wpua_profile_title = '<h3>'.__( 'Avatar','wp-user-avatar-pro' ).'</h3>';
	/**
	* Filter profile title
	* @since 1.9.4
	* @param string $wpua_profile_title
	*/
	$wpua_profile_title = apply_filters( 'wpua_profile_title', $wpua_profile_title );
?>
	<?php if ( class_exists( 'bbPress' ) && bbp_is_edit() ) : // Add to bbPress profile with same style ?>
    <h2 class="entry-title"><?php _e( 'Avatar',WPUAP_TEXT_DOMAIN ); ?></h2>
    <fieldset class="bbp-form">
      <legend><?php _e( 'Image',WPUAP_TEXT_DOMAIN ); ?></legend>
	<?php elseif ( class_exists( 'WPUF_Main' ) && wpuf_has_shortcode( 'wpuf_editprofile' ) ) : // Add to WP User Frontend profile with same style ?>
    <fieldset>
      <legend><?php _e( 'Avatar','wp-user-avatar-pro' ) ?></legend>
      <table class="wpuf-table">
        <tr>
          <th><label for="wp_user_avatar"><?php _e( 'Image',WPUAP_TEXT_DOMAIN ); ?></label></th>
          <td>
	<?php else : // Add to profile without table ?>
    <div class="wpua-edit-container">
		<?php echo $wpua_profile_title; ?>
	<?php endif; ?>
	<?php
}
add_action( 'wpua_before_avatar', 'wpua_do_before_avatar' );

/**
 * After avatar container
 * @since 1.6
 * @uses bbp_is_edit()
 * @uses wpuf_has_shortcode()
 */
function wpua_do_after_avatar() {
?>
	<?php if ( class_exists( 'bbPress' ) && bbp_is_edit() ) : // Add to bbPress profile with same style ?>
    </fieldset>
	<?php elseif ( class_exists( 'WPUF_Main' ) && wpuf_has_shortcode( 'wpuf_editprofile' ) ) : // Add to WP User Frontend profile with same style ?>
          </td>
        </tr>
      </table>
    </fieldset>
	<?php else : // Add to profile without table ?>
    </div>
	<?php endif; ?>
	<?php
}
add_action( 'wpua_after_avatar', 'wpua_do_after_avatar' );

/**
 * Before wrapper for profile in admin section
 * @since 1.9.4
 * @uses do_action()
 */
function wpua_before_avatar_admin() {
	do_action( 'wpua_before_avatar_admin' );
}

/**
 * After wrapper for profile in admin section
 * @since 1.9.4
 * @uses do_action()
 */
function wpua_after_avatar_admin() {
	do_action( 'wpua_after_avatar_admin' );
}

/**
 * Before avatar container in admin section
 * @since 1.9.4
 */
function wpua_do_before_avatar_admin() {
?>
  <h3><?php _e( 'Avatar' ) ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="wp_user_avatar"><?php _e( 'Image',WPUAP_TEXT_DOMAIN ); ?></label></th>
      <td>
	<?php
}
add_action( 'wpua_before_avatar_admin', 'wpua_do_before_avatar_admin' );

/**
 * After avatar container in admin section
 * @since 1.9.4
 */
function wpua_do_after_avatar_admin() {
?>
      </td>
    </tr>
  </table>
	<?php
}
add_action( 'wpua_after_avatar_admin', 'wpua_do_after_avatar_admin' );

/**
 * Filter for the inevitable complaints about the donation message :(
 * @since 1.6.6
 * @uses do_action()
 */
function wpua_donation_message() {
	do_action( 'wpua_donation_message' );
}

/**
 * Donation message
 * @since 1.6.6
 */
function wpua_do_donation_message() {
	?>
   <div class="updated">
    <p><?php _e( 'Do you like WP User Avatar?',WPUAP_TEXT_DOMAIN ); ?> <a href="http://wordpress.org/plugins/wp-google-map-plugin/" target="_blank"><?php _e( 'Try our Google Maps Plugin.', 'wp-user-avatar' ); ?></a></p> 
  </div>
	<?php
}
add_action( 'wpua_donation_message', 'wpua_do_donation_message' );

/**
 * Register widget
 * @since 1.9.4
 * @uses register_widget()
 */
function wpua_widgets_init() {
	register_widget( 'WP_User_Avatar_Profile_Widget' );
}
add_action( 'widgets_init', 'wpua_widgets_init' );

/**
   * Check if current user has at least Author privileges
   * @since 1.8.5
   * @uses current_user_can()
   * @uses apply_filters()
   * @return bool
   */
   function wpua_is_author_or_above() {
    // $is_author_or_above = (current_user_can('edit_published_posts') && current_user_can('upload_files') && current_user_can('publish_posts') && current_user_can('delete_published_posts')) ? true : false;
    $is_author_or_above = is_user_logged_in();
    /**
   * Filter Author privilege check
   * @since 1.9.2
   * @param bool $is_author_or_above
   */
    return (bool) apply_filters( 'wpua_is_author_or_above', $is_author_or_above );
  }

/**
 * Buddypress include
 * @since 1.9.4
 * @uses wpua_bp_include()
 */
function wpua_bp_include() {
	require_once( WPUA_INC.'class-wp-user-bp.php' );
}

function wpua_avatar_upload( $file_name, $filepath, $user_id = 0 ) {

	$avatar_storage = new Wpua_Avatar_Storage();
	$storage_load = $avatar_storage->Factory();

	if ( $user_id == 0 || $user_id == false ) {
		$user_id = get_current_user_id(); }

	if ( ! is_wp_error( $storage_load ) ) {
		$storage_load->user_id = $user_id;
		$file = array( 'name' => $file_name, 'path' => $filepath );
		return $avatar_storage->wpua_avatar_upload( (object) $file );
	}
	$avatar_storage->delete_temp_file( $filepath );
}


function wpua_get_main_blog_id() {
	global $blog_id;

	return $blog_id;

}

function format_size_units($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
