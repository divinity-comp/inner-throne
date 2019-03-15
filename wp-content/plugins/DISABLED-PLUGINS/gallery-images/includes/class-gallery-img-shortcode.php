<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Gallery_Img_Shortcode {

	/**
	 * Gallery_Img_Shortcode constructor.
	 */
	public function __construct() {
		add_shortcode( 'huge_it_gallery', array( $this, 'run_shortcode' ) );
		add_action( 'admin_footer', array( $this, 'inline_popup_content' ) );
		add_action( 'media_buttons_context', array( $this, 'add_editor_media_button' ) );

	}

	/**
	 * Run the shortcode on front-end
	 *
	 * @param $attrs
	 *
	 * @return string
	 */
	public function run_shortcode( $attrs ) {
		$attrs = shortcode_atts( array(
			'id' => 'no huge_it gallery',

		), $attrs );

		global $wpdb;

		$query        = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id=%d", $attrs['id'] );
		$gallery = $wpdb->get_results( $query );
		if( !$gallery ){
			ob_start();
			printf( __( "Gallery with ID %s doesn't exist.","gallery-img" ), $attrs['id'] );
			return ob_get_clean();
		}

		$query        = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images WHERE gallery_id=%d", $attrs['id'] );
		$images_row   = $wpdb->get_results( $query );
		if( !$images_row ){
			ob_start();
			printf( __( "Gallery with ID %s is empty.","gallery-img" ), $attrs['id'] );
			return ob_get_clean();
		}

		$query        = $wpdb->prepare( "SELECT huge_it_sl_effects FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id=%d", $attrs['id'] );
		$gallery_view = $wpdb->get_var( $query );
		$query = $wpdb->prepare( "SELECT image_url FROM " . $wpdb->prefix . "huge_itgallery_images WHERE gallery_id=%d and sl_type!='video'", $attrs['id'] );
		$images       = $wpdb->get_col( $query );

		do_action( 'gallery_img_shortcode_scripts', $attrs['id'], $gallery_view );
		do_action( 'gallery_img_localize_scripts', $attrs['id'] );

		return $this->init_frontend( $attrs['id'] );
	}

	/**
	 * Show published galleries in frontend
	 *
	 * @param $id
	 *
	 * @return string
	 */
	protected function init_frontend( $id ) {
		global $wpdb;

		$query  = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' and sl_type!='video' order by ordering ASC", $id );
		$images = $wpdb->get_results( $query );

		$query   = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys where id = '%d' order by id ASC", $id );
		$gallery = $wpdb->get_results( $query );

        $gallery_default_params = gallery_img_get_default_options();

		ob_start();
		Gallery_Img()->template_loader->load_front_end( $images, $gallery_default_params, $gallery );

		return ob_get_clean();

	}

	/**
	 * Add editor media button
	 *
	 * @param $context
	 *
	 * @return string
	 */
	public function add_editor_media_button( $context ) {
		$img          = GALLERY_IMG_IMAGES_URL . '/admin_images/post.button.png';
		$container_id = 'huge_it_gallery';
		$title        = __( 'Select Huge IT gallery to insert into post', 'gallery-images' );
		$context .= '<a class="button thickbox" title="Select gallery to insert into post" title="' . $title . '" href="#TB_inline?width=400&inlineId=' . $container_id . '">
        <span class="wp-media-buttons-icon" style="background: url(' . $img . '); background-repeat: no-repeat; background-position: left bottom;"></span>
    Add gallery
    </a>';

		return $context;
	}

	/**
	 * Inline popup contents
	 */
	public function inline_popup_content() {
		require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'inline-popup-content-html.php';
	}


}
