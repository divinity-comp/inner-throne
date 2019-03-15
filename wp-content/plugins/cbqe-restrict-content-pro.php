<?php
/**
 * Restrict Content Pro for Custom Bulk/Quick Edit plugin
 *
 * In WordPress Admin > Settings > Custom Bulk/Quick, configure your fields as needed, per below. If configuration updates are needed, either manually edit them or remove the current field configuration and click Save Changes for automatic updates.
 *
 * Paid Only? - As checkbox
 * Show Excerpt? - As checkbox
 * Hide from Feed? - As checkbox
 * Access Level - As RCP Access Level
 * Subscription Level - As RCP Subscription Level
 * User Level - As RCP User Level
 *
 * @author Michael Cannon <mc@aihr.us>
 */


add_filter( 'manage_post_posts_columns', 'rcp_manage_post_posts_columns' );
// add_filter( 'manage_guitarlessons_posts_columns', 'rcp_manage_post_posts_columns' );
// add_filter( 'manage_weeklylessons_posts_columns', 'rcp_manage_post_posts_columns' );
// add_filter( 'manage_songlessons_posts_columns', 'rcp_manage_post_posts_columns' );
function rcp_manage_post_posts_columns( $columns ) {
	$columns['_is_paid'] = esc_html__( 'Paid Only?' );
	$columns['rcp_show_excerpt'] = esc_html__( 'Show Excerpt?' );
	$columns['rcp_hide_from_feed'] = esc_html__( 'Hide from Feed?' );
	$columns['rcp_access_level'] = esc_html__( 'Access Level' );
	$columns['rcp_subscription_level'] = esc_html__( 'Subscription Level' );
	$columns['rcp_user_level'] = esc_html__( 'User Level' );

	return $columns;
}

add_filter( 'cbqe_settings_as_types', 'rcp_settings_as_types' );
function rcp_settings_as_types( $as_types ) {
	$as_types['rcp-access-level'] = esc_html__( 'As RCP Access Level' );
	$as_types['rcp-subscription-level'] = esc_html__( 'As RCP Subscription Level' );
	$as_types['rcp-user-level'] = esc_html__( 'As RCP User Level' );

	return $as_types;
}


add_filter( 'cbqe_configuration_default', 'rcp_configuration_default', 10, 3 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function rcp_configuration_default( $default, $id, $type ) {
	switch ( $type ) {
	case 'rcp-access-level':
		$default = rcp_get_access_levels();
		$default = implode( "\n", $default );
		break;

	case 'rcp-subscription-level':
		$levels  = rcp_get_subscription_levels();
		$default = array();
		foreach( $levels as $level ) {
			$default[] = $level->id . '|' . $level->name;
		}
		$default = implode( "\n", $default );
		break;

	case 'rcp-user-level':
		$default = array(
			esc_html__( 'All' ),
			esc_html__( 'Administrator' ),
			esc_html__( 'Editor' ),
			esc_html__( 'Author' ),
			esc_html__( 'Contributor' ),
			esc_html__( 'Subscriber' ),
		);
		$default = implode( "\n", $default );
		break;
	}

	return $default;
}


add_filter( 'cbqe_quick_edit_custom_box_field', 'rcp_quick_edit_custom_box_field', 10, 5 );
 
 
/**
 *
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function rcp_quick_edit_custom_box_field( $input, $field_type, $field_name, $options, $bulk_mode ) {
	$column_name    = str_replace( Custom_Bulkquick_Edit::SLUG, '', $field_name );
	$field_name_var = str_replace( '-', '_', $field_name );
 
	$result = $input;
	switch ( $field_type ) {
	case 'rcp-access-level':
	case 'rcp-user-level':
		$result = Custom_Bulkquick_Edit::custom_box_select( $column_name, $field_name, $field_name_var, $options, $bulk_mode );
		break;

	case 'rcp-subscription-level':
		if ( ! $bulk_mode ) {
			$result = Custom_Bulkquick_Edit::custom_box_checkbox( $column_name, $field_name, $field_name_var, $options );
		} else {
			$result = Custom_Bulkquick_Edit::custom_box_select_multiple( $column_name, $field_name, $field_name_var, $options, $bulk_mode );
		}
		break;
	}
 
	return $result;
}
 
 
add_filter( 'cbqe_manage_posts_custom_column_field_type', 'rcp_manage_posts_custom_column_field_type', 10, 4 );

 
 
/**
 *
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function rcp_manage_posts_custom_column_field_type( $current, $field_type, $column, $post_id ) {
	global $post;
 
	$result = $current;
	switch ( $field_type ) {
	case 'rcp-access-level':
		$field   = 'rcp_access_level';
		$current = get_post_meta( $post_id, $field );
		$details = Custom_Bulkquick_Edit::get_field_config( $post->post_type, $column );
		$options = explode( "\n", $details );
		$result  = Custom_Bulkquick_Edit::column_select( $column, $current, $options, $field_type );
		break;

	case 'rcp-subscription-level':
		$field   = 'rcp_subscription_level';
		$current = get_post_meta( $post_id, $field, true );
		$details = Custom_Bulkquick_Edit::get_field_config( $post->post_type, $column );
		$options = explode( "\n", $details );

		$field_type = 'checkbox';

		$result = Custom_Bulkquick_Edit::column_checkbox_radio( $column, $current, $options, $field_type );
		break;

	case 'rcp-user-level':
		$field   = 'rcp_user_level';
		$current = get_post_meta( $post_id, $field );
		$details = Custom_Bulkquick_Edit::get_field_config( $post->post_type, $column );
		$options = explode( "\n", $details );
		$result  = Custom_Bulkquick_Edit::column_select( $column, $current, $options, $field_type );
		break;
	}
 
	return $result;
}


add_filter( 'cbqe_field_type_core', 'rcp_field_type_core' );
function rcp_field_type_core( $field_type ) {
	if ( 'rcp-subscription-level' == $field_type ) {
		return 'checkbox';
	}
}

?>