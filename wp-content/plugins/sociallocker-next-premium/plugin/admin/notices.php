<?php

// ------------------------------------------------------------------------------------------
// StyleRoller
// ------------------------------------------------------------------------------------------
 
    /**
     * Shows offers to purhcase the StyleRoller from time to time.
     * 
     * @since 3.5.0
     */
    function onp_sl_styleroller_notices( $notices ) {
        if ( defined('ONP_SL_STYLER_PLUGIN_ACTIVE') ) return $notices;

        // show messages only for administrators
        if ( !factory_325_is_administrator() ) return $notices; global $sociallocker;
if ( in_array( $sociallocker->license->type, array( 'free','trial' ) ) ) {

            return $notices;
        
}


        global $sociallocker;
        $closed = get_option('factory_notices_closed', array());

        // leans when the premium versio was activated
        $premiumActivated = isset( $sociallocker->license->data['Activated'] )
            ? $sociallocker->license->data['Activated'] // for new users
            : 0;                                        // for old users

        $isNewUser = ( $premiumActivated !== 0 );
        $secondsInDay = 60*60*24;

        $inSeconds = time() - $premiumActivated;
        $inDays = $inSeconds / $secondsInDay;

        $forceToShow = defined('ONP_DEBUG_SHOW_STYLEROLLER_MESSAGE') && ONP_DEBUG_SHOW_STYLEROLLER_MESSAGE;
        $lang = $sociallocker->options['lang'];

        // offers a discount for new users who purchased the Social Locker a day ago
        if ( ( $isNewUser && $inDays >= 1 && $inDays <= 3 && !isset( $closed['sociallocker-styleroller-after-a-day'] ) )
              || $forceToShow ) {

            $premiumActivated = $premiumActivated + 24 * 60 * 60;
            $expiresIn = ceil( ( 3 - $inDays ) * 24 );

            $notices[] = array(
                'id'        => 'sociallocker-styleroller-after-a-day',

                // content and color
                'class'     => 'call-to-action sociallocker-styleroller-banner onp-sl-'.$lang,
                'header'    => '<span class="onp-hightlight">' . __('You\'ve got the 40% discount on the StyleRoller Add-On!', 'plugin-sociallocker') . '</span>' . sprintf( __('(Expires In %sh)', 'plugin-sociallocker'), $expiresIn ),
                'message'   => __('<p>It\'s a day since you activated the Social Locker. We would like to make you a small gift, the 40% discount on the StyleRoller Add-on. This is a time-limited offer which will be valid within 2 days.</p><p>The StyleRoller Add-on will help you to brand the Social Locker to fit the look and feel of your website, create your own unique attention-grabbing themes and, as a result, increase the number of likes and shares.</p>', 'plugin-sociallocker'),   
                'plugin'    => $sociallocker->pluginName,
                'where'     => array('plugins','dashboard', 'edit'),

                // buttons and links
                'buttons'   => array(
                    array(
                        'class'     => 'btn btn-primary',
                        'title'     => __('Get StyleRoller For 40% Off', 'plugin-sociallocker'),
                        'action'    => $sociallocker->options['styleroller'] . '-special/?' . http_build_query(array(
                            'onp_special' => md5( $premiumActivated ) . $premiumActivated,
                            'onp_target' => base64_encode( get_site_url() ),
                            'utm_source' => 'plugin',
                            'utm_medium' => 'styleroller-banner',
                            'utm_campaign' => 'after-a-day'
                        ))
                    ),
                    array(
                        'title'     => __('Hide this message', 'plugin-sociallocker'),
                        'class'     => 'btn btn-default',
                        'action'    => 'x'
                    )
                )
            );
        }

        // offers a discount for new users who purchased the Social Locker a week ago
        if ( ( $isNewUser && $inDays >= 7 && $inDays <= 9 && !isset( $closed['sociallocker-styleroller-after-a-week'] ) ) || $forceToShow ) {

            $premiumActivated = $premiumActivated + 7 * 24 * 60 * 60;
            $expiresIn = ceil( ( 9 - $inDays ) * 24 );

            $notices[] = array(
                'id'        => 'sociallocker-styleroller-after-a-week',

                // content and color
                'class'     => 'call-to-action sociallocker-styleroller-banner onp-sl-'.$lang,
                'icon'      => 'fa fa-frown-o',  
                'header'    => '<span class="onp-hightlight">' . __('Last Chance To Get StyleRoller For 40% Off!', 'plugin-sociallocker') . '</span>' . sprintf( __('(Expires In %sh)', 'plugin-sociallocker'), $expiresIn ),
                'message'   => __('We have noticed you have been using the Social Locker already more than a week. Did you know what via the StyleRoller, an add-on for creating own attention-grabbing themes, you can improve conversions of your lockers by up to 300%? Learn how, click the button below.', 'plugin-sociallocker'),   
                'plugin'    => $sociallocker->pluginName,
                'where'     => array('plugins','dashboard', 'edit'),

                // buttons and links
                'buttons'   => array(
                    array(
                        'class'     => 'btn btn-primary',
                        'title'     => __('Get StyleRoller For 40% Off', 'plugin-sociallocker'),
                        'action'    => $sociallocker->options['styleroller'] . '-special/?' . http_build_query(array(
                            'onp_special' => md5( $premiumActivated ) . $premiumActivated,
                            'onp_target' => base64_encode( get_site_url() ),
                            'utm_source' => 'plugin',
                            'utm_medium' => 'styleroller-banner',
                            'utm_campaign' => 'after-a-week'
                        ))
                    ),
                    array(
                        'title'     => __('Hide this message', 'plugin-sociallocker'),
                        'class'     => 'btn btn-default',
                        'action'    => 'x'
                    )
                )
            );
        }

        // this banner only for old users
        if ( ( !$isNewUser ) || $forceToShow ) {

            $firstShowTime = get_option('onp_sl_styleroller_firt_time', false);
            if ( !$firstShowTime ) { 
                $firstShowTime = time();
                update_option( 'onp_sl_styleroller_firt_time', time() );
            }

            $inSeconds = time() - $firstShowTime;
            $inDays = $inSeconds / $secondsInDay;

            // this offer is available only 2 days
            if ( ( $inDays <= 2 && !isset( $closed['sociallocker-styleroller-new-addon'] ) ) || $forceToShow ) {

                $expiresIn = ceil( ( 2 - $inDays ) * 24 );

                $notices[] = array(
                    'id'        => 'sociallocker-styleroller-new-addon',

                    // content and color
                    'class'     => 'call-to-action sociallocker-styleroller-banner onp-sl-'.$lang,
                    'icon'      => 'fa fa-frown-o',  
                    'header'    => '<span class="onp-hightlight">' . __('You\'ve got the 40% discount on the StyleRoller Add-On!', 'plugin-sociallocker') . '</span>' . sprintf( __('(Expires In %sh)', 'plugin-sociallocker'), $expiresIn ),
                    'message'   => __('We would like to make you a small gift, the 40% discount on the StyleRoller Add-on. This is a time-limited offer which will be valid within 2 days. The StyleRoller Add-on will help you to brand the Social Locker to fit the look and feel of your website, create your own unique attention-grabbing themes and, as a result, increase the number of likes and shares.', 'plugin-sociallocker'),   
                    'plugin'    => $sociallocker->pluginName,
                    'where'     => array('plugins','dashboard', 'edit'),

                    // buttons and links
                    'buttons'   => array(
                        array(
                            'class'     => 'btn btn-primary',
                            'title'     => __('Get StyleRoller For 40% Off', 'plugin-sociallocker'),
                            'action'    => $sociallocker->options['styleroller'] . '-special/?' . http_build_query(array(
                                'onp_special' => md5( $firstShowTime ) . $firstShowTime,
                                'onp_target' => base64_encode( get_site_url() ),
                                'utm_source' => 'plugin',
                                'utm_medium' => 'styleroller-banner',
                                'utm_campaign' => 'new-addon'
                            ))
                        ),
                        array(
                            'title'     => __('Hide this message', 'plugin-sociallocker'),
                            'class'     => 'btn btn-default',
                            'action'    => 'x'
                        )
                    )
                ); 
            }

            // this offer apperas after a week withing a day
            if ( ( $inDays >= 7 && $inDays <= 9 && !isset( $closed['sociallocker-styleroller-new-addon-after-a-week'] ) ) || $forceToShow ) {

                $firstShowTime = $firstShowTime + 7 * 24 * 60 * 60;
                $expiresIn = ceil( ( 9 - $inDays ) * 24 );

                $notices[] = array(
                        'id'        => 'sociallocker-styleroller-new-addon-after-a-week',

                    // content and color
                    'class'     => 'call-to-action sociallocker-styleroller-banner onp-sl-'.$lang,
                    'icon'      => 'fa fa-frown-o',  
                    'header'    => '<span class="onp-hightlight">' . __('Last Chance To Get StyleRoller For 40% Off!', 'plugin-sociallocker') . '</span>' . sprintf( __('(Expires In %sh)', 'plugin-sociallocker'), $expiresIn ),
                    'message'   => __('Did you know what via the StyleRoller, an add-on for creating own attention-grabbing themes for Social Locker, you can improve conversions of your lockers by up to 300%? Click the button to learn more and get the discount.', 'plugin-sociallocker'),   
                    'plugin'    => $sociallocker->pluginName,
                    'where'     => array('plugins','dashboard', 'edit'),

                    // buttons and links
                    'buttons'   => array(
                        array(
                            'class'     => 'btn btn-primary',
                            'title'     => __('Get StyleRoller For 40% Off', 'plugin-sociallocker'),
                            'action'    => $sociallocker->options['styleroller'] . '-special/?' . http_build_query(array(
                                    'onp_special' => md5( $firstShowTime ) . $firstShowTime,
                                    'onp_target' => base64_encode( get_site_url() ),
                                    'utm_source' => 'plugin',
                                    'utm_medium' => 'styleroller-banner',
                                    'utm_campaign' => 'new-addon-after-a-week'
                            ))
                        ),
                        array(
                            'title'     => __('Hide this message', 'plugin-sociallocker'),
                            'class'     => 'btn btn-default',
                            'action'    => 'x'
                        )
                    )
                );
            }
        }

        return $notices;
    }

    add_filter('factory_notices_' . $sociallocker->pluginName, 'onp_sl_styleroller_notices');

    /**
     * Assets for the StyleRoller banner.
     */
    function onp_sl_assets_for_styleroller_notices( $hook ) {

        // sytles for the plugin notices
        if ( $hook == 'index.php' || $hook == 'plugins.php' || $hook == 'edit.php' ) {
            
            wp_enqueue_style( 'sociallocker-notices', SOCIALLOCKER_URL . '/plugin/admin/assets/css/notices.010000.css' ); 
            wp_enqueue_script( 'sociallocker-notices', SOCIALLOCKER_URL . '/plugin/admin/assets/js/notices.010000.js' );    
        }
    }
    
    add_action('admin_enqueue_scripts', 'onp_sl_assets_for_styleroller_notices' );

