<?php
/*
* Plugin Name: Analytify Pro
* Plugin URI: https://analytify.io/
* Description: Analytify makes Google Analytics simple for everything in WordPress (posts,pages etc). It presents the statistics in a beautiful way under the WordPress Posts/Pages at front end, backend and in its own Dashboard. This provides Stats from Country, Referrers, Social media, General stats, New visitors, Returning visitors, Exit pages, Browser wise and Top keywords. This plugin provides the RealTime statistics in a new UI that is easy to understand and looks good.
* Version: 2.0.21
* Author: WPBrigade
* Author URI: https://wpbrigade.com/
* License: GPLv2+
* Min WP Version: 3.0
* Max WP Version: 4.8
* Text Domain: wp-analytify-pro
* Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'ANALYTIFY_PRO_ROOT_PATH', dirname( __FILE__ ) );
define( 'ANALYTIFY_PRO_UPGRADE_PATH', __FILE__ );
define( 'ANALYTIFY_PRO_VERSION', '2.0.21' );

add_action( 'plugins_loaded', 'wp_analytify_pro_load', 15 );

function wp_analytify_pro_load() {

	if ( ! file_exists( WP_PLUGIN_DIR . '/wp-analytify/analytify-general.php' ) ) {
		add_action( 'admin_notices' , 'pa_install_free' );
		return;
	}

	if ( ! in_array( 'wp-analytify/wp-analytify.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		add_action( 'admin_notices', 'pa_activate_free_plugin' );
		return;
	}

	if ( ! class_exists( 'Analytify_General' ) ) {
		add_action( 'admin_notices' , 'pa_update_free' );
		return;
	}

	add_action( 'admin_menu', 'remove_go_pro_menu' );

	include ANALYTIFY_PRO_ROOT_PATH . '/classes/analytifypro_base.php';
	include ANALYTIFY_PRO_ROOT_PATH . '/classes/class-wp-analytify-pro.php';

	WP_Analytify_Pro::instance();

}

/**
 * Hide Go Pro submenu when Pro is activated.
 *
 * @since 2.0.5
 */
function remove_go_pro_menu (){

	remove_submenu_page( 'analytify-dashboard', 'analytify-go-pro' );
}

function pa_activate_free_plugin() {

	$action = 'activate';
	$slug   = 'wp-analytify/wp-analytify.php';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );

	printf('<div class="notice notice-error is-dismissible">
	<p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'The following required plugin is currently inactive &mdash; ', 'wp-analytify-pro' ), $link, esc_html__( 'Click here to activate Analytify Core (Free)', 'wp-analytify-pro' ) );

}

function pa_update_free() {

	$action = 'upgrade-plugin';
	$slug   = 'wp-analytify';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

	printf('<div class="notice notice-error is-dismissible">
	<p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'Please update Analytify Core to latest Free version to enable PRO features &mdash; ', 'wp-analytify-pro' ), $link, esc_html__( 'Update now' ), 'wp-analytify-pro' );

}

function pa_install_free() {

	$action = 'install-plugin';
	$slug   = 'wp-analytify';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

	printf('<div class="notice notice-warning">
	<p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'The following required plugin is not installed &mdash; ', 'wp-analytify-pro' ), $link, esc_html__( 'Install Analytify Core (Free) now', 'wp-analytify-pro' ) );

}


/**
*
* @since       1.2.2
* @return      void
*/
function wp_analytify_pro_activation() {

	// Check if front_end settings are set
	$_front_settings = get_option( 'wp-analytify-front' );
	if ( 'on' ===  $_front_settings['disable_front_end']  && ! empty( $_front_settings['show_analytics_roles_front_end'] ) ) {
		return;
	}

	// Load default settings in Pro plugin.
	if ( ! get_option( 'analytify_pro_default_settings' ) ) {

		$front_tab_settings = array(
			'disable_front_end'                   => 'on',
			'show_analytics_roles_front_end'      => array( 'administrator', 'editor' ),
			'show_analytics_post_types_front_end' => array( 'post', 'page' ),
			'show_panels_front_end'               => array( 'show-overall-front', 'show-country-front', 'show-keywords-front', 'show-social-front', 'show-browser-front', 'show-referrer-front', 'show-mobile-front', 'show-os-front', 'show-city-front' )
		);

		update_option( 'wp-analytify-front', $front_tab_settings );
		update_option( 'analytify_pro_default_settings', 'done' );
	}
}
register_activation_hook( __FILE__, 'wp_analytify_pro_activation' );

/**
*
* @since       1.2.2
 * @return      void
 */
function wp_analytify_pro_de_activation() {

}
register_deactivation_hook( __FILE__, 'wp_analytify_pro_de_activation' );

/**
 * Delete settings on uninstall.
 *
 * @since 2.0.4
 */
function wp_analytify_pro_un_install() {

	// delete default settings check. So on installing it again. Default settings could be loaded again.
	delete_option( 'analytify_pro_default_settings' );
}
register_uninstall_hook( __FILE__, 'wp_analytify_pro_un_install' );

/**
 * Load TextDoamin
 *
 * @since 2.0.7
 */
function wp_analytify_pro_load_text_domain(){
	$plugin_dir = basename( dirname( __FILE__ ) );
	load_plugin_textdomain( 'wp-analytify-pro', false , $plugin_dir . '/languages/' );
}
add_action( 'init', 'wp_analytify_pro_load_text_domain' );
