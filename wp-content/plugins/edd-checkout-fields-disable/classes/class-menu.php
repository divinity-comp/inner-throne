<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// This is based off of work by bbPress and also EDD itself.
class CFM_Menu {

	public $minimum_capability = 'manage_options';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus'), 9 );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	public function admin_menus() {
		add_dashboard_page( 'About CFM', 'About CFM', $this->minimum_capability,'cfm-about', array( $this, 'about_screen' ));
		add_submenu_page( 'edit.php?post_type=download', 'Checkout Fields', 'Checkout Fields', 'manage_options', 'post.php?post=' . get_option( 'edd_cfm_id') . '&action=edit');
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function admin_head() {
		global $submenu;
		remove_submenu_page( 'index.php', 'cfm-about' );
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.about-wrap h1 {
			margin-right: 0px;
		}
		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since 1.4
	 * @return void
	 */
	public function about_screen() {
		list( $display_version ) = explode( '-', cfm_plugin_version );
		?>
		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to EDD Checkout Fields Manager %s!', 'edd_cfm' ), $display_version ); ?></h1>
			<div class="about-text"><?php _e( 'Thank you for updating to the latest version!', 'edd_cfm' ); ?></div>
			<div class="changelog">
				<h3><?php _e( 'What\'s New:', 'edd_cfm' );?></h3>

				<div class="feature-section">
					<h4><?php _e( '15 Field Types', 'edd_cfm' );?></h4>
					<p><?php _e( 'With 15 field types, you can make whatever you want on the checkout page come to reality in a couple clicks.', 'edd_cfm' );?></p>

					<h4><?php _e( 'File Upload', 'edd_cfm' );?></h4>
					<p><?php _e( 'With the file upload field, having users upload files during checkout is quick and simple', 'edd_cfm' );?></p>
					
					<h4><?php _e( 'Drag and Drop Re-Ordering of fields', 'edd_cfm' );?></h4>
					<p><?php _e( 'To change the order of the fields on the checkout page, simply drag and drop! ', 'edd_cfm' );?></p>
				</div>
			</div>
		</div>
		<?php
	}
		
	public function welcome() {
		global $edd_options;

		// Bail if no activation redirect
		if ( ! get_transient( '_edd_cfm_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_edd_cfm_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		wp_safe_redirect( admin_url( 'index.php?page=cfm-about' ) ); exit;
	}
}