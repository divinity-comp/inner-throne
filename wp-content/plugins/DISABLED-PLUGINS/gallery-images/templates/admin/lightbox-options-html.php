<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<?php
$path_site2             = plugins_url( "../images", __FILE__ );
$lightbox_options_nonce = wp_create_nonce( 'huge_it_gallery_nonce_save_lightbox_options' );
?>

<div id="post-body-heading">
    <h3 class="gen-option-title"><?php echo __( 'Lightbox Options', 'gallery-img' ); ?></h3>
    <a onclick="document.getElementById('adminForm').submit()"
       class="save-gallery-options button-primary"><?php echo __( 'Save', 'gallery-img' ); ?></a>
</div>
<form action="admin.php?page=Options_gallery_lightbox_styles&task=save&huge_it_gallery_nonce_save_lightbox_options=<?php echo $lightbox_options_nonce; ?>" method="post" id="adminForm"
      name="adminForm">
    <ul id="lightbox_type">
        <li class="<?php if ( get_option('gallery_img_lightbox_type') == 'new_type' ) {
            echo "active";
        } ?>">
            <label for="new_type">New Type</label>
            <input type="checkbox" name="params[gallery_img_lightbox_type]"
                   id="new_type" <?php if ( get_option('gallery_img_lightbox_type') == 'new_type' ) {
                echo 'checked';
            } ?>
                   value="new_type">
        </li>
        <li class="<?php if ( get_option('gallery_img_lightbox_type') == 'old_type' ) {
            echo "active";
        } ?>">
            <label for="old_type">Old Type</label>
            <input type="checkbox" name="params[gallery_img_lightbox_type]"
                   id="old_type" <?php if ( get_option('gallery_img_lightbox_type') == 'old_type' ) {
                echo 'checked';
            } ?>
                   value="old_type">
        </li>
    </ul>
    <div id="new-lightbox-options-list"
         class="unique-type-options-wrapper <?php if ( get_option('gallery_img_lightbox_type') == 'new_type' ) {
             echo "active";
         } ?>">
        <div class=""></div>
        <div class="lightbox-options-block">
            <h3>General Options</h3>
            <div class="has-background">
                <label for="gallery_img_lightbox_lightboxView">Lightbox style
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <select id="gallery_img_lightbox_lightboxView" name="params[gallery_img_lightbox_lightboxView]">
                    <option <?php selected( 'view1', get_option('gallery_img_lightbox_lightboxView') ); ?>
                            value="view1">1
                    </option>
                    <option <?php selected( 'view2', get_option('gallery_img_lightbox_lightboxView') ); ?>
                            value="view2">2
                    </option>
                    <option <?php selected( 'view3', get_option('gallery_img_lightbox_lightboxView') ); ?>
                            value="view3">3
                    </option>
                    <option <?php selected( 'view4', get_option('gallery_img_lightbox_lightboxView') ); ?>
                            value="view4">4
                    </option>
                </select>
            </div>
            <div>
                <label for="gallery_img_lightbox_speed_new">Lightbox open speed
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set lightbox opening speed</p>
                        </div>
                    </div>
                </label>
                <input type="number" name="params[gallery_img_lightbox_speed_new]" id="gallery_img_lightbox_speed_new"
                       value="<?php echo get_option('gallery_img_lightbox_speed_new'); ?>"
                       class="text">
                <span>ms</span>
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_overlayClose_new">Overlay close
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Check to enable close by Esc key.</p>
                        </div>
                    </div>
                </label>
                <input type="hidden" value="false" name="params[gallery_img_lightbox_overlayClose_new]"/>
                <input type="checkbox"
                       id="gallery_img_lightbox_overlayClose_new" <?php if ( get_option('gallery_img_lightbox_overlayClose_new') == 'true' ) {
                    echo 'checked="checked"';
                } ?> name="params[gallery_img_lightbox_overlayClose_new]" value="true"/>
            </div>
            <div>
                <label for="gallery_img_lightbox_style">Loop content
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Check to enable repeating images after one cycle.</p>
                        </div>
                    </div>
                </label>
                <input type="hidden" value="false" name="params[gallery_img_lightbox_loop_new]"/>
                <input type="checkbox"
                       id="gallery_img_lightbox_loop_new" <?php if ( get_option('gallery_img_lightbox_loop_new') == 'true' ) {
                    echo 'checked="checked"';
                } ?> name="params[gallery_img_lightbox_loop_new]" value="true"/>
            </div>
        </div>

        <div class="lightbox-options-block gallery_img_lightbox_options_grey_overlay">
            <h3>Dimensions<img src="<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/Gallery_Pro.png'; ?>"
                               class="gallery_img_lightbox_pro_logo"></h3>
            <div class="has-background">
                <label for="gallery_img_lightbox_width_new">Lightbox Width
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the width of the popup in percentages.</p>
                        </div>
                    </div>
                </label>
                <input type="number"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_width_new']; ?>"
                       class="text">
                <span>%</span>
            </div>
            <div>
                <label for="gallery_img_lightbox_height_new">Lightbox Height
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the height of the popup in percentages.</p>
                        </div>
                    </div>
                </label>
                <input type="number"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_height_new']; ?>"
                       class="text">
                <span>%</span>
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_videoMaxWidth">Lightbox Video maximum width
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the maximum width of the popup in pixels, the height will be fixed automatically.</p>
                        </div>
                    </div>
                </label>
                <input type="number"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_videoMaxWidth']; ?>"
                       class="text">
                <span>px</span>
            </div>
        </div>
        <div class="lightbox-options-block gallery_img_lightbox_options_grey_overlay">
            <h3>Slideshow<img src="<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/Gallery_Pro.png'; ?>"
                              class="gallery_img_lightbox_pro_logo"></h3>
            <div class="has-background">
                <label for="gallery_img_lightbox_slideshow_new">Slideshow
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the width of popup</p>
                        </div>
                    </div>
                </label>
                <input type="hidden" value="false" name="params[gallery_img_lightbox_slideshow_new]"/>
                <input type="checkbox"
                       id="gallery_img_lightbox_slideshow_new" <?php if ( $gallery_default__params['gallery_img_lightbox_slideshow_new'] == 'true' ) {
                    echo 'checked="checked"';
                } ?> name="params[gallery_img_lightbox_slideshow_new]" value="true"/>
            </div>
            <div>
                <label for="gallery_img_lightbox_slideshow_auto_new">Slideshow auto start
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the width of popup</p>
                        </div>
                    </div>
                </label>
                <input type="hidden" value="false" name="params[gallery_img_lightbox_slideshow_auto_new]"/>
                <input type="checkbox"
                       id="gallery_img_lightbox_slideshow_auto_new" <?php if ( $gallery_default__params['gallery_img_lightbox_slideshow_auto_new'] == 'true' ) {
                    echo 'checked="checked"';
                } ?> name="params[gallery_img_lightbox_slideshow_auto_new]" value="true"/>
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_slideshow_speed_new">Slideshow interval
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the height of popup</p>
                        </div>
                    </div>
                </label>
                <input type="number" name="params[gallery_img_lightbox_slideshow_speed_new]"
                       id="gallery_img_lightbox_slideshow_speed_new"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_slideshow_speed_new']; ?>"
                       class="text">
                <span>ms</span>
            </div>
        </div>
        <div class="lightbox-options-block gallery_img_lightbox_options_grey_overlay" style=" margin-top: -150px;">
            <h3>Advanced Options<img src="<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/Gallery_Pro.png'; ?>"
                                     class="gallery_img_lightbox_pro_logo"></h3>
            <div class="has-background">
                <label for="gallery_img_lightbox_style">EscKey close
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"
                       id="gallery_img_lightbox_escKey_new"/>
            </div>
            <div>
                <label for="gallery_img_lightbox_keyPress_new">Keyboard navigation
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"
                       id="gallery_img_lightbox_keyPress_new"/>
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_arrows">Show Arrows
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"
                       id="gallery_img_lightbox_arrows" checked/>
            </div>
            <div>
                <label for="gallery_img_lightbox_mouseWheel">Mouse Wheel Navigaion
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"
                       id="gallery_img_lightbox_mouseWheel" />
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_download">Show Download Button
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"
                       id="gallery_img_lightbox_download" />
            </div>
            <div>
                <label for="gallery_img_lightbox_showCounter">Show Counter
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"
                       id="gallery_img_lightbox_showCounter" />
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_sequence_info">Sequence Info text
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="text"
                       style="width: 13%"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_sequence_info']; ?>"
                       class="text">
                X <input type="text"
                         style="width: 13%"
                         value="<?php echo $gallery_default__params['gallery_img_lightbox_sequenceInfo']; ?>"
                         class="text">
                XX
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_slideAnimationType">Transition type
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <select id="gallery_img_lightbox_slideAnimationType" >
                    <option <?php selected( 'effect_1', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_1">Effect 1
                    </option>
                    <option <?php selected( 'effect_2', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_2">Effect 2
                    </option>
                    <option <?php selected( 'effect_3', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_3">Effect 3
                    </option>
                    <option <?php selected( 'effect_4', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_4">Effect 4
                    </option>
                    <option <?php selected( 'effect_5', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_5">Effect 5
                    </option>
                    <option <?php selected( 'effect_6', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_6">Effect 6
                    </option>
                    <option <?php selected( 'effect_7', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_7">Effect 7
                    </option>
                    <option <?php selected( 'effect_8', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_8">Effect 8
                    </option>
                    <option <?php selected( 'effect_9', $gallery_default__params['gallery_img_lightbox_slideAnimationType'] ); ?>
                            value="effect_9">Effect 9
                    </option>
                </select>
            </div>
        </div>
        <div class="lightbox-options-block gallery_img_lightbox_options_grey_overlay">
            <h3>Lightbox Watermark styles<img src="<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/Gallery_Pro.png'; ?>"
                                              class="gallery_img_lightbox_pro_logo"></h3>
            <div class="has-background">
                <label for="gallery_img_lightbox_watermark">Watermark
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the width of popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"
                       id="gallery_img_lightbox_watermark"  />
            </div>
            <div>
                <label for="gallery_img_lightbox_watermark_text">Watermark Text
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="text"  id="gallery_img_lightbox_watermark_text"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_text']; ?>"
                       class="text">
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_watermark_textColor">Watermark Text Color
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="text" class="color" id="gallery_img_lightbox_watermark_textColor"
                       value="#<?php echo $gallery_default__params['gallery_img_lightbox_watermark_textColor']; ?>"
                       size="10"/>
            </div>
            <div>
                <label for="gallery_img_lightbox_watermark_textFontSize">Watermark Text Font Size
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="number"
                       id="gallery_img_lightbox_watermark_textFontSize"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_textFontSize']; ?>"
                       class="text">
                <span>px</span>
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_watermark_containerBackground">Watermark Background Color
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="text" class="color" id="gallery_img_lightbox_watermark_containerBackground"
                       value="#<?php echo $gallery_default__params['gallery_img_lightbox_watermark_containerBackground']; ?>"
                       size="10"/>
            </div>
            <div>
                <label for="gallery_img_lightbox_watermark_containerOpacity">Watermark Background Opacity
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <div class="slider-container">
                    <input id="gallery_img_lightbox_watermark_containerOpacity" data-slider-highlight="true"
                           data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true"
                           value="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_containerOpacity']; ?>"/>
                    <span><?php echo $gallery_default__params['gallery_img_lightbox_watermark_containerOpacity']; ?>
                        %</span>
                </div>
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_watermark_containerWidth">Watermark Width
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="number"
                       id="gallery_img_lightbox_watermark_containerWidth"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_containerWidth']; ?>"
                       class="text">
                <span>px</span>
            </div>
            <div class="has-height">
                <label for="gallery_img_lightbox_watermark_containerWidth">Watermark Position
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <div>
                    <table class="bws_position_table">
                        <tbody>
                        <tr>
                            <td><input type="radio" value="1" id="watermark_top-left"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '1' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="2" id="watermark_top-center"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '2' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="3" id="watermark_top-right"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '3' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                        </tr>
                        <tr>
                            <td><input type="radio" value="4" id="watermark_middle-left"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '4' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="5" id="watermark_middle-center"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '5' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="6" id="watermark_middle-right"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '6' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                        </tr>
                        <tr>
                            <td><input type="radio" value="7" id="watermark_bottom-left"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '7' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="8" id="watermark_bottom-center"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '8' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="9" id="watermark_bottom-right"
                                    <?php if ( $gallery_default__params['gallery_img_lightbox_watermark_position_new'] == '9' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="has-background">
                <label for="gallery_img_lightbox_watermark_margin">Watermark Margin
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <input type="number"
                       id="gallery_img_lightbox_watermark_margin"
                       value="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_margin']; ?>"
                       class="text">
                <span>px</span>
            </div>
            <div class="has-background" style="display: none">
                <label for="gallery_img_lightbox_watermark_opacity">Watermark Text Opacity
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <div class="slider-container">
                    <input id="gallery_img_lightbox_watermark_opacity" data-slider-highlight="true"
                           data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true"
                           value="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_opacity']; ?>"/>
                    <span><?php echo $gallery_default__params['gallery_img_lightbox_watermark_opacity']; ?>%</span>
                </div>
            </div>
            <div style="height:auto;">
                <label for="watermark_image_btn">Select Watermark Image
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the image of Lightbox watermark.</p>
                        </div>
                    </div>
                </label>
                <img src="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_img_src_new']; ?>"
                     id="watermark_image_new" style="width:120px;height:auto;">
                <input type="button" class="button wp-media-buttons-icon"
                       style="margin-left: 63%;width: auto;display: inline-block;" id="watermark_image_btn_new"
                       value="Change Image">
                <input type="hidden" id="img_watermark_hidden_new" value="<?php echo $gallery_default__params['gallery_img_lightbox_watermark_img_src_new']; ?>">
            </div>
        </div>
        <div class="lightbox-options-block gallery_img_lightbox_options_grey_overlay" style="margin-top: -325px;">
            <h3>Social Share Buttons<img src="<?php echo GALLERY_IMG_IMAGES_URL.'/admin_images/Gallery_Pro.png'; ?>"
                                         class="gallery_img_lightbox_pro_logo"></h3>
            <div class="has-background">
                <label for="gallery_img_lightbox_socialSharing">Social Share Buttons
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Set the width of popup</p>
                        </div>
                    </div>
                </label>
                <input type="checkbox"  id="gallery_img_lightbox_socialSharing"  />
            </div>
            <div class="social-buttons-list">
                <label>Social Share Buttons List
                    <div class="help">?
                        <div class="help-block">
                            <span class="pnt"></span>
                            <p>Choose the style of your popup</p>
                        </div>
                    </div>
                </label>
                <div>
                    <table>
                        <tr>
                            <td>
                                <label for="gallery_img_lightbox_facebookButton">Facebook
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_facebookButton" <?php if ( $gallery_default__params['gallery_img_lightbox_facebookButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_twitterButton">Twitter
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_twitterButton" <?php if ( $gallery_default__params['gallery_img_lightbox_twitterButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_googleplusButton">Google Plus
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_googleplusButton" <?php if ( $gallery_default__params['gallery_img_lightbox_googleplusButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_pinterestButton">Pinterest
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_pinterestButton" <?php if ( $gallery_default__params['gallery_img_lightbox_pinterestButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="gallery_img_lightbox_linkedinButton">Linkedin
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_linkedinButton" <?php if ( $gallery_default__params['gallery_img_lightbox_linkedinButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_tumblrButton">Tumblr
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_tumblrButton" <?php if ( $gallery_default__params['gallery_img_lightbox_tumblrButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_redditButton">Reddit
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_redditButton" <?php if ( $gallery_default__params['gallery_img_lightbox_redditButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_bufferButton">Buffer
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_bufferButton" <?php if ( $gallery_default__params['gallery_img_lightbox_bufferButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="gallery_img_lightbox_vkButton">Vkontakte
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_vkButton" <?php if ( $gallery_default__params['gallery_img_lightbox_vkButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_yummlyButton">Yumly
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_yummlyButton" <?php if ( $gallery_default__params['gallery_img_lightbox_yummlyButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
                                <label for="gallery_img_lightbox_diggButton">Digg
                                    <input type="checkbox"
                                           id="gallery_img_lightbox_diggButton" <?php if ( $gallery_default__params['gallery_img_lightbox_diggButton'] == 'true' ) {
                                        echo 'checked="checked"';
                                    } ?>  value="true"/></label>
                            </td>
                            <td>
</form>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="lightbox-options-list"
         class="unique-type-options-wrapper <?php if ( get_option('gallery_img_lightbox_type') == 'old_type' ) {
             echo "active";
         } ?>">
        <div class="lightbox-options-block">
            <h3><?php echo __( 'Dimensions', 'gallery-img' ); ?></h3>

            <div class="has-background">
                <label for="light_box_size_fix"><?php echo __( 'Popup size fix', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" name="params[gallery_img_light_box_size_fix]"/>
                <input type="checkbox"
                       id="light_box_size_fix" <?php if ( $gallery_img_get_option[ 'gallery_img_light_box_size_fix' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?> name="params[gallery_img_light_box_size_fix]" value="true"/>
            </div>

            <div class="fixed-size">
                <label for="light_box_width"><?php echo __( 'Popup width', 'gallery-img' ); ?></label>
                <input type="number" name="params[gallery_img_light_box_width]" id="light_box_width"
                       value="<?php echo $gallery_img_get_option[ 'gallery_img_light_box_width' ]; ?>" class="text">
                <span>px</span>
            </div>

            <div class="has-background fixed-size">
                <label for="light_box_height"><?php echo __( 'Popup height', 'gallery-img' ); ?></label>
                <input type="number" name="params[gallery_img_light_box_height]" id="light_box_height"
                       value="<?php echo $gallery_img_get_option[ 'gallery_img_light_box_height' ]; ?>" class="text">
                <span>px</span>
            </div>

            <div class="not-fixed-size">
                <label for="light_box_maxwidth"><?php echo __( 'Popup maxWidth', 'gallery-img' ); ?></label>
                <input type="number" name="params[gallery_img_light_box_maxwidth]" id="light_box_maxwidth"
                       value="<?php echo $gallery_img_get_option[ 'gallery_img_light_box_maxwidth' ]; ?>" class="text">
                <span>px</span>
            </div>
            <div class="has-background not-fixed-size">
                <label
                        for="light_box_maxheight"><?php echo __( 'Popup maxHeight', 'gallery-img' ); ?></label>
                <input type="number" name="params[gallery_img_light_box_maxheight]" id="light_box_maxheight"
                       value="<?php echo $gallery_img_get_option[ 'gallery_img_light_box_maxheight' ]; ?>" class="text">
                <span>px</span>
            </div>
            <div>
                <label
                        for="light_box_initialwidth"><?php echo __( 'Popup initial width', 'gallery-img' ); ?></label>
                <input type="number" name="params[gallery_img_light_box_initialwidth]" id="light_box_initialwidth"
                       value="<?php echo $gallery_img_get_option[ 'gallery_img_light_box_initialwidth' ]; ?>" class="text">
                <span>px</span>
            </div>
            <div class="has-background">
                <label
                        for="light_box_initialheight"><?php echo __( 'Popup initial height', 'gallery-img' ); ?></label>
                <input type="number" name="params[gallery_img_light_box_initialheight]" id="light_box_initialheight"
                       value="<?php echo $gallery_img_get_option[ 'gallery_img_light_box_initialheight' ]; ?>" class="text">
                <span>px</span>
            </div>
        </div>
        <div class="lightbox-options-block lightbox-grey-wrapper" >
            <h3 class="section_for_pro" ><?php echo __( 'Internationalization', 'gallery-img' ); ?></h3>
            <?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); ?>
            <div class="has-background">
                <label for="light_box_style"><?php echo __( 'Lightbox style', 'gallery-img' ); ?></label>
                <select id="light_box_style" >
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_style' ] == '1' ) {
                        echo 'selected="selected"';
                    } ?> value="1">1
                    </option>
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_style' ] == '2' ) {
                        echo 'selected="selected"';
                    } ?> value="2">2
                    </option>
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_style' ] == '3' ) {
                        echo 'selected="selected"';
                    } ?> value="3">3
                    </option>
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_style' ] == '4' ) {
                        echo 'selected="selected"';
                    } ?> value="4">4
                    </option>
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_style' ] == '5' ) {
                        echo 'selected="selected"';
                    } ?> value="5">5
                    </option>
                </select>
            </div>
            <div>
                <label
                        for="light_box_transition"><?php echo __( 'Transition type', 'gallery-img' ); ?></label>
                <select id="light_box_transition" >
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_transition' ] == 'elastic' ) {
                        echo 'selected="selected"';
                    } ?> value="elastic"><?php echo __( 'Elastic', 'gallery-img' ); ?></option>
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_transition' ] == 'fade' ) {
                        echo 'selected="selected"';
                    } ?> value="fade"><?php echo __( 'Fade', 'gallery-img' ); ?></option>
                    <option <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_transition' ] == 'none' ) {
                        echo 'selected="selected"';
                    } ?> value="none"><?php echo __( 'none', 'gallery-img' ); ?></option>
                </select>
            </div>
            <div class="has-background">
                <label for="light_box_speed"><?php echo __( 'Opening speed', 'gallery-img' ); ?></label>
                <input type="number" id="light_box_speed"
                       value="<?php echo $gallery_img_get_default_options[ 'gallery_img_light_box_speed' ]; ?>" class="text">
                <span><?php echo __( 'ms', 'gallery-img' ); ?></span>
            </div>
            <div>
                <label for="light_box_fadeout"><?php echo __( 'Closing speed', 'gallery-img' ); ?></label>
                <input type="number" id="light_box_fadeout"
                       value="<?php echo $gallery_img_get_default_options[ 'gallery_img_light_box_fadeout' ]; ?>" class="text">
                <span><?php echo __( 'ms', 'gallery-img' ); ?></span>
            </div>
            <div class="has-background">
                <label for="light_box_title"><?php echo __( 'Show the title', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_title" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_title' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?>  value="true"/>
            </div>
            <div>
                <label
                        for="light_box_opacity"><?php echo __( 'Overlay transparency', 'gallery-img' ); ?></label>
                <div class="slider-container">
                    <input  id="light_box_opacity" data-slider-highlight="true"
                            data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true"
                            value="<?php echo $gallery_img_get_default_options[ 'gallery_img_light_box_opacity' ]; ?>"/>
                    <span><?php echo $gallery_img_get_default_options[ 'gallery_img_light_box_opacity' ]; ?>%</span>
                </div>
            </div>
            <div class="has-background">
                <label for="light_box_open"><?php echo __( 'Auto open', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_open" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_open' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?> />
            </div>
            <div>
                <label
                        for="light_box_overlayclose"><?php echo __( 'Overlay close', 'gallery-img' ); ?><?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_overlayclose' ] ) {
                        echo "true";
                    } ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_overlayclose" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_overlayclose' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?> value="true"/>
            </div>
            <div class="has-background">
                <label for="light_box_esckey"><?php echo __( 'EscKey close', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_esckey" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_esckey' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?> />
            </div>
            <div>
                <label
                        for="light_box_arrowkey"><?php echo __( 'Keyboard navigation', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_arrowkey" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_arrowkey' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?> />
            </div>
            <div class="has-background">
                <label for="light_box_loop"><?php echo __( 'Loop content', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_loop" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_loop' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?> />
            </div>
            <div>
                <label
                        for="light_box_closebutton"><?php echo __( 'Show close button', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_closebutton" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_closebutton' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?>  value="true"/>
            </div>
        </div>
        <div class="lightbox-options-block lightbox-grey-wrapper" style="margin-top:-310px" >
            <h3 class="section_for_pro"><?php echo __( 'Slideshow', 'gallery-img' ); ?></h3>
            <div class="has-background">
                <label for="light_box_slideshow"><?php echo __( 'Slideshow', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_slideshow" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_slideshow' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?>  value="true"/>
            </div>
            <div>
                <label
                        for="light_box_slideshowspeed"><?php echo __( 'Slideshow interval', 'gallery-img' ); ?></label>
                <input type="number"  id="light_box_slideshowspeed"
                       value="<?php echo $gallery_img_get_default_options[ 'gallery_img_light_box_slideshowspeed' ]; ?>" class="text">
                <span><?php echo __( 'ms', 'gallery-img' ); ?></span>
            </div>
            <div class="has-background">
                <label
                        for="light_box_slideshowauto"><?php echo __( 'Slideshow auto start', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_slideshowauto" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_slideshowauto' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?>  value="true"/>
            </div>
            <div>
                <label
                        for="light_box_slideshowstart"><?php echo __( 'Slideshow start button text', 'gallery-img' ); ?></label>
                <input type="text"  id="light_box_slideshowstart"
                       value="<?php echo esc_attr( $gallery_img_get_default_options[ 'gallery_img_light_box_slideshowstart' ] ); ?>" class="text">
            </div>
            <div class="has-background">
                <label
                        for="light_box_slideshowstop"><?php echo __( 'Slideshow stop button text', 'gallery-img' ); ?></label>
                <input type="text"  id="light_box_slideshowstop"
                       value="<?php echo esc_attr( $gallery_img_get_default_options[ 'gallery_img_light_box_slideshowstop' ] ); ?>" class="text">
            </div>
        </div>
        <div class="lightbox-options-block lightbox-grey-wrapper" style="margin-top: -20px;">
            <h3 class="section_for_pro"><?php echo __( 'Positioning', 'gallery-img' ); ?></h3>

            <div class="has-background">
                <label for="light_box_fixed"><?php echo __( 'Fixed position', 'gallery-img' ); ?></label>
                <input type="hidden" value="false" />
                <input type="checkbox"
                       id="light_box_fixed" <?php if ( $gallery_img_get_default_options[ 'gallery_img_light_box_fixed' ] == 'true' ) {
                    echo 'checked="checked"';
                } ?>  value="true"/>
            </div>
            <div class="has-height">
                <label for=""><?php echo __( 'Popup position', 'gallery-img' ); ?></label>
                <div>
                    <table class="bws_position_table">
                        <tbody>
                        <tr>
                            <td><input type="radio" value="1" id="slideshow_title_top-left"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '1' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="2" id="slideshow_title_top-center"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '2' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="3" id="slideshow_title_top-right"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '3' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                        </tr>
                        <tr>
                            <td><input type="radio" value="4" id="slideshow_title_middle-left"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '4' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="5" id="slideshow_title_middle-center"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '5' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="6" id="slideshow_title_middle-right"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '6' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                        </tr>
                        <tr>
                            <td><input type="radio" value="7" id="slideshow_title_bottom-left"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '7' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="8" id="slideshow_title_bottom-center"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '8' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                            <td><input type="radio" value="9" id="slideshow_title_bottom-right"
                                    <?php if ( $gallery_img_get_default_options[ 'gallery_img_lightbox_open_position' ] == '9' ) {
                                        echo 'checked="checked"';
                                    } ?> /></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</form>

<input type="hidden" name="option" value=""/>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="controller" value="options"/>
<input type="hidden" name="op_type" value="styles"/>
<input type="hidden" name="boxchecked" value="0"/>