<?php

/**
 * Attachment Uploader class
 *
 * @since 1.0
 * @package cfm
 */
class CFM_Upload {

    function __construct() {

        add_action( 'wp_ajax_cfm_file_upload', array($this, 'upload_file') );
        add_action( 'wp_ajax_nopriv_cfm_file_upload', array($this, 'upload_file') );

        add_action( 'wp_ajax_cfm_file_del', array($this, 'delete_file') );
        add_action( 'wp_ajax_nopriv_cfm_file_del', array($this, 'delete_file') );
    }

    function upload_file( $image_only = false ) {
        $upload = array(
            'name' => $_FILES['cfm_file']['name'],
            'type' => $_FILES['cfm_file']['type'],
            'tmp_name' => $_FILES['cfm_file']['tmp_name'],
            'error' => $_FILES['cfm_file']['error'],
            'size' => $_FILES['cfm_file']['size']
        );

        header('Content-Type: text/html; charset=' . get_option('blog_charset'));

        $attach = $this->handle_upload( $upload );

        if ( $attach['success'] ) {

            $response = array( 'success' => true );
            $response['html'] = $this->attach_html( $attach['attach_id'],NULL,$upload );
            echo $response['html'];
        } else {
            echo 'error';
        }
        exit;
    }

    /**
     * Generic function to upload a file
     *
     * @param string $field_name file input field name
     * @return bool|int attachment id on success, bool false instead
     */
    function handle_upload( $upload_data,$upload = false) {

        $uploaded_file = wp_handle_upload( $upload_data, array('test_form' => false) );

        // If the wp_handle_upload call returned a local path for the image
        if ( isset( $uploaded_file['file'] ) ) {
            $file_loc = $uploaded_file['file'];
            $file_name = basename( $upload_data['name'] );
            $file_type = wp_check_filetype( $file_name );

            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment( $attachment, $file_loc );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_loc );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            return array('success' => true, 'attach_id' => $attach_id);
        }

        return array('success' => false, 'error' => $uploaded_file['error']);
    }

    public static function attach_html( $attach_id, $type = NULL, $upload = NULL ) {
        if ( !$type ) {
            $type = isset( $_GET['type'] ) ? $_GET['type'] : 'image';
        }

        $attachment = get_post( $attach_id );

        if (!$attachment) {
            return;
        }

        if (wp_attachment_is_image( $attach_id)) {
            $image = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
            $image = $image[0];
        } else {
            $image = false;
        }
		if (!$image){
			$html = '<li class="image-wrap thumbnail" style="width: 150px">';
			$html .= '<div class="attachment-name">'.$upload['name'].'</div>';
			$html .= sprintf( '<div class="caption"><a href="#" class="btn btn-danger btn-small attachment-delete" data-attach_id="%d">%s</a></div>', $attach_id, __( 'Delete', 'edd_cfm' ) );
			$html .= sprintf( '<input type="hidden" name="cfm_files[%s][]" value="%d">', $type, $attach_id );
			$html .= '</li>';
		}
		else{
	        $html = '<li class="image-wrap thumbnail" style="width: 150px">';
			$html .= sprintf( '<div class="attachment-name"><img src="%s" alt="%s" /></div>', $image, esc_attr( $attachment->post_title ) );
			$html .= sprintf( '<div class="caption"><a href="#" class="btn btn-danger btn-small attachment-delete" data-attach_id="%d">%s</a></div>', $attach_id, __( 'Delete', 'edd_cfm' ) );
			$html .= sprintf( '<input type="hidden" name="cfm_files[%s][]" value="%d">', $type, $attach_id );
			$html .= '</li>';		
		}

        return $html;
    }

    function delete_file() {
        check_ajax_referer( 'cfm_nonce', 'nonce' );

        $attach_id = isset( $_POST['attach_id'] ) ? intval( $_POST['attach_id'] ) : 0;
        $attachment = get_post( $attach_id );

        //post author or editor role
        if ( get_current_user_id() == $attachment->post_author || current_user_can( 'delete_private_pages' ) ) {
            wp_delete_attachment( $attach_id, true );
            echo 'success';
        }

        exit;
    }

    function associate_file( $attach_id, $post_id ) {
        wp_update_post( array(
            'ID' => $attach_id,
            'post_parent' => $post_id
        ) );
    }

}