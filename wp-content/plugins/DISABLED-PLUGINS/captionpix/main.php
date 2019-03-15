<?php
/*
 * Plugin Name: Caption Pix
 * Plugin URI: http://www.captionpix.com
 * Description: Displays images with captions beautifully
 * Version: 1.7
 * Author: Russell Jamieson
 * Author URI: http://www.diywebmastery.com/about
 * License: GPLv2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
define('CAPTIONPIX_VERSION', '1.7');
define('CAPTIONPIX', 'captionpix');
define('CAPTIONPIX_FRIENDLY_NAME', 'CaptionPix');
define('CAPTIONPIX_PATH', CAPTIONPIX.'/main.php');
define('CAPTIONPIX_HOME', 'http://www.captionpix.com/');
define('CAPTIONPIX_ICON', 'dashicons-format-image');
define('CAPTIONPIX_NEWS', 'http://www.diywebmastery.com/tags/newsfeed/feed/?images=1&featured_only=1');
define('CAPTIONPIX_PLUGIN_URL', plugins_url(CAPTIONPIX));
if (!defined('CAPTIONPIX_IMAGES_URL')) define('CAPTIONPIX_IMAGES_URL', CAPTIONPIX_PLUGIN_URL.'/images');
if (!defined('CAPTIONPIX_BORDERS_URL')) define('CAPTIONPIX_BORDERS_URL', CAPTIONPIX_PLUGIN_URL.'/borders');
if (!defined('CAPTIONPIX_FRAMES_URL')) define('CAPTIONPIX_FRAMES_URL', CAPTIONPIX_PLUGIN_URL.'/frames');
require_once(dirname(__FILE__) . '/classes/class-plugin.php');
add_action ('init',  array('Captionpix_Plugin', 'init'),0);
if (is_admin()) add_action ('init',  array('Captionpix_Plugin', 'admin_init'),0);
?>