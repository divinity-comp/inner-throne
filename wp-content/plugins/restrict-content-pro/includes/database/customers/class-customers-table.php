<?php
/**
 * Customers Table.
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
 * Setup the "rcp_customers" database table
 *
 * @since 3.0
 */
final class Customers extends Table {

	/**
	 * @var string Table name
	 */
	protected $name = 'customers';

	/**
	 * @var string Database version
	 */
	protected $version = 201810051;

	/**
	 * Customers constructor.
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
			user_id bigint(20) unsigned NOT NULL DEFAULT '0',
			date_registered datetime NOT NULL,
			email_verification enum('verified', 'pending', 'none') DEFAULT 'none',
			last_login datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			ips longtext NOT NULL DEFAULT '',
			notes longtext NOT NULL DEFAULT '',
			uuid varchar(100) NOT NULL default '',
			PRIMARY KEY (id),
			KEY user_id (user_id)";
	}


}