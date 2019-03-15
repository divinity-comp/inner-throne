<?php
/**
 * Default PDF Invoice Template
 *
 * Builds and renders the default PDF invoice template .
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

function eddpdfi_pdf_template_default( $eddpdfi_pdf, $eddpdfi_payment, $eddpdfi_payment_meta, $eddpdfi_buyer_info, $eddpdfi_payment_gateway, $eddpdfi_payment_method, $address_line_2_line_height, $company_name, $eddpdfi_payment_date, $eddpdfi_payment_status ) {
	global $edd_options;

	$payment_obj      = new EDD_Payment( $eddpdfi_payment->ID );
	$payment_meta     = $payment_obj->get_meta();
	$payment_currency = $payment_obj->currency;
	$cart_items       = $payment_obj->cart_details;
	$customer_id      = $payment_obj->customer_id;
	$customer         = new EDD_Customer( $customer_id );

	$eddpdfi_pdf->SetMargins( 8, 8, 8 );
	$eddpdfi_pdf->SetX( 8 );

	$eddpdfi_pdf->AddPage();

	$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'Helvetica';

	$eddpdfi_pdf->Ln( 5 );

	if ( isset( $edd_options['eddpdfi_logo_upload'] ) && ! empty( $edd_options['eddpdfi_logo_upload'] ) ) {
		$eddpdfi_pdf->Image( $edd_options['eddpdfi_logo_upload'], 8, 20, '', '11', '', false, 'LTR', false, 96 );
	} else {
		$eddpdfi_pdf->SetFont( $font, '', 22 );
		$eddpdfi_pdf->SetTextColor( 50, 50, 50 );
		$eddpdfi_pdf->Cell( 0, 0, $company_name, 0, 2, 'L', false );
	}

	$eddpdfi_pdf->SetFont( $font, '', 18 );
	$eddpdfi_pdf->SetY(45);
	$eddpdfi_pdf->Cell( 0, 0, eddpdfi_get_settings( $eddpdfi_pdf, 'invoice_heading' ), 0, 2, 'L', false );

	$eddpdfi_pdf->SetXY( 8, 60 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 0, 6, __( 'From', 'eddpdfi' ), 0, 2, 'L', false );
	$eddpdfi_pdf->SetFont( $font, '', 10 );
	if ( ! empty( $edd_options['eddpdfi_name'] ) ) {
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_name']), eddpdfi_get_settings($eddpdfi_pdf, 'name'), 0, 2, 'L', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_line1'] ) ) {
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_address_line1']), eddpdfi_get_settings($eddpdfi_pdf, 'addr_line1'), 0, 2, 'L', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_line2'] ) ) {
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_address_line2']), eddpdfi_get_settings($eddpdfi_pdf, 'addr_line2'), 0, 2, 'L', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_city_state_zip'] ) ) {
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_address_city_state_zip']), eddpdfi_get_settings($eddpdfi_pdf, 'city_state_zip'), 0, 2, 'L', false );
	}
	if ( ! empty( $edd_options['eddpdfi_address_country'] ) ) {
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height( $edd_options['eddpdfi_address_country'] ), eddpdfi_get_settings( $eddpdfi_pdf, 'country'), 0, 2, 'L', false );
	}
	if ( ! empty( $edd_options['eddpdfi_email_address'] ) ) {
		$eddpdfi_pdf->SetTextColor( 41, 102, 152 );
		$eddpdfi_pdf->Cell( 0, eddpdfi_calculate_line_height($edd_options['eddpdfi_email_address']), eddpdfi_get_settings($eddpdfi_pdf, 'email'), 0, 2, 'L', false );
	}
	if ( isset( $edd_options['eddpdfi_url'] ) && $edd_options['eddpdfi_url'] ) {
		$eddpdfi_pdf->Cell( 0, 6, home_url(), 0, 2, 'L', false );
	}
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );

	$eddpdfi_pdf->Ln(12);

	$eddpdfi_pdf->Ln();
	$eddpdfi_pdf->SetXY( 60, 60 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 0, 6, __( 'To', 'eddpdfi' ), 0, 2, 'L', false );
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

	$eddpdfi_pdf->Ln( 5 );

	$eddpdfi_pdf->SetX( 60 );
	$eddpdfi_pdf->SetTextColor( 110, 110, 110 );
	$eddpdfi_pdf->Cell( 30, 6, __( 'Invoice Date', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_date, 0, 2, 'L', false );

	$eddpdfi_pdf->SetX( 60 );

	$eddpdfi_pdf->SetTextColor( 110, 110, 110 );
	$eddpdfi_pdf->Cell( 30, 6, __( 'Invoice ID', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );
	$eddpdfi_pdf->Cell( 0, 6, eddpdfi_get_payment_number( $eddpdfi_payment->ID ), 0, 2, 'L', false );
	$eddpdfi_pdf->SetX( 60 );
	$eddpdfi_pdf->SetTextColor( 110, 110, 110 );
	$eddpdfi_pdf->Cell( 30, 6, __( 'Purchase Key', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_meta['key'], 0, 2, 'L', false );
	$eddpdfi_pdf->SetX( 60 );
	$eddpdfi_pdf->SetTextColor( 110, 110, 110 );
	$eddpdfi_pdf->Cell( 30, 6, __( 'Payment Status', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_status, 0, 2, 'L', false );
	$eddpdfi_pdf->SetX( 60 );
	$eddpdfi_pdf->SetTextColor( 110, 110, 110 );
	$eddpdfi_pdf->Cell( 30, 6, __( 'Payment Method', 'eddpdfi' ), 0, 0, 'L', false );
	$eddpdfi_pdf->SetTextColor( 50, 50, 50 );
	$eddpdfi_pdf->Cell( 0, 6, $eddpdfi_payment_method, 0, 2, 'L', false );

	$eddpdfi_pdf->Ln( 5 );

	$eddpdfi_pdf->SetX( 61 );

	$eddpdfi_pdf->SetFillColor( 224, 224, 224 );
	$eddpdfi_pdf->SetDrawColor( 209, 209, 209 );
	$eddpdfi_pdf->SetFont( $font, 'B', 10 );
	$eddpdfi_pdf->Cell( 140, 8, __( 'Invoice Items', 'eddpdfi' ), 1, 2, 'C', true );

	$eddpdfi_pdf->Ln( 0.2 );

	$eddpdfi_pdf->SetX( 61 );

	$eddpdfi_pdf->SetDrawColor( 194, 221, 231 );
	$eddpdfi_pdf->SetFillColor( 238, 238, 238 );
	$eddpdfi_pdf->SetFont( $font, '', 9 );

	if ( eddpdfi_item_quantities_enabled() ) {
		$eddpdfi_pdf->Cell( 82, 7, __( 'PRODUCT NAME', 'eddpdfi' ), 'BLR', 0, 'C', false );
		$eddpdfi_pdf->Cell( 20, 7, __( 'QUANTITY', 'eddpdfi' ), 'BR', 0, 'C', false );
		$eddpdfi_pdf->Cell( 38, 7, __( 'PRICE', 'eddpdfi' ), 'BR', 0, 'C', false );
	} else {
		$eddpdfi_pdf->Cell( 102, 7, __( 'PRODUCT NAME', 'eddpdfi' ), 'BL', 0, 'C', false );
		$eddpdfi_pdf->Cell( 38, 7, __( 'PRICE', 'eddpdfi' ), 'BR', 0, 'C', false );
	}

	$eddpdfi_pdf->Ln( 0.2 );

	$eddpdfi_pdf_downloads = $payment_obj->cart_details;

	$eddpdfi_pdf->Ln();

	if ( $eddpdfi_pdf_downloads ) :
		$eddpdfi_pdf->SetX( 61 );

		foreach ( $eddpdfi_pdf_downloads as $key => $cart_item ) {
			$eddpdfi_pdf->SetDrawColor( 238, 238, 238 );

			$eddpdfi_pdf->SetX( 61 );

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

			if ( isset( $user_info['discount'] ) && $user_info['discount'] !== 'none') {
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
				$eddpdfi_pdf->MultiCell( 82, $linecount * 5, $eddpdfi_download_title, 0, 'L', false, 0, 61 );
				$eddpdfi_pdf->Cell( 20, $linecount * 5, $cart_item['quantity'], 'LB', 0, 'C', false );
				$eddpdfi_pdf->SetFillColor( 250, 250, 250 );
				$eddpdfi_pdf->Cell( 38, $linecount * 5, $eddpdfi_download_price, 'LB', 2, 'R', true );
				$eddpdfi_pdf->SetX( 61 );
				$eddpdfi_pdf->Cell( 102, 8, '', 'T' );
			} else {
				$eddpdfi_pdf->MultiCell( 102, $linecount * 5, $eddpdfi_download_title, 0, 'L', false, 0, 61 );
				$eddpdfi_pdf->SetFillColor( 250, 250, 250 );
				$eddpdfi_pdf->Cell( 38, $linecount * 5, $eddpdfi_download_price, 'B', 2, 'R', true );
				$eddpdfi_pdf->SetX( 61 );
				$eddpdfi_pdf->Cell( 102, 8, '', 'T' );
			}
		}

		$eddpdfi_pdf->Ln( 5 );
		$eddpdfi_pdf->SetX( 61 );

		$eddpdfi_pdf->SetFillColor( 224, 224, 224 );
		$eddpdfi_pdf->SetDrawColor( 209, 209, 209 );
		$eddpdfi_pdf->SetFont( $font, 'B', 10 );
		$eddpdfi_pdf->Cell( 140, 8, __( 'Invoice Totals', 'eddpdfi' ), 1, 2, 'C', true );

		$eddpdfi_pdf->Ln( 0.2 );

		$eddpdfi_pdf->SetDrawColor( 238, 238, 238 );
		$eddpdfi_pdf->SetFillColor( 250, 250, 250 );

		do_action( 'eddpdfi_additional_fields', $eddpdfi_pdf, $eddpdfi_payment, $eddpdfi_payment_meta, $eddpdfi_buyer_info, $eddpdfi_payment_gateway, $eddpdfi_payment_method, $address_line_2_line_height, $company_name, $eddpdfi_payment_date, $eddpdfi_payment_status );

		$subtotal = html_entity_decode( edd_payment_subtotal( $eddpdfi_payment->ID ), ENT_COMPAT, 'UTF-8'  );
		$tax      = html_entity_decode( edd_payment_tax( $eddpdfi_payment->ID ), ENT_COMPAT, 'UTF-8'  );

		$eddpdfi_pdf->SetX( 61 );
		$eddpdfi_pdf->Cell( 102, 8, __( 'Subtotal', 'eddpdfi' ), 'B', 0, 'L', false );
		$eddpdfi_pdf->Cell( 38, 8, $subtotal, 'B', 2, 'R', true );

		if ( edd_use_taxes() ) {
			$eddpdfi_pdf->SetX( 61 );
			$eddpdfi_pdf->Cell( 102, 8, eddpdfi_get_settings( $eddpdfi_pdf, 'tax_label' ), 'B', 0, 'L', false );
			$eddpdfi_pdf->Cell( 38, 8, $tax, 'B', 2, 'R', true );
		}

		$fees = edd_get_payment_fees( $eddpdfi_payment->ID );
		if ( ! empty ( $fees ) ) {
			foreach( $fees as $fee ) {
				$fee_amount = html_entity_decode( edd_currency_filter( $fee['amount'], $payment_currency ), ENT_COMPAT, 'UTF-8'  );
				$eddpdfi_pdf->SetX( 61 );
				$eddpdfi_pdf->Cell( 102, 8, $fee['label'], 'B', 0, 'L', false );
				$eddpdfi_pdf->Cell( 38, 8, $fee_amount, 'B', 2, 'R', true );
			}
		}

		$was_renewal = edd_get_payment_meta( $payment_id, '_edd_sl_is_renewal', true );
		if ( $was_renewal ) {
			$eddpdfi_pdf->SetX( 61 );
			$eddpdfi_pdf->Cell( 102, 8, __( 'Was Renewal', 'eddpdfi' ), 'B', 0, 'L', false );
			$eddpdfi_pdf->Cell( 38, 8, ( $was_renewal ? __( 'Yes', 'eddpdfi' ) : __( 'No', 'eddpdfi' ) ), 'B', 2, 'R', true );
		}

		$eddpdfi_pdf->SetX( 61 );
		$eddpdfi_pdf->Cell( 102, 8, __( 'Discount Used', 'eddpdfi' ), 'B', 0, 'L', false );
		$eddpdfi_pdf->Cell( 38, 8, $eddpdfi_discount, 'B', 2, 'R', true );

		$total = html_entity_decode( edd_currency_filter( edd_format_amount( $payment_obj->total ), $payment_currency ), ENT_COMPAT, 'UTF-8'  );

		$eddpdfi_pdf->SetX( 61 );
		$eddpdfi_pdf->SetFont( $font, 'B', 11 );
		$eddpdfi_pdf->Cell( 102, 10, __( 'Total Paid', 'eddpdfi' ), 'B', 0, 'L', false );
		$eddpdfi_pdf->Cell( 38, 10, $total, 'B', 2, 'R', true );

		$eddpdfi_pdf->Ln( 10 );

		if ( isset( $edd_options['eddpdfi_additional_notes'] ) && !empty ( $edd_options['eddpdfi_additional_notes'] ) ) {
			$eddpdfi_pdf->SetX( 60 );
			$eddpdfi_pdf->SetFont( $font, '', 13 );
			$eddpdfi_pdf->Cell( 0, 6, __( 'Additional Notes', 'eddpdfi' ), 0, 2, 'L', false );
			$eddpdfi_pdf->Ln(2);

			$eddpdfi_pdf->SetX( 60 );
			$eddpdfi_pdf->SetFont( $font, '', 10 );
			$eddpdfi_pdf->MultiCell( 0, 6, eddpdfi_get_settings($eddpdfi_pdf, 'notes'), 0, 'L', false );
		}

	endif;
}
add_action( 'eddpdfi_pdf_template_default', 'eddpdfi_pdf_template_default', 10, 10 );