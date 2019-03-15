<?php
function bbptoolkit_basic_sysinfo() {
	global $wpdb;
	$sysinfo = array();
	
	// bbP Toolkit version
	$newarray = array ( 'bbP Toolkit version' => bbptoolkit_get_plugin_version() );
	$sysinfo = array_merge($sysinfo, $newarray);
	
	// WP version
	$newarray = array ( 'WP version' => get_bloginfo('version') );
	$sysinfo = array_merge($sysinfo, $newarray);

	// bbpress version
	if (function_exists('bbPress')) {
		$bbp = bbpress();
	} else {
		global $bbp;
	}
	if (isset($bbp->version)) {
		$bbpversion = $bbp->version;
	} else {
		$bbpversion = '???';
	}		
	$newarray = array ( 'bbPress version' => $bbpversion );
	$sysinfo = array_merge($sysinfo, $newarray);

	// theme
	$mytheme = wp_get_theme();
	$newarray = array ( 'Theme' => $mytheme["Name"].' '.$mytheme["Version"] );
	$sysinfo = array_merge($sysinfo, $newarray);

	// PHP version
	$newarray = array ( 'PHP version' => phpversion() );
	$sysinfo = array_merge($sysinfo, $newarray);

	// database info
	$db_charset = $wpdb->get_results( 'SHOW VARIABLES LIKE "character_set_database"' );
	$db_collation = $wpdb->get_results( 'SHOW VARIABLES LIKE "collation_database"' );
	$dbchar = $dbcoll = 'unknown';
	if ($db_charset[0]->Value) $dbchar = $db_charset[0]->Value;
	if ($db_collation[0]->Value) $dbcoll = $db_collation[0]->Value;
	$newarray = array ( 'DB info' => 'DB charset: ' . $dbchar . ' / DB collation: ' . $dbcoll );
	$sysinfo = array_merge($sysinfo, $newarray);
	
	// site url		
	$newarray = array ( 'site url' => get_bloginfo('url') );
	$sysinfo = array_merge($sysinfo, $newarray);
	
	// Active plugins
	$newarray = array ( 'Active Plugins' => 'Name and Version' );
	$sysinfo = array_merge($sysinfo, $newarray);
	$plugins=get_plugins();
	$activated_plugins=array();
	$i = 1;
	foreach (get_option('active_plugins') as $p){           
		if(isset($plugins[$p])){
			$linetoadd = $plugins[$p]["Name"] . ' ' . $plugins[$p]["Version"] . '<br>';
			$newarray = array ( '- p'.$i => $linetoadd );
		       	$sysinfo = array_merge($sysinfo, $newarray);
		       	$i = $i + 1;
		}           
	}
	// Return array
	return $sysinfo;
}

function bbptoolkit_get_plugin_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename(BBPT_PLUGIN_DIR));
	$plugin_file = basename( 'bbp-toolkit.php' );
	return $plugin_folder[$plugin_file]['Version'];
}


?>