<?php
/*
Plugin Name: Convert Experiences
Version: 3.0.4
Plugin URI: http://convert.com/
Description: Convert Experiences provides A/B and Multivariate Testing for Experts. To get started: 1) Click the "Activate" link to the left of this description, 2) <a href="http://www.convert.com/pricing/">Sign up for a free trial</a>, and 3) Go to your <a href="options-general.php?page=convert-experiences">Convert Experiences configuration page</a>, and enter your project ID.
Author: Convert
Author URI: http://www.convert.com/
Text Domain: convert-experiments
Domain Path: /languages/

License: GPL v3

Convert Experiences
Copyright (C) 2008-2016

Copyright pre version 2.0.0: Copyright 2012  John Bekas Jr.  (email : john@convert.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Yoast_Convert_Experiments {

	const PLUGIN_FILE         = __FILE__;
	const PLUGIN_VERSION_CODE = '1';
	const PLUGIN_OPTIONS      = 'convert_experiments';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->includes();
		$this->setup();
	}

	/**
	 * Include files
	 */
	private function includes() {
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/classes/admin/class-admin.php';
			require_once dirname( __FILE__ ) . '/classes/admin/class-admin-page.php';
			require_once dirname( __FILE__ ) . '/classes/admin/class-upgrade-manager.php';
		} else {
			require_once dirname( __FILE__ ) . '/classes/frontend/class-convert-script.php';
		}
	}

	/**
	 * Setup plugin
	 */
	private function setup() {
		if ( is_admin() ) {

			// Load textdomain
			load_plugin_textdomain( 'convert-experiments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Plugin updater
			$plugin_updater = new YCE_Upgrade_Manager();
			$plugin_updater->check_update();

			// Setup Admin
			new YCE_Admin();

		} else {
			new YCE_Convert_Script();
		}
	}

	/**
	 * Get the plugin options
	 *
	 * @return array
	 */
	public static function get_options() {
		return apply_filters( 'convert_experiments_options', wp_parse_args( get_option( self::PLUGIN_OPTIONS, array() ), array( 'project_id' => '', 'version_code' => 0 ) ) );
	}

	/**
	 * Get the project ID
	 *
	 * @return string
	 */
	public static function get_project_ID() {
		$options = self::get_options();

		/**
		 * @api unsigned The project ID, to filter.
		 */
		return apply_filters( 'convert_experiments_project_id', $options['project_id'] );
	}

	/**
	 * Save an option
	 *
	 * @param string $key
	 * @param string $value
	 */
	public static function save_option( $key, $value ) {
		$options       = self::get_options();
		$options[$key] = $value;
		update_option( self::PLUGIN_OPTIONS, $options );
	}

}

// Create object - plugin init
add_action( 'plugins_loaded', create_function( '', 'new Yoast_Convert_Experiments();' ) );