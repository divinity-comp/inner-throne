<?php
/*
 * Plugin Name: Bulk Change Role
 * Plugin URI: http://webxmedia.co.uk
 * Description: Transfer all users from one role to another.
 * Version: 1.1
 * Author: Matt Whiteman
 * Author URI: http://webxmedia.co.uk
 * License: GPLv2
 */

/*
uncomment for testing purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

include_once 'includes/admin-enqueue-scripts.php';
include_once 'includes/ajax.php';
include_once 'includes/view.php';

?>