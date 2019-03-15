<style>
.gallery-img-content a{
    border-bottom: none;
}
.element_<?php echo $galleryID; ?> {
    max-width:<?php echo $gallery_default_params['gallery_img_ht_view6_width']+2*$gallery_default_params['gallery_img_ht_view6_border_width']; ?>px;
    margin:0 2px 10px 0;
    border:<?php echo $gallery_default_params['gallery_img_ht_view6_border_width']; ?>px solid #<?php echo $gallery_default_params['gallery_img_ht_view6_border_color']; ?>;
    border-radius:<?php echo $gallery_default_params['gallery_img_ht_view6_border_radius']; ?>px;
    outline:none;
    overflow:hidden;
}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> {
    position:relative;
    width: 100%;
	width:<?php echo $gallery_default_params['gallery_img_ht_view6_width']; ?>px;
    max-width:<?php echo $gallery_default_params['gallery_img_ht_view6_width']; ?>px;
}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> a {display:block;}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> img {
    width: 100%;
    max-width:<?php echo $gallery_default_params['gallery_img_ht_view6_width']; ?>px !important;
    height:auto;
    display:block;
    border-radius: 0 !important;
    box-shadow: 0 0 0 rgba(0, 0, 0, 0) !important;
}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> img:hover {
    cursor: -webkit-zoom-in; cursor: -moz-zoom-in;
}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?> .play-icon {
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
}

.element_<?php echo $galleryID; ?>:hover .title-block_<?php echo $galleryID; ?> {bottom:0;}
.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a, .element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:link, .element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:visited, .element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> {
    position:relative;
    margin:0;
    padding:0 1% 0 2%;
    width:97%;
    text-decoration:none !important;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space:nowrap;
    z-index:20;
    font-size: <?php echo $gallery_default_params["gallery_img_ht_view6_title_font_size"]; ?>px;
    color:#<?php echo $gallery_default_params["gallery_img_ht_view6_title_font_color"]; ?>;
    font-weight:normal;
}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?>  .play-icon.youtube-icon {background:url(<?php echo GALLERY_IMG_IMAGES_URL.'../admin_images/play.youtube.png'; ?>) center center no-repeat;}
.element_<?php echo $galleryID; ?> .image-block_<?php echo $galleryID; ?>  .play-icon.vimeo-icon {background:url(<?php echo GALLERY_IMG_IMAGES_URL.'../admin_images/play.vimeo.png'; ?>) center center no-repeat;}
.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> {
    position:absolute;
    left:0;
    width:100%;
    padding-top:5px;
    height: <?php echo 20+$gallery_default_params['gallery_img_ht_view6_title_font_size']; ?>px;
    bottom:-41px;
    background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_view6_title_background_color'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_view6_title_background_transparency"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
    -webkit-transition: bottom 0.3s ease-out 0.1s;
    -moz-transition: bottom 0.3s ease-out 0.1s;
    -o-transition: bottom 0.3s ease-out 0.1s;
    transition: bottom 0.3s ease-out 0.1s;
}
.element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:hover, .element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:focus, .element_<?php echo $galleryID; ?> .title-block_<?php echo $galleryID; ?> a:active {
    color:#<?php echo $gallery_default_params["gallery_img_ht_view6_title_font_hover_color"]; ?>;
    text-decoration:none;
}
.load_more4 {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($gallery_default_params['gallery_img_video_ht_view4_loadmore_position'] == 'left') {echo 'left';}
			elseif ($gallery_default_params['gallery_img_video_ht_view4_loadmore_position'] == 'center') { echo 'center'; }
			elseif($gallery_default_params['gallery_img_video_ht_view4_loadmore_position'] == 'right') { echo 'right'; }?>;
    width:100%;
}
.load_more_button4 {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $gallery_default_params['gallery_img_video_ht_view4_loadmore_fontsize']; ?>px !important;;
    color:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view4_loadmore_font_color']; ?> !important;;
    background:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view4_button_color']; ?> !important;
    cursor:pointer;
}
.load_more_button4:hover{
    color:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view4_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view4_button_color_hover']; ?> !important;
}
.loading4 {
    display:none;
}
.paginate4{
    font-size:<?php echo $gallery_default_params['gallery_img_video_ht_view4_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view4_paginator_color']; ?> !important;
    text-align: <?php echo $gallery_default_params['gallery_img_video_ht_view4_paginator_position']; ?>;
    margin-top: 25px;
}
.paginate4 a{
    border-bottom: none !important;
}
.icon-style4{
    font-size: <?php echo $gallery_default_params['gallery_img_video_ht_view4_paginator_icon_size']; ?>px !important;
    color:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view4_paginator_icon_color']; ?> !important;
}
.clear{
    clear:both;
}
<?php
switch ($like_dislike) {
    case "dislike":
?>
/*/////Like/Dislike Styles END//////like/dislike//////*/
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
    position: absolute;
    top: 0;
    right:0;
    z-index: 3;
    color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_likedislike_font_color']; ?>;
    display: none;
}
.element_<?php echo $galleryID; ?>:hover  .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
    display: table;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper ,
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper {
    position:relative;
    background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_lightbox_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_lightbox_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
    display: inline-block;
    border-radius: 3px;
    font-size:0;
    cursor: pointer;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper{
    margin: 3px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper{
    margin: 3px 3px 3px 0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like{
    font-size:0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb {
    display:block;
    float:left;
    padding:4px 4px 4px 18px;
    font-size: 12px;
    font-weight: 700;
    position:relative;
    height: 28px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like_count,
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike_count{
    display:block;
    float:left;
    padding:4px 4px 4px 4px;
    font-size: 12px;
    font-weight: 700;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike{
    font-size:0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_dislike .huge_it_dislike_thumb {
    display:block;
    float:left;
    padding:4px 4px 4px 18px;
    font-size: 12px;
    font-weight: 700;
    position:relative;
    height: 28px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_up{
    font-size: 17px;
    position:absolute;
    top: 5px;
    left: 4px;
    color:#<?php echo $gallery_default_params['gallery_img_ht_lightbox_likedislike_thumb_color']; ?>;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .dislike_thumb_down{
    font-size: 17px;
    position:absolute;
    top: 4px;
    left: 4px;
    color:#<?php echo $gallery_default_params['gallery_img_ht_lightbox_likedislike_thumb_color']; ?>;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_hide{
    display: none !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active{
    color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_likedislike_thumb_active_color']; ?> !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active{
    color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_active_font_color']; ?> !important;
}
@media screen and (min-width: 768px){
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like {
        color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_active_font_color']; ?> !important;
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .like_thumb_up {
        color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_likedislike_thumb_active_color']; ?> !important;
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .huge_it_dislike {
        color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_active_font_color']; ?> !important;
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .dislike_thumb_down {
        color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_likedislike_thumb_active_color']; ?> !important;
    }
}
/*/////Like/Dislike Styles END//////like/dislike//////*/
<?php break;
    case 'heart':
?>
/*/////Like/Dislike Styles BEGIN//////Heart//////*/
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
    position: absolute;
    top: 0;
    right: 0;
    z-index: 99;
    display: none;
}
.element_<?php echo $galleryID; ?>:hover  .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
    display: block;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper  {
    position:relative;
    background:<?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_lightbox_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_lightbox_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important';
	?>;
    display: inline-block;
    border-radius: 8px;
    font-size:0;
    cursor: pointer;
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
if($gallery_default_params['gallery_img_ht_lightbox_rating_count'] == 'off'){
    $heartCount="transparent";
}else{
    $heartCount='#'.$gallery_default_params['gallery_img_ht_lightbox_likedislike_font_color'];
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
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like  .likeheart{
    font-size: 32px;
    color:#<?php echo $gallery_default_params['gallery_img_ht_lightbox_heart_likedislike_thumb_color']; ?>;
    position: absolute;
    top: 4px;
    left: 3px;
    transition:0.3s ease;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active{
    color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_heart_likedislike_thumb_active_color']; ?> !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active{
<?php if($gallery_default_params['gallery_img_ht_lightbox_rating_count'] != 'off'):?>
    color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_active_font_color']; ?> !important;
<?php endif; ?>
}
@media screen and (min-width: 768px){
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like_thumb {
    <?php if($gallery_default_params['gallery_img_ht_lightbox_rating_count'] != 'off'):?>
        color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_active_font_color']; ?> !important;
    <?php endif; ?>
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .likeheart {
        color: #<?php echo $gallery_default_params['gallery_img_ht_lightbox_heart_likedislike_thumb_active_color']; ?> !important;
    }
}

/*/////Like/Dislike Styles END//////Heart//////*/
<?php
    break;
    }?>
@media screen and (max-width: 480px){
    .element_<?php echo $galleryID; ?>{
        max-width:100% !important;
    }
}
</style>