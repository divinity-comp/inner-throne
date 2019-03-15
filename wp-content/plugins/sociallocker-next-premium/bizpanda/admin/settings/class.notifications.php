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
class OPanda_NotificationsSettings extends OPanda_Settings  {
 
    public $id = 'notifications';

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
            <?php _e('Mark events you wish to get notifications about.', 'bizpanda' )?>
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
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'notify_leads',
            'title'     => __( 'New Lead Received', 'bizpanda' ),
            'default'   => false,
            'hint'      => __( 'Set On to recived notifications via email about new leads.', 'bizpanda' )
        );
        
        $options[] = array(
            'type'      => 'div',
            'id'        => 'opanda_notify_leads-options',
            'items'     => array(
                
                array(
                    'type' => 'separator'
                ),
                array(
                    'type'      => 'textbox',
                    'name'      => 'leads_email_receiver',
                    'default'   => get_option('admin_email'),
                    'title'     => __('Recipient', 'bizpanda'),
                    'hint'      => __('An email address of the recipient to send notifications.', 'bizpanda')
                ),
                array(
                    'type'      => 'textbox',
                    'name'      => 'leads_email_subject',
                    'default'   => 'A new lead grabbed on {sitename}',
                    'title'     => __('Subject', 'bizpanda'),
                    'hint'      => __('A subject of the notification email. Supported tags: {sitename}.', 'bizpanda')
                ),
                array(
                    'type'      => 'wp-editor',
                    'name'      => 'leads_email_body',
                    'data'      => $wpEditorData,
                    'title'     => __('Message', 'bizpanda'),
                    'hint'      => __('A body of the notification email. Supported tags: {sitename}, {siteurl}, {details}, {context}.', 'bizpanda'),
                    'tinymce'   => array(
                        'height' => 250,
                        'content_css' => OPANDA_BIZPANDA_URL . '/assets/admin/css/tinymce.010000.css'
                    ),
                    'default'   => $defaultLeadsEmail
                ),
                array(
                    'type' => 'separator'
                )
            )
        );

        $options[] = array(
            'type'      => 'checkbox',
            'way'       => 'buttons',
            'name'      => 'notify_unlocks',
            'title'     => __( 'Unlock Occurred', 'bizpanda' ),
            'default'   => false,
            'hint'      => __( 'Set On to recived notifications via email about unlocks.', 'bizpanda' )
        );
        
        $options[] = array(
            'type'      => 'div',
            'id'        => 'opanda_notify_unlocks-options',
            'items'     => array(
                
                array(
                    'type' => 'separator'
                ),
                array(
                    'type'      => 'textbox',
                    'name'      => 'unlocks_email_receiver',
                    'default'   => get_option('admin_email'),
                    'title'     => __('Recipient', 'bizpanda'),
                    'hint'      => __('An email address of the recipient to send notifications.', 'bizpanda')
                ),
                array(
                    'type'      => 'textbox',
                    'name'      => 'unlocks_email_subject',
                    'default'   => 'A new unlock occurred on {sitename}',
                    'title'     => __('Subject', 'bizpanda'),
                    'hint'      => __('A subject of the notification email. Supported tags: {sitename}.', 'bizpanda')
                ),
                array(
                    'type'      => 'wp-editor',
                    'name'      => 'unlocks_email_body',
                    'data'      => $wpEditorData,
                    'title'     => __('Message', 'bizpanda'),
                    'hint'      => __('A body of the notification email. Supported tags: {sitename}, {siteurl}, {context}.', 'bizpanda'),
                    'tinymce'   => array(
                        'height' => 250,
                        'content_css' => OPANDA_BIZPANDA_URL . '/assets/admin/css/tinymce.010000.css'
                    ),
                    'default'   => $defaultUnlocksEmail
                )
            )
        );

        $options[] = array(
            'type' => 'separator'
        );

        return $options;
    }

    public function onSaving() {

    }
}

