<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://adhipg.in/
 * @since      1.0.0
 *
 * @package    Innerthrone_Payments
 * @subpackage Innerthrone_Payments/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Innerthrone_Payments
 * @subpackage Innerthrone_Payments/public
 * @author     Adhip Gupta <me@adhipg.in>
 */
class Innerthrone_Payments_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->plans = array(
			'plan1' => array(
				'payments' => 1,
				'amount' => 879,
				'currency' => 'usd',
				'description' => 'A one time payment of $879 (this is a HUGE limited time discount on the normal $1749 price)'
			)/*,
			'plan2' => array(
				'payments' => 3,
				'amount' => 599,
				'currency' => 'usd',
				'stripe_plan_id' => 'innerthrone-3month-599',
				'description' => 'Three equal installments of $599'
			)*/
		);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Innerthrone_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Innerthrone_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/innerthrone-payments-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Innerthrone_Payments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Innerthrone_Payments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/innerthrone-payments-public.js', array( 'jquery' ), $this->version, false );

	}


	public function shortcode_payment_form( $atts ) {
		ob_start();
		include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/payment_form.php';
		$content = ob_get_clean();

		return $content;
	}

	public function process_innerthrone_payment() {

		$vars = array( 'email', 'name', 'token', 'token-string', 'plan' );
		foreach( $vars as $var ) {
			if( isset($_POST[$var]) ) {
				$$var = $_POST[$var];
			}
		}

		// \Stripe\Stripe::setApiKey("sk_test_HurpcJHWC63XujIMxQpLAAKn"); Test
		\Stripe\Stripe::setApiKey("sk_live_Cjay66z8wAEqFNnu9uka3Y11"); //LIVE
		
		$selected_plan = $this->plans[$plan];
		 $customer = \Stripe\Customer::create(array(
			'card' => $token,
			'description' => $email,
			'email' => $email
		));

		if( $selected_plan['payments'] === 1 ) {
			try {
				$charge = \Stripe\Charge::create(array(
					"amount" => $selected_plan['amount'] * 100,
					"currency" => $selected_plan['currency'],
					"customer" => $customer->id,
					"description" => $selected_plan['description']
				));
				echo json_encode(array("success" => true));
			}
			catch( \Stripe\CardError $e ) {
				echo json_encode(array(
					'error' => true,
					'err' => $e
				));
				die();
			}
		}
		else {
			try {
				$response = $customer->subscriptions->create(array(
					"plan" => $selected_plan['stripe_plan_id'])
				);
				echo json_encode(array("success" => true));
			}
			catch( \Stripe\Error $e ) {
				echo json_encode(array(
					'error' => true,
					'err' => $e
				));
				die();
			}
		}
		die();
	}

}
