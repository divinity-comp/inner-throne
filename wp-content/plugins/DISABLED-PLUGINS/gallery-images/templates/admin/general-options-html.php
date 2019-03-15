<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="wrap">
    <?php require(GALLERY_IMG_TEMPLATES_PATH.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'free-banner.php');?>
    <p class="pro_info">
        <?php echo __('These features are available in the Professional version of the plugin only.', 'gallery-images'); ?>
        <a href="http://huge-it.com/wordpress-gallery/" target="_blank" class="button button-primary"><?php echo __('Enable','gallery-images'); ?></a>
    </p>
    <div>
        <div id="poststuff">
            <div id="post-body-content" class="gallery-options">
                <div id="post-body-heading">
                    <h3 id="gen_option_title"><?php echo __('General Options', 'gallery-images'); ?></h3>
                    <a class="save-gallery-options button-primary"><?php echo __('Save', 'gallery-images'); ?></a>
                </div>

                <div style="clear: both;"></div>
                <div id="gallery-options-list">

                    <ul id="gallery-view-tabs">
                        <li>
                            <a href="#gallery-view-options-0"><?php echo __('Gallery/Content-Popup', 'gallery-images'); ?></a>
                        </li>
                        <li><a href="#gallery-view-options-1"><?php echo __('Content Slider', 'gallery-images'); ?></a>
                        </li>
                        <li>
                            <a href="#gallery-view-options-2"><?php echo __('Lightbox-Gallery', 'gallery-images'); ?></a>
                        </li>
                        <li><a href="#gallery-view-options-3"><?php echo __('Slider', 'gallery-images'); ?></a></li>
                        <li><a href="#gallery-view-options-4"><?php echo __('Thumbnails', 'gallery-images'); ?></a></li>
                        <li><a href="#gallery-view-options-5"><?php echo __('Justified', 'gallery-images'); ?></a></li>
                        <li>
                            <a href="#gallery-view-options-6"><?php echo __('Blog Style Gallery', 'gallery-images'); ?></a>
                        </li>
	                    <li>
                            <a href="#gallery-view-options-7"><?php echo __('Elastic Grid', 'gallery-images'); ?></a>
                        </li>
                    </ul>
                    <ul class="options-block" id="gallery-view-tabs-contents">
                        <div class="gallery_options_grey_overlay"></div>
                        <li class="gallery-view-options-0">
                            <img style="width: 100%;margin-top: -12px;"
                                 src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/popup-tab-1.png'; ?>'>
                        </li>
                        <li class="gallery-view-options-1">
                            <img style="width: 100%;margin-top: -12px;"
                                 src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/content-slider-tab-2.png'; ?>'>
                        </li>
                        <li class="gallery-view-options-2">
                            <img style="width: 100%;margin-top: -12px;"
                                 src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/lightbox-tab3.png'; ?>'>
                        </li>
                        <li class="gallery-view-options-3">
                            <img style="width: 100%;margin-top: -12px;"
                                 src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/slider-tab4.png'; ?>'>
                        </li>
                        <li class="gallery-view-options-4">
                            <img style="width: 100%;margin-top: -12px;"
                                 src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/thumbnails-tab-5.png'; ?>'>
                        </li>
                        <li class="gallery-view-options-5">
                            <img style="width: 100%;margin-top: -12px;"
                                 src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/justified-tab-6.png'; ?>'>
                        </li>
                        <li class="gallery-view-options-6">
                            <img style="width: 100%;margin-top: -12px;"
                                 src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/block-tab-7.png'; ?>'>
                        </li>
	                    <li class="gallery-view-options-7">
		                    <img style="width: 100%;margin-top: -12px;"
		                         src='<?php echo GALLERY_IMG_IMAGES_URL . '/admin_images/elastic-options.jpg'; ?>'>
	                    </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="option" value=""/>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="controller" value="options"/>
<input type="hidden" name="op_type" value="styles"/>
<input type="hidden" name="boxchecked" value="0"/>