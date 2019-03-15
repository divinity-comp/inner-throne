<?php
/**
 * Upgrade class
 *
 * This class handles database upgrade routines between versions
 *
 * @package     Restrict Content Pro
 * @copyright   Copyright (c) 2017, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.6
 */
class RCP_Upgrades {

	private $version = '';
	private $upgraded = false;

	/**
	 * RCP_Upgrades constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->version = preg_replace( '/[^0-9.].*/', '', get_option( 'rcp_version' ) );

		add_action( 'admin_init', array( $this, 'init' ), -9999 );

	}

	/**
	 * Trigger updates and maybe update the RCP version number
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		$this->v26_upgrades();
		$this->v27_upgrades();
		$this->v29_upgrades();
		$this->v30_upgrades();

		// If upgrades have occurred or the DB version is differnt from the version constant
		if ( $this->upgraded || $this->version <> RCP_PLUGIN_VERSION ) {
			rcp_log( sprintf( 'RCP upgraded from version %s to %s.', $this->version, RCP_PLUGIN_VERSION ), true );
			update_option( 'rcp_version_upgraded_from', $this->version );
			update_option( 'rcp_version', RCP_PLUGIN_VERSION );
		}

	}

	/**
	 * Process 2.6 upgrades
	 *
	 * @access private
	 * @return void
	 */
	private function v26_upgrades() {

		if( version_compare( $this->version, '2.6', '<' ) ) {
			rcp_log( 'Performing version 2.6 upgrades: options install.', true );
			@rcp_options_install();
		}
	}

	/**
	 * Process 2.7 upgrades
	 *
	 * @access private
	 * @return void
	 */
	private function v27_upgrades() {

		if( version_compare( $this->version, '2.7', '<' ) ) {

			rcp_log( 'Performing version 2.7 upgrades: options install and updating discounts database.', true );

			global $wpdb, $rcp_discounts_db_name;

			$wpdb->query( "UPDATE $rcp_discounts_db_name SET code = LOWER(code)" );

			@rcp_options_install();

			$this->upgraded = true;
		}
	}

	/**
	 * Process 2.9 upgrades
	 *
	 * @access private
	 * @since 2.9
	 * @return void
	 */
	private function v29_upgrades() {

		if( version_compare( $this->version, '2.9', '<' ) ) {

			global $rcp_options;

			// Migrate expiring soon email to new reminders.
			$period           = rcp_get_renewal_reminder_period();
			$subject          = isset( $rcp_options['renewal_subject'] ) ? $rcp_options['renewal_subject'] : '';
			$message          = isset( $rcp_options['renew_notice_email'] ) ? $rcp_options['renew_notice_email'] : '';
			$reminders        = new RCP_Reminders();
			$reminders_to_add = array();

			if ( 'none' != $period && ! empty( $subject ) && ! empty( $message ) ) {
				$allowed_periods = $reminders->get_notice_periods();
				$period          = str_replace( ' ', '', $period );

				$new_notice = array(
					'subject'     => sanitize_text_field( $subject ),
					'message'     => wp_kses( $message, wp_kses_allowed_html( 'post' ) ),
					'send_period' => array_key_exists( $period, $allowed_periods ) ? $period : '+1month',
					'type'        => 'expiration',
					'enabled'     => true
				);

				$reminders_to_add[] = $new_notice;
			}

			// Insert default renewal notice.
			$renewal_notices = $reminders->get_notices( 'renewal' );
			if ( empty( $renewal_notices ) ) {
				$reminders_to_add[] = $reminders->get_default_notice( 'renewal' );
			}

			// Update notices.
			if ( ! empty( $reminders_to_add ) ) {
				update_option( 'rcp_reminder_notices', $reminders_to_add );
			}

			@rcp_options_install();

			$this->upgraded = true;

		}

	}

	/**
	 * Process 3.0 upgrades.
	 * Renames the payment_id column to rcp_payment_id in the payment meta table.
	 *
	 * @since 3.0
	 */
	private function v30_upgrades() {

		if( version_compare( $this->version, '3.0', '<' ) ) {

			global $wpdb;

			/**
			 * Run options install to add new tables, add payment plan settings to subscription level table, etc.
			 */
			@rcp_options_install();

			/**
			 * Rename "payment_id" column in payment meta table to "rcp_payment_id".
			 */
			$payment_meta_table_name = rcp_get_payment_meta_db_name();

			rcp_log( sprintf( 'Performing version 3.0 upgrade: Renaming payment_id column to rcp_payment_id in the %s table.', $payment_meta_table_name ), true );

			$payment_meta_cols = $wpdb->get_col( "DESC " . $payment_meta_table_name, 0 );
			$column_renamed    = in_array( 'rcp_payment_id', $payment_meta_cols );

			// Only attempt to rename the column if it hasn't already been done.
			if ( ! $column_renamed ) {
				$updated = $wpdb->query( "ALTER TABLE {$payment_meta_table_name} CHANGE payment_id rcp_payment_id BIGINT(20) NOT NULL DEFAULT '0';" );

				if ( false === $updated ) {
					rcp_log( sprintf( 'Error renaming the payment_id column in %s.', $payment_meta_table_name ), true );
					return;
				} else {
					rcp_log( sprintf( 'Renaming payment_id to rcp_payment_id in %s was successful.', $payment_meta_table_name ), true );
				}
			} else {
				rcp_log( sprintf( 'payment_id column already renamed to rcp_payment_id in %s.', $payment_meta_table_name ), true );
			}

			/**
			 * Upgrade discounts table.
			 */
			$discounts_class = new RCP_Discounts();

			rcp_log( 'Performing version 3.0 upgrade: Upgrading discounts table.', true );

			$discounts_table_name = rcp_get_discounts_db_name();
			$discounts_cols       = $wpdb->get_col( "DESC " . $discounts_table_name, 0 );
			$column_added         = in_array( 'membership_level_ids', $discounts_cols );

			if ( ! $column_added ) {
				// Column would have been added in @rcp_options_install() above, but just in case....
				$updated = $wpdb->query( "ALTER TABLE {$discounts_table_name} ADD membership_level_ids TEXT NOT NULL AFTER subscription_id" );

				if ( false === $updated ) {
					rcp_log( sprintf( 'Error adding the membership_level_ids column in %s.', $discounts_table_name ), true );
					return;
				}
			}

			rcp_log( sprintf( 'Adding membership_level_ids column in %s was successful. Now migrating data.', $discounts_table_name ), true );

			// Migrate existing membership IDs to new column in proper format.
			$discounts = rcp_get_discounts();

			if ( ! empty( $discounts ) ) {
				// Prevent discount sync with Stripe - we don't need it here.
				remove_action( 'rcp_edit_discount', 'rcp_stripe_update_discount', 10 );

				foreach ( $discounts as $discount ) {
					$membership_level_ids = ( $discount->subscription_id == 0 ) ? array() : array( $discount->subscription_id );
					$args                 = array(
						'membership_level_ids' => $membership_level_ids
					);
					$discounts_class->update( $discount->id, $args );
				}

				rcp_log( sprintf( 'Successfully updated membership_level_ids column for %d discount codes.', count( $discounts ) ), true );
			} else {
				rcp_log( 'No discount codes to upgrade.', true );
			}

			// Delete old column.
			$wpdb->query( "ALTER TABLE {$discounts_table_name} DROP COLUMN subscription_id" );

			/**
			 * Change column types in rcp_payments.
			 */
			$payment_table_name = rcp_get_payments_db_name();

			rcp_log( sprintf( 'Performing version 3.0 upgrade: Changing column types in %s table.', $payment_table_name ), true );

			$wpdb->query( "ALTER TABLE {$payment_table_name} MODIFY id bigint(9) unsigned NOT NULL AUTO_INCREMENT" );
			$wpdb->query( "ALTER TABLE {$payment_table_name} MODIFY object_id bigint(9) unsigned NOT NULL" );
			$wpdb->query( "ALTER TABLE {$payment_table_name} MODIFY user_id bigint(20) unsigned NOT NULL" );

			/**
			 * Add batch processing job for migrating memberships to custom table.
			 */
			$registered = \RCP\Utils\Batch\add_batch_job( array(
				'name'        => 'Memberships Migration',
				'description' => __( 'Migrate members and their memberships from user meta to a custom table.', 'rcp' ),
				'callback'    => 'RCP_Batch_Callback_Migrate_Memberships_v3'
			) );

			if ( is_wp_error( $registered ) ) {
				rcp_log( sprintf( 'Batch: Error adding memberships migration job: %s', $registered->get_error_message() ), true );
			} else {
				rcp_log( 'Batch: Successfully initiated memberships migration job.', true );
			}

			$this->upgraded = true;
		}
	}



}
new RCP_Upgrades;