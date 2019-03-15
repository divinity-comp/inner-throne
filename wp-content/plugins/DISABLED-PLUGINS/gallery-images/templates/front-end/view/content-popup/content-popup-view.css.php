<style>
.element_<?php echo $galleryID; ?> {
	width: 100%;
	max-width: <?php echo $gallery_default_params['gallery_img_ht_view2_element_width']+2*$gallery_default_params['gallery_img_ht_view2_element_border_width']; ?>px;
	height: <?php echo $gallery_default_params['gallery_img_ht_view2_element_height']+45+2*$gallery_default_params['gallery_img_ht_view2_element_border_width']; ?>px;
	margin: 0 10px 10px 0;
	background: #<?php echo $gallery_default_params['gallery_img_ht_view2_element_background_color']; ?>;
	border: <?php echo $gallery_default_params['gallery_img_ht_view2_element_border_width']; ?>px solid #<?php echo $gallery_default_params['gallery_img_ht_view2_element_border_color']; ?>;
	outline: none;
}
.element_<?php echo $galleryID; ?>.no-title{
	height: <?php echo $gallery_default_params['gallery_img_ht_view2_element_height']; ?>px;
}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> {
<?php if($gallery_default_params['gallery_img_image_natural_size_contentpopup']=='resize'){?> position: relative;
	width: 100%;
<?php }elseif($gallery_default_params['gallery_img_image_natural_size_contentpopup']=='natural'){?> position: relative;
	width: 100%;
	overflow: hidden;
	height: <?php echo $gallery_default_params['gallery_img_ht_view2_element_height']; ?>px !important;
<?php }?>
}

.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> img {
<?php if($gallery_default_params['gallery_img_image_natural_size_contentpopup']=='resize'){?> width: 100% !important;
	height: <?php echo $gallery_default_params['gallery_img_ht_view2_element_height']; ?>px !important;
	display: block;
	border-radius: 0 !important;
	box-shadow: 0 0 0 rgba(0, 0, 0, 0) !important;
<?php }elseif($gallery_default_params['gallery_img_image_natural_size_contentpopup']=='natural'){?> display: block;
	max-width: none !important;
	border-radius: 0 !important;
	box-shadow: 0 0 0 rgba(0, 0, 0, 0) !important;
<?php }?>
}

.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> .gallery-image-overlay {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_view2_element_overlay_color'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_view2_element_overlay_transparency"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
	display: none;
}

.element_<?php echo $galleryID; ?>:hover .image-block_<?php echo $galleryID; ?> .gallery-image-overlay {
	display: block;
}

.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> .gallery-image-overlay a {
	position: absolute;
	top: 0;
	left: 0;
	display: block;
	width: 100%;
	height: 100%;
	box-shadow: none !important;
	background: url('<?php echo  GALLERY_IMG_IMAGES_URL.'/admin_images/zoom.'.$gallery_default_params["gallery_img_ht_view2_zoombutton_style"].'.png'; ?>') center center no-repeat;
}

.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> {
	position: relative;
	height: 30px;
	margin: 0;
	padding: 15px 0 15px 0;
	-webkit-box-shadow: inset 0 1px 0 rgba(0, 0, 0, .1);
	box-shadow: inset 0 1px 0 rgba(0, 0, 0, .1);
}

.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> h3 {
	position: relative;
	margin: 0 !important;
	padding: 0 1% 5px 1% !important;
	width: 98%;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
	font-weight: normal;
	font-size: <?php echo $gallery_default_params["gallery_img_ht_view2_element_title_font_size"]; ?>px !important;
	line-height: <?php echo $gallery_default_params["gallery_img_ht_view2_popup_title_font_size"]; ?>px !important;
	color: #<?php echo $gallery_default_params["gallery_img_ht_view2_element_title_font_color"]; ?>;
}

.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> .button-block {
	position: absolute;
	right: 0;
	top: 0;
	display: none;
	vertical-align: middle;
	padding: 10px 10px 4px 10px;

	border-left: 1px solid rgba(0, 0, 0, .05);
}

.element_<?php echo $galleryID; ?>:hover .title-block_<?php echo $galleryID; ?> .button-block {
	display: block;
}

.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a, .element .title-block_<?php echo $galleryID; ?> a:link, .element .title-block_<?php echo $galleryID; ?> a:visited,
.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:hover, .element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:focus, .element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:active {
	position: relative;
	display: block;
	vertical-align: middle;
	padding: 3px 10px 3px 10px;
	border-radius: 3px;
	font-size: <?php echo $gallery_default_params["gallery_img_ht_view2_element_linkbutton_font_size"]; ?>px;
	background: #<?php echo $gallery_default_params["gallery_img_ht_view2_element_linkbutton_background_color"]; ?>;
	color: #<?php echo $gallery_default_params["gallery_img_ht_view2_element_linkbutton_color"]; ?>;
	text-decoration: none !important;
}

.load_more5 {
	margin: 10px 0;
	position: relative;
	text-align: <?php if($gallery_default_params['gallery_img_video_ht_view1_loadmore_position'] == 'left') {echo 'left';}
			elseif ($gallery_default_params['gallery_img_video_ht_view1_loadmore_position'] == 'center') { echo 'center'; }
			elseif($gallery_default_params['gallery_img_video_ht_view1_loadmore_position'] == 'right') { echo 'right'; }?>;
	width: 100%;
}

.load_more_button5 {
	border-radius: 10px;
	display: inline-block;
	padding: 5px 15px;
	font-size: <?php echo $gallery_default_params['gallery_img_video_ht_view1_loadmore_fontsize']; ?>px !important;;
	color: <?php echo '#'.$gallery_default_params['gallery_img_video_ht_view1_loadmore_font_color']; ?> !important;;
	background: <?php echo '#'.$gallery_default_params['gallery_img_video_ht_view1_button_color']; ?> !important;
	cursor: pointer;
}

.load_more_button5:hover {
	color: <?php echo '#'.$gallery_default_params['gallery_img_video_ht_view1_loadmore_font_color_hover']; ?> !important;
	background: <?php echo '#'.$gallery_default_params['gallery_img_video_ht_view1_button_color_hover']; ?> !important;
}

.loading5 {
	display: none;
}

.paginate5 {
	font-size: <?php echo $gallery_default_params['gallery_img_video_ht_view1_paginator_fontsize']; ?>px !important;
	color: <?php echo '#'.$gallery_default_params['gallery_img_video_ht_view1_paginator_color']; ?> !important;
	text-align: <?php echo $gallery_default_params['gallery_img_video_ht_view1_paginator_position']; ?>;
	margin-top: 15px;
}

.paginate5 a {
	border-bottom: none !important;
}

.icon-style5 {
	font-size: <?php echo $gallery_default_params['gallery_img_video_ht_view1_paginator_icon_size']; ?>px !important;
	color: <?php echo '#'.$gallery_default_params['gallery_img_video_ht_view1_paginator_icon_color']; ?> !important;
}

.clear {
	clear: both;
}

/*#####POPUP#####*/
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> {
	position: fixed;
	display: table;
	width: 80%;
	top: 7%;
	left: 7%;
	margin: 0 !important;
	padding: 0 !important;
	list-style: none;
	z-index: 100000000;
	display: none;
	height: 85%;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?>.active {
	display: table;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> li.pupup-element {
	position: relative;
	display: none;
	width: 100%;
	padding: 40px 0 20px 0;
	min-height: 100%;
	position: relative;
	background: #<?php echo $gallery_default_params["gallery_img_ht_view2_popup_background_color"]; ?>;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> li.pupup-element.active {
	display: block;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> {
	position: absolute;
	width: 100%;
	height: 40px;
	top: 0;
	left: 0;
	z-index: 2001;
	background: url('<?php echo  GALLERY_IMG_IMAGES_URL.'/admin_images/divider.line.png'; ?>') center bottom repeat-x;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .close, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .close:link, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .close:visited {
	position: relative;
	float: right;
	width: 40px;
	height: 40px;
	display: block;
	background: url('<?php echo  GALLERY_IMG_IMAGES_URL.'/admin_images/close.popup.'.$gallery_default_params["gallery_img_ht_view2_popup_closebutton_style"].'.png'; ?>') center center no-repeat;
	border-left: 1px solid #ccc;
	opacity: .65;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .close:hover, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .close:focus, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .close:active {
	opacity: 1;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> li.pupup-element .popup-wrapper_<?php echo $galleryID; ?> {
	position: relative;
	width: 98%;
	height: 98%;
	padding: 2% 0% 0% 2%;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> {
	width: 55%;
<?php if($gallery_default_params['gallery_img_ht_view2_popup_full_width'] == 'off') { echo "height:100%;"; }
	else { echo "height:100%;"; }?> position: relative;
	float: left;
	margin-right: 2%;
	border-right: 1px solid #ccc;
	min-width: 200px;
	min-height: 100%;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> img {
<?php
	if($gallery_default_params['gallery_img_ht_view2_popup_full_width'] == 'off') { echo "max-width:100% !important; max-height:100% !important;margin: 0 auto !important; position:relative !important; display:block;"; }
	else { echo "width:100% !important;"; }
?> display: block;
	padding: 0 !important;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> iframe {
	width: 100% !important;
	height: 100%;
	display: block;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block {
	width: 42.8%;
	height: 100%;
	position: relative;
	float: left;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> li.pupup-element .popup-wrapper_<?php echo $galleryID; ?> .right-block > div {
	padding-top: 10px;
	padding-right: 4%;
	margin-bottom: 10px;
<?php if($gallery_default_params['gallery_img_ht_view2_show_separator_lines'] == "on") {?> background: url('<?php echo  GALLERY_IMG_IMAGES_URL.'/admin_images/divider.line.png'; ?>') center top repeat-x;
<?php } ?>
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> li.pupup-element .popup-wrapper_<?php echo $galleryID; ?> .right-block > div:last-child {
	background: none;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .title {
	position: relative;
	display: block;
	margin: 0 0 10px 0 !important;
	font-size: <?php echo $gallery_default_params["gallery_img_ht_view2_popup_title_font_size"]; ?>px !important;
	line-height: <?php echo $gallery_default_params["gallery_img_ht_view2_popup_title_font_size"]; ?>px !important;
	color: #<?php echo $gallery_default_params["gallery_img_ht_view2_popup_title_font_color"]; ?>;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description {
	clear: both;
	position: relative;
	font-weight: normal;
	text-align: justify;
	font-size: <?php echo $gallery_default_params["gallery_img_ht_view2_description_font_size"]; ?>px !important;
	color: #<?php echo $gallery_default_params["gallery_img_ht_view2_description_color"]; ?>;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description h1,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description h2,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description h3,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description h4,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description h5,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description h6,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description p,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description strong,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description span {
	padding: 2px !important;
	margin: 0 !important;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description ul,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block .description li {
	padding: 2px 0 2px 5px;
	margin: 0 0 0 8px;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block ul.thumbs-list {
	list-style: none;
	display: table;
	position: relative;
	clear: both;
	width: 100%;
	margin: 0 auto;
	padding: 0;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block ul.thumbs-list li {
	display: block;
	float: left;
	width: <?php echo $gallery_default_params["gallery_img_ht_view2_thumbs_width"]; ?>px;
	height: <?php echo $gallery_default_params["gallery_img_ht_view2_thumbs_height"]; ?>px;
	margin: 0 2% 5px 1% !important;
	opacity: 0.45;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block ul.thumbs-list li.active, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block ul.thumbs-list li:hover {
	opacity: 1;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block ul.thumbs-list li a {
	display: block;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block ul.thumbs-list li img {
	width: <?php echo $gallery_default_params["gallery_img_ht_view2_thumbs_width"]; ?>px !important;
	height: <?php echo $gallery_default_params["gallery_img_ht_view2_thumbs_height"]; ?>px !important;
}

/**/
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .left-change, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .right-change {
	width: 40px;
	height: 39px;
	font-size: 25px;
	display: inline-block;
	text-align: center;
	border: 1px solid #eee;
	border-bottom: none;
	border-top: none;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .right-change {
	positio: relative;
	margin-left: -6px;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .right-change:hover, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .left-change:hover {
	background: #ddd;
	border-color: #ccc;
	color: #000 !important;
	cursor: pointer;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .right-change a, #huge_it_gallery_popup_list_<?php echo $galleryID; ?> .heading-navigation_<?php echo $galleryID; ?> .left-change a {
	position: absolute;
	top: 50%;
	transform: translate(-50%, -50%);
	color: #777;
	text-decoration: none;
	width: 12px;
	height: 24px;
	line-height: 1;
	display: inline-block;
}

/**/
.pupup-element .button-block {
	position: relative;
}

.pupup-element .button-block a, .pupup-element .button-block a:link, .pupup-element .button-block a:visited {
	position: relative;
	display: inline-block;
	padding: 6px 12px;
	background: #<?php echo $gallery_default_params["gallery_img_ht_view2_popup_linkbutton_background_color"]; ?>;
	color: #<?php echo $gallery_default_params["gallery_img_ht_view2_popup_linkbutton_color"]; ?>;
	font-size: <?php echo $gallery_default_params["gallery_img_ht_view2_popup_linkbutton_font_size"]; ?>px;
	text-decoration: none;
}

.pupup-element .button-block a:hover, .pupup-element .button-block a:focus, .pupup-element .button-block a:active {
	background: #<?php echo $gallery_default_params["gallery_img_ht_view2_popup_linkbutton_background_hover_color"]; ?>;
	color: #<?php echo $gallery_default_params["gallery_img_ht_view2_popup_linkbutton_font_hover_color"]; ?>;
}

#huge-popup-overlay-image {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 199;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_view2_popup_overlay_color'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_view2_popup_overlay_transparency_color"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>
}

@media only screen and (max-width: 767px) {
	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: auto !important;
		left: 0;
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> li.pupup-element {
		margin: 0;
		height: auto !important;
		position: absolute;
		left: 0;
		top: 0;
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> li.pupup-element .popup-wrapper_<?php echo $galleryID; ?> {
		height: auto !important;
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> {
		width: 100%;
		float: none;
		clear: both;
		margin-right: 0;
		border-right: 0;
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .popup-wrapper_<?php echo $galleryID; ?> .right-block {
		width: 100%;
		float: none;
		clear: both;
		margin-right: 0;
		border-right: 0;
	}

	#huge-popup-overlay-image_<?php echo $galleryID; ?> {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 199;
	}
}

<?php switch ($like_dislike) {
case "dislike":
?>
/*/////Like/Dislike Styles BEGIN//////Dislike//////*/
#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> {
	float: right;
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_font_color']; ?>;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper,
#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper {
	position: relative;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_popup_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_popup_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
	display: inline-block;
	border-radius: 3px;
	font-size: 0;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper {
	margin: 3px;
	cursor: pointer;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper {
	margin: 3px 3px 3px 0;
	cursor: pointer;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like {
	font-size: 0;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb {
	display: block;
	float: left;
	padding: 4px 4px 4px 18px;
	font-size: 12px;
	font-weight: 700;
	position: relative;
	height: 28px;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like_count,
#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike_count {
	display: block;
	float: left;
	padding: 4px 4px 4px 4px;
	font-size: 12px;
	font-weight: 700;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike {
	font-size: 0;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike .huge_it_dislike_thumb {
	display: block;
	float: left;
	padding: 4px 4px 4px 18px;
	font-size: 12px;
	font-weight: 700;
	position: relative;
	height: 28px;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_up {
	font-size: 17px;
	position: absolute;
	top: 5px;
	left: 4px;
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_color']; ?>;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .dislike_thumb_down {
	font-size: 17px;
	position: absolute;
	top: 4px;
	left: 4px;
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_color']; ?>;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_hide {
	display: none !important;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active {
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_active_color']; ?> !important;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active {
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
}

@media screen and (min-width: 768px) {
	#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
	}

	#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .like_thumb_up {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_active_color']; ?> !important;
	}

	#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .huge_it_dislike {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
	}

	#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .dislike_thumb_down {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_active_color']; ?> !important;
	}
}

/*///////////////////POPUP////////////////*/
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> {
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_font_color']; ?>;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper {
	position: relative;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_popup_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_popup_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
	display: inline-block;
	border-radius: 3px;
	font-size: 0;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper {
	margin: 3px;
	cursor: pointer;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper {
	margin: 3px 3px 3px 0;
	cursor: pointer;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like {
	font-size: 0;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb {
	display: block;
	float: left;
	padding: 4px 4px 4px 18px;
	font-size: 12px;
	font-weight: 700;
	position: relative;
	height: 28px;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like_count,
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike_count {
	display: block;
	float: left;
	padding: 4px 4px 4px 4px;
	font-size: 12px;
	font-weight: 700;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike {
	font-size: 0;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike .huge_it_dislike_thumb {
	display: block;
	float: left;
	padding: 4px 4px 4px 18px;
	font-size: 12px;
	font-weight: 700;
	position: relative;
	height: 28px;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_up {
	font-size: 17px;
	position: absolute;
	top: 5px;
	left: 4px;
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_color']; ?>;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .dislike_thumb_down {
	font-size: 17px;
	position: absolute;
	top: 4px;
	left: 4px;
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_color']; ?>;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_hide {
	display: none !important;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active {
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_active_color']; ?> !important;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active {
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
}

@media screen and (min-width: 768px) {
	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .like_thumb_up {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_active_color']; ?> !important;
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .huge_it_dislike {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .dislike_thumb_down {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_likedislike_thumb_active_color']; ?> !important;
	}
}

<?php break; ?>
/*///////////////////POPUP////////////////*/
/*/////Like/Dislike Styles END//////Dislike//////*/
<?php case "heart":
?>
/*/////Like/Dislike Styles BEGIN//////Heart//////*/
#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> {
	float: right;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper {
	position: relative;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_popup_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_popup_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
	display: inline-block;
	border-radius: 8px;
	font-size: 0;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover {
	background: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_hov_bg_color']; ?>;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper {
	margin: 3px;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like {
	font-size: 0;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb {
	display: block;
	float: left;
<?php $heartCount='';
if($gallery_default_params['gallery_img_ht_popup_rating_count'] == 'off'){
	$heartCount="transparent";
}else{
	$heartCount='#'.$gallery_default_params['gallery_img_ht_popup_likedislike_font_color'];
}
?> color: <?php echo $heartCount.';'; ?> width: 38px;
	height: 38px;
	padding: 8px 0;
	font-size: 12px;
	text-align: center;
	font-weight: 700;
	position: relative;
	cursor: pointer;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb:after {
	color: #fff;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like_count {
	display: none;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb:hover:after {
	opacity: 1;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .likeheart {
	font-size: 32px;
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_likedislike_thumb_color']; ?>;
	position: absolute;
	top: 4px;
	left: 3px;
	transition: 0.3s ease;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active {
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_likedislike_thumb_active_color']; ?> !important;
}

#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active {
<?php if($gallery_default_params['gallery_img_ht_popup_rating_count']!='off'):?> color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
<?php endif; ?>
}

@media screen and (min-width: 768px) {
	#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like_thumb {
	<?php if($gallery_default_params['gallery_img_ht_popup_rating_count']!='off'):?> color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
	<?php endif; ?>
	}

	#huge_it_gallery_content_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .likeheart {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_likedislike_thumb_active_color']; ?> !important;
	}
}

/*///////////////POPUP//////////////////*/
#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> {
	position: absolute;
	top: 0;
	right: 0;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper {
	position: relative;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_popup_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_popup_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
	display: inline-block;
	border-radius: 8px;
	font-size: 0;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover {
	background: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_hov_bg_color']; ?>;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper {
	margin: 3px;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like {
	font-size: 0;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb {
	display: block;
	float: left;
<?php $heartCount='';
if($gallery_default_params['gallery_img_ht_popup_rating_count'] == 'off'){
	$heartCount="transparent";
}else{
	$heartCount='#'.$gallery_default_params['gallery_img_ht_popup_likedislike_font_color'];
}
?> color: <?php echo $heartCount.';'; ?> width: 38px;
	height: 38px;
	padding: 8px 0;
	font-size: 12px;
	text-align: center;
	font-weight: 700;
	position: relative;
	cursor: pointer;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb:after {
	color: #fff;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like_count {
	display: none;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb:hover:after {
	opacity: 1;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .likeheart {
	font-size: 32px;
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_likedislike_thumb_color']; ?>;
	position: absolute;
	top: 4px;
	left: 3px;
	transition: 0.3s ease;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active {
	color: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_likedislike_thumb_active_color']; ?> !important;
}

#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active {
<?php if($gallery_default_params['gallery_img_ht_popup_rating_count'] != 'off'):?> color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
<?php endif; ?>
}

@media screen and (min-width: 768px) {
	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like_thumb {
	<?php if($gallery_default_params['gallery_img_ht_popup_rating_count'] != 'off'):?> color: #<?php echo $gallery_default_params['gallery_img_ht_popup_active_font_color']; ?> !important;
	<?php endif; ?>
	}

	#huge_it_gallery_popup_list_<?php echo $galleryID; ?> .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .likeheart {
		color: #<?php echo $gallery_default_params['gallery_img_ht_popup_heart_likedislike_thumb_active_color']; ?> !important;
	}
}

/*/////Like/Dislike Styles END//////Heart//////*/
<?php break;
}?>
</style>