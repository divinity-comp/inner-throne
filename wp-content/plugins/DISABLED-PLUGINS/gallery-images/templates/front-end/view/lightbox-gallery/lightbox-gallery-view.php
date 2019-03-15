<section id="huge_it_gallery_content_<?php echo $galleryID; ?>" class="gallery-img-content"
		 data-gallery-id="<?php echo $galleryID; ?>"
		 data-content-per-page="<?php echo $num; ?>"
         data-rating-type="<?php echo $like_dislike; ?>">
	<div id="huge_it_gallery_container_<?php echo $galleryID; ?>"
	     data-show-center="<?php echo $gallery_default_params[ 'gallery_img_ht_view6_content_in_center' ]; ?>"
	     class="huge_it_gallery_container super-list variable-sizes clearfix view-<?php echo $view_slug; ?>">
		<div id="huge_it_gallery_container_moving_<?php echo $galleryID; ?>" class="super-list variable-sizes clearfix">
			<input type="hidden" class="pagenum" value="1"/>
			<?php
			global $wpdb;
			?>
			<input type="hidden" id="total" value="<?= $total; ?>"/>
			<?php
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
				$link       = str_replace( '__5_5_5__', '%', $row->sl_url );
				$descnohtml = strip_tags( str_replace( '__5_5_5__', '%', $row->description ) );
				$result     = substr( $descnohtml, 0, 50 );
				?>
				<div class="element element_<?php echo $galleryID; ?>" tabindex="0" data-symbol="<?php echo
				esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>" data-category="alkaline-earth">
					<div class="image-block_<?php echo $galleryID; ?>">
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
								<a href="<?php echo $imgurl[0]; ?>"
								   title="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"><img
										alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
										id="wd-cl-img<?php echo $key; ?>"
										title="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
										src="<?php echo esc_url( gallery_img_get_image_by_sizes_and_src(
											$imgurl[0], array(
											$gallery_default_params[ 'gallery_img_ht_view6_width' ],
											''
										), false
										) ); ?>"/></a>
							<?php } else { ?>
								<img alt="<?php echo str_replace( '__5_5_5__', '%', $row->name ); ?>"
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
									<a class="giyoutube huge_it_gallery_item gallery_group<?php echo $galleryID; ?>"
									   href="https://www.youtube.com/embed/<?php echo $videourl[0]; ?>"
									   title="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>">
										<img alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
										     src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"/>
										<div class="play-icon <?php echo $videourl[1]; ?>-icon"></div>
									</a>
									<?php
								} else {
									$hash   = unserialize( wp_remote_fopen( "http://vimeo.com/api/v2/video/" . $videourl[0] . ".php" ) );
									$imgsrc = $hash[0]['thumbnail_large'];
									?>
									<a class="givimeo huge_it_gallery_item gallery_group<?php echo $galleryID; ?>"
									   href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>"
									   title="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>">
										<img alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
										     src="<?php echo esc_attr( $imgsrc ); ?>"/>
										<div class="play-icon <?php echo $videourl[1]; ?>-icon"></div>
									</a>
									<?php
								}
								?>
								<?php
								break;
						}
						?>
					</div>
					<?php if ( $like_dislike != 'off' ): ?>
						<div class="huge_it_gallery_like_cont_<?php echo $galleryID . $pID; ?>">
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
								class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_lightbox_rating_count'] == 'off' ) {
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
								class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_lightbox_rating_count'] == 'off' ) {
									echo 'huge_it_hide';
								} ?>"
								id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
						</span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( $row->name != "" ) { ?>
						<div class="title-block_<?php echo $galleryID; ?>">
							<?php if ( $link != '' || ! empty( $link ) ): ?>
							<a title="<?php echo str_replace( '__5_5_5__', '%', $row->name ); ?>"
							   href="<?php echo $link; ?>" <?php if ( $row->link_target == "on" ) {
								echo 'target="_blank"';
							} ?>>
								<?php endif; ?>
								<?php echo str_replace( '__5_5_5__', '%', $row->name ); ?>
								<?php if ( $link != '' || ! empty( $link ) ): ?>
							</a>
						<?php endif; ?>
						</div>
					<?php } ?>
				</div>
				<?php
			} ?>
		</div>
		<div style="clear:both;"></div>
	</div>
	<?php
	$a = $disp_type;
	if ( $a == 1 ) {
        $gallery_img_lightbox_load_nonce = wp_create_nonce( 'gallery_img_lightbox_load_nonce' );
		?>
		<div class="load_more4">
			<div class="load_more_button4"
			     data-lightbox-nonce-value="<?php echo $gallery_img_lightbox_load_nonce; ?>"><?= $gallery_default_params['gallery_img_video_ht_view4_loadmore_text']; ?></div>
			<div class="loading4"><img src="<?php if ( $gallery_default_params['gallery_img_video_ht_view4_loading_type'] == '1' ) {
					echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading1.gif';
				} elseif ( $gallery_default_params['gallery_img_video_ht_view4_loading_type'] == '2' ) {
					echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading4.gif';
				} elseif ( $gallery_default_params['gallery_img_video_ht_view4_loading_type'] == '3' ) {
					echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading36.gif';
				} elseif ( $gallery_default_params['gallery_img_video_ht_view4_loading_type'] == '4' ) {
					echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading51.gif';
				} ?>"></div>
		</div>
		<?php
	} elseif ( $a == 0 ) {
		?>
		<div class="paginate4">
			<?php
			$protocol    = stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://';
			$actual_link = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "";
			$checkREQ    = '';
			$pattern     = "/\?p=/";
			$pattern2    = "/&page-img[0-9]+=[0-9]+/";
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
				$pervpage = '<a href= ' . $checkREQ . '=1><i class="icon-style4 hugeiticons-fast-backward" ></i></a>  
            <a href= ' . $checkREQ . '=' . ( $page - 1 ) . '><i class="icon-style4 hugeiticons-chevron-left"></i></a> ';
			}
			$nextpage = '';
			if ( $page != $total ) {
				$nextpage = ' <a href= ' . $checkREQ . '=' . ( $page + 1 ) . '><i class="icon-style4 hugeiticons-chevron-right"></i></a>  
            <a href= ' . $checkREQ . '=' . $total . '><i class="icon-style4 hugeiticons-fast-forward" ></i></a>';
			}
			echo $pervpage . $page . '/' . $total . $nextpage;
			?>
		</div>
		<?php
	}
	?>


</section>