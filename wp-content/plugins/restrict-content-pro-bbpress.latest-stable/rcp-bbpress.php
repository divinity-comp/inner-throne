<?php
/*
Plugin Name: Restrict Content Pro - bbPress
Plugin URL: http://pippinsplugins.com/restrict-content-pro-bbpress
Description: Adds support for bbPress forums and topics restriction to Restrict Content Pro
Version: 1.0
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Contributors: mordauk
*/


/*******************************************
* plugin text domain for translations
*******************************************/

function rcp_bbp_load_textdomain() {
	load_plugin_textdomain( 'rcp_bbpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'rcp_bbp_load_textdomain' );

/*********************************
* includes
*********************************/

include dirname( __FILE__ ) . '/includes/metaboxes.php';
include dirname( __FILE__ ) . '/includes/topic-functions.php';
include dirname( __FILE__ ) . '/includes/reply-functions.php';
include dirname( __FILE__ ) . '/includes/feedback-filters.php';
include dirname( __FILE__ ) . '/includes/forum-functions.php';
