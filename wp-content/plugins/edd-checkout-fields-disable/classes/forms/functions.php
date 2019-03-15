<?php
function cfm_allowed_extensions() {
    $extensions = array(
        'images' => array('ext' => 'jpg,jpeg,gif,png,bmp', 'label' => __( 'Images', 'edd_cfm' )),
        'audio' => array('ext' => 'mp3,wav,ogg,wma,mka,m4a,ra,mid,midi', 'label' => __( 'Audio', 'edd_cfm' )),
        'video' => array('ext' => 'avi,divx,flv,mov,ogv,mkv,mp4,m4v,divx,mpg,mpeg,mpe', 'label' => __( 'Videos', 'edd_cfm' )),
        'pdf' => array('ext' => 'pdf', 'label' => __( 'PDF', 'edd_cfm' )),
        'office' => array('ext' => 'doc,ppt,pps,xls,mdb,docx,xlsx,pptx,odt,odp,ods,odg,odc,odb,odf,rtf,txt', 'label' => __( 'Office Documents', 'edd_cfm' )),
        'zip' => array('ext' => 'zip,gz,gzip,rar,7z', 'label' => __( 'Zip Archives' )),
        'exe' => array('ext' => 'exe', 'label' => __( 'Executable Files', 'edd_cfm' )),
        'csv' => array('ext' => 'csv', 'label' => __( 'CSV', 'edd_cfm' ))
    );

    return apply_filters( 'cfm_allowed_extensions', $extensions );
}

/**
 * Associate attachemnt to a post
 *
 * @since 2.0
 *
 * @param type $attachment_id
 * @param type $post_id
 */
function cfm_associate_attachment( $attachment_id, $post_id ) {
    wp_update_post( array(
        'ID' => $attachment_id,
        'post_parent' => $post_id
    ) );
}


/**
 * Show custom fields in static order view (disabled)
 *
 * @global object $post
 * @param string $content
 * @return string
 */
function cfm_show_custom_fields() {
    global $post;

    $form_id = get_option( 'edd_cfm_id');

    $html = '<ul class="cfm_customs">';

    $form_vars = get_post_meta( $form_id, 'edd-checkout-fields', true );
    $meta = array();

    if ( $form_vars ) {
        foreach ($form_vars as $attr) {
            if ( isset( $attr['is_meta'] ) && $attr['is_meta'] == 'yes' ) {
                $meta[] = $attr;
            }
        }

        if ( !$meta ) {
            return $content;
        }

        foreach ($meta as $attr) {
            $field_value = get_post_meta( $post->ID, $attr['name'] );

            if ( $attr['input_type'] == 'hidden' ) {
                continue;
            }

            if ($attr['input_type'] == 'file_upload' ) {
                $image_html = '<li><label>' . $attr['label'] . ':</label> ';

                if ( $field_value ) {
                    foreach ($field_value as $attachment_id) {
                        $thumb = get_post_field( 'post_title', $attachment_id );
                        $full_size = wp_get_attachment_url( $attachment_id );
                        $image_html .= sprintf( '<a href="%s">%s</a> ', $full_size, $thumb );
                    }
                }

                $html .= $image_html . '</li>';

            }else {
                $value = get_post_meta( $post->ID, $attr['name'] );
                $html .= sprintf( '<li><label>%s</label>: %s</li>', $attr['label'], make_clickable( implode( ', ', $value ) ) );
            }
        }
    }

    $html .= '</ul>';

    return $html;
}


/**
 * Get attachment ID from a URL
 *
 * @since 2.1.8
 *
 * @link http://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/ Original Implementation
 *
 * @global type $wpdb
 * @param type $attachment_url
 * @return type
 */
function cfm_get_attachment_id_from_url( $attachment_url = '' ) {

    global $wpdb;
    $attachment_id = false;

    // If there is no url, return.
    if ( '' == $attachment_url )
        return;

    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();

    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

        // Remove the upload path base directory from the attachment URL
        $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

        // Finally, run a custom database query to get the attachment ID from the modified attachment URL
        $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
    }

    return $attachment_id;
}