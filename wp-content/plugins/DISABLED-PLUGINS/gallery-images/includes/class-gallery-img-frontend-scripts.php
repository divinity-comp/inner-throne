<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Gallery_Img_Frontend_Scripts
 */
class Gallery_Img_Frontend_Scripts {

	/**
	 * Gallery_Img_Frontend_Scripts constructor.
	 */
	public function __construct() {
		add_action( 'gallery_img_shortcode_scripts', array( $this, 'frontend_scripts' ), 10, 4 );
		add_action( 'gallery_img_shortcode_scripts', array( $this, 'frontend_styles' ), 10, 2 );
		add_action( 'gallery_img_localize_scripts', array( $this, 'localize_scripts' ), 10, 1 );
	}

	/**
	 * Enqueue styles
	 */
	public function frontend_styles( $id, $gallery_view ) {
        $gallery_default_params = gallery_img_get_default_options();
		wp_register_style( 'gallery-all-css', Gallery_Img()->plugin_url(). '/assets/style/gallery-all.css' );
		wp_enqueue_style( 'gallery-all-css' );

		wp_register_style( 'style2-os-css', Gallery_Img()->plugin_url(). '/assets/style/style2-os.css' );
		wp_enqueue_style( 'style2-os-css' );

        if ( get_option('gallery_img_lightbox_type') == 'old_type' ) {
            wp_register_style( 'lightbox-css', plugins_url( '../assets/style/lightbox.css', __FILE__ ) );
            wp_enqueue_style( 'lightbox-css' );
            wp_register_style( 'gallery_img_colorbox_css', untrailingslashit( Gallery_Img()->plugin_url() ) . '/assets/style/colorbox-' . $gallery_default_params['gallery_img_light_box_style'] . '.css' );
            wp_enqueue_style( 'gallery_img_colorbox_css' );
        } elseif ( get_option('gallery_img_lightbox_type') == 'new_type' ) {
            wp_register_style( 'gallery_img_resp_lightbox_css', untrailingslashit( Gallery_Img()->plugin_url() ) . '/assets/style/responsive_lightbox.css' );
            wp_enqueue_style( 'gallery_img_resp_lightbox_css' );
        }

		wp_register_style( 'fontawesome-css', Gallery_Img()->plugin_url(). '/assets/style/css/font-awesome.css' );
		wp_enqueue_style( 'fontawesome-css' );

		if ( $gallery_view == '1' ) {
			wp_register_style( 'animate-css', Gallery_Img()->plugin_url(). '/assets/style/animate-min.css' ) ;
			wp_enqueue_style( 'animate-css' );
			wp_register_style( 'liquid-slider-css', Gallery_Img()->plugin_url(). '/assets/style/liquid-slider.css' );
			wp_enqueue_style( 'liquid-slider-css' );
		}
		if ( $gallery_view == '4' ) {
			wp_register_style( 'thumb_view-css', Gallery_Img()->plugin_url(). '/assets/style/thumb_view.css' );
			wp_enqueue_style( 'thumb_view-css' );
		}
		if ( $gallery_view == '6' ) {
			wp_register_style( 'thumb_view-css', Gallery_Img()->plugin_url(). '/assets/style/justifiedGallery.css' );
			wp_enqueue_style( 'thumb_view-css' );
		}
		if ($gallery_view == '10') {
			wp_register_style('elastic-grid-css', Gallery_Img()->plugin_url().'/assets/style/elastic_grid.css');
			wp_enqueue_style('elastic-grid-css');
		}

	}

	/**
	 * Enqueue scripts
	 */
	public function frontend_scripts( $id, $gallery_view ) {
		$view_slug = gallery_img_get_view_slag_by_id( $id );

		if ( ! wp_script_is( 'jquery' ) ) {
			wp_enqueue_script( 'jquery' );
		}

        if ( get_option('gallery_img_lightbox_type') == 'old_type' ) {
            wp_register_script( 'jquery.gicolorbox-js', plugins_url( '../assets/js/jquery.colorbox.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
            wp_enqueue_script( 'jquery.gicolorbox-js' );
        } elseif ( get_option('gallery_img_lightbox_type') == 'new_type' ) {
            wp_register_script( 'gallery-resp-lightbox-js', plugins_url( '../assets/js/lightbox.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
            wp_enqueue_script( 'gallery-resp-lightbox-js' );

            wp_register_script( 'mousewheel-min-js', plugins_url( '../assets/js/mousewheel.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
            wp_enqueue_script( 'mousewheel-min-js' );

            wp_register_script( 'froogaloop2-min-js', plugins_url( '../assets/js/froogaloop2.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
            wp_enqueue_script( 'froogaloop2-min-js' );
        }

		wp_register_script( 'gallery-hugeitmicro-min-js', Gallery_Img()->plugin_url(). '/assets/js/jquery.hugeitmicro.min.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'gallery-hugeitmicro-min-js' );

		wp_register_script( 'gallery-front-end-js-'.$view_slug, Gallery_Img()->plugin_url(). '/assets/js/view-' . $view_slug . '.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'gallery-front-end-js-'.$view_slug );

		wp_register_script( 'gallery-custom-js', Gallery_Img()->plugin_url(). '/assets/js/custom.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'gallery-custom-js' );

		if ( $gallery_view == '1' ) {
			wp_register_script( 'easing-js', Gallery_Img()->plugin_url(). '/assets/js/jquery.easing.min.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'easing-js' );
			wp_register_script( 'touch_swipe-js', Gallery_Img()->plugin_url(). '/assets/js/jquery.touchSwipe.min.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'touch_swipe-js' );
			wp_register_script( 'liquid-slider-js', Gallery_Img()->plugin_url(). '/assets/js/jquery.liquid-slider.min.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'liquid-slider-js' );
		}
		if ( $gallery_view == '4' ) {
			wp_register_script( 'thumb-view-js', Gallery_Img()->plugin_url(). '/assets/js/thumb_view.min.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'thumb-view-js' );
			wp_register_script( 'lazyload-min-js', Gallery_Img()->plugin_url(). '/assets/js/jquery.lazyload.min.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'lazyload--min-js' );
		}
		if ( $gallery_view == '6' ) {
			wp_register_script( 'jusiifed-js', Gallery_Img()->plugin_url(). '/assets/js/justifiedGallery.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_script( 'jusiifed-js' );
		}
		if ($gallery_view == '10') {
			wp_register_script('modernizr.custom-js', Gallery_Img()->plugin_url().'/assets/js/modernizr.custom.js',  array('jquery'), '1.0.0', true);
			wp_enqueue_script('modernizr.custom-js');
			wp_register_script('classie-js', Gallery_Img()->plugin_url().'/assets/js/classie.js',  array('jquery'), '1.3.0', true);
			wp_enqueue_script('classie-js');
			wp_register_script('jquery.elastislide-js', Gallery_Img()->plugin_url().'/assets/js/jquery.elastislide.js',  array('jquery'), '1.0.0', true);
			wp_enqueue_script('jquery.elastislide-js');
			wp_register_script('jquery.hoverdir-js', Gallery_Img()->plugin_url().'/assets/js/jquery.hoverdir.js',  array('jquery'), '1.0.0', true);
			wp_enqueue_script('jquery.hoverdir-js');
			wp_register_script('elastic_grid-js', Gallery_Img()->plugin_url().'/assets/js/elastic_grid.js',  array('jquery'), '1.3.0', true);
			wp_enqueue_script('elastic_grid-js');
		}
	}

	public function localize_scripts( $id ) {
		global $wpdb;
		global $post;
        $gallery_img_get_option = gallery_img_get_option();
		$pID                    = (string) $post->ID;
		$query                  = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id=%d", $id );
		$gallery                = $wpdb->get_results( $query );
		$admin_url              = admin_url( "admin-ajax.php" );
		$gallery_default_params = gallery_img_get_default_options();
		$query = $wpdb->prepare( "SELECT image_url FROM " . $wpdb->prefix . "huge_itgallery_images WHERE gallery_id=%d", $id );
		$images       = $wpdb->get_col( $query );
		$view_slug = gallery_img_get_view_slag_by_id( $id );
		$gallery_view = $gallery[0]->huge_it_sl_effects;
		$lightbox     = array(
			'lightbox_transition'     => $gallery_default_params[ 'gallery_img_light_box_transition' ],
			'lightbox_speed'          => $gallery_default_params[ 'gallery_img_light_box_speed' ],
			'lightbox_fadeOut'        => $gallery_default_params[ 'gallery_img_light_box_fadeout' ],
			'lightbox_title'          => $gallery_default_params[ 'gallery_img_light_box_title' ],
			'lightbox_scalePhotos'    => $gallery_default_params[ 'gallery_img_light_box_scalephotos' ],
			'lightbox_scrolling'      => $gallery_default_params[ 'gallery_img_light_box_scrolling' ],
			'lightbox_opacity'        => ( $gallery_default_params[ 'gallery_img_light_box_opacity' ] / 100 ) + 0.001,
			'lightbox_open'           => $gallery_default_params[ 'gallery_img_light_box_open' ],
			'lightbox_returnFocus'    => $gallery_default_params[ 'gallery_img_light_box_returnfocus' ],
			'lightbox_trapFocus'      => $gallery_default_params[ 'gallery_img_light_box_trapfocus' ],
			'lightbox_fastIframe'     => $gallery_default_params[ 'gallery_img_light_box_fastiframe' ],
			'lightbox_preloading'     => $gallery_default_params[ 'gallery_img_light_box_preloading' ],
			'lightbox_overlayClose'   => $gallery_default_params[ 'gallery_img_light_box_overlayclose' ],
			'lightbox_escKey'         => $gallery_default_params[ 'gallery_img_light_box_esckey' ],
			'lightbox_arrowKey'       => $gallery_default_params[ 'gallery_img_light_box_arrowkey' ],
			'lightbox_loop'           => $gallery_default_params[ 'gallery_img_light_box_loop' ],
			'lightbox_closeButton'    => $gallery_default_params[ 'gallery_img_light_box_closebutton' ],
			'lightbox_previous'       => $gallery_default_params[ 'gallery_img_light_box_previous' ],
			'lightbox_next'           => $gallery_default_params[ 'gallery_img_light_box_next' ],
			'lightbox_close'          => $gallery_default_params[ 'gallery_img_light_box_close' ],
			'lightbox_html'           => $gallery_default_params[ 'gallery_img_light_box_html' ],
			'lightbox_photo'          => $gallery_default_params[ 'gallery_img_light_box_photo' ],
			'lightbox_innerWidth'     => $gallery_default_params[ 'gallery_img_light_box_innerwidth' ],
			'lightbox_innerHeight'    => $gallery_default_params[ 'gallery_img_light_box_innerheight' ],
			'lightbox_initialWidth'   => $gallery_img_get_option[ 'gallery_img_light_box_initialwidth' ],
			'lightbox_initialHeight'  => $gallery_img_get_option[ 'gallery_img_light_box_initialheight' ],
			'lightbox_slideshow'      => $gallery_default_params[ 'gallery_img_light_box_slideshow' ],
			'lightbox_slideshowSpeed' => $gallery_default_params[ 'gallery_img_light_box_slideshowspeed' ],
			'lightbox_slideshowAuto'  => $gallery_default_params[ 'gallery_img_light_box_slideshowauto' ],
			'lightbox_slideshowStart' => $gallery_default_params[ 'gallery_img_light_box_slideshowstart' ],
			'lightbox_slideshowStop'  => $gallery_default_params[ 'gallery_img_light_box_slideshowstop' ],
			'lightbox_fixed'          => $gallery_default_params[ 'gallery_img_light_box_fixed' ],
			'lightbox_reposition'     => $gallery_default_params[ 'gallery_img_light_box_reposition' ],
			'lightbox_retinaImage'    => $gallery_default_params[ 'gallery_img_light_box_retinaimage' ],
			'lightbox_retinaUrl'      => $gallery_default_params[ 'gallery_img_light_box_retinaurl' ],
			'lightbox_retinaSuffix'   => $gallery_default_params[ 'gallery_img_light_box_retinasuffix' ],
			'lightbox_maxWidth'       => $gallery_img_get_option[ 'gallery_img_light_box_maxwidth' ],
			'lightbox_maxHeight'      => $gallery_img_get_option[ 'gallery_img_light_box_maxheight' ],
			'lightbox_sizeFix'        => $gallery_img_get_option[ 'gallery_img_light_box_size_fix'],
			'galleryID'				  => $id,
			'liquidSliderInterval'	  => $gallery[0]->description
		);

		if ( $gallery_img_get_option[ 'gallery_img_light_box_size_fix' ] == 'false' ) {
			$lightbox['lightbox_width'] = '';
		} else {
			$lightbox['lightbox_width'] = $gallery_img_get_option[ 'gallery_img_light_box_width' ];
		}

		if ( $gallery_img_get_option[ 'gallery_img_light_box_size_fix' ] == 'false' ) {
			$lightbox['lightbox_height'] = '';
		} else {
			$lightbox['lightbox_height'] = $gallery_img_get_option[ 'gallery_img_light_box_height' ];
		}

		$pos = $gallery_default_params[ 'gallery_img_lightbox_open_position' ];
		switch ( $pos ) {
			case 1:
				$lightbox['lightbox_top']    = '10%';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = '10%';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 2:
				$lightbox['lightbox_top']    = '10%';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 3:
				$lightbox['lightbox_top']    = '10%';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = '10%';
				break;
			case 4:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = '10%';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 5:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 6:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = 'false';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = '10%';
				break;
			case 7:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = '10%';
				$lightbox['lightbox_left']   = '10%';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 8:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = '10%';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = 'false';
				break;
			case 9:
				$lightbox['lightbox_top']    = 'false';
				$lightbox['lightbox_bottom'] = '10%';
				$lightbox['lightbox_left']   = 'false';
				$lightbox['lightbox_right']  = '10%';
				break;
		}

		$justified        = array(
			'imagemargin'            => $gallery_default_params[ 'gallery_img_ht_view8_element_padding' ],
			'imagerandomize'         => $gallery_default_params[ 'gallery_img_ht_view8_element_randomize' ],
			'imagecssAnimation'      => $gallery_default_params[ 'gallery_img_ht_view8_element_cssAnimation' ],
			'imagecssAnimationSpeed' => $gallery_default_params[ 'gallery_img_ht_view8_element_animation_speed' ],
			'imageheight'            => $gallery_default_params[ 'gallery_img_ht_view8_element_height' ],
			'imagejustify'           => $gallery_default_params[ 'gallery_img_ht_view8_element_justify' ],
			'imageshowcaption'       => $gallery_default_params[ 'gallery_img_ht_view8_element_show_caption' ]
		);
		$justified_params = array();
		foreach ( $justified as $name => $value ) {
			$justified_params[ $name ] = $value;
		}

		if( $gallery[0]->content_per_page ) {
			$num = absint( $gallery[0]->content_per_page );
		}
		else{
			$num = 999;
		}
		$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' and sl_type!='video' ORDER BY ordering ASC",$id);
		$all_images = $wpdb->get_results($query);
		$total        = intval( ( ( count( $all_images ) - 1 ) / $num ) + 1 );
		$disp_type = $gallery[0]->display_type;
		if ( isset( $_GET[ 'page-img' . $id . $pID ] ) ) {
			$page = absint( $_GET[ 'page-img' . $id . $pID ] );
		} else {
			$page = '';
		}
		if ( empty( $page ) or $page < 0 ) {
			$page = 1;
		}
		if ( $page > $total ) {
			$page = $total;
		}
		$start       = $page * $num - $num;
		$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' and sl_type!='video'  order by ordering ASC LIMIT " . $start . "," . $num, $id );
		$images[$id] = $wpdb->get_results( $query );
		if ( $disp_type != 0 ) {
			$images[$id] = $all_images;
		}

		$images_obj = array();
		foreach ($images[$id] as $image) {
			$thumbnail = $image->image_url;
			$thumbs = array();
			$larg_images = array();
			if (gallery_img_youtube_or_vimeo($thumbnail) == 'image') {
				$smal_img = esc_url(gallery_img_get_image_by_sizes_and_src($thumbnail, 'medium', false));
				$big_img = $thumbnail;
			} elseif (gallery_img_youtube_or_vimeo($thumbnail) == 'youtube') {
				$videourl = gallery_img_get_video_id_from_url($thumbnail);
				$smal_img = esc_url("//img.youtube.com/vi/" . $videourl[0] . "/mqdefault.jpg");
				$videourl = gallery_img_get_video_id_from_url($thumbnail);
				$big_img = "https://www.youtube.com/embed/" . $videourl[0];
			} elseif (gallery_img_youtube_or_vimeo($thumbnail) == 'vimeo') {
				$videourl = gallery_img_get_video_id_from_url($thumbnail);
				$hash = unserialize(wp_remote_fopen("http://vimeo.com/api/v2/video/" . $videourl[0] . ".php"));
				$smal_img = esc_url($hash[0]['thumbnail_large']);
				$videourl = gallery_img_get_video_id_from_url($thumbnail);
				$big_img = "http://player.vimeo.com/video/" . $videourl[0];
			}
			array_push($thumbs, $smal_img);
			array_push($larg_images, $big_img);
			if ($image->link_target == 'on') {
				$target = '_blank';
			} else {
				$target = '';
			}
			$images_parent_obj = array(
				'title' => $image->name,
				'description' => $image->description,
				'thumbnail' => $thumbs,
				'large' => $larg_images,
				'button_list' => array(
					array(
						'title' => $gallery_default_params['gallery_img_ht_view10_expand_block_button_text'],
						'url' => $image->sl_url,
						'new_window' => $target
					),
				),
				'tags' => array()
			);
			array_push($images_obj, $images_parent_obj);
		}

        $lightbox_options = array(
            'gallery_img_lightbox_slideAnimationType'            => $gallery_default_params['gallery_img_lightbox_slideAnimationType'],
            'gallery_img_lightbox_lightboxView'                  => get_option('gallery_img_lightbox_lightboxView'),
            'gallery_img_lightbox_speed_new'                     => get_option('gallery_img_lightbox_speed_new'),
            'gallery_img_lightbox_width_new'                     => $gallery_default_params['gallery_img_lightbox_width_new'],
            'gallery_img_lightbox_height_new'                    => $gallery_default_params['gallery_img_lightbox_height_new'],
            'gallery_img_lightbox_videoMaxWidth'                 => $gallery_default_params['gallery_img_lightbox_videoMaxWidth'],
            'gallery_img_lightbox_overlayDuration'               => $gallery_default_params['gallery_img_lightbox_overlayDuration'],
            'gallery_img_lightbox_overlayClose_new'              => get_option('gallery_img_lightbox_overlayClose_new'),
            'gallery_img_lightbox_loop_new'                      => get_option('gallery_img_lightbox_loop_new'),
            'gallery_img_lightbox_escKey_new'                    => $gallery_default_params['gallery_img_lightbox_escKey_new'],
            'gallery_img_lightbox_keyPress_new'                  => $gallery_default_params['gallery_img_lightbox_keyPress_new'],
            'gallery_img_lightbox_arrows'                        => $gallery_default_params['gallery_img_lightbox_arrows'],
            'gallery_img_lightbox_mouseWheel'                    => $gallery_default_params['gallery_img_lightbox_mouseWheel'],
            'gallery_img_lightbox_download'                      => $gallery_default_params['gallery_img_lightbox_download'],
            'gallery_img_lightbox_showCounter'                   => $gallery_default_params['gallery_img_lightbox_showCounter'],
            'gallery_img_lightbox_nextHtml'                      => $gallery_default_params['gallery_img_lightbox_nextHtml'],
            'gallery_img_lightbox_prevHtml'                      => $gallery_default_params['gallery_img_lightbox_prevHtml'],
            'gallery_img_lightbox_sequence_info'                 => $gallery_default_params['gallery_img_lightbox_sequence_info'],
            'gallery_img_lightbox_sequenceInfo'                  => $gallery_default_params['gallery_img_lightbox_sequenceInfo'],
            'gallery_img_lightbox_slideshow_new'                 => $gallery_default_params['gallery_img_lightbox_slideshow_new'],
            'gallery_img_lightbox_slideshow_auto_new'            => $gallery_default_params['gallery_img_lightbox_slideshow_auto_new'],
            'gallery_img_lightbox_slideshow_speed_new'           => $gallery_default_params['gallery_img_lightbox_slideshow_speed_new'],
            'gallery_img_lightbox_slideshow_start_new'           => $gallery_default_params['gallery_img_lightbox_slideshow_start_new'],
            'gallery_img_lightbox_slideshow_stop_new'            => $gallery_default_params['gallery_img_lightbox_slideshow_stop_new'],
            'gallery_img_lightbox_watermark'                     => $gallery_default_params['gallery_img_lightbox_watermark'],
            'gallery_img_lightbox_socialSharing'                 => $gallery_default_params['gallery_img_lightbox_socialSharing'],
            'gallery_img_lightbox_facebookButton'                => $gallery_default_params['gallery_img_lightbox_facebookButton'],
            'gallery_img_lightbox_twitterButton'                 => $gallery_default_params['gallery_img_lightbox_twitterButton'],
            'gallery_img_lightbox_googleplusButton'              => $gallery_default_params['gallery_img_lightbox_googleplusButton'],
            'gallery_img_lightbox_pinterestButton'               => $gallery_default_params['gallery_img_lightbox_pinterestButton'],
            'gallery_img_lightbox_linkedinButton'                => $gallery_default_params['gallery_img_lightbox_linkedinButton'],
            'gallery_img_lightbox_tumblrButton'                  => $gallery_default_params['gallery_img_lightbox_tumblrButton'],
            'gallery_img_lightbox_redditButton'                  => $gallery_default_params['gallery_img_lightbox_redditButton'],
            'gallery_img_lightbox_bufferButton'                  => $gallery_default_params['gallery_img_lightbox_bufferButton'],
            'gallery_img_lightbox_diggButton'                    => $gallery_default_params['gallery_img_lightbox_diggButton'],
            'gallery_img_lightbox_vkButton'                      => $gallery_default_params['gallery_img_lightbox_vkButton'],
            'gallery_img_lightbox_yummlyButton'                  => $gallery_default_params['gallery_img_lightbox_yummlyButton'],
            'gallery_img_lightbox_watermark_text'                => $gallery_default_params['gallery_img_lightbox_watermark_text'],
            'gallery_img_lightbox_watermark_textColor'           => $gallery_default_params['gallery_img_lightbox_watermark_textColor'],
            'gallery_img_lightbox_watermark_textFontSize'        => $gallery_default_params['gallery_img_lightbox_watermark_textFontSize'],
            'gallery_img_lightbox_watermark_containerBackground' => $gallery_default_params['gallery_img_lightbox_watermark_containerBackground'],
            'gallery_img_lightbox_watermark_containerOpacity'    => $gallery_default_params['gallery_img_lightbox_watermark_containerOpacity'],
            'gallery_img_lightbox_watermark_containerWidth'      => $gallery_default_params['gallery_img_lightbox_watermark_containerWidth'],
            'gallery_img_lightbox_watermark_position_new'        => $gallery_default_params['gallery_img_lightbox_watermark_position_new'],
            'gallery_img_lightbox_watermark_opacity'             => $gallery_default_params['gallery_img_lightbox_watermark_opacity'],
            'gallery_img_lightbox_watermark_margin'              => $gallery_default_params['gallery_img_lightbox_watermark_margin'],
            'gallery_img_lightbox_watermark_img_src_new'         => $gallery_default_params['gallery_img_lightbox_watermark_img_src_new'],
        );

        if ( get_option('gallery_img_lightbox_type') == 'old_type' ) {
            wp_localize_script( 'jquery.gicolorbox-js', 'lightbox_obj', $lightbox );
        } elseif ( get_option('gallery_img_lightbox_type') == 'new_type' ) {
            list( $r, $g, $b ) = array_map( 'hexdec', str_split( $gallery_default_params['gallery_img_lightbox_watermark_containerBackground'], 2 ) );
            $titleopacity                                                       = $gallery_default_params["gallery_img_lightbox_watermark_containerOpacity"] / 100;
            $lightbox_options['gallery_img_watermark_container_bg_color'] = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $titleopacity . ')';
            wp_localize_script( 'gallery-resp-lightbox-js', 'gallery_resp_lightbox_obj', $lightbox_options );
            wp_localize_script( 'gallery-custom-js', 'is_watermark', $gallery_default_params['gallery_img_lightbox_watermark'] );
            wp_localize_script( 'gallery-resp-lightbox-js', 'GalleryImgDisableRightClickLightbox', get_option( 'gallery_img_disable_right_click' ) );
        }


        wp_localize_script( 'gallery-front-end-js-'.$view_slug, 'param_obj', $gallery_default_params );
		wp_localize_script( 'gallery-front-end-js-'.$view_slug, 'adminUrl', $admin_url );
		wp_localize_script( 'gallery-front-end-js-'.$view_slug, 'postID', $pID );
		wp_localize_script( 'gallery-front-end-js-'.$view_slug, 'postID', $pID );
		wp_localize_script( 'jquery.gicolorbox-js', 'lightbox_obj', $lightbox );
		wp_localize_script( 'gallery-custom-js', 'galleryId', $id );
		wp_localize_script( 'jusiifed-js', 'justified_obj', $justified );
		wp_localize_script('gallery-front-end-js-' . $view_slug, 'gallery_images_obj_' . $id, $images_obj);
		wp_localize_script('elastic_grid-js', 'elements_margin', $gallery_default_params['gallery_img_ht_view10_element_margin']);
        wp_localize_script( 'gallery-custom-js', 'galleryImgLigtboxType', get_option( 'gallery_img_lightbox_type' ) );
        wp_localize_script( 'gallery-custom-js', 'galleryImgDisableRightClick', get_option( 'gallery_img_disable_right_click' ) );
		wp_localize_script( 'gallery-custom-js', 'galleryImgDisableRightClickElastic', get_option( 'gallery_img_disable_right_click' ) );
	}
}