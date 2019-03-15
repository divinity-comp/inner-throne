<?php
/**
 * User Page Columns
 *
 * Functions for adding extra columns to the Users > All Users table.
 *
 * @package     Restrict Content Pro
 * @subpackage  User Page Columns
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Add user columns for Subscription, Status, and Actions
 *
 * @param array $columns
 *
 * @return array
 */
function rcp_add_user_columns( $columns ) {
	$columns['rcp_subscription'] 	= __( 'Membership', 'rcp' );
    $columns['rcp_status'] 			= __( 'Status', 'rcp' );
	$columns['rcp_links'] 			= __( 'Actions', 'rcp' );
    return $columns;
}
add_filter( 'manage_users_columns', 'rcp_add_user_columns' );

/**
 * Display user column values
 *
 * @param string $value       Column value.
 * @param string $column_name Name of the current column.
 * @param int    $user_id     ID of the user.
 *
 * @return string
 */
function rcp_show_user_columns( $value, $column_name, $user_id ) {
	$customer   = rcp_get_customer_by_user_id( $user_id );
	$membership = is_object( $customer ) ? rcp_get_customer_single_membership( $customer->get_id() ) : false;

	if ( 'rcp_status' == $column_name ) {
		$status = '&ndash;';

		if ( ! empty( $membership ) ) {
			$status = $membership->get_status();
		}

		return $status;
	}

	if ( 'rcp_subscription' == $column_name ) {
		$membership_level = '&ndash;';

		if ( ! empty( $membership ) ) {
			$membership_level = $membership->get_membership_level_name();
		}

		return $membership_level;
	}

	if ( 'rcp_links' == $column_name ) {
		$page  = rcp_get_memberships_admin_page();
		$label = __( 'Add Membership', 'rcp' );

		if ( ! empty( $membership ) ) {
			$page = add_query_arg( array(
				'membership_id' => $membership->get_id(),
				'view'          => 'edit'
			), $page );

			$label = __( 'Edit Membership', 'rcp' );
		} else {
			$user = get_userdata( $user_id );

			$page = add_query_arg( array(
				'view' => 'add',
				'email' => urlencode( $user->user_email )
			), $page );
		}

		$links = '<a href="' . esc_url( $page ) . '">' . $label . '</a>';

		return $links;
	}
	return $value;
}
add_filter( 'manage_users_custom_column',  'rcp_show_user_columns', 100, 3 );
