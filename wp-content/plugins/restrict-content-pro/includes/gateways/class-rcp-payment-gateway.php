<?php
/**
 * Payment Gateway Base Class
 *
 * You can extend this class to add support for a custom gateway.
 * @link http://docs.restrictcontentpro.com/article/1695-payment-gateway-api
 *
 * @package     Restrict Content Pro
 * @subpackage  Classes/Gateway
 * @copyright   Copyright (c) 2017, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
*/

class RCP_Payment_Gateway {

	/**
	 * Array of features the gateway supports, including:
	 *      one-time (one time payments)
	 *      recurring (recurring payments)
	 *      fees (setup fees)
	 *      trial (free trials)
	 *
	 * @var array
	 * @access public
	 */
	public $supports = array();

	/**
	 * The customer's email address
	 *
	 * @var string
	 * @access public
	 */
	public $email;

	/**
	 * The customer's user account ID
	 *
	 * @var int
	 * @access public
	 */
	public $user_id;

	/**
	 * The customer's username
	 *
	 * @var string
	 * @access public
	 */
	public $user_name;

	/**
	 * The selected currency code (i.e. "USD")
	 *
	 * @var string
	 * @access public
	 */
	public $currency;

	/**
	 * Recurring subscription amount
	 * This excludes any one-time fees or one-time discounts.
	 *
	 * @var int|float
	 */
	public $amount;

	/**
	 * Initial payment amount
	 * This is the amount to be billed for the first payment, including
	 * any one-time setup fees or one-time discounts.
	 *
	 * @var int|float
	 * @access public
	 */
	public $initial_amount;

	/**
	 * Total discounts applied to the payment
	 *
	 * @var int|float
	 * @access public
	 */
	public $discount;

	/**
	 * Subscription duration
	 *
	 * @var int
	 * @access public
	 */
	public $length;

	/**
	 * Subscription unit: day, month, or year
	 *
	 * @var string
	 * @access public
	 */
	public $length_unit;

	/**
	 * Signup fees to apply to the first payment
	 * (This number is included in $initial_amount)
	 *
	 * @var int|float
	 * @access public
	 */
	public $signup_fee;

	/**
	 * Subscription key
	 *
	 * @var string
	 * @access public
	 */
	public $subscription_key;

	/**
	 * Subscription ID number the customer is signing up for
	 *
	 * @var int
	 * @access public
	 */
	public $subscription_id;

	/**
	 * Name of the subscription the customer is signing up for
	 *
	 * @var string
	 * @access public
	 */
	public $subscription_name;

	/**
	 * Whether or not this registration is for a recurring subscription
	 *
	 * @var bool
	 * @access public
	 */
	public $auto_renew;

	/**
	 * URL to redirect the customer to after a successful registration
	 *
	 * @var string
	 * @access public
	 */
	public $return_url;

	/**
	 * Whether or not the site is in sandbox mode
	 *
	 * @var bool
	 * @access public
	 */
	public $test_mode;

	/**
	 * Array of all subscription data that's been passed to the gateway
	 *
	 * @var array
	 * @access public
	 */
	public $subscription_data;

	/**
	 * Webhook event ID (for example: the Stripe event ID)
	 * This may not always be populated
	 *
	 * @var string
	 * @access public
	 */
	public $webhook_event_id;

	/**
	 * Payment object for this transaction. Going into the gateway it's been
	 * create with the status 'pending' and will need to be updated after
	 * a successful payment.
	 *
	 * @var object
	 * @access public
	 * @since  2.9
	 */
	public $payment;

	/**
	 * Customer object for this user.
	 *
	 * @var RCP_Customer
	 * @access public
	 * @since 3.0
	 */
	public $customer;

	/**
	 * Membership object for this payment.
	 *
	 * @var RCP_Membership
	 * @access public
	 * @since 3.0
	 */
	public $membership;

	/**
	 * Used for saving an error message that occurs during registration.
	 *
	 * @var string
	 * @access public
	 * @since 2.9
	 */
	public $error_message;

	/**
	 * RCP_Payment_Gateway constructor.
	 *
	 * @param array $subscription_data Subscription data passed from rcp_process_registration()
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $subscription_data = array() ) {

		$this->test_mode = rcp_is_sandbox();
		$this->init();

		if( ! empty( $subscription_data ) ) {

			/**
			 * @var RCP_Payments $rcp_payments_db
			 */
			global $rcp_payments_db;

			$this->email               = $subscription_data['user_email'];
			$this->user_id             = $subscription_data['user_id'];
			$this->user_name           = $subscription_data['user_name'];
			$this->currency            = $subscription_data['currency'];
			$this->amount              = round( $subscription_data['recurring_price'], 2 );
			$this->initial_amount      = round( $subscription_data['price'] + $subscription_data['fee'], 2 );
			$this->discount            = $subscription_data['discount'];
			$this->discount_code       = $subscription_data['discount_code'];
			$this->length              = $subscription_data['length'];
			$this->length_unit         = $subscription_data['length_unit'];
			$this->signup_fee          = $this->supports( 'fees' ) ? $subscription_data['fee'] : 0;
			$this->subscription_key    = $subscription_data['key'];
			$this->subscription_id     = $subscription_data['subscription_id'];
			$this->subscription_name   = $subscription_data['subscription_name'];
			$this->auto_renew          = $this->supports( 'recurring' ) ? $subscription_data['auto_renew'] : false;;
			$this->return_url          = $subscription_data['return_url'];
			$this->subscription_data   = $subscription_data;
			$this->payment             = $rcp_payments_db->get_payment( $subscription_data['payment_id'] );
			$this->customer            = $subscription_data['customer'];
			$this->membership          = rcp_get_membership( $subscription_data['membership_id'] );

			rcp_log( sprintf( 'Registration for user #%d sent to gateway. Level ID: %d; Initial Amount: %.2f; Recurring Amount: %.2f; Auto Renew: %s; Trial: %s; Membership ID: %d', $this->user_id, $this->subscription_id, $this->initial_amount, $this->amount, var_export( $this->auto_renew, true ), var_export( $this->is_trial(), true ), $this->membership->get_id() ) );

		}

	}

	/**
	 * Initialize the gateway configuration
	 *
	 * This is used to populate the $supports property, setup any API keys, and set the API endpoint.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		/* Example:

		$this->supports[] = 'one-time';
		$this->supports[] = 'recurring';
		$this->supports[] = 'fees';
		$this->supports[] = 'trial';

		global $rcp_options;

		if ( $this->test_mode ) {
			$this->api_endpoint = 'https://sandbox.gateway.com';
			$this->api_key      = $rcp_options['my_sandbox_api_key'];
		} else {
			$this->api_endpoint = 'https://live.gateway.com';
			$this->api_key      = $rcp_options['my_live_api_key'];
		}

		*/

	}

	/**
	 * Process registration
	 *
	 * This is where you process the actual payment. If non-recurring, you'll want to use
	 * the $this->initial_amount value. If recurring, you'll want to use $this->initial_amount
	 * for the first payment and $this->amount for the recurring amount.
	 *
	 * After a successful payment, redirect to $this->return_url.
	 *
	 * @access public
	 * @return void
	 */
	public function process_signup() {}

	/**
	 * Process webhooks
	 *
	 * Listen for webhooks and take appropriate action to insert payments, renew the member's
	 * account, or cancel the membership.
	 *
	 * @access public
	 * @return void
	 */
	public function process_webhooks() {}

	/**
	 * Use this space to enqueue any extra JavaScript files.
	 *
	 * @access public
	 * @return void
	 */
	public function scripts() {}

	/**
	 * Load any extra fields on the registration form
	 *
	 * @access public
	 * @return void
	 */
	public function fields() {

		/* Example for loading the credit card fields :

		ob_start();
		rcp_get_template_part( 'card-form' );
		return ob_get_clean();

		*/

	}

	/**
	 * Validate registration form fields
	 *
	 * @access public
	 * @return void
	 */
	public function validate_fields() {

		/* Example :

		if ( empty( $_POST['rcp_card_cvc'] ) ) {
			rcp_errors()->add( 'missing_card_code', __( 'The security code you have entered is invalid', 'rcp' ), 'register' );
		}

		*/

	}

	/**
	 * Check if the gateway supports a given feature
	 *
	 * @param string $item
	 *
	 * @access public
	 * @return bool
	 */
	public function supports( $item = '' ) {
		return in_array( $item, $this->supports );
	}

	/**
	 * Generate a transaction ID
	 *
	 * Used in the manual payments gateway.
	 *
	 * @return string
	 */
	public function generate_transaction_id() {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		return strtolower( md5( $this->subscription_key . date( 'Y-m-d H:i:s' ) . $auth_key . uniqid( 'rcp', true ) ) );
	}

	/**
	 * Activate or renew the membership. If the membership has been billed `0` times then it is activated for the
	 * first time. Otherwise it is renewed.
	 *
	 * @param bool   $recurring Whether or not it's a recurring subscription.
	 * @param string $status    Status to set the member to, usually 'active'.
	 *
	 * @access public
	 * @return void
	 */
	public function renew_member( $recurring = false, $status = 'active' ) {
		if ( 0 == $this->membership->get_times_billed() ) {
			$this->membership->activate();
		} else {
			$this->membership->renew( $recurring, $status );
		}
	}

	/**
	 * Add error to the registration form
	 *
	 * @param string $code    Error code.
	 * @param string $message Error message to display.
	 *
	 * @access public
	 * @return void
	 */
	public function add_error( $code = '', $message = '' ) {
		rcp_errors()->add( $code, $message, 'register' );
	}

	/**
	 * Determines if the subscription is eligible for a trial.
	 *
	 * @since 2.7
	 * @return bool True if the subscription is eligible for a trial, false if not.
	 */
	public function is_trial() {
		return ! empty( $this->subscription_data['trial_eligible'] )
			&& ! empty( $this->subscription_data['trial_duration'] )
			&& ! empty( $this->subscription_data['trial_duration_unit'] )
		;
	}

}