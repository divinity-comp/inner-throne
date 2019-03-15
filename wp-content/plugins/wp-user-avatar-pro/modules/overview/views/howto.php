<?php
/**
 * Posts Pro Overviews.
 * @package avatar
 * @author Flipper Code <flippercode>
 **/
$form = new WPUAP_FORM();
echo $form->show_header();
?>
<div class="flippercode-ui">
	<div class="fc-main fc-how">
	  <div class="fc-container">
	       <div class="fc-divider"> 
	        <div class="fc-back fc-ecp">
	        <div class="fc-12">
           <h4 class="fc-title-blue"> <?php _e( 'Introduction',WPUAP_TEXT_DOMAIN ); ?> </h4>
          <div class="wpgmp-overview">
            <ul>
                <li>
					<?php _e( '<p>WP User Avatar Pro is avatar creation utility to setup profile images in a WordPress site. This plugin works with BBPress, BuddyPress is multisite friendly so it’s an all-in-one solution for user profile images. Store the avatar files in Dropbox, Amazon S3, Media Manager, or a custom folder.</p>
						
						<p>You can choose a profile image from the file system, media manager, or webcam. We have created an easy to use interface to make the process simple. You can also crop and resize the profile image to your liking.</p>', WPUAP_TEXT_DOMAIN ) ?>
                </li>
            </ul>
        </div>
        
</div>
<div class="fc-12">
<!-- second section start here -->
        <h4 class="fc-title-blue">
            <?php _e( 'How It Works', WPUAP_TEXT_DOMAIN ) ?>
        </h4>
    <div class="wpgmp-overview">
        <ul>
            <li><?php _e( 'Go to plugin settings and select "Default Avatar" from WP User Avatar, Mystery Man, Gravatar Logo, Identicon, Wavatar, MonsterID, Retro options. Set avatar based on roles. After setup all required settings save settings. By saving settings all user avatar will be changed as plugin settings.', WPUAP_TEXT_DOMAIN ) ?></li>
        </ul>
    </div>
</div>
<div class="fc-12">
<!-- second section start here -->
        <h4 class="fc-title-blue">
            <?php _e( 'Shortcodes', WPUAP_TEXT_DOMAIN ) ?>
        </h4>
    <div class="wpgmp-overview">
        <h5><?php _e( 'We have three shortcodes that we can use to show user avatar on posts/pages and sidebar.', WPUAP_TEXT_DOMAIN ) ?></h5>
        <ol>            
            <li><b>[avatar]</b> - Used to show avatar. Following are some attributes and thier values that we can pass to this shortcode.
                <table class="fc-placeholders-listing">
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>user</td>
                            <td>{user_id or email} - which user's avatar we want to show.</td>
                        </tr>
                        <tr>
                            <td>size</td>
                            <td>Options: original/large/medium/thumbnail/{size in pixels} - size of avatar to show.</td>
                        </tr>
                        <tr>
                            <td>align</td>
                            <td>left/center/right - align the avatar.</td>
                        </tr>
                        <tr>
                            <td>link</td>
                            <td>{link to add on avatar} - eg. http://www.flippercode.com/ </td>
                        </tr>
                        <tr>
                            <td>target</td>
                            <td>_blank/_parent/_self/_top - where to oper avatar link i.e in new window or on same page or in new tab.</td>
                        </tr>
                    </tbody>
                </table>
            </li>                        
            <li><b>[avatar_listing]</b> -  Listing user avatar's. Following are some attributes and thier values that we can pass to this shortcode.
                <table class="fc-placeholders-listing">
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>display_type</td>
                            <td>current_user/latest_users - What type useres we have to show.</td>
                        </tr>
                        <tr>
                            <td>how_many</td>
                            <td> Default: 1 - Number of max. users to be listed.</td>
                        </tr>
                        <tr>
                            <td>show_link</td>
                            <td>true/false - Show links on avatar.</td>
                        </tr>
                        <tr>
                            <td>show_name</td>
                            <td>true/false - Show name with avatar.</td>
                        </tr>
                        <tr>
                            <td>show_bio</td>
                            <td>true/false - Show Biographical Info of user.</td>
                        </tr>        
                    </tbody>
                </table>
            </li>
            <li><b>[avatar_upload]</b> - Upload user's avatar. Following are some attributes and thier values that we can pass to this shortcode.
                <table class="fc-placeholders-listing">
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>user</td>
                            <td>{user_id or email} - which user's avatar have to upload for update. Leave blank to upload current users avatar.</td>
                        </tr>                           
                    </tbody>
                </table>
            </li>
        </ol>
    </div>
</div>
<div class="fc-12">
<!-- Third section start here -->
	<h4 class="fc-title-blue">
        <?php _e( 'Following are some screenshots.', WPUAP_TEXT_DOMAIN ) ?>
    </h4>
    <div class="wpgmp-overview">
        <ul>
            <li><?php _e( '<b>Easy to use Backend Settings</b>', WPUAP_TEXT_DOMAIN ) ?></li>
            <li><?php _e( '<img src="'.WPUAP_SCREENSHOTS.'/Easy to use Backend Settings.png" width="60%">', WPUAP_TEXT_DOMAIN ) ?></li>
            <li><?php _e( '<b>Choose Avatar using Media Manager</b>', WPUAP_TEXT_DOMAIN ) ?></li>
            <li><?php _e( '<img src="'.WPUAP_SCREENSHOTS.'/image-upload-media-manager.png">', WPUAP_TEXT_DOMAIN ) ?></li>
            <li><?php _e( '<b>Choose Avatar using Files System</b>', WPUAP_TEXT_DOMAIN ) ?></li>
            <li><?php _e( '<img src="'.WPUAP_SCREENSHOTS.'/image-upload-using-computer.png">', WPUAP_TEXT_DOMAIN ) ?></li>
            <li><?php _e( '<b>Capture Avatar Using Webcam</b>', WPUAP_TEXT_DOMAIN ) ?></li>
            <li><?php _e( '<img src="'.WPUAP_SCREENSHOTS.'/image-upload-webcam.png">', WPUAP_TEXT_DOMAIN ) ?></li>
        </ul>
        <p>If still any issue, Create your <a target="_blank" href="http://www.flippercode.com/forums">support ticket</a> and we'd be happy to help you asap. </p>
    </div>
</div>
</div>
</div>
</div>
</div>