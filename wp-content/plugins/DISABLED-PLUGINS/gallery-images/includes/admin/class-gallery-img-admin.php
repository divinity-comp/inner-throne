<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Gallery_Img_Admin {

	/**
	 * Array of pages in admin
	 * @var array
	 */
	public $pages = array();

	/**
	 * Instance of Gallery_Img_General_Options class
	 *
	 * @var Gallery_Img_General_Options
	 */
	public $general_options = null;

	/**
	 * Instance of Gallery_Img_Galleries class
	 *
	 * @var Gallery_Img_Galleries
	 */
	public $galleries = null;

	/**
	 * Instance of Gallery_Img_Lightbox_Options class
	 *
	 * @var Gallery_Img_Lightbox_Options
	 */
	public $lightbox_options = null;

	/**
	 * Instance of Gallery_Img_Featured_Plugins class
	 *
	 * @var Gallery_Img_Featured_Plugins
	 */
	public $featured_plugins = null;

	/**
	 * Instance of Gallery_Img_Licensing class
	 *
	 * @var Gallery_Img_Licensing
	 */
	public $licensing = null;

	/**
	 * Gallery_Img_Admin constructor.
	 */
	public function __construct() {
		$this->init();
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
	}

	/**
	 * Initialize Image Gallery's admin
	 */
	protected function init() {
		$this->general_options  = new Gallery_Img_General_Options();
		$this->galleries        = new Gallery_Img_Galleries();
		$this->lightbox_options = new Gallery_Img_Lightbox_Options();
		$this->featured_plugins = new Gallery_Img_Featured_Plugins();
		$this->licensing        = new Gallery_Img_Licensing();
	}

	/**
	 * Prints Gallery Menu
	 */
	public function admin_menu() {
		$this->pages[] = add_menu_page( __( 'Huge-IT  Gallery', 'gallery-images' ), __( 'Huge-IT Gallery', 'gallery-images' ), 'delete_pages', 'galleries_huge_it_gallery', array(
			Gallery_Img()->admin->galleries,
			'load_gallery_page'
		), GALLERY_IMG_IMAGES_URL . "/admin_images/huge-it-gallery-logo-for-menu.png" );
		$this->pages[] = add_submenu_page( 'galleries_huge_it_gallery', __( 'Galleries', 'gallery-images' ), __( 'Galleries', 'gallery-images' ), 'delete_pages', 'galleries_huge_it_gallery', array(
			Gallery_Img()->admin->galleries,
			'load_gallery_page'
		) );

		$this->pages[] = add_submenu_page( 'galleries_huge_it_gallery', __( 'Advanced Features PRO', 'gallery-images' ), __( 'Advanced Features PRO', 'gallery-images' ), 'delete_pages', 'Options_gallery_styles', array(
			Gallery_Img()->admin->general_options,
			'load_page'
		) );
		$this->pages[] = add_submenu_page( 'galleries_huge_it_gallery', __( 'Lightbox Options', 'gallery-images' ), __( 'Lightbox Options', 'gallery-images' ), 'delete_pages', 'Options_gallery_lightbox_styles', array(
			Gallery_Img()->admin->lightbox_options,
			'load_page'
		) );

		$this->pages[] = add_submenu_page( 'galleries_huge_it_gallery', __( 'Featured Plugins', 'gallery-images' ), __( 'Featured Plugins', 'gallery-images' ), 'delete_pages', 'huge_it_gallery_featured_plugins', array(
			Gallery_Img()->admin->featured_plugins,
			'show_page'
		) );
		$this->pages[] = add_submenu_page( 'galleries_huge_it_gallery', __( 'Licensing', 'gallery-images' ), __( 'Licensing', 'gallery-images' ), 'delete_pages', 'huge_it_gallery_licensing', array(
			Gallery_Img()->admin->licensing,
			'show_page'
		) );
	}


	public function wp_loaded() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'galleries_huge_it_gallery' ) {
			if ( isset( $_GET['task'] ) ) {
				$task = $_GET['task'] ;
				switch ( $task ) {
					case 'add_gallery':
						$this->add_gallery();
						break;
					case 'duplicate_gallery_image':
						$this->duplicate_gallery();
						break;
					case 'remove_gallery':
						$this->remove_gallery();
						break;
				}
			}
		}
	}

	/**
	 * Add Gallery
	 */
	public static function add_gallery() {
		if ( ! isset( $_REQUEST['gallery_wp_nonce_add_gallery'] ) || ! wp_verify_nonce( $_REQUEST['gallery_wp_nonce_add_gallery'], 'gallery_wp_nonce_add_gallery' ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		$table_name = $wpdb->prefix . "huge_itgallery_gallerys";
		$wpdb->insert(
			$table_name,
			array(
				'name'=>'New gallery',
				'sl_height'=>'375',
				'sl_width'=>'600',
				'pause_on_hover'=>'on',
				'gallery_list_effects_s'=>'cubeH',
				'description'=>'4000',
				'param'=>'1000',
				'sl_position'=>'center',
				'ordering'=>'1',
				'published'=>'300',
				'huge_it_sl_effects'=>'4',
			),
			array('%s','%d','%d','%s','%s','%d','%d','%s','%d','%d','%d')
		);
		$last_key        = $wpdb->insert_id;
		$save_data_nonce = wp_create_nonce( 'huge_it_gallery_nonce_save_data' . $last_key );
		header( 'Location: admin.php?page=galleries_huge_it_gallery&id=' . $last_key . '&task=apply' . '&save_data_nonce=' . $save_data_nonce );
	}

	/**
	 * Duplicate Video
	 */
	public static function duplicate_gallery() {
		if ( ! isset( $_GET["id"] ) || ! absint( $_GET['id'] ) || absint( $_GET['id'] ) != $_GET['id'] ) {
			wp_die( '"id" parameter is required to be not negative integer' );
		}
		$id = absint( $_GET["id"] );
		if ( ! isset( $_REQUEST['gallery_duplicate_nonce'] ) || ! wp_verify_nonce( $_REQUEST['gallery_duplicate_nonce'], 'huge_it_gallery_nonce_duplicate_gallery' . $id ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		$table_name  = $wpdb->prefix . "huge_itgallery_gallerys";
		$query       = $wpdb->prepare( "SELECT * FROM " . $table_name . " WHERE id=%d", $id );
		$gallery_img = $wpdb->get_results( $query );
		$wpdb->insert(
			$table_name,
			array(
				'name'                   => $gallery_img[0]->name . ' Copy',
				'sl_height'              => $gallery_img[0]->sl_height,
				'sl_width'               => $gallery_img[0]->sl_width,
				'pause_on_hover'         => $gallery_img[0]->pause_on_hover,
				'gallery_list_effects_s' => $gallery_img[0]->gallery_list_effects_s,
				'description'            => $gallery_img[0]->description,
				'param'                  => $gallery_img[0]->param,
				'sl_position'            => $gallery_img[0]->sl_position,
				'ordering'               => $gallery_img[0]->ordering,
				'published'              => $gallery_img[0]->published,
				'huge_it_sl_effects'     => $gallery_img[0]->huge_it_sl_effects,
				'display_type'           => $gallery_img[0]->display_type,
				'content_per_page'       => $gallery_img[0]->content_per_page,
				'rating'                 => $gallery_img[0]->rating,
				'autoslide'              => $gallery_img[0]->autoslide
			)
		);
		$last_key       = $wpdb->insert_id;
		$table_name     = $wpdb->prefix . "huge_itgallery_images";
		$query          = $wpdb->prepare( "SELECT * FROM " . $table_name . " WHERE gallery_id=%d", $id );
		$galleries      = $wpdb->get_results( $query );
		$galleries_list = "";
		foreach ( $galleries as $key => $gallery ) {
			$new_gallery = "('";
			$new_gallery .= $gallery->name . "','" . $last_key . "','" . $gallery->description . "','" . $gallery->image_url . "','" .
				$gallery->sl_url . "','" . $gallery->sl_type . "','" . $gallery->link_target . "','" . $gallery->ordering . "','" .
				$gallery->published . "','" . $gallery->published_in_sl_width . "','" . $gallery->like . "','" .
				$gallery->dislike . "')";
			$galleries_list .= $new_gallery . ",";
		}
		$galleries_list = substr( $galleries_list, 0, strlen( $galleries_list ) - 1 );
		$query          = "INSERT into " . $table_name . " (`name`,`gallery_id`,`description`,`image_url`,`sl_url`,`sl_type`,`link_target`,`ordering`,`published`,`published_in_sl_width`,`like`,`dislike`)
					VALUES " . $galleries_list;
		$wpdb->query( $query );
		wp_redirect( 'admin.php?page=galleries_huge_it_gallery' );
	}

	/**
	 * Remove Gallery
	 */
	public function remove_gallery() {
		if ( ! isset( $_GET["id"] ) || ! absint( $_GET['id'] ) || absint( $_GET['id'] ) != $_GET['id'] ) {
			wp_die( '"id" parameter is required to be not negative integer' );
		}
		$id = absint( $_GET["id"] );
		if ( ! isset( $_REQUEST['huge_it_gallery_nonce_remove_gallery'] ) || ! wp_verify_nonce( $_REQUEST['huge_it_gallery_nonce_remove_gallery'], 'huge_it_gallery_nonce_remove_gallery' . $id ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		$sql_remov_tag    = $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id = %d", $id );
		$sql_remove_image = $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_images WHERE gallery_id = %d", $id );
		if ( ! $wpdb->query( $sql_remov_tag ) ) {
			setcookie( 'gallery_deleted', 'fail', time() + 120 );
		} else {
			$wpdb->query( $sql_remov_tag );
			$wpdb->query( $sql_remove_image );
			setcookie( 'gallery_deleted', 'success', time() + 120 );
		}
		wp_redirect( 'admin.php?page=galleries_huge_it_gallery' );
	}

}

