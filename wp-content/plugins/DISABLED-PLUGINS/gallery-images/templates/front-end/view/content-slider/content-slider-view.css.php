<style>
* {outline:none;}
#g-main-slider_<?php echo $galleryID; ?> {background:#<?php echo $gallery_default_params["gallery_img_ht_view5_slider_background_color"]; ?>;}
.entry-content a{
	border-bottom: none;
}
#g-main-slider_<?php echo $galleryID; ?> div.slider-content {
	position:relative;
	width:100%;
	padding:0 0 0 0;
	position:relative;
	background:#<?php echo $gallery_default_params["gallery_img_ht_view5_slider_background_color"]; ?>;
}
[class$="-arrow"] {
	background-image:url(<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/arrow.'.$gallery_default_params["gallery_img_ht_view5_icons_style"].'.png';?>) !important;
}
.ls-select-box {
	background:url(<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/menu.'.$gallery_default_params["gallery_img_ht_view5_icons_style"].'.png';?>) right center no-repeat #<?php echo $gallery_default_params["gallery_img_ht_view5_slider_background_color"]; ?>;
}
#g-main-slider_<?php echo $galleryID; ?>-nav-select {
	color:#<?php echo $gallery_default_params["gallery_img_ht_view5_title_font_color"]; ?>;
}
#g-main-slider_<?php echo $galleryID; ?> div.slider-content .slider-content-wrapper {
	position:relative;
	width:100%;
	padding:0;
	display:block;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .image-block_<?php echo $galleryID; ?> {
	position:relative;
	width:<?php echo $gallery_default_params["gallery_img_ht_view5_main_image_width"]; ?>px;
	display:table-cell;
	padding:0 10px 0 0;
	float:left;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .image-block_<?php echo $galleryID; ?> img.main-image {
	position:relative;
	width:100%;
	height:auto;
	display:block;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .image-block_<?php echo $galleryID; ?> .play-icon {
	position:absolute;
	top:0;
	left:0;
	width:100%;
	height:100%;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .image-block_<?php echo $galleryID; ?>  .play-icon.youtube-icon {background:url(<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/play.youtube.png' ;?>) center center no-repeat;}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .image-block_<?php echo $galleryID; ?>  .play-icon.vimeo-icon {background:url(<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/play.vimeo.png'; ?>) center center no-repeat;}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block {
	display:table-cell;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block > div {
	padding-bottom:10px;
	margin-top:10px;
<?php if($gallery_default_params['gallery_img_ht_view5_show_separator_lines'] == "on") {?>
	background:url('<?php echo  GALLERY_IMG_IMAGES_URL.'/admin_images/divider.line.png'; ?>') center bottom repeat-x;
<?php } ?>
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block > div:last-child {background:none;}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .title {
	position:relative;
	display:block;
	margin:-10px 0 0 0;
	font-size:<?php echo $gallery_default_params["gallery_img_ht_view5_title_font_size"]; ?>px !important;
	line-height:<?php echo $gallery_default_params["gallery_img_ht_view5_title_font_size"]; ?>px !important;
	color:#<?php echo $gallery_default_params["gallery_img_ht_view5_title_font_color"]; ?>;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description {
	clear:both;
	position:relative;
	font-weight:normal;
	text-align:justify;
	font-size:<?php echo $gallery_default_params["gallery_img_ht_view5_description_font_size"]; ?>px !important;
	line-height:<?php echo $gallery_default_params["gallery_img_ht_view5_description_font_size"]; ?>px !important;
	color:#<?php echo $gallery_default_params["gallery_img_ht_view5_description_color"]; ?>;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description h1,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description h2,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description h3,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description h4,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description h5,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description h6,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description p,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description strong,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description span {
	padding:2px !important;
	margin:0 !important;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description ul,
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block .description li {
	padding:2px 0 2px 5px;
	margin:0 0 0 8px;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .button-block {
	position:relative;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .button-block a,#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .button-block a:link,#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .button-block a:visited{
	position:relative;
	display:inline-block;
	padding:6px 12px;
	background:#<?php echo $gallery_default_params["gallery_img_ht_view5_linkbutton_background_color"]; ?>;
	color:#<?php echo $gallery_default_params["gallery_img_ht_view5_linkbutton_color"]; ?>;
	font-size:<?php echo $gallery_default_params["gallery_img_ht_view5_linkbutton_font_size"]; ?>px;
	text-decoration:none;
}
#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .button-block a:hover,#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .button-block a:focus,#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .button-block a:active {
	background:#<?php echo $gallery_default_params["gallery_img_ht_view5_linkbutton_background_hover_color"]; ?>;
	color:#<?php echo $gallery_default_params["gallery_img_ht_view5_linkbutton_font_hover_color"]; ?>;
}
@media only screen and (min-width:500px) {
	#g-main-slider_<?php echo $galleryID; ?>-nav-ul {
		visibility:hidden !important;
		height:1px;
	}
}
@media only screen and (max-width:500px) {
	#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .image-block_<?php echo $galleryID; ?>,#g-main-slider_<?php echo $galleryID; ?> .slider-content-wrapper .right-block {
		width:100%;
		display:block;
		float:none;
		clear:both;
	}
}
<?php
switch ($like_dislike) {
	case "dislike":
?>
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
	color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_likedislike_font_color']; ?>;
	position: relative;
	z-index: 99999999999999999999999999;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_gallery_like_wrapper ,
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_gallery_dislike_wrapper {
	position:relative;
	background:<?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_contentsl_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_contentsl_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
	display: inline-block;
	border-radius: 3px;
	cursor: pointer;
	font-size:0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_gallery_like_wrapper{
	margin: 3px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_gallery_dislike_wrapper{
	margin: 3px 3px 3px 0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_like{
	font-size:0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_like .huge_it_like_thumb {
	display:block;
	float:left;
	padding:4px 4px 4px 18px;
	font-size: 12px;
	font-weight: 700;
	position:relative;
	height: 28px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_like_count,
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_dislike_count{
	display:block;
	float:left;
	padding:4px 4px 4px 4px;
	font-size: 12px;
	font-weight: 700;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_dislike{
	font-size:0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_dislike .huge_it_dislike_thumb {
	display:block;
	float:left;
	padding:4px 4px 4px 18px;
	font-size: 12px;
	font-weight: 700;
	position:relative;
	height: 28px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_like .huge_it_like_thumb:hover:after,
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>	.huge_it_dislike .huge_it_dislike_thumb:hover:after {
	opacity:1;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_up{
	font-size: 17px;
	position:absolute;
	top: 5px;
	left: 4px;
	color:#<?php echo $gallery_default_params['gallery_img_ht_contentsl_likedislike_thumb_color']; ?>;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .dislike_thumb_down{
	font-size: 17px;
	position:absolute;
	top: 4px;
	left: 4px;
	color:#<?php echo $gallery_default_params['gallery_img_ht_contentsl_likedislike_thumb_color']; ?>;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_hide{
	display: none !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active{
	color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_likedislike_thumb_active_color']; ?> !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active{
	color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_active_font_color']; ?> !important;
}
@media screen and (min-width: 768px){
	.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like {
		color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_active_font_color']; ?> !important;
	}
	.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .like_thumb_up {
		color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_likedislike_thumb_active_color']; ?> !important;
	}
	.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .huge_it_dislike {
		color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_active_font_color']; ?> !important;
	}
	.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .dislike_thumb_down {
		color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_likedislike_thumb_active_color']; ?> !important;
	}
}
/*/////Like/Dislike Styles END/////like/dislike///////*/
<?php break;
		case 'heart';
?>
/*/////Like/Dislike Styles BEGIN//////Heart//////*/
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
	position: absolute;
	top: 0;
	right: 15px;
	z-index: 99;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper  {
	position:relative;
	background:<?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_contentsl_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_contentsl_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
	display: inline-block;
	border-radius: 8px;
	font-size:0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover{
	background: #D6D4D4;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper{
	margin: 3px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like{
	font-size:0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb {
	display: block;
	float: left;
<?php $heartCount='';
if($gallery_default_params['gallery_img_ht_contentsl_rating_count'] == 'off'){
	$heartCount="transparent";
}else{
	$heartCount='#'.$gallery_default_params['gallery_img_ht_contentsl_likedislike_font_color'];
}
?>
	color:<?php echo $heartCount.';'; ?>
	width: 38px;
	height: 38px;
	padding:8px 0;
	font-size: 12px;
	text-align: center;
	font-weight: 700;
	position: relative;
	cursor: pointer;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb:after {
	color:#fff;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like_count{
	display:none;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb:hover:after {
	opacity:1;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like  .likeheart{
	font-size: 32px;
	color:#<?php echo $gallery_default_params['gallery_img_ht_contentsl_heart_likedislike_thumb_color']; ?>;
	position: absolute;
	top: 4px;
	left: 3px;
	transition:0.3s ease;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active{
	color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_heart_likedislike_thumb_active_color']; ?> !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active{
<?php if($gallery_default_params['gallery_img_ht_contentsl_rating_count'] != 'off'):?>
	color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_active_font_color']; ?> !important;
<?php endif; ?>
}
@media screen and (min-width: 768px){
	.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like_thumb {
	<?php if($gallery_default_params['gallery_img_ht_contentsl_rating_count'] != 'off'):?>
		color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_active_font_color']; ?> !important;
	<?php endif; ?>
	}
	.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .likeheart {
		color: #<?php echo $gallery_default_params['gallery_img_ht_contentsl_heart_likedislike_thumb_active_color']; ?> !important;
	}
}
/*/////Like/Dislike Styles END//////Heart//////*/
<?php
		break;
		}?>
</style>