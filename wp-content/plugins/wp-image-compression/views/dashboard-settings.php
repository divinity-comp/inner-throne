<?php
$lazy_load = unserialize( get_option('_wpimage_lazyload_options') );
$lazy_load['enable_lazyload'] = isset( $lazy_load['enable_lazyload'] ) ? $lazy_load['enable_lazyload'] : false;
$lazy_load['lazyload_expand'] = isset( $lazy_load['lazyload_expand'] ) ? $lazy_load['lazyload_expand'] : false;
$lazy_load['lazyload_iframe'] = isset( $lazy_load['lazyload_iframe'] ) ? $lazy_load['lazyload_iframe'] : false;
$lazy_load['lazyload_autosize'] = isset( $lazy_load['lazyload_autosize'] ) ? $lazy_load['lazyload_autosize'] : false;
$lazy_load['lazyload_optimumx'] = isset( $lazy_load['lazyload_optimumx'] ) ? $lazy_load['lazyload_optimumx'] : 'false';
$lazy_load['lazyload_intrinsicRatio'] = isset( $lazy_load['lazyload_intrinsicRatio'] ) ? $lazy_load['lazyload_intrinsicRatio'] : 'false';
$lazy_load['lazyload_preloadAfterLoad'] = isset( $lazy_load['lazyload_preloadAfterLoad'] ) ? $lazy_load['lazyload_preloadAfterLoad'] : 'false';

$quality_options = array(
    'manual' => esc_html( 'Manual', 'optimisationio' ),
    'auto' => esc_html('Auto', 'optimisationio'),
    'auto:best' => esc_html('Automatic: best quality', 'optimisationio'),
    'auto:good' => esc_html('Automatic: good quality', 'optimisationio'),
    'auto:eco' => esc_html('Automatic: economy mode', 'optimisationio'),
    'auto:low' => esc_html('Automatic: low quality', 'optimisationio')
);

?>
<div class="addon-settings" data-sett-group="wp-image-compression">

    <form action="<?php echo esc_url( admin_url( 'admin.php?page=optimisationio-dashboard' ) ); ?>" method="post">

        <div class="addon-settings-tabs">
            <ul>
                <li data-tab-setting="image-convertor" class="active"><?php esc_html_e('Image convertor', 'optimisationio'); ?></li>
                <li data-tab-setting="lazy-load-images"><?php esc_html_e('Lazy load images', 'optimisationio'); ?></li>
            </ul>
        </div>

        <div class="addon-settings-section">

            <div data-tab-setting="image-convertor" class="addon-settings-content active">

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'JPG image quality (Default Auto)', 'optimisationio' ); ?></div>
                    <div class="field-right">
                        <?php $auto_quality = get_option('wpimages_quality_auto', 'auto'); ?>
                        <select name="wpimages_quality_auto"> <?php
                            foreach ($quality_options as $key => $val) {
                                echo '<option value="' . $key . '" ' . selected( $key, $auto_quality ) . '>' . $val . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="field sub-field manual-quality-group">
                    <div class="field-left"><?php esc_html_e( 'Image quality (%)', 'optimisationio'); ?></div>
                    <div class="field-right">
                        <select name="wpimages_quality" id="manual_quality"><?php
                            $q = get_option( 'wpimages_quality', WPIMAGE_DEFAULT_QUALITY );
                            for ($x = 10; $x <= 100; $x = $x + 10) {
                                echo "<option" . ( $q === $x ? " selected='selected'" : "" ) . ">" . $x . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left"><?php esc_html_e('Convert BMP To JPG', 'optimisationio'); ?></div>
                    <div class="field-right"><?php Optimisationio_Dashboard::checkbox_component(
                        'wpimages_bmp_to_jpg',
                        get_option( 'wpimages_bmp_to_jpg', WPIMAGE_DEFAULT_BMP_TO_JPG )
                    );
                    ?></div>
                </div>

            <!--     <div class="field">
                    <div class="field-left"><?php esc_html_e('Convert PNG To JPG', 'optimisationio'); ?></div>
                    <div class="field-right"><?php Optimisationio_Dashboard::checkbox_component(
                        'wpimages_png_to_jpg',
                        get_option( 'wpimages_png_to_jpg', WPIMAGE_DEFAULT_PNG_TO_JPG )
                    );
                    ?>
                    </div>
                </div> -->

                <div class="field">
                    <div class="field-left">
                        <strong style="text-transform: uppercase;"><?php esc_attr_e('Images uploaded within a Page/Post', 'optimisationio'); ?></strong>
                        <br/><br/>
                        <span>Fit within</span> <input type="number" name="wpimages_max_width" value="<?php echo get_option('wpimages_max_width', WPIMAGE_DEFAULT_MAX_WIDTH); ?>" /> <span>x</span> <input type="number" name="wpimages_max_height" value="<?php echo get_option('wpimages_max_height', WPIMAGE_DEFAULT_MAX_HEIGHT); ?>" /> <span>pixels width/height</span> <?php _e(" ( or enter 0 to disable )", 'wpimage');?>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left">
                        <strong style="text-transform: uppercase;"><?php esc_attr_e('Images uploaded directly to the Media Library', 'optimisationio'); ?></strong>
                        <br/><br/>
                        <span>Fit within</span> <input type="number" name="wpimages_max_width_library" value="<?php echo get_option('wpimages_max_width_library', WPIMAGE_DEFAULT_MAX_WIDTH); ?>" /> <span>x</span> <input type="number" name="wpimages_max_height_library" value="<?php echo get_option('wpimages_max_height_library', WPIMAGE_DEFAULT_MAX_HEIGHT); ?>" /> <span>pixels width/height</span> <?php _e(" (or enter 0 to disable)", 'wpimage');?>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left">
                        <strong style="text-transform: uppercase;"><?php esc_attr_e('Images uploaded elsewhere (Theme headers, backgrounds, logos, etc)', 'optimisationio'); ?></strong>
                        <br/><br/>
                        <span>Fit within</span> <input type="number" name="wpimages_max_width_other" value="<?php echo get_option('wpimages_max_width_other', WPIMAGE_DEFAULT_MAX_WIDTH); ?>" /> <span>x</span> <input type="number" name="wpimages_max_height_other" value="<?php echo get_option('wpimages_max_height_other', WPIMAGE_DEFAULT_MAX_HEIGHT); ?>" /> <span>pixels width/height</span> <?php _e(" (or enter 0 to disable)", 'wpimage');?>
                    </div>
                </div>

            </div>

            <div data-tab-setting="lazy-load-images" class="addon-settings-content">

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'Enable Lazy loading', 'optimisationio'); ?></div>
                    <div class="field-right">
                        <div class="optio-check-component">
                            <input id="id-_wpimage_lazyload[enable_lazyload]" class="optio-check optio-check-light" type="checkbox" name="_wpimage_lazyload[enable_lazyload]" <?php checked( $lazy_load['enable_lazyload'], 'true' ); ?> value="true"/>
                            <label for="id-_wpimage_lazyload[enable_lazyload]" class="optio-check-btn"></label>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'Lazyload iframes', 'optimisationio'); ?></div>
                    <div class="field-right">
                        <div class="optio-check-component">
                            <input id="id-_wpimage_lazyload[lazyload_iframe]" class="optio-check optio-check-light" type="checkbox" name="_wpimage_lazyload[lazyload_iframe]" <?php checked( $lazy_load['lazyload_iframe'], 'true' ); ?> value="true"/>
                            <label for="id-_wpimage_lazyload[lazyload_iframe]" class="optio-check-btn"></label>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'Calculate sizes attribute automatically', 'optimisationio'); ?></div>
                        <div class="field-right">
                        <div class="optio-check-component">
                            <input id="id-_wpimage_lazyload[lazyload_autosize]" class="optio-check optio-check-light" type="checkbox" name="_wpimage_lazyload[lazyload_autosize]" <?php checked( $lazy_load['lazyload_autosize'], 'true' ); ?> value="true"/>
                            <label for="id-_wpimage_lazyload[lazyload_autosize]" class="optio-check-btn"></label>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'Expand / Threshold', 'optimisationio'); ?></div>
                    <div class="field-right">
                        <input type='number' min="40" max="400" name="_wpimage_lazyload[lazyload_expand]" value="<?php echo $lazy_load['lazyload_expand']; ?>">
                    </div>
                </div>

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'Optmiumx (max. high DPI)', 'optimisationio'); ?></div>
                    <div class="field-right">
                        <select name="_wpimage_lazyload[lazyload_optimumx]">
                            <option value='false' <?php selected( $lazy_load['lazyload_optimumx'], 'false' ); ?>>no HIGH DPI constraints</option>
                            <option value='auto' <?php selected( $lazy_load['lazyload_optimumx'], 'auto' ); ?>>auto (recommended if you use img[srcset])</option>
                            <option value='2' <?php selected( $lazy_load['lazyload_optimumx'], 2 ); ?>>2</option>
                            <option value='1.6' <?php selected( $lazy_load['lazyload_optimumx'], 1.6 ); ?>>1.6</option>
                            <option value='1.2' <?php selected( $lazy_load['lazyload_optimumx'], 1.2 ); ?>>1.2</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'Responsive intrinsic ratio box', 'optimisationio'); ?></div>
                    <div class="field-right">
                        <select name="_wpimage_lazyload[lazyload_intrinsicRatio]">
                            <option value='false' <?php selected( $lazy_load['lazyload_intrinsicRatio'], 'false' ); ?>>no intrinsic ratio box</option>
                            <option value='true' <?php selected( $lazy_load['lazyload_intrinsicRatio'], 'true' ); ?>>intrinsic ratio box (recommended)</option>
                            <option value='animated' <?php selected( $lazy_load['lazyload_intrinsicRatio'], 'animated' ); ?>>animated intrinsic ratio box</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <div class="field-left"><?php esc_html_e( 'Load after onload', 'optimisationio'); ?></div>
                    <div class="field-right">
                        <select name="_wpimage_lazyload[lazyload_preloadAfterLoad]">
                            <option value='false' <?php selected( $lazy_load['lazyload_preloadAfterLoad'], 'false' ); ?>>Off</option>
                            <option value='true' <?php selected( $lazy_load['lazyload_preloadAfterLoad'], 'true' ); ?>>On</option>
                            <option value='smart' <?php selected( $lazy_load['lazyload_preloadAfterLoad'], 'smart' ); ?>>Smart (desktop - on, mobile - off)</option>
                        </select>
                    </div>
                </div>

            </div>

        </div>

        <div class="addon-settings-actions-section">
            <input type="submit" class="button button-primary button-large" name="optimisation_save_image_compression_settings" value="<?php esc_attr_e("Save settings", "optimisationio"); ?>" />
        </div>

        <?php wp_nonce_field( 'optimisationio-image-compression-settings', 'optimisationio_image_compression_settings' ); ?>

    </form>
</div>
