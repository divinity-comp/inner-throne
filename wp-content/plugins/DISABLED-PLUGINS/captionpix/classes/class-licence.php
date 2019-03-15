<?php
class Captionpix_Licence extends Captionpix_Admin {

	function init() {
		add_action('admin_menu',array($this, 'admin_menu'));
	}

	function admin_menu() {
		$this->screen_id = add_submenu_page($this->get_parent_slug(), __('CaptionPix License'), __('License'), 'manage_options', $this->get_slug(), array($this,'page_content'));
		add_action('load-'.$this->get_screen_id(), array($this, 'load_page'));	
	}

	function page_content() {
 		$title = $this->admin_heading('Obtaining a <span class="cpix-highlight">FREE</span> CaptionPix Licence Is A Good Idea!');		
      $this->print_admin_form_with_sidebar($title, __CLASS__, true) ;
	} 
	
	function load_page() {
 		if (isset($_POST['options_update'])) echo $this->save();
		$this->add_meta_box('licence', 'CaptionPix License', 'licence_panel');
		$this->add_meta_box('request', 'Free License Key', 'request_panel', null, 'side');
		add_filter('screen_layout_columns', array($this, 'screen_layout_columns'), 10, 2);
		add_action('admin_enqueue_scripts', array($this,'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this,'enqueue_postbox_scripts'));
		$current_screen = get_current_screen();
		if (method_exists($current_screen,'add_help_tab')) {
    		$current_screen->add_help_tab( array(
        		'id' => 'captionpix_licence_tab',
        		'title'	=> __('CaptionPix License'),
        		'content' => '<h3>CaptionPix</h3><p>Here you can get your FREE CaptionPix License Key.</p><p><a href="'.CAPTIONPIX_HOME.'tutorials" rel="external">Getting Started with CaptionPix</a></p>'));
		}
	}

	function enqueue_styles() {
		wp_enqueue_style('captionpix-admin',CAPTIONPIX_PLUGIN_URL.'/styles/admin.css', array(), CAPTIONPIX_VERSION );
		wp_enqueue_style('captionpix-licence',CAPTIONPIX_PLUGIN_URL.'/styles/licence.css', array(), CAPTIONPIX_VERSION );
	}


	function save() {
		check_admin_referer(__CLASS__);
       	if (Captionpix_Updater::save_licence(trim(stripslashes($_POST['licence'])))) 
       		$message = __("CaptionPix License saved.",CAPTIONPIX);
 		else
       		$message = __("CaptionPix License has not changed.",CAPTIONPIX);
 		Captionpix_Updater::update(false); //update cache with new entitlements as a licensed user
  		return '<div id="message" class="updated fade"><p>' . $message. '</p></div>';
	}

	function licence_panel($post, $metabox) {		
		$home = CAPTIONPIX_HOME;	
    	$themes_url = Captionpix_Plugin::get_link_url('themes'); 		
		$is_valid = $is_pro = false;
		$key_status_indicator ='';
		$notice ='';
		$licence = Captionpix_Updater::get_licence(false);
		if (! empty($licence)) {
         $is_valid = Captionpix_Updater::check_validity();
   		$flag = $is_valid ? 'tick' : 'cross';
   		$key_status_indicator = '<img src="' . CAPTIONPIX_PLUGIN_URL .'/images/'.$flag.'.png" alt="a '.$flag.'" />';
 			$notice = Captionpix_Updater::get_notice();
 		}
      $readonly = $is_valid ? '' : 'readonly="readonly" class="readonly"';
		print <<< LICENCE_PANEL
<p>CaptionPix is a FREE plugin and comes with a <span class="cpix-highlight">standard set of 9 theme designs</span> for image borders.</p>
<p>If you sign up as a free licensed user we'll give you many <a href="<?php echo $themes_url; ?>">MANY MORE CaptionPix themes</a> as a thank you. By signing up you help to support the work we do by allowing us to contact you about our other plugins and tutorials.</p>

<h4>How To Get A FREE CaptionPix License</h4>

<p>To get a license key: </p>
<ol>
<li>Fill out the form on the right</li>
<li>The first email we'll send will contain a link which you need to click, to confirm</li>
<li>Once you've done that we'll send a second email containing your license key</li>
<li>Copy and paste the license key into the box below and click on the save changes button</li>
<li>You're done!</li>
<li>Get back to us with any problems by visiting <a href=" http://www.captionpix.com/getting-help/">our support page</a></li>
</ol>
<label for="licence">License Key: </label><input type="password" name="licence" id="licence"  style="width:320px" value="{$licence}" />&nbsp;{$key_status_indicator}
<p class="cpix-notice">{$notice}</p>

LICENCE_PANEL;
	}

	function request_panel($post, $metabox) {
		$home = CAPTIONPIX_HOME;
		$domain = parse_url(site_url(),PHP_URL_HOST);
		$images_url = CAPTIONPIX_IMAGES_URL;
		print <<< REQUEST_PANEL
<p><img src="{$images_url}/get-free-license-key.png" alt="CaptionPix Free Licence Request" /></p>
<form id="captionpix_signup" name="captionpix_signup" method="post" action="{$home}" onsubmit="return captionpix_validate_form(this);">
<input type="hidden" name="form_storm" value="submit" />
<input type="hidden" name="destination" value="captionpix" />
<input type="hidden" name="domain" value="{$domain}" />
<label for="firstname">First Name<input id="firstname" name="firstname" type="text" value="" /></label><br/>
<label for="email">Your Email<input id="email" name="email" type="text" /></label><br/>
<label id="lsubject" for="subject">Subject<input id="subject" name="subject" type="text" /></label>
<input type="image" src="{$images_url}/get-free-license-key-button.png" />
</form>
REQUEST_PANEL;
	}	

	function controller() {
 		global $screen_layout_columns;		
 		$this_url = $_SERVER['REQUEST_URI']; 		
    	$themes_url = Captionpix_Themes::get_url(); 
?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <h2 class="title">Obtaining a <span class="cpix-highlight">FREE</span> CaptionPix Licence Is A Good Idea!</h2>
		<p>CaptionPix is a FREE plugin and comes with a <span class="cpix-highlight">standard set of 9 theme designs</span> for image borders.</p>
		<p>If you sign up as a free licensed user we'll give you many <a href="<?php echo $themes_url; ?>">MANY MORE CaptionPix themes</a> as a thank you. By signing up you help to support the work we do by allowing us to contact you about our other plugins and tutorials.</p>

        <div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes($this->get_screen_id(), 'side', null); ?>
        </div>
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
			<form id="slickr_flickr_options" method="post" action="<?php echo $this_url; ?>">
			<?php do_meta_boxes($this->get_screen_id(), 'normal', null); ?>
			<p class="submit">
			<input type="submit"  class="button-primary" name="options_update" value="Save Changes" />
			<?php wp_nonce_field(__CLASS__); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			</p>
			</form>
 			</div>
        </div>
        <br class="clear"/>
    </div>
<?php
	}  

}
?>