<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Gallery_Img_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_huge_it_gallery_ajax', array( $this, 'callback' ) );
		add_action( 'wp_ajax_huge_it_gallery_ajax', array( $this, 'callback' ) );
	}

	public function callback() {
		$gallery_default_params = gallery_img_get_default_options();
		if ( isset( $_POST['task'] ) ) {
			$task = $_POST['task'] ;
			switch ( $task ) {
				case 'load_images_content':
					$this->load_content_popup();
					break;
				case 'load_images_lightbox':
					$this->load_lightbox();
					break;
				case 'load_image_justified':
					$this->load_justifuied();
					break;
				case 'load_image_thumbnail':
					$this->load_thumbnail();
					break;
				case 'load_blog_view':
					$this->load_blog_view();
					break;
				case 'like':
					$this->like_action();
					break;
				case 'dislike':
					$this->dislike_action();
					break;
			}
		}
	}

	public static function load_content_popup () {
		if ( ! isset( $_POST['task'] ) || ! wp_verify_nonce( $_POST['galleryImgContentLoadNonce'], 'gallery_img_content_load_nonce' ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		global $huge_it_ip;
		$page = 1;
		if ( ! empty( $_POST["page"] ) && is_numeric( $_POST['page'] ) && $_POST['page'] > 0 ) {
			$page        = intval( $_POST["page"] );
			$num         = intval( $_POST['perpage'] );
			$start       = $page * $num - $num;
			$idofgallery = intval( $_POST['galleryid'] );
			$pID         = intval( $_POST['pID'] );
			$likeStyle   = esc_html( $_POST['likeStyle'] );
			$ratingCount = esc_html( $_POST['ratingCount'] );
			$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' and sl_type='image' order by ordering ASC LIMIT %d,%d", $idofgallery, $start, $num );
			$page_images = $wpdb->get_results( $query );
			$output      = '';
			foreach ( $page_images as $key => $row ) {
				if ( ! isset( $_COOKIE[ 'Like_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Like_' . $row->id . '' ] = '';
				}
				if ( ! isset( $_COOKIE[ 'Dislike_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Dislike_' . $row->id . '' ] = '';
				}
				$num2       = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", (int) $row->id );
				$res3       = $wpdb->get_row( $num2 );
				$num3       = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Like_' . $row->id . '' ] . "'", (int) $row->id );
				$res4       = $wpdb->get_row( $num3 );
				$num4       = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Dislike_' . $row->id . '' ] . "'", (int) $row->id );
				$res5       = $wpdb->get_row( $num4 );
				$link       = $row->sl_url;
				$video_name =
					str_replace( '__5_5_5__', '%', $row->name );
				$id         = $row->id;
				$descnohtml = strip_tags(
					str_replace( '__5_5_5__', '%', $row->description ) );
				$result     = substr( $descnohtml, 0, 50 );
				if ( $video_name == '' && ( empty( $row->sl_url ) || $row->sl_url == '' ) ) {
					$no_title = 'no-title';
				} else {
					$no_title = '';
				}
				?>
				<?php
				$imagerowstype = $row->sl_type;
				if ( $row->sl_type == '' ) {
					$imagerowstype = 'image';
				}
				switch ( $imagerowstype ) {
					case 'image':
						?>
						<?php
						if ( $gallery_default_params['gallery_img_image_natural_size_contentpopup'] == 'natural' ) {
							$imgurl = $row->image_url;
						} else {
							$imgurl = esc_url( gallery_img_get_image_by_sizes_and_src( $row->image_url, array(
								$gallery_default_params['gallery_img_ht_view2_element_width'],
								$gallery_default_params['gallery_img_ht_view2_element_height']
							), false ) );
						} ?>
						<?php if ( $row->image_url != ';' ) {
						$video = '<img id="wd-cl-img' . $key . '" src="' . $imgurl . '" alt="" />';
					} else {
						$video = '<img id="wd-cl-img' . $key . '" src="images/noimage.jpg" alt="" />';
					} ?>
						<?php
						break;
				}
				?>
				<?php if ( $row->sl_url == '' || empty( $row->sl_url ) ) {
					$button = '';
				} else {
					if ( $row->link_target == "on" ) {
						$target = 'target="_blank"';
					} else {
						$target = '';
					}
					$button = '<div class="button-block"><a href="' . $row->sl_url . '" ' . $target . ' >' . $_POST['linkbutton'] . '</a></div>';
				}
				?>
				<?php
				$thumb_status_like = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
					$thumb_status_like = $res3->image_status;
				} elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
					$thumb_status_like = $res4->image_status;
				} else {
					$thumb_status_like = 'unliked';
				}
				$thumb_status_dislike = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
					$thumb_status_dislike = $res3->image_status;
				} elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
					$thumb_status_dislike = $res5->image_status;
				} else {
					$thumb_status_dislike = 'unliked';
				}
				$likeIcon = '';
				if ( $likeStyle == 'heart' ) {
					$likeIcon = '<i class="hugeiticons-heart likeheart"></i>';
				} elseif ( $likeStyle == 'dislike' ) {
					$likeIcon = '<i class="hugeiticons-thumbs-up like_thumb_up"></i>';
				}
				$likeCount = '';
				if ( $likeStyle != 'heart' ) {
					$likeCount = $row->like;
				}
				$thumb_text_like = '';
				if ( $likeStyle == 'heart' ) {
					$thumb_text_like = $row->like;
				}
				$displayCount = '';
				if ( $ratingCount == 'off' ) {
					$displayCount = 'huge_it_hide';
				}
				if ( $likeStyle != 'heart' ) {
					$dislikeHtml = '<div class="huge_it_gallery_dislike_wrapper">
                                <span class="huge_it_dislike">
                                    <i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
                                    <span class="huge_it_dislike_thumb" id="' . $row->id . '" data-status="' . $thumb_status_dislike . '"></span>
                                    <span class="huge_it_dislike_count ' . $displayCount . '" id="' . $row->id . '">' . $row->dislike . '</span>
                                </span>
                            </div>';
				}
/////////////////////////////
				if ( $likeStyle != 'off' ) {
					$likeCont = '<div class="huge_it_gallery_like_cont_' . $idofgallery . $pID . '">
                                <div class="huge_it_gallery_like_wrapper">
                                    <span class="huge_it_like">' . $likeIcon . '
                                        <span class="huge_it_like_thumb" id="' . $row->id . '" data-status="' . $thumb_status_like . '">' . $thumb_text_like . '</span>
                                        <span class="huge_it_like_count ' . $displayCount . '" id="' . $row->id . '">' . $likeCount . '</span>
                                    </span>
                                </div>' . $dislikeHtml . '
                           </div>';
				}
///////////////////////////////
				$output .= '<div class="element ' . $no_title . ' element_' . $idofgallery . ' " tabindex="0" data-symbol="' . $video_name . '"  data-category="alkaline-earth">';
				$output .= '<input type="hidden" class="pagenum" value="' . $page . '" />';
				$output .= '<div class="image-block image-block_' . $idofgallery . '">';
				$output .= $video;
				$output .= '<div class="gallery-image-overlay"><a href="#' . $id . '" title="' . $video_name . '"></a>' . $likeCont . '
                         </div>';
				$output .= '</div>';
				$output .= '<div class="title-block_' . $idofgallery . '" title="' . $video_name . '">';
				$output .= '<h3>' . $video_name . '</h3>';
				$output .= $button;
				$output .= '</div>';
				$output .= '</div>';
			}
			echo json_encode( array( "success" => $output ) );
			die();
		}
	}

///////////////////////////////////////////////////////////////////////////////////////////////
	public static function load_lightbox() {
		if ( ! isset( $_POST['galleryImgLightboxLoadNonce'] ) || ! wp_verify_nonce( $_POST['galleryImgLightboxLoadNonce'], 'gallery_img_lightbox_load_nonce' ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		global $huge_it_ip;
		$page = 1;
		if ( ! empty( $_POST["page"] ) && is_numeric( $_POST['page'] ) && $_POST['page'] > 0 ) {
			$page        = intval( $_POST["page"] );
			$num         = intval( $_POST["perpage"] );
			$start       = $page * $num - $num;
			$idofgallery = intval( $_POST["galleryid"] );
			$pID         = intval( $_POST["pID"] );
			$likeStyle   = esc_html( $_POST['likeStyle'] );
			$ratingCount = esc_html( $_POST['ratingCount'] );
			$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' order by ordering ASC LIMIT %d,%d", $idofgallery, $start, $num );
			$page_images = $wpdb->get_results( $query );
			$output      = '';
			foreach ( $page_images as $key => $row ) {
				if ( ! isset( $_COOKIE[ 'Like_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Like_' . $row->id . '' ] = '';
				}
				if ( ! isset( $_COOKIE[ 'Dislike_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Dislike_' . $row->id . '' ] = '';
				}
				$num2       = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", (int) $row->id );
				$res3       = $wpdb->get_row( $num2 );
				$num3       = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Like_' . $row->id . '' ] . "'", (int) $row->id );
				$res4       = $wpdb->get_row( $num3 );
				$num4       = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Dislike_' . $row->id . '' ] . "'", (int) $row->id );
				$res5       = $wpdb->get_row( $num4 );
				$link       = $row->sl_url;
				$video_name =
					str_replace( '__5_5_5__', '%', $row->name );
				$descnohtml = strip_tags( str_replace( '__5_5_5__', '%', $row->description ) );
				$result     = substr( $descnohtml, 0, 50 );
				?>
				<?php
				$imagerowstype = $row->sl_type;
				if ( $row->sl_type == '' ) {
					$imagerowstype = 'image';
				}
				switch ( $imagerowstype ) {
					case 'image':
						?>
						<?php $imgurl = explode( ";", $row->image_url ); ?>
						<?php
						if ( $row->image_url != ';' ) {
							$video = '<a href="' . $imgurl[0] . '" title="' . $video_name . '"><img id="wd-cl-img' . $key . '" src="' . esc_url( gallery_img_get_image_by_sizes_and_src(
									$imgurl[0], array(
									$gallery_default_params['gallery_img_ht_view6_width'],
									''
								), false
								) ) . '" alt="' . $video_name . '" /></a>';
						} else {
							$video = '<img id="wd-cl-img' . $key . '" src="images/noimage.jpg" alt="" />';
						} ?>
						<?php
						break;
				}
				?>
				<?php if (
					str_replace( '__5_5_5__', '%', $row->name ) != ""
				) {
					if ( $row->link_target == "on" ) {
						$target = 'target="_blank"';
					} else {
						$target = '';
					}
					$linkimg = '<div class="title-block_' . $idofgallery . '" title="' . $video_name . '">';
					if ( $link != '' || ! empty( $link ) ) {
						$linkimg .= '<a href="' . $link . '"' . $target . '>';
					}
					$linkimg .= $video_name;
					if ( $link != '' || ! empty( $link ) ) {
						$linkimg .= '</a>';
					}
					$linkimg .= '</div>';
				} else {
					$linkimg = '';
				}
				?>
				<?php
				$thumb_status_like = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
					$thumb_status_like = $res3->image_status;
				} elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
					$thumb_status_like = $res4->image_status;
				} else {
					$thumb_status_like = 'unliked';
				}
				$thumb_status_dislike = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
					$thumb_status_dislike = $res3->image_status;
				} elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
					$thumb_status_dislike = $res5->image_status;
				} else {
					$thumb_status_dislike = 'unliked';
				}
				$likeIcon = '';
				if ( $likeStyle == 'heart' ) {
					$likeIcon = '<i class="hugeiticons-heart likeheart"></i>';
				} elseif ( $likeStyle == 'dislike' ) {
					$likeIcon = '<i class="hugeiticons-thumbs-up like_thumb_up"></i>';
				}
				$likeCount = '';
				if ( $likeStyle != 'heart' ) {
					$likeCount = $row->like;
				}
				$thumb_text_like = '';
				if ( $likeStyle == 'heart' ) {
					$thumb_text_like = $row->like;
				}
				$displayCount = '';
				if ( $ratingCount == 'off' ) {
					$displayCount = 'huge_it_hide';
				}
				if ( $likeStyle != 'heart' ) {
					$dislikeHtml = '<div class="huge_it_gallery_dislike_wrapper">
                                <span class="huge_it_dislike">
                                    <i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
                                    <span class="huge_it_dislike_thumb" id="' . $row->id . '" data-status="' . $thumb_status_dislike . '">
                                    </span>
                                    <span class="huge_it_dislike_count ' . $displayCount . '" id="' . $row->id . '">' . $row->dislike . '</span>
                                </span>
                            </div>';
				}
/////////////////////////////
				if ( $likeStyle != 'off' ) {
					$likeCont = '<div class="huge_it_gallery_like_cont_' . $idofgallery . $pID . '">
                                <div class="huge_it_gallery_like_wrapper">
                                    <span class="huge_it_like">' . $likeIcon . '
                                        <span class="huge_it_like_thumb" id="' . $row->id . '" data-status="' . $thumb_status_like . '">' . $thumb_text_like . '</span>
                                        <span class="huge_it_like_count ' . $displayCount . '" id="' . $row->id . '">' . $likeCount . '</span>
                                    </span>
                                </div>' . $dislikeHtml . '
                           </div>';
				}
///////////////////////////////
				$output .= '<div class="element element_' . $idofgallery . '" tabindex="0" data-symbol="' . $video_name . '"  data-category="alkaline-earth">';
				$output .= '<input type="hidden" class="pagenum" value="' . $page . '" />';
				$output .= '<div class="image-block_' . $idofgallery . '">';
				$output .= $video;
				$output .= $linkimg;
				$output .= $likeCont;
				$output .= '</div>';
				$output .= '</div>';
			}
			echo json_encode( array( "success" => $output ) );
			die();
		}
	}

	public static function load_justifuied() {
		if ( ! isset( $_POST['galleryImgJustifiedLoadNonce'] ) || ! wp_verify_nonce( $_POST['galleryImgJustifiedLoadNonce'], 'gallery_img_justified_load_nonce' ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		global $huge_it_ip;
		$page = 1;
		if ( ! empty( $_POST["page"] ) && is_numeric( $_POST['page'] ) && $_POST['page'] > 0 ) {
			$page        = intval( $_POST["page"] );
			$num         = intval( $_POST["perpage"] );
			$start       = $page * $num - $num;
			$idofgallery = intval( $_POST["galleryid"] );
			$pID         = intval( $_POST["pID"] );
			$likeStyle   = esc_html( $_POST['likeStyle'] );
			$ratingCount = esc_html( $_POST['ratingCount'] );
			$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' order by ordering ASC LIMIT %d,%d", $idofgallery, $start, $num );
			$output      = '';
			$page_images = $wpdb->get_results( $query );
			foreach ( $page_images as $key => $row ) {
				if ( ! isset( $_COOKIE[ 'Like_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Like_' . $row->id . '' ] = '';
				}
				if ( ! isset( $_COOKIE[ 'Dislike_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Dislike_' . $row->id . '' ] = '';
				}
				$num2              = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", (int) $row->id );
				$res3              = $wpdb->get_row( $num2 );
				$num3              = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Like_' . $row->id . '' ] . "'", (int) $row->id );
				$res4              = $wpdb->get_row( $num3 );
				$num4              = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Dislike_' . $row->id . '' ] . "'", (int) $row->id );
				$res5              = $wpdb->get_row( $num4 );
				$video_name        = str_replace( '__5_5_5__', '%', $row->name );
				$videourl          = gallery_img_get_video_id_from_url( $row->image_url );
				$imgurl            = explode( ";", $row->image_url );
				$image_prefix      = "_huge_it_small_gallery";
				$imagerowstype     = $row->sl_type;
				$thumb_status_like = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
					$thumb_status_like = $res3->image_status;
				} elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
					$thumb_status_like = $res4->image_status;
				} else {
					$thumb_status_like = 'unliked';
				}
				$thumb_status_dislike = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
					$thumb_status_dislike = $res3->image_status;
				} elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
					$thumb_status_dislike = $res5->image_status;
				} else {
					$thumb_status_dislike = 'unliked';
				}
				$likeIcon = '';
				if ( $likeStyle == 'heart' ) {
					$likeIcon = '<i class="hugeiticons-heart likeheart"></i>';
				} elseif ( $likeStyle == 'dislike' ) {
					$likeIcon = '<i class="hugeiticons-thumbs-up like_thumb_up"></i>';
				}
				$likeCount = '';
				if ( $likeStyle != 'heart' ) {
					$likeCount = $row->like;
				}
				$thumb_text_like = '';
				if ( $likeStyle == 'heart' ) {
					$thumb_text_like = $row->like;
				}
				$displayCount = '';
				if ( $ratingCount == 'off' ) {
					$displayCount = 'huge_it_hide';
				}
				if ( $likeStyle != 'heart' ) {
					$dislikeHtml = '<div class="huge_it_gallery_dislike_wrapper">
                                <span class="huge_it_dislike">
                                    <i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
                                    <span class="huge_it_dislike_thumb" id="' . $row->id . '" data-status="' . $thumb_status_dislike . '">
                                    </span>
                                    <span class="huge_it_dislike_count ' . $displayCount . '" id="' . $row->id . '">' . $row->dislike . '</span>
                                </span>
                            </div>';
				}
/////////////////////////////
				if ( $likeStyle != 'off' ) {
					$likeCont = '<div class="huge_it_gallery_like_cont_' . $idofgallery . $pID . '">
                                <div class="huge_it_gallery_like_wrapper">
                                    <span class="huge_it_like">' . $likeIcon . '
                                        <span class="huge_it_like_thumb" id="' . $row->id . '" data-status="' . $thumb_status_like . '">' . $thumb_text_like . '
                                        </span>
                                        <span class="huge_it_like_count ' . $displayCount . '" id="' . $row->id . '">' . $likeCount . '</span>
                                    </span>
                                </div>' . $dislikeHtml . '
                           </div>';
				}
///////////////////////////////
				if ( $row->sl_type == '' ) {
					$imagerowstype = 'image';
				}
				switch ( $imagerowstype ) {
					case 'image':
						if ( $row->image_url != ';' ) {
							$imgperfix = esc_url( gallery_img_get_image_by_sizes_and_src( $imgurl[0], array(
								'',
								$gallery_default_params['gallery_img_ht_view8_element_height']
							), false ) );
							$video     = '<a class="gallery_group' . $idofgallery . '" href="' . $imgurl[0] . '" title="' . $video_name . '">
                                            <img  id="wd-cl-img' . $key . '" alt="' . $video_name . '" src="' . $imgperfix . '"/>
                                            ' . $likeCont . '
                                        </a>
                                        <input type="hidden" class="pagenum" value="' . $page . '" />'; ?>
						<?php } else {
							$video = '<img alt="' . $video_name . '" id="wd-cl-img' . $key . '" src="images/noimage.jpg"  />
                                                ' . $likeCont . '
                                        <input type="hidden" class="pagenum" value="' . $page . '" />';
						} ?>
						<?php
						break;
				}
				$output .= $video . '<input type="hidden" class="pagenum" value="' . $page . '" />';
			}
			echo json_encode( array( "success" => $output ) );
			die();
		}
	}

	public static function load_thumbnail() {
		if ( ! isset( $_POST['galleryImgThumbnailLoadNonce'] ) || ! wp_verify_nonce( $_POST['galleryImgThumbnailLoadNonce'], 'gallery_img_thumbnail_load_nonce' ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		global $huge_it_ip;
		$page = 1;
		if ( ! empty( $_POST["page"] ) && is_numeric( $_POST['page'] ) && $_POST['page'] > 0 ) {
			$page        = intval( $_POST["page"] );
			$num         = intval( $_POST["perpage"] );
			$start       = $page * $num - $num;
			$idofgallery = intval( $_POST["galleryid"] );
			$pID         = intval( $_POST["pID"] );
			$likeStyle   = esc_html( $_POST['likeStyle'] );
			$ratingCount = esc_html( $_POST['ratingCount'] );
			$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' and sl_type='image' order by ordering ASC LIMIT %d,%d", $idofgallery, $start, $num );
			$output      = '';
			$page_images = $wpdb->get_results( $query );
			foreach ( $page_images as $key => $row ) {
				if ( ! isset( $_COOKIE[ 'Like_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Like_' . $row->id . '' ] = '';
				}
				if ( ! isset( $_COOKIE[ 'Dislike_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Dislike_' . $row->id . '' ] = '';
				}
				$num2          = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", (int) $row->id );
				$res3          = $wpdb->get_row( $num2 );
				$num3          = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Like_' . $row->id . '' ] . "'", (int) $row->id );
				$res4          = $wpdb->get_row( $num3 );
				$num4          = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Dislike_' . $row->id . '' ] . "'", (int) $row->id );
				$res5          = $wpdb->get_row( $num4 );
				$video_name    = str_replace( '__5_5_5__', '%', $row->name );
				$imgurl        = explode( ";", $row->image_url );
				$image_prefix  = "_huge_it_small_gallery";
				$videourl      = gallery_img_get_video_id_from_url( $row->image_url );
				$imagerowstype = $row->sl_type;
				if ( $row->sl_type == '' ) {
					$imagerowstype = 'image';
				}
				switch ( $imagerowstype ) {
					case 'image':
						if ( $gallery_default_params['gallery_img_image_natural_size_thumbnail'] == 'resize' ) {
							$imgperfix = esc_url( gallery_img_get_image_by_sizes_and_src( $imgurl[0], array(
								$gallery_default_params['gallery_img_thumb_image_width'],
								$gallery_default_params['gallery_img_thumb_image_height']
							), false ) );
						} else {
							$imgperfix = $imgurl[0];
						}
						$video = '<a class="gallery_group' . $idofgallery . '" href="' . $row->image_url . '" title="' . $video_name . '"></a>
                            <img  src="' . $imgperfix . '" alt="' . $video_name . '" />';
						break;
				}
				?>
				<?php
				$thumb_status_like = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
					$thumb_status_like = $res3->image_status;
				} elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
					$thumb_status_like = $res4->image_status;
				} else {
					$thumb_status_like = 'unliked';
				}
				$thumb_status_dislike = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
					$thumb_status_dislike = $res3->image_status;
				} elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
					$thumb_status_dislike = $res5->image_status;
				} else {
					$thumb_status_dislike = 'unliked';
				}
				$likeIcon = '';
				if ( $likeStyle == 'heart' ) {
					$likeIcon = '<i class="hugeiticons-heart likeheart"></i>';
				} elseif ( $likeStyle == 'dislike' ) {
					$likeIcon = '<i class="hugeiticons-thumbs-up like_thumb_up"></i>';
				}
				$likeCount = '';
				if ( $likeStyle != 'heart' ) {
					$likeCount = $row->like;
				}
				$thumb_text_like = '';
				if ( $likeStyle == 'heart' ) {
					$thumb_text_like = $row->like;
				}
				$displayCount = '';
				if ( $ratingCount == 'off' ) {
					$displayCount = 'huge_it_hide';
				}
				if ( $likeStyle != 'heart' ) {
					$dislikeHtml = '<div class="huge_it_gallery_dislike_wrapper">
                                <span class="huge_it_dislike">
                                    <i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
                                    <span class="huge_it_dislike_thumb" id="' . $row->id . '" data-status="' . $thumb_status_dislike . '">
                                    </span>
                                    <span class="huge_it_dislike_count ' . $displayCount . '" id="' . $row->id . '">' . $row->dislike . '</span>
                                </span>
                            </div>';
				}
/////////////////////////////
				if ( $likeStyle != 'off' ) {
					$likeCont = '<div class="huge_it_gallery_like_cont_' . $idofgallery . $pID . '">
                                <div class="huge_it_gallery_like_wrapper">
                                    <span class="huge_it_like">' . $likeIcon . '
                                        <span class="huge_it_like_thumb" id="' . $row->id . '" data-status="' . $thumb_status_like . '">' . $thumb_text_like . '
                                        </span>
                                        <span class="huge_it_like_count ' . $displayCount . '" id="' . $row->id . '">' . $likeCount . '</span>
                                    </span>
                                </div>' . $dislikeHtml . '
                           </div>';
				}
///////////////////////////////
				$output .= '
                <li class="huge_it_big_li">
                     ' . $likeCont . '<input type="hidden" class="pagenum" value="' . $page . '" />
                        ' . $video . '
                    <div class="overLayer"></div>
                    <div class="infoLayer">
                        <ul>
                            <li>
                                <h2>
                                    ' . $video_name . '
                                </h2>
                            </li>
                            <li>
                                <p>
                                    ' . esc_html( $_POST['thumbtext'] ) . '
                                </p>
                            </li>
                        </ul>
                    </div>
                </li>
            ';
			}
			echo json_encode( array( "success" => $output ) );
			die();
		}
	}

	public static function load_blog_view() {
		if ( ! isset( $_POST['galleryImgBlogLoadNonce'] ) || ! wp_verify_nonce( $_POST['galleryImgBlogLoadNonce'], 'gallery_img_blog_load_nonce' ) ) {
			wp_die( 'Security check fail' );
		}
		global $wpdb;
		global $huge_it_ip;
		$page = 1;
		if ( ! empty( $_POST["page"] ) && is_numeric( $_POST['page'] ) && $_POST['page'] > 0 ) {
			$page        = intval( $_POST["page"] );
			$num         = intval( $_POST["perpage"] );
			$start       = $page * $num - $num;
			$idofgallery = intval( $_POST["galleryid"] );
			$pID         = intval( $_POST["pID"] );
			$likeStyle   = esc_html( $_POST['likeStyle'] );
			$ratingCount = esc_html( $_POST['ratingCount'] );
			$query       = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = '%d' order by ordering ASC LIMIT %d,%d", $idofgallery, $start, $num );
			$output      = '';
			$page_images = $wpdb->get_results( $query );
			foreach ( $page_images as $key => $row ) {
				$img2video = '';
				if ( ! isset( $_COOKIE[ 'Like_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Like_' . $row->id . '' ] = '';
				}
				if ( ! isset( $_COOKIE[ 'Dislike_' . $row->id . '' ] ) ) {
					$_COOKIE[ 'Dislike_' . $row->id . '' ] = '';
				}
				$num2          = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", (int) $row->id );
				$res3          = $wpdb->get_row( $num2 );
				$num3          = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Like_' . $row->id . '' ] . "'", (int) $row->id );
				$res4          = $wpdb->get_row( $num3 );
				$num4          = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Dislike_' . $row->id . '' ] . "'", (int) $row->id );
				$res5          = $wpdb->get_row( $num4 );
				$img_src       = $row->image_url;
				$img_name      = str_replace( '__5_5_5__', '%', $row->name );
				$img_desc      = str_replace( '__5_5_5__', '%', $row->description );
				$videourl      = gallery_img_get_video_id_from_url( $row->image_url );
				$imagerowstype = $row->sl_type;
				$img3video     = '';
				if ( $imagerowstype == '' ) {
					$imagerowstype = 'image';
				}
				if ( $imagerowstype == 'image' ) {
					$img2video .= '<img class="view9_img" src="' . $img_src . '">';
				} else {
					if ( $videourl[1] == 'youtube' ) {
						$img3video .= '<div class="iframe_cont">
                                        <iframe class="video_blog_view" src="//www.youtube.com/embed/' . $videourl[0] . '" style="border: 0;" allowfullscreen></iframe>
                                    </div>';
					} else {
						$img3video .= '<div class="iframe_cont">
                                                <iframe class="video_blog_view" src="//player.vimeo.com/video/' . $videourl[0] . '" style="border: 0;" allowfullscreen></iframe>
                                            </div>';
					}
				}
				if ( $imagerowstype == 'image' ) {
					$link_img_video = $img2video;
				} else {
					$link_img_video = $img3video;
				}
				$thumb_status_like = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
					$thumb_status_like = $res3->image_status;
				} elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
					$thumb_status_like = $res4->image_status;
				} else {
					$thumb_status_like = 'unliked';
				}
				$thumb_status_dislike = '';
				if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
					$thumb_status_dislike = $res3->image_status;
				} elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
					$thumb_status_dislike = $res5->image_status;
				} else {
					$thumb_status_dislike = 'unliked';
				}
				$likeIcon = '';
				if ( $likeStyle == 'heart' ) {
					$likeIcon = '<i class="hugeiticons-heart likeheart"></i>';
				} elseif ( $likeStyle == 'dislike' ) {
					$likeIcon = '<i class="hugeiticons-thumbs-up like_thumb_up"></i>';
				}
				$likeCount = '';
				if ( $likeStyle != 'heart' ) {
					$likeCount = $row->like;
				}
				$thumb_text_like = '';
				if ( $likeStyle == 'heart' ) {
					$thumb_text_like = $row->like;
				}
				$displayCount = '';
				if ( $ratingCount == 'off' ) {
					$displayCount = 'huge_it_hide';
				}
				if ( $likeStyle != 'heart' ) {
					$dislikeHtml = '<div class="huge_it_gallery_dislike_wrapper">
                                <span class="huge_it_dislike">
                                    <i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
                                    <span class="huge_it_dislike_thumb" id="' . $row->id . '" data-status="' . $thumb_status_dislike . '">
                                    </span>
                                    <span class="huge_it_dislike_count ' . $displayCount . '" id="' . $row->id . '">' . $row->dislike . '</span>
                                </span>
                            </div>';
				}
/////////////////////////////
				if ( $likeStyle != 'off' ) {
					$likeCont = '<div class="huge_it_gallery_like_cont_' . $idofgallery . $pID . '">
                                <div class="huge_it_gallery_like_wrapper">
                                    <span class="huge_it_like">' . $likeIcon . '
                                        <span class="huge_it_like_thumb" id="' . $row->id . '" data-status="' . $thumb_status_like . '">' . $thumb_text_like . '
                                        </span>
                                        <span class="huge_it_like_count ' . $displayCount . '" id="' . $row->id . '">' . $likeCount . '</span>
                                    </span>
                                </div>' . $dislikeHtml . '
                           </div>';
				}
///////////////////////////////
				if ( $likeStyle != 'heart' ) {
					$output .= '<div class="view9_container">
                                <input type="hidden" class="pagenum" value="' . $page . '" />
                                <h1 class="new_view_title">' . $img_name . '</h1>' . $link_img_video . '
                                <div class="new_view_desc">' . $img_desc . '</div>' . $likeCont . '</div>
                          <div class="clear"></div>';
				}
				if ( $likeStyle == 'heart' ) {
					$output .= '<div class="view9_container">
                                <input type="hidden" class="pagenum" value="' . $page . '" />
                                <h1 class="new_view_title">' . $img_name . '</h1><div class="blog_img_wrapper">' . $link_img_video . $likeCont . '</div>
                                <div class="new_view_desc">' . $img_desc . '</div></div>
                          <div class="clear"></div>';
				}
			}
		}
		echo json_encode( array( "success" => $output, "typeOfres" => $imagerowstype ) );
		die();
	}

	public static function like_action() {
		$huge_it_ip = '';
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$huge_it_ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$huge_it_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$huge_it_ip = $_SERVER['REMOTE_ADDR'];
		}
		global $wpdb;

		if ( ! isset( $_POST['image_id'] ) || absint( $_POST['image_id'] ) != $_POST['image_id'] ) {
			echo json_encode( array( 'success' => 0 ) );
			die();
		}

		$image_id = absint( $_POST['image_id'] );
		$cook     = sanitize_text_field( $_POST['cook'] );

		$num   = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d", $image_id );
		$num2  = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", $image_id );
		$res   = $wpdb->get_results( $num );
		$res2  = $wpdb->get_results( $num, ARRAY_A );
		$res3  = $wpdb->get_row( $num2 );
		$num3  = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $cook . "'", $image_id );
		$res4  = $wpdb->get_row( $num3 );
		$resIP = '';
		for ( $i = 0; $i < count( $res2 ); $i ++ ) {
			$resIP .= $res2[ $i ]['ip'] . '|';
		}
		$arrIP = explode( "|", $resIP );
		if ( ! isset( $res3 ) && ! isset( $res4 ) ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "huge_itgallery_like_dislike (`image_id`,`image_status`,`ip`,`cook`) VALUES ( %d, 'liked', '" . $huge_it_ip . "',%s)", $image_id, $cook ) );
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `like` = `like`+1 WHERE id = %d ", $image_id ) );
			$numLike    = $wpdb->prepare( "SELECT `like` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resLike    = $wpdb->get_results( $numLike );
			$numDislike = $wpdb->prepare( "SELECT `dislike` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resDislike = $wpdb->get_results( $numDislike );
			echo json_encode( array( "like" => $resLike[0]->like, "statLike" => 'Liked' ) );
		} elseif ( ( isset( $res3 ) && $res3->image_status == 'liked' && $res3->ip == $huge_it_ip ) || ( isset( $res4 ) && $res4->image_status == 'liked' && $res4->cook == $cook ) ) {
			if ( isset( $res3 ) && $res3->image_status == 'liked' && $res3->ip == $huge_it_ip ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip`='" . $huge_it_ip . "'", $image_id ) );
			} elseif ( isset( $res4 ) && $res4->cook == $cook ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook`='" . $cook . "'", $image_id ) );
			}
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `like` = `like`-1 WHERE id = %d ", $image_id ) );
			$numLike    = $wpdb->prepare( "SELECT `like` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resLike    = $wpdb->get_results( $numLike );
			$numDislike = $wpdb->prepare( "SELECT `dislike` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resDislike = $wpdb->get_results( $numDislike );
			echo json_encode( array( "like" => $resLike[0]->like, "statLike" => 'Like' ) );
		} elseif ( ( isset( $res3 ) && $res3->image_status == 'disliked' && $res3->ip == $huge_it_ip ) || ( isset( $res4 ) && $res4->image_status == 'disliked' && $res4->cook == $cook ) ) {
			if ( isset( $res3 ) && $res3->image_status == 'disliked' && $res3->ip == $huge_it_ip ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip`='" . $huge_it_ip . "'", $image_id ) );
			} elseif ( isset( $res4 ) && $res4->image_status == 'disliked' && $res4->cook == $cook ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook`='" . $cook . "'", $image_id ) );
			}
			$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "huge_itgallery_like_dislike (`image_id`,`image_status`,`ip`,`cook`) VALUES ( %d, 'liked', '" . $huge_it_ip . "',%s)", $image_id, $cook ) );
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `like` = `like`+1 WHERE id = %d ", $image_id ) );
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `dislike` = `dislike`-1 WHERE id = %d ", $image_id ) );
			$numLike    = $wpdb->prepare( "SELECT `like` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resLike    = $wpdb->get_results( $numLike );
			$numDislike = $wpdb->prepare( "SELECT `dislike` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resDislike = $wpdb->get_results( $numDislike );
			echo json_encode( array(
				"like"        => $resLike[0]->like,
				"dislike"     => $resDislike[0]->dislike,
				"statLike"    => 'Liked',
				"statDislike" => 'Dislike'
			) );
		}
		die();
	}

	public static function dislike_action() {
		$huge_it_ip = '';
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$huge_it_ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$huge_it_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$huge_it_ip = $_SERVER['REMOTE_ADDR'];
		}
		global $wpdb;

		if ( ! isset( $_POST['image_id'] ) || absint( $_POST['image_id'] ) != $_POST['image_id'] ) {
			echo json_encode( array( 'success' => 0 ) );
			die();
		}

		$image_id = absint( $_POST['image_id'] );
		$cook     = sanitize_text_field( $_POST['cook'] );

		$num   = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d", $image_id );
		$num2  = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", $image_id );
		$res   = $wpdb->get_results( $num );
		$res2  = $wpdb->get_results( $num, ARRAY_A );
		$res3  = $wpdb->get_row( $num2 );
		$num3  = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $cook . "'", $image_id );
		$res4  = $wpdb->get_row( $num3 );
		$resIP = '';
		for ( $i = 0; $i < count( $res2 ); $i ++ ) {
			$resIP .= $res2[ $i ]['ip'] . '|';
		}
		$arrIP = explode( "|", $resIP );
		if ( ! isset( $res3 ) && ! isset( $res4 ) ) {
			$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "huge_itgallery_like_dislike (`image_id`,`image_status`,`ip`,`cook`) VALUES ( %d, 'disliked', '" . $huge_it_ip . "',%s)", $image_id, $cook ) );
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `dislike` = `dislike`+1 WHERE id = %d ", $image_id ) );
			$numDislike = $wpdb->prepare( "SELECT `dislike` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resDislike = $wpdb->get_results( $numDislike );
			$numLike    = $wpdb->prepare( "SELECT `like` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resLike    = $wpdb->get_results( $numLike );
			echo json_encode( array( "dislike" => $resDislike[0]->dislike, "statDislike" => 'Disliked' ) );
		} elseif ( ( isset( $res3 ) && $res3->image_status == 'disliked' && $res3->ip == $huge_it_ip ) || ( isset( $res4 ) && $res4->image_status == 'disliked' && $res4->cook == $cook ) ) {
			if ( isset( $res3 ) && $res3->image_status == 'disliked' && $res3->ip == $huge_it_ip ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip`='" . $huge_it_ip . "'", $image_id ) );
			} elseif ( isset( $res4 ) && $res4->image_status == 'disliked' && $res4->cook == $cook ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook`='" . $cook . "'", $image_id ) );
			}
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `dislike` = `dislike`-1 WHERE id = %d ", $image_id ) );
			$numDislike = $wpdb->prepare( "SELECT `dislike` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resDislike = $wpdb->get_results( $numDislike );
			$numLike    = $wpdb->prepare( "SELECT `like` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resLike    = $wpdb->get_results( $numLike );
			echo json_encode( array( "dislike" => $resDislike[0]->dislike, "statDislike" => 'Dislike' ) );
		} elseif ( ( isset( $res3 ) && $res3->image_status == 'liked' && $res3->ip == $huge_it_ip ) || ( isset( $res4 ) && $res4->image_status == 'liked' && $res4->cook == $cook ) ) {
			if ( isset( $res3 ) && $res3->image_status == 'liked' && $res3->ip == $huge_it_ip ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip`='" . $huge_it_ip . "'", $image_id ) );
			} elseif ( isset( $res4 ) && $res4->image_status == 'liked' && $res4->cook == $cook ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook`='" . $cook . "'", $image_id ) );
			}
			$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "huge_itgallery_like_dislike (`image_id`,`image_status`,`ip`,`cook`) VALUES ( %d, 'disliked', '" . $huge_it_ip . "',%s)", $image_id, $cook ) );
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `dislike` = `dislike`+1 WHERE id = %d ", $image_id ) );
			$wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `like` = `like`-1 WHERE id = %d ", $image_id ) );
			$numDislike = $wpdb->prepare( "SELECT `dislike` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resDislike = $wpdb->get_results( $numDislike );
			$numLike    = $wpdb->prepare( "SELECT `like` FROM " . $wpdb->prefix . "huge_itgallery_images WHERE id = %d LIMIT 1", $image_id );
			$resLike    = $wpdb->get_results( $numLike );
			echo json_encode( array(
				"like"        => $resLike[0]->like,
				"dislike"     => $resDislike[0]->dislike,
				"statLike"    => 'Like',
				"statDislike" => 'Disliked'
			) );
		}
		die();
	}
}
