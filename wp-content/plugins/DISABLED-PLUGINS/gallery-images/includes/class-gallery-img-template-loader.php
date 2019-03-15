<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Gallery_Img_Template_Loader {

	public function __contstruct() {
		add_action( 'media_buttons_context', array( $this, 'add_editor_media_button' ) );
	}


	/**
	 * Load the Plugin shortcode's frontend
	 *
	 * @param $images
	 * @param $gallery_default_params
	 * @param $gallery
	 */
	public function load_front_end( $images, $gallery_default_params, $gallery ) {
		global $post;
		global $wpdb;
		$galleryID    = $gallery[0]->id;
		$view         = $gallery[0]->huge_it_sl_effects;
		$disp_type    = $gallery[0]->display_type;
		if( $gallery[0]->content_per_page ) {
			$num = $gallery[0]->content_per_page;
		}
		else{
			$num = 999;
		}
		$like_dislike = $gallery[0]->rating;
		$total        = intval( ( ( count( $images ) - 1 ) / $num ) + 1 );
		$pattern      = '/-/';
		$huge_it_ip   = gallery_img_get_ip();
		$pID          = $post->ID;
        $slidertitle=$gallery[0]->name;
        $sliderheight=$gallery[0]->sl_height - 2*$gallery_default_params['gallery_img_slider_slideshow_border_size'];
        $sliderwidth=$gallery[0]->sl_width - 2*$gallery_default_params['gallery_img_slider_slideshow_border_size'];
        $slidereffect=$gallery[0]->gallery_list_effects_s;
        $slidepausetime=($gallery[0]->description+$gallery[0]->param);
        $sliderpauseonhover=$gallery[0]->pause_on_hover;
        $sliderposition=$gallery[0]->sl_position;
        $slidechangespeed=$gallery[0]->param;
        $trim_slider_title_position = trim($gallery_default_params['gallery_img_slider_title_position']);
        $slideshow_title_position = explode('-', $trim_slider_title_position);
        $trim_slider_description_position = trim($gallery_default_params['gallery_img_slider_description_position']);
        $slideshow_description_position = explode('-', $trim_slider_description_position);
		if ( isset( $_GET[ 'page-img' . $galleryID . $pID ] ) ) {
			$page = absint( $_GET[ 'page-img' . $galleryID . $pID ] );
		} else {
			$page = '';
		}
		$page = intval( $page );
		if ( empty( $page ) or $page < 0 ) {
			$page = 1;
		}
		if ( $page > $total ) {
			$page = $total;
		}
		$start       = $page * $num - $num;
		$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' and sl_type!='video' order by ordering ASC LIMIT " . $start . "," . $num, $galleryID );
		$page_images = $wpdb->get_results( $query );
		if ( $disp_type == 2 ) {
			$page_images = $images;
			$count_page  = 9999;
		}
		switch ( $view ) {
			case 0:
				$view_slug = gallery_img_get_view_slag_by_id( $galleryID );
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-popup' . DIRECTORY_SEPARATOR . 'content-popup-view.php';
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-popup' . DIRECTORY_SEPARATOR . 'content-popup-view.css.php';
				break;
			case 1:
				$view_slug = gallery_img_get_view_slag_by_id( $galleryID );
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-slider' . DIRECTORY_SEPARATOR . 'content-slider-view.php';
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'content-slider' . DIRECTORY_SEPARATOR . 'content-slider-view.css.php';
				break;
			case 3:
				$view_slug = gallery_img_get_view_slag_by_id( $galleryID );
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . 'slider-view.php';
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'slider' . DIRECTORY_SEPARATOR . 'slider-view.css.php';
				break;
			case 4:
				$view_slug = gallery_img_get_view_slag_by_id( $galleryID );
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . 'thumbnails-view.php';
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . 'thumbnails-view.css.php';
				break;
			case 5:
				$view_slug = gallery_img_get_view_slag_by_id( $galleryID );
                require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'lightbox-gallery' . DIRECTORY_SEPARATOR . 'lightbox-gallery-view.php';
                require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'lightbox-gallery' . DIRECTORY_SEPARATOR . 'lightbox-gallery-view.css.php';
				break;
			case 6:
				$view_slug = gallery_img_get_view_slag_by_id( $galleryID );
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'justified' . DIRECTORY_SEPARATOR . 'justified-view.php';
				require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'justified' . DIRECTORY_SEPARATOR . 'justified-view.css.php';
				break;
            case 7:
                $view_slug = gallery_img_get_view_slag_by_id( $galleryID );
                require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'blog-style-gallery' . DIRECTORY_SEPARATOR . 'blog-style-gallery-view.php';
                require GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'front-end' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'blog-style-gallery' . DIRECTORY_SEPARATOR . 'blog-style-gallery-view.css.php';
                break;
			case 10:
				$view_slug = gallery_img_get_view_slag_by_id( $galleryID );
				require GALLERY_IMG_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'elastic-grid'.DIRECTORY_SEPARATOR.'elastic-grid-view.php';
				require GALLERY_IMG_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'front-end'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'elastic-grid'.DIRECTORY_SEPARATOR.'elastic-grid-view.css.php';
				break;
		}


	}
}