<style>
.view9_container:nth-last-child(3){
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
    border:none !important;
}
.view9_container{
    width: <?php echo $gallery_default_params['gallery_img_ht_view9_general_width']; ?>%;
    float:<?php if($gallery_default_params['gallery_img_view9_general_position'] == 'left' || $gallery_default_params['gallery_img_view9_general_position'] == 'center') { echo 'none'; }
					elseif($gallery_default_params['gallery_img_view9_general_position'] == 'right') { echo 'right'; }?>;
<?php if($gallery_default_params['gallery_img_view9_general_position'] == 'center') { echo 'margin:-27px auto;'; }?>;
    margin-bottom: <?php echo $gallery_default_params['gallery_img_ht_view9_general_space']; ?>px !important;
    padding-bottom: <?php echo 44+$gallery_default_params['gallery_img_ht_view9_general_space']; ?>px !important;
    border-bottom:  <?php echo $gallery_default_params['gallery_img_ht_view9_general_separator_size']; ?>px
    <?php if($gallery_default_params['gallery_img_view9_general_separator_style'] == 'none') { echo 'none'; }
          elseif($gallery_default_params['gallery_img_view9_general_separator_style'] == 'solid') { echo 'solid'; }
          elseif($gallery_default_params['gallery_img_view9_general_separator_style'] == 'dashed') { echo 'dashed'; }
          elseif($gallery_default_params['gallery_img_view9_general_separator_style'] == 'dotted') { echo 'dotted'; }
          elseif($gallery_default_params['gallery_img_view9_general_separator_style'] == 'groove') { echo 'groove'; }
          elseif($gallery_default_params['gallery_img_view9_general_separator_style'] == 'double') { echo 'double'; }?> #<?php echo $gallery_default_params['gallery_img_ht_view9_general_separator_color']; ?>;
}
.iframe_cont{
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%;
}
.view9_img{
    display:block;
    margin: 0 auto;
    width: 100%
}
.new_view_title{
    font-size:<?php echo $gallery_default_params['gallery_img_ht_view9_title_fontsize']; ?>px !important;
    color:<?php echo '#'.$gallery_default_params['gallery_img_ht_view9_title_color']; ?> !important;
<?php if($gallery_default_params['gallery_img_ht_view9_element_title_show'] == 'false') { echo 'display:none;'; }?>;
<?php if($gallery_default_params['gallery_img_view9_title_textalign'] == 'left') { echo 'text-align:left;'; }
      elseif($gallery_default_params['gallery_img_view9_title_textalign'] == 'right') { echo 'text-align:right;'; }
      elseif($gallery_default_params['gallery_img_view9_title_textalign'] == 'center') { echo 'text-align:center;'; }
      elseif($gallery_default_params['gallery_img_view9_title_textalign'] == 'justify') { echo 'text-align:justify;'; }?>;
    padding-left:5px;
    background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_view9_title_back_color'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_view9_title_opacity"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	      ?>;
}
.new_view_desc ul{
    list-style-type: none;
}
.new_view_desc{
    margin-top: 15px;
    font-size:<?php echo $gallery_default_params['gallery_img_ht_view9_desc_fontsize']; ?>px !important;
    color:<?php echo '#'.$gallery_default_params['gallery_img_ht_view9_desc_color']; ?> !important;
<?php if($gallery_default_params['gallery_img_ht_view9_element_desc_show'] == 'false') { echo 'display:none;'; }?>;
<?php if($gallery_default_params['gallery_img_view9_desc_textalign'] == 'left') { echo 'text-align:left;'; }
  elseif($gallery_default_params['gallery_img_view9_desc_textalign'] == 'right') { echo 'text-align:right;'; }
  elseif($gallery_default_params['gallery_img_view9_desc_textalign'] == 'center') { echo 'text-align:center;'; }
  elseif($gallery_default_params['gallery_img_view9_desc_textalign'] == 'justify') { echo 'text-align:justify;'; }?>;
    background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_view9_desc_back_color'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_view9_desc_opacity"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	      ?>;
}
.video_blog_view{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.paginate{
    font-size:<?php echo $gallery_default_params['gallery_img_ht_view9_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$gallery_default_params['gallery_img_ht_view9_paginator_color']; ?> !important;
    text-align: <?php echo $gallery_default_params['gallery_img_view9_paginator_position']; ?>;
}
.paginate a{
    border-bottom: none !important;
}
.icon-style{
    font-size: <?php echo $gallery_default_params['gallery_img_ht_view9_paginator_icon_size']; ?>px !important;
    color:<?php echo '#'.$gallery_default_params['gallery_img_ht_view9_paginator_icon_color']; ?> !important;;
}
.load_more {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($gallery_default_params['gallery_img_video_view9_loadmore_position'] == 'left') {echo 'left';}
			elseif ($gallery_default_params['gallery_img_video_view9_loadmore_position'] == 'center') { echo 'center'; }
			elseif($gallery_default_params['gallery_img_video_view9_loadmore_position'] == 'right') { echo 'right'; }?>;
    width:100%;
}
.load_more_button {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $gallery_default_params['gallery_img_video_ht_view9_loadmore_fontsize']; ?>px !important;
    color:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view9_loadmore_font_color']; ?> !important;
    background:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view9_button_color']; ?> !important;
    cursor:pointer;
}
.load_more_button:hover{
    color:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view9_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$gallery_default_params['gallery_img_video_ht_view9_button_color_hover']; ?> !important;
}
.loading {
    display:none;
}
.clear{
    clear:both;
}
.blog_img_wrapper{
    position: relative;
}
<?php	switch ($like_dislike) {
case "dislike":
    ?>
/*/////Like/Dislike Styles BEGIN//////like/dislike//////*/
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
    z-index: 999;
    margin-top: 15px;
    display: table;
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_likedislike_font_color']; ?>;
    cursor: pointer;
    float: right;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper ,
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper {
    position:relative;
    background:<?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_blog_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_blog_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	?>;
    display: inline-block;
    border-radius: 3px;
    font-size: 0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper{
    margin: 3px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper{
    margin: 3px 3px 3px 0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like{
    font-size: 0;
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
    font-size: 0;
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
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_likedislike_thumb_color']; ?>;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .dislike_thumb_down{
    font-size: 17px;
    position:absolute;
    top: 4px;
    left: 4px;
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_likedislike_thumb_color']; ?>;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_hide{
    display: none !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active{
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_likedislike_thumb_active_color']; ?> !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active{
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_active_font_color']; ?> !important;
}
@media screen and (min-width: 768px){
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like {
        color: #<?php echo $gallery_default_params['gallery_img_ht_blog_active_font_color']; ?> !important;
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .like_thumb_up {
        color: #<?php echo $gallery_default_params['gallery_img_ht_blog_likedislike_thumb_active_color']; ?> !important;
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .huge_it_dislike {
        color: #<?php echo $gallery_default_params['gallery_img_ht_blog_active_font_color']; ?> !important;
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_dislike_wrapper:hover .dislike_thumb_down {
        color: #<?php echo $gallery_default_params['gallery_img_ht_blog_likedislike_thumb_active_color']; ?> !important;
    }
}
/*/////Like/Dislike Styles END////////////*/
<?php break;
          case 'heart':
?>
/*/////Like/Dislike Styles BEGIN//////Heart//////*/
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?>{
    position: absolute;
    top: 0;
    right: 0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper  {
    position:relative;
    background:<?php
			list($r,$g,$b) = array_map('hexdec',str_split($gallery_default_params['gallery_img_ht_blog_likedislike_bg'],2));
				$titleopacity=$gallery_default_params["gallery_img_ht_blog_likedislike_bg_trans"]/100;
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	?>;
    display: inline-block;
    border-radius: 8px;
    font-size: 0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover{
    background: #D6D4D4;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper{
    margin: 3px;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like{
    font-size: 0;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_like .huge_it_like_thumb {
    display: block;
    float: left;
<?php $heartCount='';
if($gallery_default_params['gallery_img_ht_blog_rating_count'] == 'off'){
    $heartCount="transparent";
}else{
    $heartCount='#'.$gallery_default_params['gallery_img_ht_blog_likedislike_font_color'];
}
?>;
    color:<?php echo $heartCount; ?>;
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
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_heart_likedislike_thumb_color']; ?>;
    position: absolute;
    top: 4px;
    left: 3px;
    transition:0.3s ease;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_thumb_active{
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_heart_likedislike_thumb_active_color']; ?> !important;
}
.huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .like_font_active{
<?php if($gallery_default_params['gallery_img_ht_blog_rating_count'] != 'off'):?>
    color: #<?php echo $gallery_default_params['gallery_img_ht_blog_active_font_color']; ?> !important;
<?php endif; ?>
}
@media screen and (min-width: 768px){
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .huge_it_like_thumb {
    <?php if($gallery_default_params['gallery_img_ht_blog_rating_count'] != 'off'):?>
        color: #<?php echo $gallery_default_params['gallery_img_ht_blog_active_font_color']; ?> !important;
    <?php endif; ?>
    }
    .huge_it_gallery_like_cont_<?php echo $galleryID.$pID; ?> .huge_it_gallery_like_wrapper:hover .likeheart {
        color: #<?php echo $gallery_default_params['gallery_img_ht_blog_heart_likedislike_thumb_active_color']; ?> !important;
    }
}
/*/////Like/Dislike Styles END//////Heart//////*/
<?php 
          break;
          }?>
</style>