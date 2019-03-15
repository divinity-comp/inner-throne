<?php
/**
 * Plugin Name:         Easy Digital Downloads - Checkout Fields Manager
 * Plugin URI:          https://easydigitaldownloads.com/extension/checkout-fields-manager/
 * Description:         Easily add and control EDD's checkout fields
 * Author:              Chris Christoff
 * Author URI:          http://www.chriscct7.com
 *
 * Version:             1.2.1
 * Requires at least:   3.8
 * Tested up to:        4.3
 *
 * Text Domain:         edd_cfm
 * Domain Path:         /edd_cfm/languages/
 *
 * @category            Plugin
 * @copyright           Copyright © 2015 Chris Christoff
 * @author              Chris Christoff
 * @package             CFM
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/** Check if Easy Digital Downloads is active */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class EDD_Checkout_Fields_Manager {
	/**
	 * @var EDD_Checkout_Fields_Manager The one true EDD_Checkout_Fields_Manager
	 * @since 1.4
	 */
	private static $instance;
	public $id = 'edd_cfm';
	public $basename;

	// Setup objects for each class
	public $render_form;
	public $admin_form;
	public $admin_posting;
	public $install;
	public $menu;
	public $upload;
	public $export;
	public $emails;
	/**
	 * Main EDD_Checkout_Fields_Manager Instance
	 *
	 * Insures that only one instance of EDD_Checkout_Fields_Manager exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.4
	 * @static
	 * @staticvar array $instance
	 * @uses EDD_Checkout_Fields_Manager::setup_globals() Setup the globals needed
	 * @uses EDD_Checkout_Fields_Manager::includes() Include the required files
	 * @uses EDD_Checkout_Fields_Manager::setup_actions() Setup the hooks and actions
	 * @see EDD()
	 * @return The one true EDD_Checkout_Fields_Manager
	 */
	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof EDD_Checkout_Fields_Manager ) ) {
			self::$instance = new EDD_Checkout_Fields_Manager;
			self::$instance->define_globals();
			self::$instance->load_textdomain();
			self::$instance->includes();
			self::$instance->setup();
			// Setup class instances
			self::$instance->render_form           = new CFM_Render_Form;
			self::$instance->setup                 = new CFM_Setup;
			self::$instance->upload                = new CFM_Upload;
			self::$instance->frontend_form_post    = new CFM_Frontend_Form;
			self::$instance->menu                  = new CFM_Menu;
			self::$instance->export                = new CFM_Export;
			self::$instance->emails                = new CFM_Emails;
			if ( is_admin() ) {
				self::$instance->admin_form        = new CFM_Admin_Form;
				self::$instance->admin_posting     = new CFM_Admin_Posting;
			}
		}
		return self::$instance;
	}

	public function define_globals() {
		$this->title    = __( 'Checkout Fields Manager', 'edd_cfm' );
		$this->file     = __FILE__;
		$this->basename = apply_filters( 'edd_cfm_plugin_basename', plugin_basename( $this->file ) );
		// Plugin Name
		if ( !defined( 'cfm_plugin_name' ) ) {
			define( 'cfm_plugin_name', 'Checkout Fields Manager' );
		}
		// Plugin Version
		if ( !defined( 'cfm_plugin_version' ) ) {
			define( 'cfm_plugin_version', '1.2.1' );
		}
		// Plugin Root File
		if ( !defined( 'cfm_plugin_file' ) ) {
			define( 'cfm_plugin_file', __FILE__ );
		}
		// Plugin Folder Path
		if ( !defined( 'cfm_plugin_dir' ) ) {
			define( 'cfm_plugin_dir', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' );
		}
		// Plugin Folder URL
		if ( !defined( 'cfm_plugin_url' ) ) {
			define( 'cfm_plugin_url', plugin_dir_url( cfm_plugin_file ) );
		}
		// Plugin Assets URL
		if ( !defined( 'cfm_assets_url' ) ) {
			define( 'cfm_assets_url', cfm_plugin_url . 'assets/' );
		}
		if ( !class_exists( 'EDD_License' ) ) {
			require_once cfm_plugin_dir . 'assets/lib/EDD_License_Handler.php';
		}
		$license = new EDD_License( __FILE__, cfm_plugin_name, cfm_plugin_version, 'Chris Christoff' );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'edd_cfm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function includes() {
		require_once cfm_plugin_dir . 'classes/class-setup.php';
		require_once cfm_plugin_dir . 'classes/class-menu.php';
		require_once cfm_plugin_dir . 'classes/forms/render-form.php';
		require_once cfm_plugin_dir . 'classes/forms/frontend-form.php';
		require_once cfm_plugin_dir . 'classes/forms/upload.php';
		require_once cfm_plugin_dir . 'classes/forms/functions.php';
		require_once cfm_plugin_dir . 'classes/class-emails.php';
		require_once cfm_plugin_dir . 'classes/class-export.php';
		if ( is_admin() ) {
			require_once cfm_plugin_dir . 'classes/forms/admin-form.php';
			require_once cfm_plugin_dir . 'classes/forms/admin-posting.php';
			require_once cfm_plugin_dir . 'classes/forms/admin-template.php';
		}
	}

	public static function install() {
		require_once cfm_plugin_dir . 'classes/class-install.php';
		$install = new CFM_Install;
		$install->init();
	}

	public function setup() {
		$this->setup = new CFM_Setup;
		do_action( 'edd_cfm_setup_actions' );
	}
}

/**
 * The main function responsible for returning the one true EDD_Checkout_Fields_Manager
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $edd_cfm = EDD_CFM(); ?>
 *
 * @since 2.0
 * @return object The one true EDD_Checkout_Fields_Manager Instance
 */
function EDD_CFM() {
	return EDD_Checkout_Fields_Manager::instance();
}

EDD_CFM();

function EDD_CFM_Install() {
	EDD_CFM()->install();
}

register_activation_hook( __FILE__, 'EDD_CFM_Install' );