<?php
// add plugin upgrade notification
add_action('in_plugin_update_message-bbp-toolkit/bbp-toolkit.php', 'bbptoolkitshowUpgradeNotification', 10, 2);
function bbptoolkitshowUpgradeNotification($currentPluginMetadata, $newPluginMetadata){
	// check "upgrade_notice"
	if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
		$str = esc_html($newPluginMetadata->upgrade_notice);
		$str = str_replace ('&lt;p&gt;', '' , $str);
		$str = str_replace ('&lt;/p&gt;', '' , $str);
		echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>Upgrade Notice:</strong> ' . $str . '</p>';
	}
}

// Add Settings and Donate next to the plugin on the plugins page
add_filter('plugin_action_links', 'bbptoolkit_plugin_action_links', 10, 2);
function bbptoolkit_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="http://casier.eu/wp-dev/">Donate</a>';
        array_unshift($links, $settings_link);
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/tools.php?page=forums_toolkit">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

function showversion($v) {
	if ($v == BBPTOOLKIT_VERSION) {
		return '&nbsp;&nbsp;<i><b>(v'.$v.')</b></i>';
	} else {
		return '&nbsp;&nbsp;<i>(v'.$v.')</i>';
	}
}

function bbptoolkit_forum_structure() {
	$all_forums_data = array();
	$i = 0;
	if ( bbp_has_forums() ) {
		while ( bbp_forums() ) {
			bbp_the_forum();
			$forum_id = bbp_get_forum_id();
			$all_forums_data[$i]['id'] = $forum_id;
			$all_forums_data[$i]['title'] = bbp_get_forum_title($forum_id);
			// Check for subforums (first level only)
			if ($sublist = bbp_forum_get_subforums($forum_id)) {
				$all_subforums = array();
				foreach ( $sublist as $sub_forum ) {
					$i++;
					$all_forums_data[$i]['id'] = $sub_forum->ID;
					$all_forums_data[$i]['title'] = '- ' . bbp_get_forum_title($sub_forum->ID);
				}
			}					
			$i++;
		} // while()
	} // if()
	return $all_forums_data;
}

function bbptoolkit_plugin_update_check() {
	// Generate CSS needed
	bbptoolkit_generate_css();
}
add_action('plugins_loaded', 'bbptoolkit_plugin_update_check');

// Create Text Domain For Translations
function bbptoolkit_textdomain() {
	load_plugin_textdomain( 'bbp-toolkit', false, dirname( plugin_basename( __FILE__ ) ) );
}
add_action( 'plugins_loaded', 'bbptoolkit_textdomain' );


function bbptoolkit_removehtml_and_cutwords($scontent, $limitwords) {
	// Remove HTML tags
	$scontent = wp_strip_all_tags($scontent);
	// Cut after $limitwords words
	$scontent = preg_replace('/(?<=\S,)(?=\S)/', ' ', $scontent);
	$scontent = str_replace("\n", " ", $scontent);
	$contentarray = explode(" ", $scontent);
	if (count($contentarray)>$limitwords) {
		array_splice($contentarray, $limitwords);
		$scontent = implode(" ", $contentarray)." ...";
	} 
	return $scontent;
}

?>