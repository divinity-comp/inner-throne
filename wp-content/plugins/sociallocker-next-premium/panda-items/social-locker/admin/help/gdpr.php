<?php

global $bizpanda;
$lang = $bizpanda->options['lang'];
?>

<div class="onp-help-section">
    <h1><?php _e('Social Locker GDPR Compatibility', 'sociallocker'); ?></h1>
    
    <p>
        <?php _e('The General Data Protection Regulation (GDPR) is a new data protection law in the EU that takes effect on May 25, 2018.', 'sociallocker') ?>      
        <?php _e('GDPR covers processing personal data.') ?>
    </p>
    
    <p>
        <strong><?php _e('Social Locker is fully compatible with GDPR out of the box.</strong>', 'sociallocker') ?></strong>
    </p>
    
    <p>
        <?php _e('Social Locker doesn\'t collect any personal data when a user clicks on like/share buttons. So you don\'t need to add any Consent Checkboxes or refer to your Terms of Use and Privacy Policy.') ?>
    </p>
    
    <p>
        <?php _e('Please note, for other types of content lockers (Sign-In Locker / Email Locker) you need to activate the Consent Checkbox for GDPR Compatibility.')?>
    </p>   
</div>
