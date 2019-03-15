<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Form UI Builder
 */
class CFM_Admin_Form {

    private $form_data_key = 'edd-checkout-fields';
    private $form_settings_key = 'edd-checkout-fields_settings';

    /**
     * Add neccessary actions and filters
     *
     * @return void
     */
    function __construct() {
        add_filter( 'post_updated_messages', array($this, 'form_updated_message') );
		add_action( 'save_post', array( $this, 'save_form_meta' ), 1, 2 );
        
		// meta boxes
        add_action( 'add_meta_boxes_edd-checkout-fields', array($this, 'add_meta_boxes') );

        // ajax actions for post forms
        add_action( 'wp_ajax_edd-checkout-fields_dump', array( $this, 'form_dump' ) );
        add_action( 'wp_ajax_edd-checkout-fields_add_el', array( $this, 'ajax_post_add_element' ) );
    }
	
    function form_updated_message( $messages ) {
        $message = array(
             0 => '',
             1 => __( 'Checkout fields updated!', 'edd_cfm' ),
             2 => __( 'Custom field updated.', 'edd_cfm' ),
             3 => __( 'Custom field deleted.', 'edd_cfm' ),
             4 => __( 'Form updated.', 'edd_cfm' ),
             5 => isset($_GET['revision']) ? sprintf( __( 'Form restored to revision from %s', 'edd_cfm' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
             6 => __( 'Form published.', 'edd_cfm' ),
             7 => __( 'Checkout fields saved!', 'edd_cfm' ),
             8 => __( 'Form submitted.', 'edd_cfm' ),
             9 => '',
            10 => __( 'Form draft updated.', 'edd_cfm' ),
        );

        $messages['edd-checkout-fields'] = $message;
        return $messages;
    }

    /**
     * Add meta boxes to form builders
     *
     * @return void
     */
    function add_meta_boxes() {
		global $post;
        add_meta_box( 'cfm-metabox-editor', __( 'Submission Form Editor', 'edd_cfm' ), array($this, 'metabox_post_form'), 'edd-checkout-fields', 'normal', 'high' );
        add_meta_box( 'cfm-metabox-fields', __( 'Add Field', 'edd_cfm' ), array($this, 'form_elements_post'), 'edd-checkout-fields', 'side', 'core' );
		remove_meta_box('submitdiv', 'edd-checkout-fields', 'side');
        remove_meta_box('slugdiv', 'edd-checkout-fields', 'normal');
	}

    function publish_button() {
        global $post, $pagenow;
        ?>
        <div class="submitbox" id="submitpost" style="float:left">
            <div id="major-publishing-actions">
                <div id="publishing-action">
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Save' ) ?>" />
                        <input name="save" type="submit" class="button button-primary button-large" style="float:left" id="publish" accesskey="p" value="<?php esc_attr_e( 'Save' ) ?>" />
						<span class="spinner" style="float:right" ></span>
                </div>
                <div class="cfm-clear"></div>
            </div>
       </div>
        <?php
    }

     function metabox_post_form( $post ) {
        ?>
        <h1><?php _e('Checkout Fields','edd_cfm');?></h1>
        <div class="tab-content">
            <div id="cfm-metabox" class="group">
                <?php $this->edit_form_area(); ?>
            </div>
            <?php do_action( 'cfm_post_form_tab_content' ); ?>
        </div>
        <?php
    }

    /**
     * Form elements for post form builder
     *
     * @return void
     */
    function form_elements_post() {
		$title = __( 'Click to add to the editor', 'edd_cfm' );
		?>
        <div class="cfm-loading hide"></div>
        <div class="edd-checkout-fields-buttons">
            <!--<button class="cfm-button button" data-name="edd_first" data-type="textarea"><?php _e( 'First', 'edd_cfm' ); ?></button> -->
            <button class="cfm-button button" data-name="edd_last" data-type="textarea"><?php _e( 'Last Name', 'edd_cfm' ); ?></button><br />
            <!--<button class="cfm-button button" data-name="edd_email" data-type="category"><?php _e( 'E-mail', 'edd_cfm' ); ?></button><br /> -->
			<button class="cfm-button button" data-name="custom_text" data-type="text" title="<?php echo $title; ?>"><?php _e( 'Text', 'edd_cfm' ); ?></button>
            <button class="cfm-button button" data-name="custom_textarea" data-type="textarea" title="<?php echo $title; ?>"><?php _e( 'Textarea', 'edd_cfm' ); ?></button><br />
            <button class="cfm-button button" data-name="custom_select" data-type="select" title="<?php echo $title; ?>"><?php _e( 'Dropdown', 'edd_cfm' ); ?></button>
            <button class="cfm-button button" data-name="custom_date" data-type="date" title="<?php echo $title; ?>"><?php _e( 'Date', 'edd_cfm' ); ?></button><br />
            <button class="cfm-button button" data-name="custom_radio" data-type="radio" title="<?php echo $title; ?>"><?php _e( 'Radio', 'edd_cfm' ); ?></button>
            <button class="cfm-button button" data-name="custom_checkbox" data-type="checkbox" title="<?php echo $title; ?>"><?php _e( 'Checkbox', 'edd_cfm' ); ?></button><br />
            <button class="cfm-button button" data-name="custom_file" data-type="file" title="<?php echo $title; ?>"><?php _e( 'File Upload', 'edd_cfm' ); ?></button>
            <button class="cfm-button button" data-name="custom_url" data-type="url" title="<?php echo $title; ?>"><?php _e( 'URL', 'edd_cfm' ); ?></button><br />
            <button class="cfm-button button" data-name="custom_multiselect" data-type="multiselect" title="<?php echo $title; ?>"><?php _e( 'Multi Select', 'edd_cfm' ); ?></button>
            <button class="cfm-button button" data-name="custom_repeater" data-type="repeat" title="<?php echo $title; ?>"><?php _e( 'Repeat Field', 'edd_cfm' ); ?></button><br />
            <button class="cfm-button button" data-name="custom_html" data-type="html" title="<?php echo $title; ?>"><?php _e( 'HTML', 'edd_cfm' ); ?></button>
            <button class="cfm-button button" data-name="action_hook" data-type="action" title="<?php echo $title; ?>"><?php _e( 'Do Action', 'edd_cfm' ); ?></button><br />

			<?php do_action( 'edd-checkout-fields_buttons_post' ); ?>
	    </div>
		<?php 
        $this->publish_button();
    }

    /**
     * Saves the form settings
     *
     * @param int $post_id
     * @param object $post
     * @return int|void
     */
    function save_form_meta( $post_id, $post ) {
        if ( !isset($_POST['edd-checkout-fields_editor'])) {
            return $post->ID;
        }

        if ( !wp_verify_nonce( $_POST['edd-checkout-fields_editor'], plugin_basename( __FILE__ ) ) ) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ) ) {
            return $post->ID;
        }

        update_post_meta( $post->ID, $this->form_data_key, $_POST['cfm_input'] );
    }

    /**
     * Edit form elements area for post
     *
     * @global object $post
     * @global string $pagenow
     */
    function edit_form_area() {
        global $post, $pagenow;

        $form_inputs = get_post_meta( $post->ID, $this->form_data_key, true );
        ?>

        <input type="hidden" name="edd-checkout-fields_editor" id="edd-checkout-fields_editor" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

        <div style="margin-bottom: 10px">
          <button class="button cfm-collapse"><?php _e( 'Toggle All Fields Open/Close', 'edd_cfm' ); ?></button>
        </div>
		<?php if ( empty( $form_inputs ) ){ ?>		
        <div class="cfm-updated">
            <p><?php _e( 'Your checkout form has no fields!', 'edd_cfm' ); ?></p>
        </div>
		<?php } ?>
        <ul id="edd-checkout-fields-editor" class="edd-checkout-fields-editor unstyled">

        <?php
        if ($form_inputs) {
            $count = 0;
            foreach ($form_inputs as $order => $input_field) {
                $name = ucwords( str_replace( '_', ' ', $input_field['template'] ) );
                CFM_Admin_Template::$input_field['template']( $count, $name, $input_field );
                $count++;
            }
        }
        ?>
        </ul>

        <?php
    }

    /**
     * Ajax Callback handler for inserting fields in forms
     *
     * @return void
     */
    function ajax_post_add_element() {

        $name = $_POST['name'];
        $type = $_POST['type'];
        $field_id = $_POST['order'];

        switch ($name) {
            case 'custom_text':
			    CFM_Admin_Template::text_field( $field_id, __( 'Custom field: Text','edd_cfm'));
                break;

            case 'custom_textarea':
                CFM_Admin_Template::textarea_field( $field_id, __( 'Custom field: Textarea','edd_cfm'));
                break;

            case 'custom_select':
                CFM_Admin_Template::dropdown_field( $field_id, __( 'Custom field: Select','edd_cfm'));
                break;

            case 'custom_multiselect':
                CFM_Admin_Template::multiple_select( $field_id, __( 'Custom field: Multiselect','edd_cfm'));
                break;

            case 'custom_radio':
                CFM_Admin_Template::radio_field( $field_id, __( 'Custom field: Radio','edd_cfm'));
                break;

            case 'custom_checkbox':
                CFM_Admin_Template::checkbox_field( $field_id, __( 'Custom field: Checkbox','edd_cfm'));
                break;

            case 'custom_file':
                CFM_Admin_Template::file_upload( $field_id, __( 'Custom field: File Upload','edd_cfm'));
                break;

            case 'custom_url':
                CFM_Admin_Template::website_url( $field_id, __( 'Custom field: URL','edd_cfm'));
                break;

            case 'custom_email':
                CFM_Admin_Template::email_address( $field_id, __( 'Custom field: E-Mail','edd_cfm'));
                break;

            case 'custom_repeater':
                CFM_Admin_Template::repeat_field( $field_id, __( 'Custom field: Repeat Field','edd_cfm'));
                break;

            case 'custom_html':
                CFM_Admin_Template::custom_html( $field_id, __( 'HTML','edd_cfm') );
                break;

            case 'action_hook':
                CFM_Admin_Template::action_hook( $field_id, __( 'Action Hook','edd_cfm') );
                break;

            case 'custom_date':
                CFM_Admin_Template::date_field( $field_id, __( 'Custom Field: Date','edd_cfm') );
                break;

            case 'edd_first':
                CFM_Admin_Template::edd_first( $field_id, __( 'First Name', 'edd_cfm' ) );
                break;

            case 'edd_last':
                CFM_Admin_Template::edd_last( $field_id, __( 'Last Name', 'edd_cfm' ) );
                break;

            case 'edd_email':
                CFM_Admin_Template::edd_email( $field_id, __( 'Email', 'edd_cfm' ) );
                break;

            default:
                do_action( 'cfm_admin_field_' . $name, $type, $field_id );
                break;
        }

        exit;
    }

}