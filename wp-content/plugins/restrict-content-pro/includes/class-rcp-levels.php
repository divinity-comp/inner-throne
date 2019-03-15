<?php
/**
 * RCP Membership Levels class
 *
 * This class handles querying, inserting, updating, and removing membership levels
 * Also includes other membership level helper functions
 *
 * @package     Restrict Content Pro
 * @subpackage  Classes/Membership Levels
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5
 */

class RCP_Levels {

	/**
	 * Holds the name of our levels database table
	 *
	 * @access  public
	 * @since   1.5
	*/
	public $db_name;

	/**
	 * Holds the name of our level meta database table
	 *
	 * @access  public
	 * @since   2.6
	*/
	public $meta_db_name;


	/**
	 * Holds the version number of our levels database table
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

		$this->db_name      = rcp_get_levels_db_name();
		$this->meta_db_name = rcp_get_level_meta_db_name();
		$this->db_version   = '1.6';

	}


	/**
	 * Retrieve a specific membership level from the database
	 *
	 * @param   int $level_id ID of the level to retrieve.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  object
	 */
	public function get_level( $level_id = 0 ) {
		global $wpdb;

		$level = wp_cache_get( 'level_' . $level_id, 'rcp' );

		if( false === $level ) {

			$level = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->db_name} WHERE id='%d';", $level_id ) );

			wp_cache_set( 'level_' . $level_id, $level, 'rcp' );

		}

		return apply_filters( 'rcp_get_level', $level );

	}

	/**
	 * Retrieve a specific membership level from the database
	 *
	 * @param   string $field Name of the field to check against.
	 * @param   mixed  $value Value of the field.
	 *
	 * @access  public
	 * @since   1.8.2
	 * @return  object
	 */
	public function get_level_by( $field = 'name', $value = '' ) {
		global $wpdb;


		$level = wp_cache_get( 'level_' . $field . '_' . $value, 'rcp' );

		if( false === $level ) {

			$level = $wpdb->get_row( "SELECT * FROM {$this->db_name} WHERE {$field}='{$value}';" );

			wp_cache_set( 'level_' . $field . '_' . $value, $level, 'rcp' );

		}

		return apply_filters( 'rcp_get_level', $level );

	}


	/**
	 * Retrieve all membership levels from the database
	 *
	 * @param  array $args Query arguments to override the defaults.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  array|false Array of level objects or false if none are found.
	 */
	public function get_levels( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'status'  => 'all',
			'limit'   => null,
			'orderby' => 'list_order'
		);

		$args = wp_parse_args( $args, $defaults );

		if( $args['status'] == 'active' ) {
			$where = "WHERE `status` = 'active'";
		} elseif( $args['status'] == 'inactive' ) {
			$where = "WHERE `status` = 'inactive'";
		} else {
			$where = "";
		}

		if( ! empty( $args['limit'] ) )
			$limit = " LIMIT {$args['limit']}";
		else
			$limit = '';

		$cache_key = md5( implode( '|', $args ) );

		$levels = wp_cache_get( $cache_key, 'rcp' );

		if( false === $levels ) {

			$levels = $wpdb->get_results( "SELECT * FROM {$this->db_name} {$where} ORDER BY {$args['orderby']}{$limit};" );

			wp_cache_set( $cache_key, $levels, 'rcp' );

		}

		$levels = apply_filters( 'rcp_get_levels', $levels );

		if( ! empty( $levels ) ) {
			return $levels;
		}

		return false;
	}

	/**
	 * Count the total number of membership levels in the database
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
			$where = "WHERE `status` = 'active'";
		} elseif ( $args['status'] == 'inactive' ) {
			$where = "WHERE `status` = 'inactive'";
		}

		$key   = md5( 'rcp_levels_count_' . serialize( $args ) );
		$count = get_transient( $key );

		if ( false === $count ) {
			$count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$this->db_name} {$where}" );
			set_transient( $key, $count, 10800 );
		}

		return $count;

	}


	/**
	 * Retrieve a field for a membership level
	 *
	 * @param   int    $level_id ID of the level.
	 * @param   string $field    Name of the field to retrieve the value for.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  mixed
	 */
	public function get_level_field( $level_id = 0, $field = '' ) {

		global $wpdb;


		$value = wp_cache_get( 'level_' . $level_id . '_' . $field, 'rcp' );

		if( false === $value ) {

			$value = $wpdb->get_col( $wpdb->prepare( "SELECT {$field} FROM {$this->db_name} WHERE id='%d';", $level_id ) );

			wp_cache_set( 'level_' . $level_id . '_' . $field, $value, 'rcp', 3600 );

		}

		$value = ( $value ) ? $value[0] : false;

		return apply_filters( 'rcp_get_level_field', $value, $level_id, $field );

	}


	/**
	 * Insert a membership level into the database
	 *
	 * @param   array $args Arguments to override the defaults.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  int|WP_Error ID of the newly created level or WP_Error on failure.
	 */
	public function insert( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'name'                => '',
			'description'         => '',
			'duration'            => 'unlimited',
			'duration_unit'       => 'month',
			'trial_duration'      => '0',
			'trial_duration_unit' => 'day',
			'price'               => '0',
			'fee'                 => '0',
			'maximum_renewals'    => '0',
			'after_final_payment' => '',
			'list_order'          => '0',
			'level'               => '0',
			'status'              => 'inactive',
			'role'                => 'subscriber'
		);

		$args = wp_parse_args( $args, $defaults );

		do_action( 'rcp_pre_add_subscription', $args );

		$args = apply_filters( 'rcp_add_subscription_args', $args );

		foreach( array( 'price', 'fee' ) as $key ) {
			if ( empty( $args[$key] ) ) {
				$args[$key] = '0';
			}
			$args[$key] = str_replace( ',', '', $args[$key] );
		}

		// Validate payment plan.
		if ( empty( $args['maximum_renewals'] ) ) {
			// If maximum renewals is "0" then no payment plan.
			$args['maximum_renewals']    = 0;
			$args['after_final_payment'] = '';
		}

		// Validate price value
		if ( false === $this->valid_amount( $args['price'] ) || $args['price'] < 0 ) {
			rcp_log( sprintf( 'Failed inserting membership level: invalid price ( %s ).', $args['price'] ), true );

			return new WP_Error( 'invalid_level_price', __( 'Invalid price: the membership level price must be a valid positive number.', 'rcp' ) );
		}

		// Validate fee value
		if ( false === $this->valid_amount( $args['fee'] ) ) {
			rcp_log( sprintf( 'Failed inserting membership level: invalid fee ( %s ).', $args['fee'] ), true );

			return new WP_Error( 'invalid_level_fee', __( 'Invalid fee: the membership level fee must be a valid number.', 'rcp' ) );
		}

		/**
		 * Validate the trial settings.
		 * If a trial is enabled, the level's regular price and duration must be > 0.
		 */
		if ( $args['trial_duration'] > 0 ) {
			if ( $args['price'] <= 0 || $args['duration'] <= 0 ) {
				rcp_log( sprintf( 'Failed inserting membership level: invalid settings for free trial. Price: %f; Duration: %d', $args['price'], $args['duration'] ), true );

				return new WP_Error( 'invalid_level_trial', __( 'Invalid trial: a membership level with a trial must have a price and duration greater than zero.', 'rcp' ) );
			}
		}

		$add = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$this->db_name} SET
					`name`                = '%s',
					`description`         = '%s',
					`duration`            = '%d',
					`duration_unit`       = '%s',
					`trial_duration`      = '%d',
					`trial_duration_unit` = '%s',
					`price`               = '%s',
					`fee`                 = '%s',
					`maximum_renewals`    = '%d',
					`after_final_payment` = '%s',
					`list_order`          = '%d',
					`level`               = '%d',
					`status`              = '%s',
					`role`                = '%s'
				;",
				sanitize_text_field( $args['name'] ),
				sanitize_text_field( $args['description'] ),
				sanitize_text_field( $args['duration'] ),
				sanitize_text_field( $args['duration_unit'] ),
				absint( $args['trial_duration'] ),
				in_array( $args['trial_duration_unit'], array( 'day', 'month', 'year' ) ) ? $args['trial_duration_unit'] : 'day',
				sanitize_text_field( $args['price'] ),
				sanitize_text_field( $args['fee'] ),
				absint( $args['maximum_renewals'] ),
				sanitize_text_field( $args['after_final_payment'] ),
				absint( $args['list_order'] ),
				absint( $args['level'] ),
				sanitize_text_field( $args['status'] ),
				sanitize_text_field( $args['role'] )
			 )
		);

		if( $add ) {

			$level_id = $wpdb->insert_id;

			// Delete caches for both "all" statuses and "active" statuses.
			$cache_args = array(
				'status'  => 'all',
				'limit'   => null,
				'orderby' => 'list_order'
			);

			$cache_key = md5( implode( '|', $cache_args ) );

			wp_cache_delete( $cache_key, 'rcp' );

			$cache_args['status'] = 'active';
			$cache_key            = md5( implode( '|', $cache_args ) );
			wp_cache_delete( $cache_key, 'rcp' );

			// Delete transients for level counts.
			delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => 'all' ) ) ) );
			delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => $args['status'] ) ) ) );

			do_action( 'rcp_add_subscription', $level_id, $args );

			rcp_log( sprintf( 'Successfully added new membership level #%d. Args: %s', $level_id, var_export( $args, true ) ) );

			return $level_id;
		} else {
			rcp_log( sprintf( 'Failed inserting new membership level into database. Args: %s', var_export( $args, true ) ), true );
		}

		return new WP_Error( 'level_not_added', __( 'An unexpected error occurred while trying to add the membership level.', 'rcp' ) );

	}


	/**
	 * Update an existing membership level
	 *
	 * @param   int   $level_id ID of the level to update.
	 * @param   array $args     Fields and values to update.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  true|WP_Error True if the update was successful, WP_Error on failure.
	 */
	public function update( $level_id = 0, $args = array() ) {

		global $wpdb;

		$level = $this->get_level( $level_id );
		$level = get_object_vars( $level );

		$args = array_merge( $level, $args );

		do_action( 'rcp_pre_edit_subscription_level', absint( $args['id'] ), $args );

		foreach( array( 'price', 'fee' ) as $key ) {
			if ( empty( $args[$key] ) ) {
				$args[$key] = '0';
			}
			$args[$key] = str_replace( ',', '', $args[$key] );
		}

		// Validate payment plan.
		if ( empty( $args['maximum_renewals'] ) ) {
			// If maximum renewals is "0" then no payment plan.
			$args['maximum_renewals']    = 0;
			$args['after_final_payment'] = '';
		}

		// Validate price value
		if ( false === $this->valid_amount( $args['price'] ) || $args['price'] < 0 ) {
			rcp_log( sprintf( 'Failed updating membership level #%d: invalid price ( %s ).', $level_id, $args['price'] ), true );

			return new WP_Error( 'invalid_level_price', __( 'Invalid price: the membership level price must be a valid positive number.', 'rcp' ) );
		}

		// Validate fee value
		if ( false === $this->valid_amount( $args['fee'] ) ) {
			rcp_log( sprintf( 'Failed updating membership level #%d: invalid fee ( %s ).', $level_id, $args['fee'] ), true );

			return new WP_Error( 'invalid_level_fee', __( 'Invalid fee: the membership level fee must be a valid number.', 'rcp' ) );
		}

		/**
		 * Validate the trial settings.
		 * If a trial is enabled, the level's regular price and duration must be > 0.
		 */
		if ( $args['trial_duration'] > 0 ) {
			if ( $args['price'] <= 0 || $args['duration'] <= 0 ) {
				rcp_log( sprintf( 'Failed updating membership level #%d: invalid settings for free trial. Price: %f; Duration: %d', $level_id, $args['price'], $args['duration'] ), true );

				return new WP_Error( 'invalid_level_trial', __( 'Invalid trial: a membership level with a trial must have a price and duration greater than zero.', 'rcp' ) );
			}
		}

		$update = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$this->db_name} SET
					`name`                = '%s',
					`description`         = '%s',
					`duration`            = '%d',
					`duration_unit`       = '%s',
					`trial_duration`      = '%d',
					`trial_duration_unit` = '%s',
					`price`               = '%s',
					`fee`                 = '%s',
					`maximum_renewals`    = '%d',
					`after_final_payment` = '%s',
					`list_order`          = '%d',
					`level`               = '%d',
					`status`              = '%s',
					`role`                = '%s'
					WHERE `id`            = '%d'
				;",
				sanitize_text_field( $args['name'] ),
				wp_kses( $args['description'], rcp_allowed_html_tags() ),
				sanitize_text_field( $args['duration'] ),
				sanitize_text_field( $args['duration_unit'] ),
				absint( $args['trial_duration'] ),
				in_array( $args['trial_duration_unit'], array( 'day', 'month', 'year' ) ) ? $args['trial_duration_unit'] : 'day',
				sanitize_text_field( $args['price'] ),
				sanitize_text_field( $args['fee'] ),
				absint( $args['maximum_renewals'] ),
				sanitize_text_field( $args['after_final_payment'] ),
				absint( $args['list_order'] ),
				absint( $args['level'] ),
				sanitize_text_field( $args['status'] ),
				sanitize_text_field( $args['role'] ),
				absint( $args['id'] )
			)
		);

		// Delete caches for both "all" statuses and "active" statuses.
		$cache_args = array(
			'status'  => 'all',
			'limit'   => null,
			'orderby' => 'list_order'
		);

		$cache_key = md5( implode( '|', $cache_args ) );

		wp_cache_delete( $cache_key, 'rcp' );

		$cache_args['status'] = 'active';
		$cache_key            = md5( implode( '|', $cache_args ) );
		wp_cache_delete( $cache_key, 'rcp' );

		wp_cache_delete( 'level_' . $level_id, 'rcp' );

		// Delete transients for level counts.
		delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => 'all' ) ) ) );
		delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => 'active' ) ) ) );
		delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => 'inactive' ) ) ) );

		do_action( 'rcp_edit_subscription_level', absint( $args['id'] ), $args );

		if( $update !== false ) {
			rcp_log( sprintf( 'Successfully updated membership level #%d. Args: %s', absint( $level_id ), var_export( $args, true ) ) );

			return true;
		}

		rcp_log( sprintf( 'Failed updating membership level #%d. Args: %s', absint( $level_id ), var_export( $args, true ) ), true );

		return new WP_Error( 'level_not_added', __( 'An unexpected error occurred while trying to update the membership level.', 'rcp' ) );

	}


	/**
	 * Delete a membership level
	 *
	 * @param   int $level_id ID of the level to delete.
	 *
	 * @access  public
	 * @since   1.5
	 * @return  void
	 */
	public function remove( $level_id = 0 ) {

		global $wpdb;

		$remove = $wpdb->query( $wpdb->prepare( "DELETE FROM " . $this->db_name . " WHERE `id`='%d';", absint( $level_id ) ) );

		// Delete caches for both "all" statuses and "active" statuses.
		$args = array(
			'status'  => 'all',
			'limit'   => null,
			'orderby' => 'list_order'
		);

		$cache_key = md5( implode( '|', $args ) );

		wp_cache_delete( $cache_key, 'rcp' );

		$cache_args['status'] = 'active';
		$cache_key            = md5( implode( '|', $cache_args ) );
		wp_cache_delete( $cache_key, 'rcp' );

		// Delete transients for level counts.
		delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => 'all' ) ) ) );
		delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => 'active' ) ) ) );
		delete_transient( md5( 'rcp_levels_count_' . serialize( array( 'status' => 'inactive' ) ) ) );

		do_action( 'rcp_remove_level', absint( $level_id ) );

		rcp_log( sprintf( 'Deleted membership level ID #%d.', $level_id ) );

	}

	/**
	 * Retrieve level meta field for a membership level.
	 *
	 * @param   int    $level_id      Membership level ID.
	 * @param   string $meta_key      The meta key to retrieve.
	 * @param   bool   $single        Whether to return a single value.
	 *
	 * @access  public
	 * @since   2.6
	 * @return  mixed  Single metadata value, or array of values
	 */
	public function get_meta( $level_id = 0, $meta_key = '', $single = false ) {
		return get_metadata( 'level', $level_id, $meta_key, $single );
	}

	/**
	 * Add meta data field to a membership level.
	 *
	 * @param   int    $level_id      Membership level ID.
	 * @param   string $meta_key      Metadata name.
	 * @param   mixed  $meta_value    Metadata value.
	 * @param   bool   $unique        Optional, default is false. Whether the same key should not be added.
	 *
	 * @access  public
	 * @since   2.6
	 * @return  int|false             The meta ID on success, false on failure.
	 */
	public function add_meta( $level_id = 0, $meta_key = '', $meta_value, $unique = false ) {
		return add_metadata( 'level', $level_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Update level meta field based on membership level ID.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and membership level ID.
	 *
	 * If the meta field for the membership level does not exist, it will be added.
	 *
	 * @param   int    $level_id      Membership level ID.
	 * @param   string $meta_key      Metadata key.
	 * @param   mixed  $meta_value    Metadata value.
	 * @param   mixed  $prev_value    Optional. Previous value to check before removing.
	 *
	 * @access  public
	 * @since   2.6
	 * @return  int|bool              Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	public function update_meta( $level_id = 0, $meta_key = '', $meta_value, $prev_value = '' ) {
		return update_metadata( 'level', $level_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Remove metadata matching criteria from a membership level.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching key, if needed.
	 *
	 * @param   int    $level_id      Membership level ID.
	 * @param   string $meta_key      Metadata name.
	 * @param   mixed  $meta_value    Optional. Metadata value.
	 *
	 * @access  public
	 * @since   2.6
	 * @return  bool                  True on successful delete, false on failure.
	 */
	public function delete_meta( $level_id = 0, $meta_key = '', $meta_value = '' ) {
		return delete_metadata( 'level', $level_id, $meta_key, $meta_value );
	}

	/**
	 * Removes all metadata for the specified membership level.
	 *
	 * @since 2.6.6
	 * @uses wpdb::query()
	 * @uses wpdb::prepare()
	 *
	 * @param  int $level_id membership level ID.
	 * @return int|false Number of rows affected/selected or false on error.
	 */
	public function remove_all_meta_for_level_id( $level_id = 0 ) {

		global $wpdb;

		if ( empty( $level_id ) ) {
			return;
		}

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->levelmeta} WHERE level_id = %d", absint( $level_id ) ) );
	}

	/**
	 * Validates that the amount is a valid format.
	 *
	 * Private for now until we finish validation for all fields.
	 *
	 * @since 2.7
	 * @access private
	 * @return boolean true if valid, false if not.
	 */
	private function valid_amount( $amount ) {
		return filter_var( $amount, FILTER_VALIDATE_FLOAT );
	}

	/**
	 * Determines if the specified membership level has a trial option.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param int $level_id The membership level ID.
	 * @return boolean true if the level has a trial option, false if not.
	 */
	public function has_trial( $level_id = 0 ) {

		if ( empty( $level_id ) ) {
			return;
		}

		$level_id = absint( $level_id );

		$level = $this->get_level( $level_id );

		return ! empty( $level->trial_duration );
	}

	/**
	 * Retrieves the trial duration for the specified membership level.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param int $level_id The membership level ID.
	 * @return int The duration of the trial. 0 if there is no trial.
	 */
	public function trial_duration( $level_id = 0 ) {

		if ( empty( $level_id ) ) {
			return;
		}

		$level_id = absint( $level_id );

		$level = $this->get_level( $level_id );

		return ! empty( $level->trial_duration ) ? $level->trial_duration : 0;

	}

	/**
	 * Retrieves the trial duration unit for the specified membership level.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param int $level_id The membership level ID.
	 * @return string The duration unit of the trial.
	 */
	public function trial_duration_unit( $level_id = 0 ) {

		if ( empty( $level_id ) ) {
			return;
		}

		$level_id = absint( $level_id );

		$level = $this->get_level( $level_id );

		return in_array( $level->trial_duration_unit, array( 'day', 'month', 'year' ) ) ? $level->trial_duration_unit : 'day';
	}

}