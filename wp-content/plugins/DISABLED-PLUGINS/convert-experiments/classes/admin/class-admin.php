<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YCE_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Hooks
	 */
	private function hooks() {
		$admin_page = new YCE_Admin_Page();
		add_action( 'admin_menu', array( $admin_page, 'setup' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'check_option_set' ) );
	}

	/**
	 * Show the warning if the option isa  different an empty string
	 */
	public function check_option_set() {
		if ( '' === Yoast_Convert_Experiments::get_project_ID() ) {
			add_action( 'admin_notices', array( $this, 'show_warning' ) );
		}
	}

	/**
	 * Show the admin warning
	 */
	public function show_warning() {
		echo "<div class='updated'><p><strong>" . __( 'Convert Experiments is almost ready.', 'convert-experiments' ) . "</strong> " . sprintf( __( 'You must <a href="%1$s">enter your Convert Experiments Project Number</a> for it to work.', 'convert-experiments' ), "options-general.php?page=convert-experiences" ) . "</p></div>";
	}

	/**
	 * Add custom link to plugin links
	 *
	 * @param array $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( dirname( Yoast_Convert_Experiments::PLUGIN_FILE ) . '/convert-experiments.php' ) ) {
			$settings_link = '<a href="options-general.php?page=convert-experiences">' . __( 'Settings', 'convert-experiments' ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

}