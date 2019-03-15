<?php
/**
 * Template Functions
 *
 * All the template functions for the PDF invoice when they are being built or
 * generated.
 *
 * @package Easy Digital Downloads PDF Invoices
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Settings
 *
 * Gets the settings for PDF Invoices plugin if they exist.
 *
 * @since 1.0
 *
 * @param object $eddpdfi_pdf PDF invoice object
 * @param string $setting Setting name
 *
 * @return string Returns option if it exists.
 */
function eddpdfi_get_settings( $eddpdfi_pdf, $setting ) {
	global $edd_options;

	$eddpdfi_payment = get_post( $_GET['purchase_id'] );

	if ( 'name' == $setting ) {
		if ( isset( $edd_options['eddpdfi_name'] ) ) {
			return $edd_options['eddpdfi_name'];
		}
	}

	if ( 'addr_line1' == $setting ) {
		if ( isset( $edd_options['eddpdfi_address_line1'] ) ) {
			return $edd_options['eddpdfi_address_line1'];
		}
	}

	if ( 'addr_line2' == $setting ) {
		if ( isset( $edd_options['eddpdfi_address_line2'] ) ) {
			return $edd_options['eddpdfi_address_line2'];
		}
	}

	if ( 'city_state_zip' == $setting ) {
		if ( isset( $edd_options['eddpdfi_address_city_state_zip'] ) ) {
			return $edd_options['eddpdfi_address_city_state_zip'];
		}
	}

	if ( 'country' == $setting ) {
		if ( isset( $edd_options['eddpdfi_address_country'] ) ) {
			$country_name = edd_get_country_name( $edd_options['eddpdfi_address_country'] );
			return $country_name;
		}
	}

	if ( 'email' == $setting ) {
		if ( isset( $edd_options['eddpdfi_email_address'] ) ) {
			return $edd_options['eddpdfi_email_address'];
		}
	}

    if ( 'invoice_heading' == $setting ) {
        if ( isset( $edd_options['eddpdfi_invoice_heading'] ) ) {
            return $edd_options['eddpdfi_invoice_heading'];
        } else {
            return __( 'Invoice', 'eddpdfi' );
        }
    }

    if ( 'tax_label' == $setting ) {
        if ( isset( $edd_options['eddpdfi_tax_label'] ) ) {
            return $edd_options['eddpdfi_tax_label'];
        } else {
            return __( 'Tax', 'eddpdfi' );
        }
    }

	if ( 'notes' == $setting ) {
		if ( isset( $edd_options['eddpdfi_additional_notes'] ) && ! empty( $edd_options['eddpdfi_additional_notes'] ) ) {
			$eddpdfi_additional_notes = $edd_options['eddpdfi_additional_notes'];
			$eddpdfi_additional_notes = str_replace( '{page}', 'Page [[page_cu]]', $eddpdfi_additional_notes );
			$eddpdfi_additional_notes = str_replace( '{sitename}', get_bloginfo('name'), $eddpdfi_additional_notes );
			$eddpdfi_additional_notes = str_replace( '{today}', date_i18n( get_option('date_format'), time() ), $eddpdfi_additional_notes );
			$eddpdfi_additional_notes = str_replace( '{date}', date_i18n( get_option('date_format'), strtotime( $eddpdfi_payment->post_date ) ), $eddpdfi_additional_notes );
			$eddpdfi_additional_notes = str_replace( '{invoice_id}', eddpdfi_get_payment_number( $eddpdfi_payment->ID ), $eddpdfi_additional_notes );
			$eddpdfi_additional_notes = strip_tags( $eddpdfi_additional_notes );
			$eddpdfi_additional_notes = stripslashes_deep( html_entity_decode( $eddpdfi_additional_notes, ENT_COMPAT, 'UTF-8' ) );

			return $eddpdfi_additional_notes;
		}
	}
	return '';
}

/**
 * Calculate Line Heights
 *
 * Calculates the line heights for the 'To' block
 *
 * @since 1.0
 *
 * @param string $setting Setting name.
 *
 * @return string Returns line height.
 */
function eddpdfi_calculate_line_height( $setting ) {
	if ( empty( $setting ) ) {
		return 0;
	} else {
		return 6;
	}
}

/**
 * Determines if EDD cart quantities as enabled.
 *
 * This is just a wrapper to deal with the fact that EDD had a typo in 1.8.4 that was fixed after 1.8.4
 *
 * @since 2.1.6
 *
 * @return bool
 */
function eddpdfi_item_quantities_enabled() {
	if( function_exists( 'edd_item_quantities_enabled' ) ) {
		return edd_item_quantities_enabled();
	} elseif( function_exists( 'edd_item_quantities_enabled' ) ) {
		return edd_item_quantities_enabled();
	} else {
		return false;
	}
}

/**
 * Retrieve the payment number
 *
 * If sequential order numbers are enabled (EDD 2.0+), this returns the order numbeer
 *
 * @since 2.2
 *
 * @return int|string
 */
function eddpdfi_get_payment_number( $payment_id = 0 ) {
	if( function_exists( 'edd_get_payment_number' ) ) {
		return edd_get_payment_number( $payment_id );
	} else {
		return $payment_id;
	}
}