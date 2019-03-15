<?php

global $bizpanda;
$lang = $bizpanda->options['lang'];
?>

<div class="onp-help-section">
    <h1><?php _e('Sign-In Locker GDPR Compatibility', 'sociallocker'); ?></h1>
    
    <p>
        <?php _e('The General Data Protection Regulation (GDPR) is a new data protection law in the EU that takes effect on May 25, 2018.', 'sociallocker') ?>      
        <?php _e('GDPR covers processing personal data.') ?>
    </p>
    
    <p>
        <strong><?php _e('Sign-In Locker is fully compatible with GDPR if the option Consent Checkbox is activated.</strong>', 'sociallocker') ?></strong>
    </p>
    
    <p>
        <?php _e('Sign-In Locker collects and processes personal data covered by GDPR. So if your website interacts with EU citizens, you need to activate the option Consent Checkbox.') ?>
    </p>  
    
    <p>
        <?php _e('Please note, Sign-In Locker doesn\'t send any personal data to our servers (to servers of the plugin developer). All data are stored only on your website and 3d party services you configured by yourself to work with Sign-In Locker.') ?>
    </p>   
</div>
