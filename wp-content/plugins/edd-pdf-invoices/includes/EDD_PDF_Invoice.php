<?php
/**
 * PDF Invoice Class
 *
 * Extends the TCPDF class to add the extra functionality for the PDF Invoices
 *
 * @since 2.0
 * @package Easy Digital Downloads - PDF Invoices
*/

/**
 * EDD_PDF_Invoice Class
 */
class EDD_PDF_Invoice extends TCPDF {

	/**
	 * Header
	 *
	 * Outputs the header message configured in the Settings on all the invoices
	 * as well as display the background images on certain templates
	 *
	 * @since 2.0
	 */
	public function Header() {

		global $edd_options;

		if ( isset( $edd_options['eddpdfi_templates'] ) && $edd_options['eddpdfi_templates'] == 'blue_stripe' ) {
			$this->Rect( 0, 0, 30, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 149, 210, 236 ) );
		} else if ( isset( $edd_options['eddpdfi_templates'] ) && $edd_options['eddpdfi_templates'] == 'lines' ) {
			$this->Rect( 1, 0, 0.5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 192, 55, 26 ) );
			$this->Rect( 3, 0, 1, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 169, 169, 169 ) );
			$this->Rect( 8, 0, 0.5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 228, 190, 172 ) );
			$this->Rect( 10, 0, 1, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 199, 60, 37 ) );
			$this->Rect( 17, 0, 0.5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 218, 180, 167 ) );
			$this->Rect( 20, 0, 5, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 240, 230, 220 ) );
			$this->Rect( 206, 0, 0.8, 297, 'F', array( 'L' => 0, 'T' => 0, 'R' => 0, 'B' => 0 ), array( 240, 230, 220 ) );
		} // end if

		if (
			$edd_options['eddpdfi_templates'] == 'blue' ||
			$edd_options['eddpdfi_templates'] == 'green' ||
			$edd_options['eddpdfi_templates'] == 'orange' ||
			$edd_options['eddpdfi_templates'] == 'pink' ||
			$edd_options['eddpdfi_templates'] == 'purple' ||
			$edd_options['eddpdfi_templates'] == 'red' ||
			$edd_options['eddpdfi_templates'] == 'yellow'
		) {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'opensans';
			$this->AddFont( 'opensansi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $edd_options['eddpdfi_templates'] == 'lines' || $edd_options['eddpdfi_templates'] == 'blue_stripe' ) {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'droidserif';
			$this->AddFont( 'droidserifi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $edd_options['eddpdfi_templates'] == 'traditional' ) {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'times';
			$this->AddFont( 'times', 'I' );
			$this->SetFont( $font, 'I', 8 );
			$this->SetTextColor( 50, 50, 50 );
		} else {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'helvetica';
			$this->SetFont( $font, 'I', 8 );
		} // end if

		if ( isset( $edd_options['eddpdfi_header_message'] ) ) {
			$eddpdfi_payment = get_post( $_GET['purchase_id'] );

			$eddpdfi_header = isset( $edd_options['eddpdfi_header_message'] ) ? $edd_options['eddpdfi_header_message']: '';
			$eddpdfi_header = str_replace( '{page}', 'Page ' . $this->PageNo(), $eddpdfi_header );
			$eddpdfi_header = str_replace( '{sitename}', get_bloginfo('name'), $eddpdfi_header );
			$eddpdfi_header = str_replace( '{today}', date_i18n( get_option('date_format'), time() ), $eddpdfi_header );
			$eddpdfi_header = str_replace( '{date}', date_i18n( get_option('date_format'), strtotime( $eddpdfi_payment->post_date ) ), $eddpdfi_header );
			$eddpdfi_header = str_replace( '{invoice_id}', $eddpdfi_payment->ID, $eddpdfi_header );

			$this->Cell( 0, 10, stripslashes_deep( html_entity_decode( $eddpdfi_header, ENT_COMPAT, 'UTF-8' ) ), 0, 0, 'C');
		} // end if

	} // end Header

	/**
	 * Footer
	 *
	 * Outputs the footer message configured in the Settings on all the invoices
	 *
	 * @since 2.0
	 */
	public function Footer() {

		global $edd_options;

		$this->SetY( -15 );

		if (
			$edd_options['eddpdfi_templates'] == 'blue' ||
			$edd_options['eddpdfi_templates'] == 'green' ||
			$edd_options['eddpdfi_templates'] == 'orange' ||
			$edd_options['eddpdfi_templates'] == 'pink' ||
			$edd_options['eddpdfi_templates'] == 'purple' ||
			$edd_options['eddpdfi_templates'] == 'red' ||
			$edd_options['eddpdfi_templates'] == 'yellow'
		) {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'opensans';
			$this->AddFont( 'opensansi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $edd_options['eddpdfi_templates'] == 'lines' || $edd_options['eddpdfi_templates'] == 'blue_stripe' ) {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'droidserif';
			$this->AddFont( 'droidserifi', '' );
			$this->SetFont( $font, 'I', 8 );
		} else if ( $edd_options['eddpdfi_templates'] == 'traditional' ) {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'times';
			$this->AddFont( 'times', 'I' );
			$this->SetFont( $font, 'I', 8 );
			$this->SetTextColor( 50, 50, 50 );
		} else {
			$font = isset( $edd_options['eddpdfi_enable_char_support'] ) ? 'kozminproregular' : 'helvetica';
			$this->SetFont( $font, 'I', 8 );
		} // end if

		if ( isset( $edd_options['eddpdfi_footer_message'] ) ) {
			$eddpdfi_payment = get_post( $_GET['purchase_id'] );

			$eddpdfi_footer = isset( $edd_options['eddpdfi_footer_message'] ) ? $edd_options['eddpdfi_footer_message']: '';
			$eddpdfi_footer = str_replace( '{page}', 'Page ' . $this->PageNo(), $eddpdfi_footer );
			$eddpdfi_footer = str_replace( '{sitename}', get_bloginfo('name'), $eddpdfi_footer );
			$eddpdfi_footer = str_replace( '{today}', date( get_option('date_format'), time() ), $eddpdfi_footer );
			$eddpdfi_footer = str_replace( '{date}', date( get_option('date_format'), strtotime( $eddpdfi_payment->post_date ) ), $eddpdfi_footer );
			$eddpdfi_footer = str_replace( '{invoice_id}', $eddpdfi_payment->ID, $eddpdfi_footer );

			$this->Cell( 0, 10, stripslashes_deep( html_entity_decode( $eddpdfi_footer, ENT_COMPAT, 'UTF-8' ) ), 0, 0, 'C');
		} // end if

	} // end Footer

} // end class