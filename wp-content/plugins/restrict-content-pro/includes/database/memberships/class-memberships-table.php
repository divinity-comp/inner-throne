<?php
/**
 * Memberships Table.
 *
 * @package     RCP
 * @subpackage  Database\Tables
 * @copyright   Copyright (c) 2018, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

namespace RCP\Database\Tables;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use RCP\Database\Table;

/**
 * Setup the "rcp_memberships" database table
 *
 * @since 3.0
 */
final class Memberships extends Table {

	/**
	 * @var string Table name
	 */
	protected $name = 'memberships';

	/**
	 * @var string Database version
	 */
	protected $version = 201811191;

	/**
	 * @var array Array of upgrade versions and methods
	 */
	protected $upgrades = array(
		'201811131' => 201811131,
		'201811132' => 201811132,
		'201811191' => 201811191
	);

	/**
	 * Memberships constructor.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function __construct() {

		$this->global = is_plugin_active_for_network( plugin_basename( RCP_PLUGIN_FILE ) );

		parent::__construct();

	}

	/**
	 * Setup the database schema
	 *
	 * @access protected
	 * @since  3.0
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			customer_id bigint(20) unsigned NOT NULL default '0',
			object_id bigint(9) NOT NULL default '0',
			object_type varchar(20) DEFAULT NULL,
			currency varchar(20) NOT NULL DEFAULT 'USD',
			initial_amount mediumtext NOT NULL,
			recurring_amount mediumtext NOT NULL,
			created_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			trial_end_date datetime DEFAULT NULL,
			renewed_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			cancellation_date datetime DEFAULT NULL,
			expiration_date datetime DEFAULT NULL,
			payment_plan_completed_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			auto_renew smallint unsigned NOT NULL DEFAULT '0',
			times_billed smallint unsigned NOT NULL DEFAULT '0',
			maximum_renewals smallint unsigned NOT NULL DEFAULT '0',
			status varchar(12) NOT NULL DEFAULT 'pending',
			gateway_customer_id tinytext DEFAULT NULL,
			gateway_subscription_id tinytext DEFAULT NULL,
			gateway tinytext NOT NULL default '',
			signup_method tinytext NOT NULL default '',
			subscription_key varchar(32) NOT NULL default '',
			notes longtext NOT NULL default '',
			upgraded_from bigint(20) unsigned DEFAULT NULL,
			date_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			disabled smallint unsigned DEFAULT NULL,
			uuid varchar(100) NOT NULL default '',
			PRIMARY KEY (id),
			KEY customer_id (customer_id),
			KEY object_id (object_id),
			KEY status (status),
			KEY disabled (disabled)";
	}

	/**
	 * Upgrade to version 201811131
	 * - Add `renewed_date` column.
	 */
	protected function __201811131() {

		// Look for column
		$result = $this->column_exists( 'renewed_date' );

		// Maybe add column
		if ( false === $result ) {
			$result = $this->get_db()->query( "
				ALTER TABLE {$this->table_name} ADD COLUMN `renewed_date` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `trial_end_date`;
			" );
		}

		// Return success/fail
		return $this->is_success( $result );

	}

	/**
	 * Upgrade to version 201811132
	 * - Add `date_modified` column.
	 */
	protected function __201811132() {

		// Look for column
		$result = $this->column_exists( 'date_modified' );

		// Maybe add column
		if ( false === $result ) {
			$result = $this->get_db()->query( "
				ALTER TABLE {$this->table_name} ADD COLUMN `date_modified` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `upgraded_from`;
			" );
		}

		// Return success/fail
		return $this->is_success( $result );

	}

	/**
	 * Upgrade to version 201811191
	 * - Add `payment_plan_completed_date` column.
	 */
	protected function __201811191() {

		// Look for column
		$result = $this->column_exists( 'payment_plan_completed_date' );

		// Maybe add column
		if ( false === $result ) {
			$result = $this->get_db()->query( "
				ALTER TABLE {$this->table_name} ADD COLUMN `payment_plan_completed_date` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `expiration_date`;
			" );
		}

		// Return success/fail
		return $this->is_success( $result );

	}


}