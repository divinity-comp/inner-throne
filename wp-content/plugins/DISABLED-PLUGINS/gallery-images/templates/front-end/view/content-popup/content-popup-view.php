<section id="huge_it_gallery_content_<?php echo $galleryID; ?>" class="gallery-img-content"
		 data-gallery-id="<?php echo $galleryID; ?>"
		 data-content-per-page="<?php echo $num; ?>"
         data-image-behaviour="<?php echo $gallery_default_params[ 'gallery_img_image_natural_size_contentpopup' ]; ?>"
         data-rating-type="<?php echo $like_dislike; ?>">
	<div id="huge_it_gallery_container_<?php echo $galleryID; ?>"
	     class="huge_it_gallery_container super-list variable-sizes clearfix view-<?php echo $view_slug; ?>"
	     data-show-center="<?php echo $gallery_default_params[ 'gallery_img_ht_view2_content_in_center' ]; ?>">
		<div id="huge_it_gallery_container_moving_<?php echo $galleryID; ?>">
			<input type="hidden" class="pagenum" value="1"/>
			<input type="hidden" id="total" value="<?php echo $total; ?>"/>
			<?php
			echo "count(page_images)".count($page_images);
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
				$title      = $row->name;
				$link       = str_replace( '__5_5_5__', '%', $row->sl_url );
				$descnohtml = strip_tags( str_replace( '__5_5_5__', '%', $row->description ) );
				$result     = substr( $descnohtml, 0, 50 );
				?>
				<div class="element element_<?php echo $galleryID; ?> <?php if ( $title == '' && $link == '' ) {
					echo 'no-title';
				} ?>"
				     tabindex="0"
				     data-symbol="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
				     data-category="alkaline-earth">
					<div class="image-block image-block_<?php echo $galleryID; ?>">
						<?php
						$imagerowstype = $row->sl_type;
						if ( $row->sl_type == '' ) {
							$imagerowstype = 'image';
						}
						switch ( $imagerowstype ) {
							case 'image':
								?>
								<?php $imgurl = explode( ";", $row->image_url ); ?>
								<?php if ( $row->image_url != ';' ) { ?>
								<img alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
								     id="wd-cl-img<?php echo $key; ?>"
								     src="<?php if ( $gallery_default_params[ 'gallery_img_image_natural_size_contentpopup' ] == 'resize' ) {
									     echo esc_url( gallery_img_get_image_by_sizes_and_src( $imgurl[0], array(
										     $gallery_default_params[ 'gallery_img_ht_view2_element_width' ],
										     $gallery_default_params[ 'gallery_img_ht_view2_element_height' ]
									     ), false ) );
								     } else {
									     echo $imgurl[0];
								     } ?>"/>
							<?php } else { ?>
								<img alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
								     id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg"/>
								<?php
							} ?>
								<?php
								break;
							case 'video':
								?>
								<?php
								$videourl = gallery_img_get_video_id_from_url( $row->image_url );
								if ( $videourl[1] == 'youtube' ) {
									?>
									<img alt="<?php echo str_replace( '__5_5_5__', '%', $row->name ); ?>"
									     src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"/>
									<?php
								} else {
									$hash   = unserialize( wp_remote_fopen( "http://vimeo.com/api/v2/video/" . $videourl[0] . ".php" ) );
									$imgsrc = $hash[0]['thumbnail_large'];
									?>
									<img alt="<?php echo str_replace( '__5_5_5__', '%', $row->name ); ?>"
									     src="<?php echo esc_attr( $imgsrc ); ?>"/>
									<?php
								}
								?>
								<?php
								break;
						}
						?>
						<?php if ( str_replace( '__5_5_5__', '%', $row->sl_url ) == '' ) {
							$viwMoreButton = '';
						} else {
							if ( $row->link_target == "on" ) {
								$target = 'target="_blank"';
							} else {
								$target = '';
							}
							$viwMoreButton = '<div class="button-block"><a href="' . str_replace( '__5_5_5__', '%', $row->sl_url ) . '" ' . $target . ' >' . $gallery_default_params["gallery_img_ht_view2_element_linkbutton_text"] . '</a></div>';
						}
						?>
						<div class="gallery-image-overlay">
							<a href="#<?php echo $row->id; ?>"
							   title="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"></a>
							<?php if ( $like_dislike != 'off' ): ?>
								<div
									class="huge_it_gallery_like_cont huge_it_gallery_like_cont_<?php echo $galleryID . $pID; ?>">
									<div class="huge_it_gallery_like_wrapper">
						<span class="huge_it_like">
							<?php if ( $like_dislike == 'heart' ): ?>
								<i class="hugeiticons-heart likeheart"></i>
							<?php endif; ?>
							<?php if ( $like_dislike == 'dislike' ): ?>
								<i class="hugeiticons hugeiticons-thumbs-up like_thumb_up"></i>
							<?php endif; ?>
							<span class="huge_it_like_thumb" id="<?php echo $row->id ?>"
							      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
								      echo $res3->image_status;
							      } elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
								      echo $res4->image_status;
							      } else {
								      echo 'unliked';
							      } ?>">
							<?php if ( $like_dislike == 'heart' ): ?>
								<?php echo $row->like; ?>
							<?php endif; ?>
						</span>
							<span
								class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_popup_rating_count'] == 'off' ) {
									echo 'huge_it_hide';
								} ?>"
								id="<?php echo $row->id ?>"><?php if ( $like_dislike != 'heart' ): ?><?php echo $row->like; ?><?php endif; ?></span>
						</span>
									</div>
									<?php if ( $like_dislike != 'heart' ): ?>
										<div class="huge_it_gallery_dislike_wrapper">
						<span class="huge_it_dislike">
							<i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
							<span class="huge_it_dislike_thumb" id="<?php echo $row->id ?>"
							      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
								      echo $res3->image_status;
							      } elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
								      echo $res5->image_status;
							      } else {
								      echo 'unliked';
							      } ?>">
							</span>
							<span
								class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_popup_rating_count'] == 'off' ) {
									echo 'huge_it_hide';
								} ?>"
								id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
						</span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php if ( $title != '' || $link != '' ): ?>
						<div
							class="title-block_<?php echo $galleryID; ?>">
							<?php if ( $title != '' && $title != null ) { ?>
								<h3
									title="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>">
									<?php echo str_replace( '__5_5_5__', '%', $row->name ); ?>
								</h3>
							<?php } ?>
							<?php if ( $gallery_default_params["gallery_img_ht_view2_element_show_linkbutton"] == 'on' ) { ?>
								<?php echo $viwMoreButton ?>
							<?php } ?>
						</div>
					<?php endif; ?>
				</div>
				<?php
			} ?>
		</div>
		<div style="clear:both;"></div>
	</div>
	<?php
	$a = $disp_type;

	if ( ($a == 1)&&(count($images)>$num) ) {
		$gallery_img_content_load_nonce = wp_create_nonce( 'gallery_img_content_load_nonce' );
		?>
		<div class="load_more5">
			<div class="load_more_button5"
			     data-content-nonce-value="<?php echo $gallery_img_content_load_nonce; ?>"><?php echo $gallery_default_params['gallery_img_video_ht_view1_loadmore_text']; ?></div>
			<div class="loading5"><img alt="<?php echo str_replace( '__5_5_5__', '%', $row->name ); ?>"
			                           src="<?php if ( $gallery_default_params['gallery_img_video_ht_view1_loading_type'] == '1' ) {
				                           echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading1.gif';
			                           } elseif ( $gallery_default_params['gallery_img_video_ht_view1_loading_type'] == '2' ) {
				                           echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading4.gif';
			                           } elseif ( $gallery_default_params['gallery_img_video_ht_view1_loading_type'] == '3' ) {
				                           echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading36.gif';
			                           } elseif ( $gallery_default_params['gallery_img_video_ht_view1_loading_type'] == '4' ) {
				                           echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading51.gif';
			                           } ?>">
			</div>

		</div>
		<?php
	} elseif ( $a == 0 ) {
		?>
		<div class="paginate5">
			<?php
			$protocol    = stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://';
			$actual_link = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
			$checkREQ    = '';
			$pattern     = "/\?p=/";
			$pattern2    = "/&page-img[0-9]+=[0-9]+/";
			//$res=preg_match($pattern, $actual_link);
			if ( preg_match( $pattern, $actual_link ) ) {
				if ( preg_match( $pattern2, $actual_link ) ) {
					$actual_link = preg_replace( $pattern2, '', $actual_link );
				}
				$checkREQ = $actual_link . '&page-img' . $galleryID . $pID;
			} else {
				$checkREQ = '?page-img' . $galleryID . $pID;
			}
			$pervpage = '';
			if ( $page != 1 ) {
				$pervpage = '<a href= ' . $checkREQ . '=1><i class="icon-style5 hugeiticons-fast-backward" ></i></a>  
			                               <a href= ' . $checkREQ . '=' . ( $page - 1 ) . '><i class="icon-style5 hugeiticons-chevron-left"></i></a> ';
			}
			$nextpage = '';
			if ( $page != $total ) {
				$nextpage = ' <a href= ' . $checkREQ . '=' . ( $page + 1 ) . '><i class="icon-style5 hugeiticons-chevron-right"></i></a>  
			                                   <a href= ' . $checkREQ . '=' . $total . '><i class="icon-style5 hugeiticons-fast-forward" ></i></a>';
			}
			echo $pervpage . $page . '/' . $total . $nextpage;
			?>
		</div>
		<?php
	}
	?>
</section>
<ul id="huge_it_gallery_popup_list_<?php echo $galleryID; ?>" class="huge_it_gallery_popup_list gallery-img-content"
    data-rating-type="<?php echo $like_dislike; ?>"
    data-popup-mobile-position-top="<?php echo $gallery_default_params[ 'gallery_img_ht_view2_popup_mobile_position_at_top' ]; ?>">
	<?php
	$changePopup = 1;
	foreach ( $images as $key => $row ) {
		$imgurl     = explode( ";", $row->image_url );
		$link       = str_replace( '__5_5_5__', '%', $row->sl_url );
		$descnohtml = strip_tags(
			str_replace( '__5_5_5__', '%', $row->description ) );
		$result     = substr( $descnohtml, 0, 50 );
		?>
		<li class="pupup-element" id="huge_it_gallery_pupup_element_<?php echo $row->id; ?>">
			<div class="heading-navigation heading-navigation_<?php echo $galleryID; ?>">
				<div style="display: inline-block; float: left;">
					<div class="left-change"><a href="#<?php echo $changePopup - 1; ?>"
					                            data-popupid="#<?php echo $row->id; ?>">&lt;</a></div>
					<div class="right-change"><a href="#<?php echo $changePopup + 1; ?>"
					                             data-popupid="#<?php echo $row->id; ?>">&gt;</a></div>
				</div>
				<?php $changePopup = $changePopup + 1; ?>
				<a href="#close" class="close"></a>
				<div style="clear:both;"></div>
			</div>
			<div class="popup-wrapper popup-wrapper_<?php echo $galleryID; ?>">
				<div class="image-block image-block_<?php echo $galleryID; ?>">
					<?php if ( $like_dislike == 'heart' ): ?>
						<div
							class="huge_it_gallery_like_cont huge_it_gallery_like_cont_<?php echo $galleryID . $pID; ?>">
							<div class="huge_it_gallery_like_wrapper">
						<span class="huge_it_like">
							<?php if ( $like_dislike == 'heart' ): ?>
								<i class="hugeiticons-heart likeheart"></i>
							<?php endif; ?>
							<?php if ( $like_dislike == 'dislike' ): ?>
								<i class="hugeiticons hugeiticons-thumbs-up like_thumb_up"></i>
							<?php endif; ?>
							<span class="huge_it_like_thumb" id="<?php echo $row->id ?>"
							      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
								      echo $res3->image_status;
							      } elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
								      echo $res4->image_status;
							      } else {
								      echo 'unliked';
							      } ?>">
							<?php if ( $like_dislike == 'heart' ): ?>
								<?php echo $row->like; ?>
							<?php endif; ?>
							</span>
							<span
								class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_contentsl_rating_count'] == 'off' ) {
									echo 'huge_it_hide';
								} ?>"
								id="<?php echo $row->id ?>"><?php if ( $like_dislike != 'heart' ): ?><?php echo $row->like; ?><?php endif; ?></span>
						</span>
							</div>
							<?php if ( $like_dislike != 'heart' ): ?>
								<div class="huge_it_gallery_dislike_wrapper">
						<span class="huge_it_dislike">
							<i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
							<span class="huge_it_dislike_thumb" id="<?php echo $row->id ?>"
							      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
								      echo $res3->image_status;
							      } elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
								      echo $res5->image_status;
							      } else {
								      echo 'unliked';
							      } ?>">
							</span>
							<span
								class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_contentsl_rating_count'] == 'off' ) {
									echo 'huge_it_hide';
								} ?>"
								id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
						</span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php
					$imagerowstype = $row->sl_type;
					if ( $row->sl_type == '' ) {
						$imagerowstype = 'image';
					}
					switch ( $imagerowstype ) {
						case 'image':
							?>
							<?php if ( $row->image_url != ';' ) { ?>
							<img alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
							     id="wd-cl-big-img<?php echo $key; ?>" src="<?php echo esc_attr( $imgurl[0] ); ?>"/>
						<?php } else { ?>
							<img alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
							     id="wd-cl-big-img<?php echo $key; ?>" src="images/noimage.jpg"/>
							<?php
						} ?>
							<?php
							break;
						case 'video':
							?>
							<?php
							$videourl = gallery_img_get_video_id_from_url( $row->image_url );
							if ( $videourl[1] == 'youtube' ) {
								?>
								<iframe src="//www.youtube.com/embed/<?php echo $videourl[0]; ?>" frameborder="0"
								        allowfullscreen></iframe>
								<?php
							} else {
								?>
								<iframe
									src="//player.vimeo.com/video/<?php echo $videourl[0]; ?>?title=0&amp;byline=0&amp;portrait=0"
									frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
								<?php
							}
							?>
							<?php
							break;
					}
					?>
					<?php if ( str_replace( '__5_5_5__', '%', $row->sl_url ) == '' ) {
						$viwMoreButton = '';
					} else {
						if ( $row->link_target == "on" ) {
							$target = 'target="_blank"';
						} else {
							$target = '';
						}
						$viwMoreButton = '<div class="button-block"><a href="' . str_replace( '__5_5_5__', '%', $row->sl_url ) . '" ' . $target . ' >' . $gallery_default_params["gallery_img_ht_view2_popup_linkbutton_text"] . '</a></div>';
					}
					?>
				</div>
				<div
					class="right-block"><?php if ( $gallery_default_params["gallery_img_ht_view2_show_popup_title"] == 'on' && $row->name != '' && $row->name != null ) { ?>
						<h3 class="title"><?php echo str_replace( '__5_5_5__', '%', $row->name ); ?></h3><?php } ?>
					<?php if ( $gallery_default_params["gallery_img_ht_view2_show_description"] == 'on' ) { ?>
						<div class="description"><?php echo str_replace( '__5_5_5__', '%', $row->description ); ?></div>
					<?php } ?>
					<?php if ( $like_dislike != 'off' && $like_dislike != 'heart' ): ?>
						<div
							class="huge_it_gallery_like_cont huge_it_gallery_like_cont_<?php echo $galleryID . $pID; ?>">
							<div class="huge_it_gallery_like_wrapper">
						<span class="huge_it_like">
							<?php if ( $like_dislike == 'heart' ): ?>
								<i class="hugeiticons-heart likeheart"></i>
							<?php endif; ?>
							<?php if ( $like_dislike == 'dislike' ): ?>
								<i class="hugeiticons-thumbs-up like_thumb_up"></i>
							<?php endif; ?>
							<span class="huge_it_like_thumb" id="<?php echo $row->id ?>"
							      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
								      echo $res3->image_status;
							      } elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
								      echo $res4->image_status;
							      } else {
								      echo 'unliked';
							      } ?>">
							<?php if ( $like_dislike == 'heart' ): ?>
								<?php echo $row->like; ?>
							<?php endif; ?>
						</span>
							<span
								class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_popup_rating_count'] == 'off' ) {
									echo 'huge_it_hide';
								} ?>"
								id="<?php echo $row->id ?>"><?php if ( $like_dislike != 'heart' ): ?><?php echo $row->like; ?><?php endif; ?></span>
						</span>
							</div>
							<?php if ( $like_dislike != 'heart' ): ?>
								<div class="huge_it_gallery_dislike_wrapper">
						<span class="huge_it_dislike">
							<i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
							<span class="huge_it_dislike_thumb" id="<?php echo $row->id ?>"
							      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
								      echo $res3->image_status;
							      } elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
								      echo $res5->image_status;
							      } else {
								      echo 'unliked';
							      } ?>">
							</span>
							<span
								class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_popup_rating_count'] == 'off' ) {
									echo 'huge_it_hide';
								} ?>"
								id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
							</span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( $gallery_default_params["gallery_img_ht_view2_show_popup_linkbutton"] == 'on' ) { ?>
						<?php echo $viwMoreButton; ?>
					<?php } ?>
					<div style="clear:both;"></div>
				</div>
				<div style="clear:both;"></div>
			</div>
		</li>
		<?php
	} ?>
</ul>
