<?php 
  error_reporting(E_ERROR);
  class Dropbox_API {
    
    public $client_id     = '';
    public $client_secret   = '';
    public $redirect_uri  = '';
    public $access_token  = '';
    public $refresh_token = '';
    public $authorize_url   = 'https://www.dropbox.com/oauth2/authorize';
    public $token_url   = 'https://api.dropboxapi.com/oauth2/token';
    public $api_url     = 'https://api.dropboxapi.com/2';
    public $upload_url    = 'https://content.dropboxapi.com/2';
    public $token_file = 'dropboxtoken.txt';
    public function __construct($options) {
      if(!empty($options['client_id']) && !empty($options['client_secret'])) {
        $this->client_id    = $options['client_id'];
        $this->client_secret  = $options['client_secret'];
        $this->redirect_uri   = $options['redirect_uri'];  
      } else if(!empty($options['access_token'])) {
        $this->access_token    = $options['access_token'];
      }else{
		  die ('Invalid CLIENT_ID or CLIENT_SECRET or ACCESS_TOKEN. Please provide CLIENT_ID, CLIENT_SECRET and ACCESS_TOKEN when creating an instance of the class.');
	  }

      //Set Here access token.

      if(isset($_GET['code']) and !$this->load_token()) {
        $token = $this->get_token($_GET['code'], true);
          if( $this->write_token($token, 'file') ) {
            $this->load_token();
          }
        } else if( !$this->load_token() ) {
              $this->get_code();
        }

    }
    
    /* First step for authentication [Gets the code] */
    public function get_authorize_url() {
        $url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
        return $url;
    }

    /* First step for authentication [Gets the code] */
    public function get_code() {
      if(array_key_exists('refresh_token', $_REQUEST)) {
        $this->refresh_token = $_REQUEST['refresh_token'];
      } else {
        // echo $url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
        $url = $this->authorize_url . '?' . http_build_query(array('response_type' => 'code', 'client_id' => $this->client_id, 'redirect_uri' => $this->redirect_uri));
        header('location: ' . $url);
        exit();
      }
    }
    
    /* Second step for authentication [Gets the access_token and the refresh_token] */
    public function get_token($code = '', $json = false) {
      $url = $this->token_url;
      if(!empty($this->refresh_token)){
        $params = array('grant_type' => 'refresh_token', 'refresh_token' => $this->refresh_token, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret);
      } else {
        $params = array('grant_type' => 'authorization_code', 'code' => $code, 'client_id' => $this->client_id, 'client_secret' => $this->client_secret,'redirect_uri' => $this->redirect_uri);
      }
      if($json){
        return $this->post($url, $params);
      } else {
        return json_decode($this->post($url, $params), true);
      }
    }
    
    /* Gets the current user details */
    public function get_user() {
      $url = $this->build_url('/users/me');
      return json_decode($this->get($url),true);
    }
    
    
    /* Get the list of items in the mentioned folder */
    public function get_folder_items($folder, $json = false) {
      $url = $this->build_url("/files/list_folder");
      $params = array('path' => $folder,'recursive' => true,'include_media_info' =>true);
      return json_decode($this->post($url, json_encode($params)), true);
  
    }
    
    /* Get the list of collaborators in the mentioned folder */
    public function get_folder_collaborators($folder, $json = false) {
      $url = $this->build_url("/folders/$folder/collaborations");
      if($json){  
        return $this->get($url);
      } else {
        return json_decode($this->get($url),true);
      }
    }
    
    /* Lists the folders in the mentioned folder */
    public function get_folders($folder) {
      $data = $this->get_folder_items($folder);
      return array_filter($data);
    }
    
    /* Lists the files in the mentioned folder */
    public function get_files($folder) {
    
      $data = $this->get_folder_items($folder);
     
      foreach($data['entries'] as $item){
        $array = '';
        if($item['type'] == 'file'){
          $array = $item;
        }
        $return[] = $array;
      }
      return array_filter($return);
    }
    
    /* Lists the files in the mentioned folder */
    public function get_links($folder) {
      $data = $this->get_folder_items($folder);
      foreach($data['entries'] as $item){
        $array = '';
        if($item['type'] == 'web_link'){
          $array = $item;
        }
        $return[] = $array;
      }
      return array_filter($return);
    }
    
    public function create_folder($name, $parent_id) {
      $url = $this->build_url("/folders");
      $params = array('name' => $name, 'parent' => array('id' => $parent_id));
      return json_decode($this->post($url, json_encode($params)), true);
    }
    
    /* Modifies the folder details as per the api */
    public function update_folder($folder, array $params) {
      $url = $this->build_url("/folders/$folder");
      return json_decode($this->put($url, $params), true);
    }
    
    /* Deletes a folder */
    public function delete_folder($folder, array $opts) {
      echo $url = $this->build_url("/folders/$folder", $opts);
      $return = json_decode($this->delete($url), true);
      if(empty($return)){
        return 'The folder has been deleted.';
      } else {
        return $return;
      }
    }
    
    /* Shares a folder */
    public function share_folder($folder, array $params) {
      $url = $this->build_url("/folders/$folder");
      return json_decode($this->put($url, $params), true);
    }
    
    /* Shares a file */
    public function share_file($file, array $params) {
      $url = $this->build_url("/files/$file");
      return json_decode($this->put($url, $params), true);
    }

    /* Get the details of the mentioned file */
    public function get_file_details($file, $json = false) {
      $url = $this->build_url("/files/$file");
      if($json){  
        return $this->get($url);
      } else {
        return json_decode($this->get($url),true);
      }
    }
    
    /* Uploads a file */
    public function put_file($filename, $name) {
      
     $url = $this->build_url('/files/upload', array(), $this->upload_url);

    $attrs=array(
    "path" => $name,
    "mode" => "add",
    "autorename" => true,
    "mute" => false
	);

     return json_decode($this->put($url,$filename, $attrs), true);

    }
    
    /**Create sharable link**/
    public function create_sharable_link($file_path){
		$url = $this->build_url('/sharing/create_shared_link');
		
		$params=array(
			"path" => $file_path,
		);
		return json_decode($this->post($url, json_encode($params)));
	}
    
    /* Modifies the file details as per the api */
    public function update_file($file, array $params) {
      $url = $this->build_url("/files/$file");
      return json_decode($this->put($url, $params), true);
    }

    /* Deletes a file */
    public function delete_file($file) {
      $url = $this->build_url("/files/$file");
      $return = json_decode($this->delete($url),true);
      if(empty($return)){
        return 'The file has been deleted.';
      } else {
        return $return;
      }
    }
    
    /* Saves the token */
    public function write_token($token, $type = 'file') {
      // Save token in DB
    }
    
    /* Reads the token */
    public function read_token($type = 'file', $json = false) {
      return $this->access_token;
    }
    
    /* Loads the token */
    public function load_token() {
    
    // Here get access token. 

      return true;
     
    }
    
    /* Builds the URL for the call */
    private function build_url($api_func, array $opts = array(), $url='') {
      $opts = $this->set_opts($opts);
      if(isset($url)&&!empty($url)){
        $base = $url . $api_func . '?';
      }else{
        $base = $this->api_url . $api_func . '?';
      }
      $query_string = http_build_query($opts);
      $base = $base . $query_string;
      return $base;
    }
    
    /* Sets the required before biulding the query */
    private function set_opts(array $opts) {
      if(!array_key_exists('authorization', $opts)) {
        $opts['authorization'] = "Bearer ".$this->access_token;
      }
      return $opts;
    }
    
    private function parse_result($res) {
      $xml = simplexml_load_string($res);
      $json = json_encode($xml);
      $array = json_decode($json,TRUE);
      return $array;
    }
    
    private static function expired($expires_in, $timestamp) {
      $ctimestamp = time();
      if(($ctimestamp - $timestamp) >= $expires_in){
        return true;
      } else {
        return false;
      }
    }
    
    private static function get($url) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
    }
    
    private static function post($url, $params) {
      $headers = array(
        'Content-Type: application/json; charset=utf-8'
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $data = curl_exec($ch);
      if(curl_errno($ch)) {
      echo 'Curl error: ' . curl_error($ch); 
      }
      curl_close($ch);
      return $data;
    }
    
    private static function put($url, $file, array $params = array()) {
      $headers = array(
        'Content-Type: application/octet-stream',
	'Dropbox-API-Arg: '. json_encode($params)
      );

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      
	  $fp = fopen($file, 'rb');
	  $filesize = filesize($file);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fp, $filesize));
      
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $data = curl_exec($ch);

      if(curl_errno($ch)) {
      echo 'Curl error: ' . curl_error($ch); 
      }
      curl_close($ch);
      fclose($fp);
      return $data;
    }
    
    private static function delete($url, $params = '') {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
    }
  }
