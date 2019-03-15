<?php


/**
 * getCometChatDockedCode
 * @return Docked html code
 */
function getCometChatDockedCode(){
    global $wpdb, $wp_roles;

    $cc_dir_name = getCometChatDirectoryName();
    $roles = array_keys($wp_roles->get_names());
    $hideDockedLayout = 1;
    $role = array();

    if(file_exists(ABSPATH.$cc_dir_name.'/license.php')){
        $prefix = $wpdb->prefix;
        $sql = ("SELECT value from cometchat_settings where setting_key = 'hideDockedLayout'");
        $result = $wpdb->get_row($sql);

        if(!empty($result) && $result->value != ""){
            $hideDockedLayout = $result->value;
        }

        foreach ($roles as $key => $value) {
            $sql = ("SELECT option_value FROM ".$prefix."options WHERE option_name = '".$value."'");

            $result = $wpdb->get_row($sql);
            if(!empty($result) && !empty($result->option_value)){
                $settings_key = $result->option_value;
                $role[$value] = $settings_key;
            }
        }

        if($hideDockedLayout == 0){
            return;
        }

        $site_url = get_site_url();
        $loginUserInfo = wp_get_current_user();

        if(!empty($loginUserInfo->roles) && !empty($role) && $role[$loginUserInfo->roles[0]] == 'true'){
            $footercode = '';
        }else{
            wp_enqueue_style("cc_corecss", $site_url."/".$cc_dir_name."/cometchatcss.php");
            wp_enqueue_script("cc_corejs", $site_url."/".$cc_dir_name."/cometchatjs.php");
        }
    }
}

/**
 * getCometChatShortCode
 * @param mixed $atts = width, height, layout, groupid, groupsonly
 * @return shortcode
 */
function getCometChatShortCode($atts){
    extract(shortcode_atts(array(
    'width' => 400,
    'height' => 420,
    'layout' => 'embedded',
    'groupid' => 0,
    'groupsonly' => 0
    ), $atts));
    $site_url = get_site_url();
    $cc_dir_name = getCometChatDirectoryName();

    if(!empty($groupsonly)){
        $groupsonly = 1;
    }

    if($layout == 'docked'){
        wp_enqueue_style("cc_corecss", $site_url."/".$cc_dir_name."/cometchatcss.php");
        wp_enqueue_script("cc_corejs", $site_url."/".$cc_dir_name."/cometchatjs.php");
    } else{
        wp_enqueue_script( 'cc_shortcodejs', $site_url.'/'.$cc_dir_name.'/js.php?type=core&name=embedcode' );

        wp_enqueue_script( 'cc_shortcode', plugin_dir_url( __DIR__ ).'js/scripttag.js' );
        wp_add_inline_script( 'cc_shortcode', 'var iframeObj = {};iframeObj.module="synergy";iframeObj.style="min-height:420px;min-width:350px;";iframeObj.width="'.$width.'px";iframeObj.height="'.$height.'px";iframeObj.src="'.$site_url.'/'.$cc_dir_name.'/cometchat_embedded.php?crid='.$groupid.'&chatroomsonly='.$groupsonly.'";if(typeof(addEmbedIframe)=="function"){addEmbedIframe(iframeObj);}' );

        echo '<div id="cometchat_embed_synergy_container" style="width:'.$width.'px;height:'.$height.'px;max-width:100%;border:1px solid #CCCCCC;border-radius:5px;overflow:hidden;"</div>';
    }
}

function cometchatInboxSyncWithBuddyPress( BP_Messages_Message $message){
    $cc_dir_name = getCometChatDirectoryName();
    if(get_option('inbox_sync') == 'true'){
        global $wpdb;
        $from = bp_loggedin_user_id();
        $msg = $_POST['content'];
        $sent = time();
        $url = get_site_url()."/".$cc_dir_name."/cometchat_send.php";
        if(!isset($_SESSION['random'])){
            $rand1 = rand(10000000000000000000,99999999999999999999);
            $rand2 = rand(1000000000000,9999999999999);
            $callback = 'jqcc'.$rand1.'_'.$rand2;
            $_SESSION['random'] = $callback;
        }else{
            $callback = $_SESSION['random'];
        }
        $prefix = $wpdb->prefix;
        $sql = ("SELECT option_value from ".$prefix."options where option_name = 'hash_value'");
        $result = $wpdb->get_row($sql);
        $hash = $result->option_value;
        $hashval = md5($hash.$from);
        $recipients = $message->recipients;
        foreach ($recipients as $key => $value) {
            if($recipients[$key]->user_id != $from){
                $to = $recipients[$key]->user_id;
                if (function_exists('curl_init')){

                    $fields_string = array(
                        'cc_social_userid' => $from,
                        'to' => $to,
                        'message' => $msg,
                        'callback' => $callback,
                        'hash_val' => $hashval,
                        'deny_sanitize' => 'true',
                        'deny_hooks_message' => 'true',
                        'cc_direction' => 0
                    );

                    $result = wp_remote_post($url, array(
                            'method' => 'POST',
                            'body' => $fields_string
                        )
                    );
                }else{
                    $sql = ("INSERT INTO cometchat(`from`,`to`,`message`,`sent`,`read`,`direction`) VALUES ('".$from."','".$to."','".$msg."','".$sent."',1,0)");
                    $wpdb->query($sql);
                }
            }
        }
    }
}

add_action('wp_head', 'getCometChatDockedCode');
add_action( 'messages_message_after_save', 'cometchatInboxSyncWithBuddyPress');
add_shortcode('cometchat', 'getCometChatShortCode');

?>