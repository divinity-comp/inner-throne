<div class="onp-help-section">
    <h1><?php _e('Getting Google Client ID', 'bizpanda'); ?></h1>

    <p>
        <?php _e('A Google Client ID is required for the following buttons:', 'bizpanda'); ?>
        <ul> 
            <li><?php _e('Google Share of the Social Locker.', 'bizpanda') ?></li>       
            <li><?php _e('Google +1 of the Social Locker (works as the Google Share).', 'bizpanda') ?></li>     
            <li><?php _e('YouTube Subscribe of the Social Locker.', 'bizpanda') ?></li>            
            <li><?php _e('Google Sign-In of the Sign-In Locker.', 'bizpanda') ?></li>
            <?php if ( BizPanda::hasPlugin('optinpanda') ) { ?>
            <li><?php _e('Google Subscribe of the Email Locker.', 'bizpanda') ?></li>  
            <?php } ?>
        </ul>
    </p>
    
    <p><?php _e('If you want to use these buttons, you need to get Google Client ID App for your website.', 'bizpanda') ?>
    <?php _e('<strong>You don\'t need to get a Client ID</strong> if you\'re not going to use these buttons.') ?></p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('1. Go to the <a href="%s" target="_blank">Google Developers Console</a>.', 'bizpanda'), 'https://console.developers.google.com/project' ) ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('2. Click "Select a project":', 'bizpanda') ?></p>
    <p class='onp-img'>
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/1.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('2. Click "Select a project":', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/1.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('3. Click the button with the plus icon:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/2.png">
    </p>
</div> 

<div class="onp-help-section">
    <p><?php _e('4. Enter a new project name (for example, your website name) and click "Create":', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/3.png">
    </p>
</div>
          
<div class="onp-help-section">
    <p><?php _e('5. Again click "Select a project":', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/1.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('6. And select your project you have just created:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/4.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('7. Make sure that you are in the Library section. Find and enable the following APIs:', 'bizpanda') ?></p>
    <ul>
        <li><?php _e('<strong>Google+ API</strong> (to use the Google Plus, Google Share and Sign-In buttons)', 'bizpanda') ?></li>
        <li><?php _e('<strong>YouTube APIs</strong> (to use the YouTube Subscribe button)', 'bizpanda') ?></li> 
    </ul>
    <p><?php _e('To enable these APIs, click on a title of the required API in the list and then click the button "Enable".', 'bizpanda') ?></p>
    <p class="onp-img">
            <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/5.png">
    </p>
    <p class="onp-img">
            <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/6.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('8. Move to the "Credentials" section:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/7.png">
    </p>
</div>     

<div class="onp-help-section">
    <p><?php _e('9. Create new credentials "OAuth client ID"', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/8.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('10. Google may ask you to configure a consent screen before creating OAuth client ID, at this case follow the Google instruction and then return back:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/9.png">
    </p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/10.png">
    </p>
</div>

<?php
    $origin = null;
    $pieces = parse_url( site_url() );
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
        $origin = $regs['domain'];
    }
?>

<div class="onp-help-section">
    <p><?php _e('11. Fill up the form:', 'bizpanda' ) ?></p>
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Field', 'bizpanda') ?></th>
                <th><?php _e('How To Fill', 'bizpanda') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="onp-title"><?php _e('Application Type', 'bizpanda') ?></td>
                <td>
                    <p>Web Application</p>
                </td>
            </tr>   
            <tr>
                <td class="onp-title"><?php _e('Authorized Javascript origins', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Add the origins:', 'bizpanda') ?></p>
                    <p><i><?php echo 'http://' . str_replace('www.', '', $origin) ?></i></p>
                    <p><i><?php echo 'http://www.' . $origin ?></i></p>
                    <p><?php _e('If you use SSL, additionally add URLs with "https"', 'bizpanda') ?></p>
                </td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Authorized redirect URIs', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Paste the URL:', 'bizpanda') ?></p>
                    <p><i><?php echo add_query_arg( array(
                            'action' => 'opanda_connect',
                            'opandaHandler' => 'google'
                        ), admin_url('admin-ajax.php') ) ?></i>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <p class='onp-img'>
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/11.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('12. After clicking on the button Create, you will see your new Client ID:', 'bizpanda' ) ?></p>
    <p class='onp-img'>
        <img src="http://cconp.s3.amazonaws.com/bizpanda/google-app/v2/12.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('10. Copy and paste it on the page Global Settings > <a href="%s">Social Options</a>.', 'bizpanda' ), opanda_get_settings_url('social') ) ?></p>
    <p><?php printf( __('Feel free to <a href="%s">contact us</a> if you faced any troubles.', 'bizpanda'), opanda_get_help_url('troubleshooting') ) ?></p>
</div>