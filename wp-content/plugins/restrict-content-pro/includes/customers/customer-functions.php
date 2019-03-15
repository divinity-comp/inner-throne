<?php
/**
 * customer-functions.php
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2018, Restrict Content Pro team
 * @license   GPL2+
 * @since     3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a customer.
 *
 * @param int $customer_id ID of the customer to retrieve. 0 for current customer.
 *
 * @since 3.0
 * @return RCP_Customer|false Customer object or false on failure.
 */
function rcp_get_customer( $customer_id = 0 ) {

	// If no customer ID is provided, get the current customer.
	if ( empty( $customer_id ) ) {
		return rcp_get_customer_by_user_id( get_current_user_id() );
	}

	$customers = new \RCP\Database\Queries\Customer();

	return $customers->get_item( $customer_id );

}

/**
 * Get a customer by a field/value pair.
 *
 * @param string $field Column to search in.
 * @param string $value Value of the row.
 *
 * @since 3.0
 * @return RCP_Customer|false
 */
function rcp_get_customer_by( $field = '', $value = '' ) {

	$customers = new \RCP\Database\Queries\Customer();

	return $customers->get_item_by( $field, $value );

}

/**
 * Get a customer object given a user ID number.
 *
 * @param int $user_id User ID number. Leave blank to use the current logged in user.
 *
 * @since 3.0
 * @return RCP_Customer|false Customer object on success, false on failure.
 */
function rcp_get_customer_by_user_id( $user_id = 0 ) {

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return rcp_get_customer_by( 'user_id', $user_id );

}

/**
 * Get customers
 *
 * @param array $args Query arguments to override the defaults.
 *
 * @see   \RCP\Database\Queries\Customer::__construct() for accepted arguments.
 *
 * @since 3.0
 * @return array Array of `RCP_Customer` objects.
 */
function rcp_get_customers( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'number' => 20
	) );

	$customers = new \RCP\Database\Queries\Customer();

	return $customers->query( $args );

}

/**
 * Count the number of customers
 *
 * @param array $args
 *
 * @since 3.0
 * @return int
 */
function rcp_count_customers( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'count' => true
	) );

	$customers = new RCP\Database\Queries\Customer( $args );

	return absint( $customers->found_items );

}

/**
 * Query for and return array of customer counts, keyed by status.
 *
 * @param array $args Query arguments to override the defaults.
 *
 * @since 3.0
 * @return array
 */
function rcp_get_customer_counts( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'count'    => true,
		'disabled' => 0,
		'groupby'  => 'email_verification'
	) );

	$customers = new RCP\Database\Queries\Customer( $args );

	return rcp_format_counts( $customers, $args['groupby'] );

}

/**
 * Add a new customer.
 *
 * @param array $data               {
 *                                  Array of arguments.
 *
 * @type int    $user_id            Optional. ID of the corresponding user account.
 * @type array  $user_args          Optional. Array of arguments for `wp_insert_user()` if creating a new account. This
 *                                  is required if a user_id is not being passed in.
 * @type string $date_registered    Optional. Date this customer registered in MySQL format.
 * @type string $email_verification Optional. Email verification status: `none`, `pending`, `verified`.
 * @type string $last_login         Optional. Date this customer last logged into their user account.
 * @type array  $ips                Optional. Array of all known IP addresses for this customer.
 * @type string $notes              Optional. Customer notes.
 *                    }
 *
 * @since 3.0
 * @return int|false ID of the new customer, or false on failure.
 */
function rcp_add_customer( $data = array() ) {

	$data = wp_parse_args( $data, array(
		'date_registered' => current_time( 'mysql' )
	) );

	rcp_log( sprintf( 'Adding a new customer. Args: %s', var_export( $data, true ) ) );

	if ( ! empty( $data['user_id'] ) ) {
		$data['user_id'] = absint( $data['user_id'] );
	} else {
		// We need to create a new user.
		$user_args = ! empty( $dat['user_args'] ) ? $data['user_args'] : array();
		$user_id   = wp_insert_user( $user_args );

		if ( is_wp_error( $user_id ) ) {
			return false;
		}

		$data['user_id'] = absint( $user_id );
	}

	// User ID is required.
	if ( empty( $data['user_id'] ) ) {
		rcp_log( sprintf( 'Failed to add new customer - missing user ID. Args: %s', var_export( $data, true ) ) );

		return false;
	}

	// We cannot have two customer records tied to the same user ID.
	$existing_customer = rcp_get_customer_by_user_id( $data['user_id'] );
	if ( ! empty( $existing_customer ) ) {
		rcp_log( sprintf( 'Failed to add new customer - customer #%d already exists with user ID #%d. Args: %s', $existing_customer->get_id(), $data['user_id'], var_export( $data, true ) ) );

		return false;
	}

	// Maybe serialize IPs.
	if ( ! empty( $data['ips'] ) ) {
		$data['ips'] = maybe_serialize( $data['ips'] );
	}

	// Check email verification value against whitelist.
	if ( ! empty( $data['email_verification'] ) && ! in_array( $data['email_verification'], array( 'none', 'pending', 'verified' ) ) ) {
		unset( $data['email_verification'] );
	}

	$customers   = new \RCP\Database\Queries\Customer();
	$customer_id = $customers->add_item( $data );

	if ( $customer_id ) {
		rcp_log( sprintf( 'Created new customer #%d.', $customer_id ) );

		return $customer_id;
	}

	rcp_log( sprintf( 'Failed to add new customer. Args: %s', var_export( $data, true ) ) );

	return false;

}

/**
 * Update a customer record in the database.
 *
 * @param int   $customer_id        ID of the customer to update.
 * @param array $data               {
 *                                  Array of data to update.
 *
 * @type int    $user_id            Optional. ID of the corresponding user account.
 * @type string $date_registered    Optional. Date this customer registered.
 * @type string $email_verification Optional. Email verification status: `none`, `pending`, `verified`.
 * @type string $last_login         Optional. Date this customer last logged into their user account.
 * @type array  $ips                Optional. Array of all known IP addresses for this customer.
 * @type string $notes              Optional. Customer notes.
 *                    }
 *
 * @since 3.0
 * @return bool True on success, false on failure.
 */
function rcp_update_customer( $customer_id, $data = array() ) {

	$customer = rcp_get_customer( $customer_id );

	return $customer->update( $data );

}

/**
 * Delete a customer
 *
 * @param int $customer_id ID of the customer to delete.
 *
 * @since 3.0
 * @return bool True on success, false on failure.
 */
function rcp_delete_customer( $customer_id ) {

	rcp_log( sprintf( 'Beginning deletion for customer #%d.', $customer_id ) );

	$customer = rcp_get_customer( $customer_id );

	if ( empty( $customer ) ) {
		rcp_log( 'Invalid customer - exiting.' );

		return false;
	}

	// First disable all their memberships.
	$customer->disable_memberships();

	// Now delete them.

	$customers = new \RCP\Database\Queries\Customer();
	$success   = $customers->delete_item( $customer_id );

	if ( $success ) {
		rcp_log( sprintf( 'Successfully deleted customer #%d.', $customer_id ) );
	} else {
		rcp_log( sprintf( 'Error deleting customer #%d.', $customer_id ) );
	}

	return $success;

}

/**
 * Returns a single membership for a given customer. This is mostly used for backwards compatibility when multiple
 * memberships is introduced, but some functions still expect users to only have a single membership. By default this
 * returns the customer's very first membership.
 *
 * @param int $customer_id ID of the customer.
 *
 * @since 3.0
 * @return RCP_Membership|false
 */
function rcp_get_customer_single_membership( $customer_id ) {

	$membership = false;

	$args = array(
		'customer_id' => absint( $customer_id ),
		'number'      => 1,
		'orderby'     => 'id',
		'order'       => 'ASC'
	);

	/**
	 * Filters the query arguments used for getting the customer's membership. This can be used to change which membership
	 * is used as the "default", for example: to retrieve the most recently added membership instead of the first one.
	 *
	 * @param array $args        Query args.
	 * @param int   $customer_id ID of the customer.
	 *
	 * @since 3.0
	 */
	$args = apply_filters( 'rcp_customer_single_membership_query_args', $args, $customer_id );

	$memberships = rcp_get_memberships( $args );

	if ( is_array( $memberships ) && isset( $memberships[0] ) ) {
		$membership = $memberships[0];
	}

	return $membership;

}

/**
 * Get all the memberships belonging to a customer.
 *
 * @param int   $customer_id ID of the customer to get the memberships for.
 * @param array $args        Query arguments to override the defaults.
 *
 * @since 3.0
 * @return array Array of RCP_Membership objects.
 */
function rcp_get_customer_memberships( $customer_id, $args = array() ) {

	$customer = rcp_get_customer( $customer_id );

	return $customer->get_memberships( $args );

}

/**
 * Inserts a new note for a customer.
 *
 * @param int    $customer_id ID of the customer to insert a note for. Leave blank for current customer.
 * @param string $note        New note to add.
 *
 * @since 3.0
 * @return void
 */
function rcp_add_customer_note( $customer_id = 0, $note = '' ) {

	$customer = rcp_get_customer( $customer_id );

	if ( ! is_object( $customer ) ) {
		return;
	}

	$customer->add_note( $note );

}

/**
 * Get all the membership level IDs a given customer is part of.
 *
 * This returns the level IDs for all active memberships; it does not include expired or pending memberships.
 *
 * @param int $customer_id ID of the customer. 0 for current customer.
 *
 * @since 3.0
 * @return array
 */
function rcp_get_customer_membership_level_ids( $customer_id = 0 ) {

	$customer = rcp_get_customer( $customer_id );

	if ( ! is_object( $customer ) ) {
		return array();
	}

	$membership_level_ids = array();

	$memberships = $customer->get_memberships( array( 'status' => array( 'active', 'cancelled' ) ) );

	if ( empty( $memberships ) ) {
		return array();
	}

	foreach ( $memberships as $membership ) {
		/**
		 * @var RCP_Membership $membership
		 */

		$membership_level_ids[] = $membership->get_object_id();
	}

	return $membership_level_ids;

}

/**
 * Get all the membership level names a given customer is part of.
 *
 * This returns the level names for all active memberships; it does not include expired or pending memberships.
 *
 * @param int $customer_id ID of the customer. 0 for current customer.
 *
 * @since 3.0
 * @return array
 */
function rcp_get_customer_membership_level_names( $customer_id = 0 ) {

	$customer = rcp_get_customer( $customer_id );

	if ( ! is_object( $customer ) ) {
		return array();
	}

	$membership_level_names = array();

	$memberships = $customer->get_memberships( array( 'status' => array( 'active', 'cancelled' ) ) );

	if ( empty( $memberships ) ) {
		return array();
	}

	foreach ( $memberships as $membership ) {
		/**
		 * @var RCP_Membership $membership
		 */
		$membership_level_names[] = rcp_get_subscription_name( $membership->get_object_id() );
	}

	return $membership_level_names;

}

/**
 * Disable all the customer's memberships. This cancels all payment profiles, expires the memberships, and hides them
 * from the customer.
 *
 * @param int $customer_id ID of the customer to disable memberships for. 0 for current customer.
 *
 * @since 3.0
 * @return void
 */
function rcp_disable_customer_memberships( $customer_id = 0 ) {

	$customer = rcp_get_customer( $customer_id );

	if ( ! is_object( $customer ) ) {
		return;
	}

	$customer->disable_memberships();

}

/**
 * Determines whether or not the customer has used a free trial.
 *
 * @param int $customer_id ID of the customer to retrieve, or 0 for current customer.
 *
 * @since 3.0
 * @return bool
 */
function rcp_customer_has_trialed( $customer_id = 0 ) {

	$customer = rcp_get_customer( $customer_id );

	if ( ! is_object( $customer ) ) {
		return false;
	}

	return $customer->has_trialed();

}

/**
 * Get the gateway customer ID for a given RCP customer and gateway.
 * This is useful if wanting to reuse the same gateway customer ID for a second subscription.
 *
 * @param int    $customer_id ID of the customer to get the ID for.
 * @param string $gateway     Gateway to get the ID for.
 *
 * @since  3.0
 * @return string|false Gateway customer ID on success, false on failure.
 */
function rcp_get_customer_gateway_id( $customer_id, $gateway ) {

	global $wpdb;

	$gateway_customer_id = false;
	$memberships_table   = rcp_get_memberships_db_name();

	$query = $wpdb->prepare(
		"SELECT gateway_customer_id FROM {$memberships_table} WHERE customer_id = %d AND gateway = %s and gateway_customer_id != '' LIMIT 1",
		absint( $customer_id ),
		sanitize_text_field( $gateway )
	);

	$result = $wpdb->get_var( $query );

	if ( ! empty( $result ) ) {
		$gateway_customer_id = $result;
	}

	return $gateway_customer_id;

}