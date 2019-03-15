<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Gallery_Img_Lightbox_Options {

	public function __construct() {
		add_action( 'gallery_img_save_lightbox_options', array( $this, 'save_options' ) );
	}

	/**
	 * Loads Lightbox options page
	 */
	public function load_page() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'Options_gallery_lightbox_styles' ) {
			if ( isset( $_GET['task'] ) ) {
				if ( $_GET['task'] == 'save' ) {
					do_action( 'gallery_img_save_lightbox_options' );
				}
			} else {
				$this->show_page();
			}
		}
	}

	public function save_options() {
		if ( !isset( $_REQUEST['huge_it_gallery_nonce_save_lightbox_options'] ) || ! wp_verify_nonce( $_REQUEST['huge_it_gallery_nonce_save_lightbox_options'], 'huge_it_gallery_nonce_save_lightbox_options' ) ) {
			wp_die( 'Security check fail' );
		}
		if (isset($_POST['params'])) {
			$params = $_POST['params'];
			foreach ($params as $name => $value) {
				update_option($name, wp_unslash(sanitize_text_field($value)));
			}
			?>
			<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
			<?php
		}
		$this->show_page();
	}


	/**
	 * Shows Lightbox options page
	 */
	public function show_page() {
		$gallery_img_get_option = gallery_img_get_option();
		$gallery_img_get_default_options = gallery_img_get_default_options();
		require( GALLERY_IMG_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'lightbox-options-html.php' );
	}
}