<?php
/**
 * Traditional PDF Invoice Template
 *
 * Builds and renders the traditional PDF invoice template .
 *
 * @since 1.0
 *
 * @uses HTML2PDF
 * @uses TCPDF
 *
 * @param object $eddpdfi_pdf PDF Invoice Object
 * @param object $eddpdfi_payment Payment Data Object
 * @param array $eddpdfi_payment_meta Payment Meta
 * @param array $eddpdfi_buyer_info Buyer Info
 * @param string $eddpdfi_payment_gateway Payment Gateway
 * @param string $eddpdfi_payment_method Payment Method
 * @param string $company_name Company Name
 * @param string $eddpdfi_payment_date Payment Date
 * @param string eddpdfi_payment_status Payment Status
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function eddpdfi_pdf_template_traditional( $eddpdfi_pdf, $eddpdfi_payment, $eddpdfi_payment_meta, $eddpdfi_buyer_info, $eddpdfi_payment_gateway, $eddpdfi_payment_method, $address_line_2_line_height, $company_name, $eddpdfi_payment_date, $eddpdfi_payment_status ) {
	global $edd_options;

	$payment_obj      = new EDD_Payment( $eddpdfi_payment->ID );
	$payment_meta     = $payment_obj->get_meta();
	$payment_currency = $payment_obj->currency;
	$cart_items       = $payment_obj->cart_details;
	$customer_id      = $payment_obj->customer_id;
	$customer         = new EDD_Customer( $customer_id );

	$eddpdfi_pdf->AddFont('times', '');
	$eddpdfi_pdf->AddFont('times', 'B');
	$eddpdfi_pdf->AddFont('times', 'BI');
	$eddpdfi_pdf->AddFont('times', 'I');

	$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'times';
	$fontb = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'helvetica';

	$eddpdfi_pdf->SetMargins( 8, 20, 8 );
	$eddpdfi_pdf->SetX( 20 );

	$eddpdfi_pdf->AddPage();

	$eddpdfi_pdf->Image( EDDPDFI_PLUGIN_URL . '/templates/traditional/header_background.jpg', 8, 20, 194, 32, 'JPEG', false, 'LTR', false, -72, 'L' );

	$eddpdfi_pdf->SetFont( $font, '', 22 );

	$eddpdfi_pdf->SetTextColor( 255, 255, 255 );

	$eddpdfi_pdf->SetY( 20 );

	if ( isset( $edd_options['eddpdfi_logo_upload'] ) && ! empty( $edd_options['eddpdfi_logo_upload'] ) ) {
		$eddpdfi_pdf->Image( $edd_options['eddpdfi_logo_upload'], 8.5, 26, '', '11', '', false, 'LTR', false, 96 );
	} else {
		$eddpdfi_pdf->SetY( 26 );
		$eddpdfi_pdf->SetFont( $fontb, '', 22 );
		$eddpdfi_pdf->SetTextColor( 255, 255, 255 );
		$eddpdfi_pdf->Cell( 0, 0, $company_name, 0, 2, 'L', false );
	}

	$eddpdfi_pdf->SetY( 42 );
	$eddpdfi_pdf->Cell( 0, 0, eddpdfi_get_settings( $eddpdfi_pdf, 'invoice_heading' ), 0, 2, 'L', false );

	$eddpdfi_pdf->SetXY( 8, 57 );

	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 0, 6, strtoupper( $eddpdfi_payment_date ), 0, 2, 'R', false );

	$eddpdfi_pdf->SetY( 57 );

	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 40, 6, __( 'INVOICE ID', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetFont( $font, '', 10 );
	$eddpdfi_pdf->Cell( 0, 6, eddpdfi_get_payment_number( $eddpdfi_payment->ID ), 0, 2, 'L', false );
	$eddpdfi_pdf->SetX( 8 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 40, 6, __( 'PURCHASE KEY', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetFont( $font, '', 10 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_meta['key'], 0, 2, 'L', false );
	$eddpdfi_pdf->SetX( 8 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 40, 6, __( 'PAYMENT STATUS', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetFont( $font, '', 10 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_status, 0, 2, 'L', false );
	$eddpdfi_pdf->SetX( 8 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 40, 6, __( 'PAYMENT METHOD', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetFont( $font, '', 10 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_method, 0, 2, 'L', false );


	$eddpdfi_pdf->SetXY( 8, 90 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 0, 12, __( 'FROM:', 'eddpdfi' ), 0, 2, 'R', false );
	$eddpdfi_pdf->SetFont( $font, '', 10 );

	$item_list_spacing = 0;
	if ( ! empty( $edd_options['eddpdfi_name'] ) ) {
		$item_list_spacing += eddpdfi_calculate_line_height($edd_options['eddpdfi_name']);
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_name']), eddpdfi_get_settings($eddpdfi_pdf, 'name'), 0, 2, 'R', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_line1'] ) ) {
		$item_list_spacing += eddpdfi_calculate_line_height($edd_options['eddpdfi_address_line1']);
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_address_line1']), eddpdfi_get_settings($eddpdfi_pdf, 'addr_line1'), 0, 2, 'R', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_line2'] ) ) {
		$item_list_spacing += eddpdfi_calculate_line_height($edd_options['eddpdfi_address_line2']);
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_address_line2']), eddpdfi_get_settings($eddpdfi_pdf, 'addr_line2'), 0, 2, 'R', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_city_state_zip'] ) ) {
		$item_list_spacing += eddpdfi_calculate_line_height($edd_options['eddpdfi_address_city_state_zip']);
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_address_city_state_zip']), eddpdfi_get_settings($eddpdfi_pdf, 'city_state_zip'), 0, 2, 'R', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_country'] ) ) {
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height( $edd_options['eddpdfi_address_country'] ), eddpdfi_get_settings( $eddpdfi_pdf, 'country'), 0, 2, 'R', false );
	}
	if ( ! empty( $edd_options['eddpdfi_email_address'] ) ) {
		$eddpdfi_pdf->SetTextColor( 41, 102, 152 );
		$item_list_spacing += eddpdfi_calculate_line_height($edd_options['eddpdfi_email_address']);
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_email_address']), eddpdfi_get_settings($eddpdfi_pdf, 'email'), 0, 2, 'R', false );
	}
	if ( isset( $edd_options['eddpdfi_url'] ) && $edd_options['eddpdfi_url'] ) {
		$eddpdfi_pdf->SetTextColor( 41, 102, 152 );
		$item_list_spacing += 6;
		$eddpdfi_pdf->Cell( 0, 6, home_url(), 0, 2, 'R', false );
	}
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );

	$eddpdfi_pdf->Ln( 12 );

	$eddpdfi_pdf->Ln();
	$eddpdfi_pdf->SetXY( 8, 90 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 0, 12, __( 'TO:', 'eddpdfi' ), 0, 2, 'L', false );
	$eddpdfi_pdf->SetFont( $font, '', 10 );
	$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($customer->name), $customer->name, 0, 2, 'L', false );
	$eddpdfi_pdf->SetTextColor( 41, 102, 152 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_meta['email'], 0, 2, 'L', false );
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );

	if ( ! empty( $eddpdfi_buyer_info['address'] ) ) {
		$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_buyer_info['address']['line1'], 0, 2, 'L', false );
		if ( ! empty( $eddpdfi_buyer_info['address']['line2'] ) ) $eddpdfi_pdf->Cell( 0, 0, $eddpdfi_buyer_info['address']['line2'], 0, 2, 'L', false );
		$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_buyer_info['address']['city'] . ', ' . $eddpdfi_buyer_info['address']['state'] . ' ' . $eddpdfi_buyer_info['address']['zip'], 0, 2, 'L', false );
		if( ! empty( $eddpdfi_buyer_info['address']['country'] ) ) {
			$countries = edd_get_country_list();
$country   = isset( $countries[ $eddpdfi_buyer_info['address']['country'] ] ) ? $countries[ $eddpdfi_buyer_info['address']['country'] ] : $eddpdfi_buyer_info['address']['country'];
$eddpdfi_pdf->Cell( 0, 6, $country, 0, 2, 'L', false );
		}
	}

	$eddpdfi_pdf->Ln( $item_list_spacing );

	if ( isset( $edd_options['eddpdfi_url'] ) && $edd_options['eddpdfi_url'] ) {
		$eddpdfi_pdf->SetX( 8 );
	} else {
		$eddpdfi_pdf->SetX( 8 );
	}

	$eddpdfi_pdf->SetDrawColor( 0, 0, 0 );
	$eddpdfi_pdf->SetFont( $font, 'B', 11 );
	$eddpdfi_pdf->Cell( 193, 8, __( 'INVOICE ITEMS', 'eddpdfi' ), 1, 2, 'C', false );

	$eddpdfi_pdf->Ln( 0.2 );

	$eddpdfi_pdf->SetX( 8 );

	$eddpdfi_pdf->SetDrawColor( 0, 0, 0 );
	$eddpdfi_pdf->SetFont( $font, '', 9 );

	if ( eddpdfi_item_quantities_enabled() ) {
		$eddpdfi_pdf->Cell( 130, 7, __( 'Product Name', 'eddpdfi' ), 'BRL', 0, 'C', false );
		$eddpdfi_pdf->Cell( 20, 7, __( 'Quantity', 'eddpdfi' ), 'BRL', 0, 'C', false );
		$eddpdfi_pdf->Cell( 43, 7, __( 'Price', 'eddpdfi' ), 'BR', 0, 'C', false );
	} else {
		$eddpdfi_pdf->Cell( 150, 7, __( 'Product Name', 'eddpdfi' ), 'BRL', 0, 'C', false );
		$eddpdfi_pdf->Cell( 43, 7, __( 'Price', 'eddpdfi' ), 'BR', 0, 'C', false );
	}

	$eddpdfi_pdf->Ln( 0.2 );

	$eddpdfi_pdf_downloads = isset( $eddpdfi_payment_meta['cart_details'] ) ? $eddpdfi_payment_meta['cart_details'] : false;

	$eddpdfi_pdf->Ln();

	if ( $eddpdfi_pdf_downloads ) :
		$eddpdfi_pdf->SetX( 8 );

		foreach ( $eddpdfi_pdf_downloads as $key => $cart_item ) {
			$eddpdfi_pdf->SetDrawColor( 0, 0, 0 );

			$eddpdfi_pdf->SetX( 8 );

			$eddpdfi_pdf->SetFont( $font, '', 10 );

			$payment_id   = $eddpdfi_payment->ID;
			$item         = get_post( $payment_id );
			$user_info    = edd_get_payment_meta_user_info( $payment_id );
			$user_id      = $payment_obj->user_id;
			$payment_date = strtotime( $payment_obj->date );
			$price_id     = isset( $cart_item['item_number']['options']['price_id'] ) ? $cart_item['item_number']['options']['price_id'] : null;

			$eddpdfi_download_id = isset( $eddpdfi_payment_meta['cart_details'] ) ? $cart_item['id'] : $cart_item;
			$user_info = $eddpdfi_payment_meta['user_info'];
			$eddpdfi_final_download_price = isset( $cart_item['subtotal'] ) ? $cart_item['subtotal'] : null;

			$item_id    = isset( $cart_item['id']    ) ? $cart_item['id'] : $cart_item;
			$price      = isset( $cart_item['price'] ) ? $cart_item['price'] : false;
			$item_price = isset( $cart_item['item_price'] ) ? $cart_item['item_price'] : $price;
			$quantity   = isset( $cart_item['quantity'] ) && $cart_item['quantity'] > 0 ? $cart_item['quantity'] : 1;

			if ( is_null( $eddpdfi_final_download_price ) ) {
				$eddpdfi_final_download_price = isset( $cart_item['price'] ) ? $cart_item['price'] : null;
			}

			if ( isset( $user_info['discount'] ) && $user_info['discount'] != 'none') {
				$eddpdfi_discount =  $user_info['discount'];
			} else {
				$eddpdfi_discount = __( 'None', 'eddpdfi' );
			}

			$eddpdfi_total_price = edd_currency_filter( edd_format_amount( $payment_obj->total ), $payment_currency );
			$eddpdfi_total_price = html_entity_decode( $eddpdfi_total_price, ENT_COMPAT, 'UTF-8'  );

			$eddpdfi_download_title = html_entity_decode( get_the_title( $eddpdfi_download_id ), ENT_COMPAT, 'UTF-8' );

			if ( edd_has_variable_prices( $item_id ) && isset( $price_id ) ) {
				$eddpdfi_download_title .= ' - ' . edd_get_price_option_name( $eddpdfi_download_id, $price_id, $payment_id );
			}

			if ( edd_get_payment_meta( $payment_id, '_edd_sl_is_renewal', true ) ) {
				$eddpdfi_download_title .= "\n" . __( 'License Renewal Discount:', 'eddpdfi' ) . ' ' . html_entity_decode( edd_currency_filter( edd_format_amount( $cart_item['discount'] ), $payment_currency ), ENT_COMPAT, 'UTF-8'  );
			}

			$eddpdfi_download_price = ' ' . html_entity_decode( edd_currency_filter( edd_format_amount( $price ), $payment_currency ), ENT_COMPAT, 'UTF-8'  );

			$dimensions = $eddpdfi_pdf->getPageDimensions();
			$has_border = false;
			$linecount = $eddpdfi_pdf->getNumLines( $eddpdfi_download_title, 82 );

			if ( eddpdfi_item_quantities_enabled() ) {
				$eddpdfi_pdf->MultiCell( 130, $linecount * 4, $eddpdfi_download_title, 'L', 'C', false, 0, 8 );
				$eddpdfi_pdf->Cell( 20, 8, $cart_item['quantity'], 'LR', 0, 'C', false );
				$eddpdfi_pdf->Cell( 43, 8, $eddpdfi_download_price, 'R', 2, 'C', false );
			} else {
				$eddpdfi_pdf->MultiCell( 150, $linecount * 4, $eddpdfi_download_title, 'L', 'C', false, 0, 8 );
				$eddpdfi_pdf->Cell( 43, 8, $eddpdfi_download_price, 'R', 2, 'C', false );
			}
		}

		$eddpdfi_pdf->SetX( 8 );

		$eddpdfi_pdf->SetDrawColor( 0, 0, 0 );
		$eddpdfi_pdf->SetFont( $font, 'B', 10 );

		$eddpdfi_pdf->Ln( 0.2 );

		do_action( 'eddpdfi_additional_fields', $eddpdfi_pdf, $eddpdfi_payment, $eddpdfi_payment_meta, $eddpdfi_buyer_info, $eddpdfi_payment_gateway, $eddpdfi_payment_method, $address_line_2_line_height, $company_name, $eddpdfi_payment_date, $eddpdfi_payment_status );

		$subtotal = html_entity_decode( edd_payment_subtotal( $eddpdfi_payment->ID ), ENT_COMPAT, 'UTF-8' );
		$tax      = html_entity_decode( edd_payment_tax( $eddpdfi_payment->ID ), ENT_COMPAT, 'UTF-8' );

		$eddpdfi_pdf->SetX( 8 );

		$eddpdfi_pdf->Cell( 150, 8, __( 'Subtotal', 'eddpdfi' ) . '  ', 'TLBR', 0, 'R', false );
		$eddpdfi_pdf->Cell( 43, 8, $subtotal, 'BTR', 2, 'C', false );

		if ( edd_use_taxes() ) {
			$eddpdfi_pdf->SetX( 8 );
			$eddpdfi_pdf->Cell( 150, 8, eddpdfi_get_settings( $eddpdfi_pdf, 'tax_label' ) . '  ', 'TLBR', 0, 'R', false );
			$eddpdfi_pdf->Cell( 43, 8, $tax, 'BTR', 2, 'C', false );
		}

		$fees = edd_get_payment_fees( $eddpdfi_payment->ID );
		if ( ! empty ( $fees ) ) {
			foreach( $fees as $fee ) {
				$fee_amount = html_entity_decode( edd_currency_filter( $fee['amount'], $payment_currency ), ENT_COMPAT, 'UTF-8'  );

				$eddpdfi_pdf->SetX( 8 );
				$eddpdfi_pdf->Cell( 150, 8, $fee['label'], 'TLBR', 0, 'R', false );
				$eddpdfi_pdf->Cell( 43, 8, $fee_amount, 'BTR', 2, 'C', true );
			}
		}

		$was_renewal = edd_get_payment_meta( $payment_id, '_edd_sl_is_renewal', true );
		if ( $was_renewal ) {
			$eddpdfi_pdf->SetX( 8 );
			$eddpdfi_pdf->Cell( 150, 8, __( 'Was Renewal', 'eddpdfi' ), 'TLBR', 0, 'R', false );
			$eddpdfi_pdf->Cell( 43, 8, ( $was_renewal ? __( 'Yes', 'eddpdfi' ) : __( 'No', 'eddpdfi' ) ), 'BTR', 2, 'C', false );
		}

		$eddpdfi_pdf->SetX( 8 );
		$eddpdfi_pdf->Cell( 150, 8, __( 'Discount Used', 'eddpdfi' ) . '  ', 'TLBR', 0, 'R', false );
		$eddpdfi_pdf->Cell( 43, 8, $eddpdfi_discount, 'BTR', 2, 'C', false );

		$total = html_entity_decode( edd_currency_filter( edd_format_amount( $payment_obj->total ), $payment_currency ), ENT_COMPAT, 'UTF-8'  );

		$eddpdfi_pdf->SetX( 8 );
		$eddpdfi_pdf->SetFont( $font, 'B', 11 );
		$eddpdfi_pdf->Cell( 150, 10, __( 'Total Paid', 'eddpdfi' ) . '  ', 'BLR', 0, 'R', false );
		$eddpdfi_pdf->Cell( 43, 10, $total, 'BR', 2, 'C', false );

		$eddpdfi_pdf->Ln( 10 );

		if ( isset( $edd_options['eddpdfi_additional_notes'] ) && !empty ( $edd_options['eddpdfi_additional_notes'] ) ) {

			$eddpdfi_pdf->SetX( 8 );
			$eddpdfi_pdf->SetFont( $font, '', 13 );
			$eddpdfi_pdf->Cell( 0, 6, __( 'ADDITIONAL NOTES:', 'eddpdfi' ), 0, 2, 'L', false );
			$eddpdfi_pdf->Ln(2);

			$eddpdfi_pdf->SetX( 8 );
			$eddpdfi_pdf->SetFont( $font, '', 10 );
			$eddpdfi_pdf->MultiCell( 0, 6, eddpdfi_get_settings($eddpdfi_pdf, 'notes'), 0, 'L', false );

		}

		$eddpdfi_pdf->Ln( 10 );

		$eddpdfi_pdf->SetFont( $font, 'B', 10 );
		$eddpdfi_pdf->Cell( 0, 8, __( 'THANK YOU FOR YOUR BUSINESS!', 'eddpdfi' ), 0, 2, 'C', false );

	endif;

}
add_action( 'eddpdfi_pdf_template_traditional', 'eddpdfi_pdf_template_traditional', 10, 10 );