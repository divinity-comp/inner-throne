<?php
/**
 * Defines widgets.
 *
 * @package Avatar
 * @version 4.0.0
 */

class WP_User_Avatar_Profile_Widget extends WP_Widget {
	/**
	 * Constructor
	 * @since 1.9.4
	 */
	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_wp_user_avatar', 'description' => __( 'Insert',WPUAP_TEXT_DOMAIN ).' '.__( '[avatar_upload]',WPUAP_TEXT_DOMAIN ).'.' );
		parent::__construct( 'wp_user_avatar_profile', __( 'WP User Avatar',WPUAP_TEXT_DOMAIN ), $widget_ops );
	}

	/**
	 * Add [avatar_upload] to widget
	 * @since 1.9.4
	 * @param array $args
	 * @param array $instance
	 * @uses object $wp_user_avatar
	 * @uses bool $wpua_allow_upload
	 * @uses object $wpua_shortcode
	 * @uses add_filter()
	 * @uses apply_filters()
	 * @uses is_user_logged_in()
	 * @uses remove_filter()
	 * @uses wpua_edit_shortcode()
	 * @uses wpua_is_author_or_above()
	 */
	public function widget($args, $instance) {
		global  $wpua_allow_upload, $wpua_shortcode;
		extract( $args );
		$instance = apply_filters( 'wpua_widget_instance', $instance );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		// Show widget only for users with permission
		$show_bio = (isset($instance['show_bio']) and $instance['show_bio']=='1') ? 'true' : 'false';
		$show_link = (isset($instance['link_to_profile']) and $instance['link_to_profile']=='1' ) ? 'true' : 'false';
		$show_name = (isset($instance['show_name']) and $instance['show_name']=='1') ? 'true' : 'false';
		$how_many_avatars = (isset($instance['how_many_avatars'])) ? $instance['how_many_avatars'] : 20;
		$display_type = (isset($instance['display_type'])) ? $instance['display_type'] : 'current_user';
		
		
			echo $before_widget;
			if ( ! empty( $title ) ) {
				echo $before_title.$title.$after_title;
			}
			if ( ! empty( $text ) ) {
				echo '<div class="textwidget">';
				echo ! empty( $instance['filter'] ) ? wpautop( $text ) : $text;
				echo '</div>';
			}
			// Remove profile title
			add_filter( 'wpua_profile_title', '__return_null' );
			// Get [avatar_upload] shortcode
			if($instance['shortcode_type'] == 'avatar_listing') {
				echo do_shortcode('[avatar_listing show_bio='.$show_bio.'  show_link='.$show_link.'  show_name='.$show_name.'  how_many='.$how_many_avatars.'  display_type='.$display_type.']');
			} else {
				if ( is_user_logged_in() ) {
				echo do_shortcode('[avatar_upload]'); }
			}
			echo $after_widget;
			remove_filter( 'wpua_profile_title', '__return_null' );
		
	}

	/**
	 * Set title
	 * @since 1.9.4
	 * @param array $instance
	 * @uses wp_parse_args()
	 */
	public function form($instance) {
		$title = strip_tags( $instance['title'] );
		$text = esc_textarea( $instance['text'] );
		extract($instance);
	?><br>
    
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        <?php _e( 'Title:',WPUAP_TEXT_DOMAIN ); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Description:',WPUAP_TEXT_DOMAIN ); ?></label>
    <textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo $text; ?></textarea>
    <p>
      <input id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" type="checkbox" <?php checked( isset( $instance['filter'] ) ? $instance['filter'] : 0 ); ?> />
      <label for="<?php echo $this->get_field_id( 'filter' ); ?>">
        <?php _e( 'Automatically add paragraphs',WPUAP_TEXT_DOMAIN ); ?>
      </label>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id( 'shortcode_type' ); ?>">
        <?php _e( 'What to do :',WPUAP_TEXT_DOMAIN ); ?>
      </label>
      <select class="widefat" id="<?php echo $this->get_field_id( 'shortcode_type' ); ?>" name="<?php echo $this->get_field_name( 'shortcode_type' ); ?>" >
      <option <?php selected('avatar_upload',$shortcode_type) ?>value='avatar_upload'><?php _e('Upload Avatar',WPUAP_TEXT_DOMAIN);?></option>
      <option <?php selected('avatar_listing',$shortcode_type) ?> value='avatar_listing'><?php _e('Show Avatar(s)',WPUAP_TEXT_DOMAIN);?></option>
      </select>
    </p>
     <div class="wpuap_show_avatars">
    	<p>
	      <label for="<?php echo $this->get_field_id( 'display_type' ); ?>">
	        <?php _e( 'Show:',WPUAP_TEXT_DOMAIN ); ?>
	      </label>
	      <select class="widefat" id="<?php echo $this->get_field_id( 'display_type' ); ?>" name="<?php echo $this->get_field_name( 'display_type' ); ?>" >
	      <option <?php selected('current_user',$display_type) ?> value="current_user"><?php _e('Current User',WPUAP_TEXT_DOMAIN);?></option>
	      <option <?php selected('latest_users',$display_type) ?> value="latest_users"><?php _e('Latest Users',WPUAP_TEXT_DOMAIN);?></option>
		 <?php wp_dropdown_roles( $display_type ); ?>
		 </select>
    	</p>
    	<p>
	      <label for="<?php echo $this->get_field_id( 'how_many_avatars' ); ?>">
	        <?php _e( 'How Many Avatars:',WPUAP_TEXT_DOMAIN ); ?>
	      </label>
	      <input id="<?php echo $this->get_field_id( 'how_many_avatars' ); ?>" name="<?php echo $this->get_field_name( 'how_many_avatars' ); ?>" type="text" value="<?php echo esc_attr( $how_many_avatars ); ?>" />
	    </p>
	    <p>
	      <label for="<?php echo $this->get_field_id( 'show_name' ); ?>">
	        <?php _e( 'Show Name',WPUAP_TEXT_DOMAIN ); ?>&nbsp;&nbsp;
	        <input  <?php checked('1',$show_name) ?> id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" type="checkbox" value="1" />
	      </label>
	    </p>
	    <p>
	      <label for="<?php echo $this->get_field_id( 'link_to_profile' ); ?>">
	        <?php _e( 'Link to Profile',WPUAP_TEXT_DOMAIN ); ?>&nbsp;&nbsp;
	        <input  <?php checked('1',$link_to_profile) ?> id="<?php echo $this->get_field_id( 'link_to_profile' ); ?>" name="<?php echo $this->get_field_name( 'link_to_profile' ); ?>" type="checkbox" value="1" />
	      </label>
	    </p>

	     <p>
	      <label for="<?php echo $this->get_field_id( 'show_bio' ); ?>">
	        <?php _e( 'Show Bio',WPUAP_TEXT_DOMAIN ); ?>&nbsp;&nbsp;
	        <input  <?php checked('1',$show_bio) ?> id="<?php echo $this->get_field_id( 'show_bio' ); ?>" name="<?php echo $this->get_field_name( 'show_bio' ); ?>" type="checkbox" value="1" />
	      </label>
	    </p>

    </div>
	<?php
	}

	/**
	 * Update widget
	 * @since 1.9.4
	 * @param array $new_instance
	 * @param array $old_instance
	 * @uses current_user_can()
	 * @return array
	 */
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['shortcode_type'] = sanitize_text_field( $new_instance['shortcode_type'] );
		$instance['display_type'] = sanitize_text_field( $new_instance['display_type'] );
		$instance['how_many_avatars'] = sanitize_text_field( $new_instance['how_many_avatars'] );
		$instance['link_to_profile'] = isset(  $new_instance['link_to_profile'] );
		$instance['show_bio'] = isset( $new_instance['show_bio'] );
		$instance['show_name'] = isset( $new_instance['show_name'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) );
		}
		$instance['filter'] = isset( $new_instance['filter'] );
		return $instance;
	}
}
