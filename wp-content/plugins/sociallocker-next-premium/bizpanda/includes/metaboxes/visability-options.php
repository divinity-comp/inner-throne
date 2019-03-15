<?php
/**
 * The file contains a class to configure the metabox Visibility Options.
 * 
 * Created via the Factory Metaboxes.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2013, OnePress Ltd
 * 
 * @package core 
 * @since 2.3.0
 */

/**
 * The class to configure the metabox Visibility Options.
 * 
 * @since 2.3.0
 */
class OPanda_VisabilityOptionsMetaBox extends FactoryMetaboxes321_FormMetabox
{
    /**
     * A visible title of the metabox.
     * 
     * Inherited from the class FactoryMetabox.
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * 
     * @since 2.3.0
     * @var string
     */
    public $title;
    
    /**
     * A prefix that will be used for names of input fields in the form.
     * 
     * Inherited from the class FactoryFormMetabox.
     * 
     * @since 2.3.0
     * @var string
     */
    public $scope = 'opanda';
    
    /**
     * The priority within the context where the boxes should show ('high', 'core', 'default' or 'low').
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Inherited from the class FactoryMetabox.
     * 
     * @since 2.3.0
     * @var string
     */
    public $priority = 'core';
    
    /**
     * The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side'). 
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Inherited from the class FactoryMetabox.
     * 
     * @since 2.3.0
     * @var string
     */
    public $context = 'side';
    
    public function __construct( $plugin ) {
        parent::__construct( $plugin );
        $this->title = __('Visibility Options', 'bizpanda');
    }
    
    public $cssClass = 'factory-bootstrap-330 factory-fontawesome-320';
    
    public function configure( $scripts, $styles ){
        $scripts->add( OPANDA_BIZPANDA_URL . '/assets/admin/js/widgets/condition-editor.010000.js');
        $scripts->add( OPANDA_BIZPANDA_URL . '/assets/admin/js/metaboxes/visability.010001.js');
    }
    
    /**
     * Configures a form that will be inside the metabox.
     * 
     * @see FactoryMetaboxes321_FormMetabox
     * @since 1.0.0
     * 
     * @param FactoryForms328_Form $form A form object to configure.
     * @return void
     */ 
    public function form( $form ) {

        $options = array(  
            array(
                'type'      => 'html',
                'html'      => array($this, 'htmlSwitcher')
            ),
            array(
                'type'      => 'hidden',
                'name'      => 'visibility_mode',
                'default'   => 'simple'
            ),
            array(
                'type'      => 'div',
                'id'        => 'bp-simple-visibility-options',
                'items'     => array(
                    array(
                        'type'      => 'textbox',
                        'name'      => 'delay',
                        'title'     => __('Lock Delay', 'bizpanda'),
                        'hint'      => __('Displays the locker after the specified interval (in seconds).', 'bizpanda'),
                        'icon'      => OPANDA_BIZPANDA_URL . '/assets/admin/img/timer-icon.png',
                        'default'   => false
                    ),     
                    array(
                        'type'      => 'checkbox',
                        'way'       => 'buttons',
                        'name'      => 'hide_for_member',
                        'title'     => __('Hide For Members', 'bizpanda'),
                        'hint'      => __('If on, hides the locker for registered members.', 'bizpanda'),
                        'icon'      => OPANDA_BIZPANDA_URL . '/assets/admin/img/member-icon.png',
                        'default'   => false
                    ),
                    array(
                        'type'      => 'checkbox',
                        'way'       => 'buttons',
                        'name'      => 'relock',
                        'title'     => __('ReLock', 'bizpanda'),
                        'hint'      => __('If on, being unlocked the locker will appear again after a specified interval.', 'bizpanda'),
                        'icon'      => OPANDA_BIZPANDA_URL . '/assets/admin/img/icon-relock-3.png',                
                        'default'   => false
                    ),
                    array(
                        'type'      => 'html',
                        'html'      => array($this, 'htmlReLockOptions')
                    ),
                    array(
                        'type'      => 'checkbox',
                        'way'       => 'buttons',
                        'name'      => 'always',
                        'title'     => '<i class="fa fa-umbrella" style="font-size: 17px; margin-right: 8px;"></i>' . __('Show Always', 'bizpanda'),
                        'hint'      => __('If on, the locker appears always even it was unlocked.', 'bizpanda'),         
                        'default'   => false
                    ), 
                    array(
                        'type'      => 'checkbox',
                        'way'       => 'buttons',
                        'name'      => 'mobile',
                        'title'     => __('Mobile', 'bizpanda'),
                        'hint'      => __('If on, the locker will appear on mobile devices.', 'bizpanda'),
                        'icon'      => OPANDA_BIZPANDA_URL . '/assets/admin/img/mobile-icon.png',
                        'default'   => true
                    )
                )
            ),
            array(
                'type'      => 'div',
                'id'        => 'bp-advanced-visibility-options',
                'items'     => array(
                    array(
                        'type'      => 'html',
                        'html'      => array( $this, 'visibilityFilters' )
                    ),
                    array(
                        'type'      => 'hidden',
                        'name'      => 'visibility_filters'
                    )
                )
            )      
        ); 
        
        if ( OPanda_Items::isCurrentFree() ) {
            
            $options[] = array(
                'type'      => 'html',
                'html'      => '<div style="display: none;" class="factory-fontawesome-320 opanda-overlay-note opanda-premium-note">' . __( '<i class="fa fa-star-o"></i> Go Premium <i class="fa fa-star-o"></i><br />To Unlock These Features <a href="#" class="opnada-button">Learn More</a>', 'bizpanda' ) . '</div>'
            );
        }

        $options = apply_filters( 'opanda_visability_options', $options, $this );
        $form->add( $options );
        
        
    }

    public function htmlReLockOptions() {
        $relock = $this->provider->getValue('relock', false);
        
        $interval = $this->provider->getValue('relock_interval', 0);
        if ( $interval == 0 ) $interval = '';
        
        $units = $this->provider->getValue('relock_interval_units', 'days');
        
        ?>
        <div class='onp-sl-sub <?php if ( !$relock ) { echo 'hide'; } ?>' id='onp-sl-relock-options'>
            <label class='control-label'><?php _e('The locker will reappear after:', 'bizpanda') ?></label>
            <input type='text' class='form-control input' name='<?php echo $this->scope ?>_relock_interval' value='<?php echo $interval ?>' />
            <select class='form-control' name='<?php echo $this->scope ?>_relock_interval_units'>
                <option value='days' <?php selected('days', $units) ?>><?php _e('day(s)', 'bizpanda') ?></option>    
                <option value='hours' <?php selected('hours', $units) ?>><?php _e('hour(s)', 'bizpanda') ?></option>
                <option value='minutes' <?php selected('minutes', $units) ?>><?php _e('minute(s)', 'bizpanda') ?></option>   
            </select>
            <p style="margin: 6px 0 0 0; font-size: 12px;"><?php _e('Any changes will apply only for new users.', 'bizpanda') ?></p>
        </div>
        <?php
    }
    
    /**
     * Shows html for the options' switcher.
     */
    public function htmlSwitcher() {
        ?>
        <div class="bp-options-switcher">
            <span class="bp-label"><?php _e('Options Mode:', 'bizpanda') ?></span>
            <div class="btn-group bp-swither-ctrl">
                <a href="#" class="btn btn-sm btn-default btn-btn-simple" data-value="simple"><?php _e('Simple', 'bizpanda') ?></a>
                <a href="#" class="btn btn-sm btn-default btn-btn-advanced" data-value="advanced"><?php _e('Advanced', 'bizpanda') ?></a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Shows a popup to display advanded options. 
     */
    public function visibilityFilters() {
        
        // filter parameters
        
        $groupedFilterParams = array(
            
            array(
                'id'    => 'user',
                'title' => __('User', 'bizpanda'),
                'items' => array(
                    array(
                        'id' => 'user-role',
                        'title' =>__('Role', 'bizpanda'),
                        'type' => 'select',
                        'values' => array(
                            'type' => 'ajax',
                            'action' => 'bp_ajax_get_user_roles'
                        ),
                        'description' => __('A role of the user who views your website. The role "guest" is applied to unregistered users.', 'bizpanda') 
                    ),
                    array(
                        'id' => 'user-registered',
                        'title' =>__('Registration Date', 'bizpanda'),
                        'type' => 'date',
                        'description' => __('The date when the user who views your website was registered. For unregistered users this date always equals to 1 Jan 1970.', 'bizpanda')
                    ),
                    array(
                        'id' => 'user-mobile',
                        'title' =>__('Mobile Device', 'bizpanda'),
                        'type' => 'select',
                        'values' => array(
                            array('value' => 'yes', 'title' => __('Yes', 'bizpanda') ),
                            array('value' => 'no', 'title' => __('No', 'bizpanda') )                        
                        ),
                        'description' => __('Determines whether the user views your website from mobile device or not.', 'bizpanda')
                    ),
                    array(
                        'id' => 'user-cookie-name',
                        'title' =>__('Cookie Name', 'bizpanda'),
                        'type' => 'text',
                        'onlyEquals' => true,
                        'description' => __('Determines whether the user\'s browser has a cookie with a given name.', 'bizpanda')
                    )
                )
            ),
            array(
                'id'    => 'session',
                'title' => __('Session', 'bizpanda'),
                'items' => array(
                    array(
                        'id' => 'session-pageviews',
                        'title' =>__('Total Pageviews', 'bizpanda'),
                        'type' => 'integer',
                        'description' => sprintf( __('The total count of pageviews made by the user within one\'s current session on your website. You can specify a duration of the sessions <a href="%s" target="_blank">here</a>.', 'bizpanda'), opanda_get_admin_url('settings', array('opanda_screen' => 'lock') ) )
                    ),
                    array(
                        'id' => 'session-locker-pageviews',
                        'title' =>__('Locker Pageviews', 'bizpanda'),
                        'type' => 'integer',
                        'description' => sprintf( __('The count of views of pages where lockers located, made by the user within one\'s current session on your website. You can specify a duration of the sessions <a href="%s" target="_blank">here</a>.', 'bizpanda'), opanda_get_admin_url('settings', array('opanda_screen' => 'lock') ) )
                    ),
                    array(
                        'id' => 'session-landing-page',
                        'title' =>__('Landing Page', 'bizpanda'),
                        'type' => 'text',
                        'description' => sprintf( __('A page of your website from which the user starts one\'s current session. You can specify a duration of the sessions <a href="%s" target="_blank">here</a>.', 'bizpanda'), opanda_get_admin_url('settings', array('opanda_screen' => 'lock') ) )
                    ),
                    array(
                        'id' => 'session-referrer',
                        'title' =>__('Referrer', 'bizpanda'),
                        'type' => 'text',
                        'description' => sprintf( __('A referrer URL which has brought the user to your website within the user\'s current session. You can specify a duration of the sessions <a href="%s" target="_blank">here</a>.', 'bizpanda'), opanda_get_admin_url('settings', array('opanda_screen' => 'lock') ) )
                    )
                )
            ),
            array(
                'id'    => 'location',
                'title' => __('Location', 'bizpanda'),
                'items' => array(
                    array(
                        'id' => 'location-page',
                        'title' =>__('Current Page', 'bizpanda'),
                        'type' => 'text',
                        'description' => __('An URL of the current page where a user who views your website is located.', 'bizpanda')
                    ),
                    array(
                        'id' => 'location-referrer',
                        'title' =>__('Current Referrer', 'bizpanda'),
                        'type' => 'text',
                        'description' => __('A referrer URL which has brought a user to the current page.', 'bizpanda')
                    )
                )
            ),  
            array(
                'id'    => 'post',
                'title' => __('Post', 'bizpanda'),
                'items' => array(
                    array(
                        'id' => 'post-published',
                        'title' =>__('Publication Date', 'bizpanda'),
                        'type' => 'date',
                        'description' => __('The publication date of a post where a user who views your website is located currently.', 'bizpanda')
                    )
                )
            ),
        );
        
        $groupedFilterParams = apply_filters('bp_visibility_filter_params', $groupedFilterParams);
        
        $filterParams = array();
        foreach( $groupedFilterParams as $filterGroup ) {
           $filterParams = array_merge( $filterParams, $filterGroup['items'] );
        }
        
        // templates
        
        $templates = array(
            array(
                'id' => 'hide_for_members',
                'title' => __('[Hide For Members]: Show the locker only for guests', 'bizpanda'),
                'filter' => array(
                    'type' => 'showif',
                    'conditions' => array(
                        array(
                            'type' => 'condition',
                            'param' => 'user-role',
                            'operator' => 'equals',
                            'value' => 'guest'
                        )
                    )
                )
            ),
            array(
                'id' => 'mobile',
                'title' => __('[Hide On Mobile]: Hide the locker on mobile devices', 'bizpanda'),
                'filter' => array(
                    'type' => 'hideif',
                    'conditions' => array(
                        array(
                            'type' => 'condition',
                            'param' => 'user-mobile',
                            'operator' => 'equals',
                            'value' => 'yes'     
                        )
                    )
                )
            ),
            array(
                'id' => 'delayed_lock',
                'title' => __('[Delayed Lock]: Show the locker only in posts older than 5 days', 'bizpanda'),
                'filter' => array(
                    'type' => 'showif',
                    'conditions' => array(
                        array(
                            'type' => 'condition',
                            'param' => 'post-published',
                            'operator' => 'older',
                            'value' => array(
                                'type' => 'relative',
                                'unitsCount' => 5,
                                'units' => 'days'
                            )   
                        )
                    )
                )
            )
        );
        
        $templates = apply_filters('bp_visibility_templates', $templates);
        
        ?>

        <div class="bp-advanded-options">
            
            <div class="bp-button-wrap">
                <a class="btn btn-default" href="#bp-advanced-visability-options" role="button" data-toggle="factory-modal">
                    <i class="fa fa-cog"></i> <?php _e('Setup Visibility Conditions', 'bizpanda') ?>
                </a>
            </div>

            <div class="modal fade bp-model" id="bp-advanced-visability-options" tabindex="-1" role="dialog" aria-labelledby="advancedVisabilityOptions" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel"><?php _e('Visibility Conditions', 'bizpanda') ?></h4>
                            <p style="margin-bottom: 0px;"><?php _e('Filters are applied consistently. Use templates to set up quickly the most popular conditions.') ?></p>
                        </div>
                        
                        <div class="modal-body">
                            
                            <script>
                                window.bp = window.bp || {};
                                window.bp.filtersParams = <?php echo json_encode( $filterParams ) ?>;
                                window.bp.templates = <?php echo json_encode( $templates ) ?>;                              
                            </script>
                            
                            <div class="bp-editor-wrap">
                                <div class="bp-when-empty">
                                     <?php _e('No filters specified. <a href="#" class="bp-add-filter">Click here</a> to add one.', 'bizpanda') ?>
                                </div>
                                <div class="bp-filters"></div>
                            </div>
     
                            <div class="bp-filter bp-template">
                                <div class="bp-point"></div>
                                <div class="bp-head">
                                    <div class="bp-left">
                                        <span style="margin-left: 0px;"><strong>Type:</strong></span>
                                        <select class="bp-filter-type">
                                            <option value="showif"><?php _e('Show Locker IF', 'bizpanda') ?></option>
                                            <option value="hideif"><?php _e('Hide Locker IF', 'bizpanda') ?></option>
                                        </select>
                                        <span>Or</span>
                                        <a href="#" class="button btn-remove-filter"><?php _e('Remove', 'bizpanda') ?></a>
                                    </div>
                                    <div class="bp-templates bp-right">
                                        <span><strong><?php _e('Template', 'bizpanda') ?></strong></span>
                                        <select class="bp-select-template">
                                            <option><?php _e('- select a template -', 'bizpanda') ?></option>
                                            <?php foreach ( $templates as $template ) { ?>
                                            <option value="<?php echo $template['id'] ?>"><?php echo $template['title'] ?></option>
                                            <?php } ?>                                 
                                        </select>
                                        <a href="#" class="button bp-btn-apply-template"><?php _e('Apply', 'bizpanda') ?></a>
                                    </div>
                                </div>
                                <div class="bp-box">
                                    <div class="bp-when-empty">
                                         <?php _e('No conditions specified. <a href="#" class="bp-link-add">Click here</a> to add one.', 'bizpanda') ?>
                                    </div>
                                    <div class="bp-conditions"></div>
                                </div>
                            </div>
                            
                            <div class="bp-scope bp-template">
                                <div class="bp-and"><span><?php _e('and', 'bizpanda') ?></span></div>
                            </div>

                            <div class="bp-condition bp-template">
                                <div class="bp-or"><?php _e('or', 'bizpanda') ?></div>
                                <span class="bp-params">
                                    <select class="bp-param-select">
                                    <?php foreach( $groupedFilterParams as $filterParam ) { ?>
                                        <optgroup label="<?php echo $filterParam['title'] ?>">
                                        <?php foreach( $filterParam['items'] as $param ) { ?>
                                            <option value="<?php echo $param['id'] ?>">
                                                <?php echo $param['title'] ?>
                                            </option>
                                        <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                    </select>
                                    <i class="bp-hint">
                                        <span class="bp-hint-icon"></span>
                                        <span class="bp-hint-content"></span>
                                    </i>
                                </span>
                                
                                <span class="bp-operators">
                                    <select class="bp-operator-select">           
                                        <option value="equals"><?php _e('Equals', 'bizpanda') ?></option>
                                        <option value="notequal"><?php _e('Doesn\'t Equal', 'bizpanda') ?></option>                                        
                                        <option value="greater"><?php _e('Greater Than', 'bizpanda') ?></option>
                                        <option value="less"><?php _e('Less Than', 'bizpanda') ?></option>
                                        <option value="older"><?php _e('Older Than', 'bizpanda') ?></option>
                                        <option value="younger"><?php _e('Younger Than', 'bizpanda') ?></option>                     
                                        <option value="contains"><?php _e('Contains', 'bizpanda') ?></option>
                                        <option value="notcontain"><?php _e('Doesn\'t Сontain', 'bizpanda') ?></option>                                  
                                        <option value="between"><?php _e('Between', 'bizpanda') ?></option>
                                    </select>
                                </span>
 
                                <span class="bp-value"></span>
                                
                                <span class="bp-controls">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-sm btn-default bp-btn-remove">-</a>
                                        <a href="#" class="btn btn-sm btn-default bp-btn-or"><?php _e('OR', 'bizpanda') ?></a>
                                        <a href="#" class="btn btn-sm btn-default bp-btn-and"><?php _e('AND', 'bizpanda') ?></a>
                                    </div>
                                </span>
                            </div>
                            
                            <div class="bp-date-control bp-relative bp-template">
                                <div class="bp-inputs">
                                    
                                    <div class="bp-between-date">
                                        
                                        <div class="bp-absolute-date">
                                            <span class="bp-label"> <?php _e('from', 'bizpanda') ?> </span>
                                            <div class="bp-date-control bp-date-start" data-date="today">
                                                <input size="16" type="text" readonly="readonly" class="bp-date-value-start" data-date="today" />
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <span class="bp-label"> <?php _e('to', 'bizpanda') ?> </span>
                                            <div class="bp-date-control bp-date-end" data-date="today">
                                                <input size="16" type="text" readonly="readonly" class="bp-date-value-end" data-date="today" />
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="bp-relative-date">
                                            <span class="bp-label"> <?php _e('older than', 'bizpanda') ?> </span>
                                            <input type="text" class="bp-date-value bp-date-value-start" value="1" />
                                            <select class="bp-date-start-units">
                                                <option value="seconds"><?php _e('Second(s)', 'bizpanda') ?></option>
                                                <option value="minutes"><?php _e('Minutes(s)', 'bizpanda') ?></option>
                                                <option value="hours"><?php _e('Hours(s)', 'bizpanda') ?></option>
                                                <option value="days"><?php _e('Day(s)', 'bizpanda') ?></option>
                                                <option value="weeks"><?php _e('Week(s)', 'bizpanda') ?></option>
                                                <option value="months"><?php _e('Month(s)', 'bizpanda') ?></option>
                                                <option value="years"><?php _e('Year(s)', 'bizpanda') ?></option>                                           
                                            </select>
                                            <span class="bp-label"> <?php _e(', younger than', 'bizpanda') ?> </span>
                                            <input type="text" class="bp-date-value bp-date-value-end" value="2" />
                                            <select class="bp-date-end-units">
                                                <option value="seconds"><?php _e('Second(s)', 'bizpanda') ?></option>
                                                <option value="minutes"><?php _e('Minutes(s)', 'bizpanda') ?></option>
                                                <option value="hours"><?php _e('Hours(s)', 'bizpanda') ?></option>
                                                <option value="days"><?php _e('Day(s)', 'bizpanda') ?></option>
                                                <option value="weeks"><?php _e('Week(s)', 'bizpanda') ?></option>
                                                <option value="months"><?php _e('Month(s)', 'bizpanda') ?></option>
                                                <option value="years"><?php _e('Year(s)', 'bizpanda') ?></option>                                             
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="bp-solo-date">
                                        
                                        <div class="bp-absolute-date">
                                           <div class="bp-date-control" data-date="today">
                                                <input size="16" type="text" class="bp-date-value" readonly="readonly" data-date="today" />
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="bp-relative-date">
                                            <input type="text" class="bp-date-value" value="1" />
                                            <select class="bp-date-value-units">
                                                <option value="seconds"><?php _e('Second(s)', 'bizpanda') ?></option>
                                                <option value="minutes"><?php _e('Minutes(s)', 'bizpanda') ?></option>
                                                <option value="hours"><?php _e('Hours(s)', 'bizpanda') ?></option>
                                                <option value="days"><?php _e('Day(s)', 'bizpanda') ?></option>
                                                <option value="weeks"><?php _e('Week(s)', 'bizpanda') ?></option>
                                                <option value="months"><?php _e('Month(s)', 'bizpanda') ?></option>
                                                <option value="years"><?php _e('Year(s)', 'bizpanda') ?></option>                                           
                                            </select>
                                        </div>       
                                        
                                    </div>
                                    
                                </div>
                                <div class="bp-switcher">
                                    <label><input type="radio" checked="checked" value="relative" /> <span><?php _e('relative', 'bizpanda') ?></span></label>
                                    <label><input type="radio" value="absolute" /> </span><?php _e('absolute', 'bizpanda') ?></span></label>
                                </div>
                            </div>
                            
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default bp-add-filter bp-btn-left"><?php _e('+ Add Filter', 'bizpanda') ?></button>
                            <button type="button" class="btn btn-default bp-cancel" data-dismiss="modal"><?php _e('Cancel', 'bizpanda') ?></button>
                            <button type="button" class="btn btn-primary bp-save"><?php _e('Save', 'bizpanda') ?></button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <?php
    }
    
    /**
     * Saves some extra options.
     */
    public function onSavingForm( $post_id) {
        parent::onSavingForm( $post_id );
        
        // saves delay lock options
        
        $delay = isset( $_POST[$this->scope . '_lock_delay'] ); 
        $interval = isset( $_POST[$this->scope . '_lock_delay_interval'] ) 
                ? intval( $_POST[$this->scope . '_lock_delay_interval'] )
                : 0;
        
        if ( $interval < 0 ) $interval = 0;
        
        $units = isset( $_POST[$this->scope . '_lock_delay_interval_units'] ) 
                ? $_POST[$this->scope . '_lock_delay_interval_units']
                : null;
        
        if ( !$units || !in_array($units, array('days', 'hours', 'minutes') )) {
            $units = 'days';
        }
        
        if ( !$interval ) $_POST[$this->scope . '_lock_delay'] = null;
        if ( !$delay ) { 
            $interval = 0;
            $units = 'days';
        }
        
        $intervalInMinutes = $interval;
        if ( $units == 'days' ) $intervalInMinutes = 24 * 60 * $interval;
        if ( $units == 'hours' ) $intervalInMinutes = 60 * $interval;
        
        $this->provider->setValue('lock_delay_interval_in_seconds', $intervalInMinutes * 60 );
        $this->provider->setValue('lock_delay_interval', $interval);   
        $this->provider->setValue('lock_delay_interval_units', $units);  
        
        // saves relock options
        
        $delay = isset( $_POST[$this->scope . '_relock'] ); 
        $interval = isset( $_POST[$this->scope . '_relock_interval'] ) 
                ? intval( $_POST[$this->scope . '_relock_interval'] )
                : 0;
        
        if ( $interval < 0 ) $interval = 0;
        
        $units = isset( $_POST[$this->scope . '_relock_interval_units'] ) 
                ? $_POST[$this->scope . '_relock_interval_units']
                : null;
        
        if ( !$units || !in_array($units, array('days', 'hours', 'minutes') )) {
            $units = 'days';
        }
        
        if ( !$interval ) $_POST[$this->scope . '_relock'] = null;
        if ( !$delay ) { 
            $interval = 0;
            $units = 'days';
        }
        
        $intervalInMinutes = $interval;
        if ( $units == 'days' ) $intervalInMinutes = 24 * 60 * $interval;
        if ( $units == 'hours' ) $intervalInMinutes = 60 * $interval;
        
        $this->provider->setValue('relock_interval_in_seconds', $intervalInMinutes * 60 );
        $this->provider->setValue('relock_interval', $interval);   
        $this->provider->setValue('relock_interval_units', $units);  
        
        do_action('onp_sl_visability_options_on_save', $this);
    }
}

global $bizpanda;
FactoryMetaboxes321::register('OPanda_VisabilityOptionsMetaBox', $bizpanda);

