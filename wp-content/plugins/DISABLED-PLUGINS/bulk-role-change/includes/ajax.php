<?php

add_action( 'wp_ajax_bulk_get_num_users', 'bulk_get_num_users_callback' );
function bulk_get_num_users_callback() {
	global $wpdb; // this is how you get access to the database
	$role 	= trim( $_POST['role'] );
	$result = count_users();
	$show 	= array($role);
	foreach ($result['avail_roles'] as $role => $count) {
		if ( in_array($role, $show)) {
			echo $count;
		}
	}
	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_bulk_change_role', 'bulk_change_role_callback' );
function bulk_change_role_callback() {
	global $wpdb; // this is how you get access to the database
	$current_role 	= trim( $_POST['current_role'] );
	$new_role 		= trim( $_POST['new_role'] );
	$users_by_role 	= get_users( array( 'role' => $current_role ) ); // https://codex.wordpress.org/Function_Reference/get_users
	$output = null;
	foreach ( $users_by_role as $user ) {
		$output .= esc_html( $user->ID ) . ',';
        $u = new WP_User( $user->ID );
        $u->remove_role( $current_role );
        $u->add_role( $new_role );
	}
	echo $output;
	wp_die(); // this is required to terminate immediately and return a proper response
}

?>