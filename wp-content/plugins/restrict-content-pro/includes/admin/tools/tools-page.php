<?php
/**
 * Tools Page
 *
 * @package     Restrict Content Pro
 * @subpackage  Admin/Tools
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Displays the Tools page
 *
 * @since 2.5
 * @return void
 */
function rcp_tools_page() {
	if( ! current_user_can( 'rcp_manage_settings' ) ) {
		return;
	}

	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'system_info';
    ?>

	<div class="wrap">
		<h1><?php _e( 'Restrict Content Pro Tools', 'rcp' ); ?></h1>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( rcp_get_tools_tabs() as $tab_id => $tab_name ) {
				$tab_url = add_query_arg( array(
					'tab' => $tab_id
				) );

				$tab_url = remove_query_arg( array(
					'rcp_message'
				), $tab_url );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';
				echo '<a href="' . esc_url( $tab_url ) . '" class="nav-tab' . $active . '">' . esc_html( $tab_name ) . '</a>';
			}
			?>
		</h2>

		<div class="metabox-holder">
			<?php do_action( 'rcp_tools_tab_' . $active_tab ); ?>
		</div>
	</div>
<?php
}

/**
 * Retrieve tools tabs
 *
 * @since 2.9
 * @return array
 */
function rcp_get_tools_tabs() {

	global $rcp_options;

	$tabs = array(
		'system_info' => __( 'System Info', 'rcp' ),
		'debug'       => __( 'Debugging', 'rcp' )
	);

	if ( ! empty( $_GET['tab'] ) && 'batch' === $_GET['tab'] ) {
		$tabs['batch'] = __( 'Batch Processing', 'rcp' );
	}

	return apply_filters( 'rcp_tools_tabs', $tabs );

}

/**
 * Display system information tab
 *
 * @since 2.9
 * @return void
 */
function rcp_tools_display_system_info() {

	include RCP_PLUGIN_DIR . 'includes/admin/tools/system-info.php';
	?>
	<form action="<?php echo esc_url( admin_url( 'admin.php?page=rcp-tools' ) ); ?>" method="post" dir="ltr">
		<textarea readonly="readonly" onclick="this.focus(); this.select()" id="rcp-system-info-textarea" name="rcp-sysinfo" title="To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac)."><?php echo rcp_tools_system_info_report(); ?></textarea>
		<p class="submit">
			<input type="hidden" name="rcp-action" value="download_sysinfo" />
			<?php submit_button( 'Download System Info File', 'primary', 'rcp-download-sysinfo', false ); ?>
		</p>
	</form>
	<?php

}
add_action( 'rcp_tools_tab_system_info', 'rcp_tools_display_system_info' );

/**
 * Listens for system info download requests and delivers the file
 *
 * @since 2.5
 * @return void
 */
function rcp_tools_sysinfo_download() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( ! current_user_can( 'rcp_manage_settings' ) ) {
		return;
	}

	if ( ! isset( $_POST['rcp-download-sysinfo'] ) ) {
		return;
	}

	nocache_headers();

	header( 'Content-Type: text/plain' );
	header( 'Content-Disposition: attachment; filename="rcp-system-info.txt"' );

	echo wp_strip_all_tags( $_POST['rcp-sysinfo'] );
	exit;
}
add_action( 'admin_init', 'rcp_tools_sysinfo_download' );

/**
 * Display debug log
 *
 * @since 2.9
 * @return void
 */
function rcp_tools_display_debug() {

	$logs = new RCP_Logging();
	?>
	<div class="postbox">
		<h3><?php _e( 'Debug Log', 'rcp' ); ?></h3>
		<div class="inside">
			<form id="rcp-debug-log" method="post">
				<p><label for="rcp-debug-log-contents"><?php _e( 'Any Restrict Content Pro errors that occur will be logged to this file.', 'rcp' ); ?></label></p>
				<textarea id="rcp-debug-log-contents" name="rcp-debug-log-contents" class="large-text" rows="15"><?php echo esc_textarea( $logs->get_log() ); ?></textarea>
				<p class="submit">
					<input type="hidden" name="rcp-action" value="submit_debug_log">
					<?php
					wp_nonce_field( 'rcp_submit_debug_log', 'rcp_debug_log_nonce' );
					submit_button( __( 'Download Debug Log', 'rcp' ), 'primary', 'rcp_download_debug_log', false );
					submit_button( __( 'Clear Log', 'rcp' ), 'secondary', 'rcp_clear_debug_log', false );
					?>
				</p>
			</form>
		</div>
	</div>
	<?php

}
add_action( 'rcp_tools_tab_debug', 'rcp_tools_display_debug' );

/**
 * Handles submit actions for the debug log
 *
 * @since 2.9
 * @return void
 */
function rcp_submit_debug_log() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( ! current_user_can( 'rcp_manage_settings' ) ) {
		return;
	}

	if ( ! isset( $_POST['rcp_debug_log_nonce'] ) || ! wp_verify_nonce( $_POST['rcp_debug_log_nonce'], 'rcp_submit_debug_log' ) ) {
		return;
	}

	if ( isset( $_POST['rcp_download_debug_log'] ) ) {

		// Download debug log
		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="rcp-debug-log.txt"' );

		echo wp_strip_all_tags( $_REQUEST['rcp-debug-log-contents'] );
		exit;

	} elseif ( isset( $_POST['rcp_clear_debug_log'] ) ) {

		// Clear debug log.
		$logs = new RCP_Logging();
		$logs->clear_log();

		wp_safe_redirect( admin_url( 'admin.php?page=rcp-tools&tab=debug') );
		exit;

	}

}
add_action( 'admin_init', 'rcp_submit_debug_log' );

/**
 * Displays the batch processing tab on the Tools page.
 *
 * @since 3.0
 */
function rcp_batch_processing_page() {

	$queue_name = ! empty( $_GET['rcp-queue'] ) ? sanitize_key( $_GET['rcp-queue'] ) : 'rcp_core';

	if( ! empty( $queue_name ) ) {
		$jobs = \RCP\Utils\Batch\get_jobs( array(
			'queue' => $queue_name
		) );
	} ?>

	<div class="wrap">

		<?php

		if( empty( $jobs ) ) {
			echo '<p>' . __( 'A valid job queue was not provided.', 'rcp' ) . '</p></div>';
			return;
		}

		/**
		 * @var \RCP\Utils\Batch\Job $job
		 */
		foreach( $jobs as $key => $job ) {
			if( $job->is_completed() ) {
				echo '<p>' . sprintf( __( '%s has already been completed.', 'rcp' ), $job->get_name() ) . '</p></div>';
				continue;
			} ?>
			<table id="rcp-batch-processing-job-<?php echo esc_attr( $job->get_id() ); ?>" class="wp-list-table widefat fixed posts rcp-batch-processing-job-table">
				<thead>
				<tr>
					<th><?php echo ! empty( $job ) ? $job->get_name() : ''; ?></th>
					<th><?php _e( 'Progress', 'rcp' ); ?></th>
					<th><?php _e( 'Actions', 'rcp' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?php echo esc_html( $job->get_description() ); ?></td>
					<td>
						<span class="rcp-batch-processing-job-progress-bar"><span style="width: <?php echo esc_attr( $job->get_percent_complete() ); ?>%;"></span></span>
						<span class="rcp-batch-processing-job-progress-text description"><?php printf( __( '%s%% complete', 'rcp' ), '<span class="rcp-batch-processing-job-percent-complete">' . $job->get_percent_complete() . '</span>' ); ?></span>
					</td>
					<td>
						<form class="rcp-batch-form">
							<input type="hidden" name="rcp-job-step" class="rcp-batch-processing-job-step" value="<?php echo esc_attr( $job->get_step() ); ?>" />
							<input type="hidden" name="rcp-job-id" class="rcp-batch-processing-job-id" value="<?php echo esc_attr( $job->get_id() ); ?>" />
							<input type="submit" value="<?php echo $job->get_percent_complete() > 0 ? esc_attr( 'Continue Processing', 'rcp' ) : esc_attr( 'Start Processing', 'rcp' ); ?>" class="button-primary"/>
							<span class="spinner"></span>
							<span class="rcp-batch-processing-message"></span>
						</form>
					</td>
				</tr>
				</tbody>
			</table>
			<?php
		}
		?>

	</div>
	<?php
}
add_action( 'rcp_tools_tab_batch', 'rcp_batch_processing_page' );