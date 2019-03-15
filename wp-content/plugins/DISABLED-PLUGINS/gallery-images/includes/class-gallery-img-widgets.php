<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Class Gallery_Img_Widgets
 */
class Gallery_Img_Widgets{

	/**
	 * Register Huge-IT Gallery Widget
	 */
	public static function init(){
		register_widget( 'Gallery_Img_Widget' );
	}
}

