<?php
class Captionpix_Dashboard extends Captionpix_Admin {

	function init() {
		add_action('admin_menu',array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'register_tooltip_styles'));
        add_action('admin_enqueue_scripts', array($this, 'register_admin_styles'));
	}

	function admin_menu() {
		$this->screen_id = add_menu_page('CaptionPix', 'CaptionPix', 'manage_options', CAPTIONPIX, array($this,'page_content'), CAPTIONPIX_ICON );
		$intro = sprintf('Intro (v%1$s)', CAPTIONPIX_VERSION);				
		add_submenu_page(CAPTIONPIX, CAPTIONPIX_FRIENDLY_NAME, $intro, 'manage_options', CAPTIONPIX, array($this,'page_content') );
		add_action('load-'.$this->get_screen_id(), array($this, 'load_page'));				
	}
	
	function page_content() {
 		$title = $this->admin_heading('Captionpix Resources');				
		$this->print_admin_form($title,__CLASS__);
	}	
	
	function load_page() {
		$this->add_meta_box('intro','Intro', 'intro_panel');
		add_filter('screen_layout_columns', array($this, 'screen_layout_columns'), 10, 2);
		add_action('admin_enqueue_scripts', array($this,'enqueue_admin_styles'));
	}

	function intro_panel() {
    	$licence_url = Captionpix_Plugin::get_link_url('licence'); 
    	$defaults_url = Captionpix_Plugin::get_link_url('defaults'); 
    	$themes_url = Captionpix_Plugin::get_link_url('themes'); 
    	$logo_url = CAPTIONPIX_IMAGES_URL . '/captionpix-logo.jpg';
    	$home_url = CAPTIONPIX_HOME;
    	print <<< INTRO_PANEL
<div style="overflow:auto">
<img src="{$logo_url}" alt="CaptionPix Image Captioning Plugin" style="float:left; padding:0 30px; 0 0" />
<p>To get your FREE license go to <a href="{$licence_url}">License</a></p>
<p>To set up your CaptionPix plugin defaults go to <a href="{$defaults_url}">Settings</a></p>
<p>To see the available CaptionPix themes go to <a href="{$themes_url}">Themes</a></p>
<p>For plugin features and capabilities go to <a href="{$home_url}">{$home_url}</a></p>
<p>For plugin tutorials go to <a href="{$home_url}tutorials">{$home_url}tutorials</a></p>
<p>For help go to <a href="{$home_url}getting-help">{$home_url}help</a></p>
</div>
INTRO_PANEL;
	}
	
}
