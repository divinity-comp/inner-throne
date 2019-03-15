<div id="mygallery_wrapper_<?php echo $galleryID; ?>" class="gallery-img-content" style="clear: both"
	 data-gallery-id="<?php echo $galleryID; ?>" data-content-per-page="<?php echo $num; ?>"
     data-rating-type="<?php echo $like_dislike; ?>">
	<div id="mygallery_<?php echo $galleryID; ?>" class="mygallery view-<?php echo $view_slug; ?>" style="clear: both">
		<?php
		global $wpdb;
		?>
		<input type="hidden" id="total" value="<?php echo $total; ?>"/>
		<?php
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
			$imgurl        = explode( ";", $row->image_url );
			$link          = str_replace( '__5_5_5__', '%', $row->sl_url );
			$descnohtml    = strip_tags( str_replace( '__5_5_5__', '%', $row->description ) );
			$result        = substr( $descnohtml, 0, 50 );
			$imagerowstype = $row->sl_type;
			if ( $row->sl_type == '' ) {
				$imagerowstype = 'image';
			}
			switch ( $imagerowstype ) {
				case 'image':
					?>
					<?php if ( $row->image_url != ';' ) { ?>
					<a class="gallery_group<?php echo $galleryID; ?>" href="<?php echo $imgurl[0]; ?>"
					   title="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>">
						<img id="wd-cl-img<?php echo $key; ?>"
						     alt="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
						     src="<?php echo esc_url( gallery_img_get_image_by_sizes_and_src( $imgurl[0], array(
							     '',
							     $gallery_default_params[ 'gallery_img_ht_view8_element_height' ]
						     ), false ) ); ?>"/>
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
												class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
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
											class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
												echo 'huge_it_hide';
											} ?>" id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
										</span>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</a>
					<input type="hidden" class="pagenum" value="1"/>
				<?php } else { ?>
					<img alt="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
					     id="wd-cl-img<?php echo $key; ?>"
					     src="images/noimage.jpg"/>
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
												class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
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
												class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
													echo 'huge_it_hide';
												} ?>" id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
										</span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<input type="hidden" class="pagenum" value="1"/>
					<?php
				} ?>
					<?php break;
				case 'video':
					$videourl = gallery_img_get_video_id_from_url( $row->image_url );
					if ( $videourl[1] == 'youtube' ) {
						?>
						<a class="giyoutube huge_it_gallery_item gallery_group<?php echo $galleryID; ?>"
						   href="https://www.youtube.com/embed/<?php echo $videourl[0]; ?>"
						   title="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>">
							<img alt="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
							     src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"
							     alt="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>"/>
							<div class="play-icon <?php echo $videourl[1]; ?>-icon"></div>
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
																class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
																	echo 'huge_it_hide';
																} ?>"
																id="<?php echo $row->id ?>"><?php if ( $like_dislike != 'heart' ): ?><?php echo $row->like; ?><?php endif; ?></span>
														</span>
									</div>
									<?php if ( $like_dislike != 'heart' ): ?>
										<div class="huge_it_gallery_dislike_wrapper">
														<span class="huge_it_dislike">
															<i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
															<span class="huge_it_dislike_thumb"
															      id="<?php echo $row->id ?>"
															      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
																      echo $res3->image_status;
															      } elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
																      echo $res5->image_status;
															      } else {
																      echo 'unliked';
															      } ?>">
															</span>
															<span
																class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
																	echo 'huge_it_hide';
																} ?>"
																id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
														</span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</a>
						<input type="hidden" class="pagenum" value="1"/>
					<?php } else {
						$hash   = unserialize( wp_remote_fopen( "http://vimeo.com/api/v2/video/" . $videourl[0] . ".php" ) );
						$imgsrc = $hash[0]['thumbnail_large'];
						?>
						<a class="givimeo huge_it_gallery_item gallery_group<?php echo $galleryID; ?>"
						   href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>"
						   title="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>">
							<img alt="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>"
							     src="<?php echo esc_attr( $imgsrc ); ?>"
							     alt="<?php echo esc_attr(  str_replace( '__5_5_5__', '%', $row->name ) ); ?>"/>
							<div class="play-icon <?php echo $videourl[1]; ?>-icon"></div>
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
																class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
																	echo 'huge_it_hide';
																} ?>"
																id="<?php echo $row->id ?>"><?php if ( $like_dislike != 'heart' ): ?><?php echo $row->like; ?><?php endif; ?></span>
														</span>
									</div>
									<?php if ( $like_dislike != 'heart' ): ?>
										<div class="huge_it_gallery_dislike_wrapper">
														<span class="huge_it_dislike">
															<i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
															<span class="huge_it_dislike_thumb"
															      id="<?php echo $row->id ?>"
															      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
																      echo $res3->image_status;
															      } elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
																      echo $res5->image_status;
															      } else {
																      echo 'unliked';
															      } ?>">
															</span>
															<span
																class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_just_rating_count'] == 'off' ) {
																	echo 'huge_it_hide';
																} ?>"
																id="<?php echo $row->id ?>"><?php echo $row->dislike; ?></span>
														</span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</a>
						<input type="hidden" class="pagenum" value="1"/>
						<?php
					}
			}
		}
		?>
	</div>
	<?php
	$a = $disp_type;
	if ( $a == 1 ) {
        $gallery_img_justified_load_nonce = wp_create_nonce( 'gallery_img_justified_load_nonce' );
        ?>
		<div class="load_more2">
			<div
				class="load_more_button2 load_more_button_<?php echo $galleryID; ?>"
				data-justified-nonce-value="<?php echo $gallery_img_justified_load_nonce; ?>"><?php echo $gallery_default_params['gallery_img_video_ht_view8_loadmore_text']; ?></div>
			<div class="loading2 loading_<?php echo $galleryID; ?>">
				<img
					src="<?php if ( $gallery_default_params['gallery_img_video_ht_view8_loading_type'] == '1' ) {
						echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading1.gif';
					} elseif ( $gallery_default_params['gallery_img_video_ht_view8_loading_type'] == '2' ) {
						echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading4.gif';
					} elseif ( $gallery_default_params['gallery_img_video_ht_view8_loading_type'] == '3' ) {
						echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading36.gif';
					} elseif ( $gallery_default_params['gallery_img_video_ht_view8_loading_type'] == '4' ) {
						echo GALLERY_IMG_IMAGES_URL . '/front_images/arrows/loading51.gif';
					} ?>">
			</div>
		</div>
		<?php
	} elseif ( $a == 0 ) {
	?>
	<div class="paginate2">
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
			$pervpage = '<a href= ' . $checkREQ . '=1><i class="icon-style2 hugeiticons-fast-backward" ></i></a>  
			  <a href= ' . $checkREQ . '=' . ( $page - 1 ) . '><i class="icon-style2 hugeiticons-chevron-left"></i></a> ';
		}
		$nextpage = '';
		if ( $page != $total ) {
			$nextpage = ' <a href= ' . $checkREQ . '=' . ( $page + 1 ) . '><i class="icon-style2 hugeiticons-chevron-right"></i></a>  
              <a href= ' . $checkREQ . '=' . $total . '><i class="icon-style2 hugeiticons-fast-forward" ></i></a>';
		}
		echo $pervpage . $page . '/' . $total . $nextpage;
		?>
    </div>
	<?php } ?>
</div>
