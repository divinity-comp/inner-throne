<?php
/**
 * Email Templates
 *
 * Creates email templates to match each of the invoice templates.
 *
 * @package Easy Digital Downloads - PDF Invoices
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Templates
 *
 * Registers the email templates bundled with the plugin
 *
 * @since 1.0
 *
 * @param array $edd_templates An array of the pre-existing EDD email templates
 *
 * @return array Merged array containing the new and pre-existing EDD email
 *          templates
 */
function eddpdfi_register_templates( $edd_templates ) {
	$eddpdfi_email_templates = array(
		'invoice_default' => __( 'Invoice Default', 'eddpdfi' ),
		'blue_stripe'     => __( 'Blue Stripe', 'eddpdfi' ),
		'lines'           => __( 'Lines', 'eddpdfi' ),
		'minimal'         => __( 'Minimal', 'eddpdfi' ),
		'traditional'     => __( 'Traditional', 'eddpdfi' ),
		'invoice_blue'    => __( 'Invoice Blue', 'eddpdfi' ),
		'invoice_green'   => __( 'Invoice Green', 'eddpdfi' ),
		'invoice_orange'  => __( 'Invoice Orange', 'eddpdfi' ),
		'invoice_pink'    => __( 'Invoice Pink', 'eddpdfi' ),
		'invoice_purple'  => __( 'Invoice Purple', 'eddpdfi' ),
		'invoice_red'     => __( 'Invoice Red', 'eddpdfi' ),
		'invoice_yellow'  => __( 'Invoice Yellow', 'eddpdfi' )
	);

	return array_merge( $edd_templates, $eddpdfi_email_templates );
}
add_filter( 'edd_email_templates', 'eddpdfi_register_templates' );


/**
 * Default Invoice Email Template
 *
 * @since		1.0
*/
function eddpdfi_invoice_default() {
	global $edd_options;

	echo '<div style="width: 550px; background: #ececec; border: 1px solid #c9c9c9; margin: 0 auto; padding: 4px; outline: none;">';
		echo '<div style="padding: 1px; background: #fff; border: 1px solid #fff;">';
			echo '<div id="edd-email-content" style="padding: 10px; background: #fff; border: 1px solid #aaa;">';
				if ( isset( $edd_options['email_logo'] ) ) {
					echo '<img src="' . $edd_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				} else if ( isset( $edd_options['eddpdfi_email_logo'] ) ) {
					echo '<img src="' . $edd_options['eddpdfi_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				}
				echo '<h1 style="color: #323232; font-size: 24px; font-weight: normal;">' . __( 'Receipt', 'eddpdfi' ) .'</h1>';
				echo '{email}';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
add_filter( 'edd_email_template_invoice_default', 'eddpdfi_invoice_default' );

/**
 * Default Email Template Extra Styling
 *
 * Overrides the default invoice template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function eddpdfi_invoice_default_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="color: #323232; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #323232; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #323232; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<li>', '<li style="color: #323232;">', $email_body );
	$email_body = str_replace( '<p>', '<p style="color: #323232;">', $email_body );

	return $email_body;
}
add_filter( 'edd_purchase_receipt_invoice_default', 'eddpdfi_invoice_default_extra_styling' );


/**
 * Blue Stripe Email Template
 *
 * @since 1.0
*/
function eddpdfi_blue_stripe() {
	global $edd_options;

	echo '<div style="width: 600px; background: #fff; border-left: 10px solid #97d3eb; margin: 0 auto; padding: 10px; outline: none;">';
		echo '<div id="edd-email-content" style="padding: 10px; background: #fff;">';
			if ( isset( $edd_options['email_logo'] ) ) {
				echo '<img src="' . $edd_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			} else if ( isset( $edd_options['eddpdfi_email_logo'] ) ) {
				echo '<img src="' . $edd_options['eddpdfi_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="color: #97d3eb; font-size: 24px; font-weight: normal;">' . __( 'Receipt', 'eddpdfi' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'edd_email_template_blue_stripe', 'eddpdfi_blue_stripe' );

/**
 * Blue Stripe Email Template Extra Styling
 *
 * Overrides the default invoice template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function eddpdfi_blue_stripe_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="color: #97d3eb; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #97d3eb; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #97d3eb; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="list-style: square;">', $email_body );

	return $email_body;
}
add_filter( 'edd_purchase_receipt_blue_stripe','eddpdfi_blue_stripe_extra_styling' );


/**
 * Lines Email Template
 *
 * @since 1.0
*/
function eddpdfi_lines() {
	global $edd_options;

	echo '<div style="width: 700px; margin: 0 auto; border: none; background: #fff url(\'' . EDDPDFI_PLUGIN_URL . 'templates/lines/lines.jpg\') repeat-y;">';
		echo '<div id="edd-email-content" style="padding: 10px 10px 10px 130px;">';
			if ( isset( $edd_options['email_logo'] ) ) {
				echo '<img src="' . $edd_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			} else if ( isset( $edd_options['eddpdfi_email_logo'] ) ) {
				echo '<img src="' . $edd_options['eddpdfi_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="margin-top: 0; color: #de3b1e; font-size: 28px; line-height: 32px; font-weight: normal;">' . __( 'Receipt', 'eddpdfi' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'edd_email_template_lines', 'eddpdfi_lines' );

/**
 * Lines Email Template Extra Styling
 *
 * Overrides the default invoice template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function eddpdfi_lines_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="color: #de3b1e; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #de3b1e; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #de3b1e; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="border-left: 2px solid #f0e6dc; padding-left: 5px; line-height: 21px;">', $email_body );

	return $email_body;
}
add_filter( 'edd_purchase_receipt_lines', 'eddpdfi_lines_extra_styling' );


/**
 * Minimal Email Template
 *
 * @since 1.0
 */
function eddpdfi_minimal() {
	global $edd_options;

	echo '<div style="width: 550px; margin: 0 auto; border: none; background: #fff; border-left: 2px solid #f0e6dc;">';
		echo '<div id="edd-email-content" style="padding: 10px;">';
			if ( isset( $edd_options['email_logo'] ) ) {
				echo '<img src="' . $edd_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			} else if ( isset( $edd_options['eddpdfi_email_logo'] ) ) {
				echo '<img src="' . $edd_options['eddpdfi_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="margin-top: 0; color: #de3b1e; font-size: 28px; line-height: 32px; font-weight: normal;">' . __( 'Receipt', 'eddpdfi' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'edd_email_template_minimal', 'eddpdfi_minimal' );

/**
 * Minimal Email Template Extra Styling
 *
 * Overrides the default invoice template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function eddpdfi_minimal_extra_styling( $email_body ) {
	return $email_body;
}
add_filter('edd_purchase_receipt_minimal', 'eddpdfi_minimal_extra_styling');


/**
 * Traditional Email Template
 *
 * @since 1.0
*/
function eddpdfi_traditional() {
	global $edd_options;

	echo '<div style="width: 660px; margin: 0 auto; border: none; background: #fff url(\''. EDDPDFI_PLUGIN_URL .'templates/traditional/header_background.jpg\') repeat-x;">';
		echo '<div id="edd-email-content" style="padding: 10px;">';
			if ( isset( $edd_options['email_logo'] ) ) {
				echo '<img src="' . $edd_options['email_logo'] . '" style="margin:10px 0 0 2px;position:relative;z-index:2;"/>';
			} else if ( isset( $edd_options['eddpdfi_email_logo'] ) ) {
				echo '<img src="' . $edd_options['eddpdfi_email_logo'] . '" style="margin:10px 0 0 2px;position:relative;z-index:2;"/>';
			}
			echo '<h1 style="margin-top: 12px; color: #fff; text-transform: uppercase; font-family: Times News Roman, Times, serif; font-size: 28px; line-height: 32px; font-weight: normal;">' . __( 'Receipt', 'eddpdfi' ) .'</h1>';
			echo '{email}';
		echo '</div>';
	echo '</div>';
}
add_filter( 'edd_email_template_traditional', 'eddpdfi_traditional' );

/**
 * Traditional Email Template Extra Styling
 *
 * Overrides the default invoice template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function eddpdfi_traditional_extra_styling( $email_body ) {
	$email_body = str_replace( '<h1>', '<h1 style="font-family: Times New Roman, Times, serif; color: #323232; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="font-family: Times New Roman, Times, serif; color: #323232; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="font-family: Times New Roman, Times, serif; color: #323232; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="font-size: 14px; line-height: 21px; font-family: Times New Roman, Times, serif; color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="font-size: 14px; font-family: Times New Roman, Times, serif; margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="font-size: 14px; line-height: 21px; font-family: Times New Roman, Times, serif; list-style: square;">', $email_body );
	$email_body = str_replace( '<p>', '<p style="font-size: 14px; line-height: 21px; font-family: Times New Roman, Times, serif;">', $email_body );

	return $email_body;
}
add_filter( 'edd_purchase_receipt_traditional', 'eddpdfi_traditional_extra_styling' );



/**
 * Different Colored Email Templates
 *
 * @since 1.0
*/
function eddpdfi_colors() {
	global $edd_options;

	switch ( $edd_options['email_template'] ) {

		case 'invoice_blue':
			$colors = array(
				'emphasis' => '479bc6',
				'title' => '479bc6',
				'header' => 'cae2ee',
				'border' => 'a6cde2'
			);
		break;

		case 'invoice_red':
			$colors = array(
				'emphasis' => 'c64747',
				'title' => 'c00000',
				'header' => 'eecaca',
				'border' => 'e2a6a6'
			);
		break;

		case 'invoice_green':
			$colors = array(
				'emphasis' => '47c662',
				'title' => '00c044',
				'header' => 'caeed4',
				'border' => 'a6e2b3'
			);
		break;

		case 'invoice_orange':
			$colors = array(
				'emphasis' => 'c68647',
				'title' => 'c05100',
				'header' => 'eedbca',
				'border' => 'e2cba6'
			);
		break;

		case 'invoice_yellow':
			$colors = array(
				'emphasis' => 'c5c647',
				'title' => 'eae80b',
				'header' => 'eeeeca',
				'border' => 'e2c1a6'
			);
		break;

		case 'invoice_purple':
			$colors = array(
				'emphasis' => '8947c6',
				'title' => '4800c0',
				'header' => 'd0caee',
				'border' => 'bda6e2'
			);
		break;

		case 'invoice_pink':
			$colors = array(
				'emphasis' => 'c64798',
				'title' => '5c0041',
				'header' => 'eecae8',
				'border' => 'e2a6d5'
			);
		break;

	}

	echo '<div style="width: 550px; background: #'.$colors['header'].'; border: 1px solid #'. $colors['emphasis'] .'; margin: 0 auto; padding: 4px; outline: none;">';
		echo '<div style="padding: 1px; background: #fff; border: 1px solid #fff;">';
			echo '<div id="edd-email-content" style="padding: 10px; background: #fff; border: 1px solid #'. $colors['border'] .';">';
				if ( isset( $edd_options['email_logo'] ) ) {
					echo '<img src="' . $edd_options['email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				} else if ( isset( $edd_options['eddpdfi_email_logo'] ) ) {
					echo '<img src="' . $edd_options['eddpdfi_email_logo'] . '" style="margin:0;position:relative;z-index:2;"/>';
				}
				echo '<h1 style="color: #'. $colors['title'] .'; font-size: 24px; font-weight: normal;">' . __( 'Receipt', 'eddpdfi' ) .'</h1>';
				echo '{email}';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
add_filter( 'edd_email_template_invoice_blue', 'eddpdfi_colors' );
add_filter( 'edd_email_template_invoice_red', 'eddpdfi_colors' );
add_filter( 'edd_email_template_invoice_green', 'eddpdfi_colors' );
add_filter( 'edd_email_template_invoice_orange', 'eddpdfi_colors' );
add_filter( 'edd_email_template_invoice_yellow', 'eddpdfi_colors' );
add_filter( 'edd_email_template_invoice_purple', 'eddpdfi_colors' );
add_filter( 'edd_email_template_invoice_pink', 'eddpdfi_colors' );

/**
 * Different Colored Email Template Extra Styling
 *
 * Overrides the default invoice template styling set by EDD
 *
 * @since 1.0
 *
 * @param string $email_body All the body text of the email to be sent
 *
 * @return string $email_body All the body text of the email to be sent
 */
function eddpdfi_colors_extra_styling( $email_body ) {
	global $edd_options;

	switch ( $edd_options['email_template'] ) {

		case 'invoice_blue':
			$colors = array('title' => '479bc6');
		break;

		case 'invoice_red':
			$colors = array('title' => 'c00000');
		break;

		case 'invoice_green':
			$colors = array('title' => '00c044');
		break;

		case 'invoice_orange':
			$colors = array('title' => 'c05100');
		break;

		case 'invoice_yellow':
			$colors = array('title' => 'eae80b');
		break;

		case 'invoice_purple':
			$colors = array('title' => '4800c0');
		break;

		case 'invoice_pink':
			$colors = array('title' => '5c0041');
		break;

	}

	$email_body = str_replace( '<h1>', '<h1 style="color: #'. $colors['title'] .'; line-height: 24px; font-weight: normal; font-size: 24px;">', $email_body );
	$email_body = str_replace( '<h2>', '<h2 style="color: #'. $colors['title'] .'; line-height: 20px; font-weight: normal; font-size: 20px;">', $email_body );
	$email_body = str_replace( '<h3>', '<h3 style="color: #'. $colors['title'] .'; line-height: 18px; font-weight: normal; font-size: 18px;">', $email_body );
	$email_body = str_replace( '<a', '<a style="line-height: 21px; color: #296698; text-decoration: none;"', $email_body );
	$email_body = str_replace( '<ul>', '<ul style="margin: 0 0 0 20px; padding: 0;">', $email_body );
	$email_body = str_replace( '<li>', '<li style="line-height: 21px;  list-style: square;">', $email_body );

	return $email_body;
}
add_filter( 'edd_purchase_receipt_invoice_blue', 'eddpdfi_colors_extra_styling' );
add_filter( 'edd_purchase_receipt_invoice_red', 'eddpdfi_colors_extra_styling' );
add_filter( 'edd_purchase_receipt_invoice_green', 'eddpdfi_colors_extra_styling' );
add_filter( 'edd_purchase_receipt_invoice_orange', 'eddpdfi_colors_extra_styling' );
add_filter( 'edd_purchase_receipt_invoice_yellow', 'eddpdfi_colors_extra_styling' );
add_filter( 'edd_purchase_receipt_invoice_purple', 'eddpdfi_colors_extra_styling' );
add_filter( 'edd_purchase_receipt_invoice_pink', 'eddpdfi_colors_extra_styling' );