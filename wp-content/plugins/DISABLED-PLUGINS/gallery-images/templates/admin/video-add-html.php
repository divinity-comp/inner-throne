<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div id="huge_it_gallery_add_videos" style="display: none;">
    <div id="huge_it_gallery_add_videos_wrap">
        <h2><?php echo __('Add Video URL From Youtube or Vimeo', 'gallery-img'); ?></h2>
        <div class="control-panel">
            <form method="post"
                  action="admin.php?page=galleries_huge_it_gallery&task=gallery_video&id=<?php echo $id ?>&closepop=1">
                <input type="text" id="huge_it_add_video_input" name="huge_it_add_video_input"/>
                <button class='save-slider-options button-primary huge-it-insert-video-button'
                        id='huge-it-insert-video-button'><?php echo __('Insert Video', 'gallery-img'); ?></button>
                <div id="add-video-popup-options">
                    <div>
                        <div>
                            <label for="show_title"><?php echo __('Title:', 'gallery-img'); ?></label>
                            <div>
                                <input name="show_title" value="" type="text"/>
                            </div>
                        </div>
                        <div>
                            <label for="show_description"><?php echo __('Description:', 'gallery-img'); ?></label>
                            <textarea id="show_description" name="show_description"></textarea>
                        </div>
                        <div>
                            <label for="show_url"><?php echo __('Url:', 'gallery-img'); ?></label>
                            <input type="text" name="show_url" value=""/>
                        </div>
                    </div>
                </div>
                <?php wp_nonce_field('huge_it_gallery_nonce_add_video','huge_it_gallery_nonce_add_video');?>
            </form>
        </div>
    </div>
</div>
