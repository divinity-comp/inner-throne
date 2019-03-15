<?php
$plugin_media_url = plugin_dir_url(__FILE__) . '/images/';
function media_url(){
    echo plugin_dir_url(__FILE__) . '/images/';
}
$has_security_plugins = false;
if(isset($response['security_plugins']) && $response['security_plugins'] )
{
    foreach ($response['security_plugins'] as $key => $value)
    {
        if($value==1)
        {
            $has_security_plugins = true;
            break;
        }
    }
}
$has_security_plugins = false;
if(isset($response['security_plugins']) && $response['security_plugins'] )
{
    foreach ($response['security_plugins'] as $key => $value)
    {
        if($value==1)
        {
            $has_security_plugins = true;
            break;
        }
    }
}
?>
<div class="contentstudio-plugin-container">
    <div class="contentstudio-plugin-head">
        <div class="contentstudio-content-section">
            <img src="<?php media_url();?>logo.png" width="260" alt="ContentStudio">
        </div>
    </div>
    <div class="contentstudio-lower">
        <div class="contentstudio-content-section">
            <div class="contentstudio-box">
                <div class="left_section">
                    <h2>
                        <?php
                        if(isset($response) && isset($response['status']) && $response['status'] == true):
                            ?>
                            Connection Status
                        <?php elseif (isset($response) && isset($response['status']) && $response['reconnect'] == true):
                            ?>
                            Reconnect Website
                        <?php else:
                            ?>
                            Connect Website
                        <?php endif;?>
                    </h2>
                </div>
                <div class="right_section">
                    <?php
                    if (isset($response) && isset($response['status']) && $response['reconnect'] == true):
                    ?>
                        <a href="<?php echo admin_url() . 'admin.php?page=contentstudio_settings';?>">Go Back</a>
                    <?php endif;?>
                </div>
                <div class="clear"></div>
            </div>

            <div class="contentstudio-box center_aligned">
                <?php
                if($has_security_plugins)
                {
                    ?>
                    <p class="security-plugins-notify">Your have security plugins installed, please whitelist ContentStudio IP addresses in the following plugins:</p>
                    <ul>
                        <?php
                        foreach ($response['security_plugins'] as $key => $value)
                        {
                            if($value==1)
                            {
                                echo '<li class="warning-plugin"><img class="warning-plugin-img" src="' . $plugin_media_url . 'warning.svg">'. str_replace("_", " ", $key ).'</li>';
                            }
                        }
                        ?>
                    </ul>
                    <?php
                }
                ?>
                <?php
                if (isset($response) && isset($response['status']) && $response['status'] == true && $response['reconnect'] == false) {
                    ?>

                    <h3>
                        <div class="notify-success-image">
                            <img src="<?php media_url();?>round.svg" class="img_success">
                        </div>
                    </h3>
                    <h3>
                        Your website is connected with ContentStudio platform.
                    </h3>
                    <p>
                        Do you want to reconnect your website? <a
                                href="<?php echo admin_url() . 'admin.php?page=contentstudio_settings&reconnect=true' ?>">Click
                            here</a>.
                    </p>
                    <?php
                }
                else {


                    ?>
                    <h3>
                        Enter your Website API key to connect with ContentStudio <span class="cs_info"><a
                                    href="https://docs.contentstudio.io/article/159-where-do-i-get-api-key-for-contentstudio-plugin" target="_blank">(What is an API key?)</a></span>
                    </h3>
                    <?php
                    if (isset($response) && isset($response['reconnect']) && $response['reconnect'] == false) {
                        ?>
                        <p>
                            Don't have a ContentStudio account? <a href="https://app.contentstudio.io/register?utm_source=blog-plugin">Create an account</a>
                        </p>
                    <?php } ?>
                    <div class="contentstudio-input">
                        <form action="javascript:;" method="post" id="apiKey">
                            <div class="input_field">
                                <input name="api_key" type="text" class="regular-text code api_key">
                            </div>
                            <div class="input_submit">
                                <input type="submit" class="regular-text code" value="Connect With API Key">
                            </div>

                            <?php
                            if (isset($response) && isset($response['reconnect']) && $response['reconnect']) {
                                ?>
                                <input name="reconnect" class="reconnect" type="hidden" value="1">
                                <?php
                            }
                            else {
                                ?>
                                <input name="reconnect" class="reconnect" type="hidden" value="0">
                                <?php
                            }
                            ?>

                        </form>
                        <div class="clear"></div>
                    </div>
                    <?php
                } ?>

            </div>


        </div>
    </div>

</div>

