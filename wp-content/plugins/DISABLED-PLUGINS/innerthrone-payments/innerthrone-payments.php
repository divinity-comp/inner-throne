<?php

/**
 * InnerThrone Payments
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://adhipg.in/
 * @since             1.0.0
 * @package           Innerthrone_Payments
 *
 * @wordpress-plugin
 * Plugin Name:       InnerThrone-Payments
 * Plugin URI:        http://adhipg.in/
 * Description:       Custom Plugin that enables Stripe Payments for Inner Throne
 * Version:           1.0.0
 * Author:            Adhip Gupta
 * Author URI:        http://adhipg.in/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       innerthrone-payments
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-innerthrone-payments-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-innerthrone-payments-activator.php';
	Innerthrone_Payments_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-innerthrone-payments-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-innerthrone-payments-deactivator.php';
	Innerthrone_Payments_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-innerthrone-payments.php';

/**
 * vendor files
 */
include plugin_dir_path( __FILE__ ) . 'vendor/stripe-php/init.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

	$plugin = new Innerthrone_Payments();
	$plugin->run();

}
run_plugin_name();
