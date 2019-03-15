<?php

if ( ! class_exists( 'WPUAP_FORM' ) ) {

	class WPUAP_FORM extends FlipperCode_HTML_Markup{
		
		function __construct($options = array()) {
			
            $productInfo = array('productName' => __('WP User Avatar Pro',WPUAP_TEXT_DOMAIN),
                        'productSlug' => 'wp-user-avatar-pro',
                        'productTagLine' => 'WP User Avatar Pro - an excellent product that allows users to upload any custom user avatar even through web-cam with the facility of cropping and resizing avatar before saving',
                        'productTextDomain' => WPUAP_TEXT_DOMAIN,
                        'productIconImage' => WPUAP_URL.'core/core-assets/images/wp-poet.png',
                        'productVersion' => WPUAP_VERSION,
                        'videoURL' => 'https://www.youtube.com/watch?v=CXUQNZLw_bE&list=PLlCp-8jiD3p3DtZ-2ZubVqwyOV1NTgEOn',
                        'docURL' => 'http://guide.flippercode.com/avatar/',
                        'demoURL' => 'http://www.flippercode.com/product/wp-user-avatar/',
                        'productImagePath' => WPUAP_URL.'core/core-assets/product-images/',
                        'productSaleURL' => 'https://codecanyon.net/item/wp-user-avatar-pro/15638832',
                        'multisiteLicence' => 'https://codecanyon.net/item/wp-user-avatar-pro/15638832?license=extended&open_purchase_for_item_id=15638832&purchasable=source'
			);
    
			$productInfo = array_merge($productInfo, $options);
			parent::__construct($productInfo);

		}

	}
	
}
