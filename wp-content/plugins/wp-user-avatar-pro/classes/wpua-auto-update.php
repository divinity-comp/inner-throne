<?php
/**
 * Auto Update notification Class File.
 * @author flippercode
 * @package Updates
 * @version 1.0.0
 */
if( !class_exists('WPUA_Auto_Update') and class_exists ('Flippercode_Product_Auto_Update') ) {
	
	class WPUA_Auto_Update extends Flippercode_Product_Auto_Update{
		
		function __construct() { $this->wsq_current_version = WPUAP_VERSION; parent::__construct(); }
	}
	return new WPUA_Auto_Update();
	
} 

