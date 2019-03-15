<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Copyright (C) 2018 hosting.io
 *
 * @wordpress-plugin
 * Plugin Name:       JPG, PNG Compression and Optimization
 * Plugin URI:        https://hosting.io
 * Description:       Image Compression and resizing - Setup under the Tools menu
 * Version:           1.7.35
 * Author:            pigeonhut, https://hosting.io, Jody Nesbitt
 * Author URI:        https://hosting.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
// require_once dirname(dirname(__FILE__)) . '/publitio_api.php';

define("OPTIMISATIONIO_IMAGE_COMPRESSION_ADDON", true);

define('WPIMAGE_VERSION', '1.6.28');
define('WPIMAGE_SCHEMA_VERSION', '1.1');

define('WPIMAGE_DEFAULT_MAX_WIDTH', 1024);
define('WPIMAGE_DEFAULT_MAX_HEIGHT', 1024);
define('WPIMAGE_DEFAULT_BMP_TO_JPG', 1);
define('WPIMAGE_DEFAULT_PNG_TO_JPG', 0);
define('WPIMAGE_DEFAULT_QUALITY', 90);

define('WPIMAGE_SOURCE_POST', 1);
define('WPIMAGE_SOURCE_LIBRARY', 2);
define('WPIMAGE_SOURCE_OTHER', 4);

define('WPIMAGE_AJAX_MAX_RECORDS', 265);

$wpImagelazySizesDefaults = serialize( array(
    'lazyload_expand' => 359,
    'lazyload_optimumx' => 'false',
    'lazyload_intrinsicRatio' => 'false',
    'lazyload_iframe' => 'false',
    'lazyload_autosize' => 'true',
    'lazyload_preloadAfterLoad' => 'false'
) );

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

load_plugin_textdomain('wp-image-compression', false, 'wp-image-compression/languages/');
register_activation_hook(__FILE__, 'wpimagecompression_install');

function wpimagecompression_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . "image_compression_settings";
    $sqle = "DROP TABLE IF EXISTS $table_name;";
    $sql = "CREATE TABLE $table_name (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
         `total_size_optimized` float DEFAULT NULL,
         `total_image_optimized` int(11) DEFAULT NULL,
         `total_allowed` int(11) NOT NULL DEFAULT '500',
         `created` datetime DEFAULT NULL,
         `modified` datetime DEFAULT NULL,
         PRIMARY KEY (`id`) );";
    dbDelta($sqle);
    dbDelta($sql);

    $table_name1 = $wpdb->prefix . "image_compression_details";
    $sqle1 = "DROP TABLE IF EXISTS $table_name1;";
    $sql1 = "CREATE TABLE $table_name1 (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
         `image_id` int(11) DEFAULT NULL,
         `file_title` varchar(255) DEFAULT NULL,
         `file_description` varchar(500) DEFAULT NULL,
         `address` varchar(500) DEFAULT NULL,
         `longitude` varchar(100) DEFAULT NULL,
         `latitude` varchar(100) DEFAULT NULL,
         `created` datetime DEFAULT NULL,
         `modified` datetime DEFAULT NULL,
         PRIMARY KEY (`id`) );";
    dbDelta($sqle1);
    dbDelta($sql1);
}

/**
 * import supporting libraries
 */
include_once plugin_dir_path(__FILE__) . 'libs/utils.php';
include_once plugin_dir_path(__FILE__) . 'settings.php';
include_once plugin_dir_path(__FILE__) . 'ajax.php';


//execute elasticsearch activation command
// $output = shell_exec('sudo service elasticsearch start');
// echo "<pre>$output</pre>";

function wpimages_get_source() {

    $id     = array_key_exists('post_id', $_REQUEST) ? $_REQUEST['post_id'] : '';
    $action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : '';

    // a post_id indicates image is attached to a post
    if ($id > 0) {
        return WPIMAGE_SOURCE_POST;
    }

    // post_id of 0 is 3.x otherwise use the action parameter
    if ($id === 0 || $action == 'upload-attachment') {
        return WPIMAGE_SOURCE_LIBRARY;
    }

    // we don't know where this one came from but $_REQUEST['_wp_http_referer'] may contain info
    return WPIMAGE_SOURCE_OTHER;
}

/**
 * Given the source, returns the max width/height
 *
 * @example:  list($w,$h) = wpimages_get_max_width_height(WPIMAGE_SOURCE_LIBRARY);
 * @param int WPIMAGE_SOURCE_POST | WPIMAGE_SOURCE_LIBRARY | WPIMAGE_SOURCE_OTHER
 */
function wpimages_get_max_width_height($source) {

    $w = wpimages_get_option('wpimages_max_width', WPIMAGE_DEFAULT_MAX_WIDTH);
    $h = wpimages_get_option('wpimages_max_height', WPIMAGE_DEFAULT_MAX_HEIGHT);

    switch ($source) {
        case WPIMAGE_SOURCE_POST:
            break;
        case WPIMAGE_SOURCE_LIBRARY:
            $w = wpimages_get_option('wpimages_max_width_library', $w);
            $h = wpimages_get_option('wpimages_max_height_library', $h);
            break;
        default:
            $w = wpimages_get_option('wpimages_max_width_other', $w);
            $h = wpimages_get_option('wpimages_max_height_other', $h);
            break;
    }

    return array($w, $h);
}

function wpimages_makedirs($dirpath, $mode=0777) {
    return is_dir($dirpath) || mkdir($dirpath, $mode, true);
}

/**
 * Handler after a file has been uploaded.  If the file is an image, check the size
 * to see if it is too big and, if so, resize and overwrite the original
 * @param Array $params
 */
function wpimages_handle_upload($params) {

    /* debug logging... */
    // file_put_contents ( "debug.txt" , print_r($params,1) . "\n" );
    // if "noresize" is included in the filename then we will bypass wpimage scaling
    if (strpos($params['file'], 'noresize') !== false) {
        return $params;
    }

    $use_our_image_cdn = get_option( 'wpimages_use_our_image_cdn');
    if ($use_our_image_cdn == '1') {
        //below publitio code for upload will automatically work in class-wp-image-compression.php file in wpimage_media_uploader_callback() function


       //first upload file to publitio

           /* $publitio_api = new PublitioAPI('hxr7aqQDXG6WyLMApSjc', 'SmSt4vSBRtBW2m0kLAx5HsPikzhNwLuj');
            $image_path = $params['file'];
            $filename = basename($image_path);
            $extensions = array('.jpg', '.JPG', '.png' ,'.PNG' ,'.jpeg' ,'.JPEG');
                foreach ($extensions as $key => $extension) {
                    if (strpos($filename, $extension) !== false) {
                        $img_name = str_replace($extension,"",$filename);
                    }
                }
            //upload original image size to publitio
            $response = $publitio_api->upload_file($image_path, "file", array('name' => $filename, 'public_id' => $img_name, 'position' => 'top-right', 'padding' => '20'));  */
        //remianing part is done in class-wp-image-compression.php in wpimage_media_uploader_callback function where we will replace file contents of original image with less quality image fetched from publitio

    } else {
   //we will only use publitio image conversion above, in case publitio doesnt work, we can use below code later


/*        // if preferences specify so then we can convert an original bmp or png file into jpg
        if ( 'image/bmp' === $params['type'] && wpimages_get_option('wpimages_bmp_to_jpg', WPIMAGE_DEFAULT_BMP_TO_JPG)) {
            $params = wpimages_convert_to_jpg('bmp', $params);
        }

        if ( 'image/png' === $params['type'] && wpimages_get_option('wpimages_png_to_jpg', WPIMAGE_DEFAULT_PNG_TO_JPG)) {
            $params = wpimages_convert_to_jpg('png', $params);
        }

        // make sure this is a type of image that we want to convert and that it exists
        // @TODO when uploads occur via RPC the image may not exist at this location
        $oldPath = $params['file'];

        if ((!is_wp_error($params)) && file_exists($oldPath) && in_array($params['type'], array('image/png', 'image/gif', 'image/jpeg'))) {

            // figure out where the upload is coming from
            $source = wpimages_get_source();

            list($maxW, $maxH) = wpimages_get_max_width_height($source);

            list($oldW, $oldH) = getimagesize($oldPath);

            if (($oldW > $maxW && $maxW > 0) || ($oldH > $maxH && $maxH > 0)) {

                $quality = wpimages_get_option('wpimages_quality', WPIMAGE_DEFAULT_QUALITY);

                list($newW, $newH) = wp_constrain_dimensions($oldW, $oldH, $maxW, $maxH);

                // this is wordpress prior to 3.5 (image_resize deprecated as of 3.5)
                $resizeResult = wpimages_image_resize($oldPath, $newW, $newH, false, null, null, $quality);

                // uncomment to debug error handling code:
                // $resizeResult = new WP_Error('invalid_image', __(print_r($_REQUEST)), $oldPath);

                // regardless of success/fail we're going to remove the original upload
                unlink($oldPath);

                if (!is_wp_error($resizeResult)) {

                    $newPath = $resizeResult;

                    // remove original and replace with re-sized image
                    rename($newPath, $oldPath);
                }
                else {
                    // resize didn't work, likely because the image processing libraries are missing
                    $params = wp_handle_upload_error( $oldPath, sprintf( __("Oh Snap! Wp image resizer was unable to resize this image "
                        . "for the following reason: '%s'.  If you continue to see this error message, you may need to upgrade plugin"
                        . " or disable the Wp image resizer plugin."
                        . "  If you think you have discovered a bug, please report it on the Wp image resizer support forum.", 'wpimage' ), $resizeResult->get_error_message() ) );
                }
            }
        }*/
    }

    return $params;
}

/*
 * On media remove, also remove it from plugin's "backup" folder, if exists.
 */
function wpimages_handle_remove($post_id){
    $meta = wp_get_attachment_metadata( $post_id );
    if( isset( $meta['file'] ) ){
        $uploads = wp_upload_dir();
        $file = $uploads['basedir'] . '/optimisationio_media_backup/' . basename( $meta['file'] );
        if( file_exists( $file ) ){
            unlink( $file );
        }
    }
    return $post_id;
}

/**
 * read in the image file from the params and then save as a new jpg file.
 * if successful, remove the original image and alter the return
 * parameters to return the new jpg instead of the original
 *
 * @param string 'bmp' or 'png'
 * @param array $params
 * @return array altered params
 */
function wpimages_convert_to_jpg($type, $params) {

// debug('qwe'); die();
    $img = null;

    if ('bmp' === $type) {
        include_once 'libs/imagecreatefrombmp.php';
        $img = imagecreatefrombmp($params['file']);
    }
    elseif ('png' === $type) {
        if (!function_exists('imagecreatefrompng')) {
            return wp_handle_upload_error($params['file'], 'wpimages_convert_to_jpg requires gd library enabled');
        }
        $img = imagecreatefrompng($params['file']);
    }
    else {
        return wp_handle_upload_error($params['file'], 'Unknown image type specified in wpimages_convert_to_jpg');
    }

    // we need to change the extension from the original to .jpg so we have to ensure it will be a unique filename
    $uploads     = wp_upload_dir();
    $oldFileName = basename($params['file']);
    $newFileName = basename(str_ireplace("." . $type, ".jpg", $oldFileName));
    $newFileName = wp_unique_filename($uploads['path'], $newFileName);

    $quality = wpimages_get_option('wpimages_quality', WPIMAGE_DEFAULT_QUALITY);

    if (imagejpeg($img, $uploads['path'] . '/' . $newFileName, $quality)) {
        // conversion succeeded.  remove the original bmp & remap the params
        unlink($params['file']);

        $params['file'] = $uploads['path'] . '/' . $newFileName;
        $params['url']  = $uploads['url'] . '/' . $newFileName;
        $params['type'] = 'image/jpeg';
    } else {
        unlink($params['file']);

        return wp_handle_upload_error($oldPath, __("Oh Snap! Wp image resizer was Unable to process the $type file.  "
            . "If you continue to see this error you may need to disable the $type-To-JPG "
            . "feature in Wp image convertor settings.", 'wpimage'));
    }

    return $params;
}




function wpimages_pretty_kb( $bytes ){
    return round( ( $bytes / 1024 ), 2 ) . ' kB';
}

/* add filters to hook into uploads */
add_filter('wp_handle_upload', 'wpimages_handle_upload');

/* add filters to hook images remove */
add_filter('delete_attachment', 'wpimages_handle_remove');

/* add filters/actions to customize upload page */

// TODO: if necessary to update the post data in the future...
// add_filter( 'wp_update_attachment_metadata', 'wpimages_handle_update_attachment_metadata' );

if ( ! class_exists( 'Wp_Image_compression' ) ) {
    require_once dirname( __FILE__ ) . '/lib/class-wp-image-compression.php';
}

new Wp_Image_compression();

//update use our cdn option to no
function myplugin_activate() {

    $is_option = get_option( 'wpimages_use_our_image_cdn');
    if (empty($is_option)) {

    } else {
        update_option('wpimages_use_our_image_cdn', 0);  //no
    }
}
register_activation_hook( __FILE__, 'myplugin_activate' );
