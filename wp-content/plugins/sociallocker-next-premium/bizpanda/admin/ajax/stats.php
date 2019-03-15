<?php
/**
 * Ajax requests linked with collecting statistics.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2014, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

add_action('wp_ajax_opanda_statistics', 'opanda_statistics');
add_action('wp_ajax_nopriv_opanda_statistics', 'opanda_statistics');

/**
 * Increases counters in a database after unlocking content.
 * 
 * @since 1.0.0
 * @return void
 */
function opanda_statistics() {
    global $wpdb;
    
    $statsItem = isset( $_POST['opandaStats'] ) ? $_POST['opandaStats'] : array();
    $contextData = isset( $_POST['opandaContext'] ) ? $_POST['opandaContext'] : array();
    
    // event name
    
    $eventName = isset( $statsItem['eventName'] ) ? $statsItem['eventName'] : null;
    $eventName = opanda_normilize_value( $eventName );

    // sender type
    
    $eventType = isset( $statsItem['eventType'] ) ? $statsItem['eventType'] : null;
    $eventType = opanda_normilize_value( $eventType );
    
    // visitor id
    
    $visitorId = isset( $statsItem['visitorId'] ) ? $statsItem['visitorId'] : null;
    $visitorId = opanda_normilize_value( $visitorId );    

    // context data
    
    $context = isset( $_POST['opandaContext'] ) ? $_POST['opandaContext'] : array();
    $context = opanda_normilize_values( $context );
    
    $itemId = isset( $context['itemId'] ) ? $context['itemId'] : null;
    $postId = isset( $context['postId'] ) ? $context['postId'] : null;
    
    if ( empty( $itemId ) ) {
        echo json_encode( array( 'error' => __('Item ID is not specified.', 'bizpanda') ) );
        exit;
    }

    // stats for form unlocks is counted only once for a give visitor ID,
    // against multiple counting when the confirmation is used
    
    if ( $eventName == 'form' && $eventType == 'unlock' ) {
        
        $key = 'opanda_' . md5($visitorId . $eventName . $eventType );
        
        $unlocked = get_transient($key);
        if ( $unlocked ) return json_encode( array( 'error' => __('Already counted.', 'bizpanda') ) );
        set_transient($key, 1, 10);
    }
    
    // counts the stats

    include_once(OPANDA_BIZPANDA_DIR . '/admin/includes/stats.php');
    OPanda_Stats::processEvent( $itemId, $postId, $eventName, $eventType );
    
    echo json_encode( array('success' => true) );
    exit;
}

