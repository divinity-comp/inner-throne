<?php
/**
 * Authorize.net Functions
 *
 * @package     Restrict Content Pro
 * @subpackage  Gateways/Authorize.net/Functions
 * @copyright   Copyright (c) 2017, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7
 */

/**
 * Cancel an Authorize.net subscriber
 *
 * @deprecated 3.0 Use `rcp_authnet_cancel_membership` instead.
 * @see rcp_authnet_cancel_membership()
 *
 * @param int $member_id ID of the member to cancel.
 *
 * @access      private
 * @since       2.7
 * @return      bool|WP_Error
 */
function rcp_authnet_cancel_member( $member_id = 0 ) {

	$customer = rcp_get_customer_by_user_id( $member_id );

	if ( empty( $customer ) ) {
		return new WP_Error( 'rcp_authnet_error', __( 'Unable to find customer from member ID.', 'rcp' ) );
	}

	$membership = rcp_get_customer_single_membership( $customer->get_id() );
	$profile_id = str_replace( 'anet_', '', $membership->get_gateway_subscription_id() );

	return rcp_authnet_cancel_membership( $profile_id );
}


/**
 * Determine if a member is an Authorize.net Customer
 *
 * @deprecated 3.0 Use `rcp_is_authnet_membership()` instead.
 * @see rcp_is_authnet_membership()
 *
 * @param int $user_id The ID of the user to check
 *
 * @since       2.7
 * @access      public
 * @return      bool
*/
function rcp_is_authnet_subscriber( $user_id = 0 ) {

	if( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$ret = false;

	$customer = rcp_get_customer_by_user_id( $user_id );

	if ( ! empty( $customer ) ) {
		$membership = rcp_get_customer_single_membership( $customer->get_id() );

		if ( ! empty( $membership ) ) {
			$ret = rcp_is_authnet_membership( $membership );
		}
	}

	return (bool) apply_filters( 'rcp_is_authorizenet_subscriber', $ret, $user_id );
}

/**
 * Determines if a membership is an Authorize.net subscription.
 *
 * @param int|RCP_Membership $membership_object_or_id Membership ID or object.
 *
 * @since 3.0
 * @return bool
 */
function rcp_is_authnet_membership( $membership_object_or_id ) {

	if ( ! is_object( $membership_object_or_id ) ) {
		$membership = rcp_get_membership( $membership_object_or_id );
	} else {
		$membership = $membership_object_or_id;
	}

	$is_authnet = false;

	if ( ! empty( $membership ) && $membership->get_id() > 0 ) {
		$subscription_id = $membership->get_gateway_subscription_id();

		if ( false !== strpos( $subscription_id, 'anet_' ) ) {
			$is_authnet = true;
		}
	}

	/**
	 * Filters whether or not the membership is an Authorize.net subscription.
	 *
	 * @param bool           $is_authnet
	 * @param RCP_Membership $membership
	 *
	 * @since 3.0
	 */
	return (bool) apply_filters( 'rcp_is_authorizenet_membership', $is_authnet, $membership );

}

/**
 * Determine if all necessary API credentials are filled in
 *
 * @since  2.7
 * @return bool
 */
function rcp_has_authnet_api_access() {

	global $rcp_options;

	$ret = false;

	if ( rcp_is_sandbox() ) {
		$api_login_id    = $rcp_options['authorize_test_api_login'];
		$transaction_key = $rcp_options['authorize_test_txn_key'];
	} else {
		$api_login_id    = $rcp_options['authorize_api_login'];
		$transaction_key = $rcp_options['authorize_txn_key'];
	}

	if ( ! empty( $api_login_id ) && ! empty( $transaction_key ) ) {
		$ret = true;
	}

	return $ret;

}

/**
 * Process an update card form request for Authorize.net
 *
 * @deprecated 3.0 Use `rcp_authorizenet_update_membership_billing_card()` instead.
 * @see rcp_authorizenet_update_membership_billing_card()
 *
 * @param int        $member_id  ID of the member.
 * @param RCP_Member $member_obj Member object.
 *
 * @access      private
 * @since       2.7
 * @return      void
 */
function rcp_authorizenet_update_billing_card( $member_id, $member_obj ) {

	if( empty( $member_id ) ) {
		return;
	}

	if( ! is_a( $member_obj, 'RCP_Member' ) ) {
		return;
	}

	$customer = rcp_get_customer_by_user_id( $member_id );

	if ( empty( $customer ) ) {
		return;
	}

	$membership = rcp_get_customer_single_membership( $customer->get_id() );

	if ( empty( $membership ) ) {
		return;
	}

	rcp_authorizenet_update_membership_billing_card( $membership );

}
//add_action( 'rcp_update_billing_card', 'rcp_authorizenet_update_billing_card', 10, 2 );

/**
 * Update the billing card for a given membership.
 *
 * @param RCP_Membership $membership
 *
 * @since 3.0
 * @return void
 */
function rcp_authorizenet_update_membership_billing_card( $membership ) {

	global $rcp_options;

	if ( ! is_a( $membership, 'RCP_Membership' ) ) {
		return;
	}

	if ( ! rcp_is_authnet_membership( $membership ) ) {
		return;
	}

	require_once RCP_PLUGIN_DIR . 'includes/libraries/anet_php_sdk/autoload.php';

	if ( rcp_is_sandbox() ) {
		$api_login_id    = isset( $rcp_options['authorize_test_api_login'] ) ? sanitize_text_field( $rcp_options['authorize_test_api_login'] ) : '';
		$transaction_key = isset( $rcp_options['authorize_test_txn_key'] ) ? sanitize_text_field( $rcp_options['authorize_test_txn_key'] ) : '';
	} else {
		$api_login_id    = isset( $rcp_options['authorize_api_login'] ) ? sanitize_text_field( $rcp_options['authorize_api_login'] ) : '';
		$transaction_key = isset( $rcp_options['authorize_txn_key'] ) ? sanitize_text_field( $rcp_options['authorize_txn_key'] ) : '';
	}
	$md5_hash_value  = isset( $rcp_options['authorize_hash_value'] ) ? sanitize_text_field( $rcp_options['authorize_hash_value'] ) : '';

	$error          = '';
	$card_number    = isset( $_POST['rcp_card_number'] )    && is_numeric( $_POST['rcp_card_number'] )    ? sanitize_text_field( $_POST['rcp_card_number'] )    : '';
	$card_exp_month = isset( $_POST['rcp_card_exp_month'] ) && is_numeric( $_POST['rcp_card_exp_month'] ) ? sanitize_text_field( $_POST['rcp_card_exp_month'] ) : '';
	$card_exp_year  = isset( $_POST['rcp_card_exp_year'] )  && is_numeric( $_POST['rcp_card_exp_year'] )  ? sanitize_text_field( $_POST['rcp_card_exp_year'] )  : '';
	$card_cvc       = isset( $_POST['rcp_card_cvc'] )       && is_numeric( $_POST['rcp_card_cvc'] )       ? sanitize_text_field( $_POST['rcp_card_cvc'] )       : '';
	$card_zip       = isset( $_POST['rcp_card_zip'] ) ? sanitize_text_field( $_POST['rcp_card_zip'] ) : '' ;

	if ( empty( $card_number ) || empty( $card_exp_month ) || empty( $card_exp_year ) || empty( $card_cvc ) || empty( $card_zip ) ) {
		$error = __( 'Please enter all required fields.', 'rcp' );
	}

	if ( empty( $error ) ) {

		$profile_id = str_replace( 'anet_', '', $membership->get_gateway_subscription_id() );

		/**
		 * Create a merchantAuthenticationType object with authentication details.
		 */
		$merchant_authentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
		$merchant_authentication->setName( $api_login_id );
		$merchant_authentication->setTransactionKey( $transaction_key );

		/**
		 * Set the transaction's refId
		 */
		$refId = 'ref' . time();

		$subscription = new net\authorize\api\contract\v1\ARBSubscriptionType();

		/**
		 * Update card details.
		 */
		$credit_card = new net\authorize\api\contract\v1\CreditCardType();
		$credit_card->setCardNumber( $card_number );
		$credit_card->setExpirationDate( $card_exp_year . '-' . $card_exp_month );
		$credit_card->setCardCode( $card_cvc );

		$payment = new net\authorize\api\contract\v1\PaymentType();
		$payment->setCreditCard( $credit_card );

		$subscription->setPayment( $payment );

		/**
		 * Update the billing zip.
		 */
		$bill_to = new net\authorize\api\contract\v1\NameAndAddressType();
		$bill_to->setZip( $card_zip );
		$subscription->setBillTo( $bill_to );

		/**
		 * Make request to update details.
		 */
		$request = new net\authorize\api\contract\v1\ARBUpdateSubscriptionRequest();
		$request->setMerchantAuthentication( $merchant_authentication );
		$request->setRefId( $refId );
		$request->setSubscriptionId( $profile_id );
		$request->setSubscription( $subscription );

		$controller  = new net\authorize\api\controller\ARBCancelSubscriptionController( $request );
		$environment = rcp_is_sandbox() ? \net\authorize\api\constants\ANetEnvironment::SANDBOX : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
		$response    = $controller->executeWithApiResponse( $environment );

		/**
		 * An error occurred - get the error message.
		 */
		if( $response == null || $response->getMessages()->getResultCode() != "Ok" ) {
			$error_messages = $response->getMessages()->getMessage();
			$error          = $error_messages[0]->getCode() . "  " .$error_messages[0]->getText();
		}

	}

	if( ! empty( $error ) ) {
		wp_redirect( add_query_arg( array( 'card' => 'not-updated', 'msg' => urlencode( $error ) ) ) ); exit;
	}

	wp_redirect( add_query_arg( array( 'card' => 'updated', 'msg' => '' ) ) ); exit;

}
add_action( 'rcp_update_membership_billing_card', 'rcp_authorizenet_update_membership_billing_card' );

/**
 * Cancel an Authorize.net subscription based on the subscription ID.
 *
 * @param string $payment_profile_id Subscription ID.
 *
 * @since 3.0
 * @return true|WP_Error True on success, WP_Error on failure.
 */
function rcp_authnet_cancel_membership( $payment_profile_id ) {

	global $rcp_options;

	$ret = true;

	if ( rcp_is_sandbox() ) {
		$api_login_id    = isset( $rcp_options['authorize_test_api_login'] ) ? sanitize_text_field( $rcp_options['authorize_test_api_login'] ) : '';
		$transaction_key = isset( $rcp_options['authorize_test_txn_key'] ) ? sanitize_text_field( $rcp_options['authorize_test_txn_key'] ) : '';
	} else {
		$api_login_id    = isset( $rcp_options['authorize_api_login'] ) ? sanitize_text_field( $rcp_options['authorize_api_login'] ) : '';
		$transaction_key = isset( $rcp_options['authorize_txn_key'] ) ? sanitize_text_field( $rcp_options['authorize_txn_key'] ) : '';
	}
	$md5_hash_value  = isset( $rcp_options['authorize_hash_value'] ) ? sanitize_text_field( $rcp_options['authorize_hash_value'] ) : '';

	require_once RCP_PLUGIN_DIR . 'includes/libraries/anet_php_sdk/autoload.php';

	$profile_id = str_replace( 'anet_', '', $payment_profile_id );

	/**
	 * Create a merchantAuthenticationType object with authentication details.
	 */
	$merchant_authentication = new net\authorize\api\contract\v1\MerchantAuthenticationType();
	$merchant_authentication->setName( $api_login_id );
	$merchant_authentication->setTransactionKey( $transaction_key );

	/**
	 * Set the transaction's refId
	 */
	$refId = 'ref' . time();

	$request = new net\authorize\api\contract\v1\ARBCancelSubscriptionRequest();
	$request->setMerchantAuthentication( $merchant_authentication );
	$request->setRefId( $refId );
	$request->setSubscriptionId( $profile_id );

	/**
	 * Submit the request
	 */
	$controller  = new net\authorize\api\controller\ARBCancelSubscriptionController( $request );
	$environment = rcp_is_sandbox() ? \net\authorize\api\constants\ANetEnvironment::SANDBOX : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
	$response    = $controller->executeWithApiResponse( $environment );

	/**
	 * An error occurred - get the error message.
	 */
	if( $response == null || $response->getMessages()->getResultCode() != "Ok" ) {

		$error_messages = $response->getMessages()->getMessage();
		$error          = $error_messages[0]->getCode() . "  " .$error_messages[0]->getText();
		$ret            = new WP_Error( 'rcp_authnet_error', $error );

	}

	return $ret;

}