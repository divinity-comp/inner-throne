<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin side posting handler
 *
 * Builds custom fields UI for post add/edit screen
 * and handles value saving.
 *
 */
class CFM_Admin_Posting extends CFM_Render_Form {

    function __construct() {
        add_action( 'edd_view_order_details_main_after', array( $this, 'render_form' ) );
		add_action( 'edd_update_edited_purchase', array( $this, 'save_meta' ) ); 
    }


    function render_form($form_id, $post_id = NULL, $preview = false) {
        $form_id = get_option( 'edd_cfm_id');
        $form_settings = get_post_meta( $form_id, 'edd-checkout-fields_settings', true );

        list($post_fields, $taxonomy_fields, $custom_fields) = $this->get_input_fields( $form_id );

        if ( empty( $custom_fields ) ) {
            // TODO: Its probably better not to output anything. To revisit later.
			//_e( 'No custom fields found.', 'edd_cfm' );
            return;
        }
        ?>
		<div id="edd-checkout-fields" class="postbox">
            <h3 class="hndle"><?php _e( 'Custom Checkout Fields', 'edd' ); ?></h3>
            <div class="inside">
                <input type="hidden" name="cfm_cf_update" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
                <input type="hidden" name="cfm_cf_form_id" value="<?php echo $form_id; ?>" />
                <table class="form-table cfm-cf-table">
                    <tbody>
                    <?php
                    $this->render_items( $custom_fields, absint( $_GET['id']) , 'post', $form_id, $form_settings );
                    ?>
                    </tbody>
                </table>
                <?php $this->submit_button(); ?>
            </div>
        </div>
        <?php
        $this->scripts_styles();
    }

    /**
     * Prints form input label
     *
     * @param string $attr
     */
    function label( $attr, $post_id = 0) {
        echo $attr['label'] . $this->required_mark( $attr );
    }

    function render_item_before( $form_field, $post_id = 0 ) {
        echo '<tr>';
        echo '<th><strong>';
        $this->label( $form_field );
        echo '</strong></th>';
        echo '<td>';
    }

    function render_item_after( $attr, $post_id = 0) {
        echo '</td>';
        echo '</tr>';
    }

    function scripts_styles() {
        ?>
        <script type="text/javascript">
            jQuery(function($){
                var cfm = {
                    init: function() {
                        $('.cfm-cf-table').on('click', 'img.cfm-clone-field', this.cloneField);
                        $('.cfm-cf-table').on('click', 'img.cfm-remove-field', this.removeField);
                    },
                    cloneField: function(e) {
                        e.preventDefault();

                        var $div = $(this).closest('tr');
                        var $clone = $div.clone();
                        // console.log($clone);

                        //clear the inputs
                        $clone.find('input').val('');
                        $clone.find(':checked').attr('checked', '');
                        $div.after($clone);
                    },

                    removeField: function() {
                        //check if it's the only item
                        var $parent = $(this).closest('tr');
                        var items = $parent.siblings().andSelf().length;

                        if( items > 1 ) {
                            $parent.remove();
                        }
                    },
                };

                cfm.init();
            });

        </script>
        <style type="text/css">
            ul.cfm-attachment-list li {
                display: inline-block;
                border: 1px solid #dfdfdf;
                padding: 5px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                margin-right: 5px;
            }
            ul.cfm-attachment-list li a.attachment-delete {
                text-decoration: none;
                padding: 3px 12px;
                border: 1px solid #C47272;
                color: #ffffff;
                text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
                background-color: #da4f49;
                background-image: -moz-linear-gradient(top, #ee5f5b, #bd362f);
                background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#bd362f));
                background-image: -webkit-linear-gradient(top, #ee5f5b, #bd362f);
                background-image: -o-linear-gradient(top, #ee5f5b, #bd362f);
                background-image: linear-gradient(to bottom, #ee5f5b, #bd362f);
                background-repeat: repeat-x;
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffbd362f', GradientType=0);
                border-color: #bd362f #bd362f #802420;
                border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
                *background-color: #bd362f;
                filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            }
            ul.cfm-attachment-list li a.attachment-delete:hover,
            ul.cfm-attachment-list li a.attachment-delete:active {
                color: #ffffff;
                background-color: #bd362f;
                *background-color: #a9302a;
            }

            .cfm-cf-table table th,
            .cfm-cf-table table td{
                padding-left: 0 !important;
            }

            .cfm-cf-table .required { color: red;}
            .cfm-cf-table textarea { width: 400px; }

        </style>
        <?php
    }

    // Save the Metabox Data
    function save_meta( $post_id ) {
		
        if ( !isset( $_POST['cfm_cf_update'] ) ) {
            return;
        }

        list( $post_vars, $tax_vars, $meta_vars ) = self::get_input_fields( $_POST['cfm_cf_form_id'] );
		$form_id       = get_option( 'edd_cfm_id' );
		$form_vars     = self::get_input_fields( $form_id );
        EDD_CFM()->frontend_form_post->update_post_meta( $meta_vars,  absint( $_GET['id']), $form_vars );
    }
	
	function submit_button( ) {
        $form_settings['update_text']= __( 'Update', 'edd_cfm' );
		?>
        <fieldset class="cfm-submit">
            <div class="cfm-label">
                &nbsp;
            </div>

            <?php wp_nonce_field( 'cfm_cf_update' ); ?>
            <input type="hidden" name="cfm_cf_update" value="cfm_cf_update">
            <input type="submit" class="button" name="submit" value="<?php echo $form_settings['update_text']; ?>" />
        </fieldset>
        <?php
    }

}