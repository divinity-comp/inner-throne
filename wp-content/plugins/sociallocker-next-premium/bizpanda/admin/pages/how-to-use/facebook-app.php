<div class="onp-help-section">
    <h1><?php _e('Creating Facebook App', 'bizpanda'); ?></h1>
    
    <p>
        <?php _e('A Facebook App is required for the following buttons:', 'bizpanda'); ?>
        <ul>
            <?php if ( BizPanda::hasPlugin('sociallocker') ) { ?>
            <li><?php _e('Facebook Share of the Social Locker.', 'bizpanda') ?></li>
            <?php } ?>
            <li><?php _e('Facebook Sign-In of the Sign-In Locker (<a href="' . opanda_get_help_url('ssl') . '">SSL required</a>).', 'bizpanda') ?></li>
            <?php if ( BizPanda::hasPlugin('optinpanda') ) { ?>
            <li><?php _e('Facebook Subscribe of the Email Locker (<a href="' . opanda_get_help_url('ssl') . '">SSL required</a>).', 'bizpanda') ?></li>      
            <?php } ?>
        </ul>
    </p>
    <p><?php _e('If you want to use these buttons, you need to register a Facebook App for your website. Otherwise you can use the default Facebook App Id (117100935120196).', 'bizpanda') ?></p>
    <p><?php _e('In other words, <strong>you don\'t need to create an own app</strong> if you\'re not going to use these Facebook buttons.') ?></p>

</div>

<div class="onp-help-section">
    <p><?php printf( __('1. Open the website <a href="%s" target="_blank">developers.facebook.com</a> and click <strong>Add a New App</strong> (you have to be logged in):', 'bizpanda'), 'https://developers.facebook.com/' ) ?></p>
    <p class='onp-img'>
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/1.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('2. Type your app name (it will be visible for users), email address (for notifications from Facebook) and click <strong>Create App ID</strong>:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/2.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('3. Pass the security check if required and click on <strong>Submit</strong>:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/3.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('4. Click <strong>Settings -> Basic</strong> in the menu at the left:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/4.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('5. Twice enter your site domain name: without "www" and with "www", check your email address, select the category and paste links to Terms & Policy pages:', 'bizpanda') ?></p>
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Field', 'bizpanda') ?></th>
                <th><?php _e('How To Fill', 'bizpanda') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
              <td class="onp-title"><?php _e('App Domains', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Paste these domains:', 'bizpanda') ?></p>
                    <p><i><?php echo opanda_get_domain( get_site_url() ) ?></i>
                    <p><i><?php echo 'www.' . opanda_get_domain( get_site_url() ) ?></i>
                    </p>
                </td>
            </tr>         
            <tr>
              <td class="onp-title"><?php _e('Privacy Policy URL', 'bizpanda') ?></td>
                <td>
                    <p><?php printf( __('Paste the URL (you can edit it <a href="%s" target="_blank">here</a>):', 'bizpanda'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index' ) ) ?></p>
                    <p><i><?php echo opanda_privacy_policy_url(true) ?></i>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Terms of Service URL', 'bizpanda') ?></td>
                <td>
                    <p><?php  printf( __('Paste the URL (you can edit it <a href="%s" target="_blank">here</a>):', 'bizpanda'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index' ) ) ?></p>
                    <p><i><?php echo opanda_terms_url(true) ?></i>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/5.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('6. Fill the form <strong>Data Protection Officer Contact Information</strong> below if required according to your business:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/6.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('7. Click on <strong>Add Platform</strong> after the forms:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/7.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('8. Select <strong>Website</strong>:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/8.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('9. Specify an URL of your website and save the changes:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/9.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('10. Move to the section <strong>App Review</strong>:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/10.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('11. Make your app available to the general public:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/11.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('12. Copy your app id:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/facebook-app/v3/12.png">
    </p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('13. Paste your Facebook App Id on the page Global Settings > <a href="%s">Social Options</a>.', 'bizpanda' ), opanda_get_settings_url('social') ) ?></p>
    <p><?php printf( __('Feel free to <a href="%s">contact us</a> if you faced any troubles.', 'bizpanda'), opanda_get_help_url('troubleshooting') ) ?></p>
</div>

