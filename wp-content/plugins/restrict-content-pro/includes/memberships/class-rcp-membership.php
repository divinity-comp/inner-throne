<?php
/**
 * Membership Object
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2018, Restrict Content Pro team
 * @license   GPL2+
 * @since     3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RCP_Membership {

	/**
	 * Membership ID.
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * ID of the customer this membership belongs to.
	 *
	 * @var int
	 */
	protected $customer_id = 0;

	/**
	 * Corresponding customer object.
	 *
	 * @var RCP_Customer
	 */
	protected $customer;

	/**
	 * Member object. Used for backwards compatibility filter support.
	 *
	 * @var RCP_Member
	 */
	protected $member;

	/**
	 * Corresponding object ID (such as a membership level ID).
	 *
	 * @var int
	 */
	protected $object_id = 0;

	/**
	 * Object type.
	 *
	 * @var string
	 */
	protected $object_type = 'membership';

	/**
	 * Currency used for membership payments.
	 *
	 * @var string
	 */
	protected $currency = 'USD';

	/**
	 * Initial payment amount.
	 *
	 * @var int|float
	 */
	protected $initial_amount = 0;

	/**
	 * Recurring amount.
	 *
	 * @var int|float
	 */
	protected $recurring_amount = 0;

	/**
	 * Date the membership was created.
	 *
	 * @var string
	 */
	protected $created_date = '0000-00-00 00:00:00';

	/**
	 * Last day of the trial. If no trial then this will be blank.
	 *
	 * @var string
	 */
	protected $trial_end_date = '';

	/**
	 * Date the membership was last renewed.
	 *
	 * @var string
	 */
	protected $renewed_date = '';

	/**
	 * Date the membership was cancelled. If it hasn't been cancelled this will be blank.
	 *
	 * @var string
	 */
	protected $cancellation_date = '';

	/**
	 * Date the membership expires or is next due for a renewal. If this is a lifetime membership then this will be
	 * `null`.
	 *
	 * @var string|null
	 */
	protected $expiration_date = null;

	/**
	 * Date the payment plan was completed, or `null` if it hasn't been.
	 *
	 * @var string|null
	 */
	protected $payment_plan_completed_date = null;

	/**
	 * Number of times this membership has been billed for, including the first payment.
	 *
	 * @var int
	 */
	protected $times_billed = 1;

	/**
	 * Maximum number of times to renew this membership. Default is `0` for unlimited.
	 *
	 * @var int
	 */
	protected $maximum_renewals = 0;

	/**
	 * Status of this membership: `active`, `cancelled`, `expired`, or `pending`.
	 *
	 * @var string
	 */
	protected $status = '';

	/**
	 * Whether or not this membership automatically renews.
	 *
	 * @var int
	 */
	protected $auto_renew = 0;

	/**
	 * Customer ID number with the gateway. This is a user profile ID - not a subscription ID. For example, if using
	 * Stripe then this ID begins with "cus_". Not all gateways have this.
	 *
	 * @var string
	 */
	protected $gateway_customer_id = '';

	/**
	 * ID of the subscription with the payment gateway. If using Stripe then this ID begins with "sub_".
	 *
	 * @var string
	 */
	protected $gateway_subscription_id = '';

	/**
	 * Payment gateway used for billing.
	 *
	 * @var string
	 */
	protected $gateway = '';

	/**
	 * Method used to create this membership. Options include: `live` (via the registration form), `manual` (manually
	 * added by a site admin), and `imported`.
	 *
	 * @var string
	 */
	protected $signup_method = '';

	/**
	 * Subscription key.
	 *
	 * @var string
	 */
	protected $subscription_key = '';

	/**
	 * Membership notes.
	 *
	 * @var string
	 */
	protected $notes = '';

	/**
	 * ID of the membership this one upgraded from.
	 *
	 * @var int
	 */
	protected $upgraded_from = 0;

	/**
	 * Date this membership was last modified.
	 *
	 * @var string
	 */
	protected $date_modified = '';

	/**
	 * Whether this membership is disabled (`0` = not disabled; `1` = disabled).
	 *
	 * @var int
	 */
	protected $disabled = 0;

	/**
	 * RCP_Membership constructor.
	 *
	 * @param object $membership_object Object from the database.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function __construct( $membership_object ) {

		if ( ! is_object( $membership_object ) ) {
			return;
		}

		$this->setup_membership( $membership_object );

	}

	/**
	 * Setup properties.
	 *
	 * @param object $membership_object
	 *
	 * @access private
	 * @since  3.0
	 * @return bool
	 */
	private function setup_membership( $membership_object ) {

		if ( ! is_object( $membership_object ) ) {
			return false;
		}

		$vars = get_object_vars( $membership_object );

		foreach ( $vars as $key => $value ) {
			switch ( $key ) {
				case 'created_date' :
				case 'trial_end_date' :
				case 'renewed_date' :
				case 'cancellation_date' :
				case 'expiration_date' :
				case 'payment_plan_completed_date' :
				case 'date_modified' :
					if ( '0000-00-00 00:00:00' === $value ) {
						$value = null;
					}
					break;
			}

			$this->{$key} = $value;
		}

		if ( empty( $this->id ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Update the membership data in the database.
	 *
	 * @param array $data
	 *
	 * @access public
	 * @since  3.0
	 * @return bool True if update was successful, false on failure.
	 */
	public function update( $data = array() ) {

		// Remove "notes" for our log because it's annoying.
		$log_data = $data;
		if ( ! empty( $log_data['notes'] ) ) {
			unset( $log_data['notes'] );
		}
		if ( ! empty( $log_data ) ) {
			rcp_log( sprintf( 'Updating membership #%d. New data: %s.', $this->get_id(), var_export( $log_data, true ) ) );
		}

		// @todo validation stuff

		// Expiration date.
		if ( ! empty( $data['expiration_date'] ) && 'none' == $data['expiration_date'] ) {
			$data['expiration_date'] = '0000-00-00 00:00:00';
		}

		$memberships = new \RCP\Database\Queries\Membership();

		$updated = $memberships->update_item( $this->get_id(), $data );

		if ( $updated ) {
			foreach ( $data as $key => $value ) {
				// Record changes of these columns.
				$columns_to_note = array(
					'expiration_date',
					'auto_renew',
					'status',
					'gateway_customer_id',
					'gateway_subscription_id',
					'gateway',
				);
				if ( in_array( $key, $columns_to_note ) && $value != $this->{$key} ) {
					$column_name = ucwords( str_replace( '_', ' ', $key ) );

					$this->add_note( sprintf( __( '%s changed from %s to %s.', 'rcp' ), $column_name, $this->{$key}, $value ) );
				}

				switch ( $key ) {
					case 'created_date' :
					case 'trial_end_date' :
					case 'renewed_date' :
					case 'cancellation_date' :
					case 'expiration_date' :
					case 'payment_plan_completed_date' :
						if ( '0000-00-00 00:00:00' === $value ) {
							$value = null;
						}
						break;
				}

				$this->{$key} = $value;
			}

			return true;
		}

		return false;

	}

	/**
	 * Get the ID of the membership.
	 *
	 * @access public
	 * @since  3.0
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the ID of the corresponding customer.
	 *
	 * @access public
	 * @since  3.0
	 * @return int
	 */
	public function get_customer_id() {
		return $this->customer_id;
	}

	/**
	 * Get the RCP_Customer object for this customer.
	 *
	 * @access public
	 * @since  3.0
	 * @return RCP_Customer
	 */
	public function get_customer() {

		if ( ! is_object( $this->customer ) ) {
			$this->customer = rcp_get_customer( $this->get_customer_id() );
		}

		return $this->customer;

	}

	/**
	 * Get the deprecated RCP_Member object for this customer.
	 *
	 * @access private
	 * @since  3.0
	 * @return RCP_Member
	 */
	private function get_member() {

		if ( ! is_object( $this->member ) ) {
			$this->member = $this->get_customer()->get_member();
		}

		return $this->member;

	}

	/**
	 * Get the corresponding object ID for this membership. This will probably be the membership level ID.
	 *
	 * @access public
	 * @since  3.0
	 * @return int|false Corresponding object ID.
	 */
	public function get_object_id() {
		$object_id = $this->object_id;

		if ( has_filter( 'rcp_member_get_subscription_id' ) ) {
			$object_id = apply_filters( 'rcp_member_get_subscription_id', $object_id, $this->get_member()->ID, $this->get_member() );
		}

		return $object_id;
	}

	/**
	 * Set the object (membership level) ID.
	 *
	 * @param int $object_id Object ID to set.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function set_object_id( $object_id ) {

		if ( has_action( 'rcp_member_pre_set_subscription_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_pre_set_object_id` instead.
			 */
			do_action( 'rcp_member_pre_set_subscription_id', $object_id, $this->get_member()->ID, $this->get_member() );
		}

		$this->update( array( 'object_id' => absint( $object_id ) ) );

		/**
		 * Action "rcp_transition_membership_object_id" will run.
		 *
		 * @see   \RCP\Database\Query::transition_item()
		 *
		 * @param string $old_value     Old object ID value.
		 * @param string $new_value     New object ID value.
		 * @param int    $membership_id ID of the membership.
		 *
		 * @since 3.0
		 */

		if ( has_action( 'rcp_member_post_set_subscription_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_transition_membership_object_id` instead.
			 */
			do_action( 'rcp_member_post_set_subscription_id', $object_id, $this->get_member()->ID, $this->get_member() );
		}

	}

	/**
	 * Get object type. This will probably be `membership`.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	/**
	 * Get the name of this membership level.
	 *
	 * @access public
	 * @since  3.0
	 * @return string|false Name of the membership level or false if none.
	 */
	public function get_membership_level_name() {

		if ( 'membership' != $this->object_type ) {
			return false;
		}

		return rcp_get_subscription_name( $this->get_object_id() );

	}

	/**
	 * Get the currency used for this membership's payments.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Get the amount charged on initial registration.
	 *
	 * @param bool $formatted Whether or not to format the amount with the currency symbol.
	 *
	 * @access public
	 * @since  3.0
	 * @return string|int|float
	 */
	public function get_initial_amount( $formatted = false ) {

		$initial_amount = $this->initial_amount;

		if ( $formatted ) {
			$initial_amount = rcp_currency_filter( $initial_amount );
		}

		return $initial_amount;

	}

	/**
	 * Get the amount charged on renewals.
	 *
	 * @param bool $formatted Whether or not to format the amount with the currency symbol.
	 *
	 * @access public
	 * @since  3.0
	 * @return string|int|float
	 */
	public function get_recurring_amount( $formatted = false ) {

		$recurring_amount = $this->recurring_amount;

		if ( $formatted ) {
			$recurring_amount = rcp_currency_filter( $recurring_amount );
		}

		return $recurring_amount;

	}

	/**
	 * Retrieves the formatted billing cycle. Will be formatted like so:
	 *        $15 then $20 every 6 months
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_formatted_billing_cycle() {

		$membership_level = rcp_get_subscription_details( $this->get_object_id() );

		if ( 0 == $this->get_initial_amount() && 0 == $this->get_recurring_amount() ) {
			$billing_cycle_string = __( 'Free', 'rcp' );

			return apply_filters( 'rcp_membership_formatted_billing_cycle', $billing_cycle_string, $this, $this->get_object_id() );
		}

		$initial_amount   = $this->get_initial_amount( true );
		$recurring_amount = $this->get_recurring_amount( true );

		if ( empty( $membership_level->duration ) ) {
			$billing_cycle_string = $initial_amount;

			return apply_filters( 'rcp_membership_formatted_billing_cycle', $billing_cycle_string, $this, $this->get_object_id() );
		}

		$duration_unit = lcfirst( rcp_filter_duration_unit( $membership_level->duration_unit, $membership_level->duration ) );

		$billing_cycle_string = sprintf( _n( '%1$s initially, then %2$s every %4$s', '%s initially, then %s every %d %s', $membership_level->duration, 'rcp' ), $initial_amount, $recurring_amount, $membership_level->duration, $duration_unit );

		/**
		 * Filters the billing cycle string.
		 *
		 * @param string         $billing_cycle_string Formatted billing cycle string.
		 * @param RCP_Membership $this                 Membership object.
		 * @param int            $membership_level_id  ID of the membership level.
		 *
		 * @since 3.0
		 */
		return apply_filters( 'rcp_membership_formatted_billing_cycle', $billing_cycle_string, $this, $this->get_object_id() );

	}

	/**
	 * Get the status of the membership
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_status() {

		$status = $this->status;

		if ( has_filter( 'rcp_member_get_status' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_get_status` instead.
			 */
			$status = apply_filters( 'rcp_member_get_status', $status, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the membership status.
		 *
		 * @param string         $status        Membership status.
		 * @param int            $membership_id ID of this membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$status = apply_filters( 'rcp_membership_get_status', $status, $this->get_id(), $this );

		return $status;
	}

	/**
	 * Sets the membership status.
	 *
	 * @param string $new_status
	 *
	 * @access public
	 * @since  3.0
	 * @return bool Whether or not the status was updated.
	 */
	public function set_status( $new_status ) {

		$set = false;

		$old_status = $this->get_status();

		if ( has_filter( 'rcp_set_status_value' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_set_membership_status_value` instead.
			 */
			$new_status = apply_filters( 'rcp_set_status_value', $new_status, $this->get_member()->ID, $old_status, $this->get_member() );
		}

		/**
		 * Filters the value of the status being set.
		 *
		 * @param string         $new_status    New status being set.
		 * @param string         $old_status    Old status from before this change.
		 * @param int            $membership_id ID of this membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$new_status = apply_filters( 'rcp_set_membership_status_value', $new_status, $old_status, $this->get_id(), $this );

		if ( ! empty( $new_status ) ) {

			// We don't have a "free" status anymore.
			if ( 'free' == $new_status ) {
				$new_status = 'active';
			}

			$update_data = array( 'status' => $new_status );

			if ( 'cancelled' == $new_status ) {
				$update_data['cancellation_date'] = current_time( 'mysql' );
			}

			$this->update( $update_data );

			/**
			 * Action "rcp_transition_membership_status" will run.
			 *
			 * @see   \RCP\Database\Query::transition_item()
			 *
			 * @param string $old_status    Old membership status.
			 * @param string $new_status    New membership status.
			 * @param int    $membership_id ID of the membership.
			 *
			 * @since 3.0
			 */

			if ( 'expired' != $new_status ) {
				delete_user_meta( $this->get_customer()->get_user_id(), '_rcp_expired_email_sent' );
			}

			if ( 'expired' == $new_status || 'cancelled' == $new_status ) {
				$this->set_recurring( false );
			}

			$set = true;
		}

		return $set;

	}

	/**
	 * Get the expiration date.
	 *
	 * @param bool $formatted Whether or not the returned value should be formatted.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_expiration_date( $formatted = true ) {

		$expiration = $this->expiration_date;

		if ( empty( $expiration ) ) {
			$expiration = 'none';
		} elseif ( $formatted ) {
			$expiration = date_i18n( get_option( 'date_format' ), strtotime( $expiration, current_time( 'timestamp' ) ) );
		}

		if ( has_filter( 'rcp_member_get_expiration_date' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_get_expiration_date` instead.
			 */
			$expiration = apply_filters( 'rcp_member_get_expiration_date', $expiration, $this->get_member()->ID, $this->get_member(), $formatted, false );
		}

		/**
		 * Filter the expiration date.
		 *
		 * @param string         $expiration    Membership expiration date.
		 * @param bool           $formatted     Whether or not to format the date.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$expiration = apply_filters( 'rcp_membership_get_expiration_date', $expiration, $formatted, $this->get_id(), $this );

		return $expiration;

	}

	/**
	 * Get the expiration date as a timestamp.
	 *
	 * @access public
	 * @since  3.0
	 * @return int|false
	 */
	public function get_expiration_time() {

		$expiration = $this->get_expiration_date( false );
		$timestamp  = ( $expiration && 'none' != $expiration ) ? strtotime( $expiration, current_time( 'timestamp' ) ) : false;

		if ( has_filter( 'rcp_member_get_expiration_time' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_get_expiration_time` instead.
			 */
			$timestamp = apply_filters( 'rcp_member_get_expiration_time', $timestamp, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the expiration time.
		 *
		 * @param int|false      $timestamp     Expiration timestamp.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$timestamp = apply_filters( 'rcp_membership_get_expiration_time', $timestamp, $this->get_id(), $this );

		return $timestamp;

	}

	/**
	 * Sets the expiration date for a member
	 *
	 * Should be passed as a MYSQL date string.
	 *
	 * @param string $new_date New date as a MySQL date string.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool Whether or not the expiration date was updated.
	 */
	public function set_expiration_date( $new_date = '' ) {

		$ret      = false;
		$old_date = $this->get_expiration_date( false );

		// Return early if there's no change in expiration date
		if ( empty( $new_date ) || ( ! empty( $old_date ) && ( $old_date == $new_date ) ) ) {
			return $ret;
		}

		$updated = $this->update( array( 'expiration_date' => $new_date ) );

		/**
		 * Action "rcp_transition_membership_expiration_date" will run.
		 *
		 * @see   \RCP\Database\Query::transition_item()
		 *
		 * @param string $old_date      Old expiration date in MySQL format.
		 * @param string $new_date      New expiration date in MySQL format.
		 * @param int    $membership_id ID of the membership.
		 *
		 * @since 3.0
		 */

		if ( has_action( 'rcp_set_expiration_date' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_transition_membership_expiration_date` instead.
			 */
			do_action( 'rcp_set_expiration_date', $this->get_member()->ID, $new_date, $old_date );
		}

		$ret = $updated;

		return $ret;

	}

	/**
	 * Calculate a new expiration date.
	 *
	 * @param bool $from_today Whether to calculate from today (`true`), or extend the existing expiration date
	 *                         (`false`).
	 * @param bool $trial      Whether or not this is for a free trial.
	 *
	 * @access public
	 * @since  3.0
	 * @return String Date in Y-m-d H:i:s format or "none" if is a lifetime membership.
	 */
	public function calculate_expiration( $from_today = false, $trial = false ) {

		// Get the member's current expiration date
		$expiration = $this->get_expiration_time();

		// Determine what date to use as the start for the new expiration calculation
		if ( ! $from_today && $expiration > current_time( 'timestamp' ) && $this->is_active() ) {

			$base_timestamp = $expiration;

		} else {

			$base_timestamp = current_time( 'timestamp' );

		}

		$membership_level_id = $this->get_object_id();
		$membership_level    = rcp_get_subscription_details( $membership_level_id );

		// @todo use membership level class
		if ( $membership_level->duration > 0 ) {

			if ( $membership_level->trial_duration > 0 && $trial ) {
				$expire_timestamp = strtotime( '+' . $membership_level->trial_duration . ' ' . $membership_level->trial_duration_unit . ' 23:59:59', $base_timestamp );
			} else {
				$expire_timestamp = strtotime( '+' . $membership_level->duration . ' ' . $membership_level->duration_unit . ' 23:59:59', $base_timestamp );
			}

			$extension_days = array( '29', '30', '31' );

			if ( in_array( date( 'j', $expire_timestamp ), $extension_days ) && 'day' !== $membership_level->duration_unit ) {

				/*
				 * Here we extend the expiration date by 1-3 days in order to account for "walking" payment dates in PayPal.
				 *
				 * See https://github.com/pippinsplugins/restrict-content-pro/issues/239
				 */

				$month = date( 'n', $expire_timestamp );

				if ( $month < 12 ) {
					$month += 1;
					$year  = date( 'Y' );
				} else {
					$month = 1;
					$year  = date( 'Y' ) + 1;
				}

				$timestamp = mktime( 0, 0, 0, $month, 1, $year );

				$expiration = date( 'Y-m-d 23:59:59', $timestamp );
			}

			$expiration = date( 'Y-m-d 23:59:59', $expire_timestamp );

		} else {

			$expiration = 'none';

		}

		if ( has_filter( 'rcp_member_calculated_expiration' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_calculated_expiration_date` instead.
			 */
			$expiration = apply_filters( 'rcp_member_calculated_expiration', $expiration, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the calculated expiration date.
		 *
		 * @param string         $expiration    Calculated expiration date in MySQL format.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$expiration = apply_filters( 'rcp_membership_calculated_expiration_date', $expiration, $this->get_id(), $this );

		return $expiration;

	}

	/**
	 * Get the date this membership was created.
	 *
	 * @param bool $formatted Whether or not the returned date should be formatted.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_created_date( $formatted = true ) {

		$created_date = $this->created_date;

		if ( $formatted ) {
			$created_date = date_i18n( get_option( 'date_format' ), strtotime( $created_date, current_time( 'timestamp' ) ) );
		}

		return $created_date;
	}

	/**
	 * Get the last day of the free trial.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_trial_end_date() {
		return $this->trial_end_date;
	}

	/**
	 * Returns true if this membership is in its free trial period. Returns false if the free trial period is over or
	 * if there never was one.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function is_trialing() {

		// There never was a free trial.
		if ( empty( $this->trial_end_date ) ) {
			$is_trialing = false;
		} elseif ( strtotime( $this->trial_end_date, current_time( 'timestamp' ) ) > current_time( 'timestamp' ) ) {
			// There was a free trial, and it is still ongoing.
			$is_trialing = true;
		} else {
			// There was a free trial but it's over now.
			$is_trialing = false;
		}

		// If the membership isn't active, it's not trialling.
		if ( ! $this->is_active() ) {
			$is_trialing = false;
		}

		if ( has_filter( 'rcp_is_trialing' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_is_trialing` instead.
			 */
			$is_trialing = apply_filters( 'rcp_is_trialing', $is_trialing, $this->get_member()->ID );
		}

		if ( has_filter( 'rcp_member_is_trialing' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_is_trialing` instead.
			 */
			$is_trialing = apply_filters( 'rcp_member_is_trialing', $is_trialing, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the trialing status.
		 *
		 * @param bool           $is_trialing   Whether or not this membership is in the trial period.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$is_trialing = apply_filters( 'rcp_membership_is_trialing', $is_trialing, $this->get_id(), $this );

		return $is_trialing;

	}

	/**
	 * Returns the date this membership was last renewed.
	 *
	 * @param bool $formatted Whether or not the returned date should be formatted.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_renewed_date( $formatted = true ) {

		$renewed_date = $this->renewed_date;

		if ( has_filter( 'rcp_get_renewed_date' ) ) {
			/**
			 * Filters the renewal date.
			 *
			 * @param string $renewed_date Date the membership was last renewed.
			 * @param int    $user_id      ID of the user account.
			 * @param int    $object_id    ID of the associated object.
			 * @param        $member       RCP_Member Deprecated member object.
			 */
			$renewed_date = apply_filters( 'rcp_get_renewed_date', $renewed_date, $this->get_member()->ID, $this->get_object_id(), $this->get_member() );
		}

		if ( $formatted && ! empty( $renewed_date ) ) {
			$renewed_date = date_i18n( get_option( 'date_format' ), strtotime( $renewed_date, current_time( 'timestamp' ) ) );
		}

		return $renewed_date;

	}

	/**
	 * Set the membership renewed date.
	 *
	 * @param string $date Date the membership was renewed. Leave blank to use current time.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function set_renewed_date( $date = '' ) {

		if ( empty( $date ) ) {
			$date = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
		}

		$updated = $this->update( array(
			'renewed_date' => $date
		) );

		if ( has_action( 'rcp_set_renewed_date' ) ) {
			do_action( 'rcp_set_renewed_date', $this->get_member()->ID, $date, $this->get_member() );
		}

		return $updated;

	}

	/**
	 * Returns the date this membership was cancelled.
	 *
	 * @param bool $formatted Whether or not the returned date should be formatted.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_cancellation_date( $formatted = true ) {

		$cancellation_date = $this->cancellation_date;

		if ( $formatted && ! empty( $cancellation_date ) ) {
			$cancellation_date = date_i18n( get_option( 'date_format' ), strtotime( $cancellation_date, current_time( 'timestamp' ) ) );
		}

		return $cancellation_date;

	}

	/**
	 * Get the number of times this membership has been billed for.
	 *
	 * @access public
	 * @since  3.0
	 * @return int
	 */
	public function get_times_billed() {
		return $this->times_billed;
	}

	/**
	 * Get the maximum number of renewals.
	 *
	 * @access public
	 * @since  3.0
	 * @return int
	 */
	public function get_maximum_renewals() {
		return $this->maximum_renewals;
	}

	/**
	 * Determines if this membership is active or not. A membership is active if it has the status "active" or if
	 * it's "cancelled" but has not yet reached EOT.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function is_active() {

		if ( $this->is_disabled() ) {

			$is_active = false;

		} else {

			if ( $this->is_expired() ) {
				$is_active = false;
			} else {
				$is_active = in_array( $this->get_status(), array( 'active', 'cancelled' ) );
			}

		}

		if ( has_filter( 'rcp_is_active' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_is_active` instead.
			 */
			$is_active = apply_filters( 'rcp_is_active', $is_active, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters whether or not the membership is active.
		 *
		 * @param bool           $is_active     Whether or not the membership is active.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$is_active = apply_filters( 'rcp_membership_is_active', $is_active, $this->get_id(), $this );

		return $is_active;

	}

	/**
	 * Determines if this is a paid membership.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function is_paid() {

		if ( $this->recurring_amount > 0 || $this->initial_amount > 0 ) {
			return true;
		}

		// As a fallback, check the price of the membership level.
		$membership_level = rcp_get_subscription_details( $this->get_object_id() );

		if ( (float) $membership_level->price > 0 ) {
			return true;
		}

		return false;

	}

	/**
	 * Determines whether or not this membership has expired.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function is_expired() {

		$is_expired = false;
		$expiration = $this->get_expiration_date( false );

		if ( $expiration && strtotime( 'NOW', current_time( 'timestamp' ) ) > strtotime( $expiration, current_time( 'timestamp' ) ) ) {
			$is_expired = true;
		}

		if ( $expiration == 'none' ) {
			$is_expired = false;
		}

		// If the expiration date is in the past but the status isn't "expired", let's update it now.
		if ( $is_expired && 'expired' != $this->get_status() ) {
			$this->set_status( 'expired' );
		}

		if ( has_filter( 'rcp_member_is_expired' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_is_expired` instead.
			 */
			$is_expired = apply_filters( 'rcp_member_is_expired', $is_expired, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters whether or not the membership is expired.
		 *
		 * @param bool           $is_expired    If the membership is expired.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$is_expired = apply_filters( 'rcp_membership_is_expired', $is_expired, $this->get_id(), $this );

		return $is_expired;

	}

	/**
	 * Determines if this membership automatically renews or not.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function is_recurring() {

		$is_recurring = ! empty( $this->auto_renew );

		if ( has_filter( 'rcp_member_is_recurring' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_is_recurring` instead.
			 */
			$is_recurring = apply_filters( 'rcp_member_is_recurring', $is_recurring, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the auto renew status.
		 *
		 * @param bool           $is_recurring  Whether or not this membership is recurring.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$is_recurring = apply_filters( 'rcp_membership_is_recurring', $is_recurring, $this->get_id(), $this );

		return $is_recurring;

	}

	/**
	 * Sets whether this membership automatically renews or not.
	 *
	 * @param bool $is_recurring
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function set_recurring( $is_recurring = true ) {

		rcp_log( sprintf( 'Updating recurring status for membership #%d. Customer ID: %d; Previous: %s; New: %s', $this->get_id(), $this->get_customer()->get_id(), var_export( $this->is_recurring(), true ), var_export( $is_recurring, true ) ) );

		$this->update( array( 'auto_renew' => (int) $is_recurring ) );

		/**
		 * Action "rcp_transition_membership_auto_renew" will run.
		 *
		 * @see   \RCP\Database\Query::transition_item()
		 *
		 * @param string $old_value     Old auto renew value.
		 * @param string $new_value     New auto renew value.
		 * @param int    $membership_id ID of the membership.
		 *
		 * @since 3.0
		 */

		if ( has_action( 'rcp_member_set_recurring' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_transition_membership_auto_renew` instead.
			 */
			do_action( 'rcp_member_set_recurring', $is_recurring, $this->get_member()->ID, $this->get_member() );
		}

	}

	/**
	 * Get the payment gateway used for this membership.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_gateway() {
		return $this->gateway;
	}

	/**
	 * Get the gateway customer ID. With Stripe this begins with "cus_".
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_gateway_customer_id() {

		$customer_id = $this->gateway_customer_id;

		if ( has_filter( 'rcp_member_get_payment_profile_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_get_gateway_customer_id` instead.
			 */
			$customer_id = apply_filters( 'rcp_member_get_payment_profile_id', $customer_id, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the gateway customer ID.
		 *
		 * @param string         $customer_id   Gateway customer ID.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$customer_id = apply_filters( 'rcp_membership_get_gateway_customer_id', $customer_id, $this->get_id(), $this );

		return $customer_id;

	}

	/**
	 * Set the gateway customer ID.
	 *
	 * @param string $customer_id New gateway ID to set.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function set_gateway_customer_id( $customer_id = '' ) {

		$customer_id = trim( $customer_id );

		if ( has_action( 'rcp_member_pre_set_profile_payment_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_pre_set_gateway_customer_id` instead.
			 */
			do_action( 'rcp_member_pre_set_profile_payment_id', $this->get_member()->ID, $customer_id, $this->get_member() );
		}

		$this->update( array( 'gateway_customer_id' => $customer_id ) );

		/**
		 * Action "rcp_transition_membership_gateway_customer_id" will run.
		 *
		 * @see   \RCP\Database\Query::transition_item()
		 *
		 * @param string $old_value     Old customer ID value.
		 * @param string $new_value     New customer ID value.
		 * @param int    $membership_id ID of the membership.
		 *
		 * @since 3.0
		 */

		if ( has_action( 'rcp_member_post_set_profile_payment_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_transition_membership_gateway_customer_id` instead.
			 */
			do_action( 'rcp_member_post_set_profile_payment_id', $this->get_member()->ID, $customer_id, $this->get_member() );
		}

	}

	/**
	 * Get the gateway subscription ID. With Stripe this begins with "sub_".
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_gateway_subscription_id() {

		$subscription_id = $this->gateway_subscription_id;

		if ( has_filter( 'rcp_member_get_merchant_subscription_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_get_gateway_subscription_id` instead.
			 */
			$subscription_id = apply_filters( 'rcp_member_get_merchant_subscription_id', $subscription_id, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the gateway subscription ID.
		 *
		 * @param string         $subscription_id Gateway subscription ID.
		 * @param int            $membership_id   ID of the membership.
		 * @param RCP_Membership $this            Membership object.
		 *
		 * @since 3.0
		 */
		$subscription_id = apply_filters( 'rcp_membership_get_gateway_subscription_id', $subscription_id, $this->get_id(), $this );

		return $subscription_id;

	}

	/**
	 * Set the gateway subscription ID.
	 *
	 * @param string $subscription_id Subscription ID to set.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function set_gateway_subscription_id( $subscription_id = '' ) {

		$subscription_id = trim( $subscription_id );

		if ( has_action( 'rcp_member_pre_set_merchant_subscription_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_pre_set_gateway_subscription_id` instead.
			 */
			do_action( 'rcp_member_pre_set_merchant_subscription_id', $this->get_member()->ID, $subscription_id, $this->get_member() );
		}

		$this->update( array( 'gateway_subscription_id' => $subscription_id ) );

		/**
		 * Action "rcp_transition_membership_gateway_subscription_id" will run.
		 *
		 * @see   \RCP\Database\Query::transition_item()
		 *
		 * @param string $old_value     Old subscription ID value.
		 * @param string $new_value     New subscription ID value.
		 * @param int    $membership_id ID of the membership.
		 *
		 * @since 3.0
		 */

		if ( has_action( 'rcp_member_post_set_merchant_subscription_id' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_transition_membership_gateway_subscription_id` instead.
			 */
			do_action( 'rcp_member_post_set_merchant_subscription_id', $this->get_member()->ID, $subscription_id, $this->get_member() );
		}

	}

	/**
	 * Get the membership's subscription key.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_subscription_key() {

		$subscription_key = $this->subscription_key;

		if ( has_filter( 'rcp_member_get_subscription_key' ) ) {
			/**
			 * @deprecated 3.0
			 */
			apply_filters( 'rcp_member_get_subscription_key', $subscription_key, $this->get_member()->ID, $this->get_member() );
		}

		return $subscription_key;

	}

	/**
	 * Set the membership subscription key.
	 *
	 * @param string $subscription_key Key to set. Automatically generated if omitted.
	 *
	 * @access public
	 * @since  2.3
	 * @return void
	 */
	public function set_subscription_key( $subscription_key = '' ) {

		if ( empty( $subscription_key ) ) {
			$subscription_key = rcp_generate_subscription_key();
		}

		$subscription_key = trim( $subscription_key );

		if ( has_action( 'rcp_member_pre_set_subscription_key' ) ) {
			/**
			 * @deprecated 3.0
			 */
			do_action( 'rcp_member_pre_set_subscription_key', $subscription_key, $this->get_member()->ID, $this->get_member() );
		}

		$this->update( array( 'subscription_key', $subscription_key ) );

		if ( has_action( 'rcp_member_post_set_subscription_key' ) ) {
			/**
			 * @deprecated 3.0
			 */
			do_action( 'rcp_member_post_set_subscription_key', $subscription_key, $this->get_member()->ID, $this->get_member() );
		}

	}

	/**
	 * Retrieves the membership ID this one was upgraded from.
	 *
	 * @access public
	 * @since  3.0
	 * @return int
	 */
	public function get_upgraded_from() {
		return $this->upgraded_from;
	}

	/**
	 * Determines whether or not this membership was upgraded from another one.
	 *
	 * @uses   RCP_Membership::get_upgraded_from()
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function was_upgrade() {
		$upgraded_from = $this->get_upgraded_from();

		return ! empty( $upgraded_from );
	}

	/**
	 * Activate the membership.
	 *
	 * Use this method instead of set_status() when activating the membership for the first time. Only this method
	 * triggers the welcome email.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function activate() {

		rcp_log( sprintf( 'Activating membership #%d.', $this->get_id() ) );

		/**
		 * Triggers before the membership is activated.
		 *
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		do_action( 'rcp_membership_pre_activate', $this->get_id(), $this );

		$this->add_note( __( 'Membership activated.', 'rcp' ) );

		if ( 'active' != $this->get_status() ) {
			$this->set_status( 'active' );
		}

		// Apply user role granted by this membership level.
		$membership_level = rcp_get_subscription_details( $this->get_object_id() );
		$role             = ! empty( $membership_level->role ) ? $membership_level->role : get_option( 'default_role', 'subscriber' );
		$user             = new WP_User( $this->get_customer()->get_user_id() );
		$user->add_role( apply_filters( 'rcp_default_user_level', $role, $membership_level->id ) );

		/**
		 * Triggers after the membership is activated.
		 *
		 * This sends the activation email.
		 *
		 * @see   rcp_email_on_membership_activation()
		 *
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		do_action( 'rcp_membership_post_activate', $this->get_id(), $this );

	}

	/**
	 * Determines whether or not the membership can be renewed.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function can_renew() {

		$can_renew = true;

		if ( $this->is_recurring() && $this->is_active() && 'cancelled' != $this->get_status() ) {
			$can_renew = false;
		}

		if ( 'none' == $this->get_expiration_date( false ) ) {
			$can_renew = false;
		}

		if ( ! $this->is_paid() ) {
			$can_renew = false;
		}

		// Can't renew a completed payment plan.
		if ( $this->is_payment_plan_complete() ) {
			$can_renew = false;
		}

		/**
		 * @deprecated 3.0
		 */
		$can_renew = apply_filters( 'rcp_member_can_renew', $can_renew, $this->get_customer()->get_user_id() );

		/**
		 * Filters whether or not the membership can be renewed.
		 *
		 * @param bool           $can_renew     Whether the membership can be renewed.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		return apply_filters( 'rcp_membership_can_renew', $can_renew, $this->get_id(), $this );

	}

	/**
	 * Renews the membership by updating status and expiration date.
	 *
	 * Does NOT handle payment processing for the renewal. This should be called after receiving a renewal payment.
	 *
	 * @param  bool   $recurring  Whether or not the membership is recurring.
	 * @param  string $status     Membership status.
	 * @param  string $expiration Membership expiration date in MySQL format.
	 *
	 * @access public
	 * @since  3.0
	 * @return true|false Whether or not the renewal was successful.
	 */
	public function renew( $recurring = false, $status = 'active', $expiration = '' ) {

		$membership_level_id = $this->get_object_id();

		rcp_log( sprintf( 'Starting membership renewal for membership #%d. Membership Level ID: %d; Current Expiration Date: %s', $this->id, $membership_level_id, $this->get_expiration_date() ) );

		if ( empty( $membership_level_id ) ) {
			return false;
		}

		// Bail if this has a payment plan and it's completed - prevents renewals from running after the fact.
		if ( $this->has_payment_plan() && $this->at_maximum_renewals() ) {
			return false;
		}

		$membership_level = rcp_get_subscription_details( $membership_level_id );

		if ( ! $expiration ) {
			$expiration = $this->calculate_expiration( $this->is_recurring() );

			if ( has_filter( 'rcp_member_renewal_expiration' ) ) {
				/**
				 * @deprecated 3.0 Use `rcp_membership_renewal_expiration_date` instead.
				 */
				$expiration = apply_filters( 'rcp_member_renewal_expiration', $expiration, $membership_level, $this->get_member()->ID );
			}

			/**
			 * Filters the calculated expiration date to be set after the renewal.
			 *
			 * @param string         $expiration       Calculated expiration date.
			 * @param object         $membership_level Membership level object.
			 * @param int            $membership_id    ID of the membership.
			 * @param RCP_Membership $this             Membership object.
			 *
			 * @since 3.0
			 */
			$expiration = apply_filters( 'rcp_membership_renewal_expiration_date', $expiration, $membership_level, $this->get_id(), $this );
		}

		if ( has_action( 'rcp_member_pre_renew' ) ) {
			/**
			 * deprecated 3.0 Use `rcp_membership_pre_renew` instead.
			 */
			do_action( 'rcp_member_pre_renew', $this->get_member()->ID, $expiration, $this->get_member() );
		}

		/**
		 * Triggers before the membership renewal.
		 *
		 * @param string         $expiration    New expiration date to be set.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		do_action( 'rcp_membership_pre_renew', $expiration, $this->get_id(), $this );

		$this->set_expiration_date( $expiration );

		if ( ! empty( $status ) ) {
			$this->set_status( $status );
		}

		$this->set_recurring( $recurring );

		// Add the role if the user doesn't already have it.
		$role = ! empty( $membership_level->role ) ? $membership_level->role : get_option( 'default_role', 'subscriber' );
		$user = new WP_User( $this->get_customer()->get_user_id() );
		if ( ! in_array( $role, $user->roles ) ) {
			$user->add_role( $role );
		}

		// Set the renewal date.
		$this->set_renewed_date(); // Current time.

		$this->add_note( __( 'Membership renewed.', 'rcp' ) );

		delete_user_meta( $this->get_customer()->get_user_id(), '_rcp_expired_email_sent' );

		if ( has_action( 'rcp_member_post_renew' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_post_renew` instead.
			 */
			do_action( 'rcp_member_post_renew', $this->get_member()->ID, $expiration, $this->get_member() );
		}

		/**
		 * Triggers after the membership renewal.
		 *
		 * @param string         $expiration    New expiration date to be set.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		do_action( 'rcp_membership_post_renew', $expiration, $this->get_id(), $this );

		rcp_log( sprintf( 'Completed membership renewal for membership #%d. Membership Level ID: %d; New Expiration Date: %s; New Status: %s', $this->id, $membership_level_id, $expiration, $this->get_status() ) );

		return true;

	}

	/**
	 * Determines whether or not an upgrade is possible for this membership.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function upgrade_possible() {

		$upgrade_possible = false;

		if ( ( ! $this->is_active() || ! $this->is_recurring() || ! $this->is_paid() ) && rcp_has_paid_levels() ) {
			$upgrade_possible = true;
		}

		if ( $this->has_upgrade_path() ) {
			$upgrade_possible = true;
		}

		if ( has_filter( 'rcp_can_upgrade_subscription' ) ) {
			/**
			 * @deprecated 3.0
			 */
			$upgrade_possible = (bool) apply_filters( 'rcp_can_upgrade_subscription', $upgrade_possible, $this->get_customer()->get_user_id() );
		}

		return $upgrade_possible;

	}

	/**
	 * Determines whether or not this membership has an upgrade path.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function has_upgrade_path() {

		$has_upgrade_path = (bool) $this->get_upgrade_paths();

		if ( has_filter( 'rcp_has_upgrade_path' ) ) {
			/**
			 * @deprecated 3.0
			 */
			$has_upgrade_path = apply_filters( 'rcp_has_upgrade_path', $has_upgrade_path, $this->get_customer()->get_user_id() );
		}

		return $has_upgrade_path;

	}

	/**
	 * Returns the available upgrade paths for this membership.
	 *
	 * @access public
	 * @since  3.0
	 * @return array
	 */
	public function get_upgrade_paths() {

		$current_membership_level = $this->get_object_id();
		$membership_levels        = rcp_get_subscription_levels( 'active' );

		// Remove the user's current subscription from the list.
		// @todo Maybe remove this `if` statement in 3.1.
		if ( ! $this->can_renew() ) {
			foreach ( $membership_levels as $key => $membership_level ) {
				if ( $current_membership_level == $membership_level->id ) {
					unset( $membership_levels[$key] );
				}
			}
		}

		$membership_levels = array_values( $membership_levels );

		if ( has_filter( 'rcp_get_upgrade_paths' ) ) {
			/**
			 * @deprecated 3.0
			 */
			$membership_levels = apply_filters( 'rcp_get_upgrade_paths', $membership_levels, $this->get_customer()->get_user_id() );
		}

		/**
		 * Filters the available upgrade paths.
		 *
		 * @param array          $membership_levels Array of membership level IDs.
		 * @param int            $membership_id     ID of the membership.
		 * @param RCP_Membership $this              Membership object.
		 */
		return apply_filters( 'rcp_get_membership_upgrade_paths', $membership_levels, $this->get_id(), $this );

	}

	/**
	 * Determines if this membership has a payment plan.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function has_payment_plan() {

		$membership_level = rcp_get_subscription_details( $this->get_object_id() );
		$renew_times      = ! empty( $membership_level->maximum_renewals ) ? $membership_level->maximum_renewals : 0;

		return $renew_times > 0;

	}

	/**
	 * Determines whether the maximum number of renewals has been reached.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function at_maximum_renewals() {

		if ( ! $this->has_payment_plan() ) {
			return false;
		}

		$times_billed = $this->get_times_billed() - 1; // Subtract 1 to exclude initial payment.
		$renew_times  = $this->get_maximum_renewals();

		return $times_billed >= $renew_times;

	}

	/**
	 * Returns the date the payment plan was completed.
	 *
	 * @access public
	 * @since  3.0
	 * @return null|string
	 */
	public function get_payment_plan_completed_date() {
		return $this->payment_plan_completed_date;
	}

	/**
	 * Set the date the payment plan was completed.
	 *
	 * @param string $date Date to set. Leave blank for current date/time.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function set_payment_plan_completed_date( $date = '' ) {

		if ( empty( $date ) ) {
			$date = current_time( 'mysql' );
		}

		$this->update( array(
			'payment_plan_completed_date' => $date
		) );

	}

	/**
	 * Determines whether or not the payment plan completion routine has run.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function is_payment_plan_complete() {

		$completed_date = $this->get_payment_plan_completed_date();

		return ! empty( $completed_date );

	}

	/**
	 * Completes a payment plan by processing "after final payment" actions.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool Whether or not the completion was successful.
	 */
	public function complete_payment_plan() {

		// No payment plan or not ready to complete.
		if ( ! $this->has_payment_plan() || ! $this->at_maximum_renewals() ) {
			return false;
		}

		// Completion routine has already run.
		if ( $this->is_payment_plan_complete() ) {
			return false;
		}

		$membership_level  = rcp_get_subscription_details( $this->get_object_id() );
		$after_plan_action = ! empty( $membership_level->after_final_payment ) ? $membership_level->after_final_payment : '';

		rcp_log( sprintf( 'Membership #%d payment plan is complete. Cancelling payment profile at gateway.', $this->get_id() ) );

		/*
		 * Cancel subscription at payment gateway.
		 */
		if ( $this->can_cancel() ) {
			$this->cancel_payment_profile( false );
		}

		/*
		 * Perform completion actions.
		 */
		if ( 'expire_immediately' == $after_plan_action ) {

			// Expire now.
			rcp_log( sprintf( 'Membership #%d expiring new.', $this->get_id() ) );
			$this->expire();

			$this->add_note( sprintf( __( '%s payment plan completed. Membership expiring immediately.', 'rcp' ), $membership_level->name ) );

		} elseif ( 'lifetime' == $after_plan_action ) {

			// Grant lifetime access.
			rcp_log( sprintf( 'Granting lifetime access for membership #%d.', $this->get_id() ) );
			$this->set_recurring( false );
			$this->set_expiration_date( 'none' );

			$this->add_note( sprintf( __( '%s payment plan completed. Granted lifetime membership access.', 'rcp' ), $membership_level->name ) );

		} elseif ( 'expire_term_end' == $after_plan_action ) {

			// Expire at end of term (so, cancel now).
			rcp_log( sprintf( 'Cancelling membership #%d at term end.', $this->get_id() ) );
			$this->set_status( 'cancelled' );

			$this->add_note( sprintf( __( '%s payment plan completed. Membership will expire at end of current term.', 'rcp' ), $membership_level->name ) );

		}

		/*
		 * Set completion date.
		 */
		$this->set_payment_plan_completed_date();

		return true;

	}

	/**
	 * Determines whether or not this membership has been disabled.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function is_disabled() {
		return ! empty( $this->disabled );
	}

	/**
	 * Disable the membership. This does the following:
	 *
	 *        - Cancels the payment profile to stop recurring billing.
	 *        - Disables the membership so the customer loses access to associated content.
	 *        - Removes the associated role from the user account.
	 *        - Hides the membership from the customer so it can no longer be renewed or cancelled.
	 *        - This is basically a way of deleting the membership while still keeping it in the database.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function disable() {

		rcp_log( sprintf( 'Disabling membership #%d.', $this->get_id() ) );

		/**
		 * Add a note.
		 */
		$this->add_note( __( 'Membership disabled.', 'rcp' ) );

		/**
		 * Disable the membership so the customer loses access.
		 */
		$this->update( array( 'disabled' => 1 ) );

		/**
		 * Cancel recurring payments.
		 */
		if ( $this->can_cancel() ) {
			$this->cancel_payment_profile( true );
		}

		/**
		 * Remove associated user role.
		 */
		$old_role         = get_option( 'default_role', 'subscriber' );
		$membership_level = rcp_get_subscription_details( $this->get_object_id() );
		$old_role         = ! empty( $membership_level->role ) ? $membership_level->role : $old_role;
		$user             = new WP_User( $this->get_customer()->get_user_id() );
		$user->remove_role( $old_role );

		/**
		 * Runs after the membership has been disabled.
		 *
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		do_action( 'rcp_membership_post_disable', $this->get_id(), $this );

	}

	/**
	 * Enable a membership. This does the following:
	 *
	 *        - The membership is re-granted access to associated content (provided membership is still active).
	 *        - The customer is able to view this membership again and renew if desired.
	 *        - The user role is reapplied to the account.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function enable() {

		$this->update( array( 'disabled' => null ) );

		/**
		 * Add associated user orle.
		 */
		$membership_level = rcp_get_subscription_details( $this->get_object_id() );
		$role             = ! empty( $membership_level->role ) ? $membership_level->role : get_option( 'default_role', 'subscriber' );
		$user             = new WP_User( $this->get_customer()->get_user_id() );
		$user->add_role( apply_filters( 'rcp_default_user_level', $role, $membership_level->id ) );

	}

	/**
	 * Changes the membership status to "cancelled".
	 *
	 * Does NOT handle actual cancellation of subscription payments, that is done in rcp_process_member_cancellation().
	 * This should be called after a member is successfully cancelled.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function cancel() {

		if ( 'cancelled' === $this->get_status() ) {
			return; // Bail if already set to cancelled
		}

		if ( has_action( 'rcp_member_pre_cancel' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_pre_cancel` instead.
			 */
			do_action( 'rcp_member_pre_cancel', $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Triggers before the membership is cancelled.
		 *
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		do_action( 'rcp_membership_pre_cancel', $this->get_id(), $this );

		// Change status.
		$this->set_status( 'cancelled' );

		if ( has_action( 'rcp_member_post_cancel' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_post_cancel` instead.
			 */
			do_action( 'rcp_member_post_cancel', $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Triggers after the membership is cancelled.
		 *
		 * This triggers the cancellation email.
		 *
		 * @see   rcp_email_on_membership_cancellation()
		 *
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		do_action( 'rcp_membership_post_cancel', $this->get_id(), $this );

	}

	/**
	 * Determines if the automatically recurring membership can be cancelled on site.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool True if the membership can be cancelled, false if not.
	 */
	public function can_cancel() {

		$can_cancel = false;

		if ( $this->is_recurring() && 'active' == $this->get_status() && $this->is_paid() && ! $this->is_expired() ) {

			// Check if the membership is a Stripe customer
			if ( 'stripe' == $this->get_gateway() || 'stripe_checkout' == $this->get_gateway() ) {

				$can_cancel = true;

			} elseif ( 'paypal' == $this->get_gateway() && rcp_has_paypal_api_access() ) {

				$can_cancel = true;

			} elseif ( 'twocheckout' == $this->get_gateway() && defined( 'TWOCHECKOUT_ADMIN_USER' ) && defined( 'TWOCHECKOUT_ADMIN_PASSWORD' ) ) {

				$can_cancel = true;

			} elseif ( 'authorizenet' == $this->get_gateway() && rcp_has_authnet_api_access() ) {

				$can_cancel = true;

			} elseif ( 'braintree' == $this->get_gateway() && rcp_has_braintree_api_access() ) {

				$can_cancel = true;

			}

		}

		if ( has_filter( 'rcp_member_can_cancel' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_can_cancel` instead.
			 */
			$can_cancel = apply_filters( 'rcp_member_can_cancel', $can_cancel, $this->get_member()->ID );
		}

		/**
		 * Filters whether or not the membership can be cancelled.
		 *
		 * @param bool           $can_cancel    Whether or not this membership can be cancelled.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$can_cancel = apply_filters( 'rcp_membership_can_cancel', $can_cancel, $this->get_id(), $this );

		rcp_log( sprintf( '"Can cancel" status for membership #%d: %s.', $this->get_id(), var_export( $can_cancel, true ) ) );

		return $can_cancel;

	}

	/**
	 * Cancel the payment profile at the gateway.
	 *
	 * @param bool $set_status Whether or not to update the membership status to "cancelled".
	 *
	 * @access public
	 * @since  3.0
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function cancel_payment_profile( $set_status = true ) {

		$success                 = new WP_Error;
		$gateway_subscription_id = $this->get_gateway_subscription_id();

		if ( ! $this->can_cancel() ) {
			rcp_log( sprintf( 'Unable to cancel payment profile for membership #%d.', $this->get_id() ) );

			return new WP_Error( 'ineligible_to_cancel', __( 'Membership is not eligible for payment profile cancellation.', 'rcp' ) );
		}

		if ( 'stripe' == $this->get_gateway() || 'stripe_checkout' == $this->get_gateway() ) {

			/**
			 * Cancel Stripe.
			 */

			if ( ! empty( $gateway_subscription_id ) ) {
				$cancelled = rcp_stripe_cancel_membership( $gateway_subscription_id );

				if ( is_wp_error( $cancelled ) ) {
					rcp_log( sprintf( 'Failed to cancel Stripe payment profile for membership #%d. Error code: %s; Error Message: %s.', $this->get_id(), $cancelled->get_error_code(), $cancelled->get_error_message() ) );

					$success = $cancelled;
				} else {
					$success = true;
				}
			} else {
				rcp_log( sprintf( 'Failed to cancel Stripe payment profile for membership #%d. Missing payment profile ID.' ) );

				$success = new WP_Error( 'missing_payment_profile_id', __( 'Missing subscription ID.', 'rcp' ) );
			}

		} elseif ( false !== strpos( 'paypal', $this->get_gateway() ) ) {

			/**
			 * Cancel PayPal.
			 */

			$cancelled = rcp_paypal_cancel_membership( $gateway_subscription_id );

			if ( is_wp_error( $cancelled ) ) {

				rcp_log( sprintf( 'Failed to cancel PayPal payment profile for membership #%d. Error code: %s; Error Message: %s.', $this->get_id(), $cancelled->get_error_code(), $cancelled->get_error_message() ) );

				$success = $cancelled;

			} else {
				$success = true;
			}

		} elseif ( 'twocheckout' == $this->get_gateway() ) {

			/**
			 * Cancel 2Checkout
			 */

			$cancelled = rcp_2checkout_cancel_membership( $gateway_subscription_id );

			if ( is_wp_error( $cancelled ) ) {

				rcp_log( sprintf( 'Failed to cancel 2Checkout payment profile for membership #%d. Error code: %s; Error Message: %s.', $this->get_id(), $cancelled->get_error_code(), $cancelled->get_error_message() ) );

				$success = $cancelled;

			} else {
				$success = true;
			}

		} elseif ( 'authorizenet' == $this->get_gateway() ) {

			/**
			 * Cancel Authorize.net
			 */

			$cancelled = rcp_authnet_cancel_membership( $gateway_subscription_id );

			if ( is_wp_error( $cancelled ) ) {

				rcp_log( sprintf( 'Failed to cancel Authorize.net payment profile for membership #%d. Error code: %s; Error Message: %s.', $this->get_id(), $cancelled->get_error_code(), $cancelled->get_error_message() ) );

				$success = $cancelled;

			} else {
				$success = true;
			}

		} elseif ( 'braintree' == $this->get_gateway() ) {

			/**
			 * Cancel Braintree
			 */

			$cancelled = rcp_braintree_cancel_membership( $gateway_subscription_id );

			if ( is_wp_error( $cancelled ) ) {

				rcp_log( sprintf( 'Failed to cancel Braintree payment profile for membership #%d. Error code: %s; Error Message: %s.', $this->get_id(), $cancelled->get_error_code(), $cancelled->get_error_message() ) );

				$success = $cancelled;

			} else {
				$success = true;
			}

		}

		/**
		 * Filters whether or not the cancellation was successful. If developing a third party gateway
		 * you'd use this filter to process cancellations, then return `true` on success or `WP_Error`
		 * on failure.
		 *
		 * @param true|WP_Error  $success                 Whether or not the cancellation was successful.
		 * @param string         $gateway                 Payment gateway for this membership.
		 * @param string         $gateway_subscription_id Gateway subscription ID.
		 * @param int            $membership_id           ID of the membership.
		 * @param RCP_Membership $this                    Membership object.
		 *
		 * @since 3.0
		 */
		$success = apply_filters( 'rcp_membership_payment_profile_cancelled', $success, $this->get_gateway(), $gateway_subscription_id, $this->get_id(), $this );

		if ( true === $success && $set_status ) {
			$this->cancel();
		}

		if ( true === $success ) {
			rcp_log( sprintf( 'Payment profile successfully cancelled for membership #%d.', $this->get_id() ) );
		} elseif ( is_wp_error( $success ) ) {
			$this->add_note( sprintf( __( 'Failed cancelling payment profile. Error code: %s; Error Message: %s.', 'rcp' ), $success->get_error_code(), $success->get_error_message() ) );
			rcp_log( sprintf( 'Failed cancelling payment profile for membership #%d.', $this->get_id() ) );
		}

		return $success;

	}

	/**
	 * Expire the membership. This sets the expiration date to yesterday.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function expire() {

		rcp_log( sprintf( 'Manually expiring membership #%d.', $this->get_id() ) );

		$this->set_status( 'expired' );
		$this->set_expiration_date( date( 'Y-m-d H:i:s', strtotime( '-1 day', current_time( 'timestamp' ) ) ) );

	}

	/**
	 * Get all the membership notes.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_notes() {
		return $this->notes;
	}

	/**
	 * Add a new membership note.
	 *
	 * @param string $note New note to add.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool Whether or not the note was successfully added.
	 */
	public function add_note( $note = '' ) {

		$notes = $this->get_notes();

		if ( empty( $notes ) ) {
			$notes = '';
		} else {
			$notes .= "\n\n";
		}

		$notes .= date_i18n( 'F j, Y H:i:s', current_time( 'timestamp' ) ) . ' - ' . $note;

		return $this->update( array( 'notes' => $notes ) );

	}

	/**
	 * Get the signup method for this membership. Options include: live, manual, imported.
	 *
	 * @access public
	 * @since  3.0
	 * @return string
	 */
	public function get_signup_method() {
		return $this->signup_method;
	}

	/**
	 * Check to see if this membership has access to view a certain post.
	 *
	 * @param int $post_id Post ID to check access for.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function can_access( $post_id = 0 ) {

		// If the post is unrestricted, everyone gets access.
		if ( ! rcp_is_restricted_content( $post_id ) ) {
			return apply_filters( 'rcp_membership_can_access', true, $this->get_id(), $post_id, $this );
		}

		// If the membership isn't active then they don't get access.
		if ( $this->is_expired() || ! $this->is_active() ) {
			return apply_filters( 'rcp_membership_can_access', false, $this->get_id(), $post_id, $this );
		}

		$post_type_restrictions = rcp_get_post_type_restrictions( get_post_type( $post_id ) );
		$membership_level_id    = $this->get_object_id();

		// Post or post type restrictions.
		if ( empty( $post_type_restrictions ) ) {
			$membership_levels = rcp_get_content_subscription_levels( $post_id );
			$access_level      = get_post_meta( $post_id, 'rcp_access_level', true );
			$user_level        = get_post_meta( $post_id, 'rcp_user_level', true );
		} else {
			$membership_levels = array_key_exists( 'subscription_level', $post_type_restrictions ) ? $post_type_restrictions['subscription_level'] : false;
			$access_level      = array_key_exists( 'access_level', $post_type_restrictions ) ? $post_type_restrictions['access_level'] : false;
			$user_level        = array_key_exists( 'user_level', $post_type_restrictions ) ? $post_type_restrictions['user_level'] : false;
		}

		// Check that user level is an array for backwards compatibility.
		if ( ! empty( $user_level ) && ! is_array( $user_level ) ) {
			$user_level = array( $user_level );
		}

		// Assume it access until proven otherwise.
		$can_access = true;

		// Check membership level restrictions.
		if ( ! empty( $membership_levels ) ) {

			if ( is_string( $membership_levels ) ) {

				switch ( $membership_levels ) {

					case 'any' :
						$can_access = ! empty( $membership_level_id );
						break;

					case 'any-paid' :
						$can_access = $this->is_paid();
						break;
				}

			} else {

				$can_access = in_array( $membership_level_id, $membership_levels );

			}
		}

		// Check post access level restrictions.
		if ( ! $this->has_access_level( $access_level ) && $access_level > 0 ) {
			$can_access = false;
		}

		// Check post user role restrictions. User needs at least one of the selected roles.
		if ( $can_access && ! empty( $user_level ) && 'all' != strtolower( $user_level[0] ) ) {
			foreach ( $user_level as $role ) {
				if ( user_can( $this->get_customer()->get_user_id(), strtolower( $role ) ) ) {
					$ret = true;
					$can_access;
				} else {
					$can_access = false;
				}
			}
		}

		// Check term restrictions.
		$has_post_restrictions    = rcp_has_post_restrictions( $post_id );
		$term_restricted_post_ids = rcp_get_post_ids_assigned_to_restricted_terms();

		// since no post-level restrictions, check to see if user is restricted via term
		if ( $can_access && ! $has_post_restrictions && in_array( $post_id, $term_restricted_post_ids ) ) {

			$restricted = false;

			$terms = (array) rcp_get_connected_term_ids( $post_id );

			if ( ! empty( $terms ) ) {

				foreach ( $terms as $term_id ) {

					$restrictions = rcp_get_term_restrictions( $term_id );

					if ( empty( $restrictions['paid_only'] ) && empty( $restrictions['subscriptions'] ) && ( empty( $restrictions['access_level'] ) || 'None' == $restrictions['access_level'] ) ) {
						if ( count( $terms ) === 1 ) {
							break;
						}
						continue;
					}

					// If only the Paid Only box is checked, check for active, paid subscription and return early if so.
					if ( ! $restricted && ! empty( $restrictions['paid_only'] ) && empty( $restrictions['subscriptions'] ) && empty( $restrictions['access_level'] ) && ( ! $this->is_active() || ! $this->is_paid() ) ) {
						$restricted = true;
						break;
					}

					if ( ! $restricted && ! empty( $restrictions['subscriptions'] ) && ! in_array( $this->get_object_id(), $restrictions['subscriptions'] ) ) {
						$restricted = true;
						break;
					}

					if ( ! $restricted && ! empty( $restrictions['access_level'] ) && 'None' !== $restrictions['access_level'] ) {
						if ( $restrictions['access_level'] > 0 && ! $this->has_access_level( $restrictions['access_level'] ) ) {
							$restricted = true;
							break;
						}
					}
				}
			}

			if ( $restricted ) {
				$can_access = false;
			}

			// since user doesn't pass post-level restrictions, see if user is allowed via term
		} else if ( ! $can_access && $has_post_restrictions && in_array( $post_id, $term_restricted_post_ids ) ) {

			$allowed = false;

			$terms = (array) rcp_get_connected_term_ids( $post_id );

			if ( ! empty( $terms ) ) {

				foreach ( $terms as $term_id ) {

					$restrictions = rcp_get_term_restrictions( $term_id );

					if ( empty( $restrictions['paid_only'] ) && empty( $restrictions['subscriptions'] ) && ( empty( $restrictions['access_level'] ) || 'None' == $restrictions['access_level'] ) ) {
						if ( count( $terms ) === 1 ) {
							break;
						}
						continue;
					}

					// If only the Paid Only box is checked, check for paid, active subscription and return early if so.
					if ( ! $allowed && ! empty( $restrictions['paid_only'] ) && empty( $restrictions['subscriptions'] ) && empty( $restrictions['access_level'] ) && $this->is_active() && $this->is_paid() ) {
						$allowed = true;
						break;
					}

					if ( ! $allowed && ! empty( $restrictions['subscriptions'] ) && in_array( $this->get_object_id(), $restrictions['subscriptions'] ) ) {
						$allowed = true;
						break;
					}

					if ( ! $allowed && ! empty( $restrictions['access_level'] ) && 'None' !== $restrictions['access_level'] ) {
						if ( $restrictions['access_level'] > 0 && $this->has_access_level( $restrictions['access_level'] ) ) {
							$allowed = true;
							break;
						}
					}
				}
			}

			if ( $allowed ) {
				$can_access = true;
			}
		}

		/**
		 * Filters whether or not this membership has access to the given post.
		 *
		 * @param bool           $can_access    True if it has access, false if not.
		 * @param int            $membership_id ID of the membership.
		 * @param int            $post_id       ID of the post to check.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		return apply_filters( 'rcp_membership_can_access', $can_access, $this->get_id(), $post_id, $this );

	}

	/**
	 * Determines if this membership has a specific access level or higher.
	 *
	 * @param int $access_level_needed Level to check.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function has_access_level( $access_level_needed = 0 ) {

		$membership_level_id     = $this->get_object_id();
		$membership_level_access = rcp_get_subscription_access_level( $membership_level_id );

		if ( ( $membership_level_access >= $access_level_needed ) || $access_level_needed == 0 ) {
			// The membership has the access level or higher.
			return true;
		}

		// The membership does not have access level.
		return false;

	}

	/**
	 * Get the prorate credit amount for the customer's remaining membership.
	 *
	 * @access public
	 * @since  3.0
	 * @return int|float
	 */
	public function get_prorate_credit_amount() {

		// Make sure this is an active, paid membership.
		if ( ! $this->is_active() || ! $this->is_paid() ) {
			return 0;
		}

		/**
		 * @deprecated 3.0 Use `rcp_membership_disable_prorate_credit` instead.
		 */
		if ( apply_filters( 'rcp_disable_prorate_credit', false, $this->get_member() ) ) {
			return 0;
		}

		/**
		 * Set to `true` to disable prorated credits.
		 *
		 * @param bool           $disable_credit
		 * @param RCP_Membership $this
		 *
		 * @since 3.0
		 */
		if ( apply_filters( 'rcp_membership_disable_prorate_credit', false, $this ) ) {
			return 0;
		}

		// Get the most recent payment.
		foreach ( $this->get_payments() as $pmt ) {
			if ( 'complete' != $pmt->status ) {
				continue;
			}

			$payment = $pmt;
			break;
		}

		if ( empty( $payment ) ) {
			return 0;
		}

		if ( ! empty( $payment->object_id ) ) {
			$membership_level_id = absint( $payment->object_id );
			$membership_level    = rcp_get_subscription_details( $membership_level_id );
		} else {
			$membership_level_id = rcp_get_subscription_details_by_name( $payment->subscription );
			$membership_level    = $this->get_object_id();
		}

		// Make sure the membership payment matches the existing membership.
		if ( empty( $membership_level->id ) || empty( $membership_level->duration ) || $membership_level->id != $membership_level_id ) {
			return 0;
		}

		$exp_date = $this->get_expiration_date( false );

		// If this is member does not have an expiration date, they don't get any credits.
		if ( 'none' == $exp_date ) {
			return 0;
		}

		// Make sure we have a valid date.
		if ( ! $exp_date = strtotime( $exp_date ) ) {
			return 0;
		}

		$exp_date_dt = date( 'Y-m-d', $exp_date ) . ' 23:59:59';
		$exp_date    = strtotime( $exp_date_dt, current_time( 'timestamp' ) );

		$time_remaining = $exp_date - current_time( 'timestamp' );

		// Calculate the start date based on the expiration date.
		if ( ! $start_date = strtotime( $exp_date_dt . ' -' . $membership_level->duration . $membership_level->duration_unit, current_time( 'timestamp' ) ) ) {
			return 0;
		}

		$total_time = $exp_date - $start_date;

		if ( $time_remaining <= 0 ) {
			return 0;
		}

		/*
		 * Calculate discount as percentage of membership remaining.
		 * Use the subtotal from their last payment as the base price. This is the amount without discounts/credits/fees applied.
		 * This was only added in version 2.9, so we use the full amount as a fallback in case the subtotal doesn't exist for the last payment.
		 */
		$payment_amount       = ! empty( $payment->subtotal ) ? abs( $payment->subtotal ) : abs( $payment->amount -= $membership_level->fee );
		$percentage_remaining = $time_remaining / $total_time;

		// make sure we don't credit more than 100%
		if ( $percentage_remaining > 1 ) {
			$percentage_remaining = 1;
		}

		$discount = round( $payment_amount * $percentage_remaining, 2 );

		// Make sure they get a discount. This shouldn't ever run.
		if ( ! $discount > 0 ) {
			$discount = $payment_amount;
		}

		$discount = floatval( $discount );

		if ( has_filter( 'rcp_member_prorate_credit' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_get_prorate_credit` instead.
			 */
			$discount = apply_filters( 'rcp_member_prorate_credit', $discount, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the prorate credit amount.
		 *
		 * @param float          $discount      Discount amount.
		 * @param int            $membership_id Membership ID number.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$discount = apply_filters( 'rcp_membership_get_prorate_credit', $discount, $this->get_id(), $this );

		return $discount;

	}

	/**
	 * Get the payments associated with this membership.
	 *
	 * @param array $args Query arguments to override the defaults.
	 *
	 * @access public
	 * @since  3.0
	 * @return array
	 */
	public function get_payments( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'membership_id' => $this->get_id()
		) );

		/**
		 * @var RCP_Payments $rcp_payments_db
		 */
		global $rcp_payments_db;

		$payments = $rcp_payments_db->get_payments( $args );

		return $payments;

	}

	/**
	 * Returns the card details associated with this membership.
	 *
	 * @access public
	 * @since  3.0
	 * @return array
	 */
	public function get_card_details() {

		$card_details = array();

		if ( has_filter( 'rcp_get_card_details' ) ) {
			/**
			 * @deprecated 3.0 Use `rcp_membership_get_card_details` instead.
			 */
			$card_details = apply_filters( 'rcp_get_card_details', $card_details, $this->get_member()->ID, $this->get_member() );
		}

		/**
		 * Filters the card details on file. Each payment gateway hooks into this to retrieve the details from the
		 * payment gateway API.
		 *
		 * @param array          $card_details  Array of card details.
		 * @param int            $membership_id ID of the membership.
		 * @param RCP_Membership $this          Membership object.
		 *
		 * @since 3.0
		 */
		$card_details = apply_filters( 'rcp_membership_get_card_details', $card_details, $this->get_id(), $this );

		return $card_details;

	}

	/**
	 * Determines whether or not the billing card associated with this membership can be updated.
	 *
	 * @access public
	 * @since  3.0
	 * @return bool
	 */
	public function can_update_billing_card() {

		$can_update = false;

		if ( rcp_is_stripe_membership( $this ) ) {
			$can_update = true;
		} elseif ( rcp_is_paypal_membership( $this ) && rcp_has_paypal_api_access() ) {
			$can_update = true;
		} elseif ( rcp_is_authnet_membership( $this ) && rcp_has_authnet_api_access() ) {
			$can_update = true;
		}

		if ( has_filter( 'rcp_member_can_update_billing_card' ) ) {
			/**
			 * @deprecated 3.0
			 */
			$can_update = apply_filters( 'rcp_member_can_update_billing_card', $can_update, $this->get_customer()->get_user_id() );
		}

		return $can_update;

	}

	/**
	 * Increment the number of times this membership has been billed for.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function increment_times_billed() {

		$times_billed = $this->get_times_billed();
		$times_billed++;

		$this->update( array(
			'times_billed' => absint( $times_billed )
		) );

		/*
		 * Complete payment plan.
		 */
		if ( $this->has_payment_plan() && $this->at_maximum_renewals() && ! $this->is_payment_plan_complete() ) {
			$this->complete_payment_plan();
		}

	}

}