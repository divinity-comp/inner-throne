<?php
/**
 * Discount Functions
 *
 * Functions for getting non-member specific info about discount codes.
 *
 * @package     Restrict Content Pro
 * @subpackage  Discount Functions
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Retrieves all discount codes
 *
 * @param array $args Arguments to override the defaults.
 *
 * @return array|bool
 */
function rcp_get_discounts( $args = array() ) {
	$discounts_db = new RCP_Discounts();
	$discounts    = $discounts_db->get_discounts( $args );
	if( $discounts ) {
		return $discounts;
	}
	return false;
}

/**
 * Check if we have any discounts
 *
 * @return bool
 */
function rcp_has_discounts() {
	$discounts = new RCP_Discounts();
	if( $discounts->get_discounts( array( 'status' => 'active' )) )
		return true;
	return false;
}

/**
 * Returns the DB object for a given discount code.
 *
 * @param int $id The ID number of the discount to retrieve data for.
 *
 * @return object
 */
function rcp_get_discount_details( $id ) {
	global $wpdb, $rcp_discounts_db_name;
	$code = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $rcp_discounts_db_name . " WHERE id='%d';", $id ) );
	return $code[0];
}

/**
 * Returns the DB object for a discount code, based on the code provided.
 *
 * @param string $code The discount code to retrieve all information for.
 *
 * @return object
 */
function rcp_get_discount_details_by_code( $code ) {
	global $wpdb, $rcp_discounts_db_name;
	$code = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $rcp_discounts_db_name . " WHERE code='%s';", strtolower( $code ) ) );
	return $code[0];
}

/**
 * Check whether a given discount code is valid.
 *
 * @param string $code            The discount code to validate.
 * @param int    $subscription_id ID of the membership level you want to use the code for.
 *
 * @return bool
 */
function rcp_validate_discount( $code, $subscription_id = 0 ) {

	$ret       = false;
	$discounts = new RCP_Discounts();
	$discount  = $discounts->get_by( 'code', strtolower( $code ) );

	if( ! empty( $discount ) && $discount->status == 'active' ) {

		// Make sure discount is not expired and not maxed out
		if( ! $discounts->is_expired( $discount->id ) && ! $discounts->is_maxed_out( $discount->id ) ) {
			$ret = true;
		}

		// If the discount is restricted to a level(s), ensure the level being signed up for is included.
		if( $discounts->has_membership_level_ids( $discount->id ) ) {
			$approved_ids = $discounts->get_membership_level_ids( $discount->id );

			if( ! in_array( $subscription_id, $approved_ids ) ) {
				$ret = false;
			}
		}

		// Ensure codes match, case insensitive
		if( strcasecmp( $code, $discount->code ) != 0 ) {
			$ret = false;
		}

	}

	return apply_filters( 'rcp_is_discount_valid', $ret, $discount, $subscription_id );
}

/**
 * Get the status of a discount code
 *
 * @param int $code_id The discount code ID.
 *
 * @return string|bool Status name on success, false on failure.
 */
function rcp_get_discount_status( $code_id ) {
	global $wpdb, $rcp_discounts_db_name;

	$code = rcp_get_discount_details( $code_id );
	if( $code ) {
		return $code->status;
	}
	return false;
}

/**
 * Checks whether a discount code has any uses left.
 *
 * @param int $code_id The ID of the discount code to check.
 *
 * @return bool True if uses left, false otherwise.
 */
function rcp_discount_has_uses_left( $code_id ) {
	global $wpdb, $rcp_discounts_db_name;

	$usage = $wpdb->get_results( $wpdb->prepare( "SELECT `use_count`, `max_uses` FROM " . $rcp_discounts_db_name . " WHERE id='%d';", $code_id ) );

	if( $usage ) {
		$use_count = $usage[0]->use_count;
		$max_uses = $usage[0]->max_uses;
		if( $max_uses > 0 ) {
			if( $use_count < $max_uses ) {
				return true;
			}
		} else {
			// this code has unlimited uses
			return true;
		}
	}
	return false;
}

/**
 * Checks whether a discount code has not expired.
 *
 * @param int $code_id The ID of the discount code to check.
 *
 * @return bool True if not expired, false if expired.
 */
function rcp_is_discount_not_expired( $code_id ) {
	global $wpdb, $rcp_discounts_db_name;
	$expiration = $wpdb->get_results( $wpdb->prepare( "SELECT expiration FROM " . $rcp_discounts_db_name . " WHERE id='%d';", $code_id ) );

	// if no expiration is set, return true
	if( $expiration[0]->expiration == '' )
		return true;

	if( $expiration ) {
		if ( strtotime( 'NOW', current_time( 'timestamp' ) ) < strtotime( $expiration[0]->expiration, current_time( 'timestamp' ) ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Calculates a subscription price after applying a discount.
 *
 * @param float $base_price The original subscription price.
 * @param float $amount The discount amount.
 * @param string $type The kind of discount, either '%' or 'flat'.
 *
 * @return string
 */
function rcp_get_discounted_price( $base_price, $amount, $type ) {

	if( $type == '%' ) {
		$discounted_price = $base_price - ( $base_price * ( $amount / 100 ) );
	} elseif($type == 'flat') {
		$discounted_price = $base_price - $amount;
	}

	return number_format( (float) $discounted_price, 2 );
}

/**
 * Stores a discount code in a user's history.
 *
 * @param string $code            The discount code to store.
 * @param int    $user_id         The ID of the user to store the discount for.
 * @param object $discount_object The object containing all info about the discount.
 *
 * @return void
 */
function rcp_store_discount_use_for_user( $code, $user_id, $discount_object ) {

	$user_discounts = get_user_meta( $user_id, 'rcp_user_discounts', true) ;

	if( !is_array( $user_discounts ) )
		$user_discounts = array();

	$user_discounts[] = $code;

	do_action( 'rcp_pre_store_discount_for_user', $code, $user_id, $discount_object );

	update_user_meta( $user_id, 'rcp_user_discounts', $user_discounts );

	do_action( 'rcp_store_discount_for_user', $code, $user_id, $discount_object );

}

/**
 * Checks whether a user has used a particular discount code.
 * This is used to prevent users from spamming discount codes.
 *
 * @param int    $user_id The ID of the user to check.
 * @param string $code    The discount code to check against the user ID.
 *
 * @return bool
 */
function rcp_user_has_used_discount( $user_id, $code ) {

	$ret = false;

	if( ! empty( $code ) ) {

		$user_discounts = get_user_meta( $user_id, 'rcp_user_discounts', true );

		if( ! empty( $user_discounts ) ) {
			if( in_array( $code, $user_discounts ) ) {
				$ret = true;
			}
		}
	}

	return apply_filters( 'rcp_user_has_used_discount', $ret, $user_id, $code );
}

/**
 * Increase the usage count of a discount code.
 *
 * @param int $code_id The ID of the discount.
 *
 * @return void
 */
function rcp_increase_code_use( $code_id ) {
	global $wpdb, $rcp_discounts_db_name;
	// add the post ID to the count database if it doesn't already exist
	if( ! $wpdb->query( $wpdb->prepare( "SELECT `use_count` FROM `" . $rcp_discounts_db_name . "` WHERE id='%d';", $code_id ) ) ) {
		$increase_count = $wpdb->insert( $rcp_discounts_db_name,
			array(
				'id' => $code_id,
				'use_count' => 1
			)
		);
	} else {
		$count = $wpdb->query( $wpdb->prepare( "UPDATE " . $rcp_discounts_db_name . " SET use_count = use_count + 1 WHERE id='%d';", $code_id ) );
	}
}

/**
 * Returns the number of times a discount code has been used.
 *
 * @param int|string $code The ID or code of the discount.
 *
 * @return int|string The number of times the discount code has been used or the string 'None'.
 */
function rcp_count_discount_code_uses( $code ) {
	global $wpdb, $rcp_discounts_db_name;
	if( is_int( $code ) ) {
		// discount ID has been given
		$count = $wpdb->get_results( $wpdb->prepare( "SELECT use_count FROM " . $rcp_discounts_db_name . " WHERE id='%d';", $code ) );
	} else {
		// discount code has been given
		$count = $wpdb->get_results( $wpdb->prepare( "SELECT use_count FROM " . $rcp_discounts_db_name . " WHERE code='%s';", $code ) );
	}
	if($count)
		return $count[0]->use_count;
	else
		return __( 'None', 'rcp' );
}

/**
 * Returns a formatted discount amount with a '%' sign appended (percentage-based) or with the
 * currency sign added to the amount (flat discount rate).
 *
 * @param float  $amount Discount amount.
 * @param string $type   Discount amount - either '%' or 'flat'.
 *
 * @return string
 */
function rcp_discount_sign_filter( $amount, $type ) {
	$discount = '';
	if( $type == '%' ) {
		$discount = $amount . '%';
	} elseif( $type == 'flat' ) {
		$discount = rcp_currency_filter( $amount );
	}
	return $discount;
}

