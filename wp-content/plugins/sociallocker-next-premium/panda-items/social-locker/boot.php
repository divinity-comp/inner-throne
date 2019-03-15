<?php

define('BIZPANDA_SOCIAL_LOCKER_DIR', dirname(__FILE__));
define('BIZPANDA_SOCIAL_LOCKER_URL', plugins_url( null, __FILE__ ));

if ( is_admin() ) require BIZPANDA_SOCIAL_LOCKER_DIR . '/admin/boot.php';
global $bizpanda;

/**
 * Registers the Email Locker item.
 * 
 * @since 1.0.0
 */
function opanda_register_social_locker( $items ) {
    global $sociallocker;
    
    $title =  __('Social Locker', 'sociallocker');
        
        $items['social-locker'] = array(
            'name' => 'social-locker',
            'type' => 'premium',
            'title' => $title,
            'help' => opanda_get_help_url('sociallocker'),
            'description' => __('<p>Asks users to "pay with a like" or share to unlock content.</p><p>Perfect way to get more followers, attract social traffic and improve some social metrics.</p>', 'sociallocker'),
            'shortcode' => 'sociallocker',
            'plugin' => $sociallocker
        );
        
    


    return $items;
}
add_filter('opanda_items', 'opanda_register_social_locker', 1);

/**
 * Adds options to print at the frontend.
 * 
 * @since 1.0.0
 */
function opanda_social_locker_options( $options, $id ) {
    global $post;

    $options['groups'] = array('social-buttons');
    $options['socialButtons'] = array();
        $buttonOrder = 'twitter-tweet,facebook-like';
    

     
    $actualUrls = opanda_get_option('actual_urls', false);

    $postUrl = !empty($post) ? get_permalink( $post->ID ) : null;
    $postUrl = $actualUrls ? null : $postUrl;

    $fbLikeUrl = opanda_get_dynamic_url( $id, 'facebook_like_url', $postUrl);
    $fbShareUrl = opanda_get_dynamic_url( $id, 'facebook_share_url', $postUrl);
    $twTweetUrl = opanda_get_dynamic_url( $id, 'twitter_tweet_url', $postUrl);
    $twFollowUrl = opanda_get_dynamic_url( $id, 'twitter_follow_url', $postUrl);
    $glPlusUrl = opanda_get_dynamic_url( $id, 'google_plus_url', $postUrl);
    $glShareUrl = opanda_get_dynamic_url( $id, 'google_share_url', $postUrl);
    $lnShareUrl = opanda_get_dynamic_url( $id, 'linkedin_share_url', $postUrl);

    $options['socialButtons']['counters'] = opanda_get_item_option($id, 'show_counters', false, 1);
    $options['socialButtons']['order'] = opanda_get_item_option($id, 'buttons_order', false, $buttonOrder);
    
    $options['socialButtons']['behaviorOnError'] = get_option( 'opanda_adblock', 'show_error'); 
    $options['socialButtons']['behaviorError'] = get_option( 'opanda_adblock_error', __( 'Unable to create social buttons. Please make sure that nothing blocks loading of social scripts in your browser. Some browser extentions (Avast, PrivDog, AdBlock, Adguard etc.) or usage of private tabs in FireFox may cause this issue. Turn them off and try again.', 'bizpanda' ));
                        
    $options['socialButtons']['facebook'] = array(
        'appId' => opanda_get_option('facebook_appid', '117100935120196'),
        'lang' => opanda_get_option('lang', 'en_GB'),
        'version' => opanda_get_option('facebook_version', 'v1.0'),
        'like' => array(
            'url' => $fbLikeUrl,
            'title' => opanda_get_item_option($id, 'facebook_like_title' ),
            'theConfirmIssue' => opanda_get_item_option($id, 'facebook_like_confirm_issue' )
        )
    );
        
        $options['socialButtons']['facebook']['share'] = array(
            'url' => $fbShareUrl,
            'title' => opanda_get_item_option($id, 'facebook_share_title' ),
            'shareDialog' => true,
            'counter' => opanda_get_item_option($id, 'facebook_share_counter_url' )
        );
    

    
    $options['socialButtons']['twitter'] = array(
        'lang' => opanda_get_option('short_lang', 'en'),
        'tweet' => array(
            'url' => $twTweetUrl,
            'text' => opanda_get_item_option($id, 'twitter_tweet_text' ),
            'doubleCheck' => opanda_get_item_option($id, 'twitter_tweet_auth' ),
            'title' => opanda_get_item_option($id, 'twitter_tweet_title' ),
            'via' => opanda_get_item_option($id, 'twitter_tweet_via' )
        )
    );
        
        $options['socialButtons']['twitter']['follow'] = array(
            'url' => $twFollowUrl,
            'title' => opanda_get_item_option($id, 'twitter_follow_title' ),
            'doubleCheck' => opanda_get_item_option($id, 'twitter_follow_auth' ),
            'hideScreenName' => opanda_get_item_option($id, 'twitter_follow_hide_name', false, false )
        );    
    


    $options['socialButtons']['google'] = array(   
        'clientId' => opanda_get_option('google_client_id', ''),
        'lang' => opanda_get_option('google_lang', opanda_get_option('short_lang', 'en' )),
        'plus' => array(
            'url' => $glPlusUrl,
            'title' => opanda_get_item_option($id, 'google_plus_title' ),
            'prefilltext' => opanda_get_item_option($id, 'google_plus_text' )   
        )
    );

        $options['socialButtons']['google']['share'] = array(   
            'url' => $glShareUrl,
            'title' => opanda_get_item_option($id, 'google_share_title' ),
            'prefilltext' => opanda_get_item_option($id, 'google_share_text' )
        ); 

        $options['socialButtons']['youtube']['subscribe'] = array(   
            'clientId' => get_option( 'opanda_google_client_id' ),
            'channelId' => opanda_get_item_option($id, 'google_youtube_channel_id' ),
            'title' => opanda_get_item_option($id, 'google_youtube_title' ) 
        );         
    


        $options['socialButtons']['linkedin'] = array(   
            'share' => array(
                'url' => $lnShareUrl,
                'title' => opanda_get_item_option($id, 'linkedin_share_title' ) 
            )
        );
    
    $allowedButtons = array('facebook-like', 'facebook-share', 'twitter-tweet', 'twitter-follow', 'google-plus', 'google-share', 'youtube-subscribe', 'linkedin-share');
    $allowedButtons = apply_filters('opanda_social-locker_allowed_buttons', $allowedButtons);
    
    if ( $options['socialButtons']['order'] ) {
        $options['socialButtons']['order'] = explode( ',', $options['socialButtons']['order'] );
    }
    
    if ( empty( $options['socialButtons']['order'] ) ) {
        unset( $options['socialButtons']['order'] );
    } else {
        $filteredButtons = array();
        foreach( $options['socialButtons']['order'] as $buttonName ) {
            if ( !in_array( $buttonName, $allowedButtons ) ) continue;
            $filteredButtons[] = $buttonName;
        }
        $options['socialButtons']['order'] = $filteredButtons;
    }
    
    // - Replaces shortcodes in the locker message and twitter text

    $postTitle = $post != null ? $post->post_title : '';
    $postUrl = $post != null ? get_permalink($post->ID) : '';

    if ( !empty( $options['socialButtons']['twitter']['tweet']['text'] ) ) {
        $tweetText = str_replace('[post_title]', $postTitle, $options['socialButtons']['twitter']['tweet']['text'] );
        $options['socialButtons']['twitter']['tweet']['text'] = apply_filters('opanda_twitter_tweet_text', $tweetText, $id, $options);
    }
    
    if ( !empty( $options['socialButtons']['google']['plus']['prefilltext'] ) ) {
        $text = str_replace('[post_title]', $postTitle, $options['socialButtons']['google']['plus']['prefilltext'] );
        $options['socialButtons']['google']['plus']['prefilltext'] = apply_filters('opanda_google_plus_text', $text, $id, $options);
    } 
    
    if ( !empty( $options['socialButtons']['google']['share']['prefilltext'] ) ) {
        $text = str_replace('[post_title]', $postTitle, $options['socialButtons']['google']['share']['prefilltext'] );
        $options['socialButtons']['google']['share']['prefilltext'] = apply_filters('opanda_google_share_text', $text, $id, $options);
    } 
    
    return $options;
}

add_filter('opanda_social-locker_item_options', 'opanda_social_locker_options', 10, 2);

/**
 * Requests assets for email locker.
 */
function opanda_social_locker_assets( $lockerId, $options, $fromBody, $fromHeader ) {
    OPanda_AssetsManager::requestLockerAssets();

    // Miscellaneous
    OPanda_AssetsManager::requestTextRes(array(
        'misc_your_agree_with',
        'misc_agreement_checkbox',
        'misc_agreement_checkbox_alt',
        'misc_terms_of_use',
        'misc_privacy_policy',
        'misc_close',
        'misc_or_wait',
        'errors_not_signed_in',
        'errors_not_granted'
    ));
    
    // Errors & Notices
    OPanda_AssetsManager::requestTextRes(array(
        'errors_no_consent'
    ));

    if ( isset( $options['opanda_buttons_order'] ) && strpos( $options['opanda_buttons_order'], 'facebook' ) !== false ) {
        OPanda_AssetsManager::requestFacebookSDK();  
    }
}

add_action('opanda_request_assets_for_social-locker', 'opanda_social_locker_assets', 10, 4);

/**
 * A shortcode for the Social Locker
 * 
 * @since 1.0.0
 */
class OPanda_SocialLockerShortcode extends OPanda_LockerShortcode {
    
    /**
     * Shortcode name
     * @var string
     */
    public $shortcodeName = array( 
        'sociallocker', 'sociallocker-1', 'sociallocker-2', 'sociallocker-3', 'sociallocker-4'
    );
    
    protected function getDefaultId() {
        return get_option('opanda_default_social_locker_id');
    }
}

FactoryShortcodes320::register( 'OPanda_SocialLockerShortcode', $bizpanda );