<?php
/**
 * A class for the page providing the basic settings.
 * 
 * @author Alex Kovalev <alex.kovalevv@gmail.com>
 * @copyright (c) 2016, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The page Basic Settings.
 * 
 * @since 1.0.0
 */
class OPanda_ZapierSettings extends OPanda_Settings  {
 
    public $id = 'zapier';

    public function __construct($page) {
        parent::__construct($page);
    }
    
    /**
     * Shows the header html of the settings screen.
     * 
     * @since 1.0.0
     * @return void
     */
    public function header() {
        global $optinpanda;
        ?>
        <p>
            <?php printf( __('Allows to set up integration with Zapier via <a href="%s" target="_blank">Webhooks</a>.', 'bizpanda'), 'https://zapier.com/apps/webhook/integrations' )?>
        </p>
        <?php
    }
    
    /**
     * Returns options for the Basic Settings screen. 
     * 
     * @since 1.0.0
     * @return void
     */
    public function getOptions() {

        $options = array();
        $wpEditorData = array();
        
        $defaultLeadsEmail = file_get_contents( OPANDA_BIZPANDA_DIR . '/content/leads-notification.html' );
        $defaultUnlocksEmail = file_get_contents( OPANDA_BIZPANDA_DIR . '/content/unlocks-notification.html' ); 
        
        $options[] = array(
            'type' => 'separator'
        );
        
        $options[] = array(
            'type'      => 'url',
            'name'      => 'zapier_hook_new_leads',
            'title'     => __( 'Hook For Leads', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=zapier') ),
            'default'   => "",
            'hint'      => sprintf( __( 'Fires when a lead gained. <a href="%s" target="_blank">Click here</a> to know how to get a webhook URL.', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=zapier') )
        );
        
        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'zapier_only_new',
            'title'     => __( 'Only New Leads', 'bizpanda' ),
            'default'   => false,
            'hint'      => __( 'If On, sends data to Zapier only if a lead is new (not listed on the page Leads).', 'bizpanda' )
        );
        
        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'zapier_only_confirmed',
            'title'     => __( 'Only Confirmed Leads', 'bizpanda' ),
            'default'   => false,
            'hint'      => __( 'If On, sends data to Zapier only for those leads who confirmed their subscription (or all leads if the Single Opt-In is set).', 'bizpanda' )
        );
        
        $options[] = array(
            'type' => 'separator'
        );
        
        $options[] = array(
            'type'      => 'url',
            'way'       => 'buttons',
            'name'      => 'zipier_hook_new_unlocks',
            'title'     => __( 'Hook For New Unlocks', 'bizpanda' ),
            'after'     => sprintf( __( '<a href="%s" class="btn btn-default">How To Get</a>', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=zapier') ),
            'default'   => "",
            'hint'      => sprintf( __( 'Fires when a new unlock occurs. <a href="%s" target="_blank">Click here</a> to know how to get a webhook URL.', 'bizpanda' ), admin_url('admin.php?page=how-to-use-' . $this->plugin->pluginName . '&onp_sl_page=zapier') )
        ); 

        $options[] = array(
            'type' => 'separator'
        );

        return $options;
    }

    public function onSaving() {

    }
}

