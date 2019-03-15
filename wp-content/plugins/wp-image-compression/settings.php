<?php
global $_wpimages_multisite_settings;

$_wpimages_multisite_settings = null;	// Settings cache.

// activation hooks
// TODO: custom table is not removed because de-activating one site shouldn't affect the entire server
register_activation_hook('wp-image-compression/wp-image-compression.php', 'wpimages_maybe_created_custom_table' );

// add_action('plugins_loaded', 'wpimages_maybe_created_custom_table');
// register_deactivation_hook('wp-image-compression/wp-image-compression.php', ...);
// register_uninstall_hook('wp-image-compression/wp-image-compression.php', 'wpimages_maybe_remove_custom_table');

/**
 * Returns the name of the custom multi-site settings table.
 * this will be the same table regardless of the blog
 */
function wpimages_get_custom_table_name() {
	global $wpdb;
	// passing in zero seems to return $wpdb->base_prefix, which is not public
	return $wpdb->get_blog_prefix(0) . "wpimage";
}

/**
 * Return true if the multi-site settings table exists
 * @return bool
 */
function wpimages_multisite_table_exists() {
	global $wpdb;
	$table_name = wpimages_get_custom_table_name();
	return $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name;
}

/**
 * Return true if the multi-site settings table exists
 * @return bool
 */
function wpimages_multisite_table_schema_version() {
	// If the table doesn't exist then there is no schema to report.
	if ( ! wpimages_multisite_table_exists() ){ return '0'; }
	global $wpdb;
	$version = $wpdb->get_var('SELECT data FROM ' . wpimages_get_custom_table_name() . " WHERE setting = 'schema'");
	$version = ! $version ? '1.0' : $version; // this is a legacy version 1.0 installation
	return $version;
}

/**
 * Returns the default network settings in the case where they are not
 * defined in the database, or multi-site is not enabled
 * @return stdClass
 */
function wpimages_get_default_multisite_settings() {
	$data = new stdClass();
	$data->wpimages_override_site = false;
	$data->wpimages_max_height = WPIMAGE_DEFAULT_MAX_HEIGHT;
	$data->wpimages_max_width = WPIMAGE_DEFAULT_MAX_WIDTH;
	$data->wpimages_max_height_library = WPIMAGE_DEFAULT_MAX_HEIGHT;
	$data->wpimages_max_width_library = WPIMAGE_DEFAULT_MAX_WIDTH;
	$data->wpimages_max_height_other = WPIMAGE_DEFAULT_MAX_HEIGHT;
	$data->wpimages_max_width_other = WPIMAGE_DEFAULT_MAX_WIDTH;
	$data->wpimages_bmp_to_jpg = WPIMAGE_DEFAULT_BMP_TO_JPG;
	$data->wpimages_png_to_jpg = WPIMAGE_DEFAULT_PNG_TO_JPG;
	// $data->wpimages_use_our_image_cdn = WPIMAGE_DEFAULT_USE_OUR_IMAGE_CDN;

	$data->wpimages_quality = WPIMAGE_DEFAULT_QUALITY;
	return $data;
}

/**
 * On activation create the multisite database table if necessary.  this is
 * called when the plugin is activated as well as when it is automatically
 * updated.
 *
 * @param bool set to true to force the query to run in the case of an upgrade
 */
function wpimages_maybe_created_custom_table() {

	// if not a multi-site no need to do any custom table lookups
	if( ( ! function_exists( "is_multisite" ) ) || ! is_multisite() ){
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	global $wpdb;

	$schema = wpimages_multisite_table_schema_version();
	$table_name = wpimages_get_custom_table_name();

	if ( 0 === (int) $schema ) {

		// Initial database setup.
		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " ( setting varchar(55), data text NOT NULL, PRIMARY KEY ( setting ) );";
		
		dbDelta( $sql );
		$data = wpimages_get_default_multisite_settings();

		// add the rows to the database
		$data = wpimages_get_default_multisite_settings();
		$wpdb->insert( $table_name, array( 'setting' => 'multisite', 'data' => maybe_serialize($data) ) );
		$wpdb->insert( $table_name, array( 'setting' => 'schema', 'data' => WPIMAGE_SCHEMA_VERSION ) );
	}

	if ( $schema !== WPIMAGE_SCHEMA_VERSION ) {

		// Schema update. For the moment there is only one schema update available, from 1.0 to 1.1
		if ( "1.0" === $schema ) {
			$wpdb->insert( $table_name, array( 'setting' => 'schema', 'data' => WPIMAGE_SCHEMA_VERSION ) );
			$wpdb->query( "ALTER TABLE " . $table_name . " CHANGE COLUMN data data TEXT NOT NULL;" );
		}
		else {
			// @todo We don't have this yet.
			$wpdb->update( $table_name, array('data' =>  WPIMAGE_SCHEMA_VERSION), array('setting' => 'schema') );
		}
	}
}

/**
 * Return the multi-site settings as a standard class.  If the settings are not
 * defined in the database or multi-site is not enabled then the default settings
 * are returned.  This is cached so it only loads once per page load, unless
 * wpimages_network_settings_update is called.
 * @return stdClass
 */
function wpimages_get_multisite_settings() {

	global $_wpimages_multisite_settings;
	
	if ( ! $_wpimages_multisite_settings ) {

		$result = null;

		if ( function_exists( "is_multisite" ) && is_multisite() ) {
			global $wpdb;
			$result = $wpdb->get_var( 'SELECT data FROM ' . wpimages_get_custom_table_name() . " WHERE setting = 'multisite'");
		}

		// if there's no results, return the defaults instead
		$_wpimages_multisite_settings = $result ? unserialize( $result ) : wpimages_get_default_multisite_settings();

		// this is for backwards compatibility
		if ( "" === $_wpimages_multisite_settings->wpimages_max_height_library ) {
			$_wpimages_multisite_settings->wpimages_max_height_library = $_wpimages_multisite_settings->wpimages_max_height;
			$_wpimages_multisite_settings->wpimages_max_width_library = $_wpimages_multisite_settings->wpimages_max_width;
			$_wpimages_multisite_settings->wpimages_max_height_other = $_wpimages_multisite_settings->wpimages_max_height;
			$_wpimages_multisite_settings->wpimages_max_width_other = $_wpimages_multisite_settings->wpimages_max_width;
		}
	}
	return $_wpimages_multisite_settings;
}

/**
 * Gets the option setting for the given key, first checking to see if it has been
 * set globally for multi-site.  Otherwise checking the site options.
 * @param string $key
 * @param string $ifnull value to use if the requested option returns null
 */
function wpimages_get_option( $key, $ifnull ) {
	$settings = wpimages_get_multisite_settings();
	$result = $settings->wpimages_override_site ? ( null === $result ? $ifnull : $settings->$key ) :  get_option( $key, $ifnull );
	return $result;
}
