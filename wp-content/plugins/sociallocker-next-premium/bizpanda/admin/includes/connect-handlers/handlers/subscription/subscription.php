<?php
if ( !defined('OPANDA_PROXY') ) exit;

/**
 * The class to proxy the request to the Subscription API.
 */
class OPanda_SubscriptionHandler extends OPanda_Handler {
    /**
     * Handles the proxy request.
     */
    public function handleRequest() {
        
        if( !isset($_POST['opandaRequestType']) || !isset($_POST['opandaService']) ) {
           throw new Opanda_HandlerInternalException('Invalid request. The "opandaRequestType" or "opandaService" are not defined.');
        }

        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/subscriptions.php';
        $service = OPanda_SubscriptionServices::getCurrentService();

        if ( empty( $service) ) {
           throw new Opanda_HandlerInternalException( sprintf( 'The subscription service is not set.' )); 
        }

        // - service name
        
        $serviceName = $this->options['service'];
        if ( $serviceName !== $service->name  ) {
           throw new Opanda_HandlerInternalException( sprintf( 'Invalid subscription service "%s".', $serviceName ));
        }
        
        // - request type
        
        $requestType = strtolower( $_POST['opandaRequestType'] );
        $allowed = array('check', 'subscribe');

        if ( !in_array( $requestType, $allowed ) ) {
           throw new Opanda_HandlerInternalException( sprintf( 'Invalid request. The action "%s" not found.', $requestType ));
        }
        
        // - identity data
        
        $identityData = isset( $_POST['opandaIdentityData'] ) ? $_POST['opandaIdentityData'] : array();
        $identityData = $this->normilizeValues( $identityData );
        
        if ( empty( $identityData['email'] )) {
            throw new Opanda_HandlerException( 'Unable to subscribe. The email is not specified.' );
        }

        if ( get_option('opanda_forbid_temp_emails', false) ) {
 
            $tempDomains = get_option('opanda_temp_domains', false);
            if ( !empty( $tempDomains ) ) {

                $tempDomains = explode(',', $tempDomains);
                foreach( $tempDomains as $tempDomain ) {
                    $tempDomain = trim($tempDomain);
                    if ( false !== strpos($identityData['email'], $tempDomain) ) {
                        throw new Opanda_HandlerException( get_option('opanda_res_errors_temporary_email', 'Sorry, temporary email addresses cannot be used to unlock content.') );
                    }
                }
            }            
        }
        
        // - service data
        
        $serviceData = isset( $_POST['opandaServiceData'] ) ? $_POST['opandaServiceData'] : array();
        $serviceData = $this->normilizeValues( $serviceData );
        
        // - context data
        
        $contextData = isset( $_POST['opandaContextData'] ) ? $_POST['opandaContextData'] : array();
        $contextData = $this->normilizeValues( $contextData );

        // - list id
        
        $listId = isset( $_POST['opandaListId'] ) ? $_POST['opandaListId'] : null;
        if ( empty( $listId ) ) {
           throw new Opanda_HandlerException( 'Unable to subscribe. The list ID is not specified.' );
        }
        
        // - double opt-in
        
        $doubleOptin =  isset( $_POST['opandaDoubleOptin'] ) ? $_POST['opandaDoubleOptin'] : true;
        $doubleOptin = $this->normilizeValue( $doubleOptin );
        
        // - confirmation
        
        $confirm =  isset( $_POST['opandaConfirm'] ) ? $_POST['opandaConfirm'] : true;
        $confirm = $this->normilizeValue( $confirm );
        
        // verifying user data if needed while subscribing
        // works for social subscription
        
        $verified = false; 
        $mailServiceInfo = OPanda_SubscriptionServices::getServiceInfo();
        $modes = $mailServiceInfo['modes'];
            
        if ( 'subscribe' === $requestType ) {

            if ( $doubleOptin && in_array( 'quick', $mailServiceInfo['modes'] ) ) {
                $verified = $this->verifyUserData( $identityData, $serviceData );
            }     
        }

        // prepares data received from custom fields to be transferred to the mailing service
        
        $itemId = intval( $contextData['itemId'] );
        
        $identityData = $this->prepareDataToSave( $service, $itemId, $identityData );
        $serviceReadyData = $this->mapToServiceIds( $service, $itemId, $identityData );
        $identityData = $this->mapToCustomLabels( $service, $itemId, $identityData );
        
        // checks if the subscription has to be procces via WP
        
        $subscribeMode = get_post_meta($itemId, 'opanda_subscribe_mode', true);
        $subscribeDelivery = get_post_meta($itemId, 'opanda_subscribe_delivery', true);
        
        $isWpSubscription = false;
        
        if ( $service->hasSingleOptIn() 
                && in_array( $subscribeMode, array('double-optin', 'quick-double-optin') ) 
                && ( $service->isTransactional() || $subscribeDelivery == 'wordpress' ) ) {
            
            $isWpSubscription = true;
        }

        // creating subscription service

        try {    
            
            $result = array();
            
            if ( 'subscribe' === $requestType ) {
                
                if ( $isWpSubscription ) {
                    
                    // if the use signes in via a social network and we managed to confirm that the email is real,
                    // then we can skip the confirmation process
                    
                    if ( $verified ) {
                        OPanda_Leads::add( $identityData, $contextData, true, true );
                        return $service->subscribe( $serviceReadyData, $listId, false, $contextData, $verified );
                    } else {
                        $result = $service->wpSubscribe( $identityData, $serviceReadyData, $contextData, $listId, $verified );    
                    }
      
                } else {
                    $result = $service->subscribe( $serviceReadyData, $listId, $doubleOptin, $contextData, $verified );         
                }

                do_action('opanda_subscribe', 
                    ( $result && isset( $result['status'] ) ) ? $result['status'] : 'error', 
                    $identityData, $contextData, $isWpSubscription
                );
                
            } elseif ( 'check' === $requestType ) {
                
                if ( $isWpSubscription ) {
                    $result = $service->wpCheck( $identityData, $serviceReadyData, $contextData, $listId, $verified );   
                } else {
                    $result = $service->check( $serviceReadyData, $listId, $contextData );   
                }
                
                do_action('opanda_check', 
                    ( $result && isset( $result['status'] ) ) ? $result['status'] : 'error', 
                    $identityData, $contextData, $isWpSubscription
                );
            }
            
            $result = apply_filters('opanda_subscription_result', $result, $identityData);
            if ( !defined( 'OPANDA_WORDPRESS' ) ) return $result;
            
            // calls the hook to save the lead in the database
            if ( $result && isset( $result['status'] ) ) {

                $actionData = array(
                    'identity' => $identityData,
                    'requestType' => $requestType,
                    'service' => $this->options['service'],
                    'list' => $listId,
                    'doubleOptin' => $doubleOptin,
                    'confirm' => $confirm,
                    'context' => $contextData
                );
                
                if ( 'subscribed' === $result['status'] ) {
                    do_action('opanda_subscribed', $actionData);
                } else {
                    do_action('opanda_pending', $actionData); 
                }
            }
            
            return $result;
            
        } catch(Exception $ex) {
            throw new Opanda_HandlerException( $ex->getMessage() );
        }
    }
}
