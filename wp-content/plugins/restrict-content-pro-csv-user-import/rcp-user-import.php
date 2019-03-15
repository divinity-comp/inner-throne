<?php
/**
 * Plugin Name: Restrict Content Pro - CSV User Import
 * Plugin URL: https://restrictcontentpro.com/downloads/csv-user-import/
 * Description: Allows you to import a CSV of users into Restrict Content Pro
 * Version: 1.1.9
 * Author: Pippin Williamson
 * Author URI: https://pippinsplugins.com
 * Contributors: mordauk, chriscoyier, mindctrl, nosegraze
 */

if ( ! defined( 'RCP_CSVUI_PLUGIN_DIR' ) ) {
	define( 'RCP_CSVUI_PLUGIN_DIR', dirname( __FILE__ ) );
}

ini_set( 'max_execution_time', 90 );

/**
 * Create admin sub-menu page for CSV Import
 *
 * @return void
 */
function rcp_csvui_menu_page() {
	global $rcp_csvui_import_page;
	$rcp_csvui_import_page = add_submenu_page( 'rcp-members', __( 'CSV Import', 'rcp_csvui' ), __( 'CSV Import', 'rcp_csvui' ), 'manage_options', 'rcp-csv-import', 'rcp_csvui_purchase_import' );
}
add_action( 'admin_menu', 'rcp_csvui_menu_page', 100 );

/**
 * Load admin scripts on CSV Import page.
 *
 * @param string $hook Current page hook.
 *
 * @since 1.1.7
 * @return void
 */
function rcp_csvui_admin_scripts( $hook ) {

	if ( 'restrict_page_rcp-csv-import' != $hook ) {
		return;
	}

	if ( ! function_exists( 'rcp_get_subscription_levels' ) ) {
		return;
	}

	wp_enqueue_script( 'rcp-csv-user-import', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', array( 'jquery' ), '1.1.7', true );

	$levels         = rcp_get_subscription_levels();
	$free_level_ids = array();

	if ( ! empty( $levels ) ) {
		foreach ( $levels as $level ) {
			if ( empty( $level->duration ) && empty( $level->price ) ) {
				$free_level_ids[] = $level->id;
			}
		}
	}

	wp_localize_script( 'rcp-csv-user-import', 'rcp_csvui_vars', array(
		'free_level_ids'      => $free_level_ids,
		'confirm_edit_status' => __( 'Members of free subscription levels should be given the status "Free" - not "Active". Changing this may cause certain features like the [is_paid] shortcode to not work as expected.', 'rcp_csvui' )
	) );

}
add_action( 'admin_enqueue_scripts', 'rcp_csvui_admin_scripts' );

/**
 * Render the CSV Import page
 *
 * @return void
 */
function rcp_csvui_purchase_import() {
	?>
	<div class="wrap">
		<h2><?php _e( 'CSV User Import', 'rcp_csvui' ); ?></h2>
		<?php settings_errors( 'rcp-csv-ui' ); ?>
		<P><?php _e( 'Use this tool to import user memberships into Restrict Content Pro', 'rcp_csvui' ); ?></p>
		<p><?php printf( __( '<strong>Note</strong>: your CSV should contain the following fields: <em>User Email, First Name, Last Name, User Login</em>. If you wish to update existing users, you can include a <em>User ID</em> field as well. See the <a href="%s">documentation article</a> for more available fields.', 'rcp_csvui' ), 'http://docs.restrictcontentpro.com/article/1621-csv-user-import' ); ?></p>
		<script type="text/javascript">jQuery(document).ready(function($) { var dateFormat = 'yy-mm-dd'; $('.rcp_datepicker').datepicker({dateFormat: dateFormat}); });</script>
		<form id="rcp_csvui_import" enctype="multipart/form-data" method="post">
			<table class="form-table">
				<tr>
					<th><?php _e( 'CSV File', 'rcp_csvui' ); ?></th>
					<td>
						<input type="file" accept=".csv, text/csv" name="rcp_csvui_file"/>
						<div class="description"><?php _e( 'Select the CSV file to import. Must follow guidelines above.', 'rcp_csvui' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Subscription Level', 'rcp_csv_ui' ); ?></th>
					<td>
						<select name="rcp_level" id="rcp_level">
						<?php
						$subscription_levels = rcp_get_subscription_levels();
						foreach ( $subscription_levels as $level ) {
							echo '<option value="' . esc_attr( absint( $level->id ) ) . '">' . esc_html( $level->name ) . '</option>';
						}
						?>
						</select>
						<div class="description"><?php _e( 'Select the subscription level to add users to.', 'rcp_csvui' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e('Status', 'rcp_csv_ui'); ?></th>
					<td>
						<select name="rcp_status" id="rcp_status">
							<option value="active"><?php _e( 'Active', 'rcp_csvui' ); ?></option>
							<option value="pending"><?php _e( 'Pending', 'rcp_csvui' ); ?></option>
							<option value="cancelled"><?php _e( 'Cancelled', 'rcp_csvui' ); ?></option>
							<option value="expired"><?php _e( 'Expired', 'rcp_csvui' ); ?></option>
							<?php if ( version_compare( RCP_PLUGIN_VERSION, '3.0', '<' ) ) : ?>
								<option value="free"><?php _e( 'Free', 'rcp_csvui' ); ?></option>
							<?php endif; ?>
						</select>
						<a href="#" id="rcp_csvui_edit_status" style="display:none;"><?php _e( 'Edit Status', 'rcp_csvui' ); ?></a>
						<div class="description"><?php _e( 'Select the subscription status to import users with.', 'rcp_csvui' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Expiration', 'rcp_csv_ui' ); ?></th>
					<td>
						<input type="text" name="rcp_expiration" id="rcp_expiration" value="" class="rcp_datepicker"/>
						<div class="description"><?php _e( 'Select the expiration date for all users. Leave this blank and the expiration date will be automatically calculated based on the selected subscription.', 'rcp_csvui' ); ?></div>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Disable Notification Emails', 'rcp_csv_ui' ); ?></th>
					<td>
						<input type="checkbox" name="rcp_member_import_disable_notification_emails" id="rcp_member_import_disable_notification_emails" value="1"/>
						<span class="description"><?php _e( 'Check on to disable member and admin notification emails during the import process.', 'rcp_csvui' ); ?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Send Password Reset Emails', 'rcp_csv_ui' ); ?></th>
					<td>
						<input type="checkbox" name="rcp_member_import_send_password_reset_emails" id="rcp_member_import_send_password_reset_emails" value="1"/>
						<span class="description"><?php _e( 'If checked, new accounts will be sent a password reset email. Existing accounts will not receive one.', 'rcp_csvui' ); ?></span>
					</td>
				</tr>

			</table>
			<input type="hidden" name="rcp_action" value="process_csv_import"/>
			<?php wp_nonce_field( 'rcp_csvui_nonce', 'rcp_csvui_nonce' ); ?>
			<?php submit_button( __( 'Upload and Import', 'rcp_csvui' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Process CSV import
 *
 * @return void
 */
function rcp_csvui_process_csv() {

	if ( isset( $_POST['rcp_action'] ) && $_POST['rcp_action'] == 'process_csv_import' ) {

		if ( ! wp_verify_nonce( $_POST['rcp_csvui_nonce'], 'rcp_csvui_nonce' ) ) {
			return;
		}

		$import_file = ! empty( $_FILES['rcp_csvui_file'] ) ? $_FILES['rcp_csvui_file']['tmp_name'] : false;

		if ( ! $import_file ) {
			wp_die( __('Please upload a CSV file.', 'rcp_csvui' ), __('Error') );
		}

		$csv = array_map( 'str_getcsv', file( $import_file ) );
		array_walk( $csv, function( &$a ) use ( $csv ) {
			$a = array_combine( $csv[0], $a );
		});
		array_shift( $csv );

		/**
		 * @var RCP_Levels $rcp_levels_db
		 */
		global $rcp_levels_db;

		$subscription_id = isset( $_POST['rcp_level'] ) ? absint( $_POST['rcp_level'] ) : false;

		if ( ! $subscription_id ) {
			wp_die( __('Please select a subscription level.', 'rcp_csvui' ), __('Error') );
		}

		$subscription_details = rcp_get_subscription_details( $subscription_id );

		if ( ! $subscription_details ) {
			wp_die( sprintf( __('That subscription level does not exist: #%d.', 'rcp_csvui' ), $subscription_id ), __('Error') );
		}

		$status = isset( $_POST['rcp_status'] ) ? sanitize_text_field( $_POST['rcp_status'] ) : 'free';

		// Maybe disable notification emails.
		if ( ! empty( $_POST['rcp_member_import_disable_notification_emails'] ) ) {
			remove_action( 'rcp_set_status', 'rcp_email_on_expiration', 11 );
			remove_action( 'rcp_set_status', 'rcp_email_on_activation', 11 );
			remove_action( 'rcp_set_status', 'rcp_email_on_free_trial', 11 );
			remove_action( 'rcp_set_status_free', 'rcp_email_on_free_subscription', 11 );
			remove_action( 'rcp_set_status', 'rcp_email_on_cancellation', 11 );

			if ( version_compare( RCP_PLUGIN_VERSION, '3.0', '>=' ) ) {
				remove_action( 'rcp_membership_post_activate', 'rcp_email_on_membership_activation', 10 );
				remove_action( 'rcp_membership_post_cancel', 'rcp_email_on_membership_cancellation', 10 );
				remove_action( 'rcp_transition_membership_status_expired', 'rcp_email_on_membership_expiration', 10 );
			}
		}

		foreach ( $csv as $row_number => $user ) {

			$expiration = ! empty( $_POST['rcp_expiration'] ) ? sanitize_text_field( $_POST['rcp_expiration'] ) : false;
			$email      = ! empty( $user['User Email'] ) ? $user['User Email'] : $user['user_email'];

			if ( ! empty( $user['id'] ) ) {

				$user_data = get_userdata( $user['id'] );

			} elseif ( ! empty( $user['ID'] ) ) {

				$user_data = get_userdata( $user['ID'] );

			} elseif ( ! empty( $user['User ID'] ) ) {

				$user_data = get_userdata( $user['User ID'] );

			} else {

				$user_data = get_user_by( 'email', $email );

			}

			// Password
			if ( ! empty( $user['User Password'] ) ) {
				$password = $user['User Password'];
			} elseif ( ! empty( $user['user_password'] ) ) {
				$password = $user['user_password'];
			} else {
				$password = '';
			}

			// First name
			if ( ! empty( $user['First Name'] ) ) {
				$first_name = $user['First Name'];
			} elseif ( ! empty( $user['first_name'] ) ) {
				$first_name = $user['first_name'];
			} else {
				$first_name = '';
			}

			// Last name
			if ( ! empty( $user['Last Name'] ) ) {
				$last_name = $user['Last Name'];
			} elseif ( ! empty( $user['last_name'] ) ) {
				$last_name = $user['last_name'];
			} else {
				$last_name = '';
			}

			if ( ! $user_data ) {

				/**
				 * Create a new account.
				 */

				if ( empty( $password ) ) {
					$password = wp_generate_password();
				}

				// User login
				if ( ! empty( $user['User Login'] ) ) {
					$user_login = $user['User Login'];
				} elseif ( ! empty( $user['user_login'] ) ) {
					$user_login = $user['user_login'];
				} else {
					$user_login = $email;
				}

				// Email address and user login are required for new accounts.
				if ( empty( $email ) || empty( $user_login ) ) {
					if ( function_exists( 'rcp_log' ) ) {
						// We add +1 to the row number for clarity to the end user, who may not realize they start from 0 rather than 1.
						rcp_log( sprintf( 'CSV Import: skipping row #%d - missing email address and/or user login.', ( $row_number + 1 ) ) );
					}

					continue;
				}

				$user_data  = array(
					'user_login' => sanitize_text_field( $user_login ),
					'user_email' => sanitize_text_field( $email ),
					'first_name' => sanitize_text_field( $first_name ),
					'last_name'  => sanitize_text_field( $last_name ),
					'user_pass'  => $password,
					'role'       => ! empty( $subscription_details->role ) ? $subscription_details->role : 'subscriber'
				);

				$user_id = wp_insert_user( $user_data );

				if ( ! empty( $_POST['rcp_member_import_send_password_reset_emails'] ) ) {
					wp_new_user_notification( $user_id, null, 'user' );
				}

			} else {

				/**
				 * Update an existing account with new information.
				 */

				$user_id = $user_data->ID;

				$data_to_update = array();

				if ( ! empty( $password ) ) {
					$data_to_update['user_pass'] = $password;
				}

				if ( ! empty( $first_name ) ) {
					$data_to_update['first_name'] = sanitize_text_field( $first_name );
				}

				if ( ! empty( $last_name ) ) {
					$data_to_update['last_name'] = sanitize_text_field( $last_name );
				}

				if ( ! empty( $data_to_update ) && ! empty( $user_id ) ) {
					$data_to_update['ID'] = $user_id;

					wp_update_user( $data_to_update );
				}

			}

			/**
			 * If the expiration date wasn't specified on the import screen,
			 * check the CSV file. If no expiration in the CSV file, calculate
			 * the expiration date based on the subscription level.
			 */
			if ( ! $expiration || strlen( trim( $expiration ) ) <= 0 ) {
				if ( ! empty( $user['Expiration'] ) ) {
					$expiration = $user['Expiration'];
				} elseif ( ! empty( $user['expiration'] ) ) {
					$expiration = $user['expiration'];
				} else {
					// calculate expiration here
					$expiration = rcp_calculate_subscription_expiration( $subscription_id );
				}
			}

			// Make sure a supplied date is formatted correctly.
			if ( 'none' != strtolower( $expiration ) ) {
				$timestamp  = is_int( $expiration ) ? $expiration : strtotime( str_replace( ',', '', $expiration ), current_time( 'timestamp' ) );
				$expiration = date( 'Y-m-d 23:59:59', $timestamp );
			}

			/**
			 * Get subscription key.
			 */
			if ( ! empty( $user['Subscription Key'] ) ) {
				$subscription_key = $user['Subscription Key'];
			} elseif ( ! empty( $user['subscription_key'] ) ) {
				$subscription_key = $user['subscription_key'];
			} else {
				$subscription_key = '';
			}

			/**
			 * Get recurring flag.
			 */
			if ( isset( $user['Recurring'] ) ) {
				$recurring = $user['Recurring'];
			} elseif ( isset( $user['recurring'] ) ) {
				$recurring = $user['recurring'];
			} else {
				$recurring = null;
			}
			// Convert into boolean.
			if ( ! empty( $recurring ) && in_array( $recurring, array( '1', 'yes' ) ) ) {
				$recurring = true;
			} elseif ( in_array( $recurring, array( '0', 'no' ) ) ) {
				$recurring = false;
			}

			/**
			 * Get payment profile ID.
			 */
			if ( ! empty( $user['Payment Profile ID'] ) ) {
				$payment_profile_id = $user['Payment Profile ID'];
			} elseif ( ! empty( $user['payment_profile_id'] ) ) {
				$payment_profile_id = $user['payment_profile_id'];
			} else {
				$payment_profile_id = '';
			}

			/**
			 * Get join date.
			 */
			if ( ! empty( $user['Join Date'] ) ) {
				$join_date = date( 'Y-m-d H:i:s', strtotime( $user['Join Date'] ) );
			} elseif ( ! empty( $user['join_date'] ) ) {
				$join_date = date( 'Y-m-d H:i:s', strtotime( $user['join_date'] ) );
			} else {
				$join_date = ''; // Will default to today.
			}

			$member = new RCP_Member( $user_id );

			/**
			 * Flag as new membership to trigger activation email.
			 */
			if ( $subscription_id != $member->get_subscription_id() ) {
				update_user_meta( $member->ID, '_rcp_new_subscription', '1' );
			}

			if ( version_compare( RCP_PLUGIN_VERSION, '3.0', '>=' ) ) {

				/**
				 * RCP version 3.0+
				 */

				$customer = rcp_get_customer_by_user_id( $user_id );

				if ( empty( $customer ) ) {
					// Create customer.
					$customer_id = rcp_add_customer( array(
						'user_id' => $user_id
					) );

					if ( empty( $customer_id ) ) {
						rcp_log( sprintf( 'CSV Import: skipping row #%d - failed to create customer record.', ( $row_number + 1 ) ) );

						continue;
					}

					$customer = rcp_get_customer( $customer_id );
				}

				$membership_to_update = false;

				if ( ! rcp_multiple_memberships_enabled() && $customer->has_active_membership() ) {
					$membership = rcp_get_customer_single_membership( $customer->get_id() );

					/*
					 * If this customer already has a membership with the same membership level ID, we'll update it
					 * instead of creating a whole new one.
					 */
					if ( ! empty( $membership ) && $subscription_id == $membership->get_object_id() ) {
						$membership_to_update = $membership;
					}
				}

				if ( ! empty( $membership_to_update ) ) {

					/**
					 * Update existing membership.
					 */
					$membership_update_args = array(
						'status'          => $status,
						'expiration_date' => $expiration,
						'auto_renew'      => $recurring,
					);

					if ( ! empty( $join_date ) ) {
						$membership_update_args['created_date'] = $join_date;
					}
					if ( ! empty( $subscription_key ) ) {
						$membership_update_args['subscription_key'] = $subscription_key;
					}
					if ( ! empty( $payment_profile_id ) ) {
						$membership_update_args['gateway_customer_id'] = $payment_profile_id;
					}
					if ( ! empty( $user['Subscription ID'] ) ) {
						$membership_update_args['gateway_subscription_id'] = sanitize_text_field( $user['Subscription ID'] );
					}

					$membership_to_update->update( $membership_update_args );

					$membership_to_update->add_note( __( 'Updated from CSV file.', 'rcp_csvui' ) );

				} else {

					/**
					 * Disable all other memberships and add a new one.
					 */

					// Disable all other memberships.
					if ( ! rcp_multiple_memberships_enabled() ) {
						$customer->disable_memberships();
					}

					$membership_level = rcp_get_subscription_details( $subscription_id );

					// Add new membership.
					$membership_id = $customer->add_membership( array(
						'status'                  => $status,
						'object_id'               => $subscription_id,
						'expiration_date'         => $expiration,
						'subscription_key'        => $subscription_key,
						'auto_renew'              => $recurring,
						'gateway_customer_id'     => $payment_profile_id,
						'gateway_subscription_id' => ! empty( $user['Subscription ID'] ) ? sanitize_text_field( $user['Subscription ID'] ) : '',
						'signup_method'           => 'imported',
						'created_date'            => $join_date,
						'initial_amount'          => $membership_level->price + $membership_level->fee,
						'recurring_amount'        => $membership_level->price
					) );

					if ( ! empty( $membership_id ) ) {
						$membership = rcp_get_membership( $membership_id );

						if ( ! empty( $membership ) ) {
							$membership->add_note( __( 'Imported from CSV file.', 'rcp_csvui' ) );
						}
					}

				}

			} elseif ( function_exists( 'rcp_add_user_to_subscription' ) ) {

				/**
				 * Use RCP 2.9+ function for adding user to a subscription.
				 * @see rcp_add_user_to_subscription()
				 */

				if ( null === $recurring ) {
					// Get existing value so we don't change it.
					$recurring = $member->is_recurring();
				}

				// Set join date now if it's specified. If not, this will be set to today in rcp_add_user_to_subscription().
				if ( ! empty( $join_date ) ) {
					$member->set_joined_date( $join_date, $subscription_id );
				}

				$args = array(
					'status'             => $status,
					'subscription_id'    => $subscription_id,
					'expiration'         => $expiration,
					'subscription_key'   => $subscription_key,
					'recurring'          => $recurring,
					'payment_profile_id' => $payment_profile_id
				);

				rcp_add_user_to_subscription( $user_id, $args );

			} else {

				/**
				 * Backwards compatibility if RCP core is not on version 2.9+.
				 */

				$previous_subscription_level = $member->get_subscription_id();

				// Set subscription level.
				$member->set_subscription_id( $subscription_id );

				// Set recurring value.
				if ( true === $recurring ) {
					$member->set_recurring( true );
				} elseif ( false === $recurring ) {
					$member->set_recurring( false );
				}

				// Set expiration date.
				$member->set_expiration_date( $expiration );

				// Set payment profile ID
				if ( ! empty( $payment_profile_id ) ) {
					$member->set_payment_profile_id( $payment_profile_id );
				}

				// Set subscription key
				if ( ! empty( $subscription_key ) ) {
					$member->set_subscription_key( $subscription_key );
				}

				$member->set_status( $status );

				// Set join date.
				$member->set_joined_date( $join_date );

				// Remove the user's old role.
				$old_role = get_option( 'default_role', 'subscriber' );
				if ( ! empty( $previous_subscription_level ) ) {
					$old_level = $rcp_levels_db->get_level( $previous_subscription_level );
					$old_role  = ! empty( $old_level->role ) ? $old_level->role : $old_role;
				}
				$member->remove_role( $old_role );

				// Set the user's new role for this new subscription level.
				$role = ! empty( $subscription_details->role ) ? $subscription_details->role : get_option( 'default_role', 'subscriber' );
				$member->add_role( apply_filters( 'rcp_default_user_level', $role, $subscription_details->id ) );

			}

			// These are only needed below RCP 3.0.
			if ( version_compare( RCP_PLUGIN_VERSION, '3.0', '<' ) ) {
				/**
				 * Set merchant subscription ID (i.e. Stripe subscription ID number).
				 * This isn't available in rcp_add_user_to_subscription(), which is why it's down here.
				 */
				if ( ! empty( $user['Subscription ID'] ) ) {
					$member->set_merchant_subscription_id( sanitize_text_field( $user['Subscription ID'] ) );
				}

				if ( version_compare( RCP_PLUGIN_VERSION, '2.9.11', '>=' ) ) {
					update_user_meta( $user_id, 'rcp_signup_method', 'imported' );
				}

				$member->add_note( __( 'Imported from CSV file.', 'rcp_csvui' ) );
			}

			do_action( 'rcp_user_import_user_added', $user_id, $user_data, $subscription_id, $status, $expiration, $user );
		}

		wp_redirect( admin_url( '/admin.php?page=rcp-csv-import&rcp-message=users-imported' ) ); exit;
	}
}
add_action( 'admin_init', 'rcp_csvui_process_csv' );

/**
 * Display admin notice after a successful import
 *
 * @return void
 */
function rcp_csvui_notices() {
	if( isset( $_GET['rcp-message'] ) && $_GET['rcp-message'] == 'users-imported' ) {
		add_settings_error( 'rcp-csv-ui', 'imported', __('All users have been imported.', 'rcp_csvui'), 'updated' );
	}
}
add_action( 'admin_notices', 'rcp_csvui_notices' );

/**
 * Load admin scripts on the import page
 *
 * @param string $hook Current page
 *
 * @return void
 */
function rcp_csvui_scripts( $hook ) {
	global $rcp_csvui_import_page;
	if( $hook != $rcp_csvui_import_page ) {
		return;
	}

	wp_enqueue_style( 'datepicker', RCP_PLUGIN_URL . 'includes/css/datepicker.css' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
}
add_action( 'admin_enqueue_scripts', 'rcp_csvui_scripts' );

/**
 * Convert CSV to array
 *
 * @param string $filename Path to the file.
 * @param string $delimiter Delimeter.
 *
 * @return array|bool
 */
function rcp_csvui_csv_to_array( $filename = '', $delimiter = ',') {
	if ( ! file_exists( $filename ) || ! is_readable( $filename ) ) {
		return false;
	}

	$header = NULL;
	$data = array();
	if ( false !== ( $handle = fopen( $filename, 'r' ) ) ) {
		while ( false !== ( $row = fgetcsv( $handle, 1000, $delimiter ) ) ) {
			if ( ! $header ) {
				$header = $row;
			} else {
				$data[] = array_combine( $header, $row );
			}
		}
		fclose( $handle );
	}
	return $data;
}
