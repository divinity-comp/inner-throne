<?php
class Captionpix_Themes extends Captionpix_Admin {

	function init() {
		add_action('admin_menu',array($this, 'admin_menu'));		
	}

	function admin_menu() {
		$this->screen_id = add_submenu_page($this->get_parent_slug(), __('CaptionPix Themes'), __('Themes'), 'manage_options', $this->get_slug(), array($this,'page_content'));
		add_action('load-'.$this->get_screen_id(), array($this, 'load_page'));
	}

	function page_content() {
 		$title = $this->admin_heading('CaptionPix Themes');				
		$this->print_admin_form($title, __CLASS__);
	} 

	function load_page() {
		add_action('admin_enqueue_scripts', array($this,'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this,'enqueue_postbox_scripts'));
		$this->add_meta_box('free-themes','Free CaptionPix Themes',  'free_panel');
		$this->add_meta_box('bonus-themes', 'Free Licensed CaptionPix Themes','bonus_panel');
		$current_screen = get_current_screen();
		if (method_exists($current_screen,'add_help_tab')) {
    		$current_screen->add_help_tab( array(
        		'id' => 'captionpix_licence_tab',
        		'title'	=> __('CaptionPix Themes'),
        		'content' => '<h3>CaptionPix</h3><p>Here you can view the available CaptionPix themes for your captions</p>'));
		}	
	}

	function enqueue_styles() {
		wp_enqueue_style('captionpix-admin',CAPTIONPIX_PLUGIN_URL.'/styles/admin.css', array(), CAPTIONPIX_VERSION );
		wp_enqueue_style('captionpix-themes',CAPTIONPIX_PLUGIN_URL.'/styles/themes.css', array(), CAPTIONPIX_VERSION );
	}

	function screenshot($theme) {
		$images = 'http://images.captionpix.com/themes/';
   		return '<a href="'.$images.$theme.'-big.jpg" rel="thickbox-free" class="thickbox"><img src="'.$images.$theme.'.jpg" title="'.
				$theme.' Theme" alt="Screenshot of '.$theme.' theme" /></a>';
   }

	function captioned_screenshot($theme,$group) {
		$images = 'http://images.captionpix.com/themes/';
		$title = ucwords($theme.' theme');
   		$attr = array('theme' => 'crystal', 'width'=>'220', 'float'=>'center', 'imgsrc'=> $images.$theme.'.jpg',
   			'imglink'=> $images.$theme.'-big.jpg', 'imglinkrel'=> 'thickbox-'.$group, 'imglinkclass'=>'thickbox', 
   			'imgtitle'=> $title, 'imgalt' => 'Screenshot of '.$title, 'captiontext' => $title);
   		return CaptionPix::display($attr);
   }

	function free_panel($post, $metabox) {

		$themes = Captionpix_Theme_Factory::get_themes_in_set('free');
	    $themelist = '';
	    foreach ($themes as $theme) $themelist .= '<li>'.$this->captioned_screenshot($theme,'free').'</li>';
		print <<< FREE_PANEL
<p>The following themes are available to all users in the current version of CaptionPix.</p>
<p>Click the image for a larger example of how the theme looks with text wrapped around it.</p>
<ul class="cpix-thumbnails">
{$themelist}
</ul>
FREE_PANEL;
	}	

	function bonus_panel($post, $metabox) {
		$url= $_SERVER['REQUEST_URI'];
        $refresh = array_key_exists('refresh',$_GET);
        if ($refresh) {
        	$cache = false;
        	Captionpix_Updater::update($cache); //update cache with latest entitlements as a licensed user
			}
		else {
			$cache = true;
			$url .= "&refresh=true";
			}
        $themes = Captionpix_Theme_Factory::get_themes_in_set('licensed',$cache);
	    $themelist = '';
	    foreach ($themes as $theme) $themelist .= '<li>'.$this->captioned_screenshot($theme,'licensed').'</li>';
		print <<< BONUS_PANEL
<p>The following themes are available to users who register and install the FREE licence.</p>
<p>Click the image for a larger example of how the theme looks with text wrapped around it.</p>
<ul class="cpix-thumbnails">
{$themelist}
</ul>
<p>New themes will appear here automatically within 24 hours of being released.  However, if you have been notified of a release of new 
themes today then you should click to see the latest <a rel="nofollow" href="{$url}">CaptionPix themes.</a></p>
BONUS_PANEL;
	}	

	function help_panel($post, $metabox) {
		$home = CAPTIONPIX_HOME;
		print <<< HELP_PANEL
<p><img src="http://images.captionpix.com/layout/captionpix-logo.jpg" alt="CaptionPix Image Captioning Plugin" /></p>
<ul>
<li><a href="{$home}" rel="external">CaptionPix Plugin Home Page</a></li>
<li><a href="{$home}instructions/" rel="external">How To Use CaptionPix</a></li>
<li><a href="{$home}tutorials/" rel="external">FREE CaptionPix Tutorials</a></li>
<li><a href="{$home}help/" rel="external">CaptionPix Help</a></li>
</ul>
HELP_PANEL;
	}	

}
