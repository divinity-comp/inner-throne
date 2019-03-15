<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CFM Form builder template
 */
class CFM_Admin_Template {

    static $input_name = 'cfm_input';

    /**
     * Legend of a form item
     *
     * @param string $title
     * @param array $values
     */
    public static function legend( $title = 'Field Name', $values = array(), $removeable = true, $custom = false ) {
		if ( $custom ){
			$title = '';
			$field_label = $values ? '<strong>' . $values['label'] . '</strong>' : '';			
        }
		else{
			$field_label = $values ? ': <strong>' . $values['label'] . '</strong>' : '';
		}
		?>
        <div class="cfm-legend" title="<?php _e( 'Click and Drag to rearrange', 'edd_cfm'); ?>">
            <div class="cfm-label"><?php echo $title . $field_label; ?></div>
            <div class="cfm-actions">
				<?php if ($removeable){ ?>
                <a href="#" class="cfm-remove"><?php _e( 'Remove', 'edd_cfm' ); ?></a>
				<?php } ?>
                <a href="#" class="cfm-toggle"><?php _e( 'Toggle', 'edd_cfm' ); ?></a>
            </div>
        </div> <!-- .cfm-legend -->
        <?php
    }

    /**
     * Common Fields for a input field
     *
     * Contains required, label, meta_key, help text, css class name
     *
     * @param int $id field order
     * @param mixed $field_name_value
     * @param bool $custom_field if it a custom field or not
     * @param array $values saved value
     */
    public static function common( $id, $field_name_value = '', $custom_field = true, $values = array(), $reqtoggle = true, $csstoggle = true ) {
        $tpl = '%s[%d][%s]';
        $required_name = sprintf( $tpl, self::$input_name, $id, 'required' );
        $field_name = sprintf( $tpl, self::$input_name, $id, 'name' );
        $label_name = sprintf( $tpl, self::$input_name, $id, 'label' );
        $is_meta_name = sprintf( $tpl, self::$input_name, $id, 'is_meta' );
        $help_name = sprintf( $tpl, self::$input_name, $id, 'help' );
        $css_name = sprintf( $tpl, self::$input_name, $id, 'css' );
		
        $required = $values && isset($values['required']) ? esc_attr( $values['required'] ) : 'yes';
        $label_value = $values && isset($values['label']) ? esc_attr( $values['label'] ) : '';
        $help_value = $values && isset($values['help'])? esc_textarea( $values['help'] ) : '';
        $css_value = $values && isset($values['css'])? esc_attr( $values['css'] ) : '';

        if ($custom_field && $values) {
            $field_name_value = $values['name'];
        }
		do_action('edd_cfm_add_field_to_common_form_element', $tpl, self::$input_name, $id, $values);
        ?>
		
        <div class="edd-checkout-fields-rows required-field">
            <label><?php _e( 'Required', 'edd_cfm' ); ?></label>

            <?php //self::hidden_field($order_name, ''); ?>
            <div class="edd-checkout-fields-sub-fields">
                <label><input type="radio" name="<?php echo $required_name; ?>" value="yes"<?php checked( $required, 'yes' ); ?>> <?php _e( 'Yes', 'edd_cfm' ); ?> </label>
				<?php if($reqtoggle){ ?>
				<label><input type="radio" name="<?php echo $required_name; ?>" value="no"<?php checked( $required, 'no' ); ?>> <?php _e( 'No', 'edd_cfm' ); ?> </label>
				<?php } ?>
			</div>
        </div> <!-- .edd-checkout-fields-rows -->

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Field Label', 'edd_cfm' ); ?></label>
            <input type="text" data-type="label" name="<?php echo $label_name; ?>" value="<?php echo $label_value; ?>" class="smallipopInput" title="<?php _e( 'Enter a title of this field', 'edd_cfm' ); ?>">
        </div> <!-- .edd-checkout-fields-rows -->

        <?php if ( $custom_field ) { ?>
            <div class="edd-checkout-fields-rows">
                <label><?php _e( 'Meta Key', 'edd_cfm' ); ?></label>
                <input type="text" name="<?php echo $field_name; ?>" value="<?php echo $field_name_value; ?>" class="smallipopInput" title="<?php _e( 'Name of the meta key this field will save to', 'edd_cfm' ); ?>">
                <input type="hidden" name="<?php echo $is_meta_name; ?>" value="yes">
            </div> <!-- .edd-checkout-fields-rows -->
        <?php } else { ?>

            <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_name_value; ?>">
            <input type="hidden" name="<?php echo $is_meta_name; ?>" value="no">

        <?php } ?>

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Help text', 'edd_cfm' ); ?></label>
            <textarea name="<?php echo $help_name; ?>" class="smallipopInput" title="<?php _e( 'Give the user some information about this field', 'edd_cfm' ); ?>"><?php echo $help_value; ?></textarea>
        </div> <!-- .edd-checkout-fields-rows -->
		<?php if( $reqtoggle && $csstoggle ) { ?>
        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'CSS Class Name', 'edd_cfm' ); ?></label>
            <input type="text" name="<?php echo $css_name; ?>" value="<?php echo $css_value; ?>" class="smallipopInput" title="<?php _e( 'Add a CSS class name for this field', 'edd_cfm' ); ?>">
        </div> <!-- .edd-checkout-fields-rows -->
		<?php }
			  else { ?>
            <input type="hidden" name="<?php echo $css_name; ?>" value="">				  
        <?php }
    }

    /**
     * Common fields for a text area
     *
     * @param int $id
     * @param array $values
     */
    public static function common_text( $id, $values = array() ) {
        $tpl = '%s[%d][%s]';
        $placeholder_name = sprintf( $tpl, self::$input_name, $id, 'placeholder' );
        $default_name = sprintf( $tpl, self::$input_name, $id, 'default' );
        $size_name = sprintf( $tpl, self::$input_name, $id, 'size' );

        $placeholder_value = $values && isset($values['placeholder'])? esc_attr( $values['placeholder'] ) : '';
        $default_value = $values && isset($values['default']) ? esc_attr( $values['default'] ) : '';
        $size_value = $values && isset($values['size']) ? esc_attr( $values['size'] ) : '40';

        ?>
        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Placeholder text', 'edd_cfm' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo $placeholder_name; ?>" title="<?php esc_attr_e( 'Text for HTML5 placeholder attribute', 'edd_cfm' ); ?>" value="<?php echo $placeholder_value; ?>" />
        </div> <!-- .edd-checkout-fields-rows -->

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Default value', 'edd_cfm' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo $default_name; ?>" title="<?php esc_attr_e( 'The default value this field will have', 'edd_cfm' ); ?>" value="<?php echo $default_value; ?>" />
        </div> <!-- .edd-checkout-fields-rows -->

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Size', 'edd_cfm' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo $size_name; ?>" title="<?php esc_attr_e( 'Size of this input field', 'edd_cfm' ); ?>" value="<?php echo $size_value; ?>" />
        </div> <!-- .edd-checkout-fields-rows -->
        <?php
    }

    /**
     * Common fields for a textarea
     *
     * @param int $id
     * @param array $values
     */
    public static function common_textarea( $id, $values = array() ) {
        $tpl = '%s[%d][%s]';
        $rows_name = sprintf( $tpl, self::$input_name, $id, 'rows' );
        $cols_name = sprintf( $tpl, self::$input_name, $id, 'cols' );
        $rich_name = sprintf( $tpl, self::$input_name, $id, 'rich' );
        $placeholder_name = sprintf( $tpl, self::$input_name, $id, 'placeholder' );
        $default_name = sprintf( $tpl, self::$input_name, $id, 'default' );

        $rows_value = $values && isset( $values['rows'] )? esc_attr( $values['rows'] ) : '5';
        $cols_value = $values && isset( $values['cols'] )? esc_attr( $values['cols'] ) : '25';
        $rich_value = $values && isset( $values['rich'] )? esc_attr( $values['rich'] ) : 'no';
        $placeholder_value = $values && isset( $values['placeholder'] )? esc_attr( $values['placeholder'] ) : '';
        $default_value = $values && isset( $values['default'] )? esc_attr( $values['default'] ) : '';

        ?>
        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Rows', 'edd_cfm' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo $rows_name; ?>" title="Number of rows in textarea" value="<?php echo $rows_value; ?>" />
        </div> <!-- .edd-checkout-fields-rows -->

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Columns', 'edd_cfm' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo $cols_name; ?>" title="Number of columns in textarea" value="<?php echo $cols_value; ?>" />
        </div> <!-- .edd-checkout-fields-rows -->

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Placeholder text', 'edd_cfm' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo $placeholder_name; ?>" title="text for HTML5 placeholder attribute" value="<?php echo $placeholder_value; ?>" />
        </div> <!-- .edd-checkout-fields-rows -->

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Default value', 'edd_cfm' ); ?></label>
            <input type="text" class="smallipopInput" name="<?php echo $default_name; ?>" title="the default value this field will have" value="<?php echo $default_value; ?>" />
        </div> <!-- .edd-checkout-fields-rows -->

        <div class="edd-checkout-fields-rows">
            <label><?php _e( 'Textarea', 'edd_cfm' ); ?></label>

            <div class="edd-checkout-fields-sub-fields">
                <label><input type="radio" name="<?php echo $rich_name; ?>" value="no"<?php checked( $rich_value, 'no' ); ?>> <?php _e( 'Normal', 'edd_cfm' ); ?></label>
                <label><input type="radio" name="<?php echo $rich_name; ?>" value="yes"<?php checked( $rich_value, 'yes' ); ?>> <?php _e( 'Rich textarea', 'edd_cfm' ); ?></label>
                <label><input type="radio" name="<?php echo $rich_name; ?>" value="teeny"<?php checked( $rich_value, 'teeny' ); ?>> <?php _e( 'Teeny Rich textarea', 'edd_cfm' ); ?></label>
            </div>
        </div> <!-- .edd-checkout-fields-rows -->
        <?php
    }

    /**
     * Hidden field helper function
     *
     * @param string $name
     * @param string $value
     */
    public static function hidden_field( $name, $value = '' ) {
        printf( '<input type="hidden" name="%s" value="%s" />', self::$input_name . $name, $value );
    }

    /**
     * Displays a radio custom field
     *
     * @param int $field_id
     * @param string $name
     * @param array $values
     */
    public static function radio_fields( $field_id, $name, $values = array() ) {
        $selected_name = sprintf( '%s[%d][selected]', self::$input_name, $field_id );
        $input_name = sprintf( '%s[%d][%s]', self::$input_name, $field_id, $name );

        $selected_value = ( $values && isset( $values['selected'] ) ) ? $values['selected'] : '';

        if ( $values && $values['options'] > 0 ) {
            foreach ($values['options'] as $key => $value) {
                ?>
                <div>
                    <input type="radio" name="<?php echo $selected_name ?>" value="<?php echo $value; ?>" <?php checked( $selected_value, $value ); ?>>
                    <input type="text" name="<?php echo $input_name; ?>[]" value="<?php echo $value; ?>">

                    <?php self::remove_button(); ?>
                </div>
                <?php
            }
        } else {
        ?>
            <div>
                <input type="radio" name="<?php echo $selected_name ?>">
                <input type="text" name="<?php echo $input_name; ?>[]" value="">

                <?php self::remove_button(); ?>
            </div>
        <?php
        }
    }

    /**
     * Displays a checkbox custom field
     *
     * @param int $field_id
     * @param string $name
     * @param array $values
     */
    public static function common_checkbox( $field_id, $name, $values = array() ) {
        $selected_name = sprintf( '%s[%d][selected]', self::$input_name, $field_id );
        $input_name = sprintf( '%s[%d][%s]', self::$input_name, $field_id, $name );

        $selected_value = ( $values && isset( $values['selected'] ) ) ? $values['selected'] : array();

        if ( $values && $values['options'] > 0 ) {
            foreach ($values['options'] as $key => $value) {
                ?>
                <div>
                    <input type="checkbox" name="<?php echo $selected_name ?>[]" value="<?php echo $value; ?>"<?php echo in_array($value, $selected_value) ? ' checked="checked"' : ''; ?> />
                    <input type="text" name="<?php echo $input_name; ?>[]" value="<?php echo $value; ?>">

                    <?php self::remove_button(); ?>
                </div>
                <?php
            }
        } else {
        ?>
            <div>
                <input type="checkbox" name="<?php echo $selected_name ?>[]">
                <input type="text" name="<?php echo $input_name; ?>[]" value="">

                <?php self::remove_button(); ?>
            </div>
        <?php
        }
    }

    /**
     * Add/remove buttons for repeatable fields
     *
     * @return void
     */
    public static function remove_button() {
        $add = cfm_assets_url .'img/add.png';
        $remove = cfm_assets_url. 'img/remove.png';
        ?>
        <img style="cursor:pointer; margin:0 3px;" alt="add another choice" title="add another choice" class="cfm-clone-field" src="<?php echo $add; ?>">
        <img style="cursor:pointer;" class="cfm-remove-field" alt="remove this choice" title="remove this choice" src="<?php echo $remove; ?>">
        <?php
    }

    public static function get_buffered($func, $field_id, $label) {
        ob_start();

        self::$func( $field_id, $label );

        return ob_get_clean();
    }

    public static function text_field( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true ) {
        ?>
        <li class="custom-field text_field">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'text' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'text_field' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>
                <?php self::common_text( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function textarea_field( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        ?>
        <li class="custom-field textarea_field">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'textarea' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'textarea_field' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>
                <?php self::common_textarea( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function radio_field( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        ?>
        <li class="custom-field radio_field">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'radio' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'radio_field' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Options', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <?php self::radio_fields( $field_id, 'options', $values ); ?>
                    </div> <!-- .edd-checkout-fields-sub-fields -->
                </div> <!-- .edd-checkout-fields-rows -->
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function checkbox_field( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        ?>
        <li class="custom-field checkbox_field">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'checkbox' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'checkbox_field' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Options', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <?php self::common_checkbox( $field_id, 'options', $values ); ?>
                    </div> <!-- .edd-checkout-fields-sub-fields -->
                </div> <!-- .edd-checkout-fields-rows -->
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function dropdown_field( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        $first_name = sprintf('%s[%d][first]', self::$input_name, $field_id);
        $first_value = $values ? $values['first'] : ' - select -';
        $help = esc_attr( __( 'First element of the select dropdown. Leave this empty if you don\'t want to show this field', 'edd_cfm' ) );
        ?>
        <li class="custom-field dropdown_field">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'select' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'dropdown_field' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Select Text', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $first_name; ?>" value="<?php echo $first_value; ?>" title="<?php echo $help; ?>">
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Options', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <?php self::radio_fields( $field_id, 'options', $values ); ?>
                    </div> <!-- .edd-checkout-fields-sub-fields -->
                </div> <!-- .edd-checkout-fields-rows -->
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function multiple_select( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        $first_name = sprintf('%s[%d][first]', self::$input_name, $field_id);
        $first_value = $values ? $values['first'] : ' - select -';
        $help = esc_attr( __( 'First element of the select dropdown. Leave this empty if you don\'t want to show this field', 'edd_cfm' ) );
        ?>
        <li class="custom-field multiple_select">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'multiselect' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'multiple_select' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Select Text', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $first_name; ?>" value="<?php echo $first_value; ?>" title="<?php echo $help; ?>">
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Options', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <?php self::radio_fields( $field_id, 'options', $values ); ?>
                    </div> <!-- .edd-checkout-fields-sub-fields -->
                </div> <!-- .edd-checkout-fields-rows -->
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }
	
	public static function file_upload( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        $max_size_name = sprintf('%s[%d][max_size]', self::$input_name, $field_id);
        $max_files_name = sprintf('%s[%d][count]', self::$input_name, $field_id);
        $extensions_name = sprintf('%s[%d][extension][]', self::$input_name, $field_id);

        $max_size_value = $values ? $values['max_size'] : '1024';
        $max_files_value = $values ? $values['count'] : '1';
        $extensions_value = $values ? $values['extension'] : array('images', 'audio', 'video', 'pdf', 'office', 'zip', 'exe', 'csv');

        $extensions = cfm_allowed_extensions();

        $help = esc_attr( __( 'Enter maximum upload size limit in KB', 'edd_cfm' ) );
        $count = esc_attr( __( 'Number of images can be uploaded', 'edd_cfm' ) );
        ?>
        <li class="custom-field custom_image">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'file_upload' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'file_upload' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Max. file size', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $max_size_name; ?>" value="<?php echo $max_size_value; ?>" title="<?php echo $help; ?>">
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Max. files', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $max_files_name; ?>" value="<?php echo $max_files_value; ?>" title="<?php echo $count; ?>">
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Allowed Files', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <?php foreach ($extensions as $key => $value) {
                            ?>
                            <label>
                                <input type="checkbox" name="<?php echo $extensions_name; ?>" value="<?php echo $key; ?>"<?php echo in_array($key, $extensions_value) ? ' checked="checked"' : ''; ?>>
                                <?php printf('%s (%s)', $value['label'], str_replace( ',', ', ', $value['ext'] ) ) ?>
                            </label> <br />
                        <?php } ?>
                    </div>
                </div> <!-- .edd-checkout-fields-rows -->
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function website_url( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        ?>
        <li class="custom-field website_url">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'url' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'website_url' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>
                <?php self::common_text( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function email_address( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        ?>
        <li class="custom-field eamil_address">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'email' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'email_address' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>
                <?php self::common_text( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function repeat_field( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        $tpl = '%s[%d][%s]';

        $enable_column_name = sprintf( '%s[%d][multiple]', self::$input_name, $field_id );
        $column_names = sprintf( '%s[%d][columns]', self::$input_name, $field_id );
        $has_column = ( $values && isset( $values['multiple'] ) ) ? true : false;

        $placeholder_name = sprintf( $tpl, self::$input_name, $field_id, 'placeholder' );
        $default_name = sprintf( $tpl, self::$input_name, $field_id, 'default' );
        $size_name = sprintf( $tpl, self::$input_name, $field_id, 'size' );

        $placeholder_value = $values ? esc_attr( $values['placeholder'] ) : '';
        $default_value = $values ? esc_attr( $values['default'] ) : '';
        $size_value = $values ? esc_attr( $values['size'] ) : '30';

        ?>
        <li class="custom-field custom_repeater">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'repeat' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'repeat_field' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Multiple Column', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <label><input type="checkbox" class="multicolumn" name="<?php echo $enable_column_name ?>"<?php echo $has_column ? ' checked="checked"' : ''; ?> value="true"> Enable Multi Column</label>
                    </div>
                </div>

                <div class="edd-checkout-fields-rows<?php echo $has_column ? ' cfm-hide' : ''; ?>">
                    <label><?php _e( 'Placeholder text', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $placeholder_name; ?>" title="text for HTML5 placeholder attribute" value="<?php echo $placeholder_value; ?>" />
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows<?php echo $has_column ? ' cfm-hide' : ''; ?>">
                    <label><?php _e( 'Default value', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $default_name; ?>" title="the default value this field will have" value="<?php echo $default_value; ?>" />
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Size', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $size_name; ?>" title="Size of this input field" value="<?php echo $size_value; ?>" />
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows column-names<?php echo $has_column ? '' : ' cfm-hide'; ?>">
                    <label><?php _e( 'Columns', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                    <?php

                        if ( $values && $values['columns'] > 0 ) {
                            foreach ($values['columns'] as $key => $value) {
                                ?>
                                <div>
                                    <input type="text" name="<?php echo $column_names; ?>[]" value="<?php echo $value; ?>">

                                    <?php self::remove_button(); ?>
                                </div>
                                <?php
                            }
                        } else {
                        ?>
                            <div>
                                <input type="text" name="<?php echo $column_names; ?>[]" value="">

                                <?php self::remove_button(); ?>
                            </div>
                        <?php
                        }
                    ?>
                    </div>
                </div> <!-- .edd-checkout-fields-rows -->
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function custom_html( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        $title_name = sprintf( '%s[%d][label]', self::$input_name, $field_id );
        $html_name = sprintf( '%s[%d][html]', self::$input_name, $field_id );

        $title_value = $values ? esc_attr( $values['label'] ) : '';
        $html_value = $values ? esc_attr( $values['html'] ) : '';
        ?>
        <li class="custom-field custom_html">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'html' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'custom_html' ); ?>

            <div class="edd-checkout-fields-holder">
                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Title', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" title="Title of the section" name="<?php echo $title_name; ?>" value="<?php echo esc_attr( $title_value ); ?>" />
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'HTML Codes', 'edd_cfm' ); ?></label>
                    <textarea class="smallipopInput" title="Paste your HTML codes, WordPress shortcodes will also work here" name="<?php echo $html_name; ?>" rows="10"><?php echo esc_html( $html_value ); ?></textarea>
                </div>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function action_hook( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        $title_name = sprintf( '%s[%d][label]', self::$input_name, $field_id );
        $title_value = $values ? esc_attr( $values['label'] ) : '';
        ?>
        <li class="custom-field custom_html">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'action_hook' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'action_hook' ); ?>

            <div class="edd-checkout-fields-holder">
                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Hook Name', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <input type="text" class="smallipopInput" title="<?php _e( 'Name of the hook', 'edd_cfm' ); ?>" name="<?php echo $title_name; ?>" value="<?php echo esc_attr( $title_value ); ?>" />

                        <div class="description" style="margin-top: 8px;">
                            <?php _e( "This is for developers to add dynamic elements as they want. It provides the chance to add whatever input type you want to add in this form.", 'edd_cfm' ); ?>
                            <?php _e( 'You can bind your own functions to render the form to this action hook. You\'ll be given 3 parameters to play with: $form_id, $post_id, $form_settings.', 'edd_cfm' ); ?>
<pre>
add_action('HOOK_NAME', 'your_function_name', 10, 3 );
function your_function_name( $form_id, $post_id, $form_settings ) {
    // do whatever you want
}
</pre>
                        </div>
                    </div> <!-- .edd-checkout-fields-rows -->
                </div>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function date_field( $field_id, $label, $values = array(), $removeable = true, $reqtoggle = true  ) {
        $format_name = sprintf('%s[%d][format]', self::$input_name, $field_id);
        $time_name = sprintf('%s[%d][time]', self::$input_name, $field_id);

        $format_value = $values ? $values['format'] : 'dd/mm/yy';
        $time_value = $values ? $values['time'] : 'no';

        $help = esc_attr( __( 'The date format', 'edd_cfm' ) );
        ?>
        <li class="custom-field custom_image">
            <?php self::legend( $label, $values, $removeable ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'date' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'date_field' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, '', true, $values, $reqtoggle ); ?>

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Date Format', 'edd_cfm' ); ?></label>
                    <input type="text" class="smallipopInput" name="<?php echo $format_name; ?>" value="<?php echo $format_value; ?>" title="<?php echo $help; ?>">
                </div> <!-- .edd-checkout-fields-rows -->

                <div class="edd-checkout-fields-rows">
                    <label><?php _e( 'Time', 'edd_cfm' ); ?></label>

                    <div class="edd-checkout-fields-sub-fields">
                        <label>
                            <?php self::hidden_field( "[$field_id][time]", 'no' ); ?>
                            <input type="checkbox" name="<?php echo $time_name ?>" value="yes"<?php checked( $time_value, 'yes' ); ?> />
                            <?php _e( 'Enable time input', 'edd_cfm' ); ?>
                        </label>
                    </div>
                </div> <!-- .edd-checkout-fields-rows -->
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function edd_first( $field_id, $label, $values = array() ) {
	    if(!isset($values['label']) || $values['label'] == ''){
			$values['label'] = $label;
		}
        ?>
        <li class="edd_first">
            <?php self::legend( $label, $values, false, true); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'text' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'edd_first' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, 'edd_first', false, $values, true ); ?>
                <?php self::common_text( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }

    public static function edd_last( $field_id, $label, $values = array() ) {
		if(!isset($values['label']) || $values['label'] == ''){
			$values['label'] = $label;
		}
        ?>
        <li class="edd_last">
            <?php self::legend( $label, $values, true, true ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'text' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'edd_last' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, 'edd_last', false, $values, true, false ); ?>
                <?php self::common_text( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }
	 public static function user_email( $field_id, $label, $values = array() ) {
		 CFM_Admin_Template::edd_email( $field_id, $label, $values = array());
	 }

    public static function edd_email( $field_id, $label, $values = array() ) {
		if(!isset($values['label']) || $values['label'] == ''){
			$values['label'] = $label;
		}
        ?>
        <li class="edd_email">
            <?php self::legend( $label, $values, false, true ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'email' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'edd_email' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, 'edd_email', false, $values, false); ?>
                <?php self::common_text( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }


    public static function description( $field_id, $label, $values = array() ) {	    
		if(!isset($values['label']) || $values['label'] == ''){
			$values['label'] = $label;
		}
        ?>
        <li class="user_bio">
            <?php self::legend( $label, $values ); ?>
            <?php self::hidden_field( "[$field_id][input_type]", 'textarea' ); ?>
            <?php self::hidden_field( "[$field_id][template]", 'description' ); ?>

            <div class="edd-checkout-fields-holder">
                <?php self::common( $field_id, 'description', false, $values ); ?>
                <?php self::common_textarea( $field_id, $values ); ?>
            </div> <!-- .edd-checkout-fields-holder -->
        </li>
        <?php
    }
}
