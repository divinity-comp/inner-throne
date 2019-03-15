<?php

/**
 *
 *
 * @package cometchat
 */
	if ( ! defined( 'ABSPATH' ) ) exit;

	include_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'plugin.php');

	if ( !current_user_can( 'activate_plugins' ) ) {
		exit("You don't have permission to access this plugin.");
	}

	$selfhostedcc = $adminpanelurl = $cometchat_dir = $adminpaneliframe = $extraction_path = $cometchatPluginPath = $cometchatLogo = '';

	$cometchatPluginPath = plugin_dir_url( __FILE__ );
	if(!defined('CC_PLUGIN_REFRRER')) define('CC_PLUGIN_REFRRER', $cometchatPluginPath);

	$cometchatLogo = esc_url($cometchatPluginPath.'images/cometchat_logo.png');
	$cometchatDockedLayout = esc_url($cometchatPluginPath.'images/docked_layout.png');

	if(empty($cc_clientid)){
		$cc_dir_name = getCometChatDirectoryName();
		$siteUrl = get_site_url();
		$adminpanelurl = esc_url($siteUrl.'/'.$cc_dir_name.'/admin/');
		$cometchat_dir = ABSPATH.$cc_dir_name;

		$selfhostedcc = <<<EOD
			<div id="cc_credentials">
				<p style="font-weight:bold;color: black !important;">
					Username: cometchat</br>
					Password: cometchat</br>
				</p>
				<p style="padding-bottom:20px;color: black !important;">
					Do not forget to change these default credentials after you login.
				</p>
			</div>
EOD;
	}

	if(!empty($cc_clientid) || !empty($_COOKIE['cc_cloud'])) {
		$cc_client_url = (!empty($_COOKIE['cc_cloud']) && $_COOKIE['cc_cloud'] != 1) ? $_COOKIE['cc_cloud'] : $cc_clientid;
		if($cc_client_url < 50000){
			$adminpanelurl = esc_url("//".$cc_client_url.".cometondemand.net/admin/");
		}else{
			$adminpanelurl = esc_url("https://secure.cometchat.com/licenses/access/".$cc_client_url);
		}
	}

	global $wpdb;
	$prefix = $wpdb->prefix;

	$result = $wpdb->get_var("SELECT COUNT(1) FROM information_schema.tables WHERE table_schema='$wpdb->dbname' AND table_name='cometchat_settings'");

	/** Check if buddypress intalled or not **/
	if(!empty($result) && !is_plugin_active('buddypress/bp-loader.php')){
		$guestsql = ("INSERT INTO `cometchat_settings` (`setting_key`, `value`, `key_type`) VALUES ('guestsMode', '1', '1') on duplicate key UPDATE value = 1");
        $execute = $wpdb->get_row($guestsql);
	}

	/** Initial access of CometChat Installation **/
	$sql = ("SELECT option_value FROM ".$prefix."options WHERE option_name = 'ccintialaccess'");
	$result = $wpdb->get_row($sql);

	if(!empty($result) && !empty($result->option_value)){
		$ccintialaccess = $result->option_value;
	}

	if(is_dir($cometchat_dir) && file_exists($cometchat_dir. DIRECTORY_SEPARATOR .'license.php') && is_dir($cometchat_dir. DIRECTORY_SEPARATOR .'admin') || !empty($cc_clientid) || !empty($_COOKIE['cc_cloud'])){

		if (!empty($cc_clientid) || !empty($_COOKIE['cc_cloud'])) {
			$selfhostedcc = "";
		}

		wp_enqueue_style("ccstyle", plugin_dir_url( __FILE__ ).'css/ccstyle.css');
		wp_enqueue_script("ccscript", plugin_dir_url( __FILE__ ).'js/ccscript.js');

		if(empty($ccintialaccess)){
			$adminpaneliframe = <<<EOD

				<div class="cometchat_outerframe">
					<div class="cometchat_middleform" >
						<div class="cometchat-tab-content" onclick="">
				            <div id="cometchat_adminpanel">
				            	<div class="cometchat_logo_div" style="margin-bottom: 0px !important;opacity:0.1;" id="ccimg">
									<img src="{$cometchatLogo}" class="cometchat_logo_image" style="padding-top: 100px !important;">
								</div><br>
								<iframe src="{$adminpanelurl}" id="ccadminpanel" style="display: none;"></iframe>

								<div id="ccinitialaccess" style="background: #ffffff;width: 50%;margin: 0px auto;border-radius:5px;">
									<p style="padding: 20px 0px 5px 0px; font-weight:bold;color: black !important;">CometChat has been successfully installed on your site. </br>We have pre-enabled our Docked Layout for your convenience. </p>
									<img src="{$cometchatDockedLayout}" class="" style="width: 63%;">
									<p style="color: black !important;">
										You can change the layout directly from the CometChat Admin Panel:</br>
									</p>
										<button type="button" id="cc_plugin_admin_panel" class="ccadminpanel ccadminpanel-primary" onclick="openCCAdminPanel('{$adminpanelurl}');" style="opacity:0.1;margin-bottom: 10px;">Launch Admin Panel</button>
									$selfhostedcc
								</div>
				            </div>
			        	</div>
					</div>
				</div>
EOD;
		add_option('ccintialaccess','1','','no');
		}else{
			include_once(plugin_dir_path(__FILE__).'admin.php');
		}
	}

	if(is_dir($cometchat_dir) && file_exists($cometchat_dir. DIRECTORY_SEPARATOR .'license.php') && is_dir($cometchat_dir. DIRECTORY_SEPARATOR .'admin') || !empty($cc_clientid) || !empty($_COOKIE['cc_cloud'])) {

?>
		<div id="cc_dom_ready" style="opacity:0.1;"><?php echo $adminpaneliframe; ?></div>

<?php
	}
	else{
		$dir = plugin_dir_path( __FILE__ ).'installer.php';
		require_once($dir);
	}

?>