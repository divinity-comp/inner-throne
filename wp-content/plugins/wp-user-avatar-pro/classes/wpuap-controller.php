<?php
/**
 * Controller class
 * @author Flipper Code<hello@flippercode.com>
 * @version 3.0.0
 * @package Posts
 */

if ( ! class_exists( 'WPUAP_Controller' ) ) {

	/**
	 * Controller class to display views.
	 * @author: Flipper Code<hello@flippercode.com>
	 * @version: 3.0.0
	 * @package: Maps
	 */

	class WPUAP_Controller extends Flippercode_Factory_Controller{


		function __construct() {

			parent::__construct(WPUAP_MODEL,'WPUAP_Model_');

		}

	}
	
}
