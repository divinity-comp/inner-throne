<?php
/**
 * Plugin Name: Easy Digital Downloads - Zapier
 * Plugin URI: http://easydigitaldownloads.com/extensions/
 * Description: Adds Zapier integration for your EDD Shop. Trigger actions based on new customers and orders. Send sample data to Zapier via Downloads > Settings > Extensions.
 * Version: 1.3.7
 * Author: Easy Digital Downloads, LLC
 * Author URI: https://easydigitaldownloads.com
 * License: GPL2
 * Text Domain: edd-zapier
 * Domain Path: /languages
 */

/*
Copyright 2013 rzen Media, LLC (email : brian@rzen.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main plugin instantiation class.
 *
 * @since 1.0.0
 */
class EDD_Zapier_Integration {

	/**
	 * Fire up the engines.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Define plugin constants
		$this->basename       = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url  = plugin_dir_url( __FILE__ );

		// Basic setup
		add_action( 'admin_notices',  array( $this, 'maybe_disable_plugin' ) );
		add_action( 'plugins_loaded', array( $this, 'licensed_updates' ), 9 );
		add_action( 'plugins_loaded', array( $this, 'i18n' ) );
		add_action( 'init',           array( $this, 'register_cpt' ) );
		add_action( 'plugins_loaded', array( $this, 'includes' ) );



	} /* __construct() */

	/**
	 * Check if all requirements are met.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if requirements are met, otherwise false.
	 */
	private function meets_requirements() {
		if ( function_exists( 'EDD' ) && defined( 'EDD_VERSION' ) && version_compare( EDD_VERSION, '2.1.0', '>=' ) ) {
			return true;
		} else {
			return false;
		}
	} /* meets_requirements() */

	/**
	 * Output error message and disable plugin if requirements are not met.
	 *
	 * This fires on admin_notices.
	 *
	 * @since 1.0.0
	 */
	public function maybe_disable_plugin() {
		if ( ! $this->meets_requirements() ) {
			// Display our error
			echo '<div id="message" class="error">';
			echo '<p>' . sprintf( __( 'Easy Digital Downloads - Zapier requires Easy Digital Downloads 2.1.0 or greater and has been <a href="%s">deactivated</a>.', 'edd-zapier' ), admin_url( 'plugins.php' ) ) . '</p>';
			echo '</div>';

			// Deactivate our plugin
			deactivate_plugins( $this->basename );
		}
	} /* maybe_disable_plugin() */

	/**
	 * Register EDD License
	 *
	 * @since 1.0.0
	 */
	public function licensed_updates() {
		if ( class_exists( 'EDD_License' ) ) {
			$license = new EDD_License( __FILE__, 'Zapier', '1.3.7', 'EDD Team', null, null, 319476 );
		}
	} /* licensed_updates() */

	/**
	 * Load localization.
	 *
	 * @since 1.0.0
	 */
	public function i18n() {
		load_plugin_textdomain( 'edd-zapier', false, $this->directory_path . '/languages/' );
	} /* i18n() */

	/**
	 * Register EDD Zapier Subscription post type.
	 *
	 * @since  1.0.0
	 */
	function register_cpt() {
		register_post_type( 'edd-zapier-sub', array(
			'description' => __( 'Used for storing EDD Zapier subscriptions', 'edd-zapier'),
			'public'      => false,
			'rewrite'     => false,
			'query_var'   => false,
		) );
	} /* register_cpt() */

	/**
	 * Include file dependencies.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		if ( $this->meets_requirements() ) {
			require_once( $this->directory_path . 'includes/class.EDD_Zapier_Customer.php' );
			require_once( $this->directory_path . 'includes/class.EDD_Zapier_Subscription_Factory.php' );
			require_once( $this->directory_path . 'includes/class.EDD_Zapier_Connection.php' );
			require_once( $this->directory_path . 'includes/subscription_hooks.php' );
			require_once( $this->directory_path . 'includes/settings.php' );
		}
	} /* includes() */

}
$GLOBALS['edd_zapier_integration'] = new EDD_Zapier_Integration;
