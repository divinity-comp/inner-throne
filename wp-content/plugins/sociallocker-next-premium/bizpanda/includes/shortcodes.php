<?php

/**
 * A base shortcode for all lockers
 * 
 * @since 1.0.0
 */
class OPanda_LockerShortcode extends FactoryShortcodes320_Shortcode {

    // -------------------------------------------------------------------------------------
    // Includes assets
    // -------------------------------------------------------------------------------------

    public $assetsInHeader = true;
    
    /**
     * Defines what assets need to include.
     * The method is called separate from the Render method during shortcode registration.
     */
    public function assets( $attrs = array(), $fromBody = false, $fromHook = false ) {
        if ( is_admin() ) return false;

        if ( is_array( $attrs) ) {
            
            foreach( $attrs as $attr ) {
                $id = isset( $attr['id'] ) ? (int)$attr['id'] : $this->getDefaultId(); 
                OPanda_AssetsManager::requestAssets( $id, $fromBody, $fromHook ); 
            }

            return true;
        } else {
            return false;
        }
    }
    
    // -------------------------------------------------------------------------------------
    // Content render
    // -------------------------------------------------------------------------------------
        
    public function html($attr, $content) { 
        global $post;
        global $wp_embed; global $sociallocker;
if ( in_array( $sociallocker->license->type, array( 'free' ) ) ) {

                echo $content;
                return;
            
}

        


        $id = isset( $attr['id'] ) ? (int)$attr['id'] : $this->getDefaultId();
        if ( !empty( $id ) ) $lockerMeta = get_post_meta($id, '');
        
        if ( empty( $id ) || empty($lockerMeta) || empty($lockerMeta['opanda_item']) ) {
            printf( __('<div><strong>[Locker] The locker [id=%d] doesn\'t exist or the default lockers were deleted.</strong></div>', 'bizpanda'), $id );
            return;
        }
        
        // runs nested shortcodes
        
        $content = $wp_embed->autoembed($content);
        $content = do_shortcode( $content );

        // passcode
        
        if ( OPanda_AssetsManager::autoUnlock( $id ) ) {
            echo $content;
            return;
        }

            // - RSS and Members
            // if it's a premium build, check premium features such 
            // as RSS feeds and logged in users.
            
            if (is_feed()) {

                if ( get_option('opanda_rss', false) ) {
                    echo $content;
                    return;
                } else {
                    return;
                }
            }
            
            if ( is_user_logged_in() && OPanda_AssetsManager::getLockerOption($id, 'hide_for_member', false) ) {
                echo '<p>' . $content . '</p>';
                return;
            }

            if ( !empty($post) && OPanda_AssetsManager::getLockerOption($id, 'lock_delay', false) ) {
                $lockDelayInterval = OPanda_AssetsManager::getLockerOption($id, 'lock_delay_interval_in_seconds');

                $createdTime = get_post_time('U', true, $post);
                $currentTime = time();

                if ( $currentTime - $createdTime <= $lockDelayInterval ) {
                    echo '<p>' . $content . '</p>';
                    return;
                }
            }
        

        
        // if returns:
        // 'content' - shows the locker content
        // 'nothing' - shows nothing (cut content)
        // 'locker' or other values - shows the locker
        
        $whatToShow = apply_filters('onp_sl_what_to_show', 'locker', $id );
        if ( 'content' === $whatToShow ) { echo $content; return; }
        if ( 'nothing' === $whatToShow ) return;
        
        $content = preg_replace( '/^<br \/>/', '', $content );
        $content = preg_replace( '/<br \/>$/', '', $content );

        $lockData = OPanda_AssetsManager::getLockerDataToPrint( $id );
        
        // -
        // use the shortcode attrs if specified instead of configured option
        
        if ( isset( $attr['url'] ) ) {
            $lockData['options']['facebook']['like']['url'] = $attr['url'];
            $lockData['options']['facebook']['share']['url'] = $attr['url']; 
            $lockData['options']['twitter']['tweet']['url'] = $attr['url'];
            $lockData['options']['google']['plus']['url'] = $attr['url'];
            $lockData['options']['google']['share']['url'] = $attr['url'];
            $lockData['options']['linkedin']['share']['url'] = $attr['url'];     
        }
        
        if ( isset( $attr['title'] ) ) {
            $lockData['options']['text']['title'] = $attr['title'];    
        }  
        
        if ( isset( $attr['message'] ) ) {
            $lockData['options']['text']['message'] = $attr['message'];    
        } 
        
        if ( isset( $attr['theme'] ) ) {
            $lockData['options']['theme'] = $attr['theme'];    
        } 
        
        $lockData['options']['lazy'] = opanda_get_option('lazy', false) ? true : false;  
    
        $isAjax = false;
        $lockData['ajax'] = false;
        
            // - AJAX
            // if it's a premium build, check is ajax required?

            $contentHash = null;

            if (isset( $lockerMeta['opanda_ajax'] ) && $lockerMeta['opanda_ajax'][0] ) {
                if ( 'full' == OPanda_AssetsManager::getLockerOption($id, 'overlap', false, 'full') ) {

                    $isAjax = true;

                    $ajaxContent = '<p>' . $content . '</p>';
                    $lockData['contentHash'] = $contentHash = md5( $ajaxContent );
                    $lockData['ajax'] = true;

                    $metaKey = 'opanda_locker_content_hash_' . $contentHash;

                    if ( !isset( $lockerMeta[$metaKey] ) ) {
                        add_post_meta($id, $metaKey, $ajaxContent, true);
                    }
                }
            }
        


        $dynamicTheme = get_option('opanda_dynamic_theme', 0);
        
        $lockData['stats'] = get_option('opanda_tracking', false) ? true : false;

        $this->lockId = "onpLock" . rand(100000, 999999);
        $this->lockData = $lockData;
        
        $overlap = $lockData['options']['overlap']['mode'];
        
        $contentVisibility = get_option('opanda_content_visibility', 'auto');
        $hideContent = $overlap === 'full';
        
        if ( $contentVisibility == 'always_hidden') {
            $hideContent = true;
        } elseif ( $contentVisibility == 'always_visible') {
            $hideContent = false;  
        }

        if ($isAjax) { ?>
            <div class="onp-locker-call" style="display: none;" data-lock-id="<?php echo $this->lockId ?>"></div>
        <?php } else { ?>           
            <div class="onp-locker-call" <?php if ( $hideContent ) { ?>style="display: none;"<?php } ?> data-lock-id="<?php echo $this->lockId ?>">
                <p><?php echo  $content ?></p>
            </div>
        <?php } ?> 

        <?php 

        if ( $dynamicTheme ) { ?>
            <script type="text/bp-data" class="onp-optinpanda-params">
                <?php echo json_encode( $lockData ) ?>
            </script>
            <?php  do_action('opanda_print_locker_assets', $this->lockData['lockerId'], $this->lockData, $this->lockId ); ?>
        <?php } else {
           add_action('wp_footer', array($this, 'wp_footer'), 1);
           add_action('login_footer', array($this, 'wp_footer'), 1);           
        }
    }
    
    public function wp_footer() {
        $dynamicTheme = get_option('opanda_dynamic_theme', false);
        if ( !$dynamicTheme ) $this->printOptions();
    }
    
    public function printOptions() {    
    ?>
        <script>
            if ( !window.bizpanda ) window.bizpanda = {};
            if ( !window.bizpanda.lockerOptions ) window.bizpanda.lockerOptions = {};
            window.bizpanda.lockerOptions['<?php echo $this->lockId; ?>'] = <?php echo json_encode( $this->lockData ) ?>;
        </script>
        <?php  do_action('opanda_print_locker_assets', $this->lockData['lockerId'], $this->lockData, $this->lockId ); ?>
    <?php
    }
        
    // -------------------------------------------------------------------------------------
    // Shortcode Tracking
    // -------------------------------------------------------------------------------------
    
    /**
     * Defines whether the changes of post what includes shortcodes are tracked.
     * @var boolean 
     */
    public $track = true;

    /**
     * The function that will be called when a post containing a current shortcode is changed. 
     * @param string $shortcode
     * @param mixed[] $attr
     * @param string $content
     * @param integer $postId
     */
    public function onTrack($shortcode, $attr, $content, $postId) { 

        $id = isset( $attr['id'] ) ? (int)$attr['id'] : $this->getDefaultId(); 
        
        $lockerMeta = get_post_meta($id, '');
        if (empty($lockerMeta)) return;
        
        foreach($lockerMeta as $metaKey => $metaValue) {
            if (strpos($metaKey, 'opanda_locker_content_hash_') === 0) {
                delete_post_meta($id, $metaKey);
            }
        }
    }
}