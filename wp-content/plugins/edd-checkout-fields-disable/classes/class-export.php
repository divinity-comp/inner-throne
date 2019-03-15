<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// This is based off of work by bbPress and also EDD itself.
class CFM_Export {

	public function __construct() {
		add_filter( 'edd_export_csv_cols_payments', array($this, 'columns') );
		add_filter( 'edd_export_get_data_payments', array($this, 'data'));
	}
	public function columns( $cols ){
		$submission = array('text','textarea','date','url','email','radio','select','multiselect','repeat','checkbox');
		$submission_meta = array();

		$form_id = get_option( 'edd_cfm_id' );
		if ( $form_id ){
			list($post_fields, $taxonomy_fields, $custom_fields) = EDD_CFM()->render_form->get_input_fields( $form_id );
			foreach($custom_fields as $field){
				if ( in_array( $field['input_type'], $submission ) ){
					$cols[$field['name']] = $field['label'];
				}
			}
		}
		return $cols;
	}

	public function data( $data ){
		$submission = array('text','textarea','date','url','email','radio','select','multiselect','repeat','checkbox');
		$submission_meta = array();
		$form_id = get_option( 'edd_cfm_id' );
		if ( $form_id ){
			list($post_fields, $taxonomy_fields, $custom_fields) = EDD_CFM()->render_form->get_input_fields( $form_id );
			foreach ( $data as $pid => $id ){
				$post_id = $id['id'];
				foreach($custom_fields as $field){
					if ( in_array( $field['input_type'], $submission ) ){
						$n = $field["name"];
						$data[$pid][$n] = EDD_CFM()->export->get_post_meta($n, $post_id);
					}
				}
			}
		}
		return $data;	
	}

	public function email_body( $message, $post_id ){
		$submission = array('text','textarea','date','url','email','radio','select','multiselect','repeat','checkbox');
		$submission_meta = array();

		$form_id = get_option( 'edd_cfm_id' );
		if ( $form_id ){
			list($post_fields, $taxonomy_fields, $custom_fields) = EDD_CFM()->render_form->get_input_fields( $form_id );
			foreach($custom_fields as $field){
				if ( in_array( $field['input_type'], $submission ) ){
					array_push($submission_meta, $field['name']);
				}
			}
		}

		foreach($submission_meta as $meta ){
			$message = str_replace('{'.$meta.'}', EDD_CFM()->export->get_post_meta($meta, $post_id), $message );
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
}