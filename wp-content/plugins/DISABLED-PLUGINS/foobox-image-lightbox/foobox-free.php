<?php

/*
Plugin Name: FooBox Image Lightbox
Plugin URI: http://fooplugins.com/plugins/foobox/
Description: The best responsive lightbox for WordPress.
Version: 1.2.22
Author: FooPlugins
Author URI: http://fooplugins.com
License: GPL2
Text Domain: foobox-image-lightbox
Domain Path: /languages
@fs_premium_only /pro/
*/

if ( !class_exists( 'FooBox' ) ) {
    define( 'FOOBOX_BASE_FILE', __FILE__ );
    define( 'FOOBOX_BASE_SLUG', 'foobox-image-lightbox' );
    define( 'FOOBOX_BASE_PAGE_SLUG_SETTINGS', 'foobox-settings' );
    define( 'FOOBOX_BASE_ACTIVATION_REDIRECT_TRANSIENT_KEY', '_foobox_activation_redirect' );
    define( 'FOOBOX_BASE_PATH', plugin_dir_path( __FILE__ ) );
    define( 'FOOBOX_BASE_URL', plugin_dir_url( __FILE__ ) );
    define( 'FOOBOX_BASE_VERSION', '1.2.22' );
    // Create a helper function for easy SDK access.
    function foobox_fs()
    {
        global  $foobox_fs ;
        
        if ( !isset( $foobox_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $foobox_fs = fs_dynamic_init( array(
                'id'             => '374',
                'slug'           => 'foobox-image-lightbox',
                'type'           => 'plugin',
                'public_key'     => 'pk_7a17ec700c89fe71a25605589e0b9',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'       => 'foobox-image-lightbox',
                'first-path' => 'admin.php?page=foobox-image-lightbox',
                'contact'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $foobox_fs;
    }
    
    // Init Freemius.
    foobox_fs();
    class FooBox
    {
        private static  $instance ;
        public static function get_instance()
        {
            if ( !isset( self::$instance ) && !self::$instance instanceof FooBox ) {
                self::$instance = new FooBox();
            }
            return self::$instance;
        }
        
        /**
         * Initialize the plugin!!!
         */
        private function __construct()
        {
            //include all the things!
            $this->includes();
            
            if ( is_admin() ) {
                new FooBox_Admin_Menu();
                add_action( 'admin_init', array( $this, 'check_for_activation_redirect' ) );
                add_action( FOOBOX_ACTION_ADMIN_MENU_RENDER_GETTING_STARTED, array( $this, 'render_page_getting_started' ) );
                foobox_fs()->add_filter( 'support_forum_submenu', array( $this, 'override_support_menu_text' ) );
                foobox_fs()->add_filter( 'support_forum_url', array( $this, 'override_support_forum_url' ) );
                add_action( 'admin_menu', array( $this, 'remove_admin_menu_items_on_mobile' ), WP_FS__LOWEST_PRIORITY + 1 );
                foobox_fs()->add_action( 'after_premium_version_activation', array( 'FooBox', 'activate' ) );
                foobox_fs()->add_filter(
                    'connect_message',
                    array( $this, 'custom_connect_message' ),
                    10,
                    6
                );
                foobox_fs()->add_filter(
                    'connect_message_on_update',
                    array( $this, 'custom_connect_message' ),
                    10,
                    6
                );
            }
            
            //register activation hook for free
            register_activation_hook( __FILE__, array( 'FooBox', 'activate' ) );
            require_once FOOBOX_BASE_PATH . 'free/foobox-free.php';
        }
        
        public function custom_connect_message(
            $message,
            $user_first_name,
            $plugin_title,
            $user_login,
            $site_link,
            $freemius_link
        )
        {
            return sprintf(
                __fs( 'hey-x' ) . '<br>' . __( 'Never miss an important update: opt-in to our security and feature email updates, as well as non-sensitive diagnostic tracking with the Freemius.com service.', 'foobox-image-lightbox' ) . '<br>' . __( 'If you skip this, that\'s okay! %2$s will still work just fine.', 'foobox-image-lightbox' ),
                $user_first_name,
                '<b>' . $plugin_title . '</b>',
                '<b>' . $user_login . '</b>',
                $site_link,
                $freemius_link
            );
        }
        
        public function override_support_menu_text( $text )
        {
            return __( 'Support', 'foobox-image-lightbox' );
        }
        
        public function override_support_forum_url( $url )
        {
            return $url;
        }
        
        /**
         * Include all the files needed
         */
        public function includes()
        {
            require_once FOOBOX_BASE_PATH . 'includes/functions.php';
            require_once FOOBOX_BASE_PATH . 'includes/actions.php';
            require_once FOOBOX_BASE_PATH . 'includes/filters.php';
            require_once FOOBOX_BASE_PATH . 'includes/admin/menu.php';
        }
        
        /**
         * Fired when the plugin is activated.
         *
         * @since    1.1.0
         *
         * @param    boolean    $network_wide    True if WPMU superadmin uses
         *                                       "Network Activate" action, false if
         *                                       WPMU is disabled or plugin is
         *                                       activated on an individual blog.
         */
        public static function activate( $network_wide )
        {
            
            if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            } else {
                //Make sure we redirect to the welcome page
                set_transient( FOOBOX_BASE_ACTIVATION_REDIRECT_TRANSIENT_KEY, true, 30 );
            }
        
        }
        
        /**
         * On admin_init check that the plugin was activated and redirect to the getting started page
         */
        public function check_for_activation_redirect()
        {
            // Bail if no activation redirect
            if ( !get_transient( FOOBOX_BASE_ACTIVATION_REDIRECT_TRANSIENT_KEY ) ) {
                return;
            }
            // Delete the redirect transient
            delete_transient( FOOBOX_BASE_ACTIVATION_REDIRECT_TRANSIENT_KEY );
            // Bail if activating from network, or bulk
            if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
                return;
            }
            $url = admin_url( 'admin.php?page=' . FOOBOX_BASE_SLUG );
            wp_safe_redirect( $url );
            die;
        }
        
        public function render_page_getting_started()
        {
            require_once FOOBOX_BASE_PATH . 'includes/admin/view-getting-started.php';
        }
        
        public function remove_admin_menu_items_on_mobile()
        {
            
            if ( foobox_hide_pricing_menu() ) {
                //we only want to hide menu items if we are on mobile!
                remove_submenu_page( 'foobox-image-lightbox', 'foobox-image-lightbox-pricing' );
                remove_submenu_page( 'foobox-image-lightbox', 'foobox-image-lightbox-account' );
                remove_submenu_page( 'foobox-image-lightbox', 'foobox-image-lightbox-contact' );
            }
        
        }
    
    }
} else {
}

FooBox::get_instance();