<?php
/**
 * Template Tags
 *
 * Creates and renders the additional template tags for thes PDF invoice.
 *
 * @package Easy Digital Downloads - PDF Invoices
 * @since 1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function eddpdfi_register_email_tag() {
	edd_add_email_tag( 'invoice', __( 'Creates a link to a downloadable invoice', 'eddpdfi' ), 'eddpdfi_email_template_tags' );
}
add_action( 'edd_add_email_tags', 'eddpdfi_register_email_tag' );

/**
 * Email Template Tags
 *
 * Additional template tags for the Purchase Receipt.
 *
 * @since       1.0
 * @uses        edd_pdf_invoices()->get_pdf_invoice_url()
 * @return      string Invoice Link.
*/

function eddpdfi_email_template_tags( $payment_id ) {

	if ( ! edd_pdf_invoices()->is_invoice_link_allowed( $payment_id ) ) {
		return;
	}
	
	return '<a href="'. edd_pdf_invoices()->get_pdf_invoice_url( $payment_id ) . '">'. __( 'Download Invoice', 'eddpdfi' ) . '</a>';
}