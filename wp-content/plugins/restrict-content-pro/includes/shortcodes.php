<?php
/**
 * Shortcodes
 *
 * @package     Restrict Content Pro
 * @subpackage  Shortcodes
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

add_filter( 'rcp_restrict_shortcode_return', 'wpautop' );
add_filter( 'rcp_restrict_shortcode_return', 'do_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );


/**
 * Restricting content to registered users and or user roles
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_restrict_shortcode( $atts, $content = null ) {

	$atts = shortcode_atts( array(
		'userlevel'    => 'none',
		'message'      => '',
		'paid'         => false,
		'level'        => 0,
		'subscription' => ''
	), $atts, 'restrict' );

	global $rcp_options, $user_ID;

	if ( strlen( $atts['message'] ) > 0 ) {
		$teaser = $atts['message'];
	} else {
		$teaser = rcp_get_restricted_content_message( ! empty( $atts['paid'] ) );
	}

	$subscriptions = array_map( 'trim', explode( ',', $atts['subscription'] ) );

	$has_access = false;

	$customer  = rcp_get_customer(); // currently logged in customer
	$is_active = ! empty( $customer ) ? $customer->has_active_membership() : false;

	if( $atts['paid'] ) {

		if ( ! empty( $customer ) && $customer->has_paid_membership() && $customer->has_access_level( $atts['level'] ) ) {
			$has_access = true;
		}

		$classes = 'rcp_restricted rcp_paid_only';

	} else {

		if ( ! empty( $customer ) && $customer->has_access_level( $atts['level'] ) ) {
			$has_access = true;
		}

		$classes = 'rcp_restricted';
	}

	if ( ! empty( $subscriptions ) && ! empty( $subscriptions[0] ) ) {
		if ( $is_active && count( array_intersect( rcp_get_customer_membership_level_ids( $customer->get_id() ), $subscriptions ) ) ) {
			$has_access = true;
		} else {
			$has_access = false;
		}
	}

	if ( $atts['userlevel'] === 'none' && ! is_user_logged_in() ) {
		$has_access = false;
	}
	if( 'none' != $atts['userlevel'] ) {
		$roles = array_map( 'trim', explode( ',', $atts['userlevel'] ) );

		foreach ( $roles as $role ) {
			if ( current_user_can( strtolower( $role ) ) ) {
				$has_access = true;
				break;
			} else {
				$has_access = false;
			}
		}
	}

	// No access if pending email verification.
	if ( ! empty( $customer ) && $customer->is_pending_verification() ) {
		$has_access = false;
	}

	if ( current_user_can( 'manage_options' ) ) {
		$has_access = true;
	}

	$has_access = (bool) apply_filters( 'rcp_restrict_shortcode_has_access', $has_access, $user_ID, $atts );

	if ( $has_access ) {
		return apply_filters( 'rcp_restrict_shortcode_return', $content );
	} else {
		return '<div class="' . esc_attr( $classes ) . '">' . rcp_format_teaser( $teaser ) . '</div>';
	}
}
add_shortcode( 'restrict', 'rcp_restrict_shortcode' );


/**
 * Shows content only to active, paid users
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_is_paid_user_shortcode( $atts, $content = null ) {

	$customer = rcp_get_customer(); // currently logged in customer

	if ( ! empty( $customer ) && $customer->has_paid_membership() ) {
		return do_shortcode( $content );
	}

	return '';
}
add_shortcode( 'is_paid', 'rcp_is_paid_user_shortcode' );


/**
 * Shows content only to logged-in free users, and can hide from paid
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_is_free_user_shortcode( $atts, $content = null ) {

	$atts = shortcode_atts( array(
		'hide_from_paid' => true
	), $atts, 'is_free' );

	$customer = rcp_get_customer(); // currently logged in customer

	if( $atts['hide_from_paid'] ) {
		if( ! empty( $customer ) && ! $customer->has_paid_membership() && is_user_logged_in() ) {
			return do_shortcode( $content );
		}
	} elseif( is_user_logged_in() ) {
		return do_shortcode( $content );
	}

	return '';
}
add_shortcode( 'is_free', 'rcp_is_free_user_shortcode' );

/**
 * Shows content only to expired users
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_is_expired_user_shortcode( $atts, $content = null ) {

	$customer = rcp_get_customer(); // currently logged in customer

	if ( empty( $customer ) ) {
		return '';
	}

	$memberships = $customer->get_memberships( array(
		'status' => 'expired'
	) );

	if( ! empty( $memberships ) ) {
		return do_shortcode( $content );
	}

	return '';
}
add_shortcode( 'is_expired', 'rcp_is_expired_user_shortcode' );


/**
 * Shows content only to not logged-in users
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_not_logged_in( $atts, $content = null ) {
	if( ! is_user_logged_in() ) {
		return do_shortcode( $content );
	}

	return '';
}
add_shortcode( 'not_logged_in', 'rcp_not_logged_in' );


/**
 * Allows content to be shown to only users that don't have an active subscription
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_is_not_paid( $atts, $content = null ) {

	$customer = rcp_get_customer(); // currently logged in customer

	// If there are no paid, active memberships then show the content.
	if ( empty( $customer ) || ! $customer->has_paid_membership() ) {
		return do_shortcode( $content );
	}

	return '';

}
add_shortcode( 'is_not_paid', 'rcp_is_not_paid' );


/**
 * Displays the currently logged-in user display-name
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_user_name( $atts, $content = null ) {
	global $user_ID;

	if( is_user_logged_in() ) {
		return get_userdata( $user_ID )->display_name;
	}

	return '';

}
add_shortcode( 'user_name', 'rcp_user_name' );


/**
 * Displays user registration form
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_registration_form( $atts, $content = null ) {

	$customer   = rcp_get_customer(); // current customer
	$membership = ! empty( $customer ) ? rcp_get_customer_single_membership( $customer->get_id() ) : false;

	$atts = shortcode_atts( array(
		'id'  => null, // Single specific level
		'ids' => null, // Multiple specific levels
		'registered_message' => __( 'You are already registered and have an active subscription.', 'rcp' ),
		'logged_out_header'  => __( 'Register New Account', 'rcp' ),
		'logged_in_header'   => ! empty( $membership ) ? __( 'Upgrade or Renew Your Subscription', 'rcp' ) : __( 'Join Now', 'rcp' )
	), $atts, 'register_form' );

	global $user_ID;

	// only show the registration form to non-logged-in members
	if( empty( $membership ) || ( ! empty( $membership ) && ( ! $membership->is_active() || $membership->upgrade_possible() || $membership->is_trialing() ) ) ) {

		global $rcp_options, $rcp_load_css, $rcp_load_scripts;

		// set this to true so the CSS and JS scripts are loaded
		$rcp_load_css = true;
		$rcp_load_scripts = true;

		$output = rcp_registration_form_fields( $atts['id'], $atts );

	} else {
		$output = $atts['registered_message'];
	}
	return $output;
}
add_shortcode( 'register_form', 'rcp_registration_form' );


/**
 * Displays stripe checkout form
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @since 2.5
 * @access public
 * @return string
 */
function rcp_register_form_stripe_checkout( $atts ) {
	global $rcp_options;

	if ( empty( $atts['id'] ) ) {
		return '';
	}

	// button is an alias for data-label
	if ( isset( $atts['button'] ) ) {
		$atts['data-label'] = $atts['button'];
	}

	$key = ( rcp_is_sandbox() ) ? $rcp_options['stripe_test_publishable'] : $rcp_options['stripe_live_publishable'];

	$customer     = rcp_get_customer_by_user_id(); // current customer
	$membership   = ! empty( $customer ) ? rcp_get_customer_single_membership( $customer->get_id() ) : false;
	$user         = wp_get_current_user();
	$subscription = rcp_get_subscription_details( $atts['id'] );
	$amount       = $subscription->price + $subscription->fee;
	$has_trialed  = ! empty( $customer ) ? $customer->has_trialed() : false;
	$is_trial     = ! empty( $subscription->trial_duration ) && ! empty( $subscription->trial_duration_unit ) && ! $has_trialed;

	if( ! empty( $membership ) ) {
		$amount -= $membership->get_prorate_credit_amount(); // @todo this may need to be removed in 3.1
	}

	if( $amount < 0 || $is_trial ) {
		$amount = 0;
	}

	$data = wp_parse_args( $atts, array(
		'id'                     => 0,
		'data-key'               => $key,
		'data-name'              => $subscription->name,
		'data-description'       => $subscription->description,
		'data-label'             => sprintf( __( 'Join %s', 'rcp' ), $subscription->name ),
		'data-panel-label'       => $is_trial ? __( 'Start Trial', 'rcp' ) : __( 'Register', 'rcp' ),
		'data-amount'            => $amount * rcp_stripe_get_currency_multiplier(),
		'data-locale'            => 'auto',
		'data-allow-remember-me' => true,
		'data-currency'          => rcp_get_currency()
	) );

	if ( empty( $data['data-email'] ) && ! empty( $user->user_email ) ) {
		$data['data-email'] = $user->user_email;
	}

	if ( empty( $data['data-image'] ) && $image = get_site_icon_url() ) {
		$data['data-image'] = $image;
	}

	$data = apply_filters( 'rcp_stripe_checkout_data', $data );

	global $rcp_load_css;

	// set this to true so the CSS is loaded. Used for styling error messages.
	$rcp_load_css = true;
	rcp_show_error_messages( 'register' );

	ob_start();

	if( ! empty( $membership ) && $membership->get_object_id() == $subscription->id && $membership->is_active() ) : ?>

		<div class="rcp-stripe-checkout-notice"><?php _e( 'You are already subscribed.', 'rcp' ); ?></div>

	<?php else :
		if( ! has_action( 'wp_footer', 'rcp_stripe_checkout_shortcode_scripts' ) ) {
			add_action( 'wp_footer', 'rcp_stripe_checkout_shortcode_scripts' );
		}
		?>
		<form action="" method="post">
			<?php do_action( 'register_form_stripe_fields', $data ); ?>
			<script src="https://checkout.stripe.com/checkout.js" class="stripe-button" <?php foreach( $data as $label => $value ) { printf( ' %s="%s" ', esc_attr( $label ), esc_attr( $value ) ); } ?> ></script>
			<input type="hidden" name="rcp_level" value="<?php echo $subscription->id ?>" />
			<input type="hidden" name="rcp_register_nonce" value="<?php echo wp_create_nonce('rcp-register-nonce' ); ?>"/>
			<input type="hidden" name="rcp_gateway" value="stripe_checkout"/>
			<input type="hidden" name="rcp_stripe_checkout" value="1"/>
		</form>
	<?php endif;

	return apply_filters( 'register_form_stripe', ob_get_clean(), $atts );
}
add_shortcode( 'register_form_stripe', 'rcp_register_form_stripe_checkout' );


/**
 * Displays user login form
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_login_form( $atts, $content = null ) {

	global $post;

	$current_page = rcp_get_current_url();

	$atts = shortcode_atts( array(
		'redirect' 	=> $current_page,
		'class' 	=> 'rcp_form'
	), $atts, 'login_form' );

	$output = '';

	global $rcp_load_css;

	// set this to true so the CSS is loaded
	$rcp_load_css = true;

	return rcp_login_form_fields( array( 'redirect' => $atts['redirect'], 'class' => $atts['class'] ) );

}
add_shortcode( 'login_form', 'rcp_login_form' );


/**
 * Displays a password reset form
 *
 * @access public
 * @return string
 */
function rcp_reset_password_form() {
	if( is_user_logged_in() ) {

		global $rcp_load_css, $rcp_load_scripts;
		// Load CSS and scripts.
		$rcp_load_css = true;
		$rcp_load_scripts = true;

		// get the password reset form fields
		$output = rcp_change_password_form();

		return $output;
	}
}
add_shortcode( 'password_form', 'rcp_reset_password_form' );


/**
 * Displays a list of premium posts
 *
 * @access public
 * @return string
 */
function rcp_list_paid_posts() {
	$paid_posts = rcp_get_paid_posts();
	$list = '';
	if( $paid_posts ) {
		$list .= '<ul class="rcp_paid_posts">';
		foreach( $paid_posts as $post_id ) {
			$list .= '<li><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a></li>';
		}
		$list .= '</ul>';
	}
	return $list;
}
add_shortcode( 'paid_posts', 'rcp_list_paid_posts' );


/**
 * Displays the current user's subscription details
 * templates/subscription.php
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @return string
 */
function rcp_user_subscription_details( $atts, $content = null ) {

	$atts = shortcode_atts( array(
		'option' => ''
	), $atts, 'subscription_details' );

	global $user_ID, $rcp_options, $rcp_load_css;

	$rcp_load_css = true;

	ob_start();

	if( is_user_logged_in() ) {

		rcp_get_template_part( 'subscription' );

	} else {

		echo rcp_login_form_fields();

	}

	return ob_get_clean();
}
add_shortcode( 'subscription_details', 'rcp_user_subscription_details' );


/**
 * Profile Editor Shortcode
 *
 * Outputs the RCP Profile Editor to allow users to amend their details from the front-end
 * templates/profile-editor.php
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @since 1.5
 * @access public
 * @return string
 */
function rcp_profile_editor_shortcode( $atts, $content = null ) {

	global $rcp_load_css;

	$rcp_load_css = true;

	ob_start();

	rcp_get_template_part( 'profile', 'editor' );

	return ob_get_clean();
}
add_shortcode( 'rcp_profile_editor', 'rcp_profile_editor_shortcode' );


/**
 * Update card form short code
 *
 * Displays a form to update the billing credit / debit card.
 * templates/card-update-form.php
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 *
 * @since 2.1
 * @access public
 * @return string
 */
function rcp_update_billing_card_shortcode( $atts, $content = null ) {
	global $rcp_load_css, $rcp_load_scripts, $rcp_membership;

	ob_start();

	$membership_id = isset( $_GET['membership_id'] ) ? absint( $_GET['membership_id'] ) : false;

	if ( empty( $membership_id ) ) {
		$customer   = rcp_get_customer(); // currently logged in customer
		$membership = ( ! empty( $customer ) ) ? rcp_get_customer_single_membership( $customer->get_id() ) : false;
	} else {
		$membership = rcp_get_membership( $membership_id );
	}

	if( is_object( $membership ) && $membership->can_update_billing_card() ) {

		$rcp_membership   = $membership;
		$rcp_load_css     = true;
		$rcp_load_scripts = true;

		do_action( 'rcp_before_update_billing_card_form', $membership );

		if( isset( $_GET['card'] ) ) {

			switch( $_GET['card'] ) {

				case 'updated' :

					echo '<p class="rcp_success"><span>' . __( 'Billing card updated successfully', 'rcp' ) . '</span></p>';

					break;

				case 'not-updated' :

					if( isset( $_GET['msg'] ) ) {
						$message = urldecode( $_GET['msg'] );
					} else {
						$message = __( 'Billing card could not be updated, please try again.', 'rcp' );
					}

					echo '<p class="rcp_error"><span>' . $message . '</span></p>';

					break;

			}

		}

		rcp_get_template_part( 'card-update', 'form' );
		do_action( 'rcp_after_update_billing_card_form', $membership );

	}

	return ob_get_clean();
}
add_shortcode( 'card_details', 'rcp_update_billing_card_shortcode' ); // Old version
add_shortcode( 'rcp_update_card', 'rcp_update_billing_card_shortcode' );

/**
 * Show Customer's Membership Level ID Shortcode
 *
 * Note: This is the ID of the corresponding membership level - NOT the ID of the membership record itself.
 *
 * @since 2.5
 * @access public
 *
 * @return string
 */
function rcp_user_subscription_id_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$customer = rcp_get_customer(); // currently logged in customer

	if ( empty( $customer ) ) {
		return '';
	}

	$membership = rcp_get_customer_single_membership( $customer->get_id() );

	if ( empty( $membership ) ) {
		return '';
	}

	return $membership->get_object_id();
}
add_shortcode( 'subscription_id', 'rcp_user_subscription_id_shortcode' );


/**
 * Show Customer's Membership Level Name Shortcode
 *
 * @since 2.5
 * @access public
 *
 * @return string
 */
function rcp_user_subscription_name_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$customer = rcp_get_customer(); // currently logged in customer

	if ( empty( $customer ) ) {
		return '';
	}

	$membership = rcp_get_customer_single_membership( $customer->get_id() );

	if ( empty( $membership ) ) {
		return '';
	}

	return $membership->get_membership_level_name();
}
add_shortcode( 'subscription_name', 'rcp_user_subscription_name_shortcode' );


/**
 * Show User's Expiration Shortcode
 *
 * @since 2.5
 * @access public
 *
 * @return string
 */
function rcp_user_expiration_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$customer = rcp_get_customer(); // currently logged in customer

	if ( empty( $customer ) ) {
		return '';
	}

	$membership = rcp_get_customer_single_membership( $customer->get_id() );

	if ( empty( $membership ) ) {
		return '';
	}

	return $membership->get_expiration_date();
}
add_shortcode( 'user_expiration', 'rcp_user_expiration_shortcode' );

/**
 * Loads the scripts for the Stripe Checkout shortcode.
 * Hooked into wp_footer via rcp_register_form_stripe_checkout().
 *
 * @since 2.9.11
 */
function rcp_stripe_checkout_shortcode_scripts() {

	wp_print_scripts( 'jquery-blockui' );
	?>

	<script>
		let $ = jQuery;

		let stripeCheckoutHelper = {
			init: function() {
				let stripeCheckoutButton = document.querySelectorAll( '.stripe-button-el' );

				if( ! stripeCheckoutButton ) {
					return;
				}

				stripeCheckoutButton.forEach( function( element ) {
					element.addEventListener( 'click', function( event ) {
						event.preventDefault();
						$.blockUI({
							message: '<?php _e( 'Please wait . . . ', 'rcp' ); ?>',
							css: {
								border: 'none',
								padding: '15px',
								backgroundColor: '#000',
								opacity: 0.75,
								color: '#fff'
							}
						} );
					} );
				} );
			}
		};

		$( document ).ready( function() {
			stripeCheckoutHelper.init();
			$( document ).on( 'DOMNodeRemoved', '.stripe_checkout_app', function() {
				let stripeTokenElement = document.getElementsByName( 'stripeToken' );
				if( ! stripeTokenElement || stripeTokenElement.length === 0 ) {
					$( 'body' ).unblock( { fadeOut: 0 } );
				}
			} );
		} );

	</script>
	<?php
}