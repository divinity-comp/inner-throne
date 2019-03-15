<?php
/*
 * Product Customizer Class
 * @author Flipper Code<hello@flippercode.com>
 * @version 1.0.0
 * @package Core
 * Author URL : http://www.flippercode.com/
****/

if ( ! class_exists( 'Flippercode_Product_Customizer' ) ) {

	/**
	** Class Vars
	**/
	class Flippercode_Product_Customizer {
		
		
		private $product;
		private $productTextDomain;
		private $productFilerefrence;
		private $productCustomiserRefrence;
		private $productDirectory;
		private $productDirectoryPath;
		private $productURL;
		private $corePath;
		private $templatePath;
		private $productTemplate;
		private $templateType;
		private $productCustomiserScript;
		private $isAuthenticated = false;
		private $isAjax;
		private $optionName;
		private $dbsettings;
		private $productStyles;
		private $productScripts;
		private $localizedScript;
		private $customiserFormElements = array();
		private $availableProductTemplates = array();
		private $currentSettings;
		private $currentBasicSettings;
		private $currentTemplateBackups;
		private $dboption;
		public  $placeholders = array();
		private $defaults = array();
		private $finalMarkup;
		private $frontendRequest = false;
		private $customiserModals;
		private $instance;
		private $basicCustomiserArgs;
		private $googleFonts = array();
		private $fontStyle = array();
		private $fontWeight = array();
		public $productCustomiserInfo;
		
	    function __construct( $productCustomiserInfo ) {
			
			$this->productCustomiserInfo = $productCustomiserInfo;
			$this->register_customizer_hooks();
			$this->load_customizer_resources(); //Optmisation Required Here Too.
				
		}
		
		function register_customizer_hooks() {
			
			if(is_admin()) {
				add_action( 'admin_init',array( $this, 'customizer_screen_verfification' ) );
				add_action( 'wp_ajax_copy_from_preformatted_template',array( $this, 'copy_from_preformatted_template' ) );
				add_action( 'wp_ajax_load_template_backup',array( $this, 'load_template_backup' ) );
				add_action( 'wp_ajax_remove_template_backup',array( $this, 'remove_template_backup' ) );
				add_action( 'admin_enqueue_scripts', array($this,'load_product_customizer_resources') );
				add_action( 'admin_menu', array($this,'register_flippercode_product_customiser') );
				add_action('admin_head',array($this,'backend_hook_in_head'));
				add_action('admin_footer',array($this,'admin_hook_in_footer'));
				
			}
			add_action('wp_head',array($this,'frontend_hook_in_head'));
			add_action('init',array($this,'hook_at_init'));
			add_action( 'wp_enqueue_scripts', array($this,'load_currently_activated_plugins_templates_styles') );
			
		}
		
		function customizer_screen_verfification() {
			
			$this->isAuthenticated = ( $this->is_fc_customizer_valid_request() or isset($this->productCustomiserInfo['frontendRequest']) );
			if( $this->isAuthenticated )
			$this->_init_customizer();
			
		}
		
		function remove_template_formatting($data) {
			
			$product = $data['product'];
			$template = $data['template'];
			$currentstyles = get_option($product.'-fc-styles');
			unset($currentstyles[$template]);
			update_option($product.'-fc-styles',$currentstyles);
							
		}
		
		function reset_default_templates($product) {
			
			$data = get_option($product);
			$currentDefaultTemplates = $data['default_templates'];
			$realDefaultTemplates = $data['real_default_templates'];
			unset($data['default_templates']);
			$newDefault = array('default_templates' => $realDefaultTemplates );
			$data = wp_parse_args($newDefault,$data);
			update_option($product,$data);
			
		}
		
		function remove_all_template_formatting($data) {
			
			$product = $data['product'];
			delete_option($product.'-fc-styles');
			$this->reset_default_templates($product);
							
		}
		
		function set_default_template() {
		
		$response = array();
		$optionName = $_POST['product'];
		$data = get_option($optionName);
		if(!is_array($data))
		$data = unserialize($data);
		$templates = $data['default_templates'];
		unset($data['default_templates']);
		$templates[$_POST['templatetype']] = $_POST['template'];
		$data['default_templates'] = $templates;
		//echo '<pre>final'; print_r($data); 
		update_option($optionName,$data);
		return $data;
		
		}
		
		function get_the_CSS($currentTemplate,$customiserOptionName) { 
			
			
			$productcustomiserdata = get_option($customiserOptionName);
			
			if(isset($productcustomiserdata[$currentTemplate])) {
				$originalelements =  $productcustomiserdata[$currentTemplate]['originalelements'];
				$realformelements =  $productcustomiserdata[$currentTemplate]['formdata'];
				$templatestyle =  $this->generate_css( '',$originalelements, $realformelements,true);
				$css = '<style>'.$templatestyle.'</style>';
				echo $css;
			}
			
		}
		
		function generate_css($prefix,$originalelements,$realformelements,$isImportant) {
			
			$prefixspecificstyle = '';
			
			$final = array();
			
			$important = ( $isImportant ) ? '!important' : '';
			
			if( is_array($originalelements) ) {
				
					foreach($originalelements as $element) {
				
				foreach ($realformelements as $key => $value) { 
					
						if (strpos($key, $element) === 0) {
							
							$property = explode('*',$key);
							$final[$element][$property[1]] = $value;
						}
					}

					
				}
				
				foreach($final as $selector => $cssInfo) {
					
						$prefixspecificstyle .= $prefix.' .'.$selector.'{ ';
						foreach($cssInfo as $cssproperty => $csspropertyvalue) {
							
							$unit = $this->get_css_property_unit($cssproperty);
							$unit = '';
							$prefixspecificstyle .= $cssproperty.' : '.$csspropertyvalue.$unit.$important.'; ';
							
						}
						$prefixspecificstyle .= ' }';
				
				}
			
			}
			
			return $prefixspecificstyle;
			
		}
		
		function get_template_backup_css( $prefix,$layoutid,$productcustomiserdata,$backupTime ) {
			
			$backups = $productcustomiserdata[$layoutid]['templatebackup'];
			
			$position = '';
			
			foreach($backups as $key => $backup) {
				
				if( $backup['backuptime'] == $backupTime ) {
					 $position = $key;
					 break;
				}
			}
			
			$originalelements =  $productcustomiserdata[$layoutid]['templatebackup'][$key]['templateInfo']['originalelements'];
			
			$realformelements =  $productcustomiserdata[$layoutid]['templatebackup'][$key]['templateInfo']['formdata'];
			
			$backupStyle =  $this->generate_css( $prefix, $originalelements, $realformelements,false);
			
			return $backupStyle;
			
		}
		
		function give_css_with_prefix($prefix,$layoutid,$productcustomiserdata) {
			
			if(isset($productcustomiserdata[$layoutid]['originalelements'])) {
				$originalelements =  $productcustomiserdata[$layoutid]['originalelements'];
				$realformelements =  $productcustomiserdata[$layoutid]['formdata'];
				$prefixspecificstyle =  $this->generate_css( $prefix, $originalelements, $realformelements,false);
				
			}
			if(!empty($prefixspecificstyle))
			return $prefixspecificstyle;
			else
			return '';
						
		}
		
		function delete_custom_template() {
			
			$dbdata = get_option($_POST['product']);
			if(!is_array($dbdata))
			$dbdata = unserialize($dbdata);
			$optionName = wp_unslash($_POST['product']);
			$templateToRemove = wp_unslash($_POST['templateName']);
			$templateType =  wp_unslash($_POST['templatetype']);
			$data = get_option($optionName.'-fc-styles');
			if(array_key_exists($templateToRemove,$data)) {
				unset($data[$templateToRemove]);
				update_option($optionName.'-fc-styles',$data);
			}
			if($dbdata['default_templates'][$templateType] == $templateToRemove) {}
			return $data;
			
		}
		
		function get_flippercode_activated_products() {
			
			
			global $wpdb;
			$query = "SELECT * FROM {$wpdb->prefix}options WHERE `option_name` LIKE '%-fc-styles%'";
			$options = $wpdb->get_results($query);
			$plugins = get_option('active_plugins');
			$fcActivatedProducts = array();
			
				foreach($options as $option) { 
					
					$supposedPlugin = explode('-fc-styles',$option->option_name);
					foreach($plugins as $activated) {
						 if( strpos($activated, $supposedPlugin[0]) !== false  ){
							$fcActivatedProducts[] = $supposedPlugin[0];
						 }   
					}	
			}
			
			return $fcActivatedProducts;
				
		}
		
		function load_currently_activated_plugins_templates_styles() { 
			
			$fcActivatedProducts = $this->get_flippercode_activated_products();	
			//echo '<pre>'; print_r($fcActivatedProducts); exit;
			foreach($fcActivatedProducts as $optionName) {
				
				$data = get_option($optionName);
				if(!is_array($data))
				$data = unserialize($data);
				$styleData = get_option($optionName.'-fc-styles');
				
				if($data) {
					 
					foreach($data['default_templates'] as $type => $template) {
					   
					   $parent = $template;
					   $templateType = $type;
					   $core_dir_path = plugin_dir_path(dirname(__FILE__));
					   $core_dir_url = plugin_dir_url(dirname(__FILE__));
					   $cssPath = $core_dir_url.'templates/'.$templateType.'/'.$parent.'/'.$parent.'.css';
					   if(file_exists($cssPath)) {
						   
						   $cssUrl = $core_dir_url.'templates/'.$templateType.'/'.$parent.'/'.$parent.'.css';
						   
					   }
					   else {
							if(isset($styleData[$template]['parentTemplate'])) {
								$parent = $styleData[$template]['parentTemplate'];
								$templateType = $type;
								$cssUrl = $core_dir_url.'templates/'.$templateType.'/'.$parent.'/'.$parent.'.css';
							}
					   }
					   
					   wp_enqueue_style( $parent.'-main-style', $cssUrl );
						
					}
				}
				
			}	
			
		}

		function get_template_markup() {
						
			 $filePath =  plugin_dir_path( __DIR__ ).'templates/'.$_POST['templatetype'].'/'.$_POST['template'].'/'.$_POST['template'].'.html';
			 
			 if(file_exists( $filePath )) {
			   $response = array('template_markup' => nl2br(file_get_contents( $filePath ) ) ) ;
			 }
			 else {
				 // GET Template From DB
				 $response = '';
			 }
			 
			 return $response;
			 
			
		}
		
		function process_data($formData) {
		
		
				$_POST = $formData;
				$data = array();
			
				$originalelements = $_POST['originalelements'];
				
				$gf = array();
				$final = $realformelements = array();
				
				foreach ($_POST as $key => $value) { 
						
					if (strpos($key, '*') !== FALSE and !empty($value))	{
					   $realformelements[ $key ] = $value;
					}
					if(strpos($key, 'font-family') !== FALSE and !empty($value)){
						$gf[] = $value;
					}
						
				}
					
				foreach($originalelements as $element) {
					
					foreach ($_POST as $key => $value) { 
						
						if(empty($value))
						continue; // No need to save blank css property name without value.
						
						if (strpos($key, $element) === 0) {
							$property = explode('*',$key);
							$final[$element][$property[1]] = $value;
						}
					}

					
				}
				
				$style = '';
				
				foreach($final as $selector => $cssInfo) {
					
					$style .= '.'.$selector.'{ ';
					foreach($cssInfo as $cssproperty => $csspropertyvalue) {
						
						$unit = $this->get_css_property_unit($cssproperty);
						$style .= $cssproperty.' : '.$csspropertyvalue.$unit.';';
						
					}
					$style .= ' }';
					
				}
				
				$postedData = array( 'templateMarkup' => $_POST['templateMarkup'],
									 'style' => $style ,
									 'formdata' => $realformelements,
									 'googlefonts' => $gf,
									 'originalelements' => $originalelements,
									 'parentTemplate' => $_POST['parentTemplate'],
									// 'required_base_css' => $dependencyCSS,
									 'instance' => $_POST['instance'],
									 'templateType' => $_POST['templatetype'],
									 'templateName' => $_POST['template_name']
									  );
				 
			 $parentCSSFile =  plugin_dir_path( __DIR__ ).'templates/'.$_POST['templatetype'].'/'.$_POST['productTemplate'].'/'.$_POST['productTemplate'].'.css';
									  
			 if(isset($_POST['template_name']) and !empty($_POST['template_name']))	
			 $data = $postedData;
			 
			 return $data;
		}
		
		function key_exists_recursive($keys, $array)
		{
			if(!is_array($keys) or !is_array($array))
			{
				return false;
			}

			if(count($keys) > 1)
			{
				return $this->key_exists_recursive(array_slice($keys, 1), $array[$keys[0]]);
			}

			return isset($array[$keys[0]]);
		}
		
		function set_up_class( $data ) {
			
			$product = $data['product'];
			$this->optionName = $this->dboption = $data['dboption'];
			$this->dbsettings = get_option($product.'-fc-styles');
			$this->dboption = get_option($this->dboption);
			if(!is_array($this->dboption))
			$this->dboption = unserialize(get_option($this->dboption));
			$this->productTemplate = $data['productTemplate'];
			$this->productDirectory = $data['productDirectory'];
			
			
			$template = (!empty($this->productTemplate)) ? $this->productTemplate : $this->productCustomiserInfo['productTemplate'];
		
			if(isset($template) and !empty($this->dbsettings[$template])) {
			   
			   $current = $this->currentSettings = $this->dbsettings[$template];
			   unset($current['templatebackup']); //To get only settings current settings excliding backups.
			   $this->currentBasicSettings = $current;
			   //$this->currentTemplateBackups = $this->currentSettings['templatebackup'];			
			}

			
		}
		
		function save_customiser_updations ( $usercustomisation ) {
			
			$templateSlug = $this->create_url_slug( $_POST['template_name'] );
			$newTemplate = array( $templateSlug => $usercustomisation );
			$existingTemplates =  get_option($this->productDirectory.'-fc-styles');
			$mergedTemplates = wp_parse_args($existingTemplates,$newTemplate);
			update_option( $this->productDirectory.'-fc-styles' , $mergedTemplates );
			
			if(array_key_exists('editable',$_POST) ) {
			
				$editable = $_POST['editable'];
				$dbdata = $this->dboption;
				foreach($editable as $key => $value) { 
					//Change only those values which are edited by admin
					$dbdata[$key] = $value;
				}
				
				update_option( $this->optionName, serialize( wp_unslash( $dbdata ) ) );
			}
					
		}
		
		function create_url_slug($string) {
			
				$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
				return $slug;
		}

		function save_new_template() {
			
			if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				
				$formdata = wp_parse_args($_POST['formdata']);
				$originalelements = array('originalelements' => $_POST['originalelements']);
		$formMarkup = array('templateMarkup' => htmlentities(stripslashes($_POST['templateMarkup'])) );
				$_POST = wp_parse_args($formdata,$originalelements);
				$_POST = wp_parse_args($_POST,$formMarkup);
				
				$this->set_up_class($_POST);
				$usercustomisation = $this->process_data($_POST);
				$this->save_customiser_updations($usercustomisation);	 
				return $_POST; 
				
			}
			
		}
		
		function hook_at_init() {
			
			if(!$this->isAjax) { //Right Place To Handle Non Ajax Form Submissions.
				
				if( !empty($_POST)  ) {
					
					//Save Customiser Form Updations
					if(isset($_POST['nonce']) and wp_verify_nonce( $_POST['nonce'], 'my-nonce' ) ) {
						$this->set_up_class($_POST);
						$usercustomisation = $this->process_data();
						$this->save_customiser_updations($usercustomisation);	
					}
						
				}
				
			}
			
		}
		
		function frontend_hook_in_head() {
			
			$googleFonts = array();
			
			$fcActivatedProducts = $this->get_flippercode_activated_products();
			
			foreach($fcActivatedProducts as $option) { //May be Different Google Fonts in diff products
				
				$optionName = $option.'-fc-styles';
				$productcustomiserdata = get_option($optionName);
				
				if($productcustomiserdata) {
					foreach($productcustomiserdata as $templateID => $templateDetails) { 
						// Diff gf in diff template
						if(isset($templateDetails['googlefonts'])) {
							$googleFonts = wp_parse_args($templateDetails['googlefonts'], $googleFonts);
						}
					}
				}
				
	//Load Customiser's Custom Css For Every Plugin And For Its Every Default Template In Frontend Header.
	
				$data = get_option($option);
				if(!is_array($data))
				$data = unserialize($data);
				
				if(isset($data['default_templates'])) {
					foreach($data['default_templates'] as $key => $template ) {
						
						$this->get_the_CSS($template,$optionName);
						
					}
			   }
				
					
			}
			
	//Load All Google Fonts In Header Of All Activated Plugin Just A Single Time.
	
			$this->load_google_fonts_in_header($googleFonts);
			
			
		}
		
		function load_google_fonts_in_header($googleFonts) {
			
			
			if(isset($googleFonts) and count($googleFonts) > 0) {
				
				if(count($googleFonts) > 1)
				$googleFonts = join('|',$googleFonts);
				else
				$googleFonts = $googleFonts[0];

			}
			
			$googleFonts = str_replace('||','|',$googleFonts);
			
			if(!empty($googleFonts)) { ?>
			   <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=<?php echo $googleFonts; ?>'>
			<?php }
			?>
			
			<?php
		}
		
		function admin_hook_in_footer() {
		?>
		
		 <div style="z-index:99999999; display:none;" class="modal fade custom-modal" id="remove-current-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="tktemplatebckup">Delete Template</h4>
						</div>
						<div class="modal-body">
						   <p>You are about to delete this template. Do you really want to proceed ?</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<a class="btn btn-success btn-ok yes-remove-current-template">Delete Template</a>
						</div>
					</div>
				</div>
			</div>
		 
		<?php	
			
		}
		
		function backend_hook_in_head(){
			
			if($this->isAuthenticated) {   
				  
				   $currentTemplatestyle = $otherTemplatestyle = $templateBackupStyles = ''; 
				   $googleFonts = array();
				   $product = $_GET['product'];
				   $productTemplate = $_GET['productTemplate'];
				   $productcustomiserdata = get_option( $product.'-fc-styles' );
				   
				   if($productcustomiserdata) {
					
					   foreach($productcustomiserdata as $templateID => $templateDetails) { 
							
							if(isset($templateDetails['googlefonts'])) {
							   $googleFonts = wp_parse_args($templateDetails['googlefonts'], $googleFonts);
							}
					   }
					  
					   // Load Google Font 
					   $this->load_google_fonts_in_header($googleFonts);
						
					   // Altered Css with added prefix for current layout preview 
					   
					   $currentTemplatestyle = $this->give_css_with_prefix('.product-template-preview',$_GET['productTemplate'], $productcustomiserdata );
					   
					   if(isset($_GET['backuptime']) and !empty($_GET['backuptime'])) {
						   
						  $backupTime = $_GET['backuptime']; 
						  $currentTemplatestyle = $this->get_template_backup_css('.product-template-preview',$_GET['productTemplate'], $productcustomiserdata,$backupTime );
					   }
					   
				   }
				   
				   echo '<style class="dynamic_style">'.$currentTemplatestyle.$otherTemplatestyle.$templateBackupStyles.'</style>';
				   ?>
				   
				   <style>
					.se-pre-con {
						display:block;
						position: fixed;
						left: 0px;
						top: 0px;
						width: 100%;
						height: 100%;
						z-index: 99999999;
						background: url(<?php echo FCCI.'Preloader_3.gif'; ?>) center no-repeat #fff; 
					}
				   </style>
				   <?php  
			   }
		}
		
		static function get_padding_fields($info) {
		
		if(!isset($info['name']))
		return '';
		
		$controlName = $info['name'].'-padding';
		ob_start();
		?>
		
		<div class="form-group padding-margin-fields">	 
			 <!-- Rounded switch -->
			<span style="font-weight: bold;">&nbsp;Manage Padding </span>
			<label class="switch">
			  <input type="checkbox" class="show-padding-controls" name="<?php echo "fcc-managepadding".$controlName; ?>" id=<?php echo "fcc-managepadding".$controlName; ?>>
			  <div class="slider round"></div>
			</label>
			</div>
			
			<div class="all-padding-controls" style="display:none;">
			 <?php
			 
			$markup = '';
			
			$groupproperty = array('top','right','bottom','left'); 
		   foreach($groupproperty as $key => $individualproperty) {
			
			 $particularName =  $controlName.$individualproperty;
			// echo $controlName.'here'.$particularName.'<br>';
			 //$controlName = $controlId = $particularName;
			 $newlabel = 'Padding - '. ucfirst($individualproperty);
			 $newproperty = 'padding'.'-'.$individualproperty;
			 
			 $markup .= '<div class="form-group">
			  <label for="'.$particularName.'" style="width:100%;">'.$newlabel.'</label>
			  <div class="range">
				<input type="range" min="0" max="100" step = "1" id = "'.$particularName.'" name="'.$particularName.'" value="" class="fc-cc-cp form-control" data-event="range-change" data-property="'.$newproperty.'">
				<output id="'.$particularName.'-range"></output>
			  </div>
			 </div>'; 
		   } //$values[$key].
		   echo $markup;
		   
		   $controlName = $info['name'].'-margin';
		   ?>
		   
		   </div>
		   <div class="form-group padding-margin-fields">
		   <span style="font-weight: bold;">&nbsp;Manage Margin </span><label class="switch">
			  <input type="checkbox" class="show-margin-controls" name="<?php echo "fcc-managemargin".$controlName; ?>" id="<?php echo "fcc-managemargin".$controlName; ?>">
			  <div class="slider round"></div>
			</label>
			</div>
			
			
			<div class="all-margin-controls" style="display:none;">
		   <?php
		   
		   $markup = '';
			
				$groupproperty = array('top','right','bottom','left'); 
		   foreach($groupproperty as $key => $individualproperty) {
			
			 //$controlName = 'fcc-margin';
			// $controlName = $controlId = $controlName.$individualproperty;
			
			 $particularName =  $controlName.$individualproperty;
			 
			// echo $controlName.'here'.$particularName.'<br>';
			 
			 $newlabel = 'Margin - '. ucfirst($individualproperty);
			 $newproperty = 'margin'.'-'.$individualproperty;
			 
			 $markup .= '<div class="form-group">
			  <label for="'.$particularName.'" style="width:100%;">'.$newlabel.'</label>
			  <div class="range">
				<input type="range" min="0" max="100" step = "1" id = "'.$particularName.'" name="'.$particularName.'" value="" class="fc-cc-cp form-control" data-event="range-change" data-event="range-change" data-property="'.$newproperty.'">
				<output id="'.$particularName.'-range"></output>
			  </div>
			 </div>'; 
		   } //$values[$key].
		   echo $markup;
	
			 ?>	
			 </div>
			 <?php
			$paddingField = ob_get_contents();
			ob_clean();
			return $paddingField;
			
		  }
		  
		public function product_ui_customiser() {
			
		    ?>
			<div class="se-pre-con"></div>
			<div class="fc-product-customiser-wrapper">
				<div class="row">
					<div class="col-md-3 render_customiser_form">
						<div class="product-description" style="display:none;">
						  <img width="100px" src="<?php echo FCCI.'wp-poet.png'; ?>">
						  <div class="fpc-heading">Customise Templates</div>
						</div>
						<div class="product-customizer-form">
							    <form class="fc-product-customisation" name="fc-product-customisation" id="fc-product-customisation" method="post" action = "">
								<input type="hidden" name="product" id="product" value="<?php echo $_GET['product']; ?>">
								<input type="hidden" name="productTemplate" id="productTemplate" value="<?php echo $_GET['productTemplate']; ?>">
								<input type="hidden" name="instance" id="instance" value="<?php echo $_GET['instance']; ?>">
								<input type="hidden" name="templatetype" id="templatetype" value="<?php echo $_GET['templatetype']; ?>">
								<?php
								$parentTemplate = (!isset($_GET['parentTemplate'])) ? $_GET['productTemplate'] : $_GET['parentTemplate'];
								?>
								<input type="hidden" name="parentTemplate" id="parentTemplate" value="<?php echo $parentTemplate; ?>">
								<input type="hidden" name="dboption" id="dboption" value="<?php echo $_GET['product']; ?>">
								<input type="hidden" name="productDirectory" id="productDirectory" value="<?php echo $_GET['product']; ?>">
								<input type="hidden" name="ajaxurl" id="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
								<input type="hidden" name="nonce" id="nonce" value ="<?php echo wp_create_nonce( 'my-nonce' ); ?>">
							</form>
		
							<form class="fc-product-customisation-dynamic-form" name="fc-product-customisation-dynamic-form" id="fc-product-customisation-dynamic-form" method="post" action="">
							<div class="currentelement"></div>	
							<div class="fc-bg-elements" style="display:none;">
								<div class="form-group">
									 <label for="fcc-bgcolor" style="width:100%;">Color</label>
										<input type="text" id = "fcc-color" name="fcc-color" value="" class="fc-cc-cp color-field form-control" data-property="color">
								</div>	
								<div class="form-group">
									 <label for="fcc-bgcolor" style="width:100%;">Background - Color</label>
										<input type="text" id = "fcc-bgcolor" name="fcc-bgcolor" value="" class="fc-cc-cp color-field form-control" data-property="background-color">
								 </div>
							</div>
							<div class="fc-bg-txt-elements" style="display:none;">
								<div class="form-group">
									 <label for="fcc-bgcolor" style="width:100%;">Set Google Fonts</label>
										<select name="fcc-google-fonts" id="fcc-google-fonts" data-event='select-change' data-property="font-family">
											<option value="">Please Select Font</option>
											<option value="ABeeZee">ABeeZee</option>
											<option value="Abel">Abel</option>
											<option value="Abril Fatface">Abril Fatface</option>
											<option value="Aclonica">Aclonica</option>
											<option value="Acme">Acme</option>
										</select>
								</div>	
								<div class="form-group">
								  <label for="fcc-fontsize" style="width:100%;">Font Size</label>
								  <div class="range">
									<input type="range" step="1" min="1" max="200" id = "fcc-fontsize" name="fcc-fontsize" id="fcc-fontsize" value="0" class="fc-cc-cp form-control" data-event="range-change" data-property="font-size">
									<output id="fcc-fontsize-range">0</output>
								  </div>
								</div>
								<div class="form-group">
									 <label for="fcc-bg-txt-color" style="width:100%;">Color</label>
										<input type="text" id = "fcc-bg-txt-color" name="fcc-bg-txt-color" value="" class="fc-cc-cp color-field form-control" data-property="color">
								</div>	
								<div class="form-group">
									 <label for="fcc-bg-txt-bgcolor" style="width:100%;">Background - Color</label>
										<input type="text" id = "fcc-bg-txt-bgcolor" name="fcc-bg-txt-bgcolor" value="" class="fc-cc-cp color-field form-control" data-property="background-color">
								 </div>	
								 <!-- Here -->
							</div>
							 <?php
								 // Here 
								 $data = array('name' => 'fcc-txt');
								 echo self::get_padding_fields( $data ); ?>
							</form>
						</div>
						
					</div>
					<div class="col-md-9 render_customiser_preiview">
					  <div class="row fc-customizer-actions">
							 <div class="col-md-12">
				<?php
				//$css = get_option('wp-age-gate-pro-fc-styles');
				//echo '<pre>'; print_r($css); exit;
				$dboptions = get_option($_GET['product'].'-fc-styles');
				$customTemplates = false;
				if(!empty($dboptions)) {
					$customTemplates = true;
				}
				
				?>				 
				
				<button type="button"  style="float:left"; name="save_customiser" id="save_customiser" class="save_customiser bluebg likebtn" value="save_current_settings" data-settingpage = "<?php echo $_GET['settingPage']; ?>">Save Template <i></i></button>
				
				<button data-toggle="modal" data-target="#update-template-content" type="button" style="float:left"; name="create_new_template_btn" id="create_new_template_btn" class="update-template-content-btn-action bluebg likebtn" data-product = <?php echo $_GET['product']; ?> data-template="<?php echo $_GET['productTemplate']; ?>" data-templatetype = <?php echo $_GET['templatetype']; ?>>Update Template Content<i></i></button>
				<?php
				if($customTemplates) { ?>
				
				<button data-toggle="modal" data-target="#remove-current-formatting" type="button" style="float:left"; name="remove_template_formatting" id="remove_template_formatting" class="likebtn" data-product = <?php echo $_GET['product']; ?> data-template="<?php echo $_GET['productTemplate']; ?>" >Clear Current Template Formatting<i></i></button>
				
				<?php } ?>
				
				<?php
				if($customTemplates) { ?>
				
				<button data-toggle="modal" data-target="#remove-all-formatting" type="button" style="float:left"; name="remove_all_template_formatting" id="remove_all_template_formatting" class="likebtn" data-product = <?php echo $_GET['product']; ?> data-settingpage = "<?php echo $_GET['settingPage']; ?>" data-template="<?php echo $_GET['productTemplate']; ?>" >Delete All Templates<i></i></button> 
				
				<?php }
				?>
								
							</div>
						</div>
						<?php 
						
						
						$this->render_customiser_preview();
						
						 ?>		
					</div>
			 
			 
			 
			 </div>
			 
			<?php
				
		}
		
		public function register_flippercode_product_customiser() {
				
				add_submenu_page( 
				 NULL,
				'Product UI Customiser',
				'Product UI Customiser',
				'manage_options',
				'fpc',
				array($this,'product_ui_customiser') );
		}
		
		function load_product_customizer_resources($hook) {
			
			
			//
			//dashboard_page_flippercode-product-customiser // admin_page_flippercode-product-customiser
			if( $hook == 'admin_page_fpc') {
					
					//Customiser Screen	
					wp_enqueue_style( 'wp-color-picker' ); 
					//echo '<pre>'; print_r($this); exit;
					wp_enqueue_style( 'bootstrap_flat_style', $this->corePath. '/css/bootstrap.min.flat.css' );
					//wp_enqueue_style( 'fc-ui', $this->corePath. '/css/.css' );
					wp_enqueue_style( 'fc-product-customiser', $this->corePath . '/css/flippercode_customiser.css' );
					$customise['ajaxurl'] = admin_url('admin-ajax.php');
					$customise['nonce'] = wp_create_nonce( 'customiser-nonce' );
					$customise['backendURL'] = admin_url();
					wp_enqueue_script( 'custom-script-handle', $this->corePath . 'js/customiser.js', array( 'wp-color-picker' ), false, true ); 
					wp_localize_script( 'custom-script-handle', 'fc_customiser', $customise );
					$parent = (!empty($_GET['parentTemplate'])) ? $_GET['parentTemplate'] : $_GET['productTemplate'];
			   
				    $templateType = $_GET['templatetype'];
				   
				    $cssPath = plugin_dir_path( __DIR__ ).'templates/'.$templateType.'/'.$parent.'/'.$parent.'.css';
				    if(file_exists($cssPath)) {
					   
					   $cssUrl = plugin_dir_url( __DIR__ ).'templates/'.$templateType.'/'.$parent.'/'.$parent.'.css';
				    }
				    else {
					   
						$parent = $styleData[$template]['parentTemplate'];
						$templateType = $type;
						$cssUrl = plugin_dir_url( __DIR__ ).'templates/'.$templateType.'/'.$parent.'/'.$parent.'.css';
					   
				    }
				    wp_enqueue_style( $parent.'-current-template-base-style', $cssUrl );
				    wp_enqueue_script( 'bootstrap_script', $this->corePath . 'js/bootstrap.min.js' );
			   
			}
			
			//if(isset($_GET['fpc']) and $_GET['fpc']='true')  {
					 
			   
				   
			//}
					
				
		}
		
		
		public function remove_template_backup() {
			
			$product = $_POST['product'];
			$productTemplate = $_POST['template'];
			$productBackup = $_POST['templatebackup'];
			$customiserData = get_option($product.'-fc-styles');
			$originalTemplateData = $customiserData[$productTemplate];
			$completebackup = $backups = $originalTemplateData['templatebackup'];
			unset($originalTemplateData['templatebackup']);
			$previousCurrentTemplateData = $originalTemplateData;
			
			$position = '';
			foreach($backups as $key => $backup) {
				
				if( $backup['backuptime'] == $productBackup ) {
					$position = $key;
					break;
				}
				
			}
			
			unset($completebackup[$position]);
			$remainingBackups = array( 'templatebackup' => $completebackup );
			$finalDataCurrentTemplate = wp_parse_args($remainingBackups,$previousCurrentTemplateData);
			$customiserData[$productTemplate] = $finalDataCurrentTemplate;
			update_option($product.'-fc-styles',$customiserData);
			echo json_encode(array('status' => 'success','updated' => $customiserData,'removed' => $productBackup));
			exit;
			
		}
		
		public function load_template_backup() {
			
			$product = $_POST['product'];
			$dboption = $_POST['dboption'];
			$productTemplate = $_POST['template'];
			$productBackup = $_POST['templatebackup'];
			$customiserData = get_option($dboption.'-fc-styles');
			$originalTemplateData = $customiserData[$productTemplate];
			
			$backupData = array();
			$completebackup = $backups = $customiserData[$productTemplate]['templatebackup'];
			
			$position = '';
			foreach($backups as $key => $backup) {
				
				if( $backup['backuptime'] == $productBackup ) {
					$backupDataToLoad = $backup['templateInfo'];
					$position = $key;
					break;
				}
				
			}
			
			//unset($completebackup[$position]);
			$remainingBackups = array( 'templatebackup' => $completebackup );
			$newDataCurrentTemplate = $backupDataToLoad;
			$finalDataCurrentTemplate = wp_parse_args($remainingBackups,$backupDataToLoad);
			
			$customiserData[$productTemplate] = $finalDataCurrentTemplate;
			update_option($dboption.'-fc-styles',$customiserData);
			$css = $this->give_css_with_prefix('.product-template-preview',$productTemplate,$customiserData);
			$response = array('css' => $css,'updated' => $customiserData,'loaded'=>$productBackup);
			echo json_encode($response);
			exit;
		}
		
		public function copy_from_preformatted_template() {
			
			$product = $_POST['product'];
			$productTemplateFrom = $_POST['templateFrom'];
			$productTemplateTo = $_POST['templateTo'];
			
			$customiserData = get_option($product.'-fc-styles');
			//$dataOfOtherTemplates = $this->dbsettings;
			if (array_key_exists( $productTemplateTo , $customiserData))
			unset($customiserData[$productTemplateTo]);
			
			$currentlayoutsettings = $customiserData[$productTemplateFrom];
			$customiserData[$productTemplateTo] = $currentlayoutsettings;
			//$customiserData = wp_parse_args( $customiserData , $currentlayoutsettings);							
			update_option( $product.'-fc-styles' , $customiserData );
			echo json_encode($customiserData);
			exit;
		}
		
		function get_html_editor() {
			
			ob_start();
			wp_editor( '', 'new_template_content' );
			$editor = ob_get_contents();
			ob_clean();
			return $editor;
			
		}
		
		function setup_customiser_modals() {
			
		
	        $model_remove_all_formatting = array('modalID' => 'remove-all-formatting',
								'modalHeading' => 'Delete All Templates',
								'modalBody' => '<p>You are about to delete all the custom templates of category '.$_GET["templatetype"].' that were generated by customiser. <p>Do you really want to proceed ?</p>',
								'saveBtnText' => 'Remove Formatting',				
								'saveBtnClass' => 'remove-all-template-formatting');
		
		    $model_remove_current_template_formatting = array('modalID' => 'remove-current-template',
								'modalHeading' => 'Delete Current Custom Template',
								'modalBody' => '<p>You are about to delete this custom template.</p>
								<p>Do you want to proceed?</p>',
								'saveBtnText' => 'Delete Template',				
								'saveBtnClass' => 'remove-current-template');
		
			
	       $model_remove_current_template_formatting = array('modalID' => 'remove-current-formatting',
								'modalHeading' => 'Remove Current Template Formatting',
								'modalBody' => '<p>You are about to remove all the formatting of current template.</p>
								<p>Do you want to proceed?</p>',
								'saveBtnText' => 'Remove Formatting',				
								'saveBtnClass' => 'remove-template-formatting');
		
	       $model_remove_current_template_backups = array('modalID' => 'remove-template',
								'modalHeading' => 'Remove Current Template Backup',
								'modalBody' => '<p>You are about to remove a backup of current template.</p>
								<p>Do you want to proceed?</p>',
								'saveBtnText' => 'Remove Template Backup',				
								'saveBtnClass' => 'remove-template-backup');							
								
	       $model_load_current_template_backup = array('modalID' => 'load-template',
								'modalHeading' => 'Load Template Backup',
								'modalBody' => '<p>You are about to load a backup of current template. Loading it will set it as current style of current template. Please take a backup of current styling if you wish before proceeding.</p>
								<p>Do you want to proceed?</p>',
								'saveBtnText' => 'Load Template Backup',				
								'saveBtnClass' => 'load-template-backup');	
								
	       $model_load_another_template = array('modalID' => 'confirm-navigate',
								'modalHeading' => 'Load Another Template',
								'modalBody' => '<p>You are about to load a different template, Please save current template settings before proceeding.</p>
								<p>Do you want to proceed?</p>',
								'saveBtnText' => 'Load Template',				
								'saveBtnClass' => 'btn-ok');								
		
				
	       $model_tpl_bckup = array('modalID' => 'take-template-name',
								'modalHeading' => 'Enter Template Name',
								'modalBody' => '<div class="form-group">
													<label for="template_backup_name">Template Name : </label>
													<input type="text" class="form-control" name ="template_backup_name" id="template_backup_name" placeholder="Enter Template Name Eg. Light Blue Template">
												</div>',
								'saveBtnText' => 'Save Template Backup',				
								'saveBtnClass' => 'take-template-backup');
		/* <input type="textarea" cols="50" row="10" class="form-control" name ="create_new_template_html" id="create_new_template_html" > */						
		   $update_template_html = array('modalID' => 'update-template-content',
								'modalHeading' => 'Update Template Content',
								'modalBody' => '<div class="form-group">
								<label for="new_template_content">Update Template HTML Content : </label>
								'.$this->get_html_editor().'</div>',
								'saveBtnText' => 'Update Template Content',				
								'saveBtnClass' => 'update-template-content-btn');
								
		  $no_element_changed = array('modalID' => 'no-element-changed',
								'modalHeading' => 'Nothing Was Changed.',
								'modalBody' => '<p>Please make some changes first for saving it as a new template.</p>',
								'saveBtnText' => 'Ok',				
								'saveBtnClass' => 'no-element-changed-btn');						
														
								
		$this->customiserModals = array($model_remove_all_formatting,
										$model_remove_current_template_formatting,
										$model_remove_current_template_backups,
										$model_load_current_template_backup,
										$model_load_another_template,
								        $model_tpl_bckup,
								        $update_template_html,
								        $no_element_changed);						
	
			
		}
		
		function render_customiser_modals() {
			
			$this->setup_customiser_modals();
			
			foreach($this->customiserModals as $modal) {
				
			 $modalID = $modal['modalID'];
			 $modalHeading = $modal['modalHeading'];
			 $modalBody = $modal['modalBody'];
			 $saveBtnText = $modal['saveBtnText']; 
			 $saveBtnClass = $modal['saveBtnClass']; 
			 	
			 ?>
			 <div style="z-index:99999999;" class="modal fade custom-modal" id="<?php echo $modalID; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="tktemplatebckup"><?php echo $modalHeading; ?></h4>
							</div>
							<div class="modal-body">
							   <?php echo $modalBody; ?>
							</div>
							<?php
							if($modalID == 'no-element-changed') {
								
								?>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
							</div>
								<?php
								
							} else {
								?>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<a class="btn btn-success btn-ok <?php echo $saveBtnClass; ?>"><?php echo $saveBtnText; ?></a>
							</div>
								<?php
							}
							?>
							
						</div>
					</div>
				</div>
			 <?php		
				
			}
			
		}
		
		function is_fc_customizer_valid_request() {
			
			if( $_GET['page'] == 'fpc'
			    and !empty($_GET['fc-security']) 
			    and !empty($_GET['product']) 
			    and !empty($_GET['productTemplate'])
			    and !empty($_GET['instance'])
			    and !empty($_GET['templatetype'])
			    and !empty($_GET['settingPage'])
			    and wp_verify_nonce($_GET['fc-security'], 'fc-product-customizer' ) ) {
				return true;
				
			}else{
				return false;
			}
			
		}
		
		function _init_customizer() {
					
			$this->isAjax = ( defined( 'DOING_AJAX' ) and DOING_AJAX and is_admin() ) ? true : false;
			$this->load_customizer_configuration();
			$this->dbsettings = get_option($this->dboption.'-fc-styles');
			
			$this->optionName = $this->dboption;
			$template = (!empty($this->productTemplate)) ? $this->productTemplate : $this->productCustomiserInfo['productTemplate'];
			$this->fontStyle = array('normal' => 'Normal','bold' => 'Bold','italic' => 'Italic');
			$this->fontWeight = array('normal' => 'Normal','bold' => 'Bold','900' => '900' );
			
			if(isset($template) and !empty($this->dbsettings[$template])) {
			   
			   $current = $this->currentSettings = $this->dbsettings[$template];
			   unset($current['templatebackup']); //To get only settings current settings excliding backups.
			   $this->currentBasicSettings = $current;
			   if(isset($this->currentSettings['templatebackup']))
			   $this->currentTemplateBackups = $this->currentSettings['templatebackup'];			
			}
			$this->load_google_fonts_in_header();
		
		}
		
		function get_placeholder_value($placeholder) {
			
			//Get values from DB or Defaults if not set :)
			if(isset($this->dboption[$placeholder]) and !empty($this->dboption[$placeholder]) ) 
			$this->placeholders[$placeholder] = nl2br($this->dboption[$placeholder]);
			else
			$this->placeholders[$placeholder] = $this->defaults[$placeholder];
			
		}
		
		function load_google_fonts() {
		
		  $file = $this->productDirectoryPath.'core/core-assets/js/fonts.json';
		  if( file_exists( $file ) ) {
			    $gf = json_decode( file_get_contents( $file ), true);
			    if($gf) {
					foreach ($gf as $gfonts) {
						
						if(is_array($gfonts)) {
							foreach($gfonts as $name => $value) {
							  
							   $this->googleFonts[$value['family']] = $value['family'];
							   
							}
					   }
				    }
				}
				
		  }	
		  
		}
		
		
		function load_customizer_resources() {
			
			if( !empty($this->productStyles) ) {
				foreach($this->productStyles as $key => $style) {
					wp_enqueue_style( $key.'-dependend-style',$this->productURL.'assets/css/'.$style.'.css');
				}
		    }
			
			if( !empty($this->productScripts) ) {
				foreach($this->productScripts as $key => $script) {
					
					if(array_key_exists($script,$this->localizedScript)) {
					 
					$object = $this->localizedScript[$script][0];
					$information =  $this->localizedScript[$script][1];
					wp_enqueue_script(
						$key.'-dependend-script',
						$this->productURL.'assets/js/'.$script.'.js'
					);
                    wp_localize_script($key.'-dependend-script', $object, $information ); 
                    //Put Only Localised Scripts With Objects in Header.
    
					}else{
						
                     wp_enqueue_script( $key.'-dependend-script',$this->productURL.'assets/js/'.$script.'.js',array( 'jquery' ),'',true);
                     
				    }
				}
		    }
		    //echo '<pre>'; print_r($this->defaults);
		    /*if(!empty($this->defaults)) {
			 
				 foreach($this->defaults as $key => $defaultValue) {
						   $this->placeholders[$key] = '';
				 }	
     			 foreach($this->placeholders as $placeholder => $value) {
					$this->get_placeholder_value($placeholder);
				 }
			 	
			}*/
			//echo '<pre>test'; print_r($this->defaults); exit;
			
		}
		
		function load_customizer_configuration() {
			
			
			foreach($this->productCustomiserInfo as $propertyKey => $property) {
				
				if( property_exists( __CLASS__,$propertyKey) )
				$this->$propertyKey = $property;
			}
			
			$this->basicCustomiserArgs = array( 'product' => $this->productDirectory,
												'instance' => $this->instance
											);
			//echo '<pre>'; print_r($this->productCustomiserInfo); exit;		
		}
		
		function render_customiser_form() {
				
			$this->setup_customiser_configurations();
			$this->setup_customiser_form();
			$this->setup_template_backups();
		    
		}
		
		function setup_template_backups() {
			
			if(isset($this->dbsettings[$_GET['productTemplate']]['templatebackup'])) {
				
				$templatebackups = $this->dbsettings[$_GET['productTemplate']]['templatebackup'];
			if($templatebackups) { ?>
				
			<div class="current-template-backups">
			<p class="center">List Of Saved Backups</p>		 
			<?php 
			
			rsort($templatebackups);
			foreach($templatebackups as $key => $backup) { 
			
			//echo '<pre>'; print_r($this->dbsettings); exit;
			$seeInAction = $this->get_see_in_action_link($backup['backuptime']);
			?>
			<div class="rtl template-backup-<?php echo $backup['backuptime'];?>">
				<div class="template-no-<?php echo $backup['backuptime'];?>">
				<div class="template-backup-details">
					<p>Template Name : <?php echo $backup['templatename'];?></p>
					<p>Backup Date : <?php echo date('Y-m-d H:i:s',$backup['backuptime']);?></p>
					 <a href="<?php echo $seeInAction; ?>" class="likebutton see-template-in-action" data-product = <?php echo $_GET['product']; ?> data-backuptime = <?php echo $backup['backuptime']; ?> data-template="<?php echo $_GET['productTemplate']; ?>">See In Action</a>
					 <a href="#" data-toggle="modal" data-target="#load-template" class="load-template likebutton" data-product = <?php echo $_GET['product']; ?> data-backuptime = <?php echo $backup['backuptime']; ?> data-template="<?php echo $_GET['productTemplate']; ?>">Load This Template</a>
					 <a href="#" data-toggle="modal" data-target="#remove-template" class="remove-template likebutton" data-product = <?php echo $_GET['product']; ?> data-backuptime = <?php echo $backup['backuptime']; ?> data-template="<?php echo $_GET['productTemplate']; ?>">Remove This Template</a>
				</div>	 	 
			    </div>
			</div>    
			<?php	
				
			 }
			
			?>
				
			</div>
				<?php }
					
			}
			
			
		 }
		
		function setup_customiser_form() { 
			
			//echo '<pre>'; print_r($this->customiserFormElements);
			
			
			ob_start();
			$mainelements = array_keys( $this->customiserFormElements );
			
			?>
			<div class="introduction">
				<div class="col-md-2" style="height:auto;"> 
				<a href="http://www.flippercode.com" target="_blank">
					<img src="<?php echo $this->productURL; ?>core/core-assets/images/wp-poet.png" alt="Flippercode">
				</a>
				</div>
				<div class="col-md-8 customiser_intro_wrapper">
					<div class="customiszer_heading">Product Customiser</div>
					<div class="customiszer_introduction">Customise CSS &amp; Settings with one single click.</div>
				</div>
			</div>
			<div class="product-customizer-form">
			<form class="fc-product-customisation" name="fc-product-customisation" id="fc-product-customisation" method="post" action = <?php //echo admin_url('?page=flippercode-product-customiser'); ?>>
			<input type="hidden" name="originalelements" id="originalelements" value="<?php echo base64_encode(serialize($mainelements)); ?>">
				<input type="hidden" name="product" id="product" value="<?php echo $_GET['product']; ?>">
				<input type="hidden" name="productTemplate" id="productTemplate" value="<?php echo $_GET['productTemplate']; ?>">
			<input type="hidden" name="instance" id="instance" value="<?php echo $_GET['instance']; ?>">
			<input type="hidden" name="templatetype" id="templatetype" value="<?php echo $_GET['templatetype']; ?>">
				<input type="hidden" name="dboption" id="dboption" value="<?php echo $this->optionName; ?>">
				<input type="hidden" name="productDirectory" id="productDirectory" value="<?php echo $this->productDirectory; ?>">
				<input type="hidden" name="ajaxurl" id="ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>">
				<input type="hidden" name="nonce" id="nonce" value ="<?php echo wp_create_nonce( 'my-nonce' ); ?>">
				<div class="clear"></div>
				<div class="accordion" id="searchAccordion">
				  <?php
					  
					  foreach($this->customiserFormElements as $key => $element) {
						  ?>
						  
						  <div class="accordion-group">
							<div class="accordion-heading">
							  <a class="accordion-toggle" data-toggle="collapse"
								data-parent="#searchAccordion" href="<?php echo '#section-'.$element['name']; ?>"><?php echo ucwords($element['label']); ?></a>
							</div>
							<div id="<?php echo 'section-'.$element['name']; ?>" class="accordion-body collapse">
							  <div class="accordion-inner">
								<?php
								
									foreach($element['properties'] as $propertyName => $propertyDefault) {
									 
									 $controlType =  $this->get_property_control( $propertyName );	
									 $controlMarkup =  $this->get_property_control_markup( $controlType, $element , $propertyName );
										
									} 
									
								?>
								
							  </div>
							</div>
						  </div>
					  <?php
						  
					  }
					  
					  ?>
					 
					</div>
					<?php
					/*
					<div class="customiser-buttons-area">
								
					<button data-toggle="modal" data-target="#create-new-template" type="button" style="float:right"; name="create_new_template_btn" id="create_new_template_btn" class="create-new-template-btn bluebg likebutton" data-product = <?php echo $_GET['product']; ?> data-template="<?php echo $_GET['productTemplate']; ?>" data-templatetype = <?php echo $_GET['templatetype']; ?>>Create New Template<i></i></button>
						
					<button type="submit"  name="save_customiser" id="save_customiser" class="save_customiser bluebg likebutton" value="save_current_settings">Save Customisation <i></i></button>
					
					<button data-toggle="modal" data-target="#take-template-backup" type="button" style="float:right"; name="save_template_backup" id="save_template_backup" class="save_customiser bluebg likebutton">Save Current State As Backup <i></i></button>
					
					<button data-toggle="modal" data-target="#remove-current-formatting" type="button" style="float:right"; name="remove_template_formatting" id="remove_template_formatting" class="save_customiser redbg likebutton" data-product = <?php echo $_GET['product']; ?> data-template="<?php echo $_GET['productTemplate']; ?>" >Clear Current Template Formatting<i></i></button>
					
					<button data-toggle="modal" data-target="#remove-all-formatting" type="button" style="float:right"; name="remove_all_template_formatting" id="remove_all_template_formatting" class="save_customiser redbg likebutton" data-product = <?php echo $_GET['product']; ?> data-template="<?php echo $_GET['productTemplate']; ?>" data-settingPage="<?php echo $_GET['settingPage']; ?>">Reset Formatting For All Templates<i></i></button>
					
					</div>
					* 
					*/ ?> 
				</form>
				</div>

			<?php 
			$form = ob_get_contents();
			ob_clean();
		    echo $form;
			
		}
		
		
		
		function get_css_property_unit($property) {
			
			switch ( $property ) {
				
				case 'width':
					  $unit ='%';
					  break;
				case 'font-size':
				      $unit ='px';
					  break;	  
				case 'border-radius':
				      $unit ='px';
				case 'padding':
				      $unit ='px';
				case 'padding-top':
				      $unit ='px';
				case 'padding-right':
				      $unit ='px';
				case 'padding-bottom':
				      $unit ='px';
				case 'padding-left':
				      $unit ='px';
				case 'margin':
				      $unit ='px';
				case 'margin-top':
				      $unit ='px';
				case 'margin-right':
				      $unit ='px';
				case 'margin-bottom':
				      $unit ='px';
				case 'margin-left':
				      $unit ='px';
					  break;
				default:
					  $unit ='';
					  
			}
			return $unit;
		}
		
		function get_form_control_value($controlName) {
		    
		    if(isset($this->dbsettings[$this->productTemplate])) {
				
			  if(array_key_exists($controlName,$this->dbsettings[$this->productTemplate]['formdata'])) {
					$dynamicValue = $this->dbsettings[$this->productTemplate]['formdata'][$controlName];
					if(!empty($dynamicValue))
					return $dynamicValue;
		      }
		    	
			}
			
			return '';
		}
		
		function get_property_control_markup( $controlType, $control, $property ){
			
			$type = $controlType['type'];
			$label = $controlType['propertylabel'];
			$controlName = $controlId = $control['name'].'*'.$property;
			$controlValue = $this->get_form_control_value( $controlName );
			if(empty( $controlValue ))
			$controlValue = $control['properties'][$property];
			//echo 'Control Vlue '.$controlValue.'<br>';
			//echo '<pre>'; print_r($control['properties']);
			switch ( $type ) {
				
				case 'selectbox':
				      
				      $defaultoptions = $control['defaults'][$property];
				      $markup = '<div class="form-group">
					  <label for="'.$controlId.'">'.$label.'</label>
					  <select id = "'.$controlId.'" name="'.$controlName.'" class="fc-cc-cp form-control" data-target="'.$control['selector'].'" data-event="select-change" data-property="'.$property.'" >';
					   $markup .= '<option value="">Please Select '.$label.'</option>';
					   foreach($defaultoptions as $key => $value) {
						   $markup .= '<option value="'.$key.'" '.selected( $key, $controlValue ,false ).'>'.$value.'</option>';
					   }
					  $markup .='</select></div>';
					  break;
					  
				case 'textbox':
					  $markup = '<div class="form-group">
								 <label for="'.$controlId.'">'.$label.'</label>
									<input type="text" id = "'.$controlId.'" name="'.$controlName.'" value="'.$controlValue.'" class="fc-cc-text form-control" data-event="text-change" data-target="'.$control['selector'].'" data-property="'.$property.'">
								  </div>';
					  break;
					  
			    case 'colorpicker':
			          $extraClass = (isset($control['iswrappertype']) and $control['iswrappertype'] == 'yes') ? 'wrapperelement' : '';
			          $isBackground = (strpos($controlName, 'background') !== false) ? 'for-background' : '';
			           
					  $markup = '<div class="form-group">
								 <label for="'.$controlId.'" style="width:100%;">'.$label.'</label>
									<input type="text" id = "'.$controlId.'" name="'.$controlName.'" value="'.$controlValue.'" class="fc-cc-cp form-control color-field '.$extraClass.' '.$isBackground.'" data-target="'.$control['selector'].'" data-property="'.$property.'">
								  </div>';
					  break;
					  
				case 'range':
				     
				     if(isset($control['defaults'][$property])) {
						 //echo '<pre>'; print_r($control['defaults'][$property]);
						 $min = $control['defaults'][$property]['min'];
						 $max = $control['defaults'][$property]['max'];
						 $interval = $control['defaults'][$property]['interval'];
					 } else {
						 $min = '0';
						 $max = '100';
						 $interval = '1';
					 }
				     
				     // echo '<pre>'; print_r($control);
				      
				      
					  $markup = '<div class="form-group">
					  			  <label for="'.$controlId.'" style="width:100%;">'.$label.'</label>
								  <div class="range">
									<input type="range" step="'.$interval.'" min="'.$min.'" max="'.$max.'" id = "'.$controlId.'" name="'.$controlName.'" value="'.$controlValue.'" class="fc-cc-cp form-control" data-event="range-change" data-target="'.$control['selector'].'" data-property="'.$property.'">
									<output id="'.$controlId.'-range">'.$controlValue.'</output>
								  </div>
								 </div>';
					  break;
					  
				case 'range-group':
				
					  if(isset($control['defaults'][$property])) {
						 //echo '<pre>'; print_r($control['defaults'][$property]);
						 $min = $control['defaults'][$property]['min'];
						 $max = $control['defaults'][$property]['max'];
						 $interval = $control['defaults'][$property]['interval'];
					 } else {
						 $min = '0';
						 $max = '100';
						 $interval = '1';
					 }
					 	
				      $values = explode(' ',$controlValue);
				      $length = count($values);
				      if($length == 1 or $length != 4) {
						 $markup = '<div class="form-group">
					  			  <label for="'.$controlId.'" style="width:100%;">'.$label.'</label>
								  <div class="range">
									<input type="range" step="'.$interval.'" min="'.$min.'" max="'.$max.'" id = "'.$controlId.'" name="'.$controlName.'" value="'.$controlValue.'" class="fc-cc-cp form-control" data-event="range-change" data-target="'.$control['selector'].'" data-property="'.$property.'">
									<output id="'.$controlId.'-range">'.$controlValue.'</output>
								  </div>
								 </div>';
					  		
					  } else{
						  
						  $markup = '';
						  $controlName = $controlId = $control['name'].'*'.$property;
						  
						      $groupproperty = array('top','right','bottom','left'); 
							 // echo '<pre>'; print_r($values);
							  foreach($groupproperty as $key => $individualproperty) {
								
								 $controlName = $controlId = $control['name'].'*'.$property.'-'.$individualproperty;
								 $newlabel = $label.' '. ucfirst($individualproperty);
								 $newproperty = $property.'-'.$individualproperty;
								 
								 $markup .= '<div class="form-group">
					  			  <label for="'.$controlId.'" style="width:100%;">'.$newlabel.'</label>
								  <div class="range">
									<input type="range" min="0" max="100" id = "'.$controlId.'" name="'.$controlName.'" value="'.$values[$key].'" class="fc-cc-cp form-control" data-event="range-change" data-target="'.$control['selector'].'" data-property="'.$newproperty.'">
									<output id="'.$controlId.'-range">'.$values[$key].'</output>
								  </div>
								 </div>'; 
								  
							  }
						
						  
					  }
					
					  break;	  	  
					  
				
			}
			
			echo $markup;
			
		}
		
		
		function get_property_control($property) {
			
			switch ($property) {
				case 'opacity':
					  $type = 'range';
					  $label = 'Opacity';
					  break;
				case 'font-family':
					  $type = 'selectbox';
					  $label = 'Font-Family';
					  break;
				case 'font-size':
					  $type = 'range';
					  $label = 'Font-Size';
					  break;
				case 'font-weight':
					  $type = 'selectbox';
					  $label = 'Font-Weight';
					break;
				case 'font-style':
					  $type = 'selectbox';
					  $label = 'Font-Style';
					break;	
				case 'background-color':
					  $type = 'colorpicker';
					  $label = 'Background-Color';
					break;
				case 'border-color':
					  $type = 'colorpicker';
					  $label = 'Border-Color';
					break;
				case 'color':
					  $type = 'colorpicker';
					  $label = 'Color';
				     break;	
				case 'margin':
					  $type = 'range-group';
					  $label = 'Margin';
					break;			
				case 'padding':
					  $type = 'range-group';
					  $label = 'Padding';
					break;
				case 'width':
					  $type = 'textbox';
					  $label = 'Width';
					break;
				case 'height':
					  $type = 'textbox';
					  $label = 'Height';
					break;
				case 'border-radius':
					  $type = 'range';
					  $label = 'Border Radius';
					break;	
				
			}
			//echo 'here'.$property; 
			$property = array( 'type' => $type , 'propertylabel' => $label );
			return $property;	
		}
		
		function setup_customiser_configurations() {
			
			include $this->productDirectoryPath.'templates/'.$this->templateType.'/'.$this->productTemplate.'/'.$this->productTemplate.'.php';
			
		}
		
		function get_final_merged_link($linkinfo) {
			
			$finalLinkArgs = wp_parse_args($linkinfo,$this->basicCustomiserArgs);
			$finalCustomiserLink = add_query_arg( $finalLinkArgs,admin_url('admin.php?page=fpc') );
			return $finalCustomiserLink;
		}
		
		function get_see_in_action_link($backup) {
			
			$currentbackupargs = array('productTemplate' => $this->productTemplate,'backuptime' => $backup);
			return $this->get_final_merged_link($currentbackupargs);
		}
		
		function get_template_link( $template ) {
			
			if (method_exists($this, 'get_customised_template_link')) { 
				
				$templateLink = $this->get_customised_template_link($template);
				
			} else { 
				
				$templateArgs = array('productTemplate' => $template['template']); 
				$templateLinkArgs = wp_parse_args($templateArgs,$this->basicCustomiserArgs);
			    $templateLink = add_query_arg( $templateLinkArgs,admin_url('admin.php?page=fpc') );
				
			}
			
			return $templateLink;
			
		}
		
		function render_related_templates() { 
			
			 ?>
				
				<div class="product-related-templates">
					<div class="product-related-templates-inner">
						<p class="related-template-list">Some other templates of this product that you may like to customise according to your wordpress theme.</p>
					   <?php
					   
						if (method_exists($this, 'render_related')) { 
						
						$template = $this->render_related();
						
						} else {
							
			               /* Remove current template from list of related templates and calculate width accordingly. */ 	
						  
						   $allTemplates = $this->availableProductTemplates; 	
						   if(array_key_exists( $this->productTemplate , $allTemplates ))
						   unset($allTemplates[$this->productTemplate]);
						   
						   $eachTemplateWidth = floor( 100 / count($allTemplates) );	
						    
						   ob_start();
						   
						   foreach( $allTemplates as $templateFilename => $templateName) {
							    $templateLink = $this->get_template_link( array('template' => $templateFilename ) ); 
						        echo '<a href="#" data-href="'.$templateLink.'" data-toggle="modal" data-target="#confirm-navigate" id="related-template-'.$templateFilename.'" class="related-template-'.$templateFilename.' rt"><div class="related_product_template" style="width:'.$eachTemplateWidth.'%;float:left;margin:1%;">
						        <div class="related-template-name">'.$templateName.'</div>';
								include $this->templatePath.$templateFilename.'.php';	
								echo '</div></a>';
							}	
							
							$template = ob_get_contents();
							ob_clean();
						
						}
						
						echo $template;	
					  
					    //echo $customizer->render_customiser_preview(); ?>
					  	 		
					</div>
				</div>
				
			<?php
			
				
			
		}
		
		function load_physical_template($templateFile,$templateCssFile) {
			
			include $templateFile;
			$template = ob_get_contents();
			$this->finalMarkup = $template;
			ob_clean();
			
		}
		
		function get_dynamic_template() {
			 
			  $file = $this->templatePath.$this->templateType.'/'.$this->productTemplate.'/'.$this->productTemplate.'.html';
			  $css_file = $this->templatePath.'/'.$this->templateType.'/'.$this->productTemplate.'/'.$this->productTemplate.'.css';
			    
			  ob_start();
			  if( file_exists( $file )) {
				  
				  $this->load_physical_template($file,$css_file);
				  
			  }
			  elseif(isset($this->dbsettings[$this->productTemplate]['templateMarkup'])) { //echo 'yes';
				  
				    if(isset($this->dbsettings[$this->productTemplate]['templateMarkup'])) {
	$this->finalMarkup = html_entity_decode($this->dbsettings[$this->productTemplate]['templateMarkup']);
						
					} 
				    
			  } else { 
				
				  $data = get_option($this->optionName);
				  $defaultProductTemplate = $data['real_default_templates'][$this->templateType];
				  $file = $this->templatePath.$this->templateType.'/'.$defaultProductTemplate.'/'.$defaultProductTemplate.'.html';
			      $css_file = $this->templatePath.$defaultProductTemplate.'/'.$defaultProductTemplate.'.css';
				  $this->load_physical_template($file,$css_file);
				  
				  $this->finalMarkup = '<p>Template Do Not Exists</p>';
			  }
			  return $this->finalMarkup;		
			  
		}
		
		function render_current_template() {
			
			?>
							
			<div class="product-template-preview">
			     <div class="product-preview-inner">
									
					<?php
									
					if (method_exists($this, 'render_preivew')) {
				  		$this->finalMarkup = $this->render_preivew();
					} else {
						$this->finalMarkup = $this->get_dynamic_template();
					}
					
					$this->process_template_placeholders();
					echo $this->finalMarkup;
					
					?>
            
                 </div>
			</div>
						
            <?php
            
		}
		
		function process_template_placeholders() {
			
			if(!empty($this->defaults)) {
			 
				 foreach($this->defaults as $key => $defaultValue) {
						   $this->placeholders[$key] = '';
				 }	
				 foreach($this->placeholders as $placeholder => $value) {
					$this->get_placeholder_value($placeholder);
				 }
			 	
			}
			
			$this->finalMarkup = $this->replace_placeholder_with_values($this->finalMarkup);
			
		}
		
		function render_customiser_preview() {
			
			
			$this->render_current_template();
			if($this->availableProductTemplates)
			$this->render_related_templates();
			$this->render_customiser_modals();
			
		}
		
		function get_overlay_wrapped_template($templateInfo,$templateContent) {
			
			$templateLink = $this->get_template_link( $templateInfo ); 
			$finalmarkup = '<div id="related-template-'.$templateInfo['template'].'" class="related-template-'.$templateInfo['template'].' rtl">';
			$finalmarkup .= $templateContent;
			$finalmarkup .= '<div class="overlay"><a href="'.$templateLink.'" class="bt1">Customise This Template</a></div>
			</div>';
			
		}
		function render_preformatted_templates() {
			?>
			
			<div class="availalble-formatted-templates">
			<?php
			
			if(!empty($this->dbsettings)) { ?>
			
				<select name="availalbe_formatted_templates" id="availalbe_formatted_templates">
					<option value="">Please Select A Preformatted Template</option>
				<?php
				foreach($this->dbsettings as $key => $formattedTemplate) { ?>
			     <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
				<?php } ?>
				</select>
				<button type="button" name="copy_formatting" id="copy_formatting">Copy Formatting</button>
				<p>You can choose from the above available templates to copy the style of the template and to apply to this current template just in a single click.</p>
			  
				
			<?php }
			?>
			</div>
			<?php
			
		}
		
		function replace_placeholder_with_values($markup) {
			
			foreach($this->placeholders as $key => $value){
				//$markup = str_replace('{'.$key.'}', $value, $markup);
				$markup = str_replace($key, $value, $markup);
			}
			
			return $markup;
			
		}
		
	}
}
