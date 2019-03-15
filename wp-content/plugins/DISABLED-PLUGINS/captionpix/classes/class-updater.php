<?php
class Captionpix_Updater {
	private static $plugin=CAPTIONPIX;
	private static $local_version=CAPTIONPIX_VERSION;
	private static $remote_updater='http://updater.captionpix.com/updates.php';
	private static $remote_updater_backup='http://updates.captionpix.com/updates.php';
	private static $default_action='updates';	
	private static $initialized=false;
	private static $licence='-';
	private static $upgrader;
	private static $version;
	private static $package;
	private static $expiry;
	private static $notice;
	private static $updates;
	private static $valid;
	
	private static function init() {
    	if (!self::$initialized) self::update(); 
	}	
	
	public static function update($cache = true, $action = '') {
		if (empty($action)) $action = self::$default_action;
		return ($cache && self::$initialized && ($action==self::$default_action)) ? self::$updates : self::get_updates($action,$cache); 
	}

	public static function set_licence($new_licence, $md5) {
		self::$licence = empty($new_licence) ? '' : ($md5 ? md5($new_licence) : $new_licence);
    }

	public static function get_licence($cache=true) {
    	if (!$cache || ('-'== self::$licence)) self::$licence = get_option(self::add_plugin_prefix('licence'));
    	return self::$licence;
	}
	
	public static function has_licence($cache=true) {
		$licence = self::get_licence($cache);
    	return !empty($licence) && (strlen($licence) == 32); //has a licence worth checking
	}	

	public static function empty_licence($cache=true) {
		$licence = self::get_licence($cache);
    	return empty($licence);
	}
	
	public static function save_licence($new_licence,$md5 = true) {
		$updated = false;
  		$old_licence = self::get_licence(false); //fetch old licence from database
		if ($new_licence != $old_licence) {
		    self::$initialized = false;
			self::set_licence($new_licence,$md5);
   			$updated = update_option(self::add_plugin_prefix('licence'),self::$licence) ;
   			if ($updated) self::update(false); //get updates for new licence
   		}
   		return $updated;
	}

	public static function check_validity(){
    	if (self::get_licence()) {
    		self::init();
    		return self::$valid;
    		}
    	else 
    		return false;
	}

  	public static function get_version(){
    	self::init();
    	return self::$version;
   	}
   
 	public static function get_package(){
    	self::init();
    	return self::$package;
   	}   

	public static function get_notice(){
    	self::init();
    	return self::$notice;
   	}

 	public static function get_expiry(){
    	self::init();
    	return self::$expiry;
   	}

    private static function add_plugin_prefix($action) {
		return str_replace('-','_', self::$plugin).'_'.strtolower($action);
    }
	
	private static function get_updates($action,$cache) {
	    $result = self::has_licence($cache)  ? 
	    	self::parse_updates(self::fetch_remote_or_cache($action,$cache)) :
	    	self::set_defaults(self::empty_licence($cache) ? '' : 'Invalid License key' );
		if ($action==self::$default_action) self::$updates = $result;
		return $result;
	}
	
    private static function parse_updates($response) {
        	self::$initialized = true; 
 			if (is_array($response) && (count($response) >= 6)) {
    	        self::$valid = $response['valid_key']; 
    	        self::$version = $response['version']; 
    	        self::$package = $response['package'];  
    	        self::$notice = $response['notice']; 
    	        self::$expiry = $response['expiry']; 
    			return $response['updates'];
			} else {
				return self::set_defaults('Unable to check for updates. Please try again.'); 
			}
    }

    private static function set_defaults($notice = '') {
 		self::$valid = false; 
    	self::$version = self::$local_version; 
    	self::$package = '';  
    	self::$notice = empty($notice) ? '' : ('<div class="message">'.__($notice).'</div>'); 
    	self::$expiry = ''; 
    	self::$updates = '';
    	return self::$updates;
    }

	private static function fetch_remote_or_cache($action,$cache=true){
		$transient = self::add_plugin_prefix($action);
    	$values = $cache ? get_transient($transient) : false;
    	if ((false === $values)  || is_array($values) || empty($values)) {
    	    $raw_response = self::remote_call($action, $cache);
    	    $values = (is_array($raw_response) && array_key_exists('body',$raw_response)) ? $raw_response['body'] : false;
    	    set_transient($transient, $values, 86400); //cache for 24 hours
		}
		return false === $values ? false : unserialize(gzinflate(base64_decode($values)));
	}

	private static function remote_call($action, $cache=true, $backup = false){
        $options = array('method' => 'POST', 'timeout' => 3);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );
        $raw_response = wp_remote_request(self::get_upgrader($cache, $backup). '&act='.$action  , $options);
        if ( is_wp_error( $raw_response ) || (200 != $raw_response['response']['code']) || empty($raw_response)){
			return $backup ? false : self::remote_call($action, $cache, true);
        } else {
            return $raw_response;
        }
	}

	private static function get_upgrader($cache = true, $backup = false){
        global $wpdb;
        if (empty(self::$upgrader) || ($cache == false) || $backup)
            self::$upgrader = self::get_remote_updater($backup). sprintf("?of=%s&key=%s&v=%s&wp=%s&php=%s&mysql=%s",
                urlencode(self::$plugin), urlencode(self::get_licence()), urlencode(self::$local_version), urlencode(get_bloginfo("version")),
                urlencode(phpversion()), urlencode($wpdb->db_version()));

        return self::$upgrader;
	}

	private static function get_remote_updater($backup = false) {
    	return $backup ? self::$remote_updater_backup : self::$remote_updater ;
	}
   
}
