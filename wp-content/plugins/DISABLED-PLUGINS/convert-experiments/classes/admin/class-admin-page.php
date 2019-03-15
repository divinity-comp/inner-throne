<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YCE_Admin_Page {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * Setup the menu page
	 */
	public function setup() {
		$sub_menu_page = add_submenu_page( 'options-general.php', __( 'Convert Experiments Configuration', 'convert-experiments' ), __( 'Convert Experiments Configuration', 'convert-experiments' ), 'manage_options', 'convert-experiences', array( $this, 'content' ) );

		add_action( 'admin_print_styles-' . $sub_menu_page, array( $this, 'page_scripts' ) );

		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Load page style and scripts
	 */
	public function page_scripts() {
		wp_enqueue_style( 'convert-experiments-admin-page', plugin_dir_url( Yoast_Convert_Experiments::PLUGIN_FILE ) . '/assets/css/convert-experiments.css' );
	}

	/**
	 * Register settings, sections and fields
	 */
	public function page_init() {
		register_setting( 'convert-experiments', 'convert_experiments', array( $this, 'sanitize' ) );

		add_settings_section(
			'section_convert_experiments', // ID
			__( 'Convert Experiments', 'convert-experiments' ), // Title
			array( $this, 'print_section_info' ), // Callback
			'convert-experiences' // Page
		);

		add_settings_field(
			'project_id', // ID
			__( 'Project ID', 'convert-experiments' ), // Title
			array( $this, 'project_ID_callback' ), // Callback
			'convert-experiences', // Page
			'section_convert_experiments' // Section
		);
	}

	/**
	 * Print section info
	 */
	public function print_section_info() {
		echo __( 'Enter your project ID below.', 'convert-experiments' );
	}

	/**
	 * Sanatize field
	 *
	 * @param string $input
	 *
	 * @return string
	 */
	public function sanitize( $input ) {
		return $input;
	}

	public function show_error() {
		echo "<div class='error'><p><strong>" . __( 'Convert Experiments is almost ready.', 'convert-experiments' ) . "</strong> " . sprintf( __( 'Incorrect project ID.', 'convert-experiments' ), "options-general.php?page=convert-experiences" ) . "</p></div>";
	}

	/**
	 * Print the project ID field
	 */
	public function project_ID_callback() {

		printf(
			'<input type="text" id="project_id" name="convert_experiments[project_id]" value="%s" />',
			isset( $this->options['project_id'] ) ? esc_attr( $this->options['project_id'] ) : ''
		);

		if ( '' != $this->options['project_id'] && ! preg_match( '/^[0-9]+\_[0-9]+$/', $this->options['project_id'] ) ) {
			echo "<p class='yce-error'>" . __( 'Incorrect project ID', 'convert-experiments' ) . "</p>\n";
		}
	}

	/**
	 * Print page content
	 */
	public function content() {
		$this->options = Yoast_Convert_Experiments::get_options();
		?>
		<div class="wrap">
			<h2><?php echo __( 'Convert Experiments', 'convert-experiments' ) . ' - ' . __( 'Configuration', 'convert-experiments' ); ?></h2>

			<div class="convert-experiments-page-left">
				<form method="post" action="<?php echo admin_url( 'options.php' ); ?>">
					<?php
					// This prints out all hidden setting fields
					settings_fields( 'convert-experiments' );
					do_settings_sections( 'convert-experiences' );
					submit_button();
					?>
				</form>

				<?php if ( ! isset( $this->options['project_id'] ) || '' == $this->options['project_id'] ) { ?>

					<h3><?php _e( 'Don\'t have a Convert account yet?', 'convert-experiments' ); ?></h3>

					<p><?php printf( __( 'If you don\'t have a Convert account yet, %1$screate one here%2$s!', 'convert-experiments' ), '<a href="http://www.convert.com/pricing/">', '</a>' ); ?></p>


				<?php
				} else {

					preg_match( '/\d+_(\d+)/', $this->options['project_id'], $match );
					if ( is_array( $match ) ) {
						$project_id = $match[1];

						echo '<h3>' . __( 'Create your tests', 'convert-experiments' ) . '</h3>';
						echo '<p>' . sprintf( __( 'You can %1$screate new tests here%2$s.', 'convert-experiments' ), '<a target="_blank" href="https://app.convertexperiments.com/#/projects/' . $project_id . '/">', '</a>' ) . '</p>';
					}
				}
				?>

				<br /><br />

			</div>

			<div class="convert-experiments-page-right">
				<a name="project-id"></a>
				<?php _e( 'Your Project ID is located on the Edit page for your project:', 'convert-experiments' ); ?><br /><br />
				<img src="<?php echo plugin_dir_url( Yoast_Convert_Experiments::PLUGIN_FILE ) ?>assets/images/convert-project-id.png" alt="<?php _e( 'Project ID location', 'convert-experiments' ); ?>" />
			</div>

		</div>
	<?php
	}

}