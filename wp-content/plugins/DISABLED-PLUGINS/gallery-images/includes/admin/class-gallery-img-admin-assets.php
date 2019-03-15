<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//todo: correct urls
class Gallery_Img_Admin_Assets {

	/**
	 * Gallery_Img_Admin_Assets constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * @param $hook hook of current page
	 */
	public function admin_styles( $hook ){
		if( in_array($hook, Gallery_Img()->admin->pages ) ){
			wp_enqueue_style( "gallery_admin_css", Gallery_Img()->plugin_url()."/assets/style/admin.style.css", false );
			wp_enqueue_style( "jquery_ui", Gallery_Img()->plugin_url()."/assets/style/smoothness-ui.css", false );
			wp_enqueue_style( "simple_slider_css", Gallery_Img()->plugin_url()."/assets/style/simple-slider_sl.css",  false );
            wp_enqueue_style( "featured_plugins", Gallery_Img()->plugin_url()."/assets/style/featured-plugins.css",  false );
            wp_enqueue_style( "licensing_css", Gallery_Img()->plugin_url()."/assets/style/licensing.css",  false );
		}
	}

	public function admin_scripts( $hook ) {
		if( in_array($hook, Gallery_Img()->admin->pages ) ){
			wp_enqueue_media();
			wp_enqueue_script( "gallery_admin_js", Gallery_Img()->plugin_url()."/assets/js/admin.js", false );
			wp_enqueue_script( "jquery_ui_new", esc_url("http://code.jquery.com/ui/1.10.4/jquery-ui.js"), false );
			wp_enqueue_script( "simple_slider_js", Gallery_Img()->plugin_url().'/assets/js/simple-slider.js', false );
			wp_enqueue_script( 'param_block2', Gallery_Img()->plugin_url()."/assets/js/jscolor.js");
		}
	}

}
