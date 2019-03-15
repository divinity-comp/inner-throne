<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YCE_Upgrade_Manager {

	/**
	 * Check if there's a plugin update
	 */
	public function check_update() {

		// Get plugin options
		$options = Yoast_Convert_Experiments::get_options();

		// Get current version
		$current_version = $options['version_code'];

		// Check if update is required
		if ( Yoast_Convert_Experiments::PLUGIN_VERSION_CODE > $current_version ) {

			// Do update
			$this->do_update( $current_version );

			// Update version code
			$this->update_current_version_code();

		}

	}

	/**
	 * An update is required, do it
	 *
	 * @param int $current_version
	 */
	private function do_update( $current_version ) {

		if ( $current_version < 1 ) {

			/**
			 * Upgrade to version 2.0.0
			 *
			 * - Get old project number and save it in the new location
			 */

			// Get old project number
			$old_project_number = get_option( 'convert_experiments_project_number', '' );

			// Check if there is an old project number
			if ( '' != $old_project_number ) {

				// Save the old project number in the new project number option
				Yoast_Convert_Experiments::save_option( 'project_id', $old_project_number );
			}

			// Remove the old option
			delete_option( 'convert_experiments_project_number' );

		}

	}

	/**
	 * Update the current version code
	 */
	private function update_current_version_code() {
		Yoast_Convert_Experiments::save_option( 'version_code', Yoast_Convert_Experiments::PLUGIN_VERSION_CODE );
	}
}