<?php
/*
Plugin Name: bbPress Toolkit
Description: Manage global options not available inside bbPress and add or fix styling issues with this Toolkit.
Plugin URI: https://wordpress.org/plugins/bbp-toolkit/
Author: Pascal Casier
Author URI: http://casier.eu/wp-dev/
Text Domain: bbp-toolkit
Version: 1.0.12
License: GPL2
*/

// No direct access
if ( !defined( 'ABSPATH' ) ) exit;

define ('BBPTOOLKIT_VERSION' , '1.0.12');

if(!defined('BBPT_PLUGIN_NAME'))
	define('BBPT_PLUGIN_NAME', plugin_basename( __FILE__ ));
if(!defined('BBPT_PLUGIN_DIR'))
	define('BBPT_PLUGIN_DIR', dirname(__FILE__));
if(!defined('BBPT_URL_PATH'))
	define('BBPT_URL_PATH', plugin_dir_url(__FILE__));

include(BBPT_PLUGIN_DIR . '/includes/go-functions.php');
include(BBPT_PLUGIN_DIR . '/includes/inf-functions.php');
include(BBPT_PLUGIN_DIR . '/includes/closef-functions.php');
include(BBPT_PLUGIN_DIR . '/includes/extra-functions.php');
include(BBPT_PLUGIN_DIR . '/includes/mention.php');
include(BBPT_PLUGIN_DIR . '/includes/search.php');
include(BBPT_PLUGIN_DIR . '/includes/defaults.php');

if (!is_admin()) {
	//echo 'Cheating ? You need to be admin to view this !';
	return;
} // is_admin

// Check if bbpress is installed and running
// Check if get_plugins() function exists. This is required on the front end of the
// site, since it is in a file that is normally only loaded in the admin.
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
// Check if bbPress is active
	$plugin = 'bbpress/bbpress.php';
	$network_active = false;
	if ( is_multisite() ) {
		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[$plugin] ) ) {
			$network_active = true;
		}
	}
	if (in_array( $plugin, get_option( 'active_plugins' ) ) || $network_active) {
		// all ok, bbPress is active
	} else {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die( __('Sorry, you need to activate bbPress first.', 'bbp-toolkit'));
	}

include(BBPT_PLUGIN_DIR . '/includes/sysinfo-basic.php');
include(BBPT_PLUGIN_DIR . '/includes/gen-css.php');
include(BBPT_PLUGIN_DIR . '/includes/plugin-mgmt.php');
include(BBPT_PLUGIN_DIR . '/includes/subscriptions.php');
include(BBPT_PLUGIN_DIR . '/includes/to_trunk.php');
include(BBPT_PLUGIN_DIR . '/includes/main_page.php');


function bbptoolkit_admin_header() {
	$bbptoolkit_css_version = get_option('bbptoolkit-css-version', false);
	wp_enqueue_script('bbptoolkitadminjs', BBPT_URL_PATH.'js/bbptoolkit-config.js', false, $bbptoolkit_css_version);
	wp_enqueue_style('bbptoolkitadmincss', BBPT_URL_PATH.'css/bbptoolkit-config.css', false, $bbptoolkit_css_version);
	bbptoolkit_activate();
}

function bbptoolkit_add_admin_menu() {
	$confHook = add_management_page('bbP Toolkit', 'bbP Toolkit', 'delete_forums', 'forums_toolkit', 'forums_toolkit_page');
	add_action("admin_head-$confHook", 'bbptoolkit_admin_header');

}
add_action('admin_menu', 'bbptoolkit_add_admin_menu');

function bbptoolkit_activate() {
	// Activate mentions as a default
	$bbptoolkit_mentions_forced = get_option('bbptoolkit-mentions-forced', false);
	if (!$bbptoolkit_mentions_forced) {
		update_option('bbptoolkit-mentions', 'activate');
		update_option('bbptoolkit-mentions-forced', 'forced', false);
	}
}
register_activation_hook( __FILE__, 'bbptoolkit_activate' );

?>