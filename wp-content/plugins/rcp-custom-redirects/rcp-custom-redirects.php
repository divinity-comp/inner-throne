<?php
/**
 * Plugin Name:     Restrict Content Pro - Custom Redirects
 * Plugin URI:      https://restrictcontentpro.com/downloads/custom-redirects/
 * Description:     Add custom redirect support per subscription level
 * Version:         1.0.4
 * Author:          Restrict Content Pro team
 * Text Domain:     rcp-custom-redirects
 *
 * @package         RCP\Custom_Redirects
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2015, Daniel J Griffiths
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


if( ! class_exists( 'RCP_Custom_Redirects' ) ) {


	/**
	 * Main RCP_Custom_Redirects class
	 *
	 * @since       1.0.0
	 */
	class RCP_Custom_Redirects {


		/**
		 * @var         RCP_Custom_Redirects $instance The one true RCP_Custom_Redirects
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * @var         object $api_helper The MailChimp API helper object
		 * @since       1.0.0
		 */
		public $api_helper;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      object self::$instance The one true RCP_Custom_Redirects
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new RCP_Custom_Redirects();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		public function setup_constants() {
			// Plugin version
			define( 'RCP_CUSTOM_REDIRECTS_VER', '1.0.4' );

			// Plugin path
			define( 'RCP_CUSTOM_REDIRECTS_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'RCP_CUSTOM_REDIRECTS_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {
			require_once RCP_CUSTOM_REDIRECTS_DIR . 'includes/functions.php';
			require_once RCP_CUSTOM_REDIRECTS_DIR . 'includes/filters.php';
			require_once RCP_CUSTOM_REDIRECTS_DIR . 'includes/actions.php';

			if( is_admin() ) {
				require_once RCP_CUSTOM_REDIRECTS_DIR . 'includes/admin/subscription/fields.php';
			}
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {

			if( class_exists( 'RCP_Add_On_Updater' ) ) {
				$updater = new RCP_Add_On_Updater( 449, __FILE__, RCP_CUSTOM_REDIRECTS_VER );
			}

		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public static function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'rcp_custom_redirects_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'rcp-custom-redirects' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'rcp-custom-redirects', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/rcp-custom-redirects/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/rcp-custom-redirects/ folder
				load_textdomain( 'rcp-custom-redirects', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/rcp-custom-redirects/languages/ folder
				load_textdomain( 'rcp-custom-redirects', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'rcp-custom-redirects', false, $lang_dir );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true RCP_Custom_Redirects
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      RCP_Custom_Redirects The one true RCP_Custom_Redirects
 */
function rcp_custom_redirects() {
	return RCP_Custom_Redirects::instance();
}
add_action( 'plugins_loaded', 'rcp_custom_redirects' );
