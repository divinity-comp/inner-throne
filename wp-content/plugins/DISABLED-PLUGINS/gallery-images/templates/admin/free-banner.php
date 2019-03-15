<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="gallery_free_version_banner" <?php if( isset($_COOKIE['hgGalleryFreeBannerShow']) && $_COOKIE['hgGalleryFreeBannerShow'] == "no" ){ echo 'style="display:none"'; } ?> >
    <a class="close_gallery_free_banner">+</a>
    <img class="manual_icon" src="<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/icon-user-manual.png';?>" alt="user manual" />
    <p class="usermanual_text">If you have any difficulties in using the options, Follow the link to <a href="https://goo.gl/l5LlYA" target="_blank">User Manual</a></p>
    <a class="get_full_version" href="https://goo.gl/NmLZX2" target="_blank">GET THE FULL VERSION</a>
    <a href="http://huge-it.com" target="_blank"><img class="huge_it_logo" src="<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/Huge-It-logo.png'; ?>"/></a>
    <div style="clear: both;"></div>
    <div class="hg_social_link_buttons">
        <a target="_blank" class="fb" href="https://www.facebook.com/hugeit/"></a>
        <a target="_blank" class="twitter"  href="https://twitter.com/HugeITcom"></a>
        <a target="_blank" class="gplus" href="https://plus.google.com/111845940220835549549"></a>
        <a target="_blank" class="yt"  href="https://www.youtube.com/channel/UCueCH_ulkgQZhSuc0L5rS5Q"></a>
    </div>
    <div class="hg_view_plugins_block">
        <a target="_blank"  href="https://wordpress.org/support/plugin/gallery-images/reviews/">Rate Us</a>
        <a target="_blank"  href="https://goo.gl/4Q5i1X">Full Demo</a>
        <a target="_blank"  href="https://goo.gl/EdtZBM">FAQ</a>
        <a target="_blank"  href="http://huge-it.com/contact-us/">Contact Us</a>
    </div>
    <div  class="description_text"><p>This is the Lite version of the plugin. Click "GET THE FULL VERSION" for more advanced options and customization possibilities. We appreciate your attention and cooperation.</p></div>
    <div style="clear: both;"></div>
</div>