<?php
/**
 * RCP Discounts class
 *
 * This class handles querying, inserting, updating, and removing discounts
 * Also includes other discount helper functions
 *
 * @package     Restrict Content Pro
 * @subpackage  Classes/Discounts
 * @copyright   Copyright (c) 2017, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 1.5
 */


class RCP_Discounts {

	/**
	 * Holds the name of our discounts database table
	 *
	 * @access  public
	 * @since   1.5
	 */
	public $db_name;


	/**
	 * Holds the version number of our discounts database table
	 *
	 * @access  public
	 * @since   1.5
	 */
	public $db_version;


	/**
	 * Get things started
	 *
	 * @since   1.5
	 * @return  void
	 */
	function __construct() {

		$this->db_name    = rcp_get_discounts_db_name();
		$this->db_version = '1.3';

	}


	/**
	 * Retrieve discounts from the database
	 *
	 * @param array $args Query arguments.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  array|false Array of discounts or false if none.
	 */
	public function get_discounts( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'status' => 'all'
		);

		$args = wp_parse_args( $args, $defaults );

		$where = '';

		// Filter by status.
		if ( $args['status'] == 'active' ) {
			$where = "WHERE `status` != 'disabled'";
		} elseif ( $args['status'] == 'disabled' ) {
			$where = "WHERE `status` = 'disabled'";
		}

		// TODO: Add optional args for limit, order, etc

		$discounts = $wpdb->get_results( "SELECT * FROM {$this->db_name} {$where};" );

		if( $discounts )
			return $discounts;
		return false;

	}

	/**
	 * Count the total number of discount codes in the database
	 *
	 * @param array $args Query arguments to override the defaults.
	 *
	 * @access public
	 * @return int
	 */
	public function count( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'status' => 'all'
		);

		$args = wp_parse_args( $args, $defaults );

		$where = '';

		// Filter by status.
		if ( $args['status'] == 'active' ) {
			$where = "WHERE `status` != 'disabled'";
		} elseif ( $args['status'] == 'disabled' ) {
			$where = "WHERE `status` = 'disabled'";
		}

		$key   = md5( 'rcp_discounts_count_' . serialize( $args ) );
		$count = get_transient( $key );

		if ( false === $count ) {
			$count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$this->db_name} {$where}" );
			set_transient( $key, $count, 10800 );
		}

		return $count;

	}


	/**
	 * Retrieve a specific discount from the database
	 *
	 * @param  int $discount_id ID of the discount to retrieve.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  object|null Database row or null on failure.
	 */
	public function get_discount( $discount_id = 0 ) {
		global $wpdb;

		$discount = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->db_name} WHERE id='%d';", $discount_id ) );

		return $discount;

	}


	/**
	 * Retrieve a specific discount from the database by field
	 *
	 * @param string $field Name of the field to check.
	 * @param string $value Value of the field.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  object|null Database row or null on failure.
	 */
	public function get_by( $field = 'code', $value = '' ) {
		global $wpdb;

		$discount = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->db_name} WHERE {$field}='%s';", $value ) );

		return $discount;

	}


	/**
	 * Get the status of a discount
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  string|false Discount status or false on failure.
	 */
	public function get_status( $discount_id = 0 ) {

		$discount = $this->get_discount( $discount_id );

		if( $discount )
			return $discount->status;
		return false;

	}


	/**
	 * Get the amount of a discount
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  int|float
	 */
	public function get_amount( $discount_id = 0 ) {

		$discount = $this->get_discount( $discount_id );

		if( $discount )
			return $discount->amount;
		return 0;

	}


	/**
	 * Get the number of times a discount has been used
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  int
	 */
	public function get_uses( $discount_id = 0 ) {

		$discount = $this->get_discount( $discount_id );

		if( $discount )
			return $discount->use_count;
		return 0;

	}


	/**
	 * Get the maximum number of times a discount can be used
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  int
	 */
	public function get_max_uses( $discount_id = 0 ) {

		$discount = $this->get_discount( $discount_id );

		if( $discount )
			return $discount->max_uses;
		return 0;

	}


	/**
	 * Get the associated membership level for a discount
	 *
	 * @deprecated 3.0 Use `get_membership_level_ids()` instead.
	 * @see RCP_Discounts::get_membership_level_ids()
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.6
	 * @return  int
	 */
	public function get_subscription_id( $discount_id = 0 ) {

		$ids = $this->get_membership_level_ids( $discount_id );

		if ( empty( $ids ) || ! isset( $ids[0] ) ) {
			return 0;
		}

		return $ids[0];

	}


	/**
	 * Get the associated membership level(s) for a discount.
	 *
	 * @param int $discount_id ID of the discount.
	 *
	 * @access public
	 * @since 3.0
	 * @return array
	 */
	public function get_membership_level_ids( $discount_id = 0 ) {

		$discount = $this->get_discount( $discount_id );

		if ( empty( $discount ) ) {
			return array();
		}

		$ids = maybe_unserialize( $discount->membership_level_ids );

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return array();
		}

		return array_map( 'absint', $ids );

	}


	/**
	 * Checks whether a discount code has a membership level associated
	 *
	 * @deprecated 3.0 Use `has_membership_level_ids()` instead.
	 * @see RCP_Discounts::has_membership_level_ids()
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.6
	 * @return  bool
	 */
	public function has_subscription_id( $discount_id = 0 ) {

		return $this->get_subscription_id( $discount_id ) > 0;

	}


	/**
	 * Checks whether a discount code has at least one associated membership level.
	 *
	 * @param int $discount_id ID of the discount code.
	 *
	 * @access public
	 * @since 3.0
	 * @return bool
	 */
	public function has_membership_level_ids( $discount_id = 0 ) {

		$membership_level_ids = $this->get_membership_level_ids( $discount_id );

		return ( count( $membership_level_ids ) > 0 );

	}


	/**
	 * Increase the use count of a discount by 1
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  void
	*/
	public function increase_uses( $discount_id = 0 ) {

		$uses = absint( $this->get_uses( $discount_id ) );
		$uses += 1;
		$this->update( $discount_id, array( 'use_count' => $uses ) );
	}

	/**
	 * Decrease the use count of a discount by 1
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   2.8
	 * @return  void
	 */
	public function decrease_uses( $discount_id = 0 ) {

		$uses = absint( $this->get_uses( $discount_id ) );
		$uses -= 1;
		
		if( $uses < 0 ) {
			$uses = 0;
		}

		$this->update( $discount_id, array( 'use_count' => $uses ) );

	}


	/**
	 * Get the expiration date of a discount
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  string|false Expiration date, or false if it never expires.
	 */
	public function get_expiration( $discount_id = 0 ) {

		$discount = $this->get_discount( $discount_id );

		if( $discount )
			return $discount->expiration;
		return false;

	}


	/**
	 * Get the discount type
	 *
	 * @param  int $discount_id ID of the discount.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  string|false
	 */
	public function get_type( $discount_id = 0 ) {

		$discount = $this->get_discount( $discount_id );

		if( $discount )
			return $discount->unit;
		return false;

	}


	/**
	 * Store a discount in the database
	 *
	 * @param  array $args Arguments for the discount code.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  int|WP_Error|false ID of the newly created discount code or WP_Error/false on failure.
	 */
	public function insert( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'name'                 => '',
			'description'          => '',
			'amount'               => '0.00',
			'status'               => 'inactive',
			'unit'                 => '%',
			'code'                 => '',
			'expiration'           => '',
			'max_uses' 	           => 0,
			'use_count'            => '0',
			'membership_level_ids' => array()
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['subscription_id'] ) && empty( $args['membership_level_ids'] ) ) {
			$args['membership_level_ids'] = array( absint( $args['subscription_id'] ) );
		}

		if ( empty( $args['membership_level_ids'] ) ) {
			$args['membership_level_ids'] = '';
		}

		$amount = $this->format_amount( $args['amount'], $args['unit'] );

		if ( is_wp_error( $amount ) ) {
			return $amount;
		} else {
			$args['amount'] = $amount;
		}

		$args['code'] = strtolower( $args['code'] );

		if( $this->get_by( 'code', $args['code'] ) ) {
			return false; // this code already exists
		}

		do_action( 'rcp_pre_add_discount', $args );

		$add = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$this->db_name} SET
					`name`                 = '%s',
					`description`          = '%s',
					`amount`               = '%s',
					`status`               = 'active',
					`unit`                 = '%s',
					`code`                 = '%s',
					`expiration`           = '%s',
					`max_uses`             = '%d',
					`use_count`            = '0',
					`membership_level_ids` = '%s'
				;",
				sanitize_text_field( $args['name'] ),
				strip_tags( $args['description'] ),
				sanitize_text_field( $args['amount'] ),
				$args['unit'],
				sanitize_text_field( $args['code'] ),
				sanitize_text_field( $args['expiration'] ),
				absint( $args['max_uses'] ),
				maybe_serialize( $args['membership_level_ids'] )
			)
		);

		if( $add ) {

			$discount_id = $wpdb->insert_id;

			do_action( 'rcp_add_discount', $args, $discount_id );

			delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => 'all' ) ) ) );
			delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => $args['status'] ) ) ) );

			return $discount_id;

		}

		return false;
	}


	/**
	 * Update an existing discount
	 *
	 * @param  int   $discount_id ID of the discount to update.
	 * @param  array $args        Array of fields/values to update.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  bool Whether or not the update was successful.
	 */
	public function update( $discount_id = 0, $args = array() ) {

		global $wpdb;

		$discount = $this->get_discount( $discount_id );
		$discount = get_object_vars( $discount );

		$args     = array_merge( $discount, $args );

		$amount = $this->format_amount( $args['amount'], $args['unit'] );

		if ( is_wp_error( $amount ) ) {
			return $amount;
		} else {
			$args['amount'] = $amount;
		}

		do_action( 'rcp_pre_edit_discount', absint( $discount_id ), $args );

		$update = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$this->db_name} SET
					`name`                 = '%s',
					`description`          = '%s',
					`amount`               = '%s',
					`unit`                 = '%s',
					`code`                 = '%s',
					`status`               = '%s',
					`expiration`           = '%s',
					`max_uses`             = '%d',
					`use_count`            = '%d',
					`membership_level_ids` = '%s'
					WHERE `id`             = '%d'
				;",
				sanitize_text_field( $args['name'] ),
				strip_tags( $args['description'] ),
				sanitize_text_field( $args['amount'] ),
				$args['unit'],
				sanitize_text_field( $args['code'] ),
				sanitize_text_field( $args['status'] ),
				sanitize_text_field( $args['expiration'] ),
				absint( $args['max_uses'] ),
				absint( $args['use_count'] ),
				maybe_serialize( $args['membership_level_ids'] ),
				absint( $discount_id )
			)
		);

		do_action( 'rcp_edit_discount', absint( $discount_id ), $args );

		delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => 'all' ) ) ) );
		delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => 'active' ) ) ) );
		delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => 'disabled' ) ) ) );

		if( $update )
			return true;
		return false;

	}


	/**
	 * Delete a discount code
	 *
	 * @param  int $discount_id ID of the discount to delete.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  void
	 */
	public function delete( $discount_id = 0 ) {
		global $wpdb;
		do_action( 'rcp_delete_discount', $discount_id );
		$remove = $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->db_name} WHERE `id` = '%d';", absint( $discount_id ) ) );

		delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => 'all' ) ) ) );
		delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => 'active' ) ) ) );
		delete_transient( md5( 'rcp_discounts_count_' . serialize( array( 'status' => 'disabled' ) ) ) );
	}


	/**
	 * Check if a discount is maxed out
	 *
	 * @param  int $discount_id ID of the discount to check.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  bool
	 */
	public function is_maxed_out( $discount_id = 0 ) {

		$uses = $this->get_uses( $discount_id );
		$max  = $this->get_max_uses( $discount_id );
		$ret  = false;

		if( ! empty( $max ) && $max > 0 ) {
			if( $uses >= $max ) {
				$ret = true;
			}
		}

		return (bool) apply_filters( 'rcp_is_discount_maxed_out', $ret, $discount_id, $uses, $max );

	}


	/**
	 * Check if a discount is expired
	 *
	 * @param  int $discount_id ID of the discount to check.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  bool
	 */
	public function is_expired( $discount_id = 0 ) {

		$ret        = false;
		$expiration = $this->get_expiration( $discount_id );

		// if no expiration is set, return true
		if( ! empty( $expiration ) ) {

			if ( strtotime( 'NOW', current_time( 'timestamp' ) ) > strtotime( $expiration, current_time( 'timestamp' ) ) ) {
				$ret = true;
			}
		}

		return (bool) apply_filters( 'rcp_is_discount_expired', $ret, $discount_id, $expiration );

	}


	/**
	 * Add a discount to a user's history
	 *
	 * @param  int    $user_id ID of the user to add the discount to.
	 * @param  string $discount_code Discount code to add.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  void
	 */
	public function add_to_user( $user_id = 0, $discount_code = '' ) {

		$user_discounts = get_user_meta( $user_id, 'rcp_user_discounts', true );

		if( ! is_array( $user_discounts ) ) {
			$user_discounts = array();
		}

		$user_discounts[] = $discount_code;

		do_action( 'rcp_pre_store_discount_for_user', $discount_code, $user_id );

		update_user_meta( $user_id, 'rcp_user_discounts', $user_discounts );

		do_action( 'rcp_store_discount_for_user', $discount_code, $user_id );

	}

	/**
	 * Remove a discount from a user's history
	 *
	 * @param  int    $user_id ID of the user to remove the discount from.
	 * @param  string $discount_code Discount code to remove.
	 *
	 * @access  public
	 * @since   2.8
	 * @return  bool Whether or not the discount was removed.
	 */
	public function remove_from_user( $user_id, $discount_code = '' ) {

		$user_discounts = get_user_meta( $user_id, 'rcp_user_discounts', true );

		if( ! is_array( $user_discounts ) ) {
			$user_discounts = array();
		}

		// Reverse the array to remove the last instance of the discount.
		$key = array_search( $discount_code, array_reverse( $user_discounts, true ) );

		if( false !== $key ) {
			unset( $user_discounts[ $key ] );

			do_action( 'rcp_pre_remove_discount_from_user', $discount_code, $user_id );

			if( empty( $user_discounts ) ) {
				delete_user_meta( $user_id, 'rcp_user_discounts' );
			} else {
				update_user_meta( $user_id, 'rcp_user_discounts', $user_discounts );
			}

			do_action( 'rcp_remove_discount_from_user', $discount_code, $user_id );

			return true;
		}

		return false;

	}


	/**
	 * Check if a user has used a discount
	 *
	 * @param  int    $user_id ID of the user to check.
	 * @param  string $discount_code Discount code to check.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  bool
	 */
	public function user_has_used( $user_id = 0, $discount_code = '' ) {

		$user_discounts = get_user_meta( $user_id, 'rcp_user_discounts', true );

		if( is_array( $user_discounts ) && in_array( $discount_code, $user_discounts ) )
			return true;

		return false;

	}


	/**
	 * Format the discount code
	 *
	 * @param int|float $amount Discount amount.
	 * @param string    $type   Type of discount - either '%' or 'flat'.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  string
	 */
	public function format_discount( $amount = '', $type = '' ) {

		if( $type == '%' ) {
			$discount = $amount . '%';
		} elseif( $type == 'flat' ) {
			$discount = rcp_currency_filter( $amount );
		}

		return $discount;

	}


	/**
	 * Calculate the discounted price
	 *
	 * @param int|float $base_price      Full price without the discount.
	 * @param int|float $discount_amount Amount of the discount code.
	 * @param string    $type            Type of discount - either '%' or 'flat'.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  int|float
	 */
	public function calc_discounted_price( $base_price = '', $discount_amount = '', $type = '%' ) {

		$discounted_price = $base_price;
		if( $type == '%' ) {
			$discounted_price = $base_price - ( $base_price * ( $discount_amount / 100 ) );
		} elseif($type == 'flat') {
			$discounted_price = $base_price - $discount_amount;
		}

		return $discounted_price;

	}


	/**
	 * Sanitizes the discount amount
	 *
	 * @param int|float $amount The discount amount.
	 * @param string    $type   The discount type - either '%' or 'flat'.
	 *
	 * @access public
	 * @since  2.4.9
	 * @return mixed|array|WP_Error
	 */
	public function format_amount( $amount, $type ) {

		if ( empty( $amount ) || ! is_numeric( $amount ) ) {
			return new WP_Error( 'amount_missing', __( 'Please enter a discount amount containing numbers only.', 'rcp' ) );
		}

		if ( ! isset( $type ) || ! in_array( $type, array( '%', 'flat' ) ) ) {
			$type = 'flat';
		}

		if ( '%' === $type && ! filter_var( $amount, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1, 'max_range' => 100 ) ) ) ) {
			return new WP_Error( 'invalid_percent', __( 'Percentage discounts must be whole numbers between 1 and 100.', 'rcp' ) );
		}

		return filter_var( $amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION  );
	}


}
