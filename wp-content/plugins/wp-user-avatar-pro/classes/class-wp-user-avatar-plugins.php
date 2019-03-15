<?php 

class WP_User_Avatar_Plugins{
   
   private $avatar_options = array();
   
   public $avatar;

   public function __construct(){
	  
	 $this->avatar_options =  (array)WPUA_Avatar::avatar_settings();
	 $this->avatar = new WPUA_Avatar();
	 $this->trigger_hooks();
   }
   
   private function plugin_hooks(){
    
	 static $hooks = false;
	 
	 if( $hooks )
	 return $hooks;
	 
	 
	 return $hooks = array(
	   'woocommerce' => array(
	     'woocommerce_after_checkout_registration_form', // woocommerce checkout form on avatar
	     'woocommerce_edit_account_form',
		 'woocommerce_save_account_details'
	   ),
	   's2_members' => array(
	    'ws_plugin__s2member_during_profile_after_fields',
		'ws_plugin__s2member_after_handle_profile_modifications'
	   ) 
	 );
   }
   
   private function trigger_hooks(){

     if( $this->plugin_hooks() ){
	   
	   foreach( $this->plugin_hooks() as $slug => $actions ){
		 
		 if( $this->plugin_exists( $slug ) == false )
		 continue;  
		 
		 foreach( $actions as $action ){
			 
		    add_action( $action, array( $this, $action ) );	 
		 }
	     
	   }	 
	 }    	   
	   
   }
   
   private function plugin_exists( $slug ){  
      
	  $callback = apply_filters( 'wp_user_avatar_plugin_validate_callback', $slug.'_exist' );
	  if( function_exists($callback) ) return call_user_func( $callback );
	  else 
	  return ( method_exists( $this, $callback ) ) ? $this->$callback() : true;    
   }	
   
   public function enable_on_register(){
	  $options = $this->avatar_options;
	  $key = 'wp_user_avatar_upload_registration';
	  return ( isset( $options[$key] ) && $options[$key] == 1 ) ? true : false;  
   }
   
   public static function woocommerce_exist(){
	 return ( class_exists('woocommerce') ) ? true : false;  
   }
   
   
   public function woocommerce_after_checkout_registration_form(){
	    
		if( ! $this->enable_on_register() )
		return;
		
		$this->avatar->wpua_avatar_form_new();
		
		?>
         <style type="text/css">
           .wpua-edit-container{ display:none; }
         </style>
         <script type="text/javascript">
           jQuery('input[name="createaccount"]').on( 'change', function(e){
			  e.preventDefault();
			  if(  jQuery(this).prop('checked') == true )
			   jQuery('.wpua-edit-container').show();
			  else 
			   jQuery('.wpua-edit-container').hide();
		   }).change();
         </script>
        <?php 
  }
  
  public function woocommerce_edit_account_form(){
     global $current_user;
	 $this->avatar->wpua_avatar_html($current_user);	  
  }
  
  public function woocommerce_save_account_details( $user_id ){
	$this->wpua_avatar_save( $user_id );  
  }
  
  public function wpua_avatar_save( $user_id = 0 ){
	 if( $user_id < 1 )
	 $user_id = get_current_user_id();
	 $this->avatar->wpua_avatar_save( $user_id );  
  }
  
  public function ws_plugin__s2member_after_handle_profile_modifications(){
	$this->wpua_avatar_save();  
  }
  
  public function ws_plugin__s2member_during_profile_after_fields(){
     global $current_user;
	 ?>
     <tr>
	   <td valign="top"> <?php $this->avatar->wpua_avatar_html($current_user); ?> </td>	  
	 </tr>
     <?php  
  }
  
	
}

new WP_User_Avatar_Plugins();