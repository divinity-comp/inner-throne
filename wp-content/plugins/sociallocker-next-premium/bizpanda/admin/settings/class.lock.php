<?php
/**
 * The file contains a page that shows the common settings for the plugin.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2013, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The page Common Settings.
 * 
 * @since 1.0.0
 */
class OPanda_AdvancedSettings extends OPanda_Settings  {
 
    public $id = 'advanced';
    
    /**
     * Shows the header html of the settings screen.
     * 
     * @since 1.0.0
     * @return void
     */
    public function header() {
        global $optinpanda;
        ?>
        <p>
            <?php _e('Options linked with the locking feature. Don\'t change the options here if you are not sure that you do.', 'bizpanda' )?>
        </p>
        <?php
    }

    /**
     * A page to edit the Advanced Options.
     * 
     * @since v3.7.2
     * @return vod
     */
    public function getOptions() {
        global $optinpanda;

        $forms = array();
        
        $forms[] = array(
            'type' => 'separator'
        );
        
        $forms[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'debug',
            'title'     => __( 'Debug', 'bizpanda' ),
            'hint'      => __( 'If this option turned on, the plugin displays information about why the locker is not visible.', 'bizpanda' )
        );

        $forms[] = array(
            'type' => 'separator'
        );
        
        $forms[] = array(
            'type'      => 'textbox',
            'name'      => 'passcode',
            'title'     => __( 'Pass Code', 'bizpanda' ),
            'hint'      => sprintf( __( 'Optional. When the pass code is contained in your website URL, the locked content gets automatically unlocked.<br/><div class="opanda-example"><strong>Usage example:</strong> <a href="#" class="opanda-url" target="_blank">%s<span class="opanda-passcode"></span></a></div>', 'bizpanda' ), site_url() ),
            'default'   => false
        );
        
        $forms[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'permanent_passcode',
            'title'     => __( 'Permanent Unlock<br /> For Pass Code', 'bizpanda' ),
            'hint'      => __( 'Optional. If On, your lockers will be revealed forever if the user once opened the page URL with the Pass Code.<br />Otherwise your lockers will be unlocked only when the page URL contains the Pass Code.', 'bizpanda' ),
            'default'   => false
        );
        
        if ( BizPanda::hasPlugin('sociallocker') ) {
            
            $forms[] = array(
                'type' => 'separator'
            );

            $forms[] = array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'interrelation',
                'title'     => __( 'Interrelation', 'bizpanda' ),
                'hint'      => __( 'Set On to make lockers interrelated. When one of the interrelated lockers are unlocked on your site, the others will be unlocked too.<br /> Recommended to turn on, if you use the Batch Locking feature.', 'bizpanda' ),
                'default'   => false
            );
        
        }
        
        if ( BizPanda::hasPlugin('optinpanda') ) {
            
            if ( !BizPanda::hasPlugin('sociallocker') ) {
                $forms[] = array(
                    'type' => 'separator'
                );
            }

            $forms[] = array(
                'type'      => 'dropdown',
                'data'      => array(
                    array( 'byemail', __('Unlock all email lockers having the same list together.', 'bizpanda') ),
                    array( 'bypage', __('Unlock all email lockers located on the same page together.', 'bizpanda') )
                ),
                'name'      => 'emaillocker_mode',
                'default'   => 'byemail',
                'title'     => __( 'Email Lockers', 'bizpanda' ),
                'hint'      => __( 'Sets what parameter will be used to link and unlock email lockers.', 'bizpanda' )
            );
            
            $forms[] = array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'forbid_temp_emails',
                'default'   => false,
                'title'     => __( 'Forbid Temp Emails', 'bizpanda' ),
                'hint'      => __( 'If On, the locker will not accept temporary email address to unlock content.', 'bizpanda' )
            );    
            
            $tempDomains = self::getTempEmailDomains();
            
            $forms[] = array(
                'type'      => 'div',
                'id'        => 'temp_domains_list',
                'items'     => array(
                    array(
                        'type'      => 'textarea',
                        'name'      => 'temp_domains',
                        'default'   => implode(', ', $tempDomains),
                        'title'     => __( 'Forbid Email Domains', 'bizpanda' ),
                        'hint'      => __( 'A list of domains (and their parts) used for creating temporary email addresses. You can edit it or leave it as is.', 'bizpanda' )
                    )
                )
            ); 
        }

        $forms[] = array(
            'type' => 'separator'
        );
        
        $forms[] = array(
            'type'      => 'dropdown',
            'data'      => array(
                array( 'visible_with_warning', __('Show Locker With Warning', 'bizpanda') ),
                array( 'visible', __('Show Locker As Usual', 'bizpanda') ),
                array( 'hidden', __('Hide Locker', 'bizpanda') ),
            ),
            'name'      => 'in_app_browsers',
            'default'   => 'visible_with_warning',
            'title'     => __( 'In-App Browsers', 'bizpanda' ),
            'hint'      => __( 'Optional. By default the locker appears when a page is opened in in-app mobile browsers like Facebook In-App Browser, Instagram In-App Browser (and others). For some users the locker may not work properly in in-app browsers, so you can hide it or show the locker with a warning offering to open a page in a standard browser.', 'bizpanda' )
        );
        
        $forms[] = array(
            'type'      => 'div',
            'id'        => 'in_app_browsers_warning',
            'items'     => array(
                array(
                    'type'      => 'textarea',
                    'name'      => 'in_app_browsers_warning',
                    'title'     => __( 'In-App Warning', 'bizpanda' ),
                    'default'     => __( 'You are viewing this page in the {browser}. The locker may work incorrectly in this browser. Please open this page in a standard browser.', 'bizpanda' ),
                    'hint'      => __( 'A warning message visible together with the locker when a user opens your page in an in-app browser.', 'bizpanda' )
                )
            )
        ); 
        
        if ( BizPanda::hasPlugin('sociallocker') ) {
            
            $forms[] = array(
                'type' => 'separator'
            );
        
            $forms[] = array(
                'type'      => 'dropdown',
                'data'      => array(
                    array( 'show_error', __('Show Locker With Error', 'bizpanda') ),
                    array( 'show_content', __('Hide Locker, Show Content', 'bizpanda') )
                ),
                'name'      => 'adblock',
                'default'   => 'show_error',
                'title'     => __( 'AdBlock', 'bizpanda' ),
                'hint'      => __( 'Optional. Setup how the locker should behave if AdBlock blocks social widgets.', 'bizpanda' )
            );

            $forms[] = array(
                'type'      => 'div',
                'id'        => 'adblock_error',
                'items'     => array(
                    array(
                        'type'      => 'textarea',
                        'name'      => 'adblock_error',
                        'title'     => __( 'AdBlock Error', 'bizpanda' ),
                        'default'     => __( 'Unable to create social buttons. Please make sure that nothing blocks loading of social scripts in your browser. Some browser extentions (Avast, PrivDog, AdBlock, Adguard etc.) or usage of private tabs in FireFox may cause this issue. Turn them off and try again.', 'bizpanda' ),
                        'hint'      => __( 'An error displaying when AdBlock extensions block loading of social buttons.', 'bizpanda' )
                    )
                )
            ); 

        }
        
        $forms[] = array(
            'type' => 'separator'
        );

        $forms[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'rss',
            'title'     => __( 'Locked content<br /> is visible in RSS feeds', 'bizpanda' ),
            'hint'      => __( 'Set On to make locked content visible in RSS feed.', 'bizpanda' ),
            'default'   => false
        );
        
        if ( BizPanda::hasPlugin('sociallocker') ) {
        
            $forms[] = array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'actual_urls',
                'title'     => __( 'Actual URLs by default', 'bizpanda' ),
                'hint'      => __( 'Optional. If you do not set explicitly URLs to like/share in the settings of social buttons, then by default the plugin will use an URL of the page where the locker is located. Turn on this option to extract URLs to like/share from an address bar of the user browser, saving all query arguments. By default permalinks are used.', 'bizpanda' ),
                'default'   => false
            );
        }
        
        $forms[] = array(
            'type' => 'separator'
        );
         
        $forms[] = array(
            'type' => 'html',
            'html' =>   '<div class="col-md-offset-2" style="padding: 30px 0 10px 0;">' . 
                            '<strong style="font-size: 15px;">' . __('Advanced Options', 'bizpanda') . '</strong>' .
                            '<p>' . __('Please don\'t change these options if everything works properly.', 'bizpanda') . '</p>' .
                        '</div>'
        ); 
        
        $forms[] = array(
            'type' => 'separator'
        );
        
        $forms[] = array(
            'type'      => 'textbox',
            'name'      => 'session_duration',
            'title'     => __( 'Session Duration<br />(in secs)', 'bizpanda' ),
            'hint'      => __( 'Optional. The session duration used in the advanced Visiblity Options. The default value 900 seconds (15 minutes).', 'bizpanda' ),
            'default'   => 900
        );
        
        $forms[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'session_freezing',
            'title'     => __( 'Session Freezing', 'bizpanda' ),
            'hint'      => __( 'Optional. If On, the length of users\' sessions is fixed, by default the sessions are prolonged automatically every time when a user visits your website for a specified value of the session duration.', 'bizpanda' ),
            'default'   => false
        );  
        
        $forms[] = array(
            'type' => 'separator'
        );
        
        $forms[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'normalize_markup',
            'title'     => __( 'Normalize Markup', 'bizpanda' ),
            'hint'      => __( 'Optional. If you use the Batch Lock and the locker appears incorrectly, probably HTML markup of your page is broken. Try to turn on this option and the plugin will try to normalize html markup before output.', 'bizpanda' )
        );
        
        $forms[] = array(
            'type' => 'separator'
        );
        
        $forms[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'dynamic_theme',
            'title'     => __( 'I use a dynamic theme', 'bizpanda' ),
            'hint'      => __( 'If your theme loads pages dynamically via ajax, set "On" to get the lockers working (if everything works properly, don\'t turn on this option).', 'bizpanda' )
        );

        $forms[] = array(
            'type'      => 'textbox',
            'way'       => 'buttons',
            'name'      => 'managed_hook',
            'title'     => __( 'Creater Trigger', 'bizpanda' ),
            'hint'      => __( 'Optional. Set any jQuery trigger bound to the root document to create lockers. By default lockers are created on loading a page.', 'bizpanda' )
        );    

        $forms[] = array(
            'type' => 'separator'
        );
        
        $forms[] = array(
            'type'      => 'dropdown',
            'name'      => 'alt_overlap_mode',
            'data'      => array(
                array( 'full', __('Classic (full)', 'bizpanda') ),
                array( 'transparence', __('Transparency', 'bizpanda') )   
            ),
            'default'   => 'transparence',
            'title'     => __( 'Alt Overlap Mode', 'bizpanda' ),
            'hint'      => __( 'This overlap mode will be applied for browsers which don\'t support the blurring effect.', 'bizpanda' )
        );
        
        $forms[] = array(
            'type'      => 'dropdown',
            'data'      => array(
                array( 'auto', __('Auto', 'bizpanda') ),
                array( 'always_hidden', __('Hidden On Loading', 'bizpanda') ),
                array( 'always_visible', __('Visible On Loading', 'bizpanda') )          
            ),
            'name'      => 'content_visibility',
            'default'   => 'auto',
            'title'     => __( 'Content Visibility<br />On Loading', 'bizpanda' ),
            'hint'      => __( 'By default if the blurring or transparent mode is used, the content may be visible during a short time before the locker appears. On other side, if the classic mode is used, the locked content is hidden by default on loading. Change this option to manage content visibility when a page loads.', 'bizpanda' )
        );
        
        if ( BizPanda::hasPlugin('sociallocker') ) {

            $forms[] = array(
                'type' => 'separator'
            );
        
            $forms[] = array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'tumbler',
                'title'     => __( 'Anti-Cheating', 'bizpanda' ),
                'default'   => false,
                'hint'      => __( 'Turn it on to protect your locked content against cheating from visitors. Some special browser extensions allow to view the locked content without actual sharing. This option checks whether the user has really liked/shared your page.', 'bizpanda' )
            );

            $forms[] = array(
                'type'      => 'textbox',
                'name'      => 'timeout',
                'title'     => __( 'Timeout of waiting<br />loading the locker (in ms)', 'bizpanda' ),
                'default'   => '20000',
                'hint'      => __( 'A user can have browser extensions which block loading scripts of social networks. If the social buttons have not been loaded within the specified timeout interval, the locker shows the error (in the red container) alerting about that a browser blocks loading of the social buttons.<br />', 'bizpanda' )
            );
        }
        
        $forms[] = array(
            'type' => 'separator'
        );
        
        return $forms;
    }
    
    /**
     * Returns a list of default temporary email domains.
     */
    public static function getTempEmailDomains() {
        
        return array(
            'sharklasers',
            'grr',
            'guerrillamail',
            'guerrillamailblock',
            'pokemail',
            'spam4',
            'yk20.com',
            '0hiolce.com',
            'etoic.com',
            'jklasdf.com',
            'u.0u.ro',
            'uacro.com',
            'rblx.site',
            'malove.site',
            'harvard-ac-uk.tk',
            'xing886',
            'xww.ro',
            'barryogorman.com',
            'kozow.com',
            'dmarc.ro',
            'freemail.tweakly.net',
            'ppetw.com',
            'uu.gl',
            'usa.cc',
            '0v.ro',
            'mailfs.com',
            'apssdc.ml',
            '0w.ro',
            'laoho.com',
            'wupics.com',
            'xww.ro',
            'getnada.com',
            'amail.club',
            'banit',
            'cars2.club',
            'cmail.club',
            'duck2.club',
            'nada.email',
            'nada.ltd',
            'wmail.club'
        );
    }
}

