<?php
class Captionpix_Plugin {

	private static $links = array();

	public static function get_link_url($key) {
		if (array_key_exists($key, self::$links))
			return self::$links[$key];
		else
			return '';
	}
	
	public static function init() {
		$dir = dirname(__FILE__) . '/';
		require_once($dir . 'class-utils.php');
		require_once($dir . 'class-diy-options.php');
		require_once($dir . 'class-options.php');
		require_once($dir . 'class-updater.php');
		require_once($dir . 'class-theme-factory.php');
		require_once($dir . 'class-public.php');
		CaptionPix_Options::init();
		CaptionPix::init();
	}

	public static function admin_init() {
		$dir = dirname(__FILE__) . '/';
		require_once($dir . 'class-tooltip.php');
		require_once($dir . 'class-feed-widget.php');		
		require_once($dir . 'class-admin.php');
		require_once($dir . 'class-dashboard.php');
		require_once($dir . 'class-licence.php');
		require_once($dir . 'class-defaults.php');
		require_once($dir . 'class-themes.php');
		$intro = new Captionpix_Dashboard(CAPTIONPIX_VERSION, CAPTIONPIX_PATH, CAPTIONPIX);	
		self::$links['intro'] = $intro->get_url();
		$licence = new Captionpix_Licence(CAPTIONPIX_VERSION, CAPTIONPIX_PATH, CAPTIONPIX,'captionpix_license');	
		self::$links['licence'] = $licence->get_url();
		$defaults = new Captionpix_Defaults(CAPTIONPIX_VERSION, CAPTIONPIX_PATH, CAPTIONPIX, 'captionpix_defaults');			
		self::$links['defaults'] = $defaults->get_url();
		$themes = new Captionpix_Themes(CAPTIONPIX_VERSION, CAPTIONPIX_PATH, CAPTIONPIX, 'captionpix_themes');	
		self::$links['themes'] = $themes->get_url();
	}
}
