<?php
class Captionpix_Defaults extends Captionpix_Admin {
	private $tips = array(
      'theme' => array('heading' => 'Theme', 'tip' => 'The theme controls the formatting of the frame, image and caption by setting attributes such as colors, font, margins, padding, etc.'),
		'width' => array('heading' => 'Width', 'tip' => '<p>The width is used to set the size of the image. If you generally want to wrap a paragraph of text around the image then choose a value that is around 50% of the width of the content section of your page. This is typically be around 300 pixels.</p><p class="description">If you want the site to be mobile responsive and have images appear at the maximum possible size for the device then leave the width field blank.</b></p>'),
	   'align' => array('heading' => 'Alignment', 'tip' => 'Set this value if you typically want to center the image or float the image to the left or right of the text.'),
		'marginside' => array('heading' => 'Side Margin', 'tip' => 'The side margin is used to create white space between the image and text paragraph. (This will be on the right of the image if the image is aligned to the left, and on the left of the image if the image is aligned to the right).'),
		'margintop' => array('heading' => 'Top Margin', 'tip' => 'The Top Margin is used to align the top of image with the top of the text paragraph.'),
		'marginbottom' => array('heading' => 'Bottom Margin', 'tip' => 'The Bottom Margin adds white space between the bottom of the image and the paragraph text.'),
	);


	function init() {
		add_action('admin_menu',array($this, 'admin_menu'));				
	}

	function admin_menu() {
		$this->screen_id = add_submenu_page($this->get_parent_slug(), __('CaptionPix Default Settings'), __('Settings'), 'manage_options', 
			$this->get_slug(), array($this,'page_content'));
		add_action('load-'.$this->screen_id, array($this, 'load_page'));
	}

 
	function page_content() {
 		$title = $this->admin_heading('Captionpix Defaults');				
		$this->print_admin_form($title,__CLASS__, array_keys($this->tips)); 
	} 

	function load_page() {
 		if (isset($_POST['options_update']) ) $this->save();	
		$this->add_meta_box('intro', 'Intro', 'intro_panel');
		$this->add_meta_box('settings', 'Settings', 'settings_panel', array ('options' =>  Captionpix_Options::get_options($this->use_cache())));
		$this->add_meta_box('news', 'News', 'news_panel', null, 'advanced');
		$this->set_tooltips($this->tips);
		add_action('admin_enqueue_scripts', array($this,'enqueue_admin_styles'));
		add_action('admin_enqueue_scripts', array($this,'enqueue_metabox_scripts'));
		add_action('admin_enqueue_scripts', array($this,'enqueue_postbox_scripts'));
		add_filter('screen_layout_columns', array($this, 'screen_layout_columns'), 10, 2);
		$current_screen = get_current_screen();
		if (method_exists($current_screen,'add_help_tab')) {
    		$current_screen->add_help_tab( array(
        		'id' => 'captionpix_instructions_tab',
        		'title'	=> __('CaptionPix Defaults'),
        		'content' => '<h3>CaptionPix</h3><p>Here you can set the default plugin settings.</p><p><a href="'.CAPTIONPIX_HOME.'tutorials" rel="external">Getting Started with CaptionPix</a></p>'));
		}
	}

    function use_cache() {
		return !(array_key_exists('options_update',$_POST) && isset($_POST['options_update'])) ;    
    }

	function save() {
		check_admin_referer(__CLASS__);
		return $this->save_options('Captionpix_Options', 'Defaults') ;
	}

	function intro_panel($post,$metabox){		 	
		print <<< INTRO_PANEL
<p>For help on gettting the best from CaptionPix visit the <a href="http://www.captionpix.com/">CaptionPix Plugin Home Page</a></p>
<p>If you supply a value for one of the settings below, the plugin will remember it so you do not need to supply it for every image.</p>
<p>Note also that you can override any default value you set here by specifying a value on each image you caption.</p>
<p>For example, you may use the <em>Crystal</em> theme as the default caption style but perhaps use the <em>Chunky</em> style on a particular image by specifying <code>theme="chunky"</code> in the shortcode</p>
INTRO_PANEL;
	}

 	function settings_panel($post,$metabox) {
      $options = $metabox['args']['options'];
      $this->display_metabox( array(
         'Defaults' => $this->defaults_panel($options),
         'Useful Links' => $this->help_panel(),
		));
   }

	function defaults_panel($options) {			
	   $theme_list = array("" => __('Please select')) + Captionpix_Theme_Factory::get_theme_names_in_order();		
      return 
		$this->fetch_form_field('theme', $options['theme'], 'select', $theme_list) .
		$this->fetch_text_field('width', $options['width'], array('size' => 4, 'maxlength' => 4,'suffix' => 'pixels')).
		$this->fetch_text_field('marginside', $options['marginside'], array('size' => 4, 'maxlength' => 3,'suffix' => 'pixels')).
		$this->fetch_text_field('margintop', $options['margintop'], array('size' => 4, 'maxlength' => 3,'suffix' => 'pixels')).
		$this->fetch_text_field('marginbottom', $options['marginbottom'], array('size' => 4, 'maxlength' => 3,'suffix' => 'pixels')).
		$this->fetch_form_field('align', $options['align'], 'select', array('none','center','left','right'));
	}

	function advanced_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 	
		print <<< ADVANCED_PANEL
<h4>Auto-captioning</h4>
<p>Set auto-captioning if you want to caption the photos automatically based on their title attribute, the alt tag or the post name.</p>
<p>Captions will only applied on pages with single posts and on the home page.</p>
ADVANCED_PANEL;
		$this->print_form_field('autocaption', $options['autocaption'], 'select', CaptionPix_Options::get_autocaptioning());
ADVANCED_PANEL;
	}

	function help_panel() {
		$home = CAPTIONPIX_HOME;
		return <<< HELP_PANEL
<ul>
<li><a href="{$home}" rel="external">CaptionPix Plugin Home Page</a></li>
<li><a href="{$home}instructions/" rel="external">How To Use CaptionPix</a></li>
<li><a href="{$home}tutorials/" rel="external">FREE CaptionPix Tutorials</a></li>
<li><a href="{$home}help/" rel="external">CaptionPix Help</a></li>
</ul>
HELP_PANEL;
	}	


}
