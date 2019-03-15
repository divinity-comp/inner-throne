<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Gallery_Img_General_Options
{

    /**
     * Loads General options page
     */
    public function load_page()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'Options_gallery_styles') {
            if (isset($_GET['task'])) {
                if ($_GET['task'] == 'save') {
                    do_action('gallery_img_save_general_options');
                }
            } else {
                $this->show_page();
            }
        }
    }

    /**
     * Shows General options page
     */
    public function show_page()
    {
        require(GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'general-options-html.php');
    }
}