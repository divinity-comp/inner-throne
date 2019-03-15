<?php

//Plugin activation 

// activation 
function avada_cpt_activation()
{
    set_transient('cptt-admin-notice-show', true, 100);
}


add_action('admin_notices', 'cptt_admin_notice_run');

function cptt_admin_notice_run()
{
    if (get_transient('cptt-admin-notice-show')) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>Add the new Fusion Builder elements in Fusion Builder->Settings ->Fusion Builder Elements (<strong>Blog CPT and Portfolio CPT</strong>).</p>
        </div>
        <?php
        delete_transient('cptt-admin-notice-show');
    }

}

//deactivation	 
function avada_cpt_deactivation()
{
}

register_activation_hook(plugin_dir_path(__FILE__) . 'fusion_cptt.php', 'avada_cpt_activation');
register_deactivation_hook(plugin_dir_path(__FILE__) . 'fusion_cptt.php', 'avada_cpt_deactivation');

?>