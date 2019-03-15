<?php
/**
 * Scripts
 *
 * @package     Restrict Content Pro
 * @subpackage  Scripts
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Load admin scripts
 *
 * @param string $hook Page hook.
 *
 * @return void
 */
function rcp_admin_scripts( $hook ) {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	global $rcp_options, $rcp_members_page, $rcp_customers_page, $rcp_subscriptions_page, $rcp_discounts_page, $rcp_payments_page, $rcp_reports_page, $rcp_settings_page, $rcp_export_page, $rcp_help_page, $rcp_tools_page;
	$pages = array( $rcp_members_page, $rcp_customers_page, $rcp_subscriptions_page, $rcp_discounts_page, $rcp_payments_page, $rcp_reports_page, $rcp_settings_page, $rcp_export_page, $rcp_tools_page, $rcp_help_page );

	$pages[] = 'post.php';
	$pages[] = 'post-new.php';
	$pages[] = 'edit.php';

	if( false !== strpos( $hook, 'rcp-restrict-post-type' ) ) {
		$pages[] = $hook;
	}

	if ( $rcp_customers_page == $hook ) {
		// Load the password show/hide feature and strength meter.
		wp_enqueue_script( 'user-profile' );
	}

	if ( $rcp_discounts_page == $hook ) {
		wp_enqueue_script( 'jquery-ui-timepicker', RCP_PLUGIN_URL . 'includes/js/jquery-ui-timepicker-addon' . $suffix . '.js', array( 'jquery-ui-datepicker', 'jquery-ui-slider' ), '1.6.3' );
	}

	if( in_array( $hook, $pages ) ) {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-tooltip' );
		wp_enqueue_script( 'bbq',  RCP_PLUGIN_URL . 'includes/js/jquery.ba-bbq.min.js' );
		wp_enqueue_script( 'rcp-admin-scripts',  RCP_PLUGIN_URL . 'includes/js/admin-scripts.js', array( 'jquery' ), RCP_PLUGIN_VERSION );
	}

	if ( $rcp_reports_page == $hook ) {
		wp_enqueue_script( 'jquery-flot', RCP_PLUGIN_URL . 'includes/js/jquery.flot.min.js' );
	}

	if( in_array( $hook, $pages ) ) {
		wp_localize_script( 'rcp-admin-scripts', 'rcp_vars', array(
				'action_cancel'       => __( 'Cancel', 'rcp' ),
				'action_edit'         => __( 'Edit', 'rcp' ),
				'rcp_member_nonce'    => wp_create_nonce( 'rcp_member_nonce' ),
				'cancel_user'         => __( 'Are you sure you wish to cancel this member\'s subscription?', 'rcp' ),
				'delete_customer'     => __( 'Are you sure you want to delete this customer? This action is irreversible. All their memberships will be cancelled. Proceed?', 'rcp' ),
				'delete_membership'   => __( 'Are you sure you want to delete this membership? This action is irreversible. Proceed?', 'rcp' ),
				'delete_subscription' => __( 'If you delete this subscription, all members registered with this level will be canceled. Proceed?', 'rcp' ),
				'delete_payment'      => __( 'Are you sure you want to delete this payment? This action is irreversible. Proceed?', 'rcp' ),
				'delete_discount'     => __( 'Are you sure you want to delete this discount? This action is irreversible. Proceed?', 'rcp' ),
				'delete_reminder'     => __( 'Are you sure you want to delete this reminder email? This action is irreversible. Proceed?', 'rcp' ),
				'expire_membership'   => __( 'Are you sure you want to expire this membership? The customer will lose access immediately.', 'rcp' ),
				'missing_username'    => __( 'You must choose a username', 'rcp' ),
				'currency_sign'       => rcp_currency_filter(''),
				'currency_pos'        => isset( $rcp_options['currency_position'] ) ? $rcp_options['currency_position'] : 'before',
				'use_as_logo'         => __( 'Use as Logo', 'rcp' ),
				'choose_logo'         => __( 'Choose a Logo', 'rcp' ),
				'can_cancel_member'   => ( $hook == $rcp_members_page && isset( $_GET['edit_member'] ) && rcp_can_member_cancel( absint( $_GET['edit_member'] ) ) ),
				'cancel_subscription' => __( 'Cancel subscription at gateway', 'rcp' ),
				'currencies'          => json_encode( rcp_get_currencies() )
			)
		);
	}

	if ( $rcp_tools_page === $hook ) {
		wp_enqueue_script( 'rcp-batch', RCP_PLUGIN_URL . 'includes/batch/batch.js', array( 'jquery' ), RCP_PLUGIN_VERSION );
		wp_localize_script( 'rcp-batch', 'rcp_batch_vars', array(
			'batch_nonce' => wp_create_nonce( 'rcp_batch_nonce' ),
			'i18n'        => array(
				'job_fail'    => __( 'Job failed to complete successfully.', 'rcp' ),
				'job_retry'   => __( 'Try again.', 'rcp' )
			)
		) );
	}
}
add_action( 'admin_enqueue_scripts', 'rcp_admin_scripts' );

/**
 * Sets the URL of the Restrict > Help page
 *
 * @access      public
 * @since       2.5
 * @return      void
 */
function rcp_admin_help_url() {
?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#adminmenu .toplevel_page_rcp-members .wp-submenu-wrap a[href="admin.php?page=rcp-help"]').prop('href', 'http://docs.restrictcontentpro.com/').prop('target', '_blank');
	});
	</script>
<?php
}
add_action( 'admin_head', 'rcp_admin_help_url' );

/**
 * Load admin stylesheets
 *
 * @param string $hook Page hook.
 *
 * @return void
 */
function rcp_admin_styles( $hook ) {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	global $rcp_members_page, $rcp_customers_page, $rcp_subscriptions_page, $rcp_discounts_page, $rcp_payments_page, $rcp_reports_page, $rcp_settings_page, $rcp_export_page, $rcp_help_page, $rcp_tools_page, $rcp_add_ons_page;

	$pages = array(
		$rcp_members_page,
		$rcp_customers_page,
		$rcp_subscriptions_page,
		$rcp_discounts_page,
		$rcp_payments_page,
		$rcp_reports_page,
		$rcp_settings_page,
		$rcp_export_page,
		$rcp_help_page,
		$rcp_tools_page,
        $rcp_add_ons_page,
		'post.php',
		'edit.php',
		'post-new.php'
	);

	if( false !== strpos( $hook, 'rcp-restrict-post-type' ) ) {
		$pages[] = $hook;
	}

	if( in_array( $hook, $pages ) ) {
		wp_enqueue_style( 'datepicker',  RCP_PLUGIN_URL . 'includes/css/datepicker' . $suffix . '.css' );
		wp_enqueue_style( 'rcp-admin',  RCP_PLUGIN_URL . 'includes/css/admin-styles' . $suffix . '.css', array(), RCP_PLUGIN_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 'rcp_admin_styles' );


/**
 * Register form CSS
 *
 * @return void
 */
function rcp_register_css() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_style('rcp-form-css',  RCP_PLUGIN_URL . 'includes/css/forms' . $suffix . '.css', array(), RCP_PLUGIN_VERSION );
}
add_action('init', 'rcp_register_css');

/**
 * Register front-end scripts
 *
 * @return void
 */
function rcp_register_scripts() {

	global $rcp_options;

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_script( 'rcp-register',  RCP_PLUGIN_URL . 'includes/js/register' . $suffix . '.js', array('jquery'), RCP_PLUGIN_VERSION );
	wp_register_script( 'jquery-blockui',  RCP_PLUGIN_URL . 'includes/js/jquery.blockUI.js', array('jquery'), RCP_PLUGIN_VERSION );
	wp_register_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js', array(), RCP_PLUGIN_VERSION );

}
add_action( 'init', 'rcp_register_scripts' );

/**
 * Load form CSS
 *
 * @return void
 */
function rcp_print_css() {
	global $rcp_load_css, $rcp_options;

	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $rcp_load_css || ( isset( $rcp_options['disable_css'] ) && $rcp_options['disable_css'] ) )
		return; // this means that neither short code is present, so we get out of here

	wp_print_styles( 'rcp-form-css' );
}
add_action( 'wp_footer', 'rcp_print_css' );

/**
 * Load form scripts
 *
 * @return void
 */
function rcp_print_scripts() {
	global $rcp_load_scripts, $rcp_options;

	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $rcp_load_scripts )
		return; // this means that neither short code is present, so we get out of here

	wp_localize_script('rcp-register', 'rcp_script_options',
		array(
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'register'           => apply_filters ( 'rcp_registration_register_button', __( 'Register', 'rcp' ) ),
			'pleasewait'         => __( 'Please Wait . . . ', 'rcp' ),
			'pay_now'            => __( 'Submit Payment', 'rcp' ),
			'user_has_trialed'   => is_user_logged_in() && rcp_has_used_trial(),
			'trial_levels'       => rcp_get_trial_level_ids(),
			'auto_renew_default' => isset( $rcp_options['auto_renew_checked_on'] ),
			'recaptcha_enabled'  => rcp_is_recaptcha_enabled()
		)
	);

	wp_print_scripts( 'rcp-register' );
	wp_print_scripts( 'jquery-blockui' );
	if ( isset( $rcp_options['enable_recaptcha'] ) ) {
		wp_print_scripts( 'recaptcha' );
	}

}
add_action( 'wp_footer', 'rcp_print_scripts' );
