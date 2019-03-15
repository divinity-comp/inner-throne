<?php
/**
 * The file contains a class to configure the metabox "More Features?".
 * 
 * Created via the Factory Metaboxes.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2013, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The class to configure the metabox "More Features?".
 * 
 * @since 1.0.0
 */
class OPanda_TermsOptionsMetaBox extends FactoryMetaboxes321_FormMetabox
{
    /**
     * A visible title of the metabox.
     * 
     * Inherited from the class FactoryMetabox.
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * 
     * @since 1.0.0
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
     * @since 1.0.0
     * @var string
     */
    public $priority = 'core';
    
    /**
     * The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side'). 
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Inherited from the class FactoryMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $context = 'side';
    
    public function __construct( $plugin ) {
        parent::__construct( $plugin );
        
        $this->title = __('Terms & Policies', 'bizpanda');
    }
    
    public $cssClass = 'factory-bootstrap-330 factory-fontawesome-320';
    
    public function configure( $scripts, $styles ){
        $scripts->add( OPANDA_BIZPANDA_URL . '/assets/admin/js/metaboxes/terms.010000.js');
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
        
        
        $itemType = OPanda_Items::getCurrentItem();
        
        $hint = __('Consent Checkbox for GDPR compatibility.', 'bizpanda');
        if ( 'social-locker' === $itemType['name'] ) {
            $hint = sprintf( __('Consent Checkbox for <a href="%s" target="_blank">GDPR compatibility.</a>', 'bizpanda'), opanda_get_help_url('gdpr-social-locker') );
        } elseif ( 'signin-locker' === $itemType['name'] ) {
            $hint = sprintf( __('Consent Checkbox for <a href="%s" target="_blank">GDPR compatibility.</a>', 'bizpanda'), opanda_get_help_url('gdpr-signin-locker') );
        } elseif ( 'email-locker' === $itemType['name'] ) {
            $hint = sprintf( __('Consent Checkbox for <a href="%s" target="_blank">GDPR compatibility.</a>', 'bizpanda'), opanda_get_help_url('gdpr-email-locker') );
        }
            
        $options = array(  
            
            array(
                'type'      => 'html',
                'html'      => array(&$this, 'showTermsContentNote')
            ),
            
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'agreement_checkbox',
                'title'     => '<i class="fa fa-check-square-o" style="font-size: 17px; margin-right: 8px; top: 2px; position: relative;"></i>' . __('Consent Checkbox', 'bizpanda'),
                'hint'      => $hint,
                'default'   => false
            ),
            
            array(
                'type'      => 'html',
                'html'      => array($this, 'htmlConsentCheckboxOption')
            ),
            
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'agreement_note',
                'title'     => '<i class="fa fa-flag" style="font-size: 17px; margin-right: 8px;"></i>' . __('Footer Reference', 'bizpanda'),
                'hint'      => __('Shows a reference to Terms & Policies at bottom.', 'bizpanda'),
                'default'   => false
            ),
        );
        
        $options = apply_filters('opanda_terms_and_policies_options', $options);
        $form->add($options);
    }
    
    public function htmlConsentCheckboxOption() {
        $consentCheckbox = $this->provider->getValue('agreement_checkbox', false);
        
        $checkboxPosiion = $this->provider->getValue('agreement_checkbox_position', 'bottom');
        if ( empty($checkboxPosiion) ) $checkboxPosiion = 'bottom';
        ?>
        <div class='onp-sl-sub <?php if ( !$consentCheckbox ) { echo 'hide'; } ?>' id='onp-sl-agreement_checkbox-options'>
            <label class='control-label' style="margin-bottom: 5px;"><?php _e('The checkbox will appear at:', 'bizpanda') ?></label>
            <select class='form-control' name='<?php echo $this->scope ?>_agreement_checkbox_position' id="<?php echo $this->scope ?>_agreement_checkbox_position">
                <option value='top' <?php selected('top', $checkboxPosiion) ?>><?php _e('Top', 'bizpanda') ?></option>    
                <option value='bottom' <?php selected('bottom', $checkboxPosiion) ?>><?php _e('Bottom', 'bizpanda') ?></option> 
            </select>
        </div>
        <?php
    }
    
    public function showTermsContentNote() {
        ?>
            <?php printf( __('You can change content of your Terms & Policies pages <a href="%s" target="_blank">here</a>.'), admin_url('admin.php?page=settings-' . $this->plugin->pluginName . '&opanda_screen=terms&action=index') ) ?>
        <?php
    }
    

    /**
     * Saves some extra options.
     */
    public function onSavingForm( $post_id) {
        parent::onSavingForm( $post_id );

        $checkbox = isset( $_POST[$this->scope . '_agreement_checkbox'] )
                        ? $_POST[$this->scope . '_agreement_checkbox']
                        : false;
        
        $position = isset( $_POST[$this->scope . '_agreement_checkbox_position'] )
                        ? $_POST[$this->scope . '_agreement_checkbox_position']
                        : 'bottom';
        
        if ( !$checkbox ) $position = false;

        $this->provider->setValue('agreement_checkbox_position', $position );
    }
}

global $bizpanda;
FactoryMetaboxes321::register('OPanda_TermsOptionsMetaBox', $bizpanda);
