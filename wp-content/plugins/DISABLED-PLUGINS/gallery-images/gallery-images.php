<?php

/*
Plugin Name: Huge IT Image Gallery
Plugin URI: https://huge-it.com/wordpress-gallery/
Description: Gallery image is the best gallery plugin to use if you want to be original with your website. Responsive image gallery with many views.
Version: 2.1.4
Author: Huge-IT
Author URI: https://huge-it.com/
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

include_once( 'config.php' );

if ( ! class_exists( 'Gallery_Img' ) ) :

    final class Gallery_Img {

        /**
         * Version of plugin
         * @var float
         */
        public $version = '2.1.4';

        /**
         * Instance of Gallery_Img_Admin class to manage admin
         * @var Gallery_Img_Admin instance
         */
        public $admin = null;

        /**
         * Instance of Gallery_Img_Template_Loader class to manage admin
         * @var Gallery_Img_Template_Loader instance
         */
        public $template_loader = null;

        /**
         * The single instance of the class.
         *
         * @var Gallery_Img
         */
        protected static $_instance = null;

        /**
         * Main Gallery_Img Instance.
         *
         * Ensures only one instance of Gallery_Img is loaded or can be loaded.
         *
         * @static
         * @see Gallery_Img()
         * @return Gallery_Img - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gallery-images' ), '2.1' );
        }

        /**
         * Unserializing instances of this class is forbidden.
         */
        private function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'gallery-images' ), '2.1' );
        }

        /**
         * Gallery_Img Constructor.
         */
        private function __construct() {
            $this->define_constants();
            $this->includes();
            $this->init_hooks();
            global $Gallery_Img_url,$Gallery_Img_path;
            $Gallery_Img_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
            $Gallery_Img_url = plugins_url('', __FILE__ );
            do_action( 'Gallery_Img_loaded' );
        }

        /**
         * Hook into actions and filters.
         */
        private function init_hooks() {
            register_activation_hook( __FILE__, array( 'Gallery_Img_Install', 'install' ) );
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'plugins_loaded', array($this,'load_plugin_textdomain') );
            add_action( 'widgets_init', array( 'Gallery_Img_Widgets', 'init' ) );
        }

        /**
         * Define Image Gallery Constants.
         */
        private function define_constants() {
            $this->define( 'GALLERY_IMG_PLUGIN_URL', plugin_dir_path(__FILE__));
            $this->define( 'GALLERY_IMG_PLUGIN_FILE', __FILE__ );
            $this->define( 'GALLERY_IMG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'GALLERY_IMG_VERSION', $this->version );
            $this->define( 'GALLERY_IMG_IMAGES_PATH', $this->plugin_path(). DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR );
            $this->define( 'GALLERY_IMG_IMAGES_URL', untrailingslashit($this->plugin_url() . '/assets/images/' ));
            $this->define( 'GALLERY_IMG_TEMPLATES_PATH', $this->plugin_path() . DIRECTORY_SEPARATOR . 'templates');
            $this->define( 'GALLERY_IMG_TEMPLATES_URL', untrailingslashit($this->plugin_url()) . '/templates/');
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * What type of request is this?
         * string $type ajax, frontend or admin.
         *
         * @return bool
         */
        private function is_request( $type ) {
            switch ( $type ) {
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined( 'DOING_AJAX' );
                case 'cron' :
                    return defined( 'DOING_CRON' );
                case 'frontend' :
                    return  ! is_admin() && ! defined( 'DOING_CRON' );
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes() {
            include_once( 'includes/gallery-img-functions.php' );
            include_once( 'includes/gallery-img-video-function.php' );
            if ( $this->is_request( 'admin' ) ) {
                include_once( 'includes/admin/gallery-img-admin-functions.php' );
            }
            if ( $this->is_request( 'frontend' ) ) {
                $this->frontend_includes();
            }

        }

        /**
         * Include required core files used in front end
         */
        public function frontend_includes(){

        }

        /**
         * Load plugin text domain
         */
        public function load_plugin_textdomain(){
            load_plugin_textdomain( 'gallery-img', false, $this->plugin_path() . '/languages/' );
        }

        /**
         * Init Image gallery when WordPress `initialises.
         */
        public function init() {
            // Before init action.
            do_action( 'before_Gallery_Img_init' );

            Gallery_Img_Install::init();

            $this->template_loader = new Gallery_Img_Template_Loader();

            if ( $this->is_request( 'admin' ) ) {

                $this->admin = new Gallery_Img_Admin();

                new Gallery_Img_Admin_Assets();

            }

            new Gallery_Img_Frontend_Scripts();

            new Gallery_Img_Ajax();

            new Gallery_Img_Shortcode();

            // Init action.
            do_action( 'Gallery_Img_init' );
        }

        /**
         * Get Ajax URL.
         * @return string
         */
        public function ajax_url() {
            return admin_url( 'admin-ajax.php', 'relative' );
        }

        /**
         * Image Gallery Plugin Path.
         *
         * @var string
         * @return string
         */
        public function plugin_path(){
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

        /**
         * Image Gallery Plugin Url.
         * @return string
         */
        public function plugin_url(){
            return plugins_url('', __FILE__ );
        }
    }

endif;

function Gallery_Img(){
    return Gallery_Img::instance();
}

$GLOBALS['Gallery_Img'] = Gallery_Img();