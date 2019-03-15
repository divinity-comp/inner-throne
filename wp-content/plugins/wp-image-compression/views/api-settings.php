<?php
global $wpdb;

if (isset($_POST['optimisation_save_publitio_api_settings'])) {
	update_option('wpimages_use_our_image_cdn', (isset($_POST['wpimages_use_our_image_cdn'])) ? "1" : "");
}
?>

<div class="sidebar-publitio-api">

    <div class="sidebar-title-wrap">
        <span><?php esc_html_e('API Settings', 'optimisationio');?></span>
    </div>

    <form action="<?php echo esc_url(admin_url('admin.php?page=optimisationio-dashboard')); ?>" method="post">

        <div class="sidebar-content-wrap">

            <div class="addon-settings-content auto-table-layout">
                <div class="field brd-top">
                    <div class="field-left"><?php esc_html_e('Use our CDN for your images', 'optimisationio');?></div>
                    <div class="field-right">
                <?php Optimisationio_Dashboard::checkbox_component('wpimages_use_our_image_cdn',get_option('wpimages_use_our_image_cdn'));
                ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="addon-settings-actions-section">
            <input type="submit" class="button button-primary button-large" name="optimisation_save_publitio_api_settings" value="<?php echo esc_attr("Save", "optimisationio"); ?>" />
        </div>
    </form>

</div>

<div id="cache_plugin_cdn_setting">
    
</div>

