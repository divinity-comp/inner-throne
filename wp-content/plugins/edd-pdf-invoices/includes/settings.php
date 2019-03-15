<?php
/**
 * Settings
 *
 * Registers all the settings required for the plugin.
 *
 * @package Easy Digital Downloads PDF Invoices
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers the settings section
 *
 * @since 2.2.20
 *
 * @param array $sections Array of EDD Extensions settings sections
 *
 * @return array The modified EDD Extensions settings section array
 */
function eddpdfi_settings_section( $sections ) {
	$sections['eddpdfi-settings'] = __( 'PDF Invoices', 'eddpdfi' );
	return $sections;
}
add_filter( 'edd_settings_sections_extensions', 'eddpdfi_settings_section' );

/**
 * Add Settings
 *
 * Adds the new settings for the plugin
 *
 * @since 1.0
 *
 * @param array $settings Array of pre-defined setttings
 *
 * @return array Merged array with new settings
 */
function eddpdfi_add_settings( $settings ) {
	$eddpdfi_settings = array(
		array(
			'id'   => 'eddpdfi_settings',
			'name' => '<strong>' . __( 'PDF Invoice Settings', 'eddpdfi' ) . '</strong>',
			'desc' => __( 'Configure the PDF invoice settings', 'eddpdfi' ),
			'type' => 'header'
		),
		array(
			'id'      => 'eddpdfi_disable_invoices_on_free_downloads',
			'name'    => __( 'Disable Invoices for Free Downloads', 'eddpdfi' ),
			'desc'    => __( 'Check this box to disable invoices from being generated for free downloads.', 'eddpdfi' ),
			'type'    => 'checkbox'
		),
		array(
			'id'      => 'eddpdfi_templates',
			'name'    => __( 'Invoice Template', 'eddpdfi' ),
			'desc'    => __( 'Choose a template for the invoice', 'eddpdfi' ),
			'type'    => 'select',
			'options' => apply_filters( 'eddpdfi_templates_list', array(
				'default'     => __( 'Default', 'eddpdfi' ),
				'blue_stripe' => __( 'Blue Stripe', 'eddpdfi' ),
				'lines'       => __( 'Lines', 'eddpdfi' ),
				'minimal'     => __( 'Minimal', 'eddpdfi' ),
				'traditional' => __( 'Traditional', 'eddpdfi' ),
				'blue'        => __( 'Blue', 'eddpdfi' ),
				'green'       => __( 'Green', 'eddpdfi' ),
				'orange'      => __( 'Orange', 'eddpdfi' ),
				'pink'        => __( 'Pink', 'eddpdfi' ),
				'purple'      => __( 'Purple', 'eddpdfi' ),
				'red'         => __( 'Red', 'eddpdfi' ),
				'yellow'      => __( 'Yellow', 'eddpdfi' )
			) )
		),
		array(
			'id'   => 'eddpdfi_enable_char_support',
			'name' => __( 'Characters not displaying correctly?', 'eddpdfi' ),
			'desc' => __( 'Check to enable the Free Sans/Free Serif font replacing Open Sans/Helvetica/Times. Only do this if you have characters which do not display correctly (e.g. Greek characters)', 'eddpdfi' ),
			'type' => 'checkbox',
		),
		array(
			'id'   => 'eddpdfi_logo_upload',
			'name' => __( 'Logo Upload', 'eddpdfi' ),
			'desc' => __( 'Upload your logo here which will show up on the invoice. If the logo is greater than 90px in height, it will not be shown. On the Traditional template, if the logo is greater than 80px in height, it will not be shown. Also note that the logo will be output at 96 dpi.', 'eddpdfi' ),
			'type' => 'upload'
		),
		array(
			'id'   => 'eddpdfi_company_name',
			'name' => __( 'Company Name', 'eddpdfi' ),
			'desc' => __( 'Enter the company name that will be shown on the invoice.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => ''
		),
		array(
			'id'   => 'eddpdfi_name',
			'name' => __( 'Name', 'eddpdfi' ),
			'desc' => __( 'Enter the name that will be shown on the invoice.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => ''
		),
		array(
			'id'   => 'eddpdfi_address_line1',
			'name' => __( 'Address Line 1', 'eddpdfi' ),
			'desc' => __( 'Enter the first address line that will appear on the invoice.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => ''
		),
		array(
			'id'   => 'eddpdfi_address_line2',
			'name' => __( 'Address Line 2', 'eddpdfi' ),
			'desc' => __( 'Enter the second address line that will appear on the invoice.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => ''
		),
		array(
			'id'   => 'eddpdfi_address_city_state_zip',
			'name' => __( 'City, State and Zip Code', 'eddpdfi' ),
			'desc' => __( 'Enter the city, state and zip code that will appear on the invoice.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => ''
		),
		array(
			'id'   => 'eddpdfi_address_country',
			'name' => __( 'Country', 'eddpdfi' ),
			'desc' => __( 'Select the country that will appear on the invoice.', 'eddpdfi' ),
			'type'        => 'select',
			'options'     => edd_get_country_list(),
			'chosen'      => true,
			'placeholder' => __( 'Select a country', 'eddpdfi' ),
			'data'        => array(
				'nonce' => wp_create_nonce( 'edd-country-field-nonce' )
			)
		),
		array(
			'id'   => 'eddpdfi_email_address',
			'name' => __( 'Email Address', 'eddpdfi' ),
			'desc' => __( 'Enter the email address that will appear on the invoice.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => get_option('admin_email')
		),
		array(
			'id'   => 'eddpdfi_url',
			'name' => __( 'Show website address?', 'eddpdfi' ),
			'desc' => __( 'Check this box if you would like your website address to be shown.', 'eddpdfi' ),
			'type' => 'checkbox'
		),
		array(
			'id'   => 'eddpdfi_header_message',
			'name' => __( 'Header Message', 'eddpdfi' ),
			'desc' => __( 'Enter the message you would like to be shown on the header of the invoice. Please note that the header will not show up on the Blue Stripe and Traditional template.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => ''
		),
		array(
			'id'   => 'eddpdfi_footer_message',
			'name' => __( 'Footer Message', 'eddpdfi' ),
			'desc' => __( 'Enter the message you would like to be shown on the footer of the invoice.', 'eddpdfi' ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => ''
		),
        array(
            'id'   => 'eddpdfi_invoice_heading',
            'name' => __( 'Invoice Heading', 'eddpdfi' ),
            'desc' => __( 'Enter the heading text you would like to be shown directly above invoice data. The default is <strong>Invoice</strong>.', 'eddpdfi' ),
            'type' => 'text',
            'size' => 'regular',
            'std'  => ''
        ),
        array(
            'id'   => 'eddpdfi_tax_label',
            'name' => __( 'Tax Label', 'eddpdfi' ),
            'desc' => __( 'Enter the text you would like to use when referring to tax. The default is <strong>tax</strong>.',
                'eddpdfi' ),
            'type' => 'text',
            'size' => 'regular',
            'std'  => ''
        ),
		array(
			'id'   => 'eddpdfi_template_tags',
			'name' => __( 'Template Tags', 'eddpdfi' ),
			'desc' => __( 'The following template tags will work for the Header and Footer message as well as the Additional Notes:', 'eddpdfi' ) . '<br />' .
				__( '{page} - Page Number', 'eddpdfi' ) . '<br />' .
				__( '{sitename} - Site Name', 'eddpdfi' ) . '<br />' .
				__( '{today} - Date of Invoice Generation', 'eddpdfi' ) . '<br />' .
				__( '{date} - Invoice Date', 'eddpdfi' ). '<br />' .
				__( '{invoice_id} - Invoice ID', 'eddpdfi' ),
			'type' => 'pdfi_plain_text'
		),
		array(
			'id'   => 'eddpdfi_additional_notes',
			'name' => __( 'Additional Notes', 'eddpdfi' ),
			'desc' => __( 'Enter any messages you would to be displayed at the end of the invoice. Only plain text is currently supported. Any HTML will not be shown on the invoice.', 'eddpdfi' ),
			'type' => 'rich_editor'
		),
		array(
			'id'   => 'eddpdfi_email_settings',
			'name' => '<strong>' . __( 'PDF Invoice Email Settings', 'eddpdfi' ) . '</strong>',
			'type' => 'header'
		),
		array(
			'id'   => 'eddpdfi_email_logo',
			'name' => __( 'Email Logo', 'eddpdfi' ),
			'desc' => __( 'Upload or choose a logo to be displayed at the top of the email', 'eddpdfi' ),
			'type' => 'upload',
			'size' => 'regular'
		)
	);

	if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
		$eddpdfi_settings = array( 'eddpdfi-settings' => $eddpdfi_settings );
	}

	return array_merge( $settings, $eddpdfi_settings );
}
add_filter( 'edd_settings_extensions', 'eddpdfi_add_settings' );

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since       1.0
*/

function edd_pdfi_plain_text_callback( $args ) {
	echo $args['desc'];
}