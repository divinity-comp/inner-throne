<?php 
abstract class OPanda_Subscription {
    
    public $name;
    public $title; 
    public $data;
    
    public $deliveryError;
    
    public function __construct( $data = array() ) {
        $this->data = $data;
        
        if ( isset( $data['name']) ) $this->name = $data['name'];
        if ( isset( $data['title']) ) $this->title = $data['title'];
    }

    public function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);       
    }
    
    public function isTransactional() {
        return isset( $this->data['transactional'] ) && $this->data['transactional'];
    }
    
    public function hasSingleOptIn() {
        return in_array('quick', $this->data['modes'] );
    }

    public abstract function getLists();
    public abstract function subscribe( $identityData, $listId, $doubleOptin, $contextData, $verified );
    public abstract function check( $identityData, $listId, $contextData );
    public abstract function getCustomFields( $listId );
    
    public function prepareFieldValueToSave( $mapOptions, $value ) {
        return $value;
    }
    
    public function getNameFieldIds() {
        return array();
    }
    
    public function slugify($text, $separator = ' ')
    { 
      // replace non letter or digits by -
      $text = preg_replace('~[^\\pL\d]+~u', $separator, $text);

      // trim
      $text = trim($text, '-');

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // lowercase
      $text = strtolower($text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      if (empty($text))
      {
        return 'n-a';
      }

      return $text;
    }
    
    public function refine( $identityData, $clearEmpty = false ) {
        if ( empty( $identityData ) ) return $identityData;
        
        unset( $identityData['html'] );     
        unset( $identityData['label'] ); 
        unset( $identityData['separator'] );        
        unset( $identityData['name'] );
        unset( $identityData['family'] );
        unset( $identityData['displayName'] );
        unset( $identityData['fullname'] ); 
        
        if ( $clearEmpty ) {
            foreach ($identityData as $key => $value) {
                if ( empty($value) ) unset( $identityData[$key] );
            }
        }
        
        return $identityData;
    }
    
    /**
     * The function to subscribe an user through Wordpress. 
     */
    public function wpSubscribe( $identity, $serviceReady, $context, $listId, $verified ) {
        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/leads.php';
        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/stats.php';
        
        $email = $identity['email'];
        if ( empty( $email ) ) {
            throw new OPanda_SubscriptionException( __('The email is not specified.','optinpanda') );
        }
        
        $temp = array(
            'identity' => $identity,
            'serviceReady' => $serviceReady,
            'context' => $context,
            'listId' => $listId, 
            'verified' => $verified   
        );
        
        $lead = OPanda_Leads::getByEmail($email);
        
        // already exists
        if ( $lead ) {
            
            if ( $lead->lead_subscription_confirmed ) return array('status' => 'subscribed');
            OPanda_Leads::setTempData( $lead, $temp );
            
            $this->sendConfirmation( $lead, $context );
            return array('status' => 'pending');
        }

        $lead->lead_email = $email; 
        $lead->ID = OPanda_Leads::add( $identity, $context, $verified, false, $temp );

        $this->sendConfirmation( $lead, $context );
        return array('status' => 'pending');
    }
    
    /**
     * The function to subscribe an user through Wordpress. 
     */
    public function wpCheck( $identity, $serviceReady, $context, $listId, $verified ) {

        $email = $identity['email'];
        if ( empty( $email ) ) {
            throw new OPanda_SubscriptionException( __('The email is not specified.','optinpanda') );
        }
        
        $lead = OPanda_Leads::getByEmail($email);
        if ( !$lead ) throw new OPanda_SubscriptionException( __('The email is not found. Please try again.','optinpanda') );
        
        return array('status' => $lead->lead_subscription_confirmed ? 'subscribed' : 'pending');
    }
    
    /**
     * The function sends the confirmation email.
     */
    public function sendConfirmation( $lead, $context ) {
        
        if ( empty( $context['itemId'] ) ) 
            throw new OPanda_SubscriptionException( __('Invalid request. Please contact the OnePress support.', 'optinpanda') );
        
        $lockerId = (int)$context['itemId'];
        
        $emailSubject = get_post_meta($lockerId, 'opanda_confirm_email_subject', true);
        $emailBody = get_post_meta($lockerId, 'opanda_confirm_email_body', true);
        
        $link = $this->getConfirmationLink($lead, $context);
        $emailBody = str_replace('[link]', '<a href="' . $link . '" target="_blank">' . $link . '</a>', $emailBody);

        if ( $this->isTransactional() ) {
            
            $this->send( $lead->lead_email, $emailSubject, $emailBody );
            
        } else {
  
            add_action( 'wp_mail_failed', array($this, 'handleDeliveryError') );
            add_filter( 'wp_mail_from', array($this, 'mailFrom'), 99 );
            add_filter( 'wp_mail_from_name', array($this, 'mailFromName'), 99 );
            
            $this->deliveryError = false;
        
            $headers[] = 'Content-type: text/html; charset=utf-8';
            $result = wp_mail( $lead->lead_email, $emailSubject, $emailBody, $headers );

            if ( !$result ) {
                if ( $this->deliveryError ) throw new OPanda_SubscriptionException( $this->deliveryError->get_error_message() );
                else throw new OPanda_SubscriptionException( __('Unable to send a confirmation email to the specified email address.', 'optinpanda') );
            }

            remove_action('wp_mail_failed', array($this, 'handleDeliveryError'));
            remove_action('wp_mail_failed', array($this, 'wp_mail_from'));
            remove_action('wp_mail_failed', array($this, 'wp_mail_from_name'));
        }
    }
    
    public function handleDeliveryError( $error ) {
        $this->deliveryError = $error;
    }
    
    public function mailFrom( $from ) {
        $value = get_option('opanda_sender_email', null);
        return empty( $value ) ? $from : $value;
    }
    
    public function mailFromName( $fromName ) {
        $value = get_option('opanda_sender_name', null);
        return empty( $value ) ? $fromName : $value;
    }
    
    public function getConfirmationLink( $lead, $context ) {

        $uri = $context['postUrl'];
        if ( empty( $uri ) ) $uri = home_url();
        
        $code = $lead->lead_confirmation_code;
        
        if ( empty( $code ) ) {
            $code = md5( NONCE_SALT . $lead->lead_email . time() );
            OPanda_Leads::setConfirmationCode($lead, $code);
        }

        $args = array(
            'opanda_confirm' => 1,
            'opanda_lead' => $lead->ID,
            'opanda_code' => $code
        );
        
        return add_query_arg($args, $uri);
    }
}

/**
 * A subscription service exception.
 */
class OPanda_SubscriptionException extends Exception {
    
    public function __construct ($message) {
        parent::__construct($message, 0, null);
    }
}