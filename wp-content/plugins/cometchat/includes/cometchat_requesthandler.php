<?php

/***

	* WordPress inbuild function used in this file
	* wp_remote_get, wp_remote_retrieve_body and wp_die
	* $wpdb - global variable used for database
*/

$cometchatIntegration = 'wordpress';

class CometChatInstaller {
/*
	CometChatInstaller  constructor
*/
	public $writablepath ;
	public $latest_v;
	public $cometchatPluginReferrer;
	public $integration;
	public $licensekey;
	public $target;
	public $token;
	public $download_link;
	public $cc_api_response;
	public $wpdb;
	public $accessKey = 'flGBNxeq8Mgu5bynUhS5w3S2CJ7dfo3latMTxDNa';

	function __construct($arguments = array()){
		$this->latest_v = !empty($arguments['latest_v']) ? $arguments['latest_v']: "";
		$this->integration = !empty($arguments['integration']) ? $arguments['integration']: "";
		$this->licensekey = !empty($arguments['licensekey']) ? $arguments['licensekey']: "";
		$this->token = !empty($arguments['token']) ? $arguments['token']: "";
		$this->target = !empty($arguments['target']) ? $arguments['target']: "";
		$this->download_link = !empty($arguments['download_link']) ? $arguments['download_link']: "";
		$this->cc_api_response = !empty($arguments['cc_api_response']) ? $arguments['cc_api_response']: "";
		$this->wpdb = !empty($arguments['wpdb']) ? $arguments['wpdb']: "";
		$this->basePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;
		ini_set('memory_limit', '-1');
	}

	/**
	 * checkCometChatLicenseKey: check for valid license key
	 * @param: license key
	 * @return json response
	*/
	public function checkCometChatLicenseKey(){
		try{
			$response = array();

			$url = "https://secure.cometchat.com/api-software/subscription?accessKey=".$this->accessKey;
			$url .= "&licenseKey=".$this->licensekey;

			/***WordPress remote call start ***/
			$data = wp_remote_get( $url );
	        $body = wp_remote_retrieve_body( $data );
	        /***WordPress remote call end ***/

			$licensedata = !empty($body) ? json_decode($body) : '';
			$response['cc_api_response'] = !empty($body) ? $body : '';

			/*** cms details start ***/
			if(empty($this->integration)){
				$cc_cms_file = (is_object($licensedata) && property_exists($licensedata, 'integration')) ? $licensedata->integration->file : 'standalone';
			}else{
				$cc_cms_file = $this->integration;
			}
			/*** cms details end ***/

			/*** cloud status start ***/
			$cc_cloud_active = (is_object($licensedata) && property_exists($licensedata, 'cloud') && !empty($licensedata->cloud)) ? 1 : 0 ;
			if($cc_cloud_active){
				setcookie('cc_license_key', $this->licensekey, time() + (60 * 5), "/"); // 300 = 5 min
				update_option('cc_license_key', $this->licensekey);
			}
			/*** cloud status end ***/

			/*** success response ***/
			if(!empty($licensedata) && is_object($licensedata) && property_exists($licensedata, 'success') && $licensedata->success == 1){
				$response['success'] = 1;
				$response['cloud'] = $licensedata->cloud;
			}else{
			/*** error response ***/
				$response['success'] = 0;
				$response['error'] = (is_object($licensedata) && property_exists($licensedata, 'error')) ? $licensedata->error: 'License not found';
			}
		} catch (Exception $e) {
        	$response['error'] = 1;
			$response['message'] = $e->getMessage();
    	}
		header('Content-Type: application/json');
		echo json_encode($response);
		wp_die();
	}

	/**
	 * isCometChatPackageAlreadyExists: checking available CometChat zip file
	 * @param: token key
	 * @return json object
	*/
	public function isCometChatPackageAlreadyExists(){
		$response = array();

		if(file_exists('cometchat.zip')){
			$response['zip'] = 1;
			$response['success'] = 1;
		}else{
			$response['success'] = 0;
			$response['message'] = 'Unable to find the zip file please download the CometChat cometchat.com and manually place it in in your website root directory';
		}
		$response['cc_api_response'] = (!empty($this->cc_api_response)) ? $this->cc_api_response : '';

		header('Content-Type: application/json');
		echo json_encode($response);
		wp_die();
	}

	/**
	 * updateCometChatTokenToDataBase: Updating token key in txt file
	 * @return json response
	*/
	public function updateCometChatTokenToDataBase(){
		$response = array('status'=>0,'message'=> 'Unable to get token and version.');
		$data = json_decode(file_get_contents('php://input'), true);
		if(!empty($data)){
			if(!empty($data['token'])){
				$response['status'] = 1;
				update_option('cc_token', $data['token']);
				$response['message'] = 'Success';
			}
		}
		header('Content-Type: application/json');
		echo json_encode($response);
		wp_die();
	}

	/**
	 * getCometChatTokenKey: Feching token key from respective server
	 * @param no param
	 * @return json response
	*/
	public function getCometChatTokenKey(){
		$response = array();
		try{
			$cc_api_response = !empty($this->cc_api_response) ? $this->cc_api_response: "";
			$this->licensekey = !empty($cc_api_response['license_key']) ? $cc_api_response['license_key'] :'';

			if(empty($this->integration)){
				$this->integration = (is_array($cc_api_response) && !empty($cc_api_response['integration']) && !empty($cc_api_response['integration']['file'])) ? $cc_api_response['integration']['file'] : 'standalone';
			}
			$cc_update_url = (is_array($cc_api_response) && !empty($cc_api_response['upgrade_link'])) ? $cc_api_response['upgrade_link'] : '';
			$cc_download_link = (is_array($cc_api_response) && !empty($cc_api_response['download_link'])) ? $cc_api_response['download_link'] : '';

			$protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
			$callback = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?action=updateCometChatTokenToDataBase';

			$searchArray = array("LICENSEKEY", "CALLBACKURL", "INTEGRATION","ONLYCORE");
			$replaceArray = array($this->licensekey, $callback, $this->integration, 1);

			$url = str_replace($searchArray, $replaceArray, $cc_update_url);
			$url .= "&noCallback=1";

			$args = array(
			    'timeout'     => 50000,
			    'redirection' => 5
			);

			$data = wp_remote_get( $url , $args );
	        $body = wp_remote_retrieve_body( $data );

			if (is_array($body) && array_key_exists("error",$body) && $body['error'] == 1) {
				$response['error'] = 1;
				$response['message'] = "Oops! We are unable to connect to our servers. You can create a support ticket and our team will be happy to assist you.";
			} else {
				$response = json_decode($body,true);
				$response['download_link'] = $cc_download_link;
			}
		} catch (Exception $e) {
        	$response['error'] = 1;
			$response['message'] = $e->getMessage();
    	}

		header('Content-Type: application/json');
		echo json_encode($response);
		wp_die();
	}

	/**
	 * downloadLatestCometChatPackage: Downloading latest CometChat zip from server
	 *@ param: token key
	 * @return json response
	*/
	public function downloadLatestCometChatPackage(){
		try {
			ini_set('max_execution_time', 3000);
			if(empty($this->token)){
				$response = array(
					'error' => 1,
					'message' => 'Cannot find the token, you can also create a support ticket and our team will be happy to assist you.'
				);
			} else {

				$download_link = (!empty($this->download_link)) ? $this->download_link : 'https://my.cometchat.com/software/get-download?token=TOKEN';

				$searchArray = array("TOKEN");
				$replaceArray = array($this->token);

				$url = str_replace($searchArray, $replaceArray, $download_link);
				$url .= "&onlyCore=1";

				$args = array(
				    'timeout'     => 50000,
				    'redirection' => 5
				);

				$data = wp_remote_get( $url , $args );
		        $body = wp_remote_retrieve_body( $data );

				$file = "cometchat.zip";

				if (is_array($body) &&  array_key_exists("error",$body) && $body['error'] == 1) {
					$response['error'] = 1;
					$response['message'] = 'Oops! We are unable to connect to our servers. Please manually download CometChat from the <a href="https://secure.cometchat.com" target="_blank">Client Area</a> and place it in the root folder of your website and try again. You can also create a support ticket and our team will be happy to assist you.';
				} else {
					file_put_contents($file, $body);
					if (filesize($file) > 0) {
						$response = array(
							'error'=>0,
							'message'=> 'CometChat zip downloaded succssfully'
						);
					} else {
						$response = array(
							'error' => 1,
							'message'=> 'Oops! We are unable to connect to our servers. Please manually download CometChat from the <a href="https://secure.cometchat.com" target="_blank">Client Area</a> and place it in the root folder of your website and try again. You can also create a support ticket and our team will be happy to assist you.'
						);
					}
				}
			}
		} catch (Exception $e) {
        	$response['error'] = 1;
			$response['message'] = $e->getMessage();
    	}
		header('Content-Type: application/json');
		echo json_encode($response);
		wp_die();
	}

	/**
	 * extractCometChatZip: Extract CometChat zip file
	 * @return json response
	*/
	public function extractCometChatZip() {
		try {
			ini_set('max_execution_time', 300);
			$path = 'cometchat.zip';
			$to = dirname(__FILE__);
			if (defined('ABSPATH')) {
				$to = ABSPATH;
			}
			if(file_exists($path)){
				if(class_exists('ZipArchive')){
					$zip = new ZipArchive();
					$x = $zip->open($path);
					if ($x === true) {
						$zip->extractTo($to);
						$zip->close();
						$response = array('error' => 0, 'message' => 'Zip file extracted successfully');
					}else{
						$response = array(
							'error' => 1,
							'message' => 'Oops! Something went wrong during the extraction process.  Please create a support ticket from the <a href="https://secure.cometchat.com" target="_blank">Client Area</a> and our team will be happy to assist you.'
						);
					}
				}else{
					include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'writable'.DIRECTORY_SEPARATOR.'pclzip.lib.php');
					$archive = new PclZip($path);
					if ($archive->extract(PCLZIP_OPT_PATH, $to) == 0) {
						$response = array(
							'error' => 1,
							'message' => 'Oops! Something went wrong during the extraction process.  Please create a support ticket from the <a href="https://secure.cometchat.com" target="_blank">Client Area</a> and our team will be happy to assist you.'
						);
					}else{
						$response = array(
							'error' => 0,
							'message' => 'Zip file extracted successfully'
						);
					}
				}
			} else {
				$response = array(
					'error' => 1,
					'message' => 'Please download the Latest CometChat package.'
				);
			}
		} catch (Exception $e) {
        	$response['error'] = 1;
			$response['message'] = $e->getMessage();
    	}
		header('Content-Type: application/json');
		echo json_encode($response);
		wp_die();
	}

	/**
	 * createDirectory: Is use to create new directory
	 * @params: name of directory
	*/
	private function createDirectory($directory){
	    if (!is_dir($directory)){
	        mkdir($directory,0777,true) ;
	    }
	}

}

function cleanCometChatRequests($Input){
    if (!is_array($Input)){
    	return str_replace('<','',str_replace('"','',str_replace("'",'',str_replace('>','',trim($Input)))));
    }
    return array_map('cleanCometChatRequests', $Input);
}

if(!empty($_REQUEST)){
	foreach($_REQUEST as $key => $val){
		$_REQUEST[$key] = cleanCometChatRequests($_REQUEST[$key]);
		if(!empty($_POST[$key])){
			$_POST[$key] = cleanCometChatRequests($_POST[$key]);
		}
		if(!empty($_GET[$key])){
			$_GET[$key] = cleanCometChatRequests($_GET[$key]);
		}
		if(!empty($_COOKIE[$key])){
			$_COOKIE[$key] = cleanCometChatRequests($_COOKIE[$key]);
		}
	}
}

if (!empty($_REQUEST['api']) && $_REQUEST['api'] == 'checkCometChatLicenseKey') {
	$licensekey = $_REQUEST['licensekey'];
	$update = new CometChatInstaller(array('licensekey'=>$licensekey, 'integration'=>$cometchatIntegration, 'wpdb'=>$wpdb));
	$update -> checkCometChatLicenseKey();
	wp_die();
}

if (!empty($_REQUEST['api']) && $_REQUEST['api'] == 'isCometChatPackageAlreadyExists') {
	$cc_api_response = $_REQUEST['cc_api_response'];
	$update = new CometChatInstaller(array('cc_api_response'=>$cc_api_response));
	$update -> isCometChatPackageAlreadyExists();
	wp_die();
}

if (!empty($_REQUEST['api']) && $_REQUEST['api'] == 'extractCometChatZip') {
	$update = new CometChatInstaller();
	$update -> extractCometChatZip();
	wp_die();
}

if (!empty($_REQUEST['api']) && $_REQUEST['api'] == 'getCometChatTokenKey') {
	$cc_api_response = $_REQUEST['cc_api_response'];
	$update = new CometChatInstaller(array('integration'=>$cometchatIntegration, 'cc_api_response'=>$cc_api_response, 'wpdb'=>$wpdb));
	$update -> getCometChatTokenKey();
	wp_die();
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'updateCometChatTokenToDataBase') {
	$update = new CometChatInstaller();
	$update -> updateCometChatTokenToDataBase();
	wp_die();
}

if (!empty($_REQUEST['api']) && $_REQUEST['api'] == 'downloadLatestCometChatPackage') {
	$token = $_REQUEST['token'];
	$download_link = $_REQUEST['download_link'];
	$update = new CometChatInstaller(array('token'=>$token, 'download_link'=>$download_link, 'wpdb'=>$wpdb));
	$update -> downloadLatestCometChatPackage();
	wp_die();
}
