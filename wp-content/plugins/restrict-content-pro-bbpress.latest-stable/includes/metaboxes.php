<?php

// Add meta box
function rcp_bbp_add_meta_boxes() {
	add_meta_box( 'rcpb_forum_meta_box', __( 'Restrict this Forum', 'rcp_bbpress' ), 'rcp_bbp_render_meta_box', 'forum', 'side' );
	add_meta_box( 'rcpb_topic_meta_box', __( 'Restrict this Topic', 'rcp_bbpress' ), 'rcp_bbp_render_meta_box', 'topic', 'side' );
}
add_action( 'admin_menu', 'rcp_bbp_add_meta_boxes' );

function rcp_bbp_get_metabox_fields() {

	$fields = array(
		array(
			'name' => __( 'Paid Only?', 'rcp_bbpress' ),
			'id' => '_is_forum_paid',
			'type' => 'checkbox',
			'desc' => __( 'Restrict this forum to paid users only', 'rcp_bbpress' )
		),
		array(
			'name' => __( 'Access Level', 'rcp' ),
			'id'   => 'rcp_access_level',
			'type' => 'select',
			'desc' => __( 'Choose the access level required to see this content. The access level is determined by the subscription the member is subscribed to.', 'rcp' ),
			'options' => rcp_get_access_levels(),
			'std'  => 'All'
		),
		array(
			'name' => __( 'Subscription Level', 'rcp' ),
			'id'   => 'rcp_subscription_level',
			'type' => 'levels',
			'desc' => __( 'Choose the subscription levels allowed to view this content.', 'rcp' ),
			'std'  => 'All'
		)
	);

	return apply_filters( 'rcp_bbp_get_metabox_fields', $fields );

}

// Callback function to show fields in meta box
function rcp_bbp_render_meta_box() {

	global $post;

	echo '<input type="hidden" name="rcpb_meta_box" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';

	echo '<table class="form-table">';

	foreach ( rcp_bbp_get_metabox_fields() as $field ) {

		// get current post meta data
		$meta = get_post_meta( $post->ID, $field['id'], true );

		echo '<div>';
			echo '<p><strong>' . $field['name'] . '</strong></p>';

			switch ( $field['type'] ) {
				case 'checkbox':

					echo '<input type="checkbox" value="1" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '"' . checked( $meta, 1, false ) . '/>&nbsp;';
					break;

				case 'select':

					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					foreach ( $field['options'] as $option ) {
						echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
					echo '</select><br/>';
					break;

				case 'levels':

					$selected = is_array( $meta ) ? $meta : array( $meta );


					$levels = rcp_get_subscription_levels( 'all' );
					foreach ( $levels as $level ) {
						echo '<input type="checkbox" value="' . absint( $level->id ) . '"' . checked( true, in_array( $level->id, $selected ), false ) . ' name="' . esc_attr( $field['id'] ) . '[]" id="' . esc_attr( $field['id'] ) . '_' . absint( $level->id ) . '" />&nbsp;';
						echo '<label for="' . esc_attr( $field['id'] ) . '_' . absint( $level->id ) . '">' . $level->name . '</label><br/>';
					}
					break;
			}
			echo '<span class="description">' . $field['desc'] . '</div>';

		echo '</div>';
	}

	echo '</table>';
}

// Save data from meta box
function rcp_bbp_save_meta_data( $post_id ) {

	// verify nonce
	if ( ! isset( $_POST['rcpb_meta_box'] ) || ! wp_verify_nonce( $_POST['rcpb_meta_box'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// check permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
	
		return $post_id;
	
	}

	foreach ( rcp_bbp_get_metabox_fields() as $field ) {

		if ( isset( $_POST[ $field['id'] ] ) ) {

			$old  = get_post_meta( $post_id, $field['id'], true );
			$data = $_POST[ $field['id'] ];

			if ( ( $data || $data == 0 ) && $data != $old ) {
				
				update_post_meta( $post_id, $field['id'], $data );
			
			} elseif ( '' == $data && $old ) {
			
				delete_post_meta( $post_id, $field['id'], $old );
			
			}

		} else {

			delete_post_meta( $post_id, $field['id'] );
		
		}

	}

}
add_action( 'save_post', 'rcp_bbp_save_meta_data' );
