<?php
if ( !defined('OPANDA_PROXY') ) exit;

/**
 * The class to proxy the request to the Twitter API.
 */
class OPanda_TwitterHandler extends OPanda_Handler {

    /**
     * Handles the proxy request.
     */
    public function handleRequest() {
        
        // the request type is to determine which action we should to run
        $requestType = !empty( $_REQUEST['opandaRequestType'] ) ? $_REQUEST['opandaRequestType'] : null;
        
        // allowed request types, others will trigger an error
        $allowed = array('init', 'callback', 'user_info', 'follow', 'tweet', 'get_tweets', 'get_followers');
        
        if ( empty( $requestType ) || !in_array($requestType, $allowed) )
            throw new Opanda_HandlerException('Invalid request type.'); 
        
        // the visitor id is used as a key for the storage where all the tokens are saved
        $visitorId = !empty( $_REQUEST['opandaVisitorId'] ) ? $_REQUEST['opandaVisitorId'] : null;
        $readOnly = !empty( $_REQUEST['opandaReadOnly'] ) ? (bool)$_REQUEST['opandaReadOnly'] : null; 
        
        $this->options['consumer_key'] = get_option('opanda_twitter_consumer_key');
        $this->options['consumer_secret'] = get_option('opanda_twitter_consumer_secret');
        
        require_once( 'libs/twitteroauth.php');

        switch( $requestType ) {
            
            case 'init':
                $this->doInit( $visitorId ); 
 
            case 'callback':
                $this->doCallback( $visitorId ); 
            
            case 'user_info':
                $this->getUserData( $visitorId );
                
            case 'follow':
                $this->follow( $visitorId );
                
            case 'tweet':
                $this->tweet( $visitorId );    
                
            case 'get_tweets':
                $this->getTweets( $visitorId ); 
                
            case 'get_followers':
                $this->getFollowers( $visitorId );  
        }
    }
    
    /**
     * Build the callback URL for Twitter.
     */
    public function getCallbackUrl( $visitorId, $keepOpen ) {
        $proxy = $this->options['proxy'];
        $prefix = ( strpos( $proxy, '?') === false) ? '?' : '&';
        return $proxy . $prefix . 'opandaHandler=twitter&opandaRequestType=callback&opandaVisitorId=' . $visitorId . ('&opandaKeepOpen=' . ($keepOpen ? '1' : '0'));
    }
    
    /**
     * Inits an OAuth request.
     */
    public function doInit( $visitorId ) {
        $options = $this->options;

        if ( empty( $visitorId ) ) $visitorId = $this->getGuid();
        $keepOpen = !empty( $_REQUEST['opandaKeepOpen'] ) ? (bool)$_REQUEST['opandaKeepOpen'] : null; 
        $readOnly = !empty( $_REQUEST['opandaReadOnly'] ) ? (bool)$_REQUEST['opandaReadOnly'] : null; 
        
        if ( empty( $options['consumer_key'] ) ||  empty( $options['consumer_secret'] ) ) {
            printf( 'Please create a Twitter App for your website. <a href="%s" target="_blank">Click here</a> and follow the instruction.', admin_url('admin.php?page=how-to-use-bizpanda&onp_sl_page=twitter-app') );
            exit;
        }
        
        $oauth = new OPanda_TwitterOAuth( $options['consumer_key'], $options['consumer_secret'] );
        $requestToken = $oauth->getRequestToken( $this->getCallbackUrl( $visitorId, $keepOpen ) ); 

        if ( empty( $requestToken ) ) {
            printf( 'Twitter has returned an empty response. Please make sure that you created a Twitter App for your website correctly. <a href="%s" target="_blank">Click here</a> to learn more.', admin_url('admin.php?page=how-to-use-bizpanda&onp_sl_page=twitter-app') );
            exit;  
        }
        
        if ( !isset( $requestToken['oauth_token'] ) ) {
            print_r($requestToken);
            exit;  
        }

        $token = $requestToken['oauth_token'];
        $secret = $requestToken['oauth_token_secret'];          

        $this->saveValue( $visitorId, 'twitter_token', $token );
        $this->saveValue( $visitorId, 'twitter_secret', $secret );

        $authorizeURL = $oauth->getAuthorizeURL( $token, false );

        header("Location: $authorizeURL");
        exit;
    }
    
    /**
     * Handles a callback from Twitter (when the user has been redirected)
     */
    public function doCallback( $visitorId ) {
        $options = $this->options;
        $keepOpen = !empty( $_REQUEST['opandaKeepOpen'] ) ? (bool)$_REQUEST['opandaKeepOpen'] : null; 

        if ( empty( $visitorId ) )
            throw new Opanda_HandlerException('Invalid visitor ID.');
        
        $denied = isset( $_REQUEST['denied'] );
        if ( $denied ) { 
        ?>
            <script>
                if( window.opener ) window.opener.OPanda_OPanda_TwitterOAuthDenied( '<?php echo $visitorId ?>' );                
                window.close();                
            </script>
        <?php
        exit;
        }
        
        $token = !empty( $_REQUEST['oauth_token']) ? $_REQUEST['oauth_token'] : null;
        $verifier = !empty( $_REQUEST['oauth_verifier']) ? $_REQUEST['oauth_verifier'] : null;

        if ( empty( $token ) || empty( $verifier ) ) {
            throw new Opanda_HandlerException('The verifier value is invalid.');
        }

        $secret = $this->getValue( $visitorId, 'twitter_secret' );

        if ( empty( $secret ) ) {
            throw new Opanda_HandlerException( "The secret of the request token is invalid for $visitorId" );
        }    

        $connection = new OPanda_TwitterOAuth( $options['consumer_key'], $options['consumer_secret'], $token, $secret );

        $accessToken = $connection->getAccessToken( $verifier );

        $this->saveValue( $visitorId, 'twitter_token', $accessToken['oauth_token'] );
        $this->saveValue( $visitorId, 'twitter_secret', $accessToken['oauth_token_secret'] );  

        ?>
            <script>
                if( window.opener ) window.opener.OPanda_TwitterOAuthCompleted( '<?php echo $visitorId ?>' );                
                <?php if ( !$keepOpen ) { ?>
                    window.close();
                <?php } ?>
                
                window.updateState = function( url, width, height, x, y ){
                    window.location.href = url;
                    window.resizeTo && window.resizeTo(width, height);
                    window.moveTo && window.moveTo(x, y);
                }
            </script>
        <?php
        
        exit;
    }
    
    protected function getTwitterOAuth( $visitorId = null, $token = null, $secret = null ) {
        $options = $this->options;
        
        if ( empty( $visitorId ) && ( empty( $token ) || empty( $secret ) ) )
            throw new Opanda_HandlerException('Invalid visitor ID.');
        
        if ( empty( $token ) ) {
            $token = $this->getValue( $visitorId, 'twitter_token' );
            if ( empty( $token ) ) throw new Opanda_HandlerException( "The access token not found for $visitorId" );
        }
        if ( empty( $secret ) ) {
            $secret = $this->getValue( $visitorId, 'twitter_secret' );
            if ( empty( $secret ) ) throw new Opanda_HandlerException( "The secret of the access token is invalid for $visitorId" );
        }
        
        return new OPanda_TwitterOAuth( $options['consumer_key'], $options['consumer_secret'], $token, $secret);
    }
    
    public function getUserData( $visitorId, $returnData = false ) {
        require_once( 'libs/twitteroauth.php');
        $oauth = $this->getTwitterOAuth( $visitorId );

        $response = $oauth->get('account/verify_credentials', array('skip_status' => 1, 'include_email' => 'true'));
        if ( $returnData ) return $response;
        
        echo json_encode($response);
        exit;
    }
    
    public function getTweets( $visitorId ) {
        $oauth = $this->getTwitterOAuth( $visitorId );

        $response = $oauth->get('statuses/user_timeline', array('count' => 3));
        echo json_encode($response);
        
        exit;
    }
    
    public function getFollowers( $visitorId ) {
        $oauth = $this->getTwitterOAuth( $visitorId );
        $sceenName = !empty( $_REQUEST['opandaSceenName']) ? $_REQUEST['opandaSceenName'] : null;

        $response = $oauth->get('friendships/lookup', array('screen_name' => $sceenName));
        echo json_encode($response);
        
        exit;
    }    
    
    protected function follow( $visitorId ) {
        $oauth = $this->getTwitterOAuth( $visitorId );
        
        $contextData = isset( $_POST['opandaContextData'] ) ? $_POST['opandaContextData'] : array();
        $contextData = $this->normilizeValues( $contextData );
        
        $followTo = isset( $_REQUEST['opandaFollowTo'] ) ? $_REQUEST['opandaFollowTo'] : null;
        if ( empty( $followTo) ) throw new Opanda_HandlerException( "The user name to follow is not specified" );
        
        $notifications = isset( $_REQUEST['opandaNotifications'] ) ? $_REQUEST['opandaNotifications'] : false;
        $notifications = $this->normilizeValue( $notifications );

        $response = $oauth->get('friendships/lookup', array(
            'screen_name' => $followTo
        ));
        
        if ( isset( $response->errors ) ) {
            echo json_encode(array('error' => $response->errors[0]->message )); 
            exit;
        }
        
        if ( isset( $response[0]->connections ) && in_array( 'following', $response[0]->connections ) ) {
            echo json_encode(array('success' => true)); 
            exit;
        }

        $response = $oauth->post('friendships/create', array(
            'screen_name' => $followTo,
            'follow' => $notifications
        ));
        
        if ( isset( $response->errors ) ) {
            echo json_encode(array('error' => $response->errors[0]->message )); 
            exit;
        }
        
        do_action('opanda_got_twitter_follower', $contextData );

        echo json_encode($response);
        exit;
    }
    
    protected function tweet( $visitorId ) {
        $oauth = $this->getTwitterOAuth( $visitorId );
        
        $contextData = isset( $_POST['opandaContextData'] ) ? $_POST['opandaContextData'] : array();
        $contextData = $this->normilizeValues( $contextData );
        
        $message = !empty( $_REQUEST['opandaTweetMessage'] ) ? $_REQUEST['opandaTweetMessage'] : null;
        if ( empty( $message) ) throw new Opanda_HandlerException( "The tweet text is not specified." );
        
        $response = $oauth->post('statuses/update', array(
            'status' => $message
        ));
        
        if ( isset( $response->errors ) ) {
            
            // the tweet already is twitted
            if ( $response->errors[0]->code == 187 ) {
                echo json_encode(array('success' => true)); 
                exit;
            }
            
            echo json_encode(array('error' => $response->errors[0]->message )); 
            exit;
        }

        do_action('opanda_tweet_posted', $contextData );
        
        echo json_encode($response);
        exit;
    }
    
    protected function getGuid() {
        if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}


