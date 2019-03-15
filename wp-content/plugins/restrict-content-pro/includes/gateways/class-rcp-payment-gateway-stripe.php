<?php
/**
 * Stripe Payment Gateway
 *
 * @package     Restrict Content Pro
 * @subpackage  Classes/Gateways/Stripe
 * @copyright   Copyright (c) 2017, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.1
*/

class RCP_Payment_Gateway_Stripe extends RCP_Payment_Gateway {

	protected $secret_key;
	protected $publishable_key;

	/**
	 * Get things going
	 *
	 * @access public
	 * @since  2.1
	 * @return void
	 */
	public function init() {

		global $rcp_options;

		$this->supports[] = 'one-time';
		$this->supports[] = 'recurring';
		$this->supports[] = 'fees';
		$this->supports[] = 'gateway-submits-form';
		$this->supports[] = 'trial';

		if( $this->test_mode ) {

			$this->secret_key      = isset( $rcp_options['stripe_test_secret'] )      ? trim( $rcp_options['stripe_test_secret'] )      : '';
			$this->publishable_key = isset( $rcp_options['stripe_test_publishable'] ) ? trim( $rcp_options['stripe_test_publishable'] ) : '';

		} else {

			$this->secret_key      = isset( $rcp_options['stripe_live_secret'] )      ? trim( $rcp_options['stripe_live_secret'] )      : '';
			$this->publishable_key = isset( $rcp_options['stripe_live_publishable'] ) ? trim( $rcp_options['stripe_live_publishable'] ) : '';

		}

		if( ! class_exists( 'Stripe\Stripe' ) ) {
			require_once RCP_PLUGIN_DIR . 'includes/libraries/stripe/init.php';
		}

		\Stripe\Stripe::setApiKey( $this->secret_key );

		\Stripe\Stripe::setApiVersion( '2018-02-06' );

		if ( method_exists( '\Stripe\Stripe', 'setAppInfo' ) ) {
			\Stripe\Stripe::setAppInfo( 'Restrict Content Pro', RCP_PLUGIN_VERSION, esc_url( site_url() ) );
		}
	}

	/**
	 * Process registration
	 *
	 * @access public
	 * @since  2.1
	 * @return void
	 */
	public function process_signup() {

		global $rcp_options;

		/**
		 * @var RCP_Payments $rcp_payments_db
		 */
		global $rcp_payments_db;

		$paid            = false;
		$member          = new RCP_Member( $this->user_id ); // for backwards compatibility only
		$customer_exists = false;

		if( empty( $_POST['stripeToken'] ) ) {
			wp_die( __( 'Missing Stripe token, please try again or contact support if the issue persists.', 'rcp' ), __( 'Error', 'rcp' ), array( 'response' => 400 ) );
		}

		$customer_id = rcp_get_customer_gateway_id( $this->membership->get_customer()->get_id(), 'stripe' );

		if ( $customer_id ) {

			$customer_exists = true;

			try {

				// Update the customer to ensure their card data is up to date
				$customer = \Stripe\Customer::retrieve( $customer_id );

				if( isset( $customer->deleted ) && $customer->deleted ) {

					// This customer was deleted
					$customer_exists = false;

				}

			// No customer found
			} catch ( Exception $e ) {

				$customer_exists = false;

			}

		}

		if( empty( $customer_exists ) ) {

			try {

				$customer_args = array(
					'email' => $this->email
				);

				$customer = \Stripe\Customer::create( apply_filters( 'rcp_stripe_customer_create_args', $customer_args, $this ) );

				/*
				 * A temporary invoice is created to force the customer's currency to be set to the store currency.
				 * See https://github.com/restrictcontentpro/restrict-content-pro/issues/549
				 * See https://github.com/restrictcontentpro/restrict-content-pro/issues/382
				 */
				if ( ! empty( $this->signup_fee ) || ! empty( $this->discount_code ) ) {

					\Stripe\InvoiceItem::create( array(
						'customer'    => $customer->id,
						'amount'      => 0,
						'currency'    => rcp_get_currency(),
						'description' => 'Setting Customer Currency',
					) );

					$temp_invoice = \Stripe\Invoice::create( array(
						'customer' => $customer->id,
					) );

				}

				$customer_id = $customer->id;

			} catch ( Exception $e ) {

				$this->handle_processing_error( $e );

			}

		}

		// Set up array of subscriptions we cancel below so we don't try to cancel the same one twice.
		$cancelled_subscriptions = array();

		// clean up any past due or unpaid subscriptions before upgrading/downgrading
		$subscriptions = $customer->subscriptions->all( array(
			'expand' => array( 'data.plan.product' )
		) );
		foreach( $subscriptions->data as $subscription ) {

			// Cancel subscriptions with the RCP metadata present and matching member ID.
			// @todo When we add multiple subscriptions we need to update this to only cancel subscriptions where $this->subscription_id matches the rcp_subscription_level_id in the metadata.
			if ( ! empty( $subscription->metadata ) && ! empty( $subscription->metadata['rcp_subscription_level_id'] ) && $this->user_id == $subscription->metadata['rcp_member_id'] ) {
				$subscription->cancel();
				$cancelled_subscriptions[] = $subscription->id;
				rcp_log( sprintf( 'Cancelled Stripe subscription %s.', $subscription->id ) );
				continue;
			}

			/*
			 * This handles subscriptions from before metadata was added. We check the plan name against the
			 * RCP membership level database. If the Stripe plan name matches a sub level name then we cancel it.
			 * @todo When we add multiple subscriptions we need to update this to only cancel if the plan name != $member->get_pending_subscription_name()
			 */
			if ( ! empty( $subscription->plan->product->name ) ) {

				/**
				 * @var RCP_Levels $rcp_levels_db
				 */
				global $rcp_levels_db;

				$level = $rcp_levels_db->get_level_by( 'name', $subscription->plan->product->name );

				// Cancel if this plan name matches an RCP membership level.
				if ( ! empty( $level ) ) {
					$subscription->cancel();
					$cancelled_subscriptions[] = $subscription->id;
					rcp_log( sprintf( 'Cancelled Stripe subscription %s.', $subscription->id ) );
				}

			}
		}

		/*
		 * Set Stripe customer ID as payment profile ID. We can safely do this now that we've cancelled any existing
		 * subscriptions. See https://github.com/restrictcontentpro/restrict-content-pro/issues/1719
		 */
		$this->membership->set_gateway_customer_id( $customer_id );

		// Now save card details. This has to be done after the above cancellations. See https://github.com/restrictcontentpro/restrict-content-pro/issues/1570
		$customer->source = $_POST['stripeToken'];
		try {
			$customer->save();
		} catch( Exception $e ) {
			$this->handle_processing_error( $e );
		}

		if ( $this->auto_renew ) {

			// process a subscription sign up
			if ( ! $plan_id = $this->plan_exists( $this->subscription_id ) ) {
				// create the plan if it doesn't exist
				$plan_id = $this->create_plan( $this->subscription_id );
			}

			try {

				// Add fees before the plan is updated and charged

				if( $this->initial_amount > $this->amount ) {
					$save_balance   = true;
					$amount         = $this->initial_amount - $this->amount;
					$balance_amount = round( $customer->account_balance + ( $amount * rcp_stripe_get_currency_multiplier() ), 0 ); // Add additional amount to initial payment (in cents)
				}

				if( $this->initial_amount < $this->amount ) {
					$save_balance   = true;
					$amount         = $this->amount - $this->initial_amount;
					$balance_amount = round( $customer->account_balance - ( $amount * rcp_stripe_get_currency_multiplier() ), 0 ); // Add additional amount to initial payment (in cents)
				}

				if ( ! empty( $save_balance ) ) {

					$customer->account_balance = $balance_amount;
					$customer->save();

				}

				// Remove the temporary invoice
				if( isset( $temp_invoice ) ) {
					$invoice = \Stripe\Invoice::retrieve( $temp_invoice->id );
					$invoice->closed = true;
					$invoice->save();
					unset( $temp_invoice, $invoice );
				}

				$sub_args = array(
					'plan'     => $plan_id,
					'prorate'  => false,
					'metadata' => array(
						'rcp_subscription_level_id' => $this->subscription_id,
						'rcp_member_id'             => $this->user_id,
						'rcp_customer_id'           => $this->customer->get_id(),
						'rcp_membership_id'         => $this->membership->get_id()
					)
				);

				if ( ! empty( $this->discount_code ) && ! isset( $rcp_options['one_time_discounts'] ) ) {

					$sub_args['coupon'] = $this->discount_code;

				}

				// Is this a free trial?
				if ( $this->is_trial() ) {
					$sub_args['trial_end'] = strtotime( $this->subscription_data['trial_duration'] . ' ' . $this->subscription_data['trial_duration_unit'], current_time( 'timestamp' ) );
				}

				// Set the customer's subscription in Stripe
				$sub_args = apply_filters( 'rcp_stripe_create_subscription_args', $sub_args, $this );

				$sub_options = array();

				$stripe_connect_user_id = get_option( 'rcp_stripe_connect_account_id', false );

				if( ! empty( $stripe_connect_user_id ) ) {
					$sub_options['stripe_account'] = $stripe_connect_user_id;
				}

				$subscription = $customer->subscriptions->create( $sub_args, $sub_options );

				$this->membership->set_gateway_subscription_id( $subscription->id );

				// Complete payment and activate account.

				$payment_data = array(
					'payment_type'   => 'Credit Card',
					'transaction_id' => '',
					'status'         => 'complete'
				);

				if ( $this->is_trial() ) {

					// Free trials use the subscription ID as the payment transaction ID.
					$payment_data['transaction_id'] = $subscription->id;

				} else {

					// Try to get the invoice from the subscription we just added so we can add the transaction ID to the payment.
					$invoices = \Stripe\Invoice::all( array(
						'subscription' => $subscription->id,
						'limit'        => 1
					) );

					if ( is_array( $invoices->data ) && isset( $invoices->data[0] ) ) {
						$invoice = $invoices->data[0];

						// We only want the transaction ID if it's actually been paid. If not, we'll let the webhook handle it.
						if ( true === $invoice->paid ) {
							$payment_data['transaction_id'] = $invoice->charge;
						}
					}

				}

				// Only complete the payment if we have a transaction ID. If we don't, the webhook will complete the payment.
				if ( ! empty( $payment_data['transaction_id'] ) ) {
					$rcp_payments_db->update( $this->payment->id, $payment_data );

					do_action( 'rcp_gateway_payment_processed', $member, $this->payment->id, $this );
				} else {
					rcp_log( 'Unable to retrieve transaction ID. Payment will be completed via webhook.' );
				}

				$paid = true;

			} catch ( \Stripe\Error\Card $e ) {

				if ( ! empty( $save_balance ) ) {
					$customer->account_balance -= $balance_amount;
					$customer->save();
				}

				$this->handle_processing_error( $e );

			} catch ( \Stripe\Error\InvalidRequest $e ) {

				// Invalid parameters were supplied to Stripe's API
				$this->handle_processing_error( $e );

			} catch ( \Stripe\Error\Authentication $e ) {

				// Authentication with Stripe's API failed
				// (maybe you changed API keys recently)
				$this->handle_processing_error( $e );

			} catch ( \Stripe\Error\ApiConnection $e ) {

				// Network communication with Stripe failed
				$this->handle_processing_error( $e );

			} catch ( \Stripe\Error\Base $e ) {

				// Display a very generic error to the user
				$this->handle_processing_error( $e );

			} catch ( Exception $e ) {

				// Something else happened, completely unrelated to Stripe

				$error = '<p>' . __( 'An unidentified error occurred.', 'rcp' ) . '</p>';
				$error .= print_r( $e, true );

				wp_die( $error, __( 'Error', 'rcp' ), array( 'response' => 401 ) );

			}

		} else {

			// process a one time payment signup

			try {

				$charge_args = apply_filters( 'rcp_stripe_charge_create_args', array(
					'amount'         => round( ( $this->initial_amount ) * rcp_stripe_get_currency_multiplier(), 0 ), // amount in cents
					'currency'       => strtolower( $this->currency ),
					'customer'       => $customer->id,
					'description'    => 'User ID: ' . $this->user_id . ' - User Email: ' . $this->email . ' - Subscription: ' . $this->subscription_name . ' - Membership: ' . $this->membership->get_id(),
					'metadata'       => array(
						'email'         => $this->email,
						'user_id'       => $this->user_id,
						'level_id'      => $this->subscription_id,
						'level'         => $this->subscription_name,
						'key'           => $this->subscription_key,
						'membership_id' => $this->membership->get_id()
					)
				), $this );

				$charge_options = array();

				$stripe_connect_user_id = get_option( 'rcp_stripe_connect_account_id', false );

				if( ! empty( $stripe_connect_user_id ) ) {
					$charge_options['stripe_account'] = $stripe_connect_user_id;
				}

				$charge = \Stripe\Charge::create( $charge_args, $charge_options );

				// Complete pending payment. This also updates the expiration date, status, etc.
				$rcp_payments_db->update( $this->payment->id, array(
					'payment_type'   => 'Credit Card One Time',
					'transaction_id' => $charge->id,
					'status'         => 'complete'
				) );

				do_action( 'rcp_gateway_payment_processed', $member, $this->payment->id, $this );

				// Subscription ID is not used when non-recurring.
				delete_user_meta( $member->ID, 'rcp_merchant_subscription_id' );

				$paid = true;

			} catch ( \Stripe\Error\Card $e ) {

				$this->handle_processing_error( $e );

			} catch ( \Stripe\Error\InvalidRequest $e ) {

				// Invalid parameters were supplied to Stripe's API
				$this->handle_processing_error( $e );


			} catch ( \Stripe\Error\Authentication $e ) {

				// Authentication with Stripe's API failed
				// (maybe you changed API keys recently)
				$this->handle_processing_error( $e );

			} catch ( \Stripe\Error\ApiConnection $e ) {

				// Network communication with Stripe failed
				$this->handle_processing_error( $e );


			} catch ( \Stripe\Error\Base $e ) {

				// Display a very generic error to the user
				$this->handle_processing_error( $e );

			} catch ( Exception $e ) {

				// Something else happened, completely unrelated to Stripe

				$error = '<p>' . __( 'An unidentified error occurred.', 'rcp' ) . '</p>';
				$error .= print_r( $e, true );

				wp_die( $error, __( 'Error', 'rcp' ), array( 'response' => 401 ) );

			}
		}

		if ( $paid ) {

			// Add description and meta to Stripe Customer
			$customer->description = 'User ID: ' . $this->user_id . ' - User Email: ' . $this->email . ' Subscription: ' . $this->subscription_name;
			$customer->metadata    = array(
				'user_id'      => $this->user_id,
				'email'        => $this->email,
				'subscription' => $this->subscription_name,
				'customer_id'  => $this->customer->get_id()
			);

			try {
				$customer->save();
			} catch( Exception $e ) {
				$this->handle_processing_error( $e );
			}

			do_action( 'rcp_stripe_signup', $this->user_id, $this );

		} else {

			wp_die( __( 'An error occurred, please contact the site administrator: ', 'rcp' ) . get_bloginfo( 'admin_email' ), __( 'Error', 'rcp' ), array( 'response' => 401 ) );

		}

		// redirect to the success page, or error page if something went wrong
		wp_redirect( $this->return_url ); exit;

	}

	/**
	 * Handle Stripe processing error
	 *
	 * @param $e
	 *
	 * @access protected
	 * @since  2.5
	 * @return void
	 */
	protected function handle_processing_error( $e ) {

		if ( method_exists( $e, 'getJsonBody' ) ) {

			$body                = $e->getJsonBody();
			$err                 = $body['error'];
			$error_code          = ! empty( $err['code'] ) ? $err['code'] : false;
			$this->error_message = $err['message'];

		} else {

			$error_code          = $e->getCode();
			$this->error_message = $e->getMessage();

			// $err is here for backwards compat for the rcp_stripe_signup_payment_failed hook below.
			$err = array(
				'message' => $e->getMessage(),
				'type'    => 'other',
				'param'   => false,
				'code'    => 'other'
			);

		}

		do_action( 'rcp_registration_failed', $this );
		do_action( 'rcp_stripe_signup_payment_failed', $err, $this );

		$error = '<h4>' . __( 'An error occurred', 'rcp' ) . '</h4>';

		if( ! empty( $error_code ) ) {
			$error .= '<p>' . sprintf( __( 'Error code: %s', 'rcp' ), $error_code ) . '</p>';
		}

		if ( method_exists( $e, 'getHttpStatus' ) ) {
			$error .= "<p>Status: " . $e->getHttpStatus() ."</p>";
		}

		$error .= "<p>Message: " . $this->error_message . "</p>";

		wp_die( $error, __( 'Error', 'rcp' ), array( 'response' => 401 ) );
	}

	/**
	 * Process webhooks
	 *
	 * @access public
	 * @return void
	 */
	public function process_webhooks() {

		if( ! isset( $_GET['listener'] ) || strtolower( $_GET['listener'] ) != 'stripe' ) {
			return;
		}

		rcp_log( 'Starting to process Stripe webhook.' );

		// Ensure listener URL is not cached by W3TC
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		// retrieve the request's body and parse it as JSON
		$body          = @file_get_contents( 'php://input' );
		$event_json_id = json_decode( $body );
		$expiration    = '';

		// for extra security, retrieve from the Stripe API
		if ( ! isset( $event_json_id->id ) ) {

			rcp_log( 'Exiting Stripe webhook - no event ID found.' );

			die( 'no event ID found' );

		}

		$rcp_payments = new RCP_Payments();

		$event_id = $event_json_id->id;

		try {

			$event         = \Stripe\Event::retrieve( $event_id );
			$payment_event = $event->data->object;

			if( empty( $payment_event->customer ) ) {
				rcp_log( 'Exiting Stripe webhook - no customer attached to event.' );

				die( 'no customer attached' );
			}

			$invoice = $customer = $subscription = false;

			if ( ! empty( $payment_event->invoice ) ) {
				$invoice = \Stripe\Invoice::retrieve( $payment_event->invoice );

				if ( ! empty( $invoice->subscription ) ) {
					$subscription = \Stripe\Subscription::retrieve( $invoice->subscription );
				}

			}

			// Retrieve the membership by subscription ID.
			if ( ! empty( $subscription ) ) {
				$membership = rcp_get_membership_by( 'gateway_subscription_id', $subscription->id );
			}

			// Retrieve the membership by customer ID.
			if ( empty( $membership ) ) {
				$membership = rcp_get_membership_by( 'gateway_customer_id', $payment_event->customer );
			}

			if( empty( $membership ) ) {

				// Grab the customer ID from the old meta keys
				global $wpdb;
				$user_id  = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_rcp_stripe_user_id' AND meta_value = %s LIMIT 1", $payment_event->customer ) );
				$customer = rcp_get_customer_by_user_id( $user_id );

				if ( ! empty( $customer ) ) {
					$membership = rcp_get_customer_single_membership( $customer->get_id() );
				}

			}

			if( empty( $membership ) ) {
				rcp_log( sprintf( 'Exiting Stripe webhook - membership not found. Customer ID: %s.', $payment_event->customer ), true );

				die( 'no membership ID found' );
			}

			$this->membership = $membership;
			$customer = $membership->get_customer();
			$member   = new RCP_Member( $customer->get_user_id() ); // for backwards compatibility

			rcp_log( sprintf( 'Processing webhook for membership #%d.', $membership->get_id() ) );

			$subscription_level_id = $membership->get_object_id();

			if( ! $subscription_level_id ) {
				rcp_log( 'Exiting Stripe webhook - no membership level ID for membership.', true );

				die( 'no membership level ID for member' );
			}

			if( $event->type == 'customer.subscription.created' ) {
				do_action( 'rcp_webhook_recurring_payment_profile_created', $member, $this );
			}

			if( $event->type == 'charge.succeeded' || $event->type == 'invoice.payment_succeeded' ) {

				rcp_log( sprintf( 'Processing Stripe %s webhook.', $event->type ) );

				// setup payment data
				$payment_data = array(
					'date'                  => date_i18n( 'Y-m-d g:i:s', $event->created ),
					'payment_type'          => 'Credit Card',
					'user_id'               => $member->ID,
					'customer_id'           => $customer->get_id(),
					'membership_id'         => $membership->get_id(),
					'amount'                => '',
					'transaction_id'        => '',
					'object_id'             => $subscription_level_id,
					'status'                => 'complete',
					'gateway'               => 'stripe'
				);

				if ( $event->type == 'charge.succeeded' ) {

					// Successful one-time payment
					if ( empty( $payment_event->invoice ) ) {

						$payment_data['amount']         = $payment_event->amount / rcp_stripe_get_currency_multiplier();
						$payment_data['transaction_id'] = $payment_event->id;

					// Successful subscription payment
					} else {

						$payment_data['amount']         = $invoice->amount_due / rcp_stripe_get_currency_multiplier();
						$payment_data['transaction_id'] = $payment_event->id;

						// @todo Not sure about this because I don't think we can do it with all gateways.
						if ( ! empty( $payment_event->discount ) ) {
							$payment_data['discount_code'] = $payment_event->discount->coupon_id;
						}

					}

				} elseif ( $event->type == 'invoice.payment_succeeded' && empty( $payment_event->charge ) ) {

					$invoice = $payment_event;

					// Successful subscription paid made with account credit, or free trial, where no charge is created
					if ( 'in_' !== substr( $invoice->id, 0, 3 ) ) {
						$payment_data['amount']         = $invoice->amount_due / rcp_stripe_get_currency_multiplier();
						$payment_data['transaction_id'] = $invoice->id;
					} else {
						$payment_data['amount']           = $invoice->lines->data[0]->amount / rcp_stripe_get_currency_multiplier();
						$payment_data['transaction_id']   = $invoice->subscription; // trials don't get a charge ID. set the subscription ID.
					}

				}

				if( ! empty( $payment_data['transaction_id'] ) && ! $rcp_payments->payment_exists( $payment_data['transaction_id'] ) ) {

					if ( ! empty( $subscription ) ) {

						$expiration = date( 'Y-m-d 23:59:59', $subscription->current_period_end );
						$membership->set_recurring();
						$membership->set_gateway_subscription_id( $subscription->id );

					}

					$pending_payment_id = $member->get_pending_payment_id();
					if ( ! empty( $pending_payment_id ) ) {

						// Completing a pending payment. Account activation is handled in rcp_complete_registration()
						$rcp_payments->update( $pending_payment_id, $payment_data );
						$payment_id = $pending_payment_id;

					} else {

						// Inserting a new payment and renewing.
						$membership->renew( $membership->is_recurring(), 'active', $expiration );

						// These must be retrieved after the status is set to active in order for upgrades to work properly
						$payment_data['subscription']     = rcp_get_subscription_name( $membership->get_object_id() );
						$payment_data['subscription_key'] = $membership->get_subscription_key();
						$payment_id                       = $rcp_payments->insert( $payment_data );

						if ( $membership->is_recurring() ) {
							do_action( 'rcp_webhook_recurring_payment_processed', $member, $payment_id, $this );
						}

					}

					do_action( 'rcp_gateway_payment_processed', $member, $payment_id, $this );
					do_action( 'rcp_stripe_charge_succeeded', $customer->get_user_id(), $payment_data, $event );

					die( 'rcp_stripe_charge_succeeded action fired successfully' );

				} elseif ( ! empty( $payment_data['transaction_id'] ) && $rcp_payments->payment_exists( $payment_data['transaction_id'] ) ) {

					do_action( 'rcp_ipn_duplicate_payment', $payment_data['transaction_id'], $member, $this );

					die( 'duplicate payment found' );

				}

			}

			// failed payment
			if ( $event->type == 'invoice.payment_failed' ) {

				rcp_log( 'Processing Stripe invoice.payment_failed webhook.' );

				$this->webhook_event_id = $event->id;

				// Make sure this invoice is tied to a subscription and is the user's current subscription.
				if ( ! empty( $event->data->object->subscription ) && $event->data->object->subscription == $membership->get_gateway_subscription_id() ) {
					do_action( 'rcp_recurring_payment_failed', $member, $this );
				} else {
					rcp_log( sprintf( 'Stripe subscription ID %s doesn\'t match membership\'s merchant subscription ID %s. Skipping rcp_recurring_payment_failed hook.', $event->data->object->subscription, $member->get_merchant_subscription_id() ), true );
				}

				do_action( 'rcp_stripe_charge_failed', $payment_event, $event, $member );

				die( 'rcp_stripe_charge_failed action fired successfully' );

			}

			// Cancelled / failed subscription
			if( $event->type == 'customer.subscription.deleted' ) {

				rcp_log( 'Processing Stripe customer.subscription.deleted webhook.' );

				if( $payment_event->id == $membership->get_gateway_subscription_id() ) {

					// If this is a completed payment plan, we can skip any cancellation actions. This is handled in renewals.
					if ( $membership->has_payment_plan() && $membership->at_maximum_renewals() ) {
						rcp_log( sprintf( 'Membership #%d has completed its payment plan - not cancelling.', $membership->get_id() ) );
						die( 'membership payment plan completed' );
					}

					if ( $membership->is_active() ) {
						$membership->cancel();
					} else {
						rcp_log( sprintf( 'Membership #%d is not active - not cancelling account.', $membership->get_id() ) );
					}

					do_action( 'rcp_webhook_cancel', $member, $this );

					die( 'member cancelled successfully' );

				} else {
					rcp_log( sprintf( 'Payment event ID (%s) doesn\'t match membership\'s merchant subscription ID (%s).', $payment_event->id, $membership->get_gateway_subscription_id() ), true );
				}

			}

			do_action( 'rcp_stripe_' . $event->type, $payment_event, $event );


		} catch ( Exception $e ) {
			// something failed
			rcp_log( sprintf( 'Exiting Stripe webhook due to PHP exception: %s.', $e->getMessage() ), true );

			die( 'PHP exception: ' . $e->getMessage() );
		}

		die( '1' );

	}

	/**
	 * Add credit card fields
	 *
	 * @since 2.1
	 * @return string
	 */
	public function fields() {

		ob_start();
?>
		<script type="text/javascript">

			var rcp_script_options;
			var rcp_processing;

			// this identifies your website in the createToken call below
			Stripe.setPublishableKey('<?php echo $this->publishable_key; ?>');

			function stripeResponseHandler(status, response) {
				if (response.error) {
					// re-enable the submit button
					jQuery('#rcp_registration_form #rcp_submit').attr("disabled", false);

					jQuery('#rcp_ajax_loading').hide();

					// show the errors on the form
					jQuery('#rcp_registration_form').unblock();
					jQuery('#rcp_submit').before( '<div class="rcp_message error"><p class="rcp_error"><span>' + response.error.message + '</span></p></div>' );
					jQuery('#rcp_submit').val( rcp_script_options.register );

					rcp_processing = false;

				} else {

					var form$ = jQuery('#rcp_registration_form');
					// token contains id, last4, and card type
					var token = response['id'];
					// insert the token into the form so it gets submitted to the server
					form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

					// and submit
					form$.get(0).submit();

				}
			}

			jQuery(document).ready(function($) {

				$('body').off('rcp_register_form_submission').on('rcp_register_form_submission', function(event, response, form_id) {

					// get the subscription price
					if( $('.rcp_level:checked').length ) {
						var price = $('.rcp_level:checked').closest('.rcp_subscription_level').find('span.rcp_price').attr('rel') * <?php echo rcp_stripe_get_currency_multiplier(); ?>;
					} else {
						var price = $('.rcp_level').attr('rel') * <?php echo rcp_stripe_get_currency_multiplier(); ?>;
					}

					if( response.gateway.slug === 'stripe' && price > 0 && ! $('.rcp_gateway_fields').hasClass('rcp_discounted_100')) {

						event.preventDefault();

						// disable the submit button to prevent repeated clicks
						$('#rcp_registration_form #rcp_submit').attr("disabled", "disabled");
						$('#rcp_ajax_loading').show();

						// createToken returns immediately - the supplied callback submits the form if there are no errors
						Stripe.createToken({
							number: $('.card-number').val(),
							name: $('.card-name').val(),
							cvc: $('.card-cvc').val(),
							exp_month: $('.card-expiry-month').val(),
							exp_year: $('.card-expiry-year').val(),
							address_zip: $('.card-zip').val()
						}, stripeResponseHandler);

						return false;
					}

				});
			});
		</script>
<?php
		rcp_get_template_part( 'card-form' );
		return ob_get_clean();
	}

	/**
	 * Validate additional fields during registration submission
	 *
	 * @since  2.1
	 * @return void
	 */
	public function validate_fields() {

		global $rcp_options;

		if( empty( $_POST['rcp_card_number'] ) ) {
			rcp_errors()->add( 'missing_card_number', __( 'The card number you have entered is invalid', 'rcp' ), 'register' );
		}

		if( empty( $_POST['rcp_card_cvc'] ) ) {
			rcp_errors()->add( 'missing_card_code', __( 'The security code you have entered is invalid', 'rcp' ), 'register' );
		}

		if( empty( $_POST['rcp_card_zip'] ) ) {
			rcp_errors()->add( 'missing_card_zip', __( 'The zip / postal code you have entered is invalid', 'rcp' ), 'register' );
		}

		if( empty( $_POST['rcp_card_name'] ) ) {
			rcp_errors()->add( 'missing_card_name', __( 'The card holder name you have entered is invalid', 'rcp' ), 'register' );
		}

		if( empty( $_POST['rcp_card_exp_month'] ) ) {
			rcp_errors()->add( 'missing_card_exp_month', __( 'The card expiration month you have entered is invalid', 'rcp' ), 'register' );
		}

		if( empty( $_POST['rcp_card_exp_year'] ) ) {
			rcp_errors()->add( 'missing_card_exp_year', __( 'The card expiration year you have entered is invalid', 'rcp' ), 'register' );
		}

		if ( $this->test_mode && ( empty( $rcp_options['stripe_test_secret'] ) || empty( $rcp_options['stripe_test_publishable'] ) ) ) {
			rcp_errors()->add( 'missing_stripe_test_keys', __( 'Missing Stripe test keys. Please enter your test keys to use Stripe in Sandbox Mode.', 'rcp' ), 'register' );
		}

		if ( ! $this->test_mode && ( empty( $rcp_options['stripe_live_secret'] ) || empty( $rcp_options['stripe_live_publishable'] ) ) ) {
			rcp_errors()->add( 'missing_stripe_live_keys', __( 'Missing Stripe live keys. Please enter your live keys to use Stripe in Live Mode.', 'rcp' ), 'register' );
		}

	}

	/**
	 * Load Stripe JS
	 *
	 * @since 2.1
	 * @return void
	 */
	public function scripts() {
		wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v2/', array( 'jquery' ) );
	}

	/**
	 * Create plan in Stripe
	 *
	 * @param int $plan_id ID number of the plan.
	 *
	 * @since 2.1
	 * @return bool|string - plan_id if successful, false if not
	 */
	private function create_plan( $plan_id = '' ) {
		global $rcp_options;

		// get all membership level info for this plan
		$plan           = rcp_get_subscription_details( $plan_id );
		$price          = round( $plan->price * rcp_stripe_get_currency_multiplier(), 0 );
		$interval       = $plan->duration_unit;
		$interval_count = $plan->duration;
		$name           = $plan->name;
		$plan_id        = sprintf( '%s-%s-%s', strtolower( str_replace( ' ', '', $plan->name ) ), $plan->price, $plan->duration . $plan->duration_unit );
		$currency       = strtolower( rcp_get_currency() );

		try {

			$product = \Stripe\Product::create( array(
				'name' => $name,
				'type' => 'service'
			) );

			$plan = \Stripe\Plan::create( array(
				"amount"         => $price,
				"interval"       => $interval,
				"interval_count" => $interval_count,
				"currency"       => $currency,
				"id"             => $plan_id,
				"product"        => $product->id
			) );

			// plann successfully created
			return $plan->id;

		} catch ( Exception $e ) {

			$this->handle_processing_error( $e );
		}

	}

	/**
	 * Determine if a plan exists
	 *
	 * @param int $plan The ID number of the plan to check
	 *
	 * @since 2.1
	 * @return bool|string false if the plan doesn't exist, plan id if it does
	 */
	private function plan_exists( $plan ) {

		if ( ! $plan = rcp_get_subscription_details( $plan ) ) {
			return false;
		}

		// fallback to old plan id if the new plan id does not exist
		$old_plan_id = strtolower( str_replace( ' ', '', $plan->name ) );
		$new_plan_id = sprintf( '%s-%s-%s', $old_plan_id, $plan->price, $plan->duration . $plan->duration_unit );

		// check if the plan new plan id structure exists
		try {

			$plan = \Stripe\Plan::retrieve( $new_plan_id );
			return $plan->id;

		} catch ( Exception $e ) {}

		try {
			// fall back to the old plan id structure and verify that the plan metadata also matches
			$stripe_plan = \Stripe\Plan::retrieve( $old_plan_id );

			if ( (int) $stripe_plan->amount !== (int) $plan->price * 100 ) {
				return false;
			}

			if ( $stripe_plan->interval !== $plan->duration_unit ) {
				return false;
			}

			if ( $stripe_plan->interval_count !== intval( $plan->duration ) ) {
				return false;
			}

			return $old_plan_id;

		} catch ( Exception $e ) {
			return false;
		}

	}

}
