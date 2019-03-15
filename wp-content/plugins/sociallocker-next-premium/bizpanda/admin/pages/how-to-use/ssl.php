<div class="onp-help-section">
    <h1><?php _e('Installation SSL Certificate', 'bizpanda'); ?></h1>

    <p>
        <?php _e('All new Facebook apps created as of March 2018 have to use only HTTPS URLs when using Facebook Login.', 'bizpanda'); ?>
        <?php _e('It means that you need to install a SSL certificate for following Facebook buttons:', 'bizpanda'); ?>
        <ul>
            <li><?php _e('Facebook Sign-In of the Sign-In Locker.', 'bizpanda') ?></li>
            <?php if ( BizPanda::hasPlugin('optinpanda') ) { ?>
            <li><?php _e('Facebook Subscribe of the Email Locker.', 'bizpanda') ?></li>      
            <?php } ?>
        </ul>
    </p>
    <p><?php _e('If you wish to use these buttons, you need to install a SSL certificate for your website. Otherwise, you don\'t need a SSL certificate.', 'bizpanda') ?></p>
    <p><?php  _e('You can use <strong>one of the following ways</strong> to install a SSL certificate:', 'bizpanda') ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('1. The most easiest way is to check a <strong>control panel</strong> of your hosting provider. The most hosting providers have some options to install SSL certificates automatically. It may be a free or paid service.', 'bizpanda') ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('2. Another easy way is to use <strong>security services</strong> like <a href="https://www.cloudflare.com/ssl/" target="_blank">Cloudflare</a>. It provides an option to enable a SSL certificate for your website automatically.', 'bizpanda') ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('3. The standard way is to grab a SSL certificate <strong>by yourself</strong> and install it on your server manually or to ask your hosting provider to do that. For example, you can get a free SSL certificate on <a href="http://letsencrypt.org" target="_blank">letsencrypt.org</a>.', 'bizpanda') ?></p>
</div>

<div class="onp-help-section">
    <p><?php _e('After successfully installation of SSL you will probably need to install <a href="https://wordpress.org/plugins/really-simple-ssl/" target="_blank">this free plugin</a> to get better control over SSL on your website.', 'bizpanda') ?></p>
    <p><?php printf( __('Feel free to <a href="%s">contact us</a> if you faced any troubles.', 'bizpanda'), opanda_get_help_url('troubleshooting') ) ?></p>
</div>