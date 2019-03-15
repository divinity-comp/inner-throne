<?php
/**
 * Subscription settings
 *
 * @package     RCP\Custom_Redirects\Admin\Subscription\Fields
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Add per-level setting fields
 *
 * @since       1.0.0
 * @return      void
 */
function rcp_custom_redirects_add_redirect_settings() {
	?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="rcp-custom-redirects-subscription-url"><?php _e( 'Subscription Redirect URL', 'rcp-custom-redirects' ); ?></label>
			</th>
			<td>
				<?php
				$subscription_url = ( isset( $_GET['edit_subscription'] ) ? rcp_custom_redirects_get_url( 'subscription', $_GET['edit_subscription'] ) : '' );

				echo '<input type="text" name="rcp-custom-redirects-subscription-url" id="rcp-custom-redirects-subscription-url" value="' . esc_attr( $subscription_url ) . '" style="width: 300px;" />';
				echo '<p class="description">' . __( 'The URL to redirect users to on signup.', 'rcp-custom-redirects' ) . '</p>';
				?>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="rcp-custom-redirects-login-url"><?php _e( 'Login Redirect URL', 'rcp-custom-redirects' ); ?></label>
			</th>
			<td>
				<?php
				$login_url = ( isset( $_GET['edit_subscription'] ) ? rcp_custom_redirects_get_url( 'login', $_GET['edit_subscription'] ) : '' );

				echo '<input type="text" name="rcp-custom-redirects-login-url" id="rcp-custom-redirects-login-url" value="' . esc_attr( $login_url ) . '" style="width: 300px;" />';
				echo '<p class="description">' . __( 'The URL to redirect users to on login.', 'rcp-custom-redirects' ) . '</p>';
				?>
			</td>
		</tr>
	<?php
}
add_action( 'rcp_add_subscription_form', 'rcp_custom_redirects_add_redirect_settings' );
add_action( 'rcp_edit_subscription_form', 'rcp_custom_redirects_add_redirect_settings' );


/**
 * Store the redirect URL in subscription meta
 *
 * @since       1.0.0
 * @param       int $level_id The subscription ID
 * @param       array $args Arguements passed to the action
 */
function rcp_custom_redirects_save( $level_id = 0, $args ) {
	if( isset( $_POST['rcp-custom-redirects-subscription-url'] ) ) {
		$url = $_POST['rcp-custom-redirects-subscription-url'];
		$subscription_urls = get_option( 'rcp_custom_redirects_subscription_urls', array() );

		if( ! empty( $url ) ) {
			$subscription_urls[ $level_id ] = sanitize_text_field( $url );
		} elseif( is_array( $subscription_urls ) && array_key_exists( $level_id, $subscription_urls ) ) {
			unset( $subscription_urls[ $level_id ] );
		}

		update_option( 'rcp_custom_redirects_subscription_urls', $subscription_urls );
	}

	if( isset( $_POST['rcp-custom-redirects-login-url'] ) ) {
		$url = $_POST['rcp-custom-redirects-login-url'];
		$login_urls = get_option( 'rcp_custom_redirects_login_urls', array() );

		if( ! empty( $url ) ) {
			$login_urls[ $level_id ] = sanitize_text_field( $url );
		} elseif( is_array( $login_urls ) && array_key_exists( $level_id, $login_urls ) ) {
			unset( $login_urls[ $level_id ] );
		}

		update_option( 'rcp_custom_redirects_login_urls', $login_urls );
	}
}
add_action( 'rcp_add_subscription', 'rcp_custom_redirects_save', 10, 2 );
add_action( 'rcp_edit_subscription_level', 'rcp_custom_redirects_save', 10, 2 );