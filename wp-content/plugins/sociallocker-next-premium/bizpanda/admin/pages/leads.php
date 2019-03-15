<?php
/**
 * The file contains a short help info.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2014, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * Common Settings
 */
class OPanda_LeadsPage extends OPanda_AdminPage  {
 
    public function __construct( $plugin ) {   
        $this->menuPostType = OPANDA_POST_TYPE;
        $this->id = "leads";
        
        if( !current_user_can('administrator') )
            $this->capabilitiy = "manage_opanda_leads";
            
        require_once OPANDA_BIZPANDA_DIR . '/admin/includes/leads.php';
        
        $count = OPanda_Leads::getCount();
        if ( empty( $count ) ) $count = '0';
        
        $this->menuTitle = sprintf( __('Leads (%d)', 'bizpanda'), $count );
        
        parent::__construct( $plugin );
    }
  
    public function assets($scripts, $styles) {
        $this->styles->add(OPANDA_BIZPANDA_URL . '/assets/admin/css/leads.010008.css'); 
        $this->scripts->add(OPANDA_BIZPANDA_URL . '/assets/admin/js/leads.010008.js'); 
        
        $this->scripts->request('jquery');
        
        $this->scripts->request( array( 
            'control.checkbox',
            'control.dropdown'
            ), 'bootstrap' );

        $this->styles->request( array( 
            'bootstrap.core', 
            'bootstrap.form-group',
            'bootstrap.separator',
            'control.dropdown',
            'control.checkbox',
            ), 'bootstrap' );
    }
    
    public function indexAction() {

        if(!class_exists('WP_List_Table')){
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }
        
        require_once( OPANDA_BIZPANDA_DIR . '/admin/includes/classes/class.leads.table.php' );

        $table = new OPanda_LeadsListTable( array('screen' => 'bizpanda-leads') );
        $table->prepare_items();

        ?>
        <div class="wrap factory-fontawesome-320" id="opanda-leads-page">

            <h2>
                <?php _e('Leads', 'bizpanda') ?>
                <a href="<?php $this->actionUrl('export') ?>" class="add-new-h2"><?php _e( 'export', 'bizpanda' ); ?></a>
                <a href="<?php $this->actionUrl('clearAll') ?>" class="add-new-h2"><?php _e( 'clear all', 'bizpanda' ); ?></a> 
            </h2>

            <?php if ( BizPanda::isSinglePlugin() ) { ?>

                <?php if ( BizPanda::hasPlugin('optinpanda') ) { ?>
                    <p style="margin-top: 0px;"> <?php _e('This page shows contacts of visitors who opted-in or signed-in on your website through Email or Sign-In Lockers.', 'bizpanda'); ?></p>
                <?php } else { ?>
                    <p style="margin-top: 0px;"><?php printf( __('This page shows contacts of visitors who signed-in on your website through the <a href="%s">Sign-In Locker</a>.', 'bizpanda'), opanda_get_help_url('what-is-signin-locker') ); ?></p>
                <?php } ?>

            <?php } else { ?>
                <p style="margin-top: 0px;"> <?php _e('This page shows contacts of visitors who opted-in or signed-in on your website through Email or Sign-In Lockers.', 'bizpanda'); ?></p>
            <?php } ?>
            
            <?php if ( isset( $_GET['onp_table_cleared'] ) ) { ?>
            <div class="factory-bootstrap-330">          
                <div class="alert alert-success">
                    <?php _e('The data has been successfully cleared.', 'bizpanda') ?>
                </div>
            </div>
            <?php } ?>
            
            <?php
                $table->search_box(__('Search Leads', 'mymail'), 's');
                $table->views();
            ?>

            <form method="post" action="">
            <?php echo $table->display(); ?>
            </form>
        </div>
        <?php
        
        OPanda_Leads::updateCount();
    }
    
    public function leadDetailsAction() {
            $leadId = isset( $_REQUEST['leadID'] ) ? intval( $_REQUEST['leadID'] ) : 0;
            
            $lead = OPanda_Leads::get( $leadId );
            $customFields = OPanda_Leads::getCustomFields( $leadId );
            
            $email = $lead->lead_email;
            $name = $lead->lead_name;
            $family = $lead->lead_family;

            if ( !empty( $name) || !empty( $family) ) {
                $displayName = $name . ' ' . $family;
            } else {
                $displayName = !empty( $lead->lead_display_name )? $lead->lead_display_name : $lead->lead_email;
            }  
                
            $emailConfirmed = empty( $lead->lead_email_confirmed ) ? 0 : 1;
            $subscriptionConfirmed = empty( $lead->lead_subscription_confirmed ) ? 0 : 1;
            
            if ( isset( $_POST['submit'] ) ) {
                $data = array();
                
                $email = $_POST['email'];    
                
                if ( !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $email) ) {
                    
                    $error = __('Please enter a valid email.', 'bizpanda');
                    
                } else {

                    $name = $_POST['name'];
                    $family = $_POST['family'];

                    if ( !empty( $name) || !empty( $family) ) {
                        $displayName = $name . ' ' . $family;
                    } else {
                        $displayName = !empty( $lead->lead_display_name )? $lead->lead_display_name : $lead->lead_email;
                    } 

                    $data['email'] = $email;
                    $data['displayName'] = $displayName;
                    $data['name'] = $name;
                    $data['family'] = $family;

                    $emailConfirmed = empty( $_POST['email_confirmed'] ) ? 0 : 1;
                    $subscriptionConfirmed = empty( $_POST['subscription_confirmed'] ) ? 0 : 1;

                    $customValues = isset( $_POST['opanda_values'] ) ? $_POST['opanda_values'] : array();
                    $customNames = isset( $_POST['opanda_names'] ) ? $_POST['opanda_names'] : array();            

                    $index = 0;
                    foreach( $customNames as $customName ) {
                        $data['{' . $customName . '}'] = $customValues[$index];
                        $customFields[$customName] = $customValues[$index];
                        $index++;
                    }

                    OPanda_Leads::save($lead, $data, array(), $emailConfirmed, $subscriptionConfirmed);

                    $url = admin_url('/edit.php?post_type=opanda-item&page=leads-bizpanda&action=leadDetails&leadID=' . $lead->ID . '&opanda_success=1');
                    wp_redirect($url);
                    exit;
                }
            }

            $avatar = OPanda_Leads::getAvatar( $leadId, $lead->lead_email, 150 );
            $postUrl = admin_url('/edit.php?post_type=opanda-item&page=leads-bizpanda&action=leadDetails&leadID=' . $lead->ID);
            $cancelUrl = admin_url('/edit.php?post_type=opanda-item&page=leads-bizpanda');
        ?>

        <div class="wrap factory-fontawesome-320" id="opanda-lead-details-page">
            
        <h2>Edit <strong><?php echo htmlspecialchars( $displayName ) ?></strong></h2>
        
        <?php if ( isset( $_GET['opanda_success'] ) ) { ?>
        <div class="factory-bootstrap-330">
            <div class="alert alert-success"><?php _e('<strong>Well done!</strong> The lead data updated successfully.', 'bizpanda') ?></div>
        </div>
        <?php } ?>
        
        <?php if ( !empty( $error ) ) { ?>
        <div class="factory-bootstrap-330">
            <div class="alert alert-danger"><?php echo $error; ?></div>
        </div>
        <?php } ?>
        
        <form method="POST" action="<?php echo $postUrl ?>">
            <input type="hidden" name="leadID" value="<?php echo $leadId ?>" />
            <input type="hidden" name="submit" value="1" />
            
        <table class="form-table">
            <tr>
                <td scope="row" class="avatar-wrap">
                    <div class="opanda-avatar"><?php echo $avatar ?></div>
                </td>
                
                <td class="user-info">
                    
                    <h3 class="detail">
                        <ul class="click-to-edit">
                            <li><?php echo htmlspecialchars( $email ) ?></li>
                            <li><input id="opanda_email" class="" type="text" name="email" value="<?php echo htmlspecialchars( $email ) ?>" placeholder="<?php _e('Email', 'bizpanda') ?>"></li>
                        </ul>
                    </h3>
                    
                    <div class="detail">
                        <label for="opanda_name"><?php _e('Name:', 'bizpanda') ?></label>
                        <ul class="click-to-edit">
                            <li><?php echo htmlspecialchars( $name ) ?> <?php echo htmlspecialchars( $family ) ?></li>
                            <li>
                                <input id="opanda_name" type="text" name="name" value="<?php echo htmlspecialchars( $name ) ?>" placeholder="<?php _e('First Name', 'bizpanda') ?>">
                                <input id="opanda_family" class="" type="text" name="family" value="<?php echo htmlspecialchars( $family ) ?>" placeholder="<?php _e('Last Name', 'bizpanda') ?>">
                            </li>
                        </ul>
                    </div>
                    
                    <div class="detail">
                        <label for="opanda_email_confirmed"><?php _e('Email Confirmed:', 'bizpanda') ?></label>
                        <ul class="click-to-edit">
                            <li><?php if ( $emailConfirmed ) { _e('yes', 'bizpanda'); } else { _e('no', 'bizpanda'); } ?></li>
                            <li>
                                <input type="checkbox" id="opanda_email_confirmed" name="email_confirmed" value="1" <?php if ( $emailConfirmed ) { echo 'checked="checked"'; } ?> >            
                            </li>
                        </ul>
                    </div>
                    
                    <?php if ( BizPanda::hasPlugin('optinpanda') ) { ?>
                    
                    <div class="detail">
                        <label for="opanda_subscription_confirmed"><?php _e('Subscription Confirmed:', 'bizpanda') ?></label>
                        <ul class="click-to-edit">
                            <li><?php if ( $subscriptionConfirmed ) { _e('yes', 'bizpanda'); } else { _e('no', 'bizpanda'); } ?></li>
                            <li>
                                <input type="checkbox" id="opanda_email_confirmed" name="subscription_confirmed" value="1" <?php if ( $subscriptionConfirmed ) { echo 'checked="checked"'; } ?> >                
                            </li>
                        </ul>
                    </div>
                    
                    <?php } ?>
                    
                    <?php if ( !empty( $customFields ) ) { ?>
                    
                    <div class="custom-field-wrap">
                        
                    <?php 
                        $index = 0;
                        foreach ( $customFields as $fieldName => $fieldValue ) {
                        $index++;
                    ?>
                    
                        <div class="detail">
                            <label for="opanda_<?php echo $index ?>"><?php echo $fieldName ?>:</label>

                            <ul class="click-to-edit">
                                <li><?php echo $fieldValue ?></li>
                                <li><input type="text" id="opanda_<?php echo $index ?>" name="opanda_values[]" value="<?php echo $fieldValue ?>" class="regular-text input"></li>
                                <input type="hidden" name="opanda_names[]" value="<?php echo $fieldName ?>" />
                            </ul>
                        </div>
                    
                    <?php } ?>
                        
                    </div>
                    
                    <?php } ?>

                    <div class="controls-wrap">
                        <input type="submit" class="button button-primary" value="<?php _e('Save Changes', 'bizpanda') ?>" />
                        <a href="<?php echo $cancelUrl ?>" class="button button-default"><?php _e('Return', 'bizpanda') ?></a> 
                    </div>
                </td>

            </tr>
        </table>
            
        </form>
        
        </div>
        
        <?php
    }
    
    public function exportAction() {
        global $bizpanda;
            
            $error = null;
            $warning = null;

            // getting a list of lockers

            $lockerIds = array();

            global $wpdb;
            $data = $wpdb->get_results(
                "SELECT l.lead_item_id AS locker_id, COUNT(l.ID) AS count, p.post_title AS locker_title "
               . "FROM {$wpdb->prefix}opanda_leads AS l "
               . "LEFT JOIN {$wpdb->prefix}posts AS p ON p.ID = l.lead_item_id "
               . "GROUP BY l.lead_item_id", ARRAY_A );

            $lockerList = array(
                array('all', __('Mark All', 'bizpanda') )
            );

            foreach( $data as $items ) {
                $lockerList[] = array( $items['locker_id'], $items['locker_title'] . ' (' . $items['count'] . ')');
                $lockerIds[] = $items['locker_id'];
            } 

            // default values

            $status = 'all';
            $fields = array('lead_email', 'lead_name', 'lead_family');
            $delimiter = ',';
            
            // custom fields
            
            $customFields = OPanda_Leads::getCustomFields();
            $selectedCustomFields = array();
            
            $customFieldsForList = array();
            foreach( $customFields as $customField ) {
                $customFieldsForList[] = array( $customField->field_name, $customField->field_name );
            }
            
            // exporting 

            if ( isset( $_POST['opanda_export'] ) ) {

                // - delimiter

                $delimiter = isset( $_POST['opanda_delimiter'] ) ? $_POST['opanda_delimiter'] : ',';
                if ( !in_array( $status, array(',', ';') ) ) $status = ',';

                // - channels

                $lockers = isset( $_POST['opanda_lockers'] ) ? $_POST['opanda_lockers'] : array();
                $lockerIds = array();
                foreach( $lockers as $lockerId ) {
                    if ( 'all' == $lockerId ) continue;
                    $lockerIds[] = intval( $lockerId );
                }

                // - status

                $status = isset( $_POST['opanda_status'] ) ? $_POST['opanda_status'] : 'all';
                if ( !in_array( $status, array('all', 'confirmed', 'not-confirmed') ) ) $status = 'all';

                // - fields

                $rawFields = isset( $_POST['opanda_fields'] ) ? $_POST['opanda_fields'] : array();
                $fields = array();

                foreach( $rawFields as $field ) {
                    if ( !in_array( $field, array('lead_email', 'lead_display_name', 'lead_name', 'lead_family', 'lead_ip') ) ) continue;
                    $fields[] = $field;
                } 
                
                // - custom fields 
                
                $rawCustomFields = isset( $_POST['opanda_custom_fields'] ) ? $_POST['opanda_custom_fields'] : array();
                $selectedCustomFields = array();  

                foreach( $rawCustomFields as $customField ) {
                    $selectedCustomFields[] = $customField;
                } 

                if ( in_array('custom_facebook_id', $rawFields) ) {
                    $selectedCustomFields[] = 'facebookId';
                }

                if ( empty( $lockers) || ( empty( $fields ) && empty( $selectedCustomFields ) ) ) {
                    $error = __('Please make sure that you selected at least one channel and field.', 'bizpanda');
                } else {
                    
                    $sql = 'SELECT leads.ID,';
                    
                    $fields[] = 'ID';
                    
                    $sqlFields = array();
                    foreach( $fields as $field ) $sqlFields[] = 'leads.' . $field;
                    $sql .= implode(',', $sqlFields);
                    
                    if ( !empty( $selectedCustomFields ) ) {
                        $sql .= ',fields.field_name,fields.field_value';
                    }
                    
                    $sql .= ' FROM ' . $wpdb->prefix . 'opanda_leads AS leads ';
                    
                    if ( !empty( $selectedCustomFields ) ) {
                        $sql .= 'LEFT JOIN ' . $wpdb->prefix . 'opanda_leads_fields AS fields ON fields.lead_id = leads.ID ';
                    }
                    
                    $sql .= 'WHERE leads.lead_item_id IN (' . implode(',', $lockerIds) . ')';

                    if ( 'all' != $status ) {
                        $sql .= ' AND leads.lead_email_confirmed = '. ( ( 'confirmed' == $status ) ? '1' : '0' );
                    }

                    $result = $wpdb->get_results( $sql, ARRAY_A );
 
                    $leads = array();
                    foreach( $result as $item ) {
                        $id = $item['ID'];
                        
                        if ( !isset( $leads[$id] ) ) {
                            $leads[$id] = array();
                            foreach( $fields as $field ) {
                                if ( $field === 'ID' ) continue;
                                $leads[$id][$field] = $item[$field];
                            }
                            foreach( $selectedCustomFields as $field ) $leads[$id][$field] = null;
                        }
                        
                        if ( !empty( $item['field_name'] ) && in_array($item['field_name'], $selectedCustomFields) ) {
                            $leads[$id][$item['field_name']] = $item['field_value'];
                        }                        
                    } 
                    
                    if ( empty( $leads ) ) {
                        $warning = __('No leads found. Please try to change the settings of exporting.', 'bizpanda');
                    } else {

                        $filename = 'leads-' . date('Y-m-d-H-i-s') . '.csv';

                        header("Content-Type: text/csv");
                        header("Content-Disposition: attachment; filename=" . $filename);
                        header("Cache-Control: no-cache, no-store, must-revalidate");
                        header("Pragma: no-cache");
                        header("Expires: 0");

                        $output = fopen("php://output", "w");
                        foreach( $leads as $row ) {
                            
                            fputcsv($output, $row, $delimiter);
                        }
                        fclose($output);

                        exit;
                    }
                }
            }

            // creating a form

            $form = new FactoryForms328_Form(array(
                'scope' => 'opanda',
                'name'  => 'exporting'
            ), $bizpanda );

            $form->setProvider( new FactoryForms328_OptionsValueProvider(array(
                'scope' => 'opanda'
            )));

            $options = array(

                array(
                    'type' => 'separator'
                ),    
                array(
                    'type' => 'radio',
                    'name' => 'format',
                    'title' => __('Format', 'bizpanda'),
                    'hint' => __('Only the CSV format is available currently.'),
                    'data' => array(
                        array('csv', __('CSV File', 'bizpanda') )              
                    ),
                    'default' => 'csv'
                ),
                array(
                    'type' => 'radio',
                    'name' => 'delimiter',
                    'title' => __('Delimiter', 'bizpanda'),
                    'hint' => __('Choose a delimiter for a CSV document.'),
                    'data' => array(
                        array(',', __('Comma', 'bizpanda') ),
                        array(';', __('Semicolon', 'bizpanda') )
                    ),
                    'default' => $delimiter
                ),
                array(
                    'type' => 'separator'
                ),  
                array(
                    'type' => 'list',
                    'way' => 'checklist',
                    'name' => 'lockers',
                    'title' => __('Channels', 'bizpanda'),
                    'hint' => __('Mark lockers which attracted leads you wish to export.'),
                    'data' => $lockerList,
                    'default' => implode(',', $lockerIds)
                ),   
                array(
                    'type' => 'radio',
                    'name' => 'status',
                    'title' => __('Email Status', 'bizpanda'),
                    'hint' => __('Choose the email status of leads to export.'),
                    'data' => array(
                        array('all', __('All', 'bizpanda') ),
                        array('confirmed', __('Only Confirmed Emails', 'bizpanda') ),
                        array('not-confirmed', __('Only Not Confirmed', 'bizpanda') )
                    ),
                    'default' => $status
                ),
                array(
                    'type' => 'separator'
                ),   
                array(
                    'type' => 'list',
                    'way' => 'checklist',
                    'name' => 'fields',
                    'title' => __('Fields To Export', 'bizpanda'),
                    'data' => array(
                        array('lead_email', __('Email', 'bizpanda') ),
                        array('lead_display_name', __('Display Name', 'bizpanda') ),
                        array('lead_name', __('Firstname', 'bizpanda') ),
                        array('lead_family', __('Lastname', 'bizpanda') ),
                        array('lead_ip', __('IP', 'bizpanda') ),
                        array('custom_facebook_id', __('Facebook App Scoped Id', 'bizpanda') )                
                    ),
                    'default' => implode(',', $fields)
                )
            );
            
            if ( !empty( $customFieldsForList ) ) {
            
                $options[] = array(
                    'type' => 'list',
                    'way' => 'checklist',
                    'name' => 'custom_fields',
                    'title' => __('Custom Fields', 'bizpanda'),
                    'data' => $customFieldsForList,
                    'default' => implode(',', $selectedCustomFields)
                ); 
            }
  
            
            $options[] = array(
                'type' => 'separator'
            );

            $form->add($options);
            ?>
            <div class="wrap" id="opanda-export-page">

                <h2>
                    <?php _e('Exporting Leads', 'bizpanda') ?>
                </h2>

                <p style="margin-top: 0px;"> <?php _e('Select leads you would like to export and click the button "Export Leads".', 'bizpanda'); ?></p>

                <div class="factory-bootstrap-330 factory-fontawesome-320">

                    <?php if ( $error ) { ?>
                    <div class="alert alert-danger"><?php echo $error ?></div>
                    <?php } ?>

                    <?php if ( $warning ) { ?>
                    <div class="alert alert-normal"><?php echo $warning ?></div>
                    <?php } ?> 

                    <form method="post" class="form-horizontal">
                        <?php $form->html(); ?>

                        <div class="form-group form-horizontal">
                            <label class="col-sm-2 control-label"> </label>
                            <div class="control-group controls col-sm-10">
                                <input name="opanda_export" class="btn btn-primary" type="submit" value="<?php _e('Export Leads', 'bizpanda') ?>"/>
                            </div>
                        </div>
                    </form> 
                </div>
            </div>
            <?php
            
        

    }
    
    /**
     * Clears the statisticals data.
     * 
     * @sinve 1.0.0
     * @return void
     */
    public function clearAllAction() {
        
        if ( !isset( $_REQUEST['onp_confirmed'] ) ) {
            return $this->confirm(array(
                'title' => __('Are you sure that you desire to clear all your leads data?', 'bizpanda'),
                'description' => __('All your leads data will be removed.', 'bizpanda'),
                'actions' => array(
                    'onp_confirm' => array(
                        'class' => 'btn btn-danger',
                        'title' => __("Yes, I'm sure", 'bizpanda'),
                        'url' => $this->getActionUrl('clearAll', array(
                            'onp_confirmed' => true
                        ))
                    ),
                    'onp_cancel' => array(
                        'class' => 'btn btn-default',
                        'title' => __("No, return back", 'bizpanda'),
                        'url' => $this->getActionUrl('index')
                    ),
                )
            ));
        }
        
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->prefix}opanda_leads");
        $wpdb->query("DELETE FROM {$wpdb->prefix}opanda_leads_fields");

        return $this->redirectToAction('index', array('onp_table_cleared' => true));
    }
    
    /**
     * Shows the html block with a confirmation dialog.
     * 
     * @sinve 1.0.0
     * @return void
     */
    public function confirm( $data ) {
        ?>
        <div class="onp-page-wrap factory-bootstrap-330" id="onp-confirm-dialog">
            <div id="onp-confirm-dialog-wrap">
                <h1><?php echo $data['title'] ?></h1>
                <p><?php echo $data['description'] ?></p>
                <div class='onp-actions'>
                    <?php foreach( $data['actions'] as $action ) { ?>
                        <a href='<?php echo $action['url'] ?>' class='<?php echo $action['class'] ?>'>
                           <?php echo $action['title'] ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }
}

FactoryPages321::register($bizpanda, 'OPanda_LeadsPage');
