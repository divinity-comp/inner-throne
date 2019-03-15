<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
class CFM_Emails {

	public function __construct() {
		add_action( 'edd_sale_notification', array( $this, 'email_body' ), 10,2 );
		add_action( 'edd_purchase_receipt', array( $this, 'email_body' ), 10,2 );
		add_action( 'eddc_sale_alert_email', array( $this, 'commissions_email' ), 10 , 6 );
		add_filter( 'edd_receipt_attachments', array( $this, 'regular_attachments' ), 10, 3 );
		add_filter( 'edd_admin_sale_notification_attachments', array( $this, 'regular_attachments' ), 10, 3 );
	}

	public function email_body( $message, $post_id ){
		$form_id = get_option( 'edd_cfm_id' );
		if ( $form_id ){
			list( $post_fields, $taxonomy_fields, $custom_fields ) = EDD_CFM()->render_form->get_input_fields( $form_id );
			foreach($custom_fields as $meta ){
				$type = 'normal';
				if ( $meta['input_type'] == 'file_upload' ){
					$type = 'file';
				} else if ( $meta['input_type'] == 'image' ){
					$type = 'image';
				} else if ( $meta['input_type'] == 'repeat' ){
					$type = 'repeat';
				}
				$message = str_replace('{'.$meta['name'].'}', EDD_CFM()->emails->get_post_meta($meta['name'], $post_id, $type ), $message );
			}
		}
		return $message;
	}

	public function get_post_meta( $name, $post_id, $type = 'normal' ){
        if ( empty( $name ) || empty( $post_id ) ) {
            return;
        }

        $post = get_post( $post_id );

        if ( $type == 'image' || $type == 'file' ) {
            $images = get_post_meta( $post->ID, $name );
            if ( $images ) {
                $html = '';
                if ( isset( $images[0] ) && is_array( $images[0] ) ){
                    $images = $images[0];
                }
                foreach ($images as $attachment_id ) {
                    if ( $type == 'image' ) {
                        $thumb = wp_get_attachment_image( $attachment_id );
                    } else {
                        $thumb = get_post_field( 'post_title', $attachment_id );
                    }

                    $full_size = wp_get_attachment_url( $attachment_id );
                    $html .= sprintf( '<a href="%s">%s</a> ', $full_size, $thumb );
                }
                return $html;
            }
        } elseif ( $type == 'repeat' ) {
            return implode( '; ', get_post_meta( $post->ID, $name ) );
        } else {
            return implode( ', ', get_post_meta( $post->ID, $name ) );
        }
    }
	
	public function commissions_email( $message, $user_id, $commission_amount, $rate, $download_id, $commission_id ){
		$form_id = get_option( 'edd_cfm_id' );
		if ( $form_id && $commission_id ){
			$post_id = get_post_meta( $commission_id, '_edd_commission_payment_id', true ); // try to get payment_id from post_id
			if ( $post_id ){ // if we got the payment
			list( $post_fields, $taxonomy_fields, $custom_fields ) = EDD_CFM()->render_form->get_input_fields( $form_id );
				foreach($custom_fields as $meta ){
					$type = 'normal';
					if ( $meta['input_type'] == 'file_upload' ){
						$type = 'file';
					} else if ( $meta['input_type'] == 'image' ){
						$type = 'image';
					} else if ( $meta['input_type'] == 'repeat' ){
						$type = 'repeat';
					}
					$message = str_replace('{'.$meta['name'].'}', EDD_CFM()->emails->get_post_meta( $meta['name'], $post_id, $type ), $message );
				}
			}
		}
		return $message;
	}
	
	public function regular_attachments( $attachments, $post_id, $payment_data ) {
		global $edd_options;
		$form_id = get_option( 'edd_cfm_id' );
		if ( $form_id ){
			list( $post_fields, $taxonomy_fields, $custom_fields ) = EDD_CFM()->render_form->get_input_fields( $form_id );
			foreach( $custom_fields as $meta ){
				if ( $meta['input_type'] == 'file' || $meta['input_type'] == 'file_upload' || $meta['input_type'] == 'image' ){
					$files = get_post_meta( $post_id, $meta['name'] );
					if ( $files ) {
						if ( isset( $files[0] ) && is_array( $files[0] ) ){
							$files = $files[0];
						}
						foreach ($files as $attachment_id ) {
							$attachments[] = get_attached_file( $attachment_id );
						}
					}
				}
			}
		
		}			
		return $attachments;
	}	
}