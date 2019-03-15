<?php

function opanda_debug_log() {
    
    // this feature is not ready yet
    return;
    
    $dsid = isset( $_POST['opanda_dsid'] ) ? $_POST['opanda_dsid'] : null;
    $message = isset( $_POST['opanda_message'] ) ? $_POST['opanda_message'] : null;

    if ( empty( $dsid ) ) return;
    
    global $wpdb;
    
    $wpdb->insert( 
        $wpdb->prefix . 'opanda_debug_log', 
        array( 
            'SessionId' => $dsid, 
            'Message' => $message,
            'RecordTime' => time()
        ), 
        array( '%s', '%s', '%d' ) 
    );
}

add_action('wp_ajax_opanda_debug_log', 'opanda_debug_log');
add_action('wp_ajax_nopriv_opanda_debug_log', 'opanda_debug_log');