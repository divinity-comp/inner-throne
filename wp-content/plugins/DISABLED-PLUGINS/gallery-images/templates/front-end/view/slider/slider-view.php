<script>
	var data_gallery_<?php echo $galleryID; ?> = [];
	var event_stack_gallery_<?php echo $galleryID; ?> = [];
	<?php
	$i=0;
	foreach($images as $image){
		$imagerowstype=$image->sl_type;
		if($image->sl_type == ''){
			$imagerowstype='image';
		}
		switch($imagerowstype){
			case 'image':
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]=[];';
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["id"]="'.$i.'";';
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["image_url"]="'.$image->image_url.'";';
				$strdesription=str_replace('"',"'",$image->description);
				$strdesription=preg_replace( "/\r|\n/", " ", $strdesription );
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["description"]="'.$strdesription.'";';
				$stralt=str_replace('"',"'",$image->name);
				$stralt=preg_replace( "/\r|\n/", " ", $stralt );
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["alt"]="'.$stralt.'";';
				$i++;
				break;
			case 'video':
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]=[];';
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["id"]="'.$i.'";';
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["image_url"]="'.$image->image_url.'";';
				$strdesription=str_replace('"',"'",$image->description);
				$strdesription=preg_replace( "/\r|\n/", " ", $strdesription );
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["description"]="'.$strdesription.'";';
				$stralt=str_replace('"',"'",$image->name);
				$stralt=preg_replace( "/\r|\n/", " ", $stralt );
				echo 'data_gallery_'.$galleryID.'["'.$i.'"]["alt"]="'.$stralt.'";';
				$i++;
				break;
			case 'last_posts':
				foreach($recent_posts as $keyl => $recentimage){
					if(get_the_post_thumbnail($recentimage["ID"], 'thumbnail') != ''){
						if($keyl < $image->sl_url){
							echo 'data_gallery_'.$galleryID.'["'.$i.'"]=[];';
							echo 'data_gallery_'.$galleryID.'["'.$i.'"]["id"]="'.$i.'";';
							echo 'data_gallery_'.$galleryID.'["'.$i.'"]["image_url"]="'.$recentimage['guid'].'";';
							$strdesription=str_replace('"',"'",$recentimage['post_content']);
							$strdesription=preg_replace( "/\r|\n/", " ", $strdesription );
							$strdesription=substr_replace($strdesription, "",$image->description);
							echo 'data_gallery_'.$galleryID.'["'.$i.'"]["description"]="'.$strdesription.'";';
							$stralt=str_replace('"',"'",$recentimage['post_title']);
							$stralt=preg_replace( "/\r|\n/", " ", $stralt );
							echo 'data_gallery_'.$galleryID.'["'.$i.'"]["alt"]="'.$stralt.'";';
							$i++;
						}
					}
				}
				break;
		}
	}
	?>
	var huge_it_trans_in_progress_gallery_<?php echo $galleryID; ?> = false;
	var huge_it_transition_duration_gallery_<?php echo $galleryID; ?> = <?php echo $slidechangespeed;?>;
	var huge_it_playInterval_gallery_<?php echo $galleryID; ?>;
	// Stop autoplay.
	window.clearInterval(huge_it_playInterval_gallery_<?php echo $galleryID; ?>);
	// alert('huge_it_current_key_gallery_<?php echo $galleryID; ?>');
	var huge_it_current_key_gallery_<?php echo $galleryID; ?> = '<?php echo (isset($current_key) ? $current_key : ''); ?>';
	function huge_it_move_dots_gallery_<?php echo $galleryID; ?>() {
		var image_left = jQuery(".huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").position().left;
		var image_right = jQuery(".huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").position().left + jQuery(".huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").outerWidth(true);
	}
	function huge_it_testBrowser_cssTransitions_gallery_<?php echo $galleryID; ?>() {
		return huge_it_testDom_gallery_<?php echo $galleryID; ?>('Transition');
	}
	function huge_it_testBrowser_cssTransforms3d_gallery_<?php echo $galleryID; ?>() {
		return huge_it_testDom_gallery_<?php echo $galleryID; ?>('Perspective');
	}
	function huge_it_testDom_gallery_<?php echo $galleryID; ?>(prop) {
		// Browser vendor CSS prefixes.
		var browserVendors = ['', '-webkit-', '-moz-', '-ms-', '-o-', '-khtml-'];
		// Browser vendor DOM prefixes.
		var domPrefixes = ['', 'Webkit', 'Moz', 'ms', 'O', 'Khtml'];
		var i = domPrefixes.length;
		while (i--) {
			if (typeof document.body.style[domPrefixes[i] + prop] !== 'undefined') {
				return true;
			}
		}
		return false;
	}
	function huge_it_cube_gallery_<?php echo $galleryID; ?>(tz, ntx, nty, nrx, nry, wrx, wry, current_image_class, next_image_class, direction) {
		/* If browser does not support 3d transforms/CSS transitions.*/
		if (!huge_it_testBrowser_cssTransitions_gallery_<?php echo $galleryID; ?>()) {
			jQuery(".huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>");
			jQuery("#huge_it_dots_" + huge_it_current_key_gallery_<?php echo $galleryID; ?> + "_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>");
			return huge_it_fallback_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
		}
		if (!huge_it_testBrowser_cssTransforms3d_gallery_<?php echo $galleryID; ?>()) {
			return huge_it_fallback3d_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
		}
		huge_it_trans_in_progress_gallery_<?php echo $galleryID; ?> = true;
		/* Set active thumbnail.*/
		jQuery(".huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>");
		jQuery("#huge_it_dots_" + huge_it_current_key_gallery_<?php echo $galleryID; ?> + "_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>");
		jQuery(".huge_it_slide_bg_gallery_<?php echo $galleryID; ?>").css('perspective', 1000);
		jQuery(current_image_class).css({
			transform : 'translateZ(' + tz + 'px)',
			backfaceVisibility : 'hidden'
		});
		jQuery(".huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?>,.huge_it_slide_bg_gallery_<?php echo $galleryID; ?>,.huge_it_slideshow_image_item_gallery_<?php echo $galleryID; ?>,.huge_it_slideshow_image_second_item_gallery_<?php echo $galleryID; ?> ").css('overflow', 'visible');
		jQuery(next_image_class).css({
			opacity : 1,
			filter: 'Alpha(opacity=100)',
			backfaceVisibility : 'hidden',
			transform : 'translateY(' + nty + 'px) translateX(' + ntx + 'px) rotateY('+ nry +'deg) rotateX('+ nrx +'deg)'
		});
		jQuery(".huge_it_slider_gallery_<?php echo $galleryID; ?>").css({
			transform: 'translateZ(-' + tz + 'px)',
			transformStyle: 'preserve-3d'
		});
		/* Execution steps.*/
		setTimeout(function () {
			jQuery(".huge_it_slider_gallery_<?php echo $galleryID; ?>").css({
				transition: 'all ' + huge_it_transition_duration_gallery_<?php echo $galleryID; ?> + 'ms ease-in-out',
				transform: 'translateZ(-' + tz + 'px) rotateX('+ wrx +'deg) rotateY('+ wry +'deg)'
			});
		}, 20);
		/* After transition.*/
		jQuery(".huge_it_slider_gallery_<?php echo $galleryID; ?>").one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(huge_it_after_trans));
		function huge_it_after_trans() {
			jQuery(".huge_it_slide_bg_gallery_<?php echo $galleryID; ?>,.huge_it_slideshow_image_item_gallery_<?php echo $galleryID; ?>,.huge_it_slideshow_image_second_item_gallery_<?php echo $galleryID; ?> ").css('overflow', 'hidden');
			jQuery(".huge_it_slide_bg_gallery_<?php echo $galleryID; ?>").removeAttr('style');
			jQuery(current_image_class).removeAttr('style');
			jQuery(next_image_class).removeAttr('style');
			jQuery(".huge_it_slider_gallery_<?php echo $galleryID; ?>").removeAttr('style');
			jQuery(current_image_class).css({'opacity' : 0, filter: 'Alpha(opacity=0)', 'z-index': 1});
			jQuery(next_image_class).css({'opacity' : 1, filter: 'Alpha(opacity=100)', 'z-index' : 2});
			// huge_it_change_watermark_container_gallery_<?php echo $galleryID; ?>();
			huge_it_trans_in_progress_gallery_<?php echo $galleryID; ?> = false;
			if (typeof event_stack_gallery_<?php echo $galleryID; ?> !== 'undefined' && event_stack_gallery_<?php echo $galleryID; ?>.length > 0) {
				key = event_stack_gallery_<?php echo $galleryID; ?>[0].split("-");
				event_stack_gallery_<?php echo $galleryID; ?>.shift();
				huge_it_change_image_gallery_<?php echo $galleryID; ?>(key[0], key[1], data_gallery_<?php echo $galleryID; ?>, true,false);
			}
		}
	}
	function huge_it_cubeH_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		/* Set to half of image width.*/
		var dimension = jQuery(current_image_class).width() / 2;
		if (direction == 'right') {
			huge_it_cube_gallery_<?php echo $galleryID; ?>(dimension, dimension, 0, 0, 90, 0, -90, current_image_class, next_image_class, direction);
		}
		else if (direction == 'left') {
			huge_it_cube_gallery_<?php echo $galleryID; ?>(dimension, -dimension, 0, 0, -90, 0, 90, current_image_class, next_image_class, direction);
		}
	}
	function huge_it_cubeV_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		/* Set to half of image height.*/
		var dimension = jQuery(current_image_class).height() / 2;
		/* If next slide.*/
		if (direction == 'right') {
			huge_it_cube_gallery_<?php echo $galleryID; ?>(dimension, 0, -dimension, 90, 0, -90, 0, current_image_class, next_image_class, direction);
		}
		else if (direction == 'left') {
			huge_it_cube_gallery_<?php echo $galleryID; ?>(dimension, 0, dimension, -90, 0, 90, 0, current_image_class, next_image_class, direction);
		}
	}
	/* For browsers that does not support transitions.*/
	function huge_it_fallback_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_fade_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
	}
	/* For browsers that support transitions, but not 3d transforms (only used if primary transition makes use of 3d-transforms).*/
	function huge_it_fallback3d_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_sliceV_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
	}
	function huge_it_none_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
		jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
		/* Set active thumbnail.*/
		jQuery(".huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>");
		jQuery("#huge_it_dots_" + huge_it_current_key_gallery_<?php echo $galleryID; ?> + "_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>");
	}
	function huge_it_fade_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		if (huge_it_testBrowser_cssTransitions_gallery_<?php echo $galleryID; ?>()) {
			jQuery(next_image_class).css('transition', 'opacity ' + huge_it_transition_duration_gallery_<?php echo $galleryID; ?> + 'ms linear');
			jQuery(current_image_class).css('transition', 'opacity ' + huge_it_transition_duration_gallery_<?php echo $galleryID; ?> + 'ms linear');
			jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
			jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
		}
		else {
			jQuery(current_image_class).animate({'opacity' : 0, 'z-index' : 1}, huge_it_transition_duration_gallery_<?php echo $galleryID; ?>);
			jQuery(next_image_class).animate({
				'opacity' : 1,
				'z-index': 2
			}, {
				duration: huge_it_transition_duration_gallery_<?php echo $galleryID; ?>,
				complete: function () {return false;}
			});
			// For IE.
			jQuery(current_image_class).fadeTo(huge_it_transition_duration_gallery_<?php echo $galleryID; ?>, 0);
			jQuery(next_image_class).fadeTo(huge_it_transition_duration_gallery_<?php echo $galleryID; ?>, 1);
		}
		jQuery(".huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>");
		jQuery("#huge_it_dots_" + huge_it_current_key_gallery_<?php echo $galleryID; ?> + "_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>");
	}
	function huge_it_grid_gallery_<?php echo $galleryID; ?>(cols, rows, ro, tx, ty, sc, op, current_image_class, next_image_class, direction) {
		/* If browser does not support CSS transitions.*/
		if (!huge_it_testBrowser_cssTransitions_gallery_<?php echo $galleryID; ?>()) {
			jQuery(".huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>");
			jQuery("#huge_it_dots_" + huge_it_current_key_gallery_<?php echo $galleryID; ?> + "_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>");
			return huge_it_fallback_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
		}
		huge_it_trans_in_progress_gallery_<?php echo $galleryID; ?> = true;
		/* Set active thumbnail.*/
		jQuery(".huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>");
		jQuery("#huge_it_dots_" + huge_it_current_key_gallery_<?php echo $galleryID; ?> + "_gallery_<?php echo $galleryID; ?>").removeClass("huge_it_slideshow_dots_deactive_gallery_<?php echo $galleryID; ?>").addClass("huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>");
		/* The time (in ms) added to/subtracted from the delay total for each new gridlet.*/
		var count = (huge_it_transition_duration_gallery_<?php echo $galleryID; ?>) / (cols + rows);
		/* Gridlet creator (divisions of the image grid, positioned with background-images to replicate the look of an entire slide image when assembled)*/
		function huge_it_gridlet(width, height, top, img_top, left, img_left, src, imgWidth, imgHeight, c, r) {
			var delay = (c + r) * count;
			/* Return a gridlet elem with styles for specific transition.*/
			return jQuery('<div class="huge_it_gridlet_gallery_<?php echo $galleryID; ?>" />').css({
				width : width,
				height : height,
				top : top,
				left : left,
				backgroundImage : 'url("' + src + '")',
				backgroundColor: jQuery(".huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?>").css("background-color"),
				/*backgroundColor: rgba(0, 0, 0, 0),*/
				backgroundRepeat: 'no-repeat',
				backgroundPosition : img_left + 'px ' + img_top + 'px',
				backgroundSize : imgWidth + 'px ' + imgHeight + 'px',
				transition : 'all ' + huge_it_transition_duration_gallery_<?php echo $galleryID; ?> + 'ms ease-in-out ' + delay + 'ms',
				transform : 'none'
			});
		}
		/* Get the current slide's image.*/
		var cur_img = jQuery(current_image_class).find('img');
		/* Create a grid to hold the gridlets.*/
		var grid = jQuery('<div />').addClass('huge_it_grid_gallery_<?php echo $galleryID; ?>');
		/* Prepend the grid to the next slide (i.e. so it's above the slide image).*/
		jQuery(current_image_class).prepend(grid);
		/* vars to calculate positioning/size of gridlets*/
		var cont = jQuery(".huge_it_slide_bg_gallery_<?php echo $galleryID; ?>");
		var imgWidth = cur_img.width();
		var imgHeight = cur_img.height();
		var contWidth = cont.width(),
			contHeight = cont.height(),
			imgSrc = cur_img.attr('src'),/*.replace('/thumb', ''),*/
			colWidth = Math.floor(contWidth / cols),
			rowHeight = Math.floor(contHeight / rows),
			colRemainder = contWidth - (cols * colWidth),
			colAdd = Math.ceil(colRemainder / cols),
			rowRemainder = contHeight - (rows * rowHeight),
			rowAdd = Math.ceil(rowRemainder / rows),
			leftDist = 0,
			img_leftDist = (jQuery(".huge_it_slide_bg_gallery_<?php echo $galleryID; ?>").width() - cur_img.width()) / 2;
		/* tx/ty args can be passed as 'auto'/'min-auto' (meaning use slide width/height or negative slide width/height).*/
		tx = tx === 'auto' ? contWidth : tx;
		tx = tx === 'min-auto' ? - contWidth : tx;
		ty = ty === 'auto' ? contHeight : ty;
		ty = ty === 'min-auto' ? - contHeight : ty;
		/* Loop through cols*/
		for (var i = 0; i < cols; i++) {
			var topDist = 0,
				img_topDst = (jQuery(".huge_it_slide_bg_gallery_<?php echo $galleryID; ?>").height() - cur_img.height()) / 2,
				newColWidth = colWidth;
			/* If imgWidth (px) does not divide cleanly into the specified number of cols, adjust individual col widths to create correct total.*/
			if (colRemainder > 0) {
				var add = colRemainder >= colAdd ? colAdd : colRemainder;
				newColWidth += add;
				colRemainder -= add;
			}
			/* Nested loop to create row gridlets for each col.*/
			for (var j = 0; j < rows; j++)  {
				var newRowHeight = rowHeight,
					newRowRemainder = rowRemainder;
				/* If contHeight (px) does not divide cleanly into the specified number of rows, adjust individual row heights to create correct total.*/
				if (newRowRemainder > 0) {
					add = newRowRemainder >= rowAdd ? rowAdd : rowRemainder;
					newRowHeight += add;
					newRowRemainder -= add;
				}
				/* Create & append gridlet to grid.*/
				grid.append(huge_it_gridlet(newColWidth, newRowHeight, topDist, img_topDst, leftDist, img_leftDist, imgSrc, imgWidth, imgHeight, i, j));
				topDist += newRowHeight;
				img_topDst -= newRowHeight;
			}
			img_leftDist -= newColWidth;
			leftDist += newColWidth;
		}
		/* Set event listener on last gridlet to finish transitioning.*/
		var last_gridlet = grid.children().last();
		/* Show grid & hide the image it replaces.*/
		grid.show();
		cur_img.css('opacity', 0);
		/* Add identifying classes to corner gridlets (useful if applying border radius).*/
		grid.children().first().addClass('rs-top-left');
		grid.children().last().addClass('rs-bottom-right');
		grid.children().eq(rows - 1).addClass('rs-bottom-left');
		grid.children().eq(- rows).addClass('rs-top-right');
		/* Execution steps.*/
		setTimeout(function () {
			grid.children().css({
				opacity: op,
				transform: 'rotate('+ ro +'deg) translateX('+ tx +'px) translateY('+ ty +'px) scale('+ sc +')'
			});
		}, 1);
		jQuery(next_image_class).css('opacity', 1);
		/* After transition.*/
		jQuery(last_gridlet).one('webkitTransitionEnd transitionend otransitionend oTransitionEnd mstransitionend', jQuery.proxy(huge_it_after_trans));
		function huge_it_after_trans() {
			jQuery(current_image_class).css({'opacity' : 0, 'z-index': 1});
			jQuery(next_image_class).css({'opacity' : 1, 'z-index' : 2});
			cur_img.css('opacity', 1);
			grid.remove();
			huge_it_trans_in_progress_gallery_<?php echo $galleryID; ?> = false;
			if (typeof event_stack_gallery_<?php echo $galleryID; ?> !== 'undefined' && event_stack_gallery_<?php echo $galleryID; ?>.length > 0) {
				key = event_stack_gallery_<?php echo $galleryID; ?>[0].split("-");
				event_stack_gallery_<?php echo $galleryID; ?>.shift();
				huge_it_change_image_gallery_<?php echo $galleryID; ?>(key[0], key[1], data_gallery_<?php echo $galleryID; ?>, true,false);
			}
		}
	}
	function huge_it_sliceH_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		if (direction == 'right') {
			var translateX = 'min-auto';
		}
		else if (direction == 'left') {
			var translateX = 'auto';
		}
		huge_it_grid_gallery_<?php echo $galleryID; ?>(1, 8, 0, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
	}
	function huge_it_sliceV_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		if (direction == 'right') {
			var translateY = 'min-auto';
		}
		else if (direction == 'left') {
			var translateY = 'auto';
		}
		huge_it_grid_gallery_<?php echo $galleryID; ?>(10, 1, 0, 0, translateY, 1, 0, current_image_class, next_image_class, direction);
	}
	function huge_it_slideV_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		if (direction == 'right') {
			var translateY = 'auto';
		}
		else if (direction == 'left') {
			var translateY = 'min-auto';
		}
		huge_it_grid_gallery_<?php echo $galleryID; ?>(1, 1, 0, 0, translateY, 1, 1, current_image_class, next_image_class, direction);
	}
	function huge_it_slideH_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		if (direction == 'right') {
			var translateX = 'min-auto';
		}
		else if (direction == 'left') {
			var translateX = 'auto';
		}
		huge_it_grid_gallery_<?php echo $galleryID; ?>(1, 1, 0, translateX, 0, 1, 1, current_image_class, next_image_class, direction);
	}
	function huge_it_scaleOut_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_grid_gallery_<?php echo $galleryID; ?>(1, 1, 0, 0, 0, 1.5, 0, current_image_class, next_image_class, direction);
	}
	function huge_it_scaleIn_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_grid_gallery_<?php echo $galleryID; ?>(1, 1, 0, 0, 0, 0.5, 0, current_image_class, next_image_class, direction);
	}
	function huge_it_blockScale_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_grid_gallery_<?php echo $galleryID; ?>(8, 6, 0, 0, 0, .6, 0, current_image_class, next_image_class, direction);
	}
	function huge_it_kaleidoscope_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_grid_gallery_<?php echo $galleryID; ?>(10, 8, 0, 0, 0, 1, 0, current_image_class, next_image_class, direction);
	}
	function huge_it_fan_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		if (direction == 'right') {
			var rotate = 45;
			var translateX = 100;
		}
		else if (direction == 'left') {
			var rotate = -45;
			var translateX = -100;
		}
		huge_it_grid_gallery_<?php echo $galleryID; ?>(1, 10, rotate, translateX, 0, 1, 0, current_image_class, next_image_class, direction);
	}
	function huge_it_blindV_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_grid_gallery_<?php echo $galleryID; ?>(1, 8, 0, 0, 0, .7, 0, current_image_class, next_image_class);
	}
	function huge_it_blindH_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		huge_it_grid_gallery_<?php echo $galleryID; ?>(10, 1, 0, 0, 0, .7, 0, current_image_class, next_image_class);
	}
	function huge_it_random_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction) {
		var anims = ['sliceH', 'sliceV', 'slideH', 'slideV', 'scaleOut', 'scaleIn', 'blockScale', 'kaleidoscope', 'fan', 'blindH', 'blindV'];
		/* Pick a random transition from the anims array.*/
		this["huge_it_" + anims[Math.floor(Math.random() * anims.length)] + "_gallery_<?php echo $galleryID; ?>"](current_image_class, next_image_class, direction);
	}
	function iterator_gallery_<?php echo $galleryID; ?>() {
		var iterator = 1;
		return iterator;
	}
	function huge_it_change_image_gallery_<?php echo $galleryID; ?>(current_key, key, data_gallery_<?php echo $galleryID; ?>, from_effect,clicked) {
		if (data_gallery_<?php echo $galleryID; ?>[key]) {
			if(typeof video_is_playing_gallery_<?php echo $galleryID; ?> != 'undefined' && video_is_playing_gallery_<?php echo $galleryID; ?> && !clicked){
				return false;
			}
			if (!from_effect) {
				// Change image key.
				jQuery("#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>").val(key);
				// if (current_key == '-2') { /* Dots.*/
				current_key = jQuery(".huge_it_slideshow_dots_active_gallery_<?php echo $galleryID; ?>").attr("data-image_key");
				//  }
			}
			if (huge_it_trans_in_progress_gallery_<?php echo $galleryID; ?>) {
				//errorlogjQuery(".huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?>").after(" --IN TRANSACTION-- <br />");
				event_stack_gallery_<?php echo $galleryID; ?>.push(current_key + '-' + key);
				return;
			}
			var direction = 'right';
			if (huge_it_current_key_gallery_<?php echo $galleryID; ?> > key) {
				var direction = 'left';
			}
			else if (huge_it_current_key_gallery_<?php echo $galleryID; ?> == key) {
				return false;
			}
			// Set active thumbnail position.
			huge_it_current_key_gallery_<?php echo $galleryID; ?> = key;
			//Change image id, title, description.
			jQuery(".huge_it_slideshow_image_gallery_<?php echo $galleryID; ?>").attr('data-image_id', data_gallery_<?php echo $galleryID; ?>[key]["id"]);
			jQuery(".huge_it_slideshow_title_text_gallery_<?php echo $galleryID; ?>").html(data_gallery_<?php echo $galleryID; ?>[key]["alt"]);
			jQuery(".huge_it_slideshow_description_text_gallery_<?php echo $galleryID; ?>").html(data_gallery_<?php echo $galleryID; ?>[key]["description"]);
			var current_image_class = "#image_id_gallery_<?php echo $galleryID; ?>_" + data_gallery_<?php echo $galleryID; ?>[current_key]["id"];
			var next_image_class = "#image_id_gallery_<?php echo $galleryID; ?>_" + data_gallery_<?php echo $galleryID; ?>[key]["id"];
			if(jQuery(current_image_class).find('.huge_it_video_frame_gallery_<?php echo $galleryID; ?>').length>0) {
				var streffect='<?php echo $slidereffect; ?>';
				if(streffect=="cubeV" || streffect=="cubeH" || streffect=="none" || streffect=="fade"){
					huge_it_<?php echo $slidereffect; ?>_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
				}else{
					huge_it_fade_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
				}
			}else{
				huge_it_<?php echo $slidereffect; ?>_gallery_<?php echo $galleryID; ?>(current_image_class, next_image_class, direction);
			}
			jQuery('.huge_it_slideshow_title_text_gallery_<?php echo $galleryID; ?>').removeClass('none');
			if(jQuery('.huge_it_slideshow_title_text_gallery_<?php echo $galleryID; ?>').html()==""){jQuery('.huge_it_slideshow_title_text_gallery_<?php echo $galleryID; ?>').addClass('none');}
			jQuery('.huge_it_slideshow_description_text_gallery_<?php echo $galleryID; ?>').removeClass('none');
			jQuery('.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>').removeClass('none');
			if(jQuery('.huge_it_slideshow_description_text_gallery_<?php echo $galleryID; ?>').html()==""){
				jQuery('.huge_it_slideshow_description_text_gallery_<?php echo $galleryID; ?>').addClass('none');
			}
			jQuery(current_image_class).find('.huge_it_slideshow_title_text_gallery_<?php echo $galleryID; ?>').addClass('none');
			jQuery(current_image_class).find('.huge_it_slideshow_description_text_gallery_<?php echo $galleryID; ?>').addClass('none');
			jQuery(current_image_class).find('.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>').addClass('none');
			//errorlogjQuery(".huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?>").after("--cur-key="+current_key+" --cur-img-class="+current_image_class+" nxt-img-class="+next_image_class+"--");
			huge_it_move_dots_gallery_<?php echo $galleryID; ?>();
			window.clearInterval(huge_it_playInterval_gallery_<?php echo $galleryID; ?>);
			play_gallery_<?php echo $galleryID; ?>();
		}
	}
	function huge_it_popup_resize_gallery_<?php echo $galleryID; ?>() {
		var staticsliderwidth=<?php echo $sliderwidth;?>;
		var sliderwidth=<?php echo $sliderwidth;?>;
		var bodyWidth=jQuery(window).width();
		var parentWidth = jQuery(".huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?>").parent().width();

		if(sliderwidth>parentWidth){sliderwidth=parentWidth;}
		if(sliderwidth>bodyWidth){sliderwidth=bodyWidth;}
		var str=(<?php echo $sliderheight;?>/staticsliderwidth);

		jQuery('.huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?>').css({'max-height':parentWidth*str-2*<?php echo $gallery_default_params['gallery_img_slider_slideshow_border_size']; ?>});

	}
	jQuery(window).load(function () {
		jQuery(window).resize(function() {
			huge_it_popup_resize_gallery_<?php echo $galleryID; ?>();
		});
		huge_it_popup_resize_gallery_<?php echo $galleryID; ?>();
		/* Disable right click.*/
		<?php if( get_option( 'gallery_img_disable_right_click' ) == 'on' ): ?>
		jQuery('div[id^="huge_it_container"]').bind("contextmenu", function () {
			return false;
		});
		<?php endif; ?>
		/*HOVER SLIDESHOW*/
		jQuery("#huge_it_slideshow_image_container_gallery_<?php echo $galleryID; ?>, .huge_it_slideshow_image_container_gallery_<?php echo $galleryID; ?>, .huge_it_slideshow_dots_container_gallery_<?php echo $galleryID; ?>,#huge_it_slideshow_right_gallery_<?php echo $galleryID; ?>,#huge_it_slideshow_left_gallery_<?php echo $galleryID; ?>").hover(function(){
			//errorlogjQuery(".huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?>").after(" -- hover -- <br /> ");
			jQuery("#huge_it_slideshow_right_gallery_<?php echo $galleryID; ?>").css({'display':'inline'});
			jQuery("#huge_it_slideshow_left_gallery_<?php echo $galleryID; ?>").css({'display':'inline'});
		},function(){
			jQuery("#huge_it_slideshow_right_gallery_<?php echo $galleryID; ?>").css({'display':'none'});
			jQuery("#huge_it_slideshow_left_gallery_<?php echo $galleryID; ?>").css({'display':'none'});
		});
		var pausehover="<?php echo $sliderpauseonhover;?>";
		if(pausehover=="on"){
			jQuery("#huge_it_slideshow_image_container_gallery_<?php echo $galleryID; ?>, .huge_it_slideshow_image_container_gallery_<?php echo $galleryID; ?>, .huge_it_slideshow_dots_container_gallery_<?php echo $galleryID; ?>,#huge_it_slideshow_right_gallery_<?php echo $galleryID; ?>,#huge_it_slideshow_left_gallery_<?php echo $galleryID; ?>").hover(function(){
				window.clearInterval(huge_it_playInterval_gallery_<?php echo $galleryID; ?>);
			},function(){
				window.clearInterval(huge_it_playInterval_gallery_<?php echo $galleryID; ?>);
				play_gallery_<?php echo $galleryID; ?>();
			});
		}
		play_gallery_<?php echo $galleryID; ?>();
	});
	function play_gallery_<?php echo $galleryID; ?>() {
		/* Play.*/
		huge_it_playInterval_gallery_<?php echo $galleryID; ?> = setInterval(function () {
			var iterator = 1;
			huge_it_change_image_gallery_<?php echo $galleryID; ?>(parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()), (parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()) + iterator) % data_gallery_<?php echo $galleryID; ?>.length, data_gallery_<?php echo $galleryID; ?>,false,false);
		}, '<?php echo $slidepausetime; ?>');
	}
	jQuery(window).focus(function() {
		var i_gallery_<?php echo $galleryID; ?> = 0;
		jQuery(".huge_it_slider_gallery_<?php echo $galleryID; ?>").children("div").each(function () {
			if (jQuery(this).css('opacity') == 1) {
				jQuery("#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>").val(i_gallery_<?php echo $galleryID; ?>);
			}
			i_gallery_<?php echo $galleryID; ?>++;
		});
		play_gallery_<?php echo $galleryID; ?>();
	});
	jQuery(window).blur(function() {
		event_stack_gallery_<?php echo $galleryID; ?> = [];
		window.clearInterval(huge_it_playInterval_gallery_<?php echo $galleryID; ?>);
	});
</script>
<div class="huge_it_slideshow_image_wrap_gallery_<?php echo $galleryID; ?> gallery-img-content"
     data-rating-type="<?php echo $like_dislike; ?>">
	<?php
	$current_pos = 0;
	?>
	<!-- ##########################DOTS######################### -->
	<div class="huge_it_slideshow_dots_container_gallery_<?php echo $galleryID; ?>">
		<div class="huge_it_slideshow_dots_thumbnails_gallery_<?php echo $galleryID; ?>">
			<?php
			$current_image_id = 0;
			$current_pos      = 0;
			$current_key      = 0;
			$stri             = 0;
			foreach ( $images as $key => $image_row ) {
				$imagerowstype = $image_row->sl_type;
				if ( $image_row->sl_type == '' ) {
					$imagerowstype = 'image';
				}
				switch ( $imagerowstype ) {
					case 'image':
						if ( $image_row->id == $current_image_id ) {
							$current_pos = $stri;
							$current_key = $stri;
						}
						?>
						<div id="huge_it_dots_<?php echo $stri; ?>_gallery_<?php echo $galleryID; ?>"
						     class="huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?> <?php echo( ( $key == $current_image_id ) ? 'huge_it_slideshow_dots_active_gallery_' . $galleryID : 'huge_it_slideshow_dots_deactive_gallery_' . $galleryID ); ?>"
						     onclick="huge_it_change_image_gallery_<?php echo $galleryID; ?>(parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()), '<?php echo $stri; ?>', data_gallery_<?php echo $galleryID; ?>,false,true);return false;"
						     data-image_id="<?php echo $image_row->id; ?>" data-image_key="<?php echo $stri; ?>"></div>
						<?php
						$stri ++;
						break;
					case 'video':
						if ( $image_row->id == $current_image_id ) {
							$current_pos = $stri;
							$current_key = $stri;
						}
						?>
						<div id="huge_it_dots_<?php echo $stri; ?>_gallery_<?php echo $galleryID; ?>"
						     class="huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?> <?php echo( ( $key == $current_image_id ) ? 'huge_it_slideshow_dots_active_gallery_' . $galleryID : 'huge_it_slideshow_dots_deactive_gallery_' . $galleryID ); ?>"
						     onclick="huge_it_change_image_gallery_<?php echo $galleryID; ?>(parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()), '<?php echo $stri; ?>', data_gallery_<?php echo $galleryID; ?>,false,true);return false;"
						     data-image_id="<?php echo $image_row->id; ?>" data-image_key="<?php echo $stri; ?>"></div>
						<?php
						$stri ++;
						break;
					case 'last_posts':
						foreach ( $recent_posts as $lkeys => $last_posts ) {
							if ( $lkeys < $image_row->sl_url ) {
								if ( get_the_post_thumbnail( $last_posts["ID"], 'thumbnail' ) != '' ) {
									$imagethumb = wp_get_attachment_image_src( get_post_thumbnail_id( $last_posts["ID"] ), 'thumbnail-size', true );
									if ( $image_row->id == $current_image_id ) {
										$current_pos = $stri;
										$current_key = $stri;
									}
									?>
									<div id="huge_it_dots_<?php echo $stri; ?>_gallery_<?php echo $galleryID; ?>"
									     class="huge_it_slideshow_dots_gallery_<?php echo $galleryID; ?> <?php echo( ( $stri == $current_image_id ) ? 'huge_it_slideshow_dots_active_gallery_' . $galleryID : 'huge_it_slideshow_dots_deactive_gallery_' . $galleryID ); ?>"
									     onclick="huge_it_change_image_gallery_<?php echo $galleryID; ?>(parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()), '<?php echo $stri; ?>', data_gallery_<?php echo $galleryID; ?>,false,true);return false;"
									     data-image_id="<?php echo $image_row->id; ?>"
									     data-image_key="<?php echo $stri; ?>"></div>
									<?php
									$stri ++;
								}
							}
						}
						break;
				}
			}
			?>
		</div>
		<?php
		if ( $gallery_default_params['gallery_img_slider_show_arrows'] == "on" ) {
			?>
			<a id="huge_it_slideshow_left_gallery_<?php echo $galleryID; ?>" href="#"
			   onclick="huge_it_change_image_gallery_<?php echo $galleryID; ?>(parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()), (parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()) - iterator_gallery_<?php echo $galleryID; ?>()) >= 0 ? (parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()) - iterator_gallery_<?php echo $galleryID; ?>()) % data_gallery_<?php echo $galleryID; ?>.length : data_gallery_<?php echo $galleryID; ?>.length - 1, data_gallery_<?php echo $galleryID; ?>,false,true);return false;">
				<div id="huge_it_slideshow_left-ico_gallery_<?php echo $galleryID; ?>">
					<div><i class="huge_it_slideshow_prev_btn_gallery_<?php echo $galleryID; ?> fa"></i></div>
				</div>
			</a>
			<a id="huge_it_slideshow_right_gallery_<?php echo $galleryID; ?>" href="#"
			   onclick="huge_it_change_image_gallery_<?php echo $galleryID; ?>(parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()), (parseInt(jQuery('#huge_it_current_image_key_gallery_<?php echo $galleryID; ?>').val()) + iterator_gallery_<?php echo $galleryID; ?>()) % data_gallery_<?php echo $galleryID; ?>.length, data_gallery_<?php echo $galleryID; ?>,false,true);return false;">
				<div id="huge_it_slideshow_right-ico_<?php echo $galleryID; ?>">
					<div><i class="huge_it_slideshow_next_btn_gallery_<?php echo $galleryID; ?> fa"></i></div>
				</div>
			</a>
			<?php
		}
		?>
	</div>
	<!-- ##########################IMAGES######################### -->
	<script>
		jQuery(document).ready(function ($) {
			$('.thumb_wrapper').on('click', function (ev) {
				var hugeid = $(this).data('rowid');
				var myid = hugeid;
				myid = parseInt(myid);
				eval('player_' + myid + '.playVideo()')
				ev.preventDefault();
			});
		});
	</script>
	<div id="huge_it_slideshow_image_container_gallery_<?php echo $galleryID; ?>"
	     class="huge_it_slideshow_image_container_gallery huge_it_slideshow_image_container_gallery_<?php echo $galleryID; ?> view-<?php echo $view_slug; ?>">
		<div class="huge_it_slide_container_gallery_<?php echo $galleryID; ?>">
			<div class="huge_it_slide_bg_gallery_<?php echo $galleryID; ?>">
				<ul class="huge_it_slider_gallery_<?php echo $galleryID; ?>">
					<?php
					$i = 0;
					foreach ( $images as $key => $image_row ) {
						global $wpdb;
						if ( ! isset( $_COOKIE[ 'Like_' . $image_row->id . '' ] ) ) {
							$_COOKIE[ 'Like_' . $image_row->id . '' ] = '';
						}
						if ( ! isset( $_COOKIE[ 'Dislike_' . $image_row->id . '' ] ) ) {
							$_COOKIE[ 'Dislike_' . $image_row->id . '' ] = '';
						}
						$num2          = $wpdb->prepare( "SELECT `image_status`,`ip` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `ip` = '" . $huge_it_ip . "'", (int) $image_row->id );
						$res3          = $wpdb->get_row( $num2 );
						$num3          = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Like_' . $image_row->id . '' ] . "'", (int) $image_row->id );
						$res4          = $wpdb->get_row( $num3 );
						$num4          = $wpdb->prepare( "SELECT `image_status`,`ip`,`cook` FROM " . $wpdb->prefix . "huge_itgallery_like_dislike WHERE image_id = %d AND `cook` = '" . $_COOKIE[ 'Dislike_' . $image_row->id . '' ] . "'", (int) $image_row->id );
						$res5          = $wpdb->get_row( $num4 );
						$imagerowstype = $image_row->sl_type;
						$videourl      = $image_row->image_url;
						$icon          = gallery_img_youtube_or_vimeo( $videourl );
						if ( $image_row->sl_type == '' ) {
							$imagerowstype = 'image';
						}
						switch ( $imagerowstype ) {
							case 'image':
								$target = "";
								?>
								<li class="huge_it_slideshow_image<?php if ( $i != $current_image_id ) {
									$current_key = $key;
									echo '_second';
								} ?>_item_gallery_<?php echo $galleryID; ?>"
								    id="image_id_gallery_<?php echo $galleryID . '_' . $i ?>">
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
											<span class="huge_it_like_thumb" id="<?php echo $image_row->id ?>"
											      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
												      echo $res3->image_status;
											      } elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
												      echo $res4->image_status;
											      } else {
												      echo 'unliked';
											      } ?>">
											<?php if ( $like_dislike == 'heart' ): ?>
												<?php echo $image_row->like; ?>
											<?php endif; ?>
											</span>
											<span
												class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_slider_rating_count'] == 'off' ) {
													echo 'huge_it_hide';
												} ?>"
												id="<?php echo $image_row->id ?>"><?php if ( $like_dislike != 'heart' ): ?><?php echo $image_row->like; ?><?php endif; ?></span>
										</span>
											</div>
											<?php if ( $like_dislike != 'heart' ): ?>
												<div class="huge_it_gallery_dislike_wrapper">
										<span class="huge_it_dislike">
											<i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
											<span class="huge_it_dislike_thumb" id="<?php echo $image_row->id ?>"
											      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
												      echo $res3->image_status;
											      } elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
												      echo $res5->image_status;
											      } else {
												      echo 'unliked';
											      } ?>">
											</span>
											<span
												class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_slider_rating_count'] == 'off' ) {
													echo 'huge_it_hide';
												} ?>"
												id="<?php echo $image_row->id ?>"><?php echo $image_row->dislike; ?></span>
										</span>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
									<?php if ( $image_row->sl_url != "" ) {
										if ( $image_row->link_target == "on" ) {
											$target = 'target="_blank"';
										}
										echo '<a href="' . $image_row->sl_url . '" ' . $target . '>';
									} ?>
									<img alt="<?php echo esc_attr( str_replace( '__5_5_5__', '%', $image_row->name ) ); ?>"
									     id="huge_it_slideshow_image_gallery_<?php echo $galleryID; ?>_<?php echo $key; ?>"
									     class="huge_it_slideshow_image_gallery_<?php echo $galleryID; ?>"
									     src="<?php echo esc_attr( $image_row->image_url ); ?>"
									     data-image_id="<?php echo $image_row->id; ?>"/>
									<?php if ( $image_row->sl_url != "" ) {
										echo '</a>';
									} ?>
									<div
										class="huge_it_slideshow_title_text_gallery_<?php echo $galleryID; ?> <?php if ( trim( str_replace( '__5_5_5__', '%', $image_row->name ) ) == "" ) {
											echo "none";
										} ?>">
										<?php echo str_replace( '__5_5_5__', '%', $image_row->name ); ?>
									</div>
									<div
										class="huge_it_slideshow_description_text_gallery_<?php echo $galleryID; ?> <?php if ( trim( str_replace( '__5_5_5__', '%', $image_row->description ) ) == "" ) {
											echo "none";
										} ?>">
										<?php echo str_replace( '__5_5_5__', '%', $image_row->description ); ?>
									</div>
								</li>
								<?php
								$i ++;
								break;
							case 'video':
								?>
								<li class="huge_it_slideshow_image<?php if ( $i != $current_image_id ) {
									$current_key = $key;
									echo '_second';
								} ?>_item_gallery_<?php echo $galleryID; ?>"
								    id="image_id_gallery_<?php echo $galleryID . '_' . $i ?>">
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
											<span class="huge_it_like_thumb" id="<?php echo $image_row->id ?>"
											      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'liked' ) {
												      echo $res3->image_status;
											      } elseif ( isset( $res4->image_status ) && $res4->image_status == 'liked' ) {
												      echo $res4->image_status;
											      } else {
												      echo 'unliked';
											      } ?>">
											<?php if ( $like_dislike == 'heart' ): ?>
												<?php echo $image_row->like; ?>
											<?php endif; ?>
											</span>
												<span
													class="huge_it_like_count <?php if ( $gallery_default_params['gallery_img_ht_slider_rating_count'] == 'off' ) {
														echo 'huge_it_hide';
													} ?>"
													id="<?php echo $image_row->id ?>"><?php if ( $like_dislike != 'heart' ): ?><?php echo $image_row->like; ?><?php endif; ?></span>
										</span>
											</div>
											<?php if ( $like_dislike != 'heart' ): ?>
												<div class="huge_it_gallery_dislike_wrapper">
										<span class="huge_it_dislike">
											<i class="hugeiticons-thumbs-down dislike_thumb_down"></i>
											<span class="huge_it_dislike_thumb" id="<?php echo $image_row->id ?>"
											      data-status="<?php if ( isset( $res3->image_status ) && $res3->image_status == 'disliked' ) {
												      echo $res3->image_status;
											      } elseif ( isset( $res5->image_status ) && $res5->image_status == 'disliked' ) {
												      echo $res5->image_status;
											      } else {
												      echo 'unliked';
											      } ?>">
											</span>
											<span
												class="huge_it_dislike_count <?php if ( $gallery_default_params['gallery_img_ht_slider_rating_count'] == 'off' ) {
													echo 'huge_it_hide';
												} ?>"
												id="<?php echo $image_row->id ?>"><?php echo $image_row->dislike; ?></span>
										</span>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
									<?php
									if ( strpos( $image_row->image_url, 'youtu' ) !== false ) {
										$video_thumb_url_info = gallery_img_get_video_id_from_url( $image_row->image_url );
										$video_thumb_url = $video_thumb_url_info[0];
										?>
										<div class="thumb_wrapper" data-rowid="<?php echo $image_row->id; ?>"
										     onclick="thevid=document.getElementById('thevideo'); thevid.style.display='block'; this.style.display='none'">
											<img class="thumb_image"
											     src="https://i.ytimg.com/vi/<?php echo $video_thumb_url; ?>/hqdefault.jpg">
											<div class="playbutton <?php echo $icon; ?>-icon"></div>
										</div>
										<div id="thevideo" style="display: block;">
											<div id="video_id_gallery_<?php echo $galleryID; ?>_<?php echo $key; ?>"
											     class="huge_it_video_frame_gallery_<?php echo $galleryID; ?>"></div>
										</div>
									<?php } else {
										$vimeo     = $image_row->image_url;
										$openError = explode( "/", $vimeo );
										$imgid     = end( $openError );
										?>
										<iframe id="player_<?php echo $key; ?>"
										        class="huge_it_video_frame_gallery_<?php echo $galleryID; ?>"
										        src="//player.vimeo.com/video/<?php echo $imgid; ?>?api=1&player_id=player_<?php echo $key; ?>&showinfo=0&controls=0"
										        frameborder="0" allowfullscreen></iframe>
									<?php } ?>
								</li>
								<?php
								$i ++;
								break;
						}
					}
					?>
				</ul>
				<input type="hidden" id="huge_it_current_image_key_gallery_<?php echo $galleryID; ?>" value="0"/>
			</div>
		</div>
	</div>
</div>