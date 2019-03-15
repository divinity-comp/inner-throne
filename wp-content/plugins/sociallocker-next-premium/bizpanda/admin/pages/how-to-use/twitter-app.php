<div class="onp-help-section">
    <h1><?php _e('Creating Twitter App', 'bizpanda'); ?></h1>

    <p>
        <?php _e('A Twitter App is required  for the following buttons:', 'bizpanda' ) ?>
        <ul>
            <?php if ( BizPanda::hasPlugin('sociallocker') ) { ?>
            <li><?php _e('Tweet Button of the Social Locker when the option Double Check is enabled. If you wish to use the Tweet button without the option Double Check, you don\'t need to create a Twitter App.', 'bizpanda') ?></li>
            <?php } ?>
            <li><?php _e('Twitter Sign-In of the Sign-In Locker.', 'bizpanda') ?></li>
            <?php if ( BizPanda::hasPlugin('optinpanda') ) { ?>
            <li><?php _e('Twitter Subscribe of the Email Locker.', 'bizpanda') ?></li>      
            <?php } ?>
        </ul>
    </p>
    <p>If you want to use these buttons, you need to register a Twitter App for your website.</p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('1. Open the website <a href="%s" target="_blank">developer.twitter.com/en/apps</a> and click <strong>Create an app</strong> (you have to be signed in).', 'bizpanda'), 'https://developer.twitter.com/en/apps' ) ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('2. Fill up the form, agree to the Developer Agreement, click <strong>Create Your Twitter application</strong>.', 'bizpanda' ) ?></p>
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Field', 'bizpanda') ?></th>
                <th><?php _e('How To Fill', 'bizpanda') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="onp-title"><?php _e('App name', 'bizpanda') ?></td>
                <td><?php _e('The best app name is your website name.', 'bizpanda') ?></td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('App Description', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Explain why you ask for the credentials, e.g:', 'bizpanda') ?></p>
                    <p><i><?php _e('This application asks your credentials in order to unlock the content. Please read the TOS.', 'bizpanda') ?></i></p>
                </td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Website URL', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Paste your website URL:', 'bizpanda') ?></p>
                    <p><i><?php echo site_url() ?></i></p>
                </td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Enable Sign in with Twitter', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Mark it.', 'bizpanda') ?></p>
                </td>
            </tr>            
            <tr>
                <td class="onp-title"><?php _e('Callback URL', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Callback URLs:', 'bizpanda') ?></p>
                    <p><i><?php echo admin_url('admin-ajax.php') ?></i>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Terms of Service URL', 'bizpanda') ?></td>
                <td>
                    <p><?php printf( __('Paste the URL (you can edit it <a href="%s" target="_blank">here</a>):', 'bizpanda'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index' ) ) ?></p>
                    <p><i><?php echo opanda_terms_url(true) ?></i>
                    <div class="alert alert-warning"><?php printf( __('For some reasons Twitter does not permit URLs containing a query part ("?="). If your URL contains it, you will see your URL is invalid. <a href="%s" target="_blank">Click here</a>, turn on the option Use Existing Pages and select a page you would like to use as Terms of Service.'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index' ) ) ?></div>       
                </td>
            </tr>   
            <tr>
                <td class="onp-title"><?php _e('Privacy policy URL', 'bizpanda') ?></td>
                <td>
                    <p><?php printf( __('Paste the URL (you can edit it <a href="%s" target="_blank">here</a>):', 'bizpanda'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index' ) ) ?></p>
                    <p><i><?php echo opanda_privacy_policy_url(true) ?></i></p>
                    <div class="alert alert-warning"><?php printf( __('For some reasons Twitter does not permit URLs containing a query part ("?="). If your URL contains it, you will see your URL is invalid. <a href="%s" target="_blank">Click here</a>, turn on the option Use Existing Pages and select a page you would like to use as Privacy policy.'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index' ) ) ?></div>  
                </td>
            </tr> 
            <tr>
                <td class="onp-title"><?php _e('Tell us how this app will be used', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Explain how your app works, e.g:', 'bizpanda') ?></p>   
                    <p><i><?php _e('This app asks visitors of our website to sign in via Twitter or tweet/follow to get access to restricted content available only for registered users or followers.', 'bizpanda') ?></i></p>
                </td>
            </tr>  
        </tbody>
    </table>
</div>

<div class="onp-help-section">
    <p><?php _e('3. Move to the section "Permissions", mark <strong>Read and Write</strong> (if you are going to use tweeting functionality) or <strong>Read Only</strong> (if you are NOT going to use tweeting functionality) and save changes.', 'bizpanda' ) ?></p>
    <p><?php _e('If you are going to use the Twitter Sign-In Button, mark the permission <strong>Request email addresses from users</strong> in the section "Additional Permissions".','bizpanda') ?></p>
    <p class='onp-img'>
        <img src='http://cconp.s3.amazonaws.com/bizpanda/twitter-app/4.png' />
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('4. Move to the section "Keys and tokens", find your API key and API secret key:', 'bizpanda' ) ?></p>
    <p class='onp-img'>
        <img src='http://cconp.s3.amazonaws.com/bizpanda/twitter-app/5.png' />
    </p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('5. Paste your key and secret on the page Global Settings > <a href="%s">Social Options</a>.', 'bizpanda' ), admin_url('admin.php?page=settings-bizpanda&opanda_screen=social') ) ?></p>
    <p><?php printf( __('Feel free to <a href="%s">contact us</a> if you faced any troubles.', 'bizpanda'), opanda_get_help_url('troubleshooting') ) ?></p>
</div>

<!--div class="onp-help-section">
    <p class='onp-note'>
        <?php _e('By default Twitter does not return an <strong>email address</strong> of the user until your app is not got whitelisted. To make your app whitelisted, please follow the instruction below.', 'bizpanda') ?>
    </p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('9. Visit Twitter Help Center: <a href="https://support.twitter.com/forms/platform" target="_blank">https://support.twitter.com/forms/platform</a>', 'bizpanda' ), admin_url('admin.php?page=settings-optinpanda&opanda_screen=social') ) ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('10. Choose <strong>I need access to special permissions</strong>, fill and submit the form:', 'bizpanda' ) ?></p>
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Field', 'bizpanda') ?></th>
                <th><?php _e('How To Fill', 'bizpanda') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="onp-title"><?php _e('Application Name', 'bizpanda') ?></td>
                <td><?php _e('Enter your app name you typed in the step 2.', 'bizpanda') ?></td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Application ID', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('You can find your app ID in the URL when viewing your app on the apps.twitter.com.', 'bizpanda') ?></p>
                    <p class='onp-img'>
                        <img src='http://cconp.s3.amazonaws.com/bizpanda/twitter-app/8.png'  style="width: 400px;" />
                    </p>
                </td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Permissions Requested', 'bizpanda') ?></td>
                <td>
                    <p><?php _e('Explain what permissions you need:', 'bizpanda') ?></p>
                    <p><i><?php _e('Please enable the permission "Request email addresses from users" for my app. I want to use the option "include_email" while requesting "account/verify_credentials". I ask visitors of my website to sign in by using their Twitter accounts and need to know their emails.', 'bizpanda') ?></i></p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="onp-help-section">
    <p><?php printf( __('10. <strong>Within 2-3 business days</strong>, you will get a reply from Twitter. If the email permission was successfully granted for your app, visit <a href="%s" target="_blank">apps.twitter.com</a> and click on the title of your app.', 'bizpanda' ), 'https://apps.twitter.com' ) ?></p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('11. Click on the tab <strong>Settings</strong>, fill the fields and save the form:', 'bizpanda' ), 'https://apps.twitter.com' ) ?></p>
    <table class="table">
        <thead>
            <tr>
                <th><?php _e('Field', 'bizpanda') ?></th>
                <th><?php _e('How To Fill', 'bizpanda') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="onp-title"><?php _e('Privacy Policy URL', 'bizpanda') ?></td>
                <td><i><?php echo opanda_privacy_policy_url() ?></i></td>
            </tr>
            <tr>
                <td class="onp-title"><?php _e('Terms of Service URL', 'bizpanda') ?></td>
                <td><i><?php echo opanda_terms_url() ?></i></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="onp-help-section">
    <p><?php printf( __('11. Click on the tab <strong>Permissions</strong>, mark the checkbox <strong>Request email addresses from users</strong> and save the changes.', 'bizpanda' ), 'https://apps.twitter.com' ) ?></p>
</div-->