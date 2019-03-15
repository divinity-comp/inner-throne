<div class="onp-help-section">
    <h1><?php _e('Connection to Zapier', 'bizpanda'); ?></h1>
    <p><?php printf( __("You can send data collected via your lockers to Zapier and then automatically pass them to other web apps supported by Zapier. For example, to Google Docs. <a href='%s' target='_blank'>Click here</a> to learn more about Zapier.", "bizpanda"), 'https://zapier.com/help/what-is-zapier/' ); ?></p>
    <p><?php _e('To connect the plugin to Zapier, please follow the steps below:', 'bizpanda'); ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('1. Make sure that you have a Zapier account. If not, create one here: <a href="https://zapier.com/sign-up/" target="_blank">https://zapier.com/sign-up/</a>', 'bizpanda') ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('2. Pick <strong>Webhooks</strong> and the apps where you wish to send data to. Learn more about Webooks here: <a href="https://zapier.com/apps/webhook/integrations" target="_blank">https://zapier.com/apps/webhook/integrations</a>', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/1.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('3. Select a <strong>Zap</strong> you need by clicking on <strong>Use this Zap</strong>. For example, Webkooks + Google Sheet:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/2.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('4. While creating a Zap, select <strong>Catch Hook</strong> option:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/3.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('5.  Skip the step <strong>Pick off a Child Key</strong> if you don\'t have any preferences for this option or you  aren\'t sure how to set it up:', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/4.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('6. Copy a webhook URL on the step <strong>Test Webhooks by Zapier</strong>. The option Silent Mode may be turned off or turned on, it doesn\'t have matter.', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/5.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php printf( __('7. Paste the webhook URL on the page Global Settings -> <a href="%s" target="_blank">Zapier</a> and click Save Changes. Make sure that the options <strong>Only New Leads</strong> and <strong>Only Confirmed Leads</strong> turned off (you need to disable them for testing connection on the next step, later you can configure them as you need).', 'bizpanda'), opanda_get_settings_url('zapier') ) ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/6.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('8. Pass connection test on Zapier. For that, on the page Test Webhooks by Zapier after saving the webhook URL in the settings of the plugin, click the button <strong>Ok, I did this</strong>.', 'bizpanda') ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('9. While the spinner runs, open a webpage on your website where the locker is located and unlock it.', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/7.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('10. If the hook is fired successfully, you will see the success message, if not try to refresh the Zapier page.', 'bizpanda') ?></p>
    <p class="onp-img">
        <img src="http://cconp.s3.amazonaws.com/bizpanda/zapier/8.jpg">
    </p>
</div>

<div class="onp-help-section">
    <p><?php _e('11.Complete other configuration steps on Zapier and enjoy your results.', 'bizpanda') ?></p>
    <p><?php printf( __('Feel free to <a href="%s">contact us</a> if you faced any troubles.', 'bizpanda'), opanda_get_help_url('troubleshooting') ) ?></p>
</div>
