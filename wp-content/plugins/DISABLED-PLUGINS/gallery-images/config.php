<?php
/**
 * Plugin configurations
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$GLOBALS['gallery_img_aliases'] = array(
	'Gallery_Img_Install'          => 'includes/class-gallery-img-install',
	'Gallery_Img_Template_Loader'  => 'includes/class-gallery-img-template-loader',
	'Gallery_Img_Ajax'             => 'includes/class-gallery-img-ajax',
	'Gallery_Img_Widgets'          => 'includes/class-gallery-img-widgets',
	'Gallery_Img_Widget'           => 'includes/class-gallery-img-huge-it-gallery-widget',
	'Gallery_Img_Shortcode'        => 'includes/class-gallery-img-shortcode',
	'Gallery_Img_Frontend_Scripts' => 'includes/class-gallery-img-frontend-scripts',
	'Gallery_Img_Admin'            => 'includes/admin/class-gallery-img-admin',
	'Gallery_Img_Admin_Assets'     => 'includes/admin/class-gallery-img-admin-assets',
	'Gallery_Img_General_Options'  => 'includes/admin/class-gallery-img-general-options',
	'Gallery_Img_Galleries'        => 'includes/admin/class-gallery-img-galleries',
	'Gallery_Img_Lightbox_Options' => 'includes/admin/class-gallery-img-lightbox-options',
	'Gallery_Img_Featured_Plugins' => 'includes/admin/class-gallery-img-featured-plugins',
	'Gallery_Img_Licensing'        => 'includes/admin/class-gallery-img-licensing'
);

/**
 * @param $classname
 *
 * @throws Exception
 */
function gallery_img_autoload( $classname ) {
	global $gallery_img_aliases;

	/**
	 * We do not touch classes that are not related to us
	 */
	if ( ! strstr( $classname, 'Gallery_Img_' ) ) {
		return;
	}

	if ( ! key_exists( $classname, $gallery_img_aliases ) ) {
		throw new Exception( 'trying to load "' . $classname . '" class that is not registered in config file.' );
	}

	$path = Gallery_Img()->plugin_path() . '/' . $gallery_img_aliases[ $classname ] . '.php';

	if ( ! file_exists( $path ) ) {

		throw new Exception( 'the given path for class "' . $classname . '" is wrong, trying to load from ' . $path );

	}

	require_once $path;

	if ( ! interface_exists( $classname ) && ! class_exists( $classname ) ) {

		throw new Exception( 'The class "' . $classname . '" is not declared in "' . $path . '" file.' );

	}
}

spl_autoload_register( 'gallery_img_autoload' );