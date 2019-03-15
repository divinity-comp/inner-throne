<?php

/**
 * Handles form generaton and posting for add/edit post in frontend
 */
class CFM_Render_Form {

    static $meta_key = 'edd-checkout-fields';
    static $separator = '| ';
    static $config_id = '_edd-checkout-fields_id';

    /**
     * Send json error message
     *
     * @param string $error
     */
    function send_error( $error ) {
        echo json_encode( array(
            'success' => false,
            'error' => $error
        ) );

        die();
    }

    /**
     * Search on multi dimentional array
     *
     * @param array $array
     * @param string $key name of key
     * @param string $value the value to search
     * @return array
     */
    function search( $array, $key, $value ) {
        $results = array();

        if ( is_array( $array ) ) {
            if ( isset( $array[$key] ) && $array[$key] == $value )
                $results[] = $array;

            foreach ($array as $subarray)
                $results = array_merge( $results, $this->search( $subarray, $key, $value ) );
        }

        return $results;
    }


    /**
     * Get input meta fields separated as post vars, taxonomy and meta vars
     *
     * @param int $form_id form id
     * @return array
     */
    public static function get_input_fields( $form_id ) {
        $form_vars = get_post_meta( $form_id, self::$meta_key, true );

        $ignore_lists = array('section_break', 'html');
        $post_vars = $meta_vars = $taxonomy_vars = array();
		if($form_vars == null){
			return array(array(),array(),array());
		}
        foreach ($form_vars as $key => $value) {

            // ignore section break and HTML input type
            if ( in_array( $value['input_type'], $ignore_lists ) ) {
                continue;
            }

            //separate the post and custom fields
            if ( isset( $value['is_meta'] ) && $value['is_meta'] == 'yes' ) {
                $meta_vars[] = $value;
                continue;
            }

            $post_vars[] = $value;
        }

        return array($post_vars, $taxonomy_vars, $meta_vars);
    }

    public static function prepare_meta_fields( $meta_vars ) {
        // loop through custom fields
        // skip files, put in a key => value paired array for later executation
        // process repeatable fields separately
        // if the input is array type, implode with separator in a field

        $files = array();
        $meta_key_value = array();
        $multi_repeated = array(); //multi repeated fields will in sotre duplicated meta key

        foreach ($meta_vars as $key => $value) {

            // put files in a separate array, we'll process it later
            if ( ($value['input_type'] == 'file_upload') || ($value['input_type'] == 'image_upload') ) {
                $files[] = array(
                    'name' => $value['name'],
                    'value' => isset( $_POST['cfm_files'][$value['name']] ) ? $_POST['cfm_files'][$value['name']] : array()
                );

                // process repeatable fiels
            } elseif ( $value['input_type'] == 'repeat' ) {

                // if it is a multi column repeat field
                if ( isset( $value['multiple'] ) ) {

                    // if there's any items in the array, process it
                    if ( $_POST[$value['name']] ) {

                        $ref_arr = array();
                        $cols = count( $value['columns'] );
                        $first = array_shift( array_values( $_POST[$value['name']] ) ); //first element
                        $rows = count( $first );

                        // loop through columns
                        for ($i = 0; $i < $rows; $i++) {

                            // loop through the rows and store in a temp array
                            $temp = array();
                            for ($j = 0; $j < $cols; $j++) {

                                $temp[] = $_POST[$value['name']][$j][$i];
                            }

                            // store all fields in a row with self::$separator separated
                            $ref_arr[] = implode( self::$separator, $temp );
                        }

                        // now, if we found anything in $ref_arr, store to $multi_repeated
                        if ( $ref_arr ) {
                            $multi_repeated[$value['name']] = array_slice( $ref_arr, 0, $rows );
                        }
                    }
                } else {
                    $meta_key_value[$value['name']] = implode( self::$separator, $_POST[$value['name']] );
                }

                // process other fields
            } elseif( ! empty( $_POST[ $value['name'] ] ) ) {

                // if it's an array, implode with this->separator
                if ( is_array( $_POST[$value['name']] ) ) {
                    $meta_key_value[$value['name']] = implode( self::$separator, $_POST[$value['name']] );
                } else {
                    $meta_key_value[$value['name']] = trim( $_POST[$value['name']] );
                }
            }
        } //end foreach

        return array($meta_key_value, $multi_repeated, $files);
    }

    /**
     * Handles the add post shortcode
     *
     * @param $atts
     */
    function render_form( $form_id, $post_id = NULL, $preview = false ) {

    	global $user_ID;

        $form_vars = get_post_meta( $form_id, self::$meta_key, true );
        $form_settings = get_post_meta( $form_id, 'edd-checkout-fields_settings', true );

        if ( $form_vars ) {
            ?>
                <fieldset id="edd_checkout_user_info">

                    <?php
                    if ( !$post_id ) {
                        do_action( 'cfm_add_post_form_top', $form_id, $form_settings );
                    } else {
                        do_action( 'cfm_edit_post_form_top', $form_id, $post_id, $form_settings );
                    }

                    $this->render_items( $form_vars, $post_id, 'post', $form_id, $form_settings );
                    do_action( 'edd_purchase_form_user_info' ); 		
		    do_action( 'edd_purchase_form_user_info_fields' ); 

                    if ( !$post_id ) {
                        do_action( 'cfm_add_post_form_bottom', $form_id, $form_settings );
                    } else {
                        do_action( 'cfm_edit_post_form_bottom', $form_id, $post_id, $form_settings );
                    }
                    ?>

                </fieldset>
            <?php
        } //endif
    }

    function render_item_before( $form_field, $post_id ) {
        $label_exclude = array('section_break', 'html', 'action_hook', 'toc');
        $el_name = !empty( $form_field['name'] ) ? $form_field['name'] : '';
        $class_name = !empty( $form_field['css'] ) ? ' ' . $form_field['css'] : '';
		
        printf( '<p id="%s_wrap" class="cfm-el %s">', $el_name, $class_name );

        if ( isset( $form_field['input_type'] ) && !in_array( $form_field['input_type'], $label_exclude ) ) {
            $this->label( $form_field, $post_id );
        }
    }

    function render_item_after( $form_field ) {
        echo '</p>';
    }

    /**
     * Render form items
     *
     * @param array $form_vars
     * @param int|null $post_id
     * @param string $type type of the form. post or user
     */
    function render_items( $form_vars, $post_id, $type = 'post', $form_id, $form_settings ) {
        $hidden_fields = array();

        foreach ($form_vars as $key => $form_field) {

            // ignore the hidden fields
            if ( $form_field['input_type'] == 'hidden' ) {
                $hidden_fields[] = $form_field;
                continue;
            }

            $this->render_item_before( $form_field, $post_id );

            switch ($form_field['input_type']) {
                case 'text':
                    $this->text( $form_field, $post_id, $type );
                    break;

                case 'textarea':
                    $this->textarea( $form_field, $post_id, $type );
                    break;

                case 'select':
                    $this->select( $form_field, false, $post_id, $type );
                    break;

                case 'multiselect':
                    $this->select( $form_field, true, $post_id, $type );
                    break;

                case 'radio':
                    $this->radio( $form_field, $post_id, $type );
                    break;

                case 'checkbox':
                    $this->checkbox( $form_field, $post_id, $type );
                    break;

                case 'file_upload':
                    $this->file_upload( $form_field, $post_id, $type );
                    break;

                case 'url':
                    $this->url( $form_field, $post_id, $type );
                    break;

                case 'email':
                    $this->email( $form_field, $post_id, $type );
                    break;

                case 'repeat':
                    $this->repeat( $form_field, $post_id, $type );
                    break;

                case 'html':
                    $this->html( $form_field );
                    break;

                case 'action_hook':
                    $this->action_hook( $form_field, $form_id, $post_id, $form_settings );
                    break;

                case 'date':
                    $this->date( $form_field, $post_id, $type );
                    break;
            }

            $this->render_item_after( $form_field );
        } //end foreach

        if( $hidden_fields ) {
            foreach($hidden_fields as $field) {
                printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $field['name'] ), esc_attr( $field['meta_value'] ) );
                echo "\r\n";
            }
        }
    }

    /**
     * Prints required field asterisk
     *
     * @param array $attr
     * @return string
     */
    function required_mark( $attr ) {
        if ( isset( $attr['required'] ) && $attr['required'] == 'yes' ) {
            return ' <span class="edd-required-indicator">*</span>';
        }
    }

    /**
     * Prints HTML5 required attribute
     *
     * @param array $attr
     * @return string
     */
    function required_html5( $attr ) {
        if ( isset( $attr['required'] ) && $attr['required'] == 'yes' ) {
            echo ' required="required"';
        }
    }

    /**
     * Print required class name
     *
     * @param array $attr
     * @return string
     */
    function required_class( $attr ) {
        return;
        if ( isset( $attr['required'] ) && $attr['required'] == 'yes' ) {
            echo ' required';
        }
    }

    /**
     * Prints form input label
     *
     * @param string $attr
     */
    function label( $attr, $post_id = 0 ) {
        ?>
            <label class="edd-label" for="cfm-<?php echo isset( $attr['name'] ) ? $attr['name'] : 'cls'; ?>"><?php echo $attr['label'] . $this->required_mark( $attr ); ?></label>
        <?php
    }

    /**
     * Check if its a meta field
     *
     * @param array $attr
     * @return boolean
     */
    function is_meta( $attr ) {
        if ( isset( $attr['is_meta'] ) && $attr['is_meta'] == 'yes' ) {
            return true;
        }

        return false;
    }

    /**
     * Get a meta value
     *
     * @param int $object_id user_ID or post_ID
     * @param string $meta_key
     * @param string $type post or user
     * @param bool $single
     * @return string
     */
    function get_meta( $object_id, $meta_key, $type = 'post', $single = true ) {
        if ( !$object_id ) {
            return '';
        }

        if ( $type == 'post' ) {
            return get_post_meta( $object_id, $meta_key, $single );
        }

        return get_user_meta( $object_id, $meta_key, $single );
    }

    function get_user_data( $user_id, $field ) {
        return get_user_by( 'id', $user_id )->$field;
    }

    /**
     * Prints a text field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function text( $attr, $post_id, $type = 'post' ) {
        // checking for user profile username
        $username = false;
        $taxonomy = false;
        if ( $post_id ) {

            if ( $this->is_meta( $attr ) ) {
                $value = $this->get_meta( $post_id, $attr['name'], $type );
            } else {
                // applicable for post tags
                if ( $type == 'post' && $attr['name'] == 'tags' ) {
                    $post_tags = wp_get_post_tags( $post_id );
                    $tagsarray = array();
                    foreach ($post_tags as $tag) {
                        $tagsarray[] = $tag->name;
                    }

                    $value = implode( ', ', $tagsarray );
                    $taxonomy = true;
                } elseif ( $type == 'post' ) {
                    $value = get_post_field( $attr['name'], $post_id );
                } elseif ( $type == 'user' ) {
                    $value = get_user_by( 'id', $post_id )->$attr['name'];

                    if ( $attr['name'] == 'user_login' ) {
                        $username = true;
                    }
                }
            }
        } else {
            $value = $attr['default'];
        }
		if ($attr['name'] == 'edd_first'){
			if ( is_user_logged_in() ){
				$user_data = get_userdata( get_current_user_id() );
				$value = $user_data->first_name;
			}
		}
		if ($attr['name'] == 'edd_last'){
			if ( is_user_logged_in() ){
				$user_data = get_userdata( get_current_user_id() );
				$value = $user_data->last_name;
			}
		}
        ?>
			<span class="edd-description"><?php echo $attr['help']; ?></span>
            <input class="textfield<?php echo $this->required_class( $attr ); ?>" id="<?php echo $attr['name']; ?>" type="text" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $value ) ?>" size="<?php echo esc_attr( $attr['size'] ) ?>" <?php echo $username ? 'disabled' : ''; ?> />

            <?php if ( $taxonomy ) { ?>
            <script type="text/javascript">
                jQuery(function($) {
                    $('fieldset.tags input[name=tags]').suggest( ajaxurl + '?action=ajax-tag-search&tax=post_tag', { delay: 500, minchars: 2, multiple: true, multipleSep: ', ' } );
                });
            </script>
            <?php } ?>

        <?php
    }

    /**
     * Prints a textarea field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function textarea( $attr, $post_id, $type ) {
        $req_class = ( isset( $attr['required'] ) && $attr['required'] == 'yes' ) ? 'required' : 'rich-editor';

        if ( $post_id ) {
            if ( $this->is_meta( $attr ) ) {
                $value = $this->get_meta( $post_id, $attr['name'], $type, true );
            } else {

                if ( $type == 'post' ) {
                    $value = get_post_field( $attr['name'], $post_id );
                } else {
                    $value = $this->get_user_data( $post_id, $attr['name'] );
                }
            }
        } else {
            $value = $attr['default'];
        }
        ?>
            <span class="edd-description"><?php echo $attr['help']; ?></span>
            <?php if ( isset( $attr['insert_image'] ) && $attr['insert_image'] == 'yes' ) { ?>
                <div id="cfm-insert-image-container">
                    <a class="cfm-button" id="cfm-insert-image" href="#">
                        <span class="cfm-media-icon"></span>
                        <?php _e( 'Insert Photo', 'edd_cfm' ); ?>
                    </a>
                </div>
            <?php } ?>

            <?php

            $rich = isset( $attr['rich'] ) ? $attr['rich'] : '';

            if ( $rich == 'yes' ) {

                printf( '<span class="cfm-rich-validation" data-required="%s" data-type="rich" data-id="%s"></span>', $attr['required'], $attr['name'] );
                wp_editor( $value, $attr['name'], array('editor_height' => $attr['rows'], 'quicktags' => false, 'media_buttons' => false, 'editor_class' => $req_class) );

            } elseif( $rich == 'teeny' ) {

                printf( '<span class="cfm-rich-validation" data-required="%s" data-type="rich" data-id="%s"></span>', $attr['required'], $attr['name'] );
                wp_editor( $value, $attr['name'], array('editor_height' => $attr['rows'], 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => $req_class) );
            } else {
                ?>
                <textarea class="textareafield<?php echo $this->required_class( $attr ); ?>" id="<?php echo $attr['name']; ?>" name="<?php echo $attr['name']; ?>" data-required="<?php echo $attr['required'] ?>" data-type="textarea"<?php $this->required_html5( $attr ); ?> placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" rows="<?php echo $attr['rows']; ?>" cols="<?php echo $attr['cols']; ?>"><?php echo esc_textarea( $value ) ?></textarea>
            <?php } ?>


        <?php
    }

    /**
     * Prints a file upload field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function file_upload( $attr, $post_id, $type ) {



        $allowed_ext = '';
        $extensions = cfm_allowed_extensions();
        if ( is_array( $attr['extension'] ) ) {
            foreach ($attr['extension'] as $ext) {
                $allowed_ext .= $extensions[$ext]['ext'] . ',';
            }
        } else {
            $allowed_ext = '*';
        }

        $uploaded_items = $post_id ? $this->get_meta( $post_id, $attr['name'], $type, false ) : array();
        ?>

            <span class="edd-description"><?php echo $attr['help']; ?></span>
            <div id="cfm-<?php echo $attr['name']; ?>-upload-container">
                <div class="cfm-attachment-upload-filelist">
                    <a id="cfm-<?php echo $attr['name']; ?>-pickfiles" class="button file-selector" href="#"><?php _e( 'Select File(s)', 'edd_cfm' ); ?></a>

                    <?php printf( '<span class="cfm-file-validation" data-required="%s" data-type="file"></span>', $attr['required'] ); ?>

                    <ul class="cfm-attachment-list thumbnails">
                        <?php
                        if ( $uploaded_items ) {
                            foreach ($uploaded_items as $attach_id) {
                                echo EDD_CFM()->upload->attach_html( $attach_id, $attr['name'] );

                                if ( is_admin() ) {
                                    printf( '<a href="%s">%s</a>', wp_get_attachment_url( $attach_id ), __( 'Download File', 'edd_cfm' ) );
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div><!-- .container -->


        <script type="text/javascript">
            jQuery(function($) {
                new CFM_Uploader('cfm-<?php echo $attr['name']; ?>-pickfiles', 'cfm-<?php echo $attr['name']; ?>-upload-container', <?php echo $attr['count']; ?>, '<?php echo $attr['name']; ?>', '<?php echo $allowed_ext; ?>', <?php echo $attr['max_size'] ?>);
            });
        </script>
        <?php
    }

    /**
     * Prints a select or multiselect field
     *
     * @param array $attr
     * @param bool $multiselect
     * @param int|null $post_id
     */
    function select( $attr, $multiselect = false, $post_id, $type ) {
        if ( $post_id ) {
            $selected = $this->get_meta( $post_id, $attr['name'], $type );
            $selected = $multiselect ? explode( self::$separator, $selected ) : $selected;
        } else {
            $selected = isset( $attr['selected'] ) ? $attr['selected'] : '';
            $selected = $multiselect ? ( is_array( $selected ) ? $selected : array() ) : $selected;
        }

        $multi = $multiselect ? ' multiple="multiple" style="width: 220px" ' : '';
        $data_type = $multiselect ? 'multiselect' : 'select';
        $css = $multiselect ? ' class="multiselect"' : '';
		?>
            <span class="edd-description"><?php echo $attr['help']; ?></span>
            <select<?php echo $css; ?> name="<?php echo $attr['name'] ?>[]"<?php echo $multi; ?> data-required="<?php echo $attr['required'] ?>" data-type="<?php echo $data_type; ?>"<?php $this->required_html5( $attr ); ?>>

                <?php if ( !empty( $attr['first'] ) ) { ?>
                    <option value=""><?php echo $attr['first']; ?></option>
                <?php } ?>

                <?php
                if ( $attr['options'] && count( $attr['options'] ) > 0 ) {
                    foreach ($attr['options'] as $option) {
                        $current_select = $multiselect ? selected( in_array( $option, $selected ), true, false ) : selected( $selected, $option, false );
                        ?>
                        <option value="<?php echo esc_attr( $option ); ?>"<?php echo $current_select; ?>><?php echo $option; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        <?php
    }

    /**
     * Prints a radio field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function radio( $attr, $post_id, $type ) {
        $selected = isset( $attr['selected'] ) ? $attr['selected'] : '';

        if ( $post_id ) {
            $selected = $this->get_meta( $post_id, $attr['name'], $type, true );
        }
        ?>

            <span data-required="<?php echo $attr['required'] ?>" data-type="radio"></span>

            <span class="edd-description"><?php echo $attr['help']; ?></span>
            <?php
            if ( $attr['options'] && count( $attr['options'] ) > 0 ) {
                foreach ($attr['options'] as $option) {
                    ?>

                    <label>
                        <input name="<?php echo $attr['name']; ?>" type="radio" value="<?php echo esc_attr( $option ); ?>"<?php checked( $selected, $option ); ?> />
                        <?php echo $option; ?>
                    </label>
                    <?php
                }
            }
            ?>

        <?php
    }

    /**
     * Prints a checkbox field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function checkbox( $attr, $post_id, $type ) {
        $selected = isset( $attr['selected'] ) ? $attr['selected'] : array();

        if ( $post_id ) {
            $selected = explode( self::$separator, $this->get_meta( $post_id, $attr['name'], $type, true ) );
        }
        ?>
            <span data-required="<?php echo $attr['required'] ?>" data-type="checkbox"></span>
            <span class="edd-description"><?php echo $attr['help']; ?></span>
            <span class="cfm-fields">
            <?php
            if ( $attr['options'] && count( $attr['options'] ) > 0 ) {
                foreach ($attr['options'] as $option) {
                    ?>

                    <label>
                        <input type="checkbox" name="<?php echo $attr['name']; ?>[]" value="<?php echo esc_attr( $option ); ?>"<?php echo in_array( $option, $selected ) ? ' checked="checked"' : ''; ?> />
                        <?php echo $option; ?>
                    </label>
                    <?php
                }
            }
            ?>
            </span>


        <?php
    }

    /**
     * Prints a url field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function url( $attr, $post_id, $type ) {

        if ( $post_id ) {
            if ( $this->is_meta( $attr ) ) {
                $value = $this->get_meta( $post_id, $attr['name'], $type, true );
            } else {
                //must be user profile url
                $value = $this->get_user_data( $post_id, $attr['name'] );
            }
        } else {
            $value = $attr['default'];
        }
        ?>
            <span class="edd-description"><?php echo $attr['help']; ?></span>
            <input id="cfm-<?php echo $attr['name']; ?>" type="url" class="url" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $value ) ?>" size="<?php echo esc_attr( $attr['size'] ) ?>" />

        <?php
    }

    /**
     * Prints a email field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function email( $attr, $post_id, $type = 'post' ) {
    	do_action( 'edd_purchase_form_before_email' );
		if ( is_user_logged_in() ){
				$user_data = get_userdata( get_current_user_id() );
				$value = $user_data->user_email;
		}
		else{
			$value = $attr['default'];
		}
        ?> <span class="edd-description"><?php echo $attr['help']; ?></span>
            <input id="edd_email" type="email" class="edd_email" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="edd_email" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $value ) ?>" size="<?php echo esc_attr( $attr['size'] ) ?>" />

        <?php
        do_action( 'edd_purchase_form_after_email' );
    }
    /**
     * Prints a repeatable field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function repeat( $attr, $post_id, $type ) {
        $add = cfm_assets_url .'img/add.png';
        $remove = cfm_assets_url. 'img/remove.png';
        ?>
            <span class="edd-description"><?php echo $attr['help']; ?></span>
            <?php if ( isset( $attr['multiple'] ) ) { ?>
                <table>
                    <thead>
                        <tr>
                            <?php
                            $num_columns = count( $attr['columns'] );
                            foreach ($attr['columns'] as $column) {
                                ?>
                                <th>
                                    <?php echo $column; ?>
                                </th>
                            <?php } ?>

                            <th style="visibility: hidden;">
                                Actions
                            </th>
                        </tr>

                    </thead>
                    <tbody>

                        <?php
                        $items = $post_id ? $this->get_meta( $post_id, $attr['name'], $type, false ) : array();

                        if ( $items ) {
                            foreach ($items as $item_val) {
                                $column_vals = explode( self::$separator, $item_val );
                                ?>

                                <tr>
                                    <?php for ($count = 0; $count < $num_columns; $count++) { ?>
                                        <td>
                                            <input type="text" name="<?php echo $attr['name'] . '[' . $count . ']'; ?>[]" value="<?php echo esc_attr( $column_vals[$count] ); ?>" size="<?php echo esc_attr( $attr['size'] ) ?>" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> />
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <img class="cfm-clone-field" alt="<?php esc_attr_e( 'Add another', 'edd_cfm' ); ?>" title="<?php esc_attr_e( 'Add another', 'edd_cfm' ); ?>" src="<?php echo $add; ?>">
                                        <img class="cfm-remove-field" alt="<?php esc_attr_e( 'Remove this choice', 'edd_cfm' ); ?>" title="<?php esc_attr_e( 'Remove this choice', 'edd_cfm' ); ?>" src="<?php echo $remove; ?>">
                                    </td>
                                </tr>

                            <?php } //endforeach   ?>

                        <?php } else { ?>

                            <tr>
                                <?php for ($count = 0; $count < $num_columns; $count++) { ?>
                                    <td>
                                        <input type="text" name="<?php echo $attr['name'] . '[' . $count . ']'; ?>[]" size="<?php echo esc_attr( $attr['size'] ) ?>" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> />
                                    </td>
                                <?php } ?>
                                <td>
                                    <img class="cfm-clone-field" alt="<?php esc_attr_e( 'Add another', 'edd_cfm' ); ?>" title="<?php esc_attr_e( 'Add another', 'edd_cfm' ); ?>" src="<?php echo $add; ?>">
                                    <img class="cfm-remove-field" alt="<?php esc_attr_e( 'Remove this choice', 'edd_cfm' ); ?>" title="<?php esc_attr_e( 'Remove this choice', 'edd_cfm' ); ?>" src="<?php echo $remove; ?>">
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>

            <?php } else { ?>


                <table>
                    <?php
                    $items = $post_id ? explode( self::$separator, $this->get_meta( $post_id, $attr['name'], $type, true ) ) : array();

                    if ( $items ) {
                        foreach ($items as $item) {
                            ?>
                            <tr>
                                <td>
                                    <input id="cfm-<?php echo $attr['name']; ?>" type="text" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>[]" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $item ) ?>" size="<?php echo esc_attr( $attr['size'] ) ?>" />
                                </td>
                                <td>
                                    <img style="cursor:pointer; margin:0 3px;" alt="add another choice" title="add another choice" class="cfm-clone-field" src="<?php echo $add; ?>">
                                    <img style="cursor:pointer;" class="cfm-remove-field" alt="remove this choice" title="remove this choice" src="<?php echo $remove; ?>">
                                </td>
                            </tr>
                        <?php } //endforeach    ?>
                    <?php } else { ?>

                        <tr>
                            <td>
                                <input id="cfm-<?php echo $attr['name']; ?>" type="text" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>[]" placeholder="<?php echo esc_attr( $attr['placeholder'] ); ?>" value="<?php echo esc_attr( $attr['default'] ) ?>" size="<?php echo esc_attr( $attr['size'] ) ?>" />
                            </td>
                            <td>
                                <img style="cursor:pointer; margin:0 3px;" alt="add another choice" title="add another choice" class="cfm-clone-field" src="<?php echo $add; ?>">
                                <img style="cursor:pointer;" class="cfm-remove-field" alt="remove this choice" title="remove this choice" src="<?php echo $remove; ?>">
                            </td>
                        </tr>

                    <?php } ?>

                </table>
            <?php } ?>

        <?php
    }

    /**
     * Prints a HTML field
     *
     * @param array $attr
     */
    function html( $attr ) {
        ?>
            <?php echo do_shortcode( $attr['html'] ); ?>
        <?php
    }

    /**
     * Prints a action hook
     *
     * @param array $attr
     * @param int $form_id
     * @param int|null $post_id
     * @param array $form_settings
     */
    function action_hook( $attr, $form_id, $post_id, $form_settings ) {

        if ( !empty( $attr['label'] ) ) {
            do_action( $attr['label'], $form_id, $post_id, $form_settings );
        }
    }

    /**
     * Prints a date field
     *
     * @param array $attr
     * @param int|null $post_id
     */
    function date( $attr, $post_id, $type ) {

        $value = $post_id ? $this->get_meta( $post_id, $attr['name'], $type, true ) : '';
        ?>
			<span class="edd-description"><?php echo $attr['help']; ?></span>
            <input id="cfm-date-<?php echo $attr['name']; ?>" type="text" class="datepicker" data-required="<?php echo $attr['required'] ?>" data-type="text"<?php $this->required_html5( $attr ); ?> name="<?php echo esc_attr( $attr['name'] ); ?>" value="<?php echo esc_attr( $value ) ?>" size="30" />
        <script type="text/javascript">
            jQuery(function($) {
        <?php if ( $attr['time'] == 'yes' ) { ?>
                                $("#cfm-date-<?php echo $attr['name']; ?>").datetimepicker({ dateFormat: '<?php echo $attr["format"]; ?>' });
        <?php } else { ?>
                                $("#cfm-date-<?php echo $attr['name']; ?>").datepicker({ dateFormat: '<?php echo $attr["format"]; ?>' });
        <?php } ?>
            });
        </script>

        <?php
    }
}
new CFM_Render_Form();
